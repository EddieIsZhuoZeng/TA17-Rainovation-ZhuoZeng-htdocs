<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! $product->is_on_sale() ){
	return;
}

$sale_price = wcpt_price( wc_get_price_to_display( $product, array(
	'qty' => 1,
	'price' => $product->get_sale_price(),
) ) );

echo '<span class="wcpt-sale-price '. $html_class .'">' . $sale_price . '</span>';
