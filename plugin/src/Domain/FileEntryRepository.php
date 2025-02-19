<?php

namespace Mgleis\DiskUsageInsights\Domain;

class FileEntryRepository {

    private \PDO $db;

    public function __construct(\PDO $db) {
        $this->db = $db;
        $this->initializeDatabase();
    }

    private function initializeDatabase() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS fileentries (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                parent_id INTEGER,
                name TEXT NOT NULL,
                type TEXT NOT NULL,
                size INTEGER,
                dir_size INTEGER,
                dir_recursive_size INTEGER,
                dir_count INTEGER,
                dir_recursive_count INTEGER,
                last_modified_date INTEGER,
                is_wp_core_file INTEGER
            )
        ");
    }

    public function createOrUpdate(FileEntry &$fileEntry) {

        if ($fileEntry->id == 0) {
            $stmt = $this->db->prepare("
                INSERT INTO fileentries
                (parent_id, name, type, size, dir_size, dir_recursive_size, dir_count, dir_recursive_count, last_modified_date, is_wp_core_file)
                VALUES (:parent_id, :name, :type, :size, :dir_size, :dir_recursive_size, :dir_count, :dir_recursive_count, :last_modified_date, :is_wp_core_file)
            ");
        } else {
            $stmt = $this->db->prepare("
                REPLACE INTO fileentries
                (id, parent_id, name, type, size, dir_size, dir_recursive_size, dir_count, dir_recursive_count, last_modified_date, is_wp_core_file)
                VALUES (:id, :parent_id, :name, :type, :size, :dir_size, :dir_recursive_size, :dir_count, :dir_recursive_count, :last_modified_date, :is_wp_core_file)
            ");
            $stmt->bindValue(':id', $fileEntry->id);
        }
        $stmt->bindValue(':parent_id', $fileEntry->parent_id);
        $stmt->bindValue(':name', $fileEntry->name);
        $stmt->bindValue(':type', $fileEntry->type);
        $stmt->bindValue(':size', $fileEntry->size);
        $stmt->bindValue(':dir_size', $fileEntry->dir_size);
        $stmt->bindValue(':dir_recursive_size', $fileEntry->dir_recursive_size);
        $stmt->bindValue(':dir_count', $fileEntry->dir_count);
        $stmt->bindValue(':dir_recursive_count', $fileEntry->dir_recursive_count);
        $stmt->bindValue(':last_modified_date', $fileEntry->last_modified_date);
        $stmt->bindValue(':is_wp_core_file', $fileEntry->is_wp_core_file);
        $stmt->execute();

        if ($fileEntry->id == 0) {
            $fileEntry->id = $this->db->lastInsertId();
        }
    }

    public function findById(int $id): FileEntry {

        $stmt = $this->db->prepare("
            SELECT * FROM fileentries WHERE id =:id
        ");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new \Exception(sprintf("No file entry with id %s found.", esc_html($id)));
        }

        $entry = new FileEntry();
        foreach ($result as $key => $value) {
            $entry->$key = $value;
        }

        return $entry;
    }

    /**
     *
     * @param string $relativeFilename e.g. '/wp-content/plugins'
     * @throws \Exception
     * @return FileEntry|null
     */
    public function findByRelativeName(string $relativeFilename): ?FileEntry {

        $arr = explode('/', $relativeFilename);
        $parentId = 0; // root entry
        $result = null;
        foreach ($arr as $part) {
            $stmt = $this->db->prepare("
                SELECT * FROM fileentries WHERE parent_id = :parent_id AND name = :name;
            ");
            $stmt->bindValue(':parent_id', $parentId);
            $stmt->bindValue(':name', $part);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($result === false) {
                throw new \Exception(sprintf("Error finding %s", esc_html($relativeFilename)));
            }
            $parentId = $result['id'];
        }

        if ($result !== null) {
            $entry = new FileEntry();
            foreach ($result as $key => $value) {
                $entry->$key = $value;
            }
            return $entry;
        } else {
            return null;
        }
    }

    private static $cache = [];
    public function calcFullPath(FileEntry $fileEntry, string $root = ''): string {
        $full = $fileEntry->name;
        while ($fileEntry->parent_id != 0) {

            $parent = self::$cache[$fileEntry->parent_id] ?? false;
            if ($parent == false) {
                $parent = $this->findById($fileEntry->parent_id);
                self::$cache[$fileEntry->parent_id] = $parent;
            }

            if ($parent->name != '') {
                $full = $parent->name . '/' . $full;
            }
            $fileEntry = $parent;
        }
        $full = $root . '/' . $full;
        return $full;
    }

    public function count(string $type = ''): int {
        $types = $type !== ''
            ? [ $type]
            : [ FileEntry::TYPE_DIR, FileEntry::TYPE_FILE ];
        $types = "'" . implode("','", $types) . "'";
        $stmt = $this->db->query("SELECT COUNT(*) FROM fileentries WHERE type in ($types)");

        return (int) $stmt->fetchColumn();
    }

    public function get(int $skip, int $count, string $type = ''): array {
        $types = $type !== ''
            ? [ $type]
            : [ FileEntry::TYPE_DIR, FileEntry::TYPE_FILE ];
        $types = "'" . implode("','", $types) . "'";
        $stmt = $this->db->prepare("
            SELECT * FROM fileentries WHERE type in ($types) LIMIT $count OFFSET $skip
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $results = [];
        foreach ($rows as $row) {
            $entry = new FileEntry();
            foreach ($row as $key => $value) {
                $entry->$key = $value;
            }
            $results[] = $entry;
        }

        return $results;
    }

}
