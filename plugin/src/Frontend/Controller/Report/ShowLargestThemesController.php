<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller\Report;

use Mgleis\DiskUsageInsights\Domain\Database;
use Mgleis\DiskUsageInsights\Frontend\Table;

class ShowLargestThemesController {


    public function execute(Database $database) {
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        $WP_SNAPSHOT_FILE = sanitize_file_name(wp_unslash($_GET['snapshot'] ?? ''));

        //
        // Largest Directories within /wp-content/themes
        //
        $themeDir = substr(get_theme_root(), strlen(rtrim(ABSPATH, '/')));  // e.g. '/wp-content/themes'  TODO store in snapshot
        $fileEntry = $database->fileEntryRepository->findByRelativeName($themeDir);
        if ($fileEntry !== null) {

            $rows = $this->fetchAssoc($database, sprintf("SELECT * FROM fileentries WHERE parent_id = %s AND type = 'dir' ORDER BY dir_recursive_size DESC LIMIT 10", $fileEntry->id));
            $table = [];
            foreach ($rows as $row) {
                $fileEntry = $database->fileEntryRepository->findById($row['id']);
                $relativeName = $row['name'];
                $table[] = [
                    $relativeName, 
                    number_format_i18n($row['dir_recursive_size'])
                ];
            }
            (new Table(
                'Largest Theme Folders',
                ['Folder', 'Total Size'],
                ['DUI-table__col--grow', 'DUI-table__col--number']
            ))->withData($table)->output();

        } else {
            echo "theme dir not found";
        }

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