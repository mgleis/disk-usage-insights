<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;

class DetermineLastModifiedDateJob extends BaseJob {

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
        $fileEntries = $this->fileEntryRepository->get($this->skip, $this->count);

        $root = $this->snapshotRepository->load()->root;
        foreach ($fileEntries as $fileEntry) {

            $absoluteFilename = $this->fileEntryRepository->calcFullPath($fileEntry, $root);

            $fileEntry->last_modified_date = filemtime($absoluteFilename);
            $this->fileEntryRepository->createOrUpdate($fileEntry);
        }
    }

    public function toArray() {
        return ['type' => self::class, 'args' => [$this->skip, $this->count, $this->totalCount]];
    }

    public function toDescription(): string {
        return sprintf('Determining last modified dates... %s%%', round(100 * $this->skip / $this->totalCount));
    }
}
