<?php
/** Model Cliente - quem compra na loja pública. */
class Cliente extends Model
{
    protected string $tabela = 'clientes';
    protected array $fillable = ['nome', 'email', 'telefone', 'senha', 'endereco', 'cidade'];

    public function buscarPorEmail(string $email): ?array
    {
        $r = $this->query('SELECT * FROM clientes WHERE email = ?', [$email])->fetch();
        return $r ?: null;
    }
}
