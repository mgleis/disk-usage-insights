<?php
declare(strict_types=1);

namespace Mgleis\PhpSqliteKeyValueStore;

class KeyValueStore {

    private \PDO $db;
    private string $table;

    public function __construct(mixed $dbFileOrPdo = 'kvstore.sqlite', string $table = 'key_value_store') {
        $this->table = $table;
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new \InvalidArgumentException("Invalid table name");
        }

        if (is_string($dbFileOrPdo)) {
            $this->db = new \PDO("sqlite:" . $dbFileOrPdo);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } else if ($dbFileOrPdo instanceof \PDO) {
            $this->db = $dbFileOrPdo;
        } else {
            throw new \InvalidArgumentException("First argument needs to be a string or a PDO instance.");
        }

        $this->initializeDatabase();
    }

    private function initializeDatabase(): void {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS {$this->table} (
                key TEXT PRIMARY KEY,
                value TEXT
            );
        ");
    }

    public function get(string $key, $default = null): mixed {
        $stmt = $this->db->prepare("SELECT value FROM {$this->table} WHERE key = :key");
        $stmt->bindParam(':key', $key, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            return $default;
        }

        return json_decode($result['value'], true, JSON_THROW_ON_ERROR);
    }

    public function has(string $key): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM {$this->table} WHERE key = :key");
        $stmt->bindParam(':key', $key, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC) !== false;
    }

    public function delete(string $key): void {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE key = :key");
        $stmt->bindParam(':key', $key, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function set(string $key, mixed $value): void {
        $jsonValue = json_encode($value, JSON_THROW_ON_ERROR);

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (key, value)
            VALUES (:key, :value)
            ON CONFLICT(key) DO UPDATE SET value = :value;
        ");
        $stmt->bindParam(':key', $key, \PDO::PARAM_STR);
        $stmt->bindParam(':value', $jsonValue, \PDO::PARAM_STR);
        $stmt->execute();
    }

    private function search(string $searchText, string $column, string $likeCondition): array {
        if (!in_array($likeCondition, ['%%%s%%', '%%%s'])) {
            throw new \InvalidArgumentException('Invalid likeCondition');
        }
        if (in_array($likeCondition, ['key', 'value'])) {
            throw new \InvalidArgumentException('Invalid column');
        }

        $stmt = $this->db->prepare("
            SELECT key, value
            FROM {$this->table}
            WHERE {$column} LIKE :text
        ");
        $searchText = '%' . $searchText . '%';
        $stmt->bindParam(':text', $searchText, \PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as &$row) {
            $row['value'] = json_decode($row['value'], true, JSON_THROW_ON_ERROR);
        }

        return $results ?: [];
    }

    public function searchValueContains(string $searchText): array {
        return $this->search($searchText, 'value', '%%%s%%');
    }

    public function searchValueStartsWith(string $searchText): array {
        return $this->search($searchText, 'value', '%%%s');
    }

    public function searchKeyContains(string $searchText): array {
        return $this->search($searchText, 'key', '%%%s%%');
    }

    public function searchKeyStartsWith(string $searchText): array {
        return $this->search($searchText, 'key', '%%%s');
    }

}