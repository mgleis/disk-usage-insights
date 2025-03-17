<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Plugin;
use Mgleis\PhpSqliteJobQueue\Job;
use Mgleis\PhpSqliteJobQueue\Worker;

class ScanWorkerController {

    public function worker() {
        check_ajax_referer(Plugin::NONCE);

        // TODO validate value: ensure file exists in data directory
        $snapshotName = sanitize_file_name(wp_unslash($_POST['snapshot'] ?? ''));

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
            ->withSleepTimeBetweenJobsInMilliseconds(0)
            ->withSleepTimeOnEmptyQueueInMilliseconds(0)
        ;

        $w->process(function(Job $job) use ($database) {
            $reflect = new \ReflectionClass($job->payload['type']);
            $instance = $reflect->newInstanceArgs($job->payload['args']);
            $instance->setQueue($database->q);
            $instance->setFileEntryRepository($database->fileEntryRepository);
            $instance->setSnapshotRepository($database->snapshotRepository);

            $database->q->db->beginTransaction();
            $instance->work();
            $database->q->db->commit();
        });

        wp_die(); // All ajax handlers should die when finished
    }

}
