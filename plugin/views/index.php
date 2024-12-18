<?php
if (!defined('ABSPATH')) {  // Ensure running within WordPress
    exit;
}
?>
<?php include __DIR__ . '/blocks/header.php'; ?>

<div id="DUI-results">

    <div class="DUI-panel">
        <div class="DUI-panel__headline">
            Start a new analysis
        </div>
        <div class="DUI-panel__content">
            <div style="margin: 20px;">
                <form>
                    <input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr($WP_NONCE); ?>">

                    <div>
                        <button class="button button-primary"
                            hx-post="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=dui_scan"
                            hx-target="#DUI-results"

                        >Analyze Now</button>
                        </div>
                    <br>

                    <div>
                        A new snapshot of your file system will be generated and stored for later review.
                    </div>
                    <div>
                        Please be aware that each snapshot requires several MB of disk space, depending on your system.
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="DUI-snapshots" class="DUI-panel"
        hx-post="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=dui_list_snapshots"
        hx-vals='{"_ajax_nonce":"<?php echo esc_attr($WP_NONCE); ?>"}'
        hx-trigger="load">
        <div class="DUI-panel__headline">
            ...
        </div>
    </div>

</div>
