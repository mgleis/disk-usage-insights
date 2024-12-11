<?php
/**
 * Plugin Name: Disk Usage Insights
 * Plugin URI: https://github.com/mgleis/disk-usage-insights
 * Description: Find large files and folders in your WordPress installation in no time!
 * Author: Marcel Gleis
 * License: GPLv3
 * Version: 1.2
 */

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

use Mgleis\DiskUsageInsights\Plugin;

function mgleis_diskusageinsights_init_plugin() {
    require_once __DIR__.'/vendor/autoload.php';

    // TODO: This is not good... what to do???
    $version = md5_file(__FILE__);

    $plugin = new Plugin($version);
    $plugin->init();
}
