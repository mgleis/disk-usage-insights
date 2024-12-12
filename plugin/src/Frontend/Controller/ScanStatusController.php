<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Domain\SnapshotRepository;
use Mgleis\DiskUsageInsights\Plugin;
use Mgleis\PhpSqliteJobQueue\Queue;

class ScanStatusController {

    public function status() {
        check_ajax_referer(Plugin::NONCE);

        // TODO validate value: ensure file exists in data directory
        $snapshotName = $_POST['snapshot'];

        $database = (new DatabaseRepository())->loadDatabase($snapshotName);

        // if process finished = stop reloading
        $snapshot = $database->snapshotRepository->load();
        if ($snapshot->collectPhaseFinished === 1) {
            http_response_code(286);
            header('HX-Redirect: ' . admin_url('tools.php?page=disk-usage-insights&snapshot=' . $snapshotName));

            include __DIR__ . '/../../../views/results.php';
            exit;
        }

        $job = $database->q->top();
        if ($job !== null) {

            $reflect = new \ReflectionClass($job->payload['type']);
            $instance = $reflect->newInstanceArgs($job->payload['args']);
            echo $instance->toDescription();
        }

        wp_die(); // All ajax handlers should die when finished
    }

}
