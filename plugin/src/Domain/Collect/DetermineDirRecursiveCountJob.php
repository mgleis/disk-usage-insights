<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;
use Mgleis\DiskUsageInsights\Domain\Jobs\PhaseCoordinatorJob;

class DetermineDirRecursiveCountJob extends BaseJob {

    public function work() {
        $this->log(self::class);

        $db = $this->queue->db;
        $stmt = $db->prepare("
            UPDATE fileentries
            SET dir_recursive_count = COALESCE(
                (
                    SELECT SUM(sub.dir_count) FROM fileentries AS sub
                    WHERE sub.type = 'dir'
                        AND sub.name LIKE fileentries.name || '%'
                ), 0)
            WHERE type = 'dir'
                AND parent_id = 0;
        ");
        $stmt->execute();

        $this->queue->push((new PhaseCoordinatorJob())->toArray());
    }

    public function toArray() {
        return ['type' => self::class, 'args' => []];
    }

    public function toDescription(): string {
        return 'Calculating recursive dir counts...';
    }

}
