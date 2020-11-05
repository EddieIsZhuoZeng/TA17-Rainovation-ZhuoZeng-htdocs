<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( $product->get_type() == 'variation' ){
	$content = $product->get_description();

}else{
	$content = get_the_content();

}

if( ! $content ){
	return;
}

if( ! empty( $limit ) ){
	$content = wp_filter_nohtml_kses( $content );
	preg_match("/(?:\w+(?:\W+|$)){0,$limit}/", $content, $matches);
	$_content = rtrim($matches[0], ' ,.') ;
	if( strlen( $content ) > strlen( $_content ) ){
		$_content .= '...';
	}
	$content = $_content;
}

echo '<div class="wcpt-content '. $html_class .'">';
echo $content;
echo '</div>';
