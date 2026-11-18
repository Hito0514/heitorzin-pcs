<?php
/** Model Pedido - compras dos clientes (cabeçalho + itens). */
class Pedido extends Model
{
    protected string $tabela = 'pedidos';
    protected array $fillable = ['cliente_id', 'status', 'total', 'observacao'];

    public const STATUS = ['pendente', 'pago', 'enviado', 'entregue', 'cancelado'];

    public function listarComCliente(): array
    {
        return $this->query(
            'SELECT p.*, c.nome AS cliente
               FROM pedidos p JOIN clientes c ON c.id = p.cliente_id
              ORDER BY p.id DESC'
        )->fetchAll();
    }

    public function buscarComCliente(int $id): ?array
    {
        $r = $this->query(
            'SELECT p.*, c.nome AS cliente, c.email AS cliente_email, c.telefone AS cliente_telefone
               FROM pedidos p JOIN clientes c ON c.id = p.cliente_id
              WHERE p.id = ?',
            [$id]
        )->fetch();
        return $r ?: null;
    }

    public function listarPorCliente(int $clienteId): array
    {
        return $this->query(
            'SELECT * FROM pedidos WHERE cliente_id = ? ORDER BY id DESC',
            [$clienteId]
        )->fetchAll();
    }

    public function itens(int $pedidoId): array
    {
        return $this->query(
            'SELECT i.*, pr.nome AS produto, pr.imagem
               FROM itens_pedido i JOIN produtos pr ON pr.id = i.produto_id
              WHERE i.pedido_id = ? ORDER BY i.id',
            [$pedidoId]
        )->fetchAll();
    }

    /**
     * Cria o pedido, os itens e baixa o estoque dentro de uma transação.
     * @param array $itens [produto_id => quantidade]
     * @throws RuntimeException se faltar estoque ou o produto estiver indisponível
     */
    public function criarComItens(int $clienteId, array $itens, string $status = 'pendente', ?string $obs = null): int
    {
        if (!$itens) {
            throw new RuntimeException('Adicione pelo menos um item ao pedido.');
        }
        $produtos = new Produto();
        $this->db->beginTransaction();
        try {
            $this->query(
                'INSERT INTO pedidos (cliente_id, status, total, observacao) VALUES (?, ?, 0, ?)',
                [$clienteId, $status, $obs]
            );
            $pedidoId = (int)$this->db->lastInsertId();
            $total = 0.0;

            foreach ($itens as $produtoId => $qtd) {
                $produtoId = (int)$produtoId;
                $qtd = (int)$qtd;
                $p = $produtos->buscar($produtoId);
                if (!$p || !$p['ativo'] || $qtd < 1) {
                    throw new RuntimeException('Um dos produtos está indisponível ou com quantidade inválida.');
                }
                if ($status !== 'cancelado' && !$produtos->baixarEstoque($produtoId, $qtd)) {
                    throw new RuntimeException("Estoque insuficiente para \"{$p['nome']}\".");
                }
                $this->query(
                    'INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)',
                    [$pedidoId, $produtoId, $qtd, $p['preco']]
                );
                $total += $qtd * (float)$p['preco'];
            }
            $this->query('UPDATE pedidos SET total = ? WHERE id = ?', [round($total, 2), $pedidoId]);
            $this->db->commit();
            return $pedidoId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e instanceof RuntimeException ? $e : new RuntimeException('Erro ao registrar o pedido.');
        }
    }

