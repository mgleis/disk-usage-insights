<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;
use Mgleis\DiskUsageInsights\Domain\Jobs\PhaseCoordinatorJob;

class OptimizeDatabaseJob extends BaseJob {

    public function work() {
        $this->log(self::class);

        $db = $this->queue->db;
        $stmt = $db->prepare("VACUUM;");
        $stmt->execute();
    }

    public function toArray() {
        return ['type' => self::class, 'args' => []];
    }

    public function toDescription(): string {
        return 'Optimizing database...';
    }

}
