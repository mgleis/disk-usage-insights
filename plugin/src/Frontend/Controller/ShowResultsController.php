<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\Database;
use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Plugin;
use Mgleis\DiskUsageInsights\WpHelper;

class ShowResultsController {

    private Database $database;

    public function execute() {
        $snapshot = sanitize_file_name(wp_unslash($_GET['snapshot'] ?? ''));

        $this->database = (new DatabaseRepository())->loadDatabase($snapshot);
        $sn = $this->database->snapshotRepository->load();
        if ($sn->collectPhaseFinished !== 1) {
            echo "Cannot read database because it is corrupt/incomplete. Please create a new snapshot.";
            exit;
        }
        $root = $sn->root;

        $WP_PLUGIN_URL = WpHelper::getPluginUrl();
        $WP_NONCE = wp_create_nonce(Plugin::NONCE);
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        $WP_SNAPSHOT_FILE = $snapshot;

        $totalSize = $this->selectInt("SELECT SUM(size) FROM fileentries;");
        $barChart = $this->createBarchart($totalSize);

        include_once __DIR__ . '/../../../views/results.php';
    }

    private function createBarchart(int $totalSize) {
        // Echo WordPress directories
//        echo "WP Content Dir: " . WP_CONTENT_DIR . "\n";
//        echo "Uploads Dir: " . wp_upload_dir()['basedir'] . "\n";
//        echo "Themes Dir: " . get_theme_root() . "\n";
//        echo "Plugins Dir: " . WP_PLUGIN_DIR . "\n";

        // Bar Chart
        $wpCoreSize = $this->selectInt("SELECT SUM(size) FROM fileentries WHERE is_wp_core_file = 1;");
        $uploadsSize = $this->selectInt("SELECT dir_recursive_size FROM fileentries WHERE name = 'uploads';"); // TODO wrong
        $themesSize = $this->selectInt("SELECT dir_recursive_size FROM fileentries WHERE name = 'themes';"); // TODO wrong
        $pluginsSize = $this->selectInt("SELECT dir_recursive_size FROM fileentries WHERE name = 'plugins';"); // TODO wrong
        $wpContentSize = $this->selectInt("SELECT dir_recursive_size FROM fileentries WHERE name = 'wp-content';") // TODO wrong
            - $themesSize - $pluginsSize - $uploadsSize;
        $barChart = [
            ['label' => 'WP Core',      'percent' => round(100 * $wpCoreSize / $totalSize), 'mb' => round($wpCoreSize / 1_000_000, 1)],
            ['label' => 'Uploads',      'percent' => round(100 * $uploadsSize / $totalSize), 'mb' => round($uploadsSize / 1_000_000, 1)],
            ['label' => 'Themes',       'percent' => round(100 * $themesSize / $totalSize), 'mb' => round($themesSize / 1_000_000, 1)],
            ['label' => 'Plugins',      'percent' => round(100 * $pluginsSize / $totalSize), 'mb' => round($pluginsSize / 1_000_000, 1)],
            ['label' => 'wp-content',   'percent' => round(100 * $wpContentSize / $totalSize), 'mb' => round($wpContentSize / 1_000_000, 1)],
        ];

        return $barChart;
    }

    private function selectInt(string $sql) {
        $stmt = $this->database->q->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result[0];
    }

}
