<?php
/** CRUD de Pedidos (admin/pedidos) - com itens e controle automático de estoque. */
class PedidoController extends Controller
{
    private Pedido $model;

    public function __construct()
    {
        $this->exigirAdmin();
        $this->model = new Pedido();
    }

    // READ - listagem
    public function index(): void
    {
        $this->view('admin/pedidos/index', [
            'titulo'  => 'Pedidos',
            'pedidos' => $this->model->listarComCliente(),
        ], 'admin');
    }

    // READ - detalhes
    public function ver(int $id): void
    {
        $pedido = $this->model->buscarComCliente($id) ?? $this->naoEncontrado();
        $this->view('admin/pedidos/ver', [
            'titulo' => 'Pedido #' . $id,
            'pedido' => $pedido,
            'itens'  => $this->model->itens($id),
        ], 'admin');
    }

    // CREATE - formulário
    public function criar(): void
    {
        $this->view('admin/pedidos/criar', [
            'titulo'   => 'Novo pedido',
            'clientes' => (new Cliente())->listar('nome ASC'),
            'produtos' => array_filter((new Produto())->listar('nome ASC'), fn($p) => $p['ativo']),
            'statusLista' => Pedido::STATUS,
        ], 'admin');
    }

    // CREATE - gravar
    public function salvar(): void
    {
        if (!$this->ehPost()) {
            redirect('admin/pedidos');
        }
        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        $status = $_POST['status'] ?? 'pendente';
        $obs = trim($_POST['observacao'] ?? '') ?: null;

        $itens = [];
        $ids = (array)($_POST['produto_id'] ?? []);
        $qtds = (array)($_POST['quantidade'] ?? []);
        foreach ($ids as $k => $pid) {
            $pid = (int)$pid;
            $q = (int)($qtds[$k] ?? 0);
            if ($pid > 0 && $q > 0) {
                $itens[$pid] = ($itens[$pid] ?? 0) + $q;
            }
        }

        try {
            if (!(new Cliente())->buscar($clienteId)) {
                throw new RuntimeException('Selecione um cliente válido.');
            }
            if (!in_array($status, Pedido::STATUS, true)) {
                throw new RuntimeException('Status inválido.');
            }
            $id = $this->model->criarComItens($clienteId, $itens, $status, $obs);
            flash('sucesso', "Pedido #$id criado com sucesso. O estoque foi atualizado.");
            redirect("admin/pedidos/ver/$id");
        } catch (RuntimeException $e) {
            flash('erro', $e->getMessage());
            guardar_old($_POST);
            redirect('admin/pedidos/criar');
        }
    }

    // UPDATE - formulário
    public function editar(int $id): void
    {
        $pedido = $this->model->buscarComCliente($id) ?? $this->naoEncontrado();
        $this->view('admin/pedidos/editar', [
            'titulo'   => 'Editar pedido #' . $id,
            'pedido'   => $pedido,
            'itens'    => $this->model->itens($id),
            'clientes' => (new Cliente())->listar('nome ASC'),
            'statusLista' => Pedido::STATUS,
        ], 'admin');
    }

    // UPDATE - gravar
    public function atualizar(int $id): void
    {
        $this->model->buscar($id) ?? $this->naoEncontrado();
        if (!$this->ehPost()) {
            redirect("admin/pedidos/editar/$id");
        }
        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $obs = trim($_POST['observacao'] ?? '') ?: null;

        try {
            if (!(new Cliente())->buscar($clienteId)) {
                throw new RuntimeException('Selecione um cliente válido.');
            }
            if (!in_array($status, Pedido::STATUS, true)) {
                throw new RuntimeException('Status inválido.');
            }
            $this->model->atualizarPedido($id, $clienteId, $status, $obs);
            flash('sucesso', "Pedido #$id atualizado.");
            redirect("admin/pedidos/ver/$id");
        } catch (RuntimeException $e) {
            flash('erro', $e->getMessage());
            redirect("admin/pedidos/editar/$id");
        }
    }

    // DELETE
    public function excluir(int $id): void
    {
        $this->model->buscar($id) ?? $this->naoEncontrado();
        if ($this->ehPost()) {
            $this->model->excluir($id);
            flash('sucesso', "Pedido #$id excluído e estoque restaurado.");
        }
        redirect('admin/pedidos');
    }
}
