<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '<span class="wcpt-sku '. $html_class .'">' . $product->get_sku() . '</span>';
