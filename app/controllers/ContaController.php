<?php
/** Área do cliente: histórico de pedidos. */
class ContaController extends Controller
{
    public function __construct()
    {
        $this->exigirCliente();
    }

    public function index(): void
    {
        $this->view('conta/pedidos', [
            'titulo'  => 'Meus pedidos',
            'pedidos' => (new Pedido())->listarPorCliente(Auth::id()),
        ]);
    }

    public function pedido(int $id): void
    {
        $model = new Pedido();
        $pedido = $model->buscarComCliente($id);
        // O cliente só pode ver os próprios pedidos
        if (!$pedido || (int)$pedido['cliente_id'] !== Auth::id()) {
            $this->naoEncontrado();
        }
        $this->view('conta/pedido', [
            'titulo' => 'Pedido #' . $id,
            'pedido' => $pedido,
            'itens'  => $model->itens($id),
        ]);
    }
}
