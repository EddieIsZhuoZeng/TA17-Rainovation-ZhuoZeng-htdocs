<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 6/6/20 06:35
 */
if (!defined("ABSPATH")) die();
?>
<div class='w3eden'>
    <div class='<?php echo $css_class; ?>'>
        <?php include \WPDM\Template::locate("shortcodes/toolbar.php", WPDM_TPL_DIR, WPDM_TPL_FALLBACK); ?>

        <div id="content_<?php echo $scid; ?>">
            <?php echo $cimg ?>
            <?php echo $subcats ?>
            <?php echo $html ?>
            <?php echo $pagination ?>
        </div>

        <div style='clear:both'></div>
    </div>
</div>
