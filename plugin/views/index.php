<?php
if (!defined('ABSPATH')) {  // Ensure running within WordPress
    exit;
}
?>
<br>

<div class="DUI-panel" style="float:left;min-width:450px; width:69%; max-width:70%">
<div class="DUI-panel__content--title">

    <div>
        <div style="float:left">
            <img src="<?php echo esc_url($WP_PLUGIN_URL); ?>/res/pie.svg"
                 style="height:90px; vertical-align: middle; padding-right: 20px;"
                 alt="Disk Usage Insights Logo"
            >
        </div>
        <div style="float:left">
            <h1>Disk Usage Insights</h1>
            <p>Find large files in no time!</p>
        </div>
    </div>
    <div style="clear:both"></div>

    <div style="margin-top: 20px; margin-bottom: 20px;">
        <form>
            <input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr($WP_NONCE); ?>">
            <input type="hidden" name="action" value="scan">

            <button class="button button-primary"
                hx-post="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=scan"
                hx-indicator="#DUI-loading"
                hx-target="#DUI-results"

            >Analyze Now</button>
        </form>
    </div>

</div>
</div>

<div class="show-desktop" style="width:29%; float:left; overflow: hidden">

    <div class="DUI-panel">
        <div class="DUI-panel__headline dashicons-before dashicons-heart">
            Support Open Source!
        </div>
        <div class="DUI-panel__content">
            - <a target="_blank" href="https://wordpress.org/support/plugin/disk-usage-insights/reviews/#new-post">Add a 5 star WordPress review</a>
            <br>
            - <a target="_blank" href="https://github.com/mgleis/disk-usage-insights">Donate a github star</a>
        </div>
    </div>
    <div class="DUI-panel">
        <div class="DUI-panel__headline dashicons-before dashicons-groups">
            Do you need help?
        </div>
        <div class="DUI-panel__content">
            - <a href="https://wordpress.org/support/plugin/disk-usage-insights/" target="_blank">Plugin Support Forum</a>
        </div>
    </div>

</div>
<div style="clear:both"></div>

<div id="DUI-loading" class="htmx-indicator" style="position: absolute; top: 100px; left:50%; -webkit-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
    <div  class="DUI-panel">
        <div class="DUI-panel__content--title">
            <img width=32 style="vertical-align:middle; padding-right: 5px"
                 src="<?php echo esc_url($WP_PLUGIN_URL); ?>/res/img/loader.gif"
                 alt="Loading..."
            >
            <span>Scanning your WordPress system...</span>
        </div>
    </div>
</div>

<div id="DUI-results">
</div>
