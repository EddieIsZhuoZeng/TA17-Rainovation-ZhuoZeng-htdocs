<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! $html ){
	return;
}

echo '<span class="wcpt-html '. $html_class .'">' . $html . '</span>';
