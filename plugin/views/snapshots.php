<div class="DUI-panel">
    <div class="DUI-panel__headline">
        View a previous snapshot
    </div>
    <div class="DUI-panel__content">
        <div style="margin: 20px;">

            <?php if (sizeof($DATABASES) > 0) { ?>

                <table>
                    <tr>
                        <th>Filename</th>
                        <th>Size</th>
                    </tr>
                    <?php foreach ($DATABASES as $DB) { ?>
                        <tr>
                            <td><a href="?page=disk-usage-insights&snapshot=<?php echo esc_attr($DB['filename']); ?>"><?php echo esc_html($DB['filename']); ?></a></td>
                            <td><?php echo esc_html(number_format_i18n($DB['filesize'])); ?></td>
                            <td>
                                <button
                                    hx-target="#DUI-snapshots"
                                    hx-post="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=dui_delete_snapshot"
                                    hx-vals='{"_ajax_nonce":"<?php echo esc_js($WP_NONCE); ?>", "snapshot":"<?php echo esc_js($DB['filename']); ?>"}'
                                    hx-confirm="Are you sure to delete this snapshot?"
                                >Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                    </table>

            <?php } else { ?>
                You haven't taken any snapshots yet.
            <?php } ?>

        </div>
    </div>
</div>
