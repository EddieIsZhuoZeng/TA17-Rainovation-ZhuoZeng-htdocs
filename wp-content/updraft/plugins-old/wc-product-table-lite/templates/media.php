<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// prepare params
if( empty( $id ) ){
  return;
}
if( empty( $size )  ){
  $size = 'thumbnail';
}

// conditional on custom field
if( ! empty( $if_cf ) ){
  $val = get_post_meta( $product->get_id(), $if_cf, true );
  // if a val is provided for comparison, compare with it
  if( ! empty( $cf_value ) && $val != $cf_value ){
    return;
  }

  // if a comparison val is not provided assume the condition to be 'EXISTS'
  if( ! isset( $cf_value ) && ! $val ){
    return;
  }
}

echo wp_get_attachment_image( $id, $size = 'thumbnail' );
