<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( empty( $template ) ){
	return;
}

if( ! empty( $condition ) && ! wcpt_condition( $condition ) ){
	return;
}

if( ! empty( $use_default_template ) ){
	echo $product->get_price_html();
	return;
}

// variable product
if( $product->get_type() == 'variable' ){

	$prices = $product->get_variation_prices( true );

	if ( empty( $prices['price'] ) ) {
		return apply_filters( 'woocommerce_variable_empty_price_html', '', $product );

	} else {
		$min_price     = current( $prices['price'] );
		$max_price     = end( $prices['price'] );
		$min_reg_price = current( $prices['regular_price'] );
		$max_reg_price = end( $prices['regular_price'] );

		if ( $min_price !== $max_price ) {
			$template = $variable_template;

		} elseif ( $product->is_on_sale() && $min_reg_price === $max_reg_price ) {
			$on_sale_class = 'wcpt-product-on-sale';
			$template = $sale_template;

		} else {
			// regular template
			// already assigned by default
		}

	}

// sale - simple product
}else if( $product->is_on_sale() ){
	$on_sale_class = 'wcpt-product-on-sale';
	$template = $sale_template;
}


echo '<span class="wcpt-price '. $html_class . ' ' . ( ! empty( $on_sale_class ) ? $on_sale_class : '' ) .'">' . wcpt_parse_2( $template, $product ) . '</span>';
