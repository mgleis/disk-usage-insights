<?php
if (!defined('ABSPATH')) {  // Ensure running within WordPress
    exit;
}
?>
<?php include_once __DIR__ . '/blocks/header.php'; ?>


<h1>Snapshot <?php echo esc_html($snapshot); ?></h1>
<div>
    <a href="?page=disk-usage-insights">Dashboard</a> &gt; Snapshot <?php echo esc_html($snapshot); ?>
</div>
<br>

<div class="DUI-panel">
    <div class="DUI-panel__content">
        Root Directory: <?php echo esc_html($root); ?><br>
        Total Size: <?php echo esc_html(number_format_i18n($totalSize)); ?>
    </div>
</div>
