<?php
if (!defined('ABSPATH')) {  // Ensure running within WordPress
    exit;
}
?>
<div class="DUI-panel">
    <div class="DUI-panel__content">

        <div id="DUI-loading" style="margin:20px;">
            <div style="display:inline-block; vertical-align: top">
                <img width=32 height=32 style="vertical-align:middle; padding-right: 5px"
                    src="<?php echo esc_url($WP_PLUGIN_URL); ?>/res/img/loader.gif"
                    alt="Loading..."
                >
            </div>
            <div style="display:inline-block;">
                <div style="font-weight: bold">Scanning your WordPress system...</div>
                <!-- STATUS -->
                <div hx-post="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=status"
                    hx-vals='{"_ajax_nonce":"<?php echo esc_attr($WP_NONCE); ?>", "snapshot":"<?php echo $WP_SNAPSHOT_FILE; ?>"}'
                    hx-trigger="every 500ms">
                    ...
                </div>
            </div>

            <br>
            <br>
            <div>
                Scanning can take some time. You will be redirected automatically to the analysis results.
            </div>
        </div>

        <!-- WORKER -->
        <div style="display:none" hx-post="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=worker"
            hx-vals='{"_ajax_nonce":"<?php echo esc_attr($WP_NONCE); ?>", "snapshot":"<?php echo $WP_SNAPSHOT_FILE; ?>"}'
            hx-trigger="load,every 5s">
            WORKER
        </div>

    </div>
</div>