<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '<div class="wcpt-excerpt '. $html_class .'">';
global $wp_embed;
echo do_shortcode( $wp_embed->run_shortcode( wpautop( get_the_excerpt() ) ) );
echo '</div>';
