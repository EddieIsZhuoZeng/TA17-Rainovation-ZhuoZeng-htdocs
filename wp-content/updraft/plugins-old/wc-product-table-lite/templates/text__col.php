<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! $text || ( ! empty( $condition ) && ! wcpt_condition( $condition ) ) ){
	return;
}

if( ! empty( $condition ) && ! wcpt_condition( $condition ) ){
	return;
}

echo '<span class="wcpt-text '. $html_class .'">' . htmlentities( $text, ENT_NOQUOTES ) . '</span>';
