<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\FileEntry;
use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;

class DetermineFileSizesJob extends BaseJob {

    private int $skip;
    private int $count;
    private int $totalCount;

    public function __construct(int $skip, int $count, int $totalCount) {
        $this->skip = $skip;
        $this->count = $count;
        $this->totalCount = $totalCount;
    }

    public function work() {
        $this->log(self::class);
        $fileEntries = $this->fileEntryRepository->get($this->skip, $this->count, FileEntry::TYPE_FILE);

        foreach ($fileEntries as $fileEntry) {

            $dir = $fileEntry->parent_id != 0 
                ? $this->fileEntryRepository->findById($fileEntry->parent_id)
                : '';
            $absoluteFilename = $dir->name . '/' . $fileEntry->name;
            $this->log($absoluteFilename);

            $fileEntry->size = filesize($absoluteFilename);

            $this->fileEntryRepository->createOrUpdate($fileEntry);
        }
    }

    public function toArray() {
        return ['type' => self::class, 'args' => [$this->skip, $this->count, $this->totalCount]];
    }

    public function toDescription(): string {
        return sprintf('Determining file sizes... %s%%', round(100 * $this->skip / $this->totalCount));
    }

}
