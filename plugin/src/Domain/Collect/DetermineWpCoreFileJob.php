<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;

class DetermineWpCoreFileJob extends BaseJob {

    private int $skip;
    private int $count;

    public function __construct(int $skip, int $count) {
        $this->skip = $skip;
        $this->count = $count;
    }

    public function work() {

        $this->log(self::class);

        $snapshot = $this->snapshotRepository->load();
        if (sizeof($snapshot->wpcorefiles) == 0) {

            // Fetch WP Core files and store them in kv store
            $str = file_get_contents(sprintf('https://api.wordpress.org/core/checksums/1.0/?version=%s&locale=en_US', wp_get_wp_version()));
            $arr = json_decode($str, true, JSON_THROW_ON_ERROR);
            if ($arr['checksums'] === false) {
                throw new \Exception("Could not fetch the checksums of wordpress api.");
            }

            $snapshot->wpcorefiles = array_keys($arr['checksums']);
            $this->snapshotRepository->save($snapshot);
        }

        // TODO: batch it
        $fileEntries = $this->fileEntryRepository->get($this->skip, $this->count);
        foreach ($fileEntries as $fileEntry) {

            if (in_array($fileEntry->name, $snapshot->wpcorefiles)) {
                $fileEntry->is_wp_core_file = 1;
                $this->fileEntryRepository->createOrUpdate($fileEntry);
            }
        }

    }

    public function toArray() {
        return ['type' => self::class, 'args' => [$this->skip, $this->count]];
    }

    public function toDescription(): string {
        return sprintf('Determining WordPress core files... (%s files done)', $this->skip);
    }

}