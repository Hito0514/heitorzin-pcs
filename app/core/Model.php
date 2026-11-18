<?php
/**
 * Model base: concentra o acesso ao banco (PDO + prepared statements).
 * Cada Model filho define a tabela e os campos que podem ser gravados.
 */
abstract class Model
{
    protected string $tabela;
    /** Campos permitidos em INSERT/UPDATE (proteção contra mass assignment). */
    protected array $fillable = [];
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Executa uma consulta preparada e devolve o statement. */
    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function listar(string $ordem = 'id DESC'): array
    {
        return $this->query("SELECT * FROM {$this->tabela} ORDER BY {$ordem}")->fetchAll();
    }

    public function buscar(int $id): ?array
    {
        $r = $this->query("SELECT * FROM {$this->tabela} WHERE id = ?", [$id])->fetch();
        return $r ?: null;
    }

    public function contar(): int
    {
        return (int)$this->query("SELECT COUNT(*) FROM {$this->tabela}")->fetchColumn();
    }

    /** CREATE */
    public function criar(array $dados): int
    {
        $dados = array_intersect_key($dados, array_flip($this->fillable));
        $colunas = implode(', ', array_keys($dados));
        $marcas  = implode(', ', array_fill(0, count($dados), '?'));
        $this->query("INSERT INTO {$this->tabela} ($colunas) VALUES ($marcas)", array_values($dados));
        return (int)$this->db->lastInsertId();
    }

    /** UPDATE */
    public function atualizar(int $id, array $dados): bool
    {
        $dados = array_intersect_key($dados, array_flip($this->fillable));
        if (!$dados) {
            return false;
        }
        $sets = implode(', ', array_map(fn($c) => "$c = ?", array_keys($dados)));
        $params = array_values($dados);
        $params[] = $id;
        $this->query("UPDATE {$this->tabela} SET $sets WHERE id = ?", $params);
        return true;
    }

    /** DELETE */
    public function excluir(int $id): bool
    {
        return $this->query("DELETE FROM {$this->tabela} WHERE id = ?", [$id])->rowCount() > 0;
    }
}
