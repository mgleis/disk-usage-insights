<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Plugin;
use Mgleis\DiskUsageInsights\WpHelper;

class IndexController {

    public function execute() {
        $WP_PLUGIN_URL = WpHelper::getPluginUrl();
        $WP_NONCE = wp_create_nonce(Plugin::NONCE);
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');

        include_once __DIR__ . '/../../../views/index.php';
    }

}
