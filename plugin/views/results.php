<?php
if (!defined('ABSPATH')) {  // Ensure running within WordPress
    exit;
}
?>
<?php include_once __DIR__ . '/blocks/header.php'; ?>


<h1>Snapshot <?php echo esc_html($snapshot); ?></h1>
<div>
    <a href="?page=disk-usage-insights">Dashboard</a> &gt; Snapshot <?php echo esc_html($snapshot); ?>
</div>
<br>

<div class="DUI-panel">
    <div class="DUI-panel__content">
        Root Directory: <?php echo esc_html($root); ?><br>
        Total Size: <?php echo esc_html(number_format_i18n($totalSize)); ?>
    </div>
</div>

<div hx-trigger="load"
    hx-get="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=dui_results_table&table=largest-files&snapshot=<?php echo esc_js($WP_SNAPSHOT_FILE); ?>"
    hx-vals='{"_ajax_nonce":"<?php echo esc_js($WP_NONCE); ?>"}'
    class="TOP10"
    >

    <div class="DUI-panel DUI-panel--TOP10">
        <div class="DUI-panel__content">
            ...
        </div>
    </div>

</div>

<div hx-trigger="load"
    hx-get="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=dui_results_table&table=largest-folders-files&snapshot=<?php echo esc_js($WP_SNAPSHOT_FILE); ?>"
    hx-vals='{"_ajax_nonce":"<?php echo esc_js($WP_NONCE); ?>"}'
    class="TOP10"
    >

    <div class="DUI-panel DUI-panel--TOP10">
        <div class="DUI-panel__content">
            ...
        </div>
    </div>

</div>

<div hx-trigger="load"
    hx-get="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=dui_results_table&table=largest-folders-sub-folders&snapshot=<?php echo esc_js($WP_SNAPSHOT_FILE); ?>"
    hx-vals='{"_ajax_nonce":"<?php echo esc_js($WP_NONCE); ?>"}'
    class="TOP10"
    >

    <div class="DUI-panel DUI-panel--TOP10">
        <div class="DUI-panel__content">
            ...
        </div>
    </div>

</div>

<div hx-trigger="load"
    hx-get="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=dui_results_table&table=folders-most-files&snapshot=<?php echo esc_js($WP_SNAPSHOT_FILE); ?>"
    hx-vals='{"_ajax_nonce":"<?php echo esc_js($WP_NONCE); ?>"}'
    class="TOP10"
    >

    <div class="DUI-panel DUI-panel--TOP10">
        <div class="DUI-panel__content">
            ...
        </div>
    </div>

</div>

<div hx-trigger="load"
    hx-get="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=dui_results_table&table=largest-plugins&snapshot=<?php echo esc_js($WP_SNAPSHOT_FILE); ?>"
    hx-vals='{"_ajax_nonce":"<?php echo esc_js($WP_NONCE); ?>"}'
    class="TOP10"
    >

    <div class="DUI-panel DUI-panel--TOP10">
        <div class="DUI-panel__content">
            ...
        </div>
    </div>

</div>

<div hx-trigger="load"

    hx-get="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=dui_results_table&table=largest-themes&snapshot=<?php echo esc_js($WP_SNAPSHOT_FILE); ?>"
    hx-vals='{"_ajax_nonce":"<?php echo esc_js($WP_NONCE); ?>"}'
    class="TOP10"
    >

    <div class="DUI-panel DUI-panel--TOP10">
        <div class="DUI-panel__content">
            ...
        </div>
    </div>

</div>