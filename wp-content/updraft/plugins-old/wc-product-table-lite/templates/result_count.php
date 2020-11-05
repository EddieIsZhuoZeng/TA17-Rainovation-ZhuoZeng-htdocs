<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( empty( $message ) ){
  $message = __( 'Showing [first_result] - [last_result] of [total_results] results', 'wc-product-table' );
}

if( empty( $single_page_message ) ){
  $single_page_message = __( 'Showing all [displayed_results] results', 'wc-product-table' );
}

if( empty( $single_result_message ) ){
  $single_result_message = __( 'Showing the single result', 'wc-product-table' );
}

if( empty( $no_results_message ) ){
  $no_results_message = __( 'No results found', 'wc-product-table' );
}

?>
<div class="wcpt-result-count <?php echo $html_class; ?> [result-count-html-class]">
  <span class="wcpt-result-message"><?php echo $message; ?></span>
  <span class="wcpt-single-page-message"><?php echo $single_page_message; ?></span>
  <span class="wcpt-single-result-message"><?php echo $single_result_message; ?></span>
  <span class="wcpt-no-results-message"><?php echo $no_results_message; ?></span>
</div>
