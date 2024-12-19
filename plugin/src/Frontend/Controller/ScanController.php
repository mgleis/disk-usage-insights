<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\Collect\ScanDirForSubDirsJob;
use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Domain\FileEntry;
use Mgleis\DiskUsageInsights\Plugin;
use Mgleis\DiskUsageInsights\WpHelper;

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
        $snapshotName = gmdate('Ymd_His_') . random_int(10000, 99999);
        $database = (new DatabaseRepository())->loadDatabase($snapshotName);
        $snapshot = $database->snapshotRepository->load();
        $snapshot->root = rtrim(ABSPATH, '/');
        $database->snapshotRepository->save($snapshot);

        // create root entry ""
        $rootFileEntry = new FileEntry();
        $rootFileEntry->type = FileEntry::TYPE_DIR;
        $database->fileEntryRepository->createOrUpdate($rootFileEntry);

        $database->q->push((new ScanDirForSubDirsJob($rootFileEntry->id))->toArray());

        // NEW Code
        $WP_NONCE = wp_create_nonce(Plugin::NONCE);
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        $WP_SNAPSHOT_FILE = $snapshotName;
        $WP_PLUGIN_URL = WpHelper::getPluginUrl();

        include_once __DIR__ . '/../../../views/scan.php';

        wp_die(); // All ajax handlers should die when finished
    }

}
