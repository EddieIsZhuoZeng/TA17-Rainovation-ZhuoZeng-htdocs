<?php
/**
 * User: shahnuralam
 * Date: 17/11/18
 * Time: 1:09 AM
 */
if (!defined('ABSPATH')) die();
if(isset($attrs)) extract($attrs);
?>
<div class="card <?php echo isset($class) ? esc_attr($class) : '' ?>">
    <?php if($heading != ''){ ?>
    <div class="card-header"><?php echo $heading; ?></div>
    <?php } ?>
    <?php if(is_array($content) && count($content) > 0){
        foreach ($content as $html)
        ?>
        <div class="card-body"><?php echo $html; ?></div>
    <?php } else { ?>
        <div class="card-body"><?php echo $content; ?></div>
    <?php } ?>
    <?php if($footer != ''){ ?>
        <div class="card-footer"><?php echo $footer; ?></div>
    <?php } ?>
</div>
