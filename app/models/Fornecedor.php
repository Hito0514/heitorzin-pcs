<?php
/** Model Fornecedor - empresas que abastecem a loja. */
class Fornecedor extends Model
{
    protected string $tabela = 'fornecedores';
    protected array $fillable = ['nome', 'cnpj', 'email', 'telefone', 'cidade'];

    public function opcoes(): array
    {
        $out = [];
        foreach ($this->listar('nome ASC') as $f) {
            $out[$f['id']] = $f['nome'];
        }
        return $out;
    }
}
