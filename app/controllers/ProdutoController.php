<?php
/** CRUD de Produtos (admin/produtos) com upload de imagem e controle de estoque. */
class ProdutoController extends CrudController
{
    protected string $recurso = 'produtos';
    protected string $titulo = 'Produtos';
    protected string $singular = 'Produto';

    public function __construct()
    {
        parent::__construct();
        $this->model = new Produto();
    }

    protected function registros(): array
    {
        return $this->model->listarComRelacionamentos();
    }

    protected function colunas(): array
    {
        return [
            ['label' => 'Foto',      'key' => 'imagem', 'fmt' => 'image'],
            ['label' => 'Produto',   'key' => 'nome'],
            ['label' => 'Categoria', 'key' => 'categoria'],
            ['label' => 'Preço',     'key' => 'preco', 'fmt' => 'money'],
            ['label' => 'Estoque',   'key' => 'estoque'],
            ['label' => 'Destaque',  'key' => 'destaque', 'fmt' => 'bool'],
            ['label' => 'Ativo',     'key' => 'ativo', 'fmt' => 'bool'],
        ];
    }

    protected function campos(): array
    {
        return [
            ['name' => 'nome', 'label' => 'Nome do produto', 'type' => 'text', 'required' => true, 'max' => 150],
            ['name' => 'marca', 'label' => 'Marca', 'type' => 'text', 'max' => 60, 'nullable' => true],
            ['name' => 'categoria_id', 'label' => 'Categoria', 'type' => 'select', 'required' => true,
                'options' => (new Categoria())->opcoes()],
            ['name' => 'fornecedor_id', 'label' => 'Fornecedor', 'type' => 'select', 'nullable' => true,
                'options' => (new Fornecedor())->opcoes(), 'vazio' => '— nenhum —'],
            ['name' => 'preco', 'label' => 'Preço (R$)', 'type' => 'number', 'required' => true, 'step' => '0.01', 'min' => 0],
            ['name' => 'estoque', 'label' => 'Estoque (unidades)', 'type' => 'number', 'required' => true, 'step' => '1', 'min' => 0],
            ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'textarea', 'nullable' => true],
            ['name' => 'imagem', 'label' => 'Imagem (JPG, PNG, WEBP ou GIF até 2 MB)', 'type' => 'file'],
            ['name' => 'destaque', 'label' => 'Mostrar em destaque na página inicial', 'type' => 'checkbox'],
            ['name' => 'ativo', 'label' => 'Produto ativo (visível na loja)', 'type' => 'checkbox', 'default' => 1],
        ];
    }

    protected function preparar(array $dados, ?array $atual): array
    {
        $dados['estoque'] = (int)round((float)($dados['estoque'] ?? 0));
        $dados['preco'] = round((float)($dados['preco'] ?? 0), 2);
        return $dados;
    }

    protected function aposExcluir(array $registro): void
    {
        Upload::remover($registro['imagem'] ?? null);
    }
}
