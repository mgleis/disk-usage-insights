<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\FileEntry;
use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;
use Mgleis\DiskUsageInsights\Domain\Jobs\PhaseCoordinatorJob;

class ScanDirForSubDirsJob extends BaseJob {

    private int $parentId;
    private string $parentName = '';

    public function __construct(int $parentId, string $parentName = '') {
        $this->parentId = $parentId;
        $this->parentName = $parentName;
    }

    public function work() {

        if ($this->parentId != 0) {
            $parentFileEntry = $this->fileEntryRepository->findById($this->parentId);
        } else {
            // create dummy entry
            $parentFileEntry = new FileEntry();
            $parentFileEntry->parent_id = 0;
        }
        $root = $this->snapshotRepository->load()->root;

        $realDir = realpath($this->fileEntryRepository->calcFullPath($parentFileEntry, $root));

        //$this->log("Scanning " . $realDir . " for sub directories...");

        $files = scandir($realDir) ?? [];
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || !is_dir($realDir . '/' . $file)) {
                continue;
            }
            $dir = $file;

            // persist dir info
            $fileEntry = new FileEntry();
            $fileEntry->parent_id = $parentFileEntry->id;
            $fileEntry->name = $dir;
            $fileEntry->type = FileEntry::TYPE_DIR;
            $fileEntry->size = 0;
            $this->fileEntryRepository->createOrUpdate($fileEntry);

            $this->queue->push((new ScanDirForSubDirsJob($fileEntry->id, $dir))->toArray());
        }
        $this->queue->push((new PhaseCoordinatorJob())->toArray());
    }

    public function toArray() {
        return ['type' => self::class, 'args' => [$this->parentId, $this->parentName]];
    }

    public function toDescription(): string {
        return sprintf('Scanning dir: %s', $this->parentName);
    }

}
