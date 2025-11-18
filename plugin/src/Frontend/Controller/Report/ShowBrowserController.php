<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller\Report;

use Mgleis\DiskUsageInsights\Domain\Database;
use Mgleis\DiskUsageInsights\Domain\FileEntry;
use Mgleis\DiskUsageInsights\Frontend\Pagination;
use Mgleis\DiskUsageInsights\Frontend\Table;
use PDO;

class ShowBrowserController {

    private PDO $db;

    public function execute(Database $database) {
        $this->db = $database->q->db;

        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        $WP_SNAPSHOT_FILE = sanitize_file_name(wp_unslash($_GET['snapshot'] ?? ''));

        $totalSize = $this->selectInt($database, "SELECT SUM(size) FROM fileentries;");

        $parent_id = $_GET['parent_id'] ?? 1;


        $parentDir = $this->findById($parent_id);
        $breadCrumbs = $this->calcDirBreadcrumbs($parentDir);
        $retval = [];
        foreach ($breadCrumbs as $bc) {
            $retval[] = [
                'name' => $bc->name,
                'link' => sprintf(
                    '%s?action=dui_results_table&table=browser&snapshot=%s&parent_id=%s',
                    esc_url($WP_ADMIN_AJAX_URL),
                    esc_js($WP_SNAPSHOT_FILE),
                    esc_js($bc->id)
                )
            ];
        }
        $breadCrumbs = $retval;

        $pagination = new Pagination('', 0, 0, 10);

        $rows = $this->fetchAssoc($database, sprintf(
            "SELECT * FROM fileentries WHERE parent_id = %s ORDER BY CASE WHEN type = 'dir' THEN dir_recursive_size ELSE size END DESC LIMIT %s OFFSET %s",
            $parent_id, $pagination->getItemsPerPage(), $pagination->calcOffset())
        );
        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'size' => $row['type'] == 'dir' ? $row['dir_recursive_size'] : $row['size'],
                'type' => $row['type'],
                'link' => $row['type'] == 'dir'
                    ? sprintf(
                        '%s?action=dui_results_table&table=browser&snapshot=%s&parent_id=%s',
                        esc_url($WP_ADMIN_AJAX_URL),
                        esc_js($WP_SNAPSHOT_FILE),
                        esc_js($row['id'])
                    )
                    : ''
            ];
        }
        $totalSize = array_reduce($items, fn($carry, $item) => $carry + $item['size'], 0);
        foreach ($items as &$item) {
            $item['percent'] = $totalSize > 0 ? round(100 * $item['size'] / $totalSize, 2) : 0;
        }
        $last = 0;
        for ($i = 0; $i < count($items); $i++) {
            $current = $last + $items[$i]['percent'];
            $items[$i]['conic'] = "{$last}% {$current}%";
            $last = $current;
        }

        include_once __DIR__ . '/../../../../views/results/browser.php';

        wp_die(); // All ajax handlers should die when finished

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

    private static $cache = [];
    public function calcDirBreadcrumbs(FileEntry $fileEntry): array {
        $retval = [];

        while ($fileEntry->parent_id != 0) {
            $retval[] = $fileEntry;
            $fileEntry = $this->findById($fileEntry->parent_id);
        }
        $fileEntry->name = 'ROOT';
        $retval[] = $fileEntry;

        return array_reverse($retval);
    }

    public function findById(int $id): FileEntry {

        $stmt = $this->db->prepare("
            SELECT * FROM fileentries WHERE id =:id
        ");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new \Exception(sprintf("No file entry with id %s found.", esc_html($id)));
        }

        $entry = new FileEntry();
        foreach ($result as $key => $value) {
            $entry->$key = $value;
        }

        return $entry;
    }


}