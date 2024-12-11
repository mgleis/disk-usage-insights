<?php
namespace Mgleis\DiskUsageInsights;

use Mgleis\DiskUsageInsights\Domain\Collect\ScanDirForSubDirsJob;
use Mgleis\DiskUsageInsights\Domain\FileEntryRepository;
use Mgleis\DiskUsageInsights\Domain\SnapshotRepository;
use Mgleis\DiskUsageInsights\Frontend\ScanResults;
use Mgleis\PhpSqliteJobQueue\Job;
use Mgleis\PhpSqliteJobQueue\Queue;
use Mgleis\PhpSqliteJobQueue\Worker;

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
        add_action('wp_ajax_scan', [$this, 'scan']);
        add_action('wp_ajax_worker', [$this, 'worker']);
        add_action('wp_ajax_status', [$this, 'status']);
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
        $WP_PLUGIN_URL = plugin_dir_url(__DIR__);
        $WP_NONCE = wp_create_nonce(self::NONCE);
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        include __DIR__ . '/../views/index.php';
    }
/*

Start Scan:

- create new database
- initialize database
- push first job to the queue
- return html
    - create a worker ajax call every 10 seconds
    - create a status ajax call every 1 second

*/

    public function scan() {
        check_ajax_referer(self::NONCE);
/*
        // OLD v1.1 code:
        $scanResults = new ScanResults();
        $scanResults->execute();

        sleep(1);
*/

        // Create a new Snapshot Database
        $snapshot = date('Ymd_His_') . rand(10000, 99999);

        $q = new Queue($snapshot . '.db');
        $q->push((new ScanDirForSubDirsJob(__DIR__.'/../../'))->toArray());

        // NEW Code
        $WP_NONCE = wp_create_nonce(self::NONCE);
        $WP_ADMIN_AJAX_URL = admin_url('admin-ajax.php');
        $WP_SNAPSHOT_FILE = $snapshot;

        include __DIR__ . '/../views/scan.php';

        wp_die(); // All ajax handlers should die when finished
    }

    public function worker() {
        check_ajax_referer(self::NONCE);

        // TODO validate value: ensure file exists in data directory
        $snapshot = $_POST['snapshot'];

        $q = new Queue($snapshot . '.db');
        $fileEntryRepository = new FileEntryRepository($q->db);
        $snapshotRepository = new SnapshotRepository($q->db);
        $w = (new Worker($q))
            ->withMaxTotalRuntimeInSeconds(5)
            ->withSleepTimeBetweenJobsInMilliseconds(150) // TODO REMOVE
        ;
        $w->process(function(Job $job) use ($q, $fileEntryRepository, $snapshotRepository) {
            $reflect = new \ReflectionClass($job->payload['type']);
            $instance = $reflect->newInstanceArgs($job->payload['args']);
            $instance->setQueue($q);
            $instance->setFileEntryRepository($fileEntryRepository);
            $instance->setSnapshotRepository($snapshotRepository);
            $instance->work();
        });

        // if process finished = stop reloading
        if ($q->size() === 0) {
            http_response_code(286);
            echo "DONE";
            exit;
        }

        wp_die(); // All ajax handlers should die when finished
    }

    public function status() {
        check_ajax_referer(self::NONCE);

        // TODO validate value: ensure file exists in data directory
        $snapshot = $_POST['snapshot'];
        $q = new Queue($snapshot . '.db');

        // if process finished = stop reloading
        if ($q->size() === 0) {
            http_response_code(286);
            echo "DONE";
            exit;
        }

        $job = $q->top();
        if ($job !== null) {

            $reflect = new \ReflectionClass($job->payload['type']);
            $instance = $reflect->newInstanceArgs($job->payload['args']);
            echo $instance->toDescription();
        }

        wp_die(); // All ajax handlers should die when finished
    }

}
