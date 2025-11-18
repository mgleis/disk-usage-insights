<?php
if (!defined('ABSPATH')) {  // Ensure running within WordPress
    exit;
}
?>
<div class="DUI-panel DUI-panel--TOP10">
    <div class="DUI-panel__headline">

        File Browser:

        <?php foreach ($breadCrumbs as $index => $bc): ?>
            /
            <?php if ($index < count($breadCrumbs) - 1): ?>
                <a href="" hx-trigger="click" hx-target="closest .DUI-panel" hx-swap="outerHTML"
                    hx-get="<?php echo esc_url($bc['link']); ?>"
                ><?php echo esc_html($bc['name']); ?></a>
            <?php else: ?>
                <?php echo esc_html($bc['name']); ?>
            <?php endif; ?>
        <?php endforeach; ?>

    </div>
    <div class="DUI-panel__content">

        <div style="display: flex; gap: 20px;">

            <div style="_flex: 1;">

                <style>
                    .donut-chart {
                        background: conic-gradient(
                            <?php foreach ($items as $index => $item): ?>
                                    var(--dui-legend-color-<?php echo $index % 10; ?>) <?php echo $item['conic']; ?><?php echo ($index < count($items) - 1) ? ',' : ''; ?>
                            <?php endforeach; ?>
                        );
                    }
                </style>
                <div>
                    <div class="donut-chart">
                        <div class="donut-chart__center">
                            <span class="donut-chart__label">Total</span>
                            <span class="donut-chart__value"><?php echo esc_html(number_format_i18n($totalSize)); ?></span>
                        </div>
                    </div>
                </div>

            </div>

            <div style="flex: 1">
                <?php foreach ($items as $index => $item): ?>
                    <div style="margin-bottom: 4px; display: flex; align-items: center;">
                        <div class="donutchart__legendcolor donutchart__color--<?php echo $index % 10; ?>"></div>
                        <div>
                        <?php if ($item['type'] == 'dir'): ?>
                                <a href="#" hx-trigger="click" hx-target="closest .DUI-panel" hx-swap="outerHTML"
                                    hx-get="<?php echo esc_url($item['link']); ?>"
                                >[<?php echo esc_html($item['name']); ?>]</a>
                                - <?php echo esc_html(number_format_i18n($item['size'])); ?>
                            <?php else: ?>
                                <?php echo esc_html($item['name']); ?>
                                - <?php echo esc_html(number_format_i18n($item['size'])); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>


    </div>
</div>

