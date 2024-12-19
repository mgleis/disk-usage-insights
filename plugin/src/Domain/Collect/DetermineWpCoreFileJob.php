<?php

namespace Mgleis\DiskUsageInsights\Domain\Collect;

use Mgleis\DiskUsageInsights\Domain\Jobs\BaseJob;

class DetermineWpCoreFileJob extends BaseJob {

    private int $skip;
    private int $count;
    private int $totalCount;

    public function __construct(int $skip, int $count, int $totalCount) {
        $this->skip = $skip;
        $this->count = $count;
        $this->totalCount = $totalCount;
    }

    public function work() {

        $this->log(self::class);

        $snapshot = $this->snapshotRepository->load();
        if (sizeof($snapshot->wpcorefiles) == 0) {

            // Fetch WP Core files and store them in kv store
            $url = sprintf('https://api.wordpress.org/core/checksums/1.0/?version=%s&locale=en_US', wp_get_wp_version());
            $this->log("Fetching from url: " . $url);
            $str = wp_remote_get($url)['body'];
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

            $relativeFilename = $this->fileEntryRepository->calcFullPath($fileEntry);
            if (in_array($relativeFilename, $snapshot->wpcorefiles)) {
                $fileEntry->is_wp_core_file = 1;
                $this->fileEntryRepository->createOrUpdate($fileEntry);
            }
        }

    }

    public function toArray() {
        return ['type' => self::class, 'args' => [$this->skip, $this->count, $this->totalCount]];
    }

    public function toDescription(): string {
        return sprintf('Determining WordPress core files... %s%%', round(100 * $this->skip / $this->totalCount));
    }

}
