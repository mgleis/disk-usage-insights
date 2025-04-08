<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Domain\Jobs\PhaseCoordinatorJob;
use Mgleis\DiskUsageInsights\Plugin;

class ScanStatusController {

    public function status() {
        check_ajax_referer(Plugin::NONCE);

        // TODO validate value: ensure file exists in data directory
        $snapshotName = sanitize_file_name(wp_unslash($_POST['snapshot'] ?? ''));

        $database = (new DatabaseRepository())->loadDatabase($snapshotName);

        // if process finished = stop reloading
        $_snapshot = $database->snapshotRepository->load();
        if ($_snapshot->collectPhaseFinished === 1) {
            http_response_code(286); // htmx stops timer
            header('HX-Redirect: ' . admin_url('tools.php?page=disk-usage-insights&snapshot=' . $snapshotName));
            exit;
        }
        $currentPhase = $_snapshot->phase;

        echo "<br>\n";
        foreach(PhaseCoordinatorJob::PHASES as $idx => $phaseText) {

            $phaseStatus = $currentPhase > $idx ? '&check; ' : '';
            $taskDescription = '';

            if ($currentPhase == $idx) {

                $job = $database->q->peek();
                if ($job !== null) {
                    $reflect = new \ReflectionClass($job->payload['type']);
                    $instance = $reflect->newInstanceArgs($job->payload['args']);

                    $taskDescription = ' -- ' . $instance->toDescription();
                }

            }
            if ($idx == $currentPhase) {
                echo "<b>";
            }
            echo sprintf('%s%s%s', $phaseStatus, esc_html($phaseText), esc_html($taskDescription));
            if ($idx == $currentPhase) {
                echo "</b>";
            }
            echo "<br>\n";
        }

        wp_die(); // All ajax handlers should die when finished
    }

}
