<?php
/**
 * Controle de autenticação e sessão.
 * Dois tipos de conta: 'usuario' (admin/gerente) e 'cliente'.
 */
class Auth
{
    public static function iniciarSessao(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name('HEITORZINSESS');
            session_set_cookie_params([
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
        // Expira a sessão após 2 horas de inatividade
        if (isset($_SESSION['ultimo_acesso']) && time() - $_SESSION['ultimo_acesso'] > 7200) {
            self::logout(false);
            session_start();
        }
        $_SESSION['ultimo_acesso'] = time();
    }

    /** Tenta autenticar. Retorna 'usuario', 'cliente' ou null. */
    public static function tentarLogin(string $email, string $senha): ?string
    {
        $usuario = (new Usuario())->buscarPorEmail($email);
        if ($usuario && $usuario['ativo'] && password_verify($senha, $usuario['senha'])) {
            self::registrar($usuario, 'usuario');
            return 'usuario';
        }

        $cliente = (new Cliente())->buscarPorEmail($email);
        if ($cliente && password_verify($senha, $cliente['senha'])) {
            self::registrar($cliente, 'cliente');
            return 'cliente';
        }
        return null;
    }

    /** Grava os dados do usuário/cliente logado na sessão. */
    public static function registrar(array $dados, string $tipo): void
    {
        session_regenerate_id(true); // evita fixação de sessão
        $carrinho = $_SESSION['carrinho'] ?? [];
        $_SESSION['auth'] = [
            'id'     => (int)$dados['id'],
            'nome'   => $dados['nome'],
            'email'  => $dados['email'],
            'tipo'   => $tipo,
            'perfil' => $dados['perfil'] ?? null,
        ];
        $_SESSION['carrinho'] = $carrinho;
    }

    public static function logout(bool $recomecar = true): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        if ($recomecar) {
            session_start();
        }
    }

    public static function logado(): bool
    {
        return isset($_SESSION['auth']);
    }

    public static function usuario(): ?array
    {
        return $_SESSION['auth'] ?? null;
    }

    public static function ehAdminArea(): bool
    {
        return self::logado() && $_SESSION['auth']['tipo'] === 'usuario';
    }

    public static function ehAdmin(): bool
    {
        return self::ehAdminArea() && $_SESSION['auth']['perfil'] === 'admin';
    }

    public static function ehCliente(): bool
    {
        return self::logado() && $_SESSION['auth']['tipo'] === 'cliente';
    }

    public static function id(): int
    {
        return (int)($_SESSION['auth']['id'] ?? 0);
    }
}
