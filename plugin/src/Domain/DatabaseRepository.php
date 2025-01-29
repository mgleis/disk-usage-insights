<?php

namespace Mgleis\DiskUsageInsights\Domain;

use Mgleis\PhpSqliteJobQueue\Queue;

class DatabaseRepository {

    private const OUTPUT_DIR = __DIR__ . '/../../output';

    public function listDatabases(): array {

        $dir = realpath(self::OUTPUT_DIR);
        $pattern = $dir . '/*.db';
        $files = glob($pattern);
        rsort($files);
        $results = [];
        foreach ($files as $file) {
            $results[] = [
                'filename' => substr(basename($file), 0, strlen(basename($file)) - 3),
                'filesize' => filesize($file)
            ];
        }
        return $results;
    }

    public function loadDatabase(string $databaseName): Database {
        // TODO verify $databaseName
        $dir = realpath(self::OUTPUT_DIR);
        $file = $dir . '/' . $databaseName . '.db';

        $db = new Database();
        $db->q = new Queue($file);
        $db->fileEntryRepository = new FileEntryRepository($db->q->db);
        $db->snapshotRepository = new SnapshotRepository($db->q->db);

        return $db;
    }

    public function deleteDatabase(string $databaseName) {
        // TODO verify $databaseName
        $dir = realpath(self::OUTPUT_DIR);
        $file = $dir . '/' . $databaseName . '.db';
        $success = wp_delete_file($file);
    }

}
