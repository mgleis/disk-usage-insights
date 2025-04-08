<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller\Report;

use Mgleis\DiskUsageInsights\Domain\Database;
use Mgleis\DiskUsageInsights\Frontend\Pagination;
use Mgleis\DiskUsageInsights\Frontend\Table;


class ShowFoldersMostFilesController {

    public function execute(Database $database) {
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        $WP_SNAPSHOT_FILE = sanitize_file_name(wp_unslash($_GET['snapshot'] ?? ''));

        $totalSize = $this->selectInt($database, "SELECT SUM(size) FROM fileentries;");

        if (isset($_GET['p'])) {

            $pagination = Pagination::parseFromString($_SERVER['REQUEST_URI']);

        } else {

            $url = sprintf('%s?action=dui_results_table&table=%s&snapshot=%s',
                esc_url($WP_ADMIN_AJAX_URL),
                'folders-most-files',
                esc_js($WP_SNAPSHOT_FILE)
            );

            $totalFileCount = $this->selectInt($database, "SELECT COUNT(*) FROM fileentries WHERE type in ('dir') ");

            $pagination = new Pagination($url, $totalFileCount, 0, 10);
        }

        //
        // Folders with most files
        //
        $rows = $this->fetchAssoc($database, sprintf("SELECT * FROM fileentries WHERE type in ('dir') ORDER BY dir_count DESC LIMIT %s OFFSET %s", $pagination->getItemsPerPage(), $pagination->calcOffset()));
        $table = [];
        foreach ($rows as $row) {
            $fileEntry = $database->fileEntryRepository->findById($row['id']);
            $relativeName = $database->fileEntryRepository->calcFullPath($fileEntry);
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
            ['DUI-table__col--grow', 'DUI-table__col--number', 'DUI-table__col--number', 'DUI-table__col--number']
        ))
            ->withData($table)
            ->withPagination($pagination)
            ->output();

    }

    private function fetchAssoc(Database $database, string $sql): array {
        $stmt = $database->q->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }

    private function selectInt(Database $database, string $sql) {
        $stmt = $database->q->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result[0];
    }

}