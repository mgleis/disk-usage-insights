<?php

namespace Mgleis\DiskUsageInsights\Domain\Jobs;

use Mgleis\DiskUsageInsights\Domain\FileEntryRepository;
use Mgleis\DiskUsageInsights\Domain\SnapshotRepository;
use Mgleis\PhpSqliteJobQueue\Queue;

class BaseJob {
    protected Queue $queue;
    protected FileEntryRepository $fileEntryRepository;
    protected SnapshotRepository $snapshotRepository;

    public function setQueue(Queue $q) {
        $this->queue = $q;
    }

    public function setFileEntryRepository(FileEntryRepository $fileEntryRepository) {
        $this->fileEntryRepository = $fileEntryRepository;
    }

    public function setSnapshotRepository(SnapshotRepository $snapshotRepository) {
        $this->snapshotRepository = $snapshotRepository;
    }

    protected function log(string $s) {
        echo esc_html($s)."\n";
        //error_log($s);
    }

    public function toDescription(): string {
        return static::class;
    }

}
