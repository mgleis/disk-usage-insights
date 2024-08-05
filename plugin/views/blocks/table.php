<?php
if (!defined('ABSPATH')) {  // Ensure running within WordPress
    exit;
}
?>
<div class="DUI-panel DUI-panel--TOP10">
    <div class=DUI-panel__headline><?php echo esc_html($this->headline); ?></div>
    <div class=DUI-panel__content>
        <table class=DUI-table>
            <thead>
                <tr class=DUI-table__header>
                    <?php foreach ($this->columnNames as $col) { ?>
                        <th>
                            <?php echo esc_html($col); ?>
                        </th>
                    <?php } ?>
                </tr>
            </thead>
        <tbody>
            <?php foreach ($table as $row) { ?>
                <tr>
                <?php foreach ($row as $idx => $column) { ?>
                    <?php if (!empty($this->columnCss[$idx])) { ?>
                        <td class="<?php echo esc_attr($this->columnCss[$idx]); ?>">
                    <?php } else { ?>
                        <td>
                    <?php } ?>
                        <?php echo esc_html($column); ?>
                    </td>
                <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
        </table>
    </div>
</div>
