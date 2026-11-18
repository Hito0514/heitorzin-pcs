<?php
/** Dashboard administrativo (admin). */
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->exigirAdmin();
    }

    public function index(): void
    {
        $pedidos = new Pedido();
        $produtos = new Produto();
        $de = date('Y-m-d', strtotime('-365 days'));
        $ate = date('Y-m-d');

        $this->view('admin/dashboard', [
            'titulo'        => 'Dashboard',
            'resumo'        => $pedidos->resumo(),
            'totalProdutos' => $produtos->contar(),
            'totalClientes' => (new Cliente())->contar(),
            'estoqueBaixo'  => $produtos->estoqueBaixo(),
            'ultimos'       => array_slice($pedidos->listarComCliente(), 0, 5),
            'porCategoria'  => $pedidos->receitaPorCategoria($de, $ate),
        ], 'admin');
    }
}
