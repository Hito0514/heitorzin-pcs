<?php
/** CRUD de Categorias (admin/categorias). */
class CategoriaController extends CrudController
{
    protected string $recurso = 'categorias';
    protected string $titulo = 'Categorias';
    protected string $singular = 'Categoria';

    public function __construct()
    {
        parent::__construct();
        $this->model = new Categoria();
    }

    protected function registros(): array
    {
        return $this->model->listarComQuantidade();
    }

    protected function colunas(): array
    {
        return [
            ['label' => '#',         'key' => 'id'],
            ['label' => 'Nome',      'key' => 'nome'],
            ['label' => 'Descrição', 'key' => 'descricao'],
            ['label' => 'Produtos',  'key' => 'total_produtos'],
        ];
    }

    protected function campos(): array
    {
        return [
            ['name' => 'nome', 'label' => 'Nome', 'type' => 'text', 'required' => true, 'max' => 60],
            ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'text', 'max' => 200, 'nullable' => true],
        ];
    }
}
