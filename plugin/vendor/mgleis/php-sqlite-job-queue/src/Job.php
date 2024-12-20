<?php
declare(strict_types = 1);

namespace Mgleis\PhpSqliteJobQueue;

class Job {
    public int $id;
    public $payload;

    public function __construct(int $id, $payload) {
        $this->id = $id;
        $this->payload = $payload;
    }
}
