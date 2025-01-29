<?php
declare(strict_types = 1);


namespace Mgleis\PhpSqliteJobQueue;

class Queue {

    public \PDO $db;
    private string $table;

    public function __construct(string $filename, string $table = 'jobs') {
        $this->db = new \PDO("sqlite:$filename");
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->db->exec('PRAGMA journal_mode = WAL;');
        $this->db->exec('PRAGMA synchronous = NORMAL;');
        $this->table = $this->db->quote($table);
        $this->initializeDatabase();
    }

    private function initializeDatabase() {
        $this->db->exec(sprintf("
            CREATE TABLE IF NOT EXISTS %s (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                payload TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'queued',
                reserved_at TIMESTAMP DEFAULT NULL
            );
        ", $this->table));
    }

    public function push($payload) {
        $strPayload = json_encode($payload, JSON_THROW_ON_ERROR);

        $stmt = $this->db->prepare(sprintf("INSERT INTO %s (payload, status) VALUES (:payload, 'queued')", $this->table));
        $stmt->bindValue(':payload', $strPayload);
        $stmt->execute();
    }

    public function pop(): ?Job {
        try {

            // find the next job
            $stmt = $this->db->prepare(sprintf("
                SELECT id, payload
                FROM %s
                WHERE status = 'queued'
                ORDER BY id ASC
                LIMIT 1
            ", $this->table));
            $stmt->execute();
            $jobData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$jobData) {
                return null;
            }

            // mark the job as "working"
            $updateStmt = $this->db->prepare(sprintf("
                UPDATE %s
                SET status = 'working', reserved_at = CURRENT_TIMESTAMP
                WHERE id = :id AND status = 'queued'
            ", $this->table));
            $updateStmt->bindValue(':id', (int) $jobData['id'], \PDO::PARAM_INT);
            $updateStmt->execute();

            return new Job(
                (int) $jobData['id'],
                json_decode($jobData['payload'], true, JSON_THROW_ON_ERROR)
            );
        } catch (\Throwable $t) {
            $this->throwIfNotALockError($t);
            return null;
        }
    }

    public function top(): ?Job {
        try {
            // Reserviere den nächsten Job
            $stmt = $this->db->prepare(sprintf("
                SELECT id, payload
                FROM %s
                WHERE status = 'queued'
                ORDER BY id ASC
                LIMIT 1
            ", $this->table));
            $stmt->execute();
            $jobData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$jobData) {
                return null;
            }

            return new Job(
                (int) $jobData['id'],
                json_decode($jobData['payload'], true, JSON_THROW_ON_ERROR)
            );
        } catch (\Throwable $t) {
            $this->throwIfNotALockError($t);
            return null;
        }
    }

    private function throwIfNotALockError(\Throwable $t) {
        if ($t instanceof \PDOException
            && str_contains($t->getMessage(), 'database is locked')) {
            return;
        }
        throw $t;
    }

    public function done(Job $job) {
        $updateStmt = $this->db->prepare(sprintf("
            DELETE FROM %s
            WHERE id = :id
        ", $this->table));
        $updateStmt->bindValue(':id', $job->id, \PDO::PARAM_INT);
        $updateStmt->execute();
    }

    public function error(Job $job) {
        $updateStmt = $this->db->prepare(sprintf("
            UPDATE %s
            SET status = 'failed'
            WHERE id = :id
        ", $this->table));
        $updateStmt->bindValue(':id', $job->id, \PDO::PARAM_INT);
        $updateStmt->execute();
    }

    public function size(): int {
        $stmt = $this->db->query(sprintf("SELECT COUNT(*) FROM %s WHERE status = 'queued'", $this->table));
        return (int)$stmt->fetchColumn();
    }

}
