<?php
namespace Mgleis\DiskUsageInsights\Frontend;

use Mgleis\DiskUsageInsights\Domain\DiskUsage;
use Mgleis\DiskUsageInsights\Frontend\Table;

class ScanResults {

    public function execute() {

        $root = \ABSPATH;

        $d = new DiskUsage($root);
        $res = $d->scan();
        $totalSize = $d->calculateDirTotalSizes($res);
        $d->calculateDirFileSizes($res);
        $d->calculateDirFileCount($res);

        echo '<div class="DUI-panel">';
        echo '<div class="DUI-panel__content">';
        echo sprintf("Root Directory: %s<br>\n", esc_html($root));
        echo sprintf("Total Size: %s<br>\n", esc_html(number_format_i18n($totalSize)));
        echo '</div>';
        echo '</div>';

        // Largest Files
        $largestFiles = $d->calculateLargestFiles(20, $res);
        $table = [];
        foreach ($largestFiles as $file) {
            $table[] = [
                $file['abs'],
                number_format_i18n($file['size']),
                number_format_i18n(100 * $file['size'] / $totalSize, 2) . '%'
            ];
        }
        (new Table(
                'Largest Files',
                ['Filename', 'Size', '%'],
                ['', 'DUI-table__col--number', 'DUI-table__col--number']
            ))->output($table);


        // Largest Folders (files only)
        $largestFiles = $d->calculateLargestFolders(20, $res);
        $table = [];
        foreach ($largestFiles as $file) {
            $table[] = [
                $file['abs'],
                number_format_i18n($file['fileSizes']),
                number_format_i18n($file['fileCount']),
                number_format_i18n(round($file['fileSizes'] / $file['fileCount'])),
                number_format_i18n(100 * $file['fileSizes'] / $totalSize, 2) . '%'
            ];
        }
        (new Table(
                'Largest Folders (files only)', 
                ['Folder', 'File Sizes', 'File Count', 'Avg File Size', '%'],
                ['', 'DUI-table__col--number', 'DUI-table__col--number', 'DUI-table__col--number', 'DUI-table__col--number'
            ]))->output($table);


        // Largest Folders (incl. sub folders)
        $largestFiles = $d->calculateLargestFoldersRecursive(20, $res);
        $table = [];
        foreach ($largestFiles as $file) {
            $table[] = [$file['abs'], number_format_i18n($file['totalSize'])];
        }
        (new Table('Largest Folders (incl. sub folders)', ['Folder', 'Total Size'], ['', 'DUI-table__col--number']))->output($table);


        // Folders with most files
        $files = $d->calculateFoldersWithMostFiles(20, $res);
        $table = [];
        foreach ($files as $file) {
            $table[] = [
                $file['abs'],
                number_format_i18n($file['fileCount']),
                number_format_i18n($file['fileSizes']),
                number_format_i18n(round($file['fileSizes'] / $file['fileCount']))
            ];
        }
        (new Table(
                'Folders with most files',
                ['Folder', 'File Count', 'File Size', 'Avg File Size'],
                ['', 'DUI-table__col--number', 'DUI-table__col--number', 'DUI-table__col--number']
            ))->output($table);


        // Largest Directories within /wp-content/plugins
        $pluginsDirRelative = substr(\WP_CONTENT_DIR . '/plugins', strlen($root));
        $subres = $d->findSubDir($res, $pluginsDirRelative);

        $largestFiles = $d->calculateLargestFilesFoldersFirstLevel(20, $subres);
        $table = [];
        foreach ($largestFiles as $file) {
            $table[] = [$file['name'], number_format_i18n($file['size'])];
        }
        (new Table(
                'Largest Plugin Folders',
                ['Folder', 'Total Size'],
                ['', 'DUI-table__col--number']
            ))->output($table);

        // Largest Directories within /wp-content/themes
        $pluginsDirRelative = substr(\WP_CONTENT_DIR . '/themes', strlen($root));
        $subres = $d->findSubDir($res, $pluginsDirRelative);

        $largestFiles = $d->calculateLargestFilesFoldersFirstLevel(20, $subres);
        $table = [];
        foreach ($largestFiles as $file) {
            $table[] = [$file['name'], number_format_i18n($file['size'])];
        }
        (new Table(
                'Largest Theme Folders',
                ['Folder', 'Total Size'],
                ['', 'DUI-table__col--number']
            ))->output($table);
            
    }

}