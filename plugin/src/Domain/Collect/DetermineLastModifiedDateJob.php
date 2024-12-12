<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;
use Mgleis\DiskUsageInsights\Domain\Jobs\PhaseCoordinatorJob;

class DetermineLastModifiedDateJob extends BaseJob {

    private int $skip;
    private int $count;

    public function __construct(int $skip, int $count) {
        $this->skip = $skip;
        $this->count = $count;
    }

    public function work() {
        $this->log(self::class);
        $fileEntries = $this->fileEntryRepository->get($this->skip, $this->count);

        foreach ($fileEntries as $fileEntry) {

            $dir = $fileEntry->parent_id != 0 
                ? $this->fileEntryRepository->findById($fileEntry->parent_id)
                : ''; // TODO ROOT-DIR
            //$absoluteFilename = $dir->name . '/' . $fileEntry->name;
            $absoluteFilename = $fileEntry->name;
            $this->log($absoluteFilename);

            $fileEntry->last_modified_date = filemtime($absoluteFilename);

            $this->fileEntryRepository->createOrUpdate($fileEntry);
        }
    }

    public function toArray() {
        return ['type' => self::class, 'args' => [$this->skip, $this->count]];
    }

    public function toDescription(): string {
        return sprintf('Determining last modified dates... (%s files done)', $this->skip);
    }
}