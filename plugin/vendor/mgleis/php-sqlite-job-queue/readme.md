# PHP SQLite Job Queue

A minimal job queue library written in PHP that persists data with SQLite.

Use it for small projects / prototypes with at most hundreds or thousands of jobs.

## Install

    composer require mgleis/php-sqlite-job-queue

## Usage

### The Main Use Cases

#### Add a new job

    $queue = new Queue('queue.sqlite');
    $queue->push("a string");
    $queue->push(["an" => "array"]);
    $queue->push([0, 1, 2, 3, 4]);
    $queue->push(['type' => 'SendEmail', 'to' => '...']);

#### Process jobs with workers

    $worker = new Worker($queue);
    $worker->process(function(Job $job) {
        $payload = $job->payload;
        if ($payload['type'] == 'SendEmail')
            send_email(...);
    });

#### Worker Configuration

You have several options to configure the worker. The options should be self-explanatory:

    // optional configuration
    $worker
        ->withMaxTotalRuntimeInSeconds(60)
        ->withMaxJobCount(100)
        ->withSleepTimeBetweenJobsInMilliseconds(1)
        ->withSleepTimeOnEmptyQueueInMilliseconds(100);

### Edge Cases

### Determine Size of the queue

    $size = $queue->size();

### Get a specific job

    $job = $queue->get(12345);

### Handling Errors
TBD

### Handling Timeouts (reserved jobs)
TBD

### Handling database locks
TBD

