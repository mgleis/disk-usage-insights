<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;
use Mgleis\DiskUsageInsights\Domain\Jobs\PhaseCoordinatorJob;

class DetermineDirRecursiveSizesJob extends BaseJob {

    public function work() {
        $this->log(self::class);

        $db = $this->queue->db;
        $stmt = $db->prepare("
            WITH RECURSIVE directory_sizes AS (
                -- Base case: Start with all directories
                SELECT
                    id AS directory_id,
                    id AS current_id,
                    COALESCE(size, 0) AS size
                FROM fileentries
                WHERE type = 'dir'

                UNION ALL

                -- Recursive step: Add files and subdirectories
                SELECT
                    ds.directory_id,
                    fe.id AS current_id,
                    COALESCE(fe.size, 0)
                FROM directory_sizes ds
                JOIN fileentries fe ON fe.parent_id = ds.current_id
            )
            -- Update the table
            UPDATE fileentries
            SET dir_recursive_size = (
                SELECT SUM(size)
                FROM directory_sizes
                WHERE directory_sizes.directory_id = fileentries.id
            )
            WHERE type = 'dir';
        ");
        $stmt->execute();

        if ($this->queue->size() == 0) {
            $this->queue->push((new PhaseCoordinatorJob())->toArray());
        }
    }

    public function toArray() {
        return ['type' => self::class, 'args' => []];
    }

    public function toDescription(): string {
        return 'Calculating recursive dir sizes...';
    }

}
