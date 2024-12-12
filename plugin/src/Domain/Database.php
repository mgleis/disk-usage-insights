<?php

namespace Mgleis\DiskUsageInsights\Domain;

use Mgleis\PhpSqliteJobQueue\Queue;

class Database {

    public Queue $q;

    public FileEntryRepository $fileEntryRepository;

    public SnapshotRepository $snapshotRepository;

}
