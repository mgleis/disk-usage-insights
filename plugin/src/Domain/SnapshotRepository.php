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
        $s->version = $this->kvstore->get('snapshot-version', Snapshot::CURRENT_VERSION);
        $s->root = $this->kvstore->get('snapshot-root', '');
        $s->phase = (int) $this->kvstore->get('snapshot-phase', 0);
        $s->wpcorefiles = $this->kvstore->get('snapshot-wpcorefiles', []);
        $s->collectPhaseFinished = $this->kvstore->get('snapshot-collectPhaseFinished', 0);

        return $s;
    }

    public function save(Snapshot $snapshot) {
        $this->kvstore->set('snapshot-version', $snapshot->version);
        $this->kvstore->set('snapshot-root', $snapshot->root);
        $this->kvstore->set('snapshot-phase', $snapshot->phase);
        $this->kvstore->set('snapshot-wpcorefiles', $snapshot->wpcorefiles);
        $this->kvstore->set('snapshot-collectPhaseFinished', $snapshot->collectPhaseFinished);
    }

}
