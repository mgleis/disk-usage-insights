<?php
if (!defined('ABSPATH')) {  // Ensure running within WordPress
    exit;
}
?>
<?php include_once __DIR__ . '/blocks/header.php'; ?>


<h1>Snapshot <? echo $snapshot; ?></h1>
<div>
    Dashboard &gt; Snapshot <? echo $snapshot; ?>
</div>
<br>

<div class="DUI-panel">
    <div class="DUI-panel__content">
        Root Directory: <?php echo esc_html($root); ?><br>
        Total Size: <?php echo esc_html(number_format_i18n($totalSize)); ?>
    </div>
</div>
