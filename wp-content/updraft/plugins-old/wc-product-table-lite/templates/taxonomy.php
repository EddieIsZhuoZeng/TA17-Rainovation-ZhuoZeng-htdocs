<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// false attribute
if( empty( $taxonomy ) ){
	return;
}

$terms = wp_get_post_terms( $product->get_id(), $taxonomy );

if(
	empty( $terms ) ||
	is_wp_error( $terms )
){
	$terms = false;

}

// excludes array
$excludes_arr = array();
if( ! empty( $exclude_terms ) ){
	$excludes_arr = preg_split( '/\r\n|\r|\n/', $exclude_terms );
}

if( $terms && count( $terms ) ){
// associated terms exist

	if( empty ( $separator ) ){
 		$separator = '';
 	}else{
		$separator = wcpt_parse_2( $separator );
	}

	$output = '';

	if( empty( $relabels ) ){
		$relabels = array();
	}

	// sort terms prioritizing current filter
	global $wcpt_table_data;
	$table_id = $wcpt_table_data['id'];
	$filter_key = $table_id . '_tax_' . $taxonomy;
	if( ! empty( $_GET[ $filter_key ] ) && ! empty( $terms ) ){
		$_terms = array();
		foreach( $terms as $term ){
			$_terms[$term->term_id] = $term;
		}
		$terms = array_replace( array_intersect_key( array_flip( $_GET[ $filter_key ] ), $_terms ), $_terms );
	}

	$terms = array_values($terms);

	// relabel each term
	foreach( $terms as $index => $term ){

		// exclude
		if( in_array( $term->name, $excludes_arr ) ){
			continue;
		}

		// filtering
		if( 
			! empty( $_GET[ $filter_key ] ) &&
			is_array( $_GET[ $filter_key ] ) &&
			in_array( $term->term_taxonomy_id, $_GET[ $filter_key ] )
		){
			$filtering = 'true';
		}else{
			$filtering = 'false';
		}

		// look for a matching rule
    $match = false;

    foreach( $relabels as $rule ){
      if( 
				! empty( $rule['term'] ) && wp_specialchars_decode( $term->name ) == $rule['term'] ||
				(
					function_exists('icl_object_id') &&
					! empty( $rule['ttid'] ) &&
					$term->term_taxonomy_id == icl_object_id( $rule['ttid'], $taxonomy, false )
				)
			){
        $match = true;

				// style
				wcpt_parse_style_2( $rule, '!important' );
				$term_html_class = 'wcpt-' . $rule['id'];

				// append
				$label = str_replace( '[term]', $term->name, wcpt_parse_2( $rule['label'] ) );
				$output .= '<div class="wcpt-taxonomy-term ' . $term_html_class . '" data-wcpt-slug="'. $term->slug .'" data-wcpt-filtering="'. $filtering .'">' . $label . '</div>';

				break;
      }
    }

		if( ! $match ){
			$output .= '<div class="wcpt-taxonomy-term " data-wcpt-slug="'. $term->slug .'" data-wcpt-filtering="'. $filtering .'">' . $term->name . '</div>';
		}

		if( $index < count( $terms ) - 1 ){
			$output .= '<div class="wcpt-taxonomy-term-separator wcpt-term-separator">'. $separator .'</div>';
		}

  }

}else{
// no associated terms

	if( empty( $empty_relabel ) ){
		$empty_relabel = '';
	}

	$output = wcpt_parse_2($empty_relabel);

}

if( ! empty( $output ) ){
	if( ! wcpt_check_if_nav_has_filter( null, 'taxonomy_filter', $taxonomy ) ){
		$filter_link_term = false;
	}

	if( empty( $filter_link_term ) ){
		$link_info = '';
	}else{
		$html_class .= ' wcpt-filter-link-terms ';
		$link_info = 'data-wcpt-taxonomy="'. $taxonomy .'"';
	}

	echo '<div class="wcpt-taxonomy '. $html_class .'" '. $link_info .'>' . $output . '</div>';
}
