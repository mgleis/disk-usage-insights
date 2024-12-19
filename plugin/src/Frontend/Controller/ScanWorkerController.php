<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Domain\FileEntryRepository;
use Mgleis\DiskUsageInsights\Domain\SnapshotRepository;
use Mgleis\DiskUsageInsights\Plugin;
use Mgleis\PhpSqliteJobQueue\Job;
use Mgleis\PhpSqliteJobQueue\Queue;
use Mgleis\PhpSqliteJobQueue\Worker;

class ScanWorkerController {

    public function worker() {
        check_ajax_referer(Plugin::NONCE);

        // TODO validate value: ensure file exists in data directory
        $snapshotName = $_POST['snapshot'];

        $database = (new DatabaseRepository())->loadDatabase($snapshotName);

        // if process finished = stop reloading
        $snapshot = $database->snapshotRepository->load();
        if ($snapshot->collectPhaseFinished === 1) {
            http_response_code(286);  // htmx stops timer
            echo "DONE";
            exit;
        }

        $w = (new Worker($database->q))
            ->withMaxTotalRuntimeInSeconds(5)
            ->withSleepTimeBetweenJobsInMilliseconds(1)
            ->withSleepTimeOnEmptyQueueInMilliseconds(1)
        ;
        $w->process(function(Job $job) use ($database) {
            $reflect = new \ReflectionClass($job->payload['type']);
            $instance = $reflect->newInstanceArgs($job->payload['args']);
            $instance->setQueue($database->q);
            $instance->setFileEntryRepository($database->fileEntryRepository);
            $instance->setSnapshotRepository($database->snapshotRepository);
            $instance->work();
        });

        wp_die(); // All ajax handlers should die when finished
    }

}
