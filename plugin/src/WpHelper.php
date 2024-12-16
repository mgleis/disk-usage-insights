<?php
namespace Mgleis\DiskUsageInsights;

use Mgleis\DiskUsageInsights\Frontend\Controller\IndexController;
use Mgleis\DiskUsageInsights\Frontend\Controller\ResultsController;
use Mgleis\DiskUsageInsights\Frontend\Controller\ScanController;
use Mgleis\DiskUsageInsights\Frontend\Controller\ScanStatusController;
use Mgleis\DiskUsageInsights\Frontend\Controller\ScanWorkerController;

class WpHelper {

    public static function getPluginUrl(): string {
        return plugins_url('', realpath(__DIR__ . ''));
    }

}