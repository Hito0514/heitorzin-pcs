<?php
/** CRUD de Fornecedores (admin/fornecedores). */
class FornecedorController extends CrudController
{
    protected string $recurso = 'fornecedores';
    protected string $titulo = 'Fornecedores';
    protected string $singular = 'Fornecedor';

    public function __construct()
    {
        parent::__construct();
        $this->model = new Fornecedor();
    }

    protected function colunas(): array
    {
        return [
            ['label' => '#',        'key' => 'id'],
            ['label' => 'Nome',     'key' => 'nome'],
            ['label' => 'CNPJ',     'key' => 'cnpj'],
            ['label' => 'E-mail',   'key' => 'email'],
            ['label' => 'Telefone', 'key' => 'telefone'],
            ['label' => 'Cidade',   'key' => 'cidade'],
        ];
    }

    protected function campos(): array
    {
        return [
            ['name' => 'nome', 'label' => 'Nome da empresa', 'type' => 'text', 'required' => true, 'max' => 100],
            ['name' => 'cnpj', 'label' => 'CNPJ', 'type' => 'text', 'max' => 18, 'nullable' => true, 'placeholder' => '00.000.000/0000-00'],
            ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'max' => 120, 'nullable' => true],
            ['name' => 'telefone', 'label' => 'Telefone', 'type' => 'tel', 'max' => 20, 'nullable' => true],
            ['name' => 'cidade', 'label' => 'Cidade', 'type' => 'text', 'max' => 80, 'nullable' => true],
        ];
    }
}
