<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$prices 		= $product->get_variation_prices( true );
$max_price 	= end( $prices['price'] );

echo '<span class="wcpt-highest-price '. $html_class .'">' . wcpt_price($max_price) . '</span>';
