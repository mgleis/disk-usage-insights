<?php

namespace Mgleis\DiskUsageInsights\Domain\Jobs;

use Mgleis\DiskUsageInsights\Domain\Collect\DetermineDirCountJob;
use Mgleis\DiskUsageInsights\Domain\Collect\DetermineDirRecursiveCountJob;
use Mgleis\DiskUsageInsights\Domain\Collect\DetermineDirRecursiveSizesJob;
use Mgleis\DiskUsageInsights\Domain\Collect\DetermineDirSizesJob;
use Mgleis\DiskUsageInsights\Domain\Collect\DetermineFileSizesJob;
use Mgleis\DiskUsageInsights\Domain\Collect\DetermineLastModifiedDateJob;
use Mgleis\DiskUsageInsights\Domain\Collect\DetermineWpCoreFileJob;
use Mgleis\DiskUsageInsights\Domain\FileEntry;
use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;
use Mgleis\DiskUsageInsights\Domain\Collect\ScanDirForFilesJob;
use Mgleis\DiskUsageInsights\Domain\Snapshot;

class PhaseCoordinatorJob extends BaseJob {

    const CHUNK_SIZE = 50;
    public function work() {

        $snapshot = $this->snapshotRepository->load();
        $phase = $snapshot->phase;

        if ($this->queue->size() == 0) {
            if ($phase == 0) {
                $this->log("New Phase: Scan for Files");
                $this->increasePhase($snapshot);
                $this->chunk($this->fileEntryRepository->count(FileEntry::TYPE_DIR), self::CHUNK_SIZE, function(int $skip, int $count) {
                    $this->queue->push((new ScanDirForFilesJob($skip, $count))->toArray());
                });
                $this->queue->push((new PhaseCoordinatorJob())->toArray());
            } else if ($phase == 1) {
                $this->log("New Phase: Start Analysis / Determine File Sizes");
                $this->increasePhase($snapshot);
                $this->chunk($this->fileEntryRepository->count(FileEntry::TYPE_FILE), self::CHUNK_SIZE, function(int $skip, int $count) {
                    $this->queue->push((new DetermineFileSizesJob($skip, $count))->toArray());
                });
                $this->queue->push((new PhaseCoordinatorJob())->toArray());
            } else if ($phase == 2) {
                $this->log("New Phase: Determine Dir Sizes");
                $this->increasePhase($snapshot);
                $this->queue->push((new DetermineDirSizesJob())->toArray());
            } else if ($phase == 3) {
                $this->log("New Phase: Determine Dir Recursive Sizes");
                $this->increasePhase($snapshot);
                $this->queue->push((new DetermineDirRecursiveSizesJob())->toArray());
            } else if ($phase == 4) {
                $this->log("New Phase: Determine Dir Counts");
                $this->increasePhase($snapshot);
                $this->queue->push((new DetermineDirCountJob())->toArray());
            } else if ($phase == 5) {
                $this->log("New Phase: Determine Dir Recursive Counts");
                $this->increasePhase($snapshot);
                $this->queue->push((new DetermineDirRecursiveCountJob())->toArray());
            } else if ($phase == 6) {
                $this->log("New Phase: Determine Last Modified Date");
                $this->increasePhase($snapshot);
                $this->chunk($this->fileEntryRepository->count(), self::CHUNK_SIZE, function(int $skip, int $count) {
                    $this->queue->push((new DetermineLastModifiedDateJob($skip, $count))->toArray());
                });
                $this->queue->push((new PhaseCoordinatorJob())->toArray());
            } else if ($phase == 7) {
                $this->log("New Phase: Determine WP Core Files");
                $this->increasePhase($snapshot);
                $this->chunk($this->fileEntryRepository->count(), self::CHUNK_SIZE, function(int $skip, int $count) {
                    $this->queue->push((new DetermineWpCoreFileJob($skip, $count))->toArray());
                });
                $this->queue->push((new PhaseCoordinatorJob())->toArray());
            } else if ($phase == 8) {
                $this->log("DONE");
                $this->increasePhase($snapshot);
                $snapshot->collectPhaseFinished = 1;
                $this->snapshotRepository->save($snapshot);
            }
        }
    }

    private function chunk(int $count, int $itemsPerChunk, Callable $callable) {
        $pages = 1 + round($count / $itemsPerChunk);
        for ($i = 0; $i < $pages; $i++) {
            $callable($i * $itemsPerChunk, $itemsPerChunk);
        }
    }

    private function increasePhase(Snapshot $snapshot) {
        $snapshot->phase++;
        $this->snapshotRepository->save($snapshot);
    }

    public function toArray() {
        return ['type' => self::class, 'args' => []];
    }

    public function toDescription(): string {
        return 'Preparing next tasks...';
    }

}
