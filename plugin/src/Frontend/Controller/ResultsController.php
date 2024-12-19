<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\Database;
use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Domain\FileEntry;
use Mgleis\DiskUsageInsights\Frontend\Table;
use Mgleis\DiskUsageInsights\Plugin;
use Mgleis\DiskUsageInsights\WpHelper;

class ResultsController {

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

        $WP_PLUGIN_URL = WpHelper::getPluginUrl();
        $WP_NONCE = wp_create_nonce(Plugin::NONCE);
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        // TODO validate
        $snapshot = sanitize_file_name(wp_unslash($_GET['snapshot'] ?? ''));
        $root = $this->database->snapshotRepository->load()->root;
        $totalSize = $this->selectInt("SELECT SUM(size) FROM fileentries;");

        include_once __DIR__ . '/../../../views/results.php';

        //
        // Largest Files
        //
        $rows = $this->fetchAssoc("SELECT * FROM fileentries WHERE type in ('file') ORDER BY size DESC LIMIT 10");
        $table = [];
        foreach ($rows as $row) {
            $fileEntry = $this->database->fileEntryRepository->findById($row['id']);
            $relativeName = $this->database->fileEntryRepository->calcFullPath($fileEntry);
            $table[] = [
                $relativeName,
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
            $fileEntry = $this->database->fileEntryRepository->findById($row['id']);
            $relativeName = $this->database->fileEntryRepository->calcFullPath($fileEntry);
            $table[] = [
                $relativeName,
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
        $rows = $this->fetchAssoc("SELECT * FROM fileentries WHERE type in ('dir') ORDER BY dir_recursive_size DESC LIMIT 10");
        $table = [];
        foreach ($rows as $row) {
            $fileEntry = $this->database->fileEntryRepository->findById($row['id']);
            $relativeName = $this->database->fileEntryRepository->calcFullPath($fileEntry);
            $table[] = [$relativeName, number_format_i18n($row['dir_recursive_size'])];
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
            $fileEntry = $this->database->fileEntryRepository->findById($row['id']);
            $relativeName = $this->database->fileEntryRepository->calcFullPath($fileEntry);
            $table[] = [
                $relativeName,
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
        $pluginDir = substr(WP_PLUGIN_DIR, strlen(rtrim(ABSPATH, '/')));    // e.g. '/wp-content/plugins' TODO store in snapshot
        $fileEntry = $this->database->fileEntryRepository->findByRelativeName($pluginDir);
        if ($fileEntry !== null) {

            $rows = $this->fetchAssoc(sprintf("SELECT * FROM fileentries WHERE parent_id = %s AND type = 'dir' ORDER BY dir_recursive_size DESC LIMIT 10", $fileEntry->id));
            $table = [];
            foreach ($rows as $row) {
                $fileEntry = $this->database->fileEntryRepository->findById($row['id']);
                $relativeName = $row['name'];
                $table[] = [
                    $relativeName, 
                    number_format_i18n($row['dir_recursive_size'])
                ];
            }
            (new Table(
                'Largest Plugin Folders',
                ['Folder', 'Total Size'],
                ['', 'DUI-table__col--number']
            ))->withData($table)->output();

        } else {
            echo "plugin dir not found";
        }

        //
        // Largest Directories within /wp-content/themes
        //
        $themeDir = substr(get_theme_root(), strlen(rtrim(ABSPATH, '/')));  // e.g. '/wp-content/themes'  TODO store in snapshot
        $fileEntry = $this->database->fileEntryRepository->findByRelativeName($themeDir);
        if ($fileEntry !== null) {

            $rows = $this->fetchAssoc(sprintf("SELECT * FROM fileentries WHERE parent_id = %s AND type = 'dir' ORDER BY dir_recursive_size DESC LIMIT 10", $fileEntry->id));
            $table = [];
            foreach ($rows as $row) {
                $fileEntry = $this->database->fileEntryRepository->findById($row['id']);
                $relativeName = $row['name'];
                $table[] = [
                    $relativeName, 
                    number_format_i18n($row['dir_recursive_size'])
                ];
            }
            (new Table(
                'Largest Theme Folders',
                ['Folder', 'Total Size'],
                ['', 'DUI-table__col--number']
            ))->withData($table)->output();

        } else {
            echo "theme dir not found";
        }

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
