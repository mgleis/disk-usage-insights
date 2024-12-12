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
            <?php foreach ($table as $rowIdx => $row) { ?>
                <tr>
                <?php foreach ($row as $idx => $column) { ?>
                    <?php if (!empty($this->columnCss[$idx])) { ?>
                        <td class="<?php echo esc_attr($this->columnCss[$idx]); ?>">
                    <?php } else { ?>
                        <td>
                    <?php } ?>
                    <?php if ($this->hasPercentBar($idx)) { ?>
                        <div style="position:relative; min-width: 60px; _outline: 1px solid #ddd;">
                            <div style="background:#eeeeee; width: <?php echo $this->getPercentBar($rowIdx); ?>%;">&nbsp;</div>
                            <div style="position:absolute; top:0; right: 0;">
                                <?php echo esc_html($column); ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <?php echo esc_html($column); ?>
                    <?php } ?>
                    </td>
                <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
        </table>
    </div>
</div>
