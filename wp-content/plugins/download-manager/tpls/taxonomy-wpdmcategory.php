<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 30/5/20 13:44
 */
if(!defined("ABSPATH")) die();

get_header();

$category = get_queried_object();
$cpage_global = maybe_unserialize(get_option('__wpdm_cpage'));
$cpage_global = !is_array($cpage_global) ? [ 'template' => 'link-template-default', 'cols' => 2, 'colsphone' => 1, 'colspad' => 1, 'heading' => 1 ] : $cpage_global;

$cpage = maybe_unserialize(get_term_meta(get_queried_object_id(), '__wpdm_pagestyle', true));
$cpage = !is_array($cpage) ? $cpage_global : $cpage;
if(get_queried_object_id() > 0)
    $cpage['categories'] = $category->slug;
$cpage['toolbar'] = (int)$cpage['heading'];
$cpage['async'] = 1;
$cpage['paging'] = 1;
if(isset($_GET['skw'])) $cpage['s'] = esc_attr($_GET['skw']);
?>
<div class="w3eden">
    <div class="container p-0">
        <div class="row">
            <div class="col-md-12">
                <div class="pb-5">
                    <?php
                    echo WPDM()->shortCode->packages( $cpage );
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

get_footer();