    /**
     * Atualiza cliente/status/observação.
     * Cancelar devolve o estoque; reativar um pedido cancelado baixa o estoque novamente.
     */
    public function atualizarPedido(int $id, int $clienteId, string $status, ?string $obs): void
    {
        $atual = $this->buscar($id);
        $itens = $this->itens($id);
        $produtos = new Produto();

        $this->db->beginTransaction();
        try {
            if ($atual['status'] !== 'cancelado' && $status === 'cancelado') {
                foreach ($itens as $i) {
                    $produtos->devolverEstoque((int)$i['produto_id'], (int)$i['quantidade']);
                }
            } elseif ($atual['status'] === 'cancelado' && $status !== 'cancelado') {
                foreach ($itens as $i) {
                    if (!$produtos->baixarEstoque((int)$i['produto_id'], (int)$i['quantidade'])) {
                        throw new RuntimeException("Estoque insuficiente para reativar o pedido ({$i['produto']}).");
                    }
                }
            }
            $this->query(
                'UPDATE pedidos SET cliente_id = ?, status = ?, observacao = ? WHERE id = ?',
                [$clienteId, $status, $obs, $id]
            );
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e instanceof RuntimeException ? $e : new RuntimeException('Erro ao atualizar o pedido.');
        }
    }

    /** Exclui o pedido (itens saem por CASCADE) devolvendo o estoque se ele estava ativo. */
    public function excluir(int $id): bool
    {
        $atual = $this->buscar($id);
        if (!$atual) {
            return false;
        }
        $itens = $this->itens($id);
        $produtos = new Produto();
        $this->db->beginTransaction();
        try {
            if ($atual['status'] !== 'cancelado') {
                foreach ($itens as $i) {
                    $produtos->devolverEstoque((int)$i['produto_id'], (int)$i['quantidade']);
                }
            }
            $this->query('DELETE FROM itens_pedido WHERE pedido_id = ?', [$id]);
            $this->query('DELETE FROM pedidos WHERE id = ?', [$id]);
            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // ---------------------------------------------------------- Relatórios
    public function vendasPorPeriodo(string $de, string $ate, string $status = ''): array
    {
        $sql = 'SELECT p.*, c.nome AS cliente
                  FROM pedidos p JOIN clientes c ON c.id = p.cliente_id
                 WHERE DATE(p.criado_em) BETWEEN ? AND ?';
        $params = [$de, $ate];
        if ($status !== '' && in_array($status, self::STATUS, true)) {
            $sql .= ' AND p.status = ?';
            $params[] = $status;
        }
        return $this->query($sql . ' ORDER BY p.criado_em DESC', $params)->fetchAll();
    }

    public function maisVendidos(string $de, string $ate, int $limite = 10): array
    {
        return $this->query(
            "SELECT pr.nome, SUM(i.quantidade) AS quantidade, SUM(i.quantidade * i.preco_unitario) AS receita
               FROM itens_pedido i
               JOIN pedidos p   ON p.id = i.pedido_id
               JOIN produtos pr ON pr.id = i.produto_id
              WHERE p.status <> 'cancelado' AND DATE(p.criado_em) BETWEEN ? AND ?
              GROUP BY pr.id, pr.nome
              ORDER BY quantidade DESC, receita DESC
              LIMIT " . (int)$limite,
            [$de, $ate]
        )->fetchAll();
    }

    public function receitaPorCategoria(string $de, string $ate): array
    {
        return $this->query(
            "SELECT c.nome, SUM(i.quantidade) AS quantidade, SUM(i.quantidade * i.preco_unitario) AS receita
               FROM itens_pedido i
               JOIN pedidos p   ON p.id = i.pedido_id
               JOIN produtos pr ON pr.id = i.produto_id
               JOIN categorias c ON c.id = pr.categoria_id
              WHERE p.status <> 'cancelado' AND DATE(p.criado_em) BETWEEN ? AND ?
              GROUP BY c.id, c.nome
              ORDER BY receita DESC",
            [$de, $ate]
        )->fetchAll();
    }

    /** Números do dashboard. */
    public function resumo(): array
    {
        $r = $this->query(
            "SELECT COUNT(*) AS pedidos,
                    COALESCE(SUM(CASE WHEN status <> 'cancelado' THEN total ELSE 0 END), 0) AS faturamento,
                    COALESCE(SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END), 0) AS pendentes
               FROM pedidos"
        )->fetch();
        return $r;
    }
}
