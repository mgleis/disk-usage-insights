<?php
namespace Mgleis\DiskUsageInsights;

use Mgleis\DiskUsageInsights\Frontend\Controller\IndexController;
use Mgleis\DiskUsageInsights\Frontend\Controller\ResultsController;
use Mgleis\DiskUsageInsights\Frontend\Controller\ScanController;
use Mgleis\DiskUsageInsights\Frontend\Controller\ScanStatusController;
use Mgleis\DiskUsageInsights\Frontend\Controller\ScanWorkerController;

class Plugin {

    const NONCE = 'disk_usage_insights';
    /** @var string */
    private $version = '';

    public function __construct(string $version)
    {
        $this->version = $version;
    }

    public function init() {
        // Ensure someone is logged in
        if (!is_user_logged_in()) {
            return;
        }

        // Ensure the user is an administrator
        if (!in_array('administrator', wp_get_current_user()->roles)) {
            return;
        }

        // Add to Tools menue
        add_action('admin_menu', function () {
            add_submenu_page(
                'tools.php',
                'Disk Usage Insights',
                'Disk Usage Insights',
                'administrator',
                'disk-usage-insights',
                [$this, 'index']
            );
        });

        // Add to admin bar
        add_action('admin_bar_menu', [$this, 'admin_bar_item'], 500);

        // Add JavaScript libs + CSS
        add_action('admin_enqueue_scripts', [$this, 'addScripts']);

        // Add AJAX
        add_action('wp_ajax_scan', function() { (new ScanController())->scan(); });
        add_action('wp_ajax_worker', function() { (new ScanWorkerController())->worker(); });
        add_action('wp_ajax_status', function() { (new ScanStatusController())->status(); });
    }

    public function addScripts($hook_suffix) {
        if ($hook_suffix != 'tools_page_disk-usage-insights') {
            return;
        }
        wp_enqueue_script(
            'htmx.min.js',
            plugins_url('/res/js/htmx-1.9.12.min.js', __DIR__),
            [],
            $this->version,
            ['in_footer' => true]
        );
        wp_enqueue_script(
            'htmx-custom-error-handler.js',
            plugins_url('/res/js/htmx-custom-error-handler.js', __DIR__),
            [],
            $this->version,
            ['in_footer' => true]
        );
        wp_enqueue_style('styles',
            plugins_url('/res/css/styles.css', __DIR__),
            [],
            $this->version
        );
    }

    public function admin_bar_item(\WP_Admin_Bar $admin_bar ) {
        $admin_bar->add_menu(array(
            'id'    => 'menu-id',
            'parent' => null,
            'group'  => null,
            'title' => '<img src="' . WP_PLUGIN_URL . '/disk-usage-insights/res/pie.svg" style="margin-top:5px; width:20px" alt="Disk Usage Insights">',
            'href'  => admin_url('tools.php?page=disk-usage-insights'),
            'meta' => [
                'title' => 'Disk Usage Insights'
            ]
        ));
    }

    public function index() {
        $snapshot = $_GET['snapshot'] ?? '';
        if ($snapshot == '') {
            return (new IndexController())->execute();
        } else {
            return (new ResultsController())->execute();
        }
    }

}
