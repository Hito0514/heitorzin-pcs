<?php
/** Carrinho de compras (guardado na sessão) e finalização do pedido. */
class CarrinhoController extends Controller
{
    public function index(): void
    {
        $carrinho = $_SESSION['carrinho'] ?? [];
        $produtos = (new Produto())->buscarVarios(array_keys($carrinho));
        $itens = [];
        $total = 0.0;
        foreach ($carrinho as $id => $qtd) {
            if (!isset($produtos[$id]) || !$produtos[$id]['ativo']) {
                unset($_SESSION['carrinho'][$id]);
                continue;
            }
            $p = $produtos[$id];
            $sub = $qtd * (float)$p['preco'];
            $total += $sub;
            $itens[] = ['produto' => $p, 'quantidade' => $qtd, 'subtotal' => $sub];
        }
        $this->view('loja/carrinho', ['titulo' => 'Carrinho', 'itens' => $itens, 'total' => $total]);
    }

    public function adicionar(): void
    {
        if (!$this->ehPost()) {
            redirect('carrinho');
        }
        $id = (int)($_POST['produto_id'] ?? 0);
        $qtd = max(1, (int)($_POST['quantidade'] ?? 1));
        $p = (new Produto())->buscar($id);

        if (!$p || !$p['ativo']) {
            flash('erro', 'Produto indisponível.');
            redirect('loja');
        }
        $novaQtd = ($_SESSION['carrinho'][$id] ?? 0) + $qtd;
        if ($novaQtd > (int)$p['estoque']) {
            flash('erro', "Estoque insuficiente. Disponível: {$p['estoque']} unidade(s) de {$p['nome']}.");
            redirect("produto/$id");
        }
        $_SESSION['carrinho'][$id] = $novaQtd;
        flash('sucesso', "{$p['nome']} adicionado ao carrinho.");
        redirect('carrinho');
    }

    public function atualizar(): void
    {
        if ($this->ehPost()) {
            $produtos = new Produto();
            foreach ((array)($_POST['qtd'] ?? []) as $id => $qtd) {
                $id = (int)$id;
                $qtd = (int)$qtd;
                if (!isset($_SESSION['carrinho'][$id])) {
                    continue;
                }
                if ($qtd <= 0) {
                    unset($_SESSION['carrinho'][$id]);
                    continue;
                }
                $p = $produtos->buscar($id);
                $max = $p ? (int)$p['estoque'] : 0;
                if ($qtd > $max) {
                    flash('erro', "Quantidade ajustada para o estoque disponível ({$max}).");
                    $qtd = $max;
                }
                if ($qtd <= 0) {
                    unset($_SESSION['carrinho'][$id]);
                } else {
                    $_SESSION['carrinho'][$id] = $qtd;
                }
            }
            flash('sucesso', 'Carrinho atualizado.');
        }
        redirect('carrinho');
    }

    public function remover(): void
    {
        if ($this->ehPost()) {
            unset($_SESSION['carrinho'][(int)($_POST['produto_id'] ?? 0)]);
            flash('sucesso', 'Item removido do carrinho.');
        }
        redirect('carrinho');
    }

    public function finalizar(): void
    {
        if (!$this->ehPost()) {
            redirect('carrinho');
        }
        if (!Auth::logado()) {
            flash('erro', 'Entre na sua conta ou cadastre-se para finalizar a compra.');
            $_SESSION['voltar_para'] = 'carrinho';
            redirect('login');
        }
        if (!Auth::ehCliente()) {
            flash('erro', 'Contas administrativas não fazem compras. Entre com uma conta de cliente.');
            redirect('carrinho');
        }
        $carrinho = $_SESSION['carrinho'] ?? [];
        try {
            $pedidoId = (new Pedido())->criarComItens(Auth::id(), $carrinho, 'pendente');
        } catch (RuntimeException $e) {
            flash('erro', $e->getMessage());
            redirect('carrinho');
        }
        $_SESSION['carrinho'] = [];
        flash('sucesso', "Pedido #$pedidoId realizado com sucesso! Obrigado pela compra.");
        redirect("conta/pedido/$pedidoId");
    }
}
