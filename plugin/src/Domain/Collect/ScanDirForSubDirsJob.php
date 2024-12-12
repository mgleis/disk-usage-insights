<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\FileEntry;
use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;
use Mgleis\DiskUsageInsights\Domain\Jobs\PhaseCoordinatorJob;

class ScanDirForSubDirsJob extends BaseJob {

    private string $parentDir;

    public function __construct(string $parentDir) {
        $this->parentDir = $parentDir;
    }

    public function work() {
        $realDir = realpath($this->parentDir);
        $pattern = $realDir . '/*';
        $this->log("Scanning " . $realDir . " for sub directories...");

        // fetch parent
        $parent = $this->fileEntryRepository->findDirByName($this->parentDir);
        $parentId = $parent === null ? 0 : $parent->id;

        foreach (glob($pattern, GLOB_ONLYDIR) as $dir) {
            $this->queue->push((new ScanDirForSubDirsJob($dir))->toArray());

            // persist dir info
            $fileEntry = new FileEntry();
            $fileEntry->parent_id = $parentId;
            $fileEntry->name = $dir;
            $fileEntry->type = FileEntry::TYPE_DIR;
            $fileEntry->size = 0;
            $this->fileEntryRepository->createOrUpdate($fileEntry);
        }
        $this->queue->push((new PhaseCoordinatorJob())->toArray());
    }

    public function toArray() {
        return ['type' => self::class, 'args' => [$this->parentDir]];
    }

    public function toDescription(): string {
        return sprintf('Scanning dir: %s', $this->parentDir);
    }

}
