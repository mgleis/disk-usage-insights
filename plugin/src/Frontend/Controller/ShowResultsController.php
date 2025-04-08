<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\Database;
use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Frontend\Table;
use Mgleis\DiskUsageInsights\Plugin;
use Mgleis\DiskUsageInsights\WpHelper;

class ShowResultsController {

    private Database $database;

    public function execute() {
        // TODO Validate
        $snapshotName = sanitize_file_name(wp_unslash($_GET['snapshot'] ?? ''));

        $this->database = (new DatabaseRepository())->loadDatabase($snapshotName);
        $sn = $this->database->snapshotRepository->load();
        if ($sn->collectPhaseFinished !== 1) {
            echo "Cannot read database because it is corrupt/incomplete. Please create a new snapshot.";
            exit;
        }

        // TODO validate
        $snapshot = sanitize_file_name(wp_unslash($_GET['snapshot'] ?? ''));
        $root = $this->database->snapshotRepository->load()->root;
        $totalSize = $this->selectInt("SELECT SUM(size) FROM fileentries;");

        $WP_PLUGIN_URL = WpHelper::getPluginUrl();
        $WP_NONCE = wp_create_nonce(Plugin::NONCE);
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        $WP_SNAPSHOT_FILE = $snapshot;

        include_once __DIR__ . '/../../../views/results.php';
    }

    private function selectInt(string $sql) {
        $stmt = $this->database->q->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result[0];
    }

    private function fetchAssoc(string $sql): array {
        $stmt = $this->database->q->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }
}
