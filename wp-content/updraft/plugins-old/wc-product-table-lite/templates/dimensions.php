<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $product->has_dimensions() ) {
  echo '<div class="wcpt-dimensions '. $html_class .'">' . wc_format_dimensions( $product->get_dimensions( false ) ) . '</div>';
}