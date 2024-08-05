<?php

namespace Mgleis\DiskUsageInsights\Domain;

class DiskUsage {

    private /** @var string */ $rootDirectory;

    public function __construct(string $rootDirectory) {
        $this->rootDirectory = $rootDirectory;
    }

    public function scan(string $directory = null, bool $first = true): array {

        if ($directory == null) {
            $directory = $this->rootDirectory;
        }

        $retval = [];

        $files = scandir($directory);
        if ($files !== false) {

            foreach ($files as $file) {
                if ($file == '.') {
                    continue;
                }
                if ($file == '..') {
                    continue;
                }

                $absName = $directory . '/' . $file;
                $isDir = is_dir($absName);
                $size = ($isDir ? 0 : filesize($absName));

                $entry = [
                    'name' => $file,
                    'abs' => substr($absName, strlen($this->rootDirectory)),
                    'file' => !$isDir,
                    'size' => $size
                ];

                if ($isDir) {
                    $entry['entries'] =$this->scan($absName, false);
                }
                $retval[] = $entry;

            }
        }

        if ($first) {
            $root = [
                'name' => basename($directory),
                'abs' => substr($directory, strlen($this->rootDirectory)),
                'file' => false,
                'size' => 0
            ];
            $root['entries'] = $retval;
            $retval = [$root];
        }

        return $retval;
    }

    public function calculateDirTotalSizes(array &$arr): int {
        $totalSize = 0;
        foreach ($arr as &$entry) {
            if (!$entry['file']) {
                $subdirSize = $this->calculateDirTotalSizes($entry['entries']);
                $entry['totalSize'] = $subdirSize;
                $totalSize += $subdirSize;
            } else {
                $totalSize += $entry['size'];
            }
        }

        return $totalSize;
    }

    public function calculateDirFileSizes(array &$arr, array &$parentFolder = null) {
        $fileSizes = 0;
        foreach ($arr as &$entry) {
            if (!$entry['file']) {
                $this->calculateDirFileSizes($entry['entries'], $entry);
            } else {
                $fileSizes += $entry['size'];
            }
        }
        if ($parentFolder !== null) {
            $parentFolder['fileSizes'] = $fileSizes;
        }
    }

    public function calculateDirFileCount(array &$arr, array &$parentFolder = null) {
        $fileCount = 0;
        foreach ($arr as &$entry) {
            if (!$entry['file']) {
                $this->calculateDirFileCount($entry['entries'], $entry);
            } else {
                $fileCount++;
            }
        }
        if ($parentFolder !== null) {
            $parentFolder['fileCount'] = $fileCount;
        }
    }

    public function calculateLargestFiles(int $n, array $arr) : array {
        $files = $this->flatten($arr);

        uasort($files, function($a, $b) {
            return $b['size'] <=> $a['size'];
        });

        return array_slice($files, 0, $n);
    }

    public function flatten(array $arr): array {
        $flattened = [];
        foreach ($arr as $entry) {
            if ($entry['file']) {
                $flattened[] = $entry;
            } else {
                $entries = $entry['entries'];
                unset($entry['entries']);
                $flattened[] = $entry;

                $flattenedEntries = $this->flatten($entries);
                foreach ($flattenedEntries as $flattenedEntry) {
                    $flattened[] = $flattenedEntry;
                }

            }
        }

        return $flattened;
    }

    public function calculateLargestFoldersRecursive(int $n, array $arr) : array {
        $files = $this->flatten($arr);
        $files = array_filter($files, function($entry) {
            return !$entry['file'];
        });

        uasort($files, function($a, $b) {
            return $b['totalSize'] <=> $a['totalSize'];
        });

        return array_slice($files, 0, $n);
    }

    public function calculateLargestFolders(int $n, array $arr) : array {
        $files = $this->flatten($arr);
        $files = array_filter($files, function($entry) {
            return !$entry['file'];
        });

        uasort($files, function($a, $b) {
            return $b['fileSizes'] <=> $a['fileSizes'];
        });

        return array_slice($files, 0, $n);
    }

    public function calculateFoldersWithMostFiles(int $n, array $arr) : array {
        $files = $this->flatten($arr);
        $files = array_filter($files, function($entry) {
            return !$entry['file'];
        });

        uasort($files, function($a, $b) {
            return $b['fileCount'] <=> $a['fileCount'];
        });

        return array_slice($files, 0, $n);
    }

    public function calculateLargestFilesFoldersFirstLevel(int $n, array $arr) : array {
        $ret = [];
        foreach ($arr[0]['entries'] as $entry) {
            $ret[] = [
                'name' => $entry['name'],
                'size' => $entry['totalSize'] ?? $entry['size']
            ];
        }

        uasort($ret, function($a, $b) {
            return $b['size'] <=> $a['size'];
        });

        return array_slice($ret, 0, $n);
    }

    public function findSubDir(array $arr, $subdir) {
        // TODO parse $subdir with PHP function to get each folder
        $folders = explode('/', trim($subdir, '/'));
        $ret = $arr[0]['entries'];
        for ($i = 0; $i < sizeof($folders); $i++) {
            $folder = $folders[$i];
            // TODO sanity checks
            foreach ($ret as $entry) {
                if ($entry['name'] == $folder) {
                    if ($i != sizeof($folders)-1) {
                        $ret = $entry['entries'];
                    } else {
                        return [$entry];
                    }
                }
            }
        }

        return $ret;
    }

    public function doMore() {
        // TODO: Remove "Largest Folders (incl. sub folders)"? Does not make sense
        // TODO: Are "file links" a problem? Loop?
        // Change Hot Spots: Folders which have most recent changes
        //   Oldest Files ? Für was?
        //   Newest Files: Recently Created, Recently Modified, ...
        //
        // Grouped by (Mime) Type: Images, Videos, Documents, ...
        // Grouped by Plugin / Theme
    }

}
