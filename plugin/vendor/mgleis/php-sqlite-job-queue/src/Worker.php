<?php
declare(strict_types = 1);

namespace Mgleis\PhpSqliteJobQueue;

class Worker {

    private Queue $queue;
    private $maxTotalRuntimeInSeconds = PHP_INT_MAX;
    private $maxJobCount = PHP_INT_MAX;
    private $sleepTimeOnEmptyQueueInMilliseconds = 0;
    private $sleepTimeBetweenJobsInMilliseconds = 0;

    public function __construct(Queue $queue) {
        $this->queue = $queue;
    }

    public function process(\Closure $callback) {
        $jobsExecuted = 0;
        $startTime = time();

        while (true) {

            $queueSize = $this->queue->size();
            if ($queueSize == 0) {
                usleep($this->sleepTimeOnEmptyQueueInMilliseconds * 1000);
            }

            $elapsedTime = time() - $startTime;
            if ($elapsedTime > $this->maxTotalRuntimeInSeconds) {
                break;
            }

            if ($jobsExecuted >= $this->maxJobCount) {
                break;
            }

            $job = $this->queue->pop();
            if ($job !== null) {
                try {
                    $callback($job);
                } catch (\Throwable $e) {
                    $this->queue->error($job);
                    throw $e;
                }
                $this->queue->done($job);
                $jobsExecuted++;
            }
            usleep($this->sleepTimeBetweenJobsInMilliseconds * 1000);
        }
    }

    public function withSleepTimeOnEmptyQueueInMilliseconds(int $sleepTimeOnEmptyQueueInMilliseconds): self {
        $this->sleepTimeOnEmptyQueueInMilliseconds = $sleepTimeOnEmptyQueueInMilliseconds;
        return $this;
    }
    public function withSleepTimeBetweenJobsInMilliseconds(int $sleepTimeBetweenJobsInMilliseconds): self {
        $this->sleepTimeBetweenJobsInMilliseconds = $sleepTimeBetweenJobsInMilliseconds;
        return $this;
    }
    public function withMaxTotalRuntimeInSeconds(int $maxTotalRuntimeInSeconds): self {
        $this->maxTotalRuntimeInSeconds = $maxTotalRuntimeInSeconds;
        return $this;
    }

    public function withMaxJobCount(int $maxJobCount): self {
        $this->maxJobCount = $maxJobCount;
        return $this;
    }

}
