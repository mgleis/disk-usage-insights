<?php

use Mgleis\DiskUsageInsights\Domain\FileEntryRepository;
use Mgleis\DiskUsageInsights\Domain\Collect\ScanDirForSubDirsJob;
use Mgleis\DiskUsageInsights\Domain\SnapshotRepository;
use Mgleis\PhpSqliteJobQueue\Job;
use Mgleis\PhpSqliteJobQueue\Queue;
use Mgleis\PhpSqliteJobQueue\Worker;

require_once 'vendor/autoload.php';

if (!function_exists('wp_get_wp_version')) {
    require_once '../dev/wp_hints.php';
}

$q = new Queue("database.db");

$q->push((new ScanDirForSubDirsJob(__DIR__.'/../../'))->toArray());
//$q->push((new ScanForSubDirsJob(__DIR__))->toArray());

$fileEntryRepository = new FileEntryRepository($q->db);
$snapshotRepository = new SnapshotRepository($q->db);

$w = (new Worker($q))
    ->withSleepTimeBetweenJobsInMilliseconds(1)
;
$w->process(function(Job $job) use ($q, $fileEntryRepository, $snapshotRepository) {

    $reflect = new \ReflectionClass($job->payload['type']);
    $instance = $reflect->newInstanceArgs($job->payload['args']);
    $instance->setQueue($q);
    $instance->setFileEntryRepository($fileEntryRepository);
    $instance->setSnapshotRepository($snapshotRepository);
    $instance->work();

});

