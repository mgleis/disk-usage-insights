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

                    <div>A new snapshot of your file system will be generated and stored for later review.</div>
                </form>
            </div>
        </div>
    </div>

    <div class="DUI-panel">
        <div class="DUI-panel__headline">
            View a previous snapshot
        </div>
        <div class="DUI-panel__content">
            <div style="margin: 20px;">

                <table>
                    <tr>
                        <th>Filename</th>
                        <th>Date</th>
                        <th>Size</th>
                    </tr>
                    <?php foreach ($DATABASES as $DB) { ?>
                        <tr>
                            <td><a href="?page=disk-usage-insights&snapshot=<?php echo $DB['filename']; ?>"><?php echo $DB['filename']; ?></a></td>
                            <td></td>
                            <td><?php echo $DB['filesize']; ?></td>
                            <td><button>Delete</button></td>
                        </tr>
                    <?php } ?>
                </table>

            </div>
        </div>
    </div>


</div>
