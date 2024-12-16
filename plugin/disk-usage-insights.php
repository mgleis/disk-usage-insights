<?php
/**
 * Plugin Name: Disk Usage Insights
 * Plugin URI: https://github.com/mgleis/disk-usage-insights
 * Description: Find large files and folders in your WordPress installation in no time!
 * Author: Marcel Gleis
 * License: GPLv3
 * Version: 1.2
 */
const DISK_USAGE_INSIGHTS_VERSION = '1.2';

 // Ensure running within WordPress
if (!defined('ABSPATH')) {
    exit;
}

// Ensure the admin interface is in use
if (!is_admin()) {
    return;
}

// If site is a multisite: Ensure the logged in user has SuperAdmin privileges
if (is_multisite() && !current_user_can('manage_sites')) {
    return;
}

add_action('plugins_loaded', 'mgleis_diskusageinsights_init_plugin');

function mgleis_diskusageinsights_init_plugin() {
    require_once __DIR__.'/vendor/autoload.php';
    $plugin = new Mgleis\DiskUsageInsights\Plugin(DISK_USAGE_INSIGHTS_VERSION);
    $plugin->init();
}
