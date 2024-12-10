<?php

namespace Mgleis\DiskUsageInsights\Domain;

class Snapshot {

    const CURRENT_VERSION = "1";

    public string $version = self::CURRENT_VERSION;
    public string $root = '';
    public int $phase = 0;
    public array $wpcorefiles = [];

}