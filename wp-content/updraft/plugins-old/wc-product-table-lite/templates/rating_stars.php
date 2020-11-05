<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$rating_number = $product->get_average_rating();

if( ! $rating_number ){
	return;
}

$full_stars  = floor( $rating_number );

$dec = $rating_number - $full_stars;

if( $dec < .25 ){
	$half_stars = 0;

}else if( $dec > .75 ){
	$half_stars = 0;
	++$full_stars;

}else{
	$half_stars = 1;

}

$empty_stars = 5 - $full_stars - $half_stars;

ob_start();
foreach ( array( $full_stars, $half_stars, $empty_stars ) as $key => $star_type ) {
    while ($star_type) {
      if ($key === 0) {
				?><i class="wcpt-star wcpt-star-full">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#FFC107"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
				</i><?php
      } else if ($key === 1) {
				?><i class="wcpt-star wcpt-star-half">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#FFC107"><polygon points="12 2 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#aaa"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77"></polygon></svg>
				</i><?php
      } else {
				?><i class="wcpt-star wcpt-star-empty">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#FFC107"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
				</i><?php
      }

      --$star_type;
    }
}
$rating_stars = ob_get_clean();

echo '<div class="wcpt-rating-stars '. $html_class .'">' . $rating_stars . '</div>';
