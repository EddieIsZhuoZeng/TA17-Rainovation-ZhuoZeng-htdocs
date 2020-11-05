<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// product variation
if( $product->get_type() == 'variation' ){
  $product_id_data = ' data-wcpt-product-id="'. wp_get_post_parent_id( $product->get_id() ) .'" ';
	$variation_id_data = ' data-wcpt-variation-id="'. $product->get_id() .'" ';
	$variation_attributes_data = '';
	$variation_attributes = $product->get_variation_attributes();
	if( $variation_attributes ){
		$variation_attributes_data = ' data-wcpt-variation-attributes="'. esc_attr( json_encode( $variation_attributes ) ) .'" ';
	}

// other product type
}else{
	$product_id_data = ' data-wcpt-product-id="'. $product->get_id() .'" ';
	$variation_id_data = '';
	$variation_attributes_data = '';

}

$product_type_html_class = 'wcpt-product-type-' . $product->get_type();

$in_cart = wcpt_get_cart_item_quantity($product->get_id());

$stock = $product->get_stock_quantity();

$html_class = ' wcpt-row '; // main row class
$html_class .= ' wcpt-'. ( $products->current_post % 2 ? 'even' : 'odd' )  .' '; // even / odd class
$html_class .= ' '. $product_type_html_class .' '; // product type

$html_class = apply_filters( 'wcpt_product_row_html_class', $html_class, $product );

echo '<tr
		'. $variation_id_data .' 
		'. $variation_attributes_data .' 
		'. $product_id_data .'
		class="'. $html_class .'" 
		data-wcpt-in-cart="'. $in_cart .'" 
		data-wcpt-stock="'. $stock .'"
	>';
?>
