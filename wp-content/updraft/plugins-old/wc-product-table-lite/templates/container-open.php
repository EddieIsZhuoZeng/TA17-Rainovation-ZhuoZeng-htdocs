<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$query_string_arr = array();

foreach( $_GET as $key => $val ){

	if(
		! in_array(
				strtolower( $key ),
				apply_filters( 'wcpt_permitted_params', array( 's', 'post_type' ) )
		) &&
		(
			empty ( $val ) ||
			0 !== strpos( $key, $table_id ) || // table id should be key prefix
			in_array(
				strtolower( $key ),
				array( // excluded
					$table_id . '_sc_attrs',
					// $table_id . '_paged',
					$table_id . '_url',
					// $table_id . '_fresh_search',
				)
			)
		)

	){
		continue;
	}

	if( is_array( $val ) ){
		$imploded_val = implode( '', $val );
		if( ! $imploded_val ){
			continue;
		}

		$val = array_unique( array_values( $val ) );
	}

	if( 
		0 !== strpos( $key, 'search' ) &&
		! is_array( $val )
	){
		// $val = htmlentities( stripslashes( $val ) );
		$val = htmlentities( $val );
	}

	$query_string_arr[ $key ] = $val;
}

$query_string = add_query_arg( $query_string_arr, '' );
?>

<div
	id="wcpt-<?php echo $table_id;?>"
	class="wcpt wcpt-<?php echo $table_id;?> <?php do_action( 'wcpt_container_html_class' ); ?> <?php if( isset( $this->attributes['class'] ) ) echo trim( $this->attributes['class'] ); ?>"
	data-wcpt-table-id="<?php echo $table_id;?>"
	data-wcpt-query-string="<?php echo $query_string; ?>"
	data-wcpt-sc-attrs="<?php echo esc_attr( json_encode( $GLOBALS['wcpt_table_data']['query']['sc_attrs'] ) ); ?>"
>
