<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( $product->get_type() == 'variable' ){
	$prices 				= $product->get_variation_prices( true );
	$regular_price 	= current( $prices['price'] );

}else{
	$regular_price = wc_get_price_to_display( $product, array(
		'qty' => 1,
		'price' => $product->get_regular_price(),
	) );

}

if( ! $regular_price ){
	return apply_filters( 'woocommerce_empty_price_html', '', $product );

}

?>
<span class="wcpt-regular-price <?php echo $html_class ?>"><?php echo wcpt_price( $regular_price ); ?></span>
