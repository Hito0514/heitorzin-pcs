<?php
/** Model Categoria - agrupa os produtos (Processadores, Placas de Vídeo...). */
class Categoria extends Model
{
    protected string $tabela = 'categorias';
    protected array $fillable = ['nome', 'descricao'];

    /** Lista categorias com a quantidade de produtos ativos de cada uma. */
    public function listarComQuantidade(): array
    {
        return $this->query(
            'SELECT c.*, COUNT(p.id) AS total_produtos
               FROM categorias c
               LEFT JOIN produtos p ON p.categoria_id = c.id AND p.ativo = 1
              GROUP BY c.id, c.nome, c.descricao
              ORDER BY c.nome'
        )->fetchAll();
    }

    /** Opções [id => nome] para selects de formulário. */
    public function opcoes(): array
    {
        $out = [];
        foreach ($this->listar('nome ASC') as $c) {
            $out[$c['id']] = $c['nome'];
        }
        return $out;
    }
}
