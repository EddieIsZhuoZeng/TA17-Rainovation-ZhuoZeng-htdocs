<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// product variation
if( $product->get_type() == 'variation' && get_post_thumbnail_id( $product->get_id() ) ){
	$object_id = $product->get_id();

// regular product
}else{
	$object_id = wp_get_post_parent_id( $product->get_id() );
}

$post_thumbnail_id = get_post_thumbnail_id( $object_id );

if( empty( $click_action ) ){
	$click_action = false;
}

if( empty( $size ) ){
	$size = 'thumbnail';
}

$img_markup = '';

if( ! $post_thumbnail_id && ! empty( $placeholder_enabled ) ){
	$img_markup = str_replace('class="woocommerce-placeholder', 'class="woocommerce-placeholder ' . $html_class, wc_placeholder_img( $size ) );
	$using_placeholder = true;
}else{
	$img_markup = get_the_post_thumbnail( $object_id, $size, array( 'title' => get_post_field( 'post_title', $post_thumbnail_id ), 'class' => $html_class ) );
}

$title = '';
$html_class = 'wcpt-product-image-wrapper '. $html_class;

$lightbox_attrs = '';
$lightbox_icon = '';
if( $click_action == 'lightbox' && empty( $using_placeholder ) ){
	$lightbox_attrs = ' data-wcpt-lightbox="'. get_the_post_thumbnail_url( $object_id, 'large' ) .'" ';
	$html_class .= ' wcpt-lightbox-enabled ';
	if( empty( $icon_when ) ){
		$icon_when = 'always';
	}
	ob_start();
	if( 'never' != $icon_when ){
		wcpt_icon('search', 'wcpt-lightbox-icon wcpt-when-' . $icon_when);
	}
	$lightbox_icon = ob_get_clean();
}

$zoom_attrs = '';
if( ! empty( $zoom_trigger ) ){
	$html_class .= ' wcpt-zoom-enabled ';
	if( empty( $zoom_scale ) ){
		$zoom_scale = '1.75';
	}

	if( $zoom_scale == 'custom' ){
		if( empty( $custom_zoom_scale ) ){
			$custom_zoom_scale = '1.75';
		}

		$zoom_scale = $custom_zoom_scale;
	}

	$zoom_attrs .= ' data-wcpt-zoom-level="' . $zoom_scale . '" ';
	$zoom_attrs .= ' data-wcpt-zoom-trigger="' . $zoom_trigger . '" ';
}

if( in_array( $click_action, array( 'product_page', 'product_page_new' ) ) ){
	$target = '';
	if( $click_action == 'product_page_new' ){
		$target = ' target="_blank" ';
	}

	echo '<a class="'. $html_class .'" data-wcpt-image-size="'. $size .'" '. $target .' href="'. get_the_permalink( $product->get_id() ) .'" '. $title .' '. $zoom_attrs .' >'. $img_markup . '</a>';
}else{
	echo '<div class="'. $html_class .'" data-wcpt-image-size="'. $size .'" '. $title .' '. $lightbox_attrs .' '. $zoom_attrs .' >' . $img_markup . $lightbox_icon . '</div>';
}
