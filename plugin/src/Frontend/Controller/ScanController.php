<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\Collect\ScanDirForSubDirsJob;
use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Plugin;
use Mgleis\PhpSqliteJobQueue\Queue;

class ScanController {


    public function scan() {
        check_ajax_referer(Plugin::NONCE);
/*
        // TODO: Remove Code
        // OLD v1.1 code:
        $scanResults = new ScanResults();
        $scanResults->execute();

        sleep(1);
*/

        // Create a new Snapshot Database
        $snapshot = date('Ymd_His_') . rand(10000, 99999);
        $database = (new DatabaseRepository())->loadDatabase($snapshot);
        $database->q->push((new ScanDirForSubDirsJob(__DIR__.'/../../../../../'))->toArray());

        // NEW Code
        $WP_NONCE = wp_create_nonce(Plugin::NONCE);
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        $WP_SNAPSHOT_FILE = $snapshot;
        $WP_PLUGIN_URL = plugin_dir_url(__DIR__);

        include __DIR__ . '/../../../views/scan.php';

        wp_die(); // All ajax handlers should die when finished
    }

}
