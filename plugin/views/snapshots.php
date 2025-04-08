<div class="DUI-panel">
    <div class="DUI-panel__headline">
        View a previous snapshot
    </div>
    <div class="DUI-panel__content">
        <div style="margin: 20px;">

            <?php if (sizeof($DATABASES) > 0) { ?>

                <table class="DUI-table">
                    <thead>
                        <tr class="DUI-table__header">
                            <th>Filename</th>
                            <th>Snapshot Size</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($DATABASES as $DB) { ?>
                        <tr>
                            <td><a href="?page=disk-usage-insights&snapshot=<?php echo esc_attr($DB['filename']); ?>"><?php echo esc_html($DB['filename']); ?></a></td>
                            <td class="DUI-table__col--number"><?php echo esc_html(number_format_i18n($DB['filesize'])); ?></td>
                            <td>
                                &nbsp;
                                <button
                                    hx-target="#DUI-snapshots"
                                    hx-post="<?php echo esc_url($WP_ADMIN_AJAX_URL); ?>?action=dui_delete_snapshot"
                                    hx-vals='{"_ajax_nonce":"<?php echo esc_js($WP_NONCE); ?>", "snapshot":"<?php echo esc_js($DB['filename']); ?>"}'
                                    hx-confirm="Are you sure to delete this snapshot?"
                                >Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                You haven't taken any snapshots yet.
            <?php } ?>
        </div>
    </div>
</div>
