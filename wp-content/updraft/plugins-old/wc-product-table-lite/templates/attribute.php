<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// false attribute
if( empty( $attribute_name ) ){
	return;
}

// product variation
if( $product->get_type() == 'variation' ){
	$field_name = 'attribute_pa_' . $attribute_name;
	include( 'custom_field.php' );
	return;
}

$taxonomy = 'pa_' . $attribute_name;
$attributes = $product->get_attributes();

if ( isset( $attributes[ $attribute_name] ) ) {
	$attribute_object = $attributes[ $attribute_name];
} elseif ( isset( $attributes[ 'pa_' . $attribute_name ] ) ) {
	$attribute_object = $attributes[ 'pa_' . $attribute_name ];
}

if( empty( $attribute_object ) ){
	$terms = false;

} else if( $attribute_object && $attribute_object->is_taxonomy() ){
	$terms = wc_get_product_terms( $product->get_id(), $attribute_object->get_name(), array( 'fields' => 'all', 'orderby' => 'menu_id' ) );

}else{ // text attribute
	$terms = $attribute_object->get_options();

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

	// sort terms prioritizing current fitler
	global $wcpt_table_data;
	$table_id = $wcpt_table_data['id'];
	$filter_key = $table_id . '_attr_pa_' . $attribute_name;
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
				wp_specialchars_decode( $term->name ) == $rule['term'] ||
				(
					function_exists('icl_object_id') &&
					! empty( $rule['ttid'] ) &&
					$term->term_taxonomy_id == icl_object_id( $rule['ttid'], 'pa_'. $attribute_name , false )
				)
			){
				$match = true;

				// style
				wcpt_parse_style_2( $rule, '!important' );
				$term_html_class = 'wcpt-' . $rule['id'];

				// append
				$label = str_replace( '[term]', $term->name, wcpt_parse_2( $rule['label'] ) );
				$output .= '<div class="wcpt-attribute-term ' . $term_html_class . '" data-wcpt-slug="'. $term->slug .'" data-wcpt-filtering="'. $filtering .'">' . $label . '</div>';

				break;
      }
    }

		if( ! $match ){
			$output .= '<div class="wcpt-attribute-term " data-wcpt-slug="'. $term->slug .'" data-wcpt-filtering="'. $filtering .'">' . $term->name . '</div>';
		}

		if( $index < count( $terms ) - 1 ){
			$output .= '<div class="wcpt-attribute-term-separator wcpt-term-separator">'. $separator .'</div>';
		}

  }

}else{
// no associated terms

	if( empty( $empty_relabel ) ){
		$empty_relabel = '';
	}

	$output = wcpt_parse_2($empty_relabel);

}

if( ! wcpt_check_if_nav_has_filter( null, 'attribute_filter', $attribute_name ) ){
	$filter_link_term = false;
}

if( empty( $filter_link_term ) ){
	$link_info = '';
}else{
	$html_class .= ' wcpt-filter-link-terms ';
	$link_info = 'data-wcpt-taxonomy="'. $taxonomy .'"';
}

if( ! empty( $output ) ){
	echo '<div class="wcpt-attribute '. $html_class .'" '. $link_info .'>' . $output . '</div>';
}
