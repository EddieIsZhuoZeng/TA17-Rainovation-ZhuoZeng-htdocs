<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
	return;
}

if( empty( $template ) ){
	if( isset( $output_format ) ){
		$template = $output_format;
	}else{
		return;
	}
}

if( $product->get_review_count() ){
	echo '<div class="wcpt-rating '. $html_class .'" title="'. $product->get_average_rating() . ' out of 5 stars">' . wcpt_parse_2( $template ) . '</div>';

}else{

	if( empty( $not_rated ) ){
		$not_rated = '';
	}

	echo '<div class="wcpt-rating '. $html_class .'">' . wcpt_parse_2( $not_rated ) . '</div>';

}
