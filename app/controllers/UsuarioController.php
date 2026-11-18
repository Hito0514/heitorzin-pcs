<?php
/** CRUD de Usuários do sistema (admin/usuarios) - somente perfil "admin". */
class UsuarioController extends CrudController
{
    protected string $recurso = 'usuarios';
    protected string $titulo = 'Usuários';
    protected string $singular = 'Usuário';

    public function __construct()
    {
        parent::__construct();
        if (!Auth::ehAdmin()) {
            flash('erro', 'Apenas administradores podem gerenciar usuários.');
            redirect('admin');
        }
        $this->model = new Usuario();
    }

    protected function colunas(): array
    {
        return [
            ['label' => '#',      'key' => 'id'],
            ['label' => 'Nome',   'key' => 'nome'],
            ['label' => 'E-mail', 'key' => 'email'],
            ['label' => 'Perfil', 'key' => 'perfil'],
            ['label' => 'Ativo',  'key' => 'ativo', 'fmt' => 'bool'],
        ];
    }

    protected function campos(): array
    {
        return [
            ['name' => 'nome', 'label' => 'Nome', 'type' => 'text', 'required' => true, 'max' => 100],
            ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'required' => true, 'max' => 120],
            ['name' => 'senha', 'label' => 'Senha', 'type' => 'password', 'required' => true, 'help' => 'Mínimo 6 caracteres. Na edição, deixe em branco para manter a atual.'],
            ['name' => 'perfil', 'label' => 'Perfil', 'type' => 'select', 'required' => true,
                'options' => ['admin' => 'Administrador', 'gerente' => 'Gerente']],
            ['name' => 'ativo', 'label' => 'Usuário ativo', 'type' => 'checkbox', 'default' => 1],
        ];
    }

    protected function preparar(array $dados, ?array $atual): array
    {
        // O admin logado não pode desativar nem rebaixar a própria conta
        if ($atual && (int)$atual['id'] === Auth::id()) {
            $dados['ativo'] = 1;
            $dados['perfil'] = $atual['perfil'];
        }
        return $dados;
    }

    protected function bloqueioExclusao(array $registro): ?string
    {
        if ((int)$registro['id'] === Auth::id()) {
            return 'Você não pode excluir a sua própria conta.';
        }
        if ($registro['perfil'] === 'admin' && $this->model->contarAdminsAtivos() <= 1) {
            return 'Não é possível excluir o último administrador.';
        }
        return null;
    }
}
