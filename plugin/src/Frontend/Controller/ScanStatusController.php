<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Plugin;

class ScanStatusController {

    public function status() {
        check_ajax_referer(Plugin::NONCE);

        // TODO validate value: ensure file exists in data directory
        $snapshotName = $_POST['snapshot'];

        $database = (new DatabaseRepository())->loadDatabase($snapshotName);

        // if process finished = stop reloading
        $_snapshot = $database->snapshotRepository->load();
        if ($_snapshot->collectPhaseFinished === 1) {
            http_response_code(286); // htmx stops timer
            header('HX-Redirect: ' . admin_url('tools.php?page=disk-usage-insights&snapshot=' . $snapshotName));
            wp_die();
        }

        $job = $database->q->top();
        if ($job !== null) {

            $reflect = new \ReflectionClass($job->payload['type']);
            $instance = $reflect->newInstanceArgs($job->payload['args']);
            echo sprintf('Phase %s / %s<br>%s',
                $_snapshot->phase + 1,
                10,
                $instance->toDescription()
            );
        }

        wp_die(); // All ajax handlers should die when finished
    }

}
