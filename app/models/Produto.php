<?php
/** Model Produto - peças de computador à venda. */
class Produto extends Model
{
    protected string $tabela = 'produtos';
    protected array $fillable = [
        'nome', 'marca', 'descricao', 'preco', 'estoque', 'imagem',
        'destaque', 'ativo', 'categoria_id', 'fornecedor_id',
    ];

    private const SELECT = 'SELECT p.*, c.nome AS categoria, f.nome AS fornecedor
                              FROM produtos p
                              JOIN categorias c ON c.id = p.categoria_id
                              LEFT JOIN fornecedores f ON f.id = p.fornecedor_id';

    /** Listagem administrativa (com nome da categoria e do fornecedor). */
    public function listarComRelacionamentos(): array
    {
        return $this->query(self::SELECT . ' ORDER BY p.id DESC')->fetchAll();
    }

    /** Produto com categoria/fornecedor (página de detalhes). */
    public function buscarCompleto(int $id): ?array
    {
        $r = $this->query(self::SELECT . ' WHERE p.id = ?', [$id])->fetch();
        return $r ?: null;
    }

    /** Catálogo público: busca por texto, filtro de categoria e ordenação. */
    public function catalogo(string $busca = '', int $categoria = 0, string $ordem = 'recentes'): array
    {
        $ordens = [
            'recentes'   => 'p.id DESC',
            'preco_asc'  => 'p.preco ASC',
            'preco_desc' => 'p.preco DESC',
            'nome'       => 'p.nome ASC',
        ];
        $orderBy = $ordens[$ordem] ?? $ordens['recentes'];

        $where = ['p.ativo = 1'];
        $params = [];
        if ($busca !== '') {
            $like = '%' . addcslashes($busca, '%_\\') . '%';
            $where[] = '(p.nome LIKE ? OR p.marca LIKE ? OR p.descricao LIKE ?)';
            array_push($params, $like, $like, $like);
        }
        if ($categoria > 0) {
            $where[] = 'p.categoria_id = ?';
            $params[] = $categoria;
        }
        $sql = self::SELECT . ' WHERE ' . implode(' AND ', $where) . " ORDER BY $orderBy";
        return $this->query($sql, $params)->fetchAll();
    }

    public function destaques(int $limite = 4): array
    {
        return $this->query(
            self::SELECT . ' WHERE p.ativo = 1 AND p.destaque = 1 ORDER BY p.id DESC LIMIT ' . (int)$limite
        )->fetchAll();
    }

    public function recentes(int $limite = 8): array
    {
        return $this->query(
            self::SELECT . ' WHERE p.ativo = 1 ORDER BY p.id DESC LIMIT ' . (int)$limite
        )->fetchAll();
    }

    /** Produtos com estoque baixo (alerta do dashboard). */
    public function estoqueBaixo(int $limite = 5, int $quantidade = 5): array
    {
        return $this->query(
            'SELECT id, nome, estoque FROM produtos WHERE estoque <= ? ORDER BY estoque ASC LIMIT ' . (int)$limite,
            [$quantidade]
        )->fetchAll();
    }

    public function buscarVarios(array $ids): array
    {
        if (!$ids) {
            return [];
        }
        $marcas = implode(',', array_fill(0, count($ids), '?'));
        $linhas = $this->query("SELECT * FROM produtos WHERE id IN ($marcas)", array_values($ids))->fetchAll();
        $out = [];
        foreach ($linhas as $l) {
            $out[$l['id']] = $l;
        }
        return $out;
    }

    // ---------------- Controle de estoque ----------------
    /** Baixa estoque de forma atômica. Retorna false se não houver quantidade suficiente. */
    public function baixarEstoque(int $id, int $quantidade): bool
    {
        $stmt = $this->query(
            'UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?',
            [$quantidade, $id, $quantidade]
        );
        return $stmt->rowCount() === 1;
    }

    public function devolverEstoque(int $id, int $quantidade): void
    {
        $this->query('UPDATE produtos SET estoque = estoque + ? WHERE id = ?', [$quantidade, $id]);
    }
}
