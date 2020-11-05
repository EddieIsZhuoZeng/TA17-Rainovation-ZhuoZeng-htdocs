<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( empty( $format ) ){
	$format = 'M j, Y';
}
echo '<span class="wcpt-bang '. $html_class .'">'. get_the_date($format) .'</span>';
