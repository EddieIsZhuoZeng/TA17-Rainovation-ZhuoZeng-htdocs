<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( $custom_field ){
  $src = get_post_meta( $product->get_id(), $custom_field, true );
  echo '[audio src="'. $src .'"]';
}
