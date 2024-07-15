<br>

<div class="DUI-panel">
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
                hx-post="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>"
                hx-indicator="#DUI-loading"
                hx-target="#DUI-results"

            >Analyze Now</button>

        </form>
    </div>

</div>
</div>

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
