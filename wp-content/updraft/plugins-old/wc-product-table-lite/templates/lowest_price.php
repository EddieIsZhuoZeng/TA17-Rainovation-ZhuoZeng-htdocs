<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$prices 		= $product->get_variation_prices( true );
$min_price 	= current( $prices['price'] );

echo '<span class="wcpt-highest-price '. $html_class .'">' . wcpt_price($min_price) . '</span>';
