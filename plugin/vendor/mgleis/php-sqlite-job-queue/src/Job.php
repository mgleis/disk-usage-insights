<?php
declare(strict_types = 1);

namespace Mgleis\PhpSqliteJobQueue;

class Job {
    public int $id;
    public mixed $payload;

    public function __construct(int $id, mixed $payload) {
        $this->id = $id;
        $this->payload = $payload;
    }
}
