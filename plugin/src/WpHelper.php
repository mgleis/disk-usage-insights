<?php
namespace Mgleis\DiskUsageInsights;

class WpHelper {

    public static function getPluginUrl(): string {
        return plugins_url('', realpath(__DIR__ . ''));
    }

}