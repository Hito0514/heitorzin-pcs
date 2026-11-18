<?php
/**
 * Conexão com o banco usando PDO (padrão Singleton).
 */
class Database
{
    private static ?PDO $conexao = null;

    public static function getConnection(): PDO
    {
        if (self::$conexao === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
            );
            try {
                self::$conexao = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                exit('<h1>Erro de conexão com o banco de dados</h1>'
                    . '<p>Verifique se o MySQL está ligado no XAMPP e se o banco '
                    . '<b>heitorzin_pcs</b> foi importado. Confira também config/config.php.</p>');
            }
        }
        return self::$conexao;
    }

    /** Permite trocar a conexão (usado em testes automatizados). */
    public static function setConnection(PDO $pdo): void
    {
        self::$conexao = $pdo;
    }
}
