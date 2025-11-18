<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Frontend\Controller\Report\ShowBrowserController;
use Mgleis\DiskUsageInsights\Frontend\Controller\Report\ShowFoldersMostFilesController;
use Mgleis\DiskUsageInsights\Frontend\Controller\Report\ShowLargestFilesController;
use Mgleis\DiskUsageInsights\Frontend\Controller\Report\ShowLargestFoldersFilesOnlyController;
use Mgleis\DiskUsageInsights\Frontend\Controller\Report\ShowLargestFoldersIncludingSubFoldersController;
use Mgleis\DiskUsageInsights\Frontend\Controller\Report\ShowLargestPluginsController;
use Mgleis\DiskUsageInsights\Frontend\Controller\Report\ShowLargestThemesController;
use Mgleis\DiskUsageInsights\Plugin;


class ShowResultsTableController {

    public function execute() {
        // TODO Security

        $WP_NONCE = wp_create_nonce(Plugin::NONCE);
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        $WP_SNAPSHOT_FILE = sanitize_file_name(wp_unslash($_GET['snapshot'] ?? ''));

        $database = (new DatabaseRepository())->loadDatabase($WP_SNAPSHOT_FILE);
        $sn = $database->snapshotRepository->load();
        if ($sn->collectPhaseFinished !== 1) {
            echo "Cannot read database because it is corrupt/incomplete. Please create a new snapshot.";
            exit;
        }
        $table = sanitize_file_name(wp_unslash($_GET['table'] ?? ''));

        switch ($table) {
            case 'largest-files':
                (new ShowLargestFilesController())->execute($database);
                break;
            case 'largest-folders-files':
                (new ShowLargestFoldersFilesOnlyController())->execute($database);
                break;
            case 'largest-folders-sub-folders':
                (new ShowLargestFoldersIncludingSubFoldersController())->execute($database);
                break;
            case 'folders-most-files':
                (new ShowFoldersMostFilesController())->execute($database);
                break;
            case 'largest-themes':
                (new ShowLargestThemesController())->execute($database);
                break;
            case 'largest-plugins':
                (new ShowLargestPluginsController())->execute($database);
                break;
            case 'browser':
                (new ShowBrowserController())->execute($database);
                break;
            default:
                echo "unknown table";
                break;
        }

        wp_die();
    }

}