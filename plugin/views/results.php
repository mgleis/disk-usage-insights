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

<!--
<input id="cbIncludeCore" type="checkbox" checked><label for="cbIncludeCore">Include WordPress Core Files</label><br>
<br>
-->

<div class="DUI-panel">
    <div class="DUI-panel__content">
        Root Directory: <?php echo esc_html($root); ?><br>
        Total Size: <?php echo esc_html(number_format_i18n($totalSize)); ?>
        <br>
        <br>
        <div class="stackedbarchart" style="border: 1px solid black; margin-bottom: 5px;">
            <?php foreach ($barChart as $idx => $bar): ?>
                <div class="stackedbarchart__bar stackedbarchart__color--<?php echo ($idx % 10); ?>" style="width: <?php echo esc_html($bar['percent']) ?>%; height: 20px;">
                    <?php if ($bar['percent'] > 10): ?>
                        <?php echo esc_html($bar['label']) ?>
                    <?php endif; ?>
                </div>
            <?php endforeach ?>
        </div>
        <div class="stackedbarchart__legend">
            <?php foreach ($barChart as $idx => $bar): ?>
                <div>
                    <div class="stackedbarchart__legendcolor stackedbarchart__color--<?php echo ($idx % 10); ?>"></div>
                    <!-- <?php echo esc_html($bar['percent']) ?>% -->
                    <?php echo esc_html($bar['mb']) ?> MB
                    <?php echo esc_html($bar['label']) ?>
                </div>
            <?php endforeach ?>
        </div>
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