<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;
use Mgleis\DiskUsageInsights\Domain\Jobs\PhaseCoordinatorJob;

class DetermineDirRecursiveCountJob extends BaseJob {

    public function work() {
        $this->log(self::class);

        $db = $this->queue->db;
        $stmt = $db->prepare("
            WITH RECURSIVE directory_counts AS (
                -- Base case: Start with all directories
                SELECT
                    id AS directory_id,
                    id AS current_id,
                    CASE WHEN type = 'file' THEN 1 ELSE 0 END AS file_count
                FROM fileentries

                UNION ALL

                -- Recursive step: Add files and subdirectories
                SELECT
                    dc.directory_id,          -- The original directory
                    fe.id AS current_id,      -- The current entry (file or directory)
                    CASE WHEN fe.type = 'file' THEN 1 ELSE 0 END AS file_count
                FROM directory_counts dc
                JOIN fileentries fe ON fe.parent_id = dc.current_id
            )
            -- Update the table
            UPDATE fileentries
            SET dir_recursive_count = (
                SELECT SUM(file_count)
                FROM directory_counts
                WHERE directory_counts.directory_id = fileentries.id
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
        return 'Calculating recursive dir counts...';
    }

}
