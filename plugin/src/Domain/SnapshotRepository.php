<?php

namespace Mgleis\DiskUsageInsights\Domain;

use Mgleis\PhpSqliteKeyValueStore\KeyValueStore;

class SnapshotRepository {

    private KeyValueStore $kvstore;

    public function __construct(\PDO $pdo) {
        $this->kvstore = new KeyValueStore($pdo);
    }

    public function load(): Snapshot {
        $s = new Snapshot();
        $s->version = $this->kvstore->get('version', Snapshot::CURRENT_VERSION);
        $s->root = $this->kvstore->get('root', '');
        $s->phase = (int) $this->kvstore->get('phase', 0);
        $s->wpcorefiles = $this->kvstore->get('wpcorefiles', []);

        return $s;
    }

    public function save(Snapshot $snapshot) {
        $this->kvstore->set('version', $snapshot->version);
        $this->kvstore->set('root', $snapshot->root);
        $this->kvstore->set('phase', $snapshot->phase);
        $this->kvstore->set('wpcorefiles', $snapshot->wpcorefiles);
    }

}
