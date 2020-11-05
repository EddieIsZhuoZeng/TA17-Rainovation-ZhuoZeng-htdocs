<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;
$taxonomy = 'product_cat';
$terms = get_the_terms( $product->get_id(), $taxonomy );

// excludes array
$excludes_arr = array();
if( ! empty( $exclude_terms ) ){
	$excludes_arr = preg_split( '/\r\n|\r|\n/', $exclude_terms );
}

// relabels

if( $terms && count( $terms ) ){
// associated terms exist

	if( empty ( $separator ) ){
 		$separator = '';
 	}else{
		$separator = wcpt_parse_2( $separator );
	}

	$output = '';

	// relabel each term
	foreach( $terms as $index => $term ){

		// exclude
		if( in_array( $term->name, $excludes_arr ) ){
			continue;
		}

		// look for a matching rule
	    $match = false;

	    if( ! empty( $relabels ) ){

		    foreach( $relabels as $rule ){
					if(
						wp_specialchars_decode( $term->name ) == $rule['term'] ||
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
						$output .= '<div class="wcpt-category ' . $term_html_class . '">' . $label . '</div>';

						break;
		      }
		    }

	    }

		if( ! $match ){
			$output .= '<div class="wcpt-category" data-wcpt-slug="'. $term->slug .'">' . $term->name . '</div>';
		}

		if( $index < count( $terms ) - 1 ){
			$output .= '<div class="wcpt-category-separator wcpt-term-separator">'. $separator .'</div>';
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
	echo '<div class="wcpt-categories '. $html_class .'">' . $output . '</div>';
}
