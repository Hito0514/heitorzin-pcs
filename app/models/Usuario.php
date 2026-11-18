<?php
/** Model Usuario - quem acessa a área administrativa (admin / gerente). */
class Usuario extends Model
{
    protected string $tabela = 'usuarios';
    protected array $fillable = ['nome', 'email', 'senha', 'perfil', 'ativo'];

    public function buscarPorEmail(string $email): ?array
    {
        $r = $this->query('SELECT * FROM usuarios WHERE email = ?', [$email])->fetch();
        return $r ?: null;
    }

    public function contarAdminsAtivos(): int
    {
        return (int)$this->query("SELECT COUNT(*) FROM usuarios WHERE perfil = 'admin' AND ativo = 1")->fetchColumn();
    }
}
