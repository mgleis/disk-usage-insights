<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\Database;
use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Frontend\Table;
use Mgleis\DiskUsageInsights\Plugin;
use Mgleis\DiskUsageInsights\WpHelper;

class ResultsController {

    private Database $database;

    public function execute() {

        // TODO Validate
        $snapshotName = $_GET['snapshot'];

        $this->database = (new DatabaseRepository())->loadDatabase($snapshotName);
        $sn = $this->database->snapshotRepository->load();
        if ($sn->collectPhaseFinished !== 1) {
            echo "Cannot read database because it is corrupt/incomplete. Please create a new snapshot.";
            exit;
        }

        $WP_PLUGIN_URL = WpHelper::getPluginUrl();
        $WP_NONCE = wp_create_nonce(Plugin::NONCE);
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        // TODO validate
        $snapshot = $_GET['snapshot'];
        $root = ABSPATH; // TODO take from kvstore
        $totalSize = $this->selectInt("SELECT SUM(size) FROM fileentries;");

        include_once __DIR__ . '/../../../views/results.php';

        //
        // Largest Files
        //
        $rows = $this->fetchAssoc("SELECT * FROM fileentries WHERE type in ('file') ORDER BY size DESC LIMIT 10");
        $table = [];
        foreach ($rows as $row) {
            $table[] = [
                $row['name'],
                number_format_i18n($row['size']),
                number_format_i18n(100 * $row['size'] / $totalSize, 2) . '%'
            ];
        }
        (new Table(
            'Largest Files',
            ['Filename', 'Size', '%'],
            ['', 'DUI-table__col--number', 'DUI-table__col--number']
        ))->withPercentBar(2, 2)->withData($table)->output();

        //
        // Largest Folders (files only)
        //
        $rows = $this->fetchAssoc("SELECT * FROM fileentries WHERE type in ('dir') ORDER BY dir_size DESC LIMIT 10");
        $table = [];
        foreach ($rows as $row) {
            $table[] = [
                $row['name'],
                number_format_i18n($row['dir_size']),
                number_format_i18n($row['dir_count']),
                number_format_i18n(round($row['dir_size'] / $row['dir_count'])),
                number_format_i18n(100 * $row['dir_size'] / $totalSize, 2) . '%'
            ];
        }
        (new Table(
            'Largest Folders (files only)',
            ['Folder', 'File Sizes', 'File Count', 'Avg File Size', '%'],
            ['', 'DUI-table__col--number', 'DUI-table__col--number', 'DUI-table__col--number', 'DUI-table__col--number']
        ))->withPercentBar(4, 4)->withData($table)->output();

        //
        // Largest Folders (incl. sub folders)
        //
        $rows = $this->fetchAssoc("SELECT * FROM fileentries WHERE type in ('dir') ORDER BY dir_recursive_count DESC LIMIT 10");
        $table = [];
        foreach ($rows as $row) {
            $table[] = [$row['name'], number_format_i18n($row['dir_recursive_size'])];
        }
        (new Table(
            'Largest Folders (incl. sub folders)',
            ['Folder', 'Total Size'],
            ['', 'DUI-table__col--number']
        ))->withData($table)->output();

        //
        // Folders with most files
        //
        $rows = $this->fetchAssoc("SELECT * FROM fileentries WHERE type in ('dir') ORDER BY dir_count DESC LIMIT 10");
        $table = [];
        foreach ($rows as $row) {
            $table[] = [
                $row['name'],
                number_format_i18n($row['dir_count']),
                number_format_i18n($row['dir_size']),
                number_format_i18n(round($row['dir_size'] / $row['dir_count']))
            ];
        }
        (new Table(
            'Folders with most files',
            ['Folder', 'File Count', 'File Size', 'Avg File Size'],
            ['', 'DUI-table__col--number', 'DUI-table__col--number', 'DUI-table__col--number']
        ))->withData($table)->output();


        //
        // Largest Directories within /wp-content/plugins
        //
        // TODO

        //
        // Largest Directories within /wp-content/themes
        //
        // TODO

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
