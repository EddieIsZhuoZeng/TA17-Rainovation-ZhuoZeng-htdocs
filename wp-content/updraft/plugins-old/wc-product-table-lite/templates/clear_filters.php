<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// no relevant user filter set
if( empty( $_GET ) || ! count( $_GET ) ){
	return;
}else{
	$table_id = (string) $GLOBALS['wcpt_table_data']['id'];
	$esc = true;
	foreach( $_GET as $key => $val ){
		if(
			empty( $val ) ||
			( is_array( $val ) && ! implode( $val ) ) ||
			( strlen( $key ) <  ( strlen( $table_id ) + 2 ) ) || // key too short to be filter
			in_array( substr( $key, strlen( $table_id ) + 1  ), array( 'availability', 'orderby', 'order', 'paged', 'device', 'sc_attrs', 'results_per_page', 'filtered' ) )
		){
			continue;
		}
		if( substr( $key, 0, strlen( $table_id ) ) == $table_id ){
			$esc = false;
			break;
		}
	}

	if( $esc ){
		return;
	}
}

if( empty( $GLOBALS['wcpt_nav_later_flag'] ) ){
	// defer this elm post $nav, to its filter hook
	$placeholder = '{' . $elm_tpl . '-' . rand(0, 10000) . '}';
	$GLOBALS['wcpt_nav_later'][] = array(
		'placeholder' => $placeholder,
		'element' 		=> $element,
		'elm_tpl' 		=> $elm_tpl,
		'elm_type' 		=> $elm_type,
		'product' 		=> $product,
	);
	echo $placeholder;

	return;
}

if( empty( $GLOBALS['wcpt_user_filters'] ) ){
  return;
}

// skip if only sorting
if( count( $GLOBALS['wcpt_user_filters'] ) == 1 && $GLOBALS['wcpt_user_filters'][0]['filter'] == 'orderby' ){
  return;
}

ob_start();

foreach( $GLOBALS['wcpt_user_filters'] as $filter_info ){
  if(
    ( empty( $filter_info['values'] ) ) ||
    $filter_info['filter'] == 'orderby' ||
    ( $filter_info['filter'] == 'price_range' && empty( $filter_info['min_price'] ) && empty( $filter_info['max_price'] ) )
  ){
    continue;
  }

	foreach( $filter_info['values'] as $option ){
		if( ! empty( $filter_info['clear_labels_2'] ) || ! empty( $filter_info['clear_label'] ) ){

			?>
		 	<div
		 		class="wcpt-clear-filter"
		 		data-wcpt-filter="<?php echo $filter_info['filter']; ?>"
		 		data-wcpt-taxonomy="<?php echo isset($filter_info['taxonomy']) ? $filter_info['taxonomy'] : ''; ?>"
		 		data-wcpt-meta-key="<?php echo isset($filter_info['meta_key']) ? $filter_info['meta_key'] : ''; ?>"
		 		data-wcpt-value="<?php echo esc_attr( $option ); ?>"
		 	>

		 		<!-- x icon -->
		     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>

		 		<?php
		 		if( 
					 ! empty( $filter_info['clear_labels_2'] ) &&
					 ! empty( $filter_info['clear_labels_2'][ $option ] )
				){
		 			echo '<span class="wcpt-filter-label">' . $filter_info['clear_labels_2'][ $option ] . '</span>';
		 		}else{

					if( in_array( $filter_info['filter'], array( 'attribute', 'category', 'taxonomy' ) ) ){
						$term = get_term_by('term_taxonomy_id', $option);
						$label = $term->name;
					}else{

						$label = $option;
					}

		 			?>
		 			<span class="wcpt-filter-label"><?php echo $filter_info['clear_label']; ?></span><span class="wcpt-separator wcpt-colon">:</span>
		 	    <span class="wcpt-selected-filter"><?php echo $label; ?></span>
		 			<?php
		 		}
		 		?>

		   </div>
		 	<?php

		}
	}

}

$markup = trim( ob_get_clean() );
if( $markup ){

	if( empty( $reset_label ) ){
		$reset_label = 'Clear all';
	}

	echo '
		<div class="wcpt-clear-filters-wrapper '. $html_class .'">
			<a href="javascript:void(0)" class="wcpt-clear-all-filters wcpt-small-device-only">'. $reset_label .'</a>'
			. $markup .
			'<a href="javascript:void(0)" class="wcpt-clear-all-filters wcpt-big-device-only">'. $reset_label .'</a>
		</div>
	';
}
?>
