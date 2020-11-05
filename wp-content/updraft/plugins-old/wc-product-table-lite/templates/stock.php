<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '<span class="wcpt-stock '. $html_class .'">'. $product->get_stock_quantity() .'</span>';
