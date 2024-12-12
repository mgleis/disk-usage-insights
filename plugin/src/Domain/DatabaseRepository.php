<?php

namespace Mgleis\DiskUsageInsights\Domain;

use Mgleis\PhpSqliteJobQueue\Queue;

class DatabaseRepository {

    public function listDatabases(): array {

        $dir = realpath(__DIR__ . '/../../output');
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

        $dir = realpath(__DIR__ . '/../../output');
        $file = $dir . '/' . $databaseName . '.db';

        $db = new Database();
        $db->q = new Queue($file);
        $db->fileEntryRepository = new FileEntryRepository($db->q->db);
        $db->snapshotRepository = new SnapshotRepository($db->q->db);

        return $db;
    }

}