<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$title = get_the_title( $product->get_id() );

if( ! empty( $product_link_enabled ) ){
	$target = '';
	if( ! empty( $target_new_page ) ){
		$target = ' target="_blank" ';
	}

	echo '<a class="wcpt-title '. $html_class .'" '. $target .' href="'. get_the_permalink( $product->get_id() ) .'" title="'. $title .'">'. $title .'</a>';

}else{
	echo '<span class="wcpt-title '. $html_class .'">' . $title . '</span>';

}
