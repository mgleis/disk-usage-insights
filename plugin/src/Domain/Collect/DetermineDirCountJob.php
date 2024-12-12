<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;
use Mgleis\DiskUsageInsights\Domain\Jobs\PhaseCoordinatorJob;

class DetermineDirCountJob extends BaseJob {

    public function work() {
        $this->log(self::class);

        $db = $this->queue->db;
        $stmt = $db->prepare("
            UPDATE fileentries
            SET dir_count = COALESCE(
                (
                    SELECT COUNT(*)
                    FROM fileentries AS sub
                    WHERE sub.parent_id = fileentries.id
                ), 0)
            WHERE type = 'dir';
        ");
        $stmt->execute();

        $this->queue->push((new PhaseCoordinatorJob())->toArray());
    }

    public function toArray() {
        return ['type' => self::class, 'args' => []];
    }

    public function toDescription(): string {
        return 'Calculating dir counts...';
    }

}
