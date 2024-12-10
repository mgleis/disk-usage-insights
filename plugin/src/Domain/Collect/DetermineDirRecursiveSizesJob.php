<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;
use Mgleis\DiskUsageInsights\Domain\Jobs\PhaseCoordinatorJob;

class DetermineDirRecursiveSizesJob extends BaseJob {

    public function work() {
        $this->log(self::class);

        $db = $this->queue->db;
        $stmt = $db->prepare("
            UPDATE fileentries
            SET dir_recursive_size = COALESCE(
                (
                    SELECT SUM(sub.dir_size) FROM fileentries AS sub
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

}