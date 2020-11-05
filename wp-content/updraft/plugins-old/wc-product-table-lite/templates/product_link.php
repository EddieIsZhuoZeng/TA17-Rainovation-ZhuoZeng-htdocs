<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! empty( $condition ) && ! wcpt_condition( $condition ) ){
	return;
}

if( empty( $target ) ){
	$target = "_self";
}

$title = get_the_title( $product->get_id() );
$url 	 = get_permalink( $product->get_id() );

if( empty( $template ) ){
	$template = get_the_title( $product->get_id() );
}else{
	$template = wcpt_parse_2( $template );
}

$template = str_replace( array( '[title]', '[url]' ), array( $title, $url ), $template );

echo '<a class="wcpt-product-link '. $html_class .'" href="'. $url . $suffix .'" target="'. $target .'">'. $template .'</a>';
