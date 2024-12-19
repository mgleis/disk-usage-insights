<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\FileEntry;
use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;

class ScanDirForFilesJob extends BaseJob {

    private int $skip;
    private int $count;
    private int $totalCount;

    public function __construct(int $skip, int $count, int $totalCount) {
        $this->skip = $skip;
        $this->count = $count;
        $this->totalCount = $totalCount;
    }

    public function work() {

        $dirEntries = $this->fileEntryRepository->get($this->skip, $this->count, FileEntry::TYPE_DIR);
        $root = $this->snapshotRepository->load()->root;
        foreach ($dirEntries as $dirEntry) {

            $realDir = $this->fileEntryRepository->calcFullPath($dirEntry, $root);
            $pattern = $realDir . '/*';
            $this->log("Scanning " . $realDir . " for files...");

            $entries = glob($pattern);
            $files = array_values(array_filter($entries, 'is_file'));

            foreach ($files as $file) {
                // persist file info
                $fileEntry = new FileEntry();
                $fileEntry->parent_id = $dirEntry->id;
                $fileEntry->name = basename($file);
                $fileEntry->type = FileEntry::TYPE_FILE;
                $fileEntry->size = 0;
                $this->fileEntryRepository->createOrUpdate($fileEntry);
            }
        }
    }

    public function toArray() {
        return ['type' => self::class, 'args' => [$this->skip, $this->count, $this->totalCount]];
    }

    public function toDescription(): string {
        $percent = $this->totalCount > 0
            ? round(100 * $this->skip / $this->totalCount)
            : '0';
        return sprintf('Scanning dirs for files... %s%%', $percent);
    }

}
