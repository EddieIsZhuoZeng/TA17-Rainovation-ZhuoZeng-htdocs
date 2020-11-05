<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( $products->max_num_pages <= 1 ) {
	return;
}

?>
<div class="wcpt-pagination wcpt-device-<?php echo $device; ?>">
	<?php
		$args = array(
			'format'       => '?'. $table_id .'_paged=%#%',
			'total'        => $products->max_num_pages,
			'current'      => max( 1, $products->query_vars['paged'] ),
			'prev_next'    => false,
			'prev_text'    => false,
			'next_text'    => false,
			'type'         => 'plain',
			'end_size'     => 1,
			'mid_size'     => 1,
			'before_page_number' => '',
			'after_page_number'  => '',
			'add_args'     => false,
		);
		// if( ! empty( $_POST['url'] ) ){
		// 	$args['base'] = $_POST['url'] . '%_%';
		// }
		echo paginate_links( $args );
	?>
</div>
