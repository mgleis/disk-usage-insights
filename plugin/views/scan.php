
<h1>SCANNING...</h1>

<!-- STATUS -->
Status:
<div hx-post="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=status"
    hx-vals='{"_ajax_nonce":"<?php echo esc_attr($WP_NONCE); ?>", "snapshot":"<?php echo $WP_SNAPSHOT_FILE; ?>"}'
    hx-trigger="every 1s">
    ...
</div>

<!-- WORKER -->
 <pre>
<div hx-post="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=worker"
    hx-vals='{"_ajax_nonce":"<?php echo esc_attr($WP_NONCE); ?>", "snapshot":"<?php echo $WP_SNAPSHOT_FILE; ?>"}'
    hx-trigger="load,every 5s">
    WORKER
</div>
</pre>

