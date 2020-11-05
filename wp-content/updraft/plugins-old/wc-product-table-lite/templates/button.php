<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! empty( $condition ) && ! wcpt_condition( $condition ) ){
	return;
}

// default
if( ! empty( $use_default_template ) ){
	echo '<div class="woocommerce">';
	woocommerce_template_loop_add_to_cart();
	echo '</div>';
	return;
}

// label
if( empty( $label ) ){
	$label = '';
}else{
	$label = '<span class="wcpt-button-label">'. wcpt_parse_2( $label ) .'</span>';
}

// link
if(
	empty( $link ) ||
	$product->get_type() == 'grouped'
){
	$link = 'product_link';
}

switch ( $link ) {
	case 'cart_checkout':
		$href = wc_get_checkout_url();
		break;

	case 'product_link':
		$href = get_permalink( $product->get_id() );
		break;

	case 'external_link':
		if( $product->get_type() !== 'external' || ! $product->get_product_url() ){
			return;
		}else{
			$href = $product->get_product_url();
		}
		break;

	case 'cart_refresh':
		$href = '';
		break;

	case 'custom_field':
		if( empty( $custom_field ) || ! $href = get_post_meta( $product->get_id(), $custom_field, true ) ){
			if( ! empty( $custom_field_empty_relabel ) && ! empty( $custom_field_empty_relabel ) ){
				echo '<span class="wcpt-button-cf-empty">' . wcpt_parse_2($custom_field_empty_relabel) . '</span>';
			}
			return;
		}
		break;

	case 'custom_field_media_id':
		if( empty( $custom_field ) || ! ( $field_value = get_post_meta( $product->get_id(), $custom_field, true ) ) ){
			if( ! empty( $custom_field_empty_relabel ) && ! empty( $custom_field_empty_relabel ) ){
				echo '<span class="wcpt-button-cf-empty">' . wcpt_parse_2($custom_field_empty_relabel) . '</span>';
			}
			return;
		}

		$href = wp_get_attachment_url( $field_value );

		break;

	default:
		$href = wc_get_cart_url();
		break;
}

// target / download
if( empty( $target ) ){
	$target = ' target="_self" ';

}else if( $target === 'download' ){
	$target = ' download="'. basename( $href ) .'" ';
}else{
	$target = ' target="'. $target .'" ';
}

// disabled class
if(
	! in_array( $link, array( 'product_link', 'external_link', 'custom_field', 'custom_field_media_id' ) ) &&
	in_array( $product->get_type(), array( 'simple', 'variation' ) ) &&
	( ! $product->is_purchasable() || ! $product->is_in_stock() )
){
	$disabled_class = ' wcpt-disabled ';
}else{
	$disabled_class = '';
}

// cart badge
if( $link !== 'product_link' && $link !== 'external_link' && $link !== 'custom_field' && $link !== 'custom_field_media_id' ){
	$quantity = 0;
	if( wp_doing_ajax() ){
		foreach( WC()->cart->cart_contents as $key => $item ){
			if( $item['product_id'] == $product->get_id() ){
				$quantity += $item['quantity'];
			}
		}
	}

	if( ! $quantity ){
		$quantity = '';
	}

	$cart_badge = '<i class="wcpt-cart-badge-number"></i>';
}else{
	$cart_badge = '';
}

echo '<a class="wcpt-button wcpt-button-'. $link .' ' . $html_class . $disabled_class . '" data-wcpt-link-code="'. $link .'" href="'. $href .'" '. $target .' >' . $label . $cart_badge . '</a>';
