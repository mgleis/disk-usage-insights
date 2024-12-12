<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\FileEntry;
use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;

class ScanDirForFilesJob extends BaseJob {

    private int $skip;
    private int $count;

    public function __construct(int $skip, int $count) {
        $this->skip = $skip;
        $this->count = $count;
    }

    public function work() {

        $dirEntries = $this->fileEntryRepository->get($this->skip, $this->count, FileEntry::TYPE_DIR);
        foreach ($dirEntries as $dirEntry) {

            $realDir = realpath($dirEntry->name);
            $pattern = $realDir . '/*';
            $this->log("Scanning " . $realDir . " for files...");

            $entries = glob($pattern);
            $files = array_values(array_filter($entries, 'is_file'));

            foreach ($files as $file) {
                // persist file info
                $fileEntry = new FileEntry();
                $fileEntry->parent_id = $dirEntry->id;
                $fileEntry->name = $file;
                $fileEntry->type = FileEntry::TYPE_FILE;
                $fileEntry->size = 0;
                $this->fileEntryRepository->createOrUpdate($fileEntry);
            }
        }
    }

    public function toArray() {
        return ['type' => self::class, 'args' => [$this->skip, $this->count]];
    }

    public function toDescription(): string {
        return sprintf('Scanning dirs for files... (%s files done)', $this->skip);
    }

}
