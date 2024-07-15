<?php
/**
 * Plugin Name: Disk Usage Insights
 * Plugin URI: https://github.com/mgleis/disk-usage-insights
 * Description: Find large files and folders in your WordPress installation in no time!
 * Author: Marcel Gleis
 * License: GPLv3
 * Version: 1.0
 */
if (!defined('WPINC')) {   // Ensure running within WordPress
    return;
}

if (!is_admin()) {         // Ensure the admin interface is in use
    return;
}

add_action('plugins_loaded', 'disk_usage_insights_init_plugin');

use Mgleis\DiskUsageInsights\Plugin;

function disk_usage_insights_init_plugin() {
    require_once __DIR__.'/vendor/autoload.php';

    global $WP_DISK_USAGE_INSIGHTS_VERSION;
    $WP_DISK_USAGE_INSIGHTS_VERSION = md5_file(__FILE__);

    $plugin = new Plugin();
    $plugin->init();
}
