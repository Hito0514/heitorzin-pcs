<?php
/** CRUD de Clientes (admin/clientes). */
class ClienteController extends CrudController
{
    protected string $recurso = 'clientes';
    protected string $titulo = 'Clientes';
    protected string $singular = 'Cliente';

    public function __construct()
    {
        parent::__construct();
        $this->model = new Cliente();
    }

    protected function colunas(): array
    {
        return [
            ['label' => '#',        'key' => 'id'],
            ['label' => 'Nome',     'key' => 'nome'],
            ['label' => 'E-mail',   'key' => 'email'],
            ['label' => 'Telefone', 'key' => 'telefone'],
            ['label' => 'Cidade',   'key' => 'cidade'],
            ['label' => 'Cadastro', 'key' => 'criado_em', 'fmt' => 'date'],
        ];
    }

    protected function campos(): array
    {
        return [
            ['name' => 'nome', 'label' => 'Nome completo', 'type' => 'text', 'required' => true, 'max' => 100],
            ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'required' => true, 'max' => 120],
            ['name' => 'telefone', 'label' => 'Telefone', 'type' => 'tel', 'required' => true, 'max' => 20, 'placeholder' => '(38) 99999-9999'],
            ['name' => 'senha', 'label' => 'Senha', 'type' => 'password', 'required' => true, 'help' => 'Mínimo 6 caracteres. Na edição, deixe em branco para manter a atual.'],
            ['name' => 'endereco', 'label' => 'Endereço', 'type' => 'text', 'max' => 200, 'nullable' => true],
            ['name' => 'cidade', 'label' => 'Cidade', 'type' => 'text', 'max' => 80, 'nullable' => true],
        ];
    }
}
