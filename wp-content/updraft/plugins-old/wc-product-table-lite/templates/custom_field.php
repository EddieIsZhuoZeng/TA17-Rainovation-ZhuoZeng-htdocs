<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( empty( $manager ) ){
	$field_value = get_post_meta( $product->get_id(), $field_name, true );

}else if( $manager === 'acf' && function_exists( 'get_field' ) ){
	$field_value = get_field( $field_name, $product->get_id(), true );
	$field_object = get_field_object( $field_name );

	// link
	if(
		$field_object['type'] == 'link' &&
		$field_object['return_format'] === 'array' &&
		! empty( $field_value['url'] ) &&
		! empty( $field_value['title'] )
	){
		$field_value = '<a class="wcpt-acf-link" href="'. $field_value['url'] .'" target="'. $field_value['target'] .'">'. $field_value['title'] .'</a>';

	// file
	}else if(
		$field_object['type'] == 'file' &&
		$field_object['return_format'] === 'array'
	){
		$field_value = '<a class="wcpt-acf-file" href="'. $field_value['url'] .'" download="'. esc_attr( $field_value['filename'] ) .'">'. $field_value['filename'] .'</a>';

	// image
	}else if(
		$field_object['type'] == 'image' &&
		$field_object['return_format'] === 'array' &&
		! empty( $field_value['url'] )
	){
		$field_value = '<img class="wcpt-acf-image" src="'. $field_value['url'] .'" />';
	}

}else{
	return;
}

if( $product->get_type() == 'variation' ){
	// sent from attribute template Trying to get term
	if( $field_value && $attribute_name ){
		$taxonomy  ='pa_' . $attribute_name;
		$term = get_term_by('slug', $field_value, $taxonomy);
		$field_value = $term->name;

	// could be defined on parent
	}else if( ! $field_value ){
		$field_value = get_post_meta( wp_get_post_parent_id( $product->get_id() ), $field_name, true );

	}
}

// is forwarded by attribute tmply because 'product variation'
if( $field_value && ! empty( $attribute_name ) ){
	$taxonomy  ='pa_' . $attribute_name;
	$term = get_term_by('slug', $field_value, $taxonomy);
	$field_value = $term->name;
}

if( ! empty( $condition ) && ! wcpt_condition( $condition ) ){
	return;
}

// iterate over custom relabel rules first
if( empty( $relabel_rules ) ){
	$relabel_rules = array();
}

foreach( $relabel_rules as $rule ){

	$use = false;

	if( $rule['compare'] == '=' && $rule['value'] != $field_value ){
		continue;

	}else if( $rule['compare'] == 'BETWEEN' ){
		if(
			( ! empty( $rule['min_value'] ) && (int) $rule['min_value'] > $field_value ) ||
			( ! empty( $rule['max_value'] ) && (int) $rule['max_value'] < $field_value )
		){
				continue;
		}

	}

	echo '<div class="wcpt-custom-field '. $html_class .'" ">' . wcpt_parse_2($rule['label']) . '</div>';

	return;
}

// empty value
if( empty( $empty_relabel ) ){
	$empty_relabel = '';
}

if( in_array( $field_value, array( '', null ), true ) ){
		if( $empty_relabel = wcpt_parse_2( $empty_relabel ) ){
			echo '<div class="wcpt-custom-field-empty">' . $empty_relabel . '</div>';
		}

		return;
}

// by default show value as text
if( empty( $display_as ) ){
	$display_as = 'text';
}

// for ACF shown value as HTML
if( ! empty( $manager ) ){
	$display_as = 'html';
}

switch ($display_as) {
	case 'text':
		$field_value = htmlentities( $field_value );
		break;

	case 'html':
		// do shortcodes as well
		global $wp_embed;
		$field_value = do_shortcode( $wp_embed->run_shortcode($field_value) );
		break;

	case 'link':
		$label = rtrim( preg_replace("(^https?://)", "", $field_value ), '/' );
		if( empty( $link_target ) ){
			$link_target = '_self';
		}
		$field_value = '<a class="wcpt-cf-link" href="'. $field_value .'" target="'. $link_target .'">'. $label .'</a>';
		break;

	case 'phone_link':
		$field_value = '<a class="wcpt-cf-phone-link" href="tel:'. $field_value .'">'. $field_value .'</a>';
		break;

	case 'email_link':
		$field_value = '<a class="wcpt-cf-phone-link" href="mailto:'. $field_value .'">'. $field_value .'</a>';
		break;

	case 'pdf_link':
		if( empty( $pdf_val_type ) || $pdf_val_type == 'url' ){
			$url = $field_value;
		}else{
			$url = wp_get_attachment_url( $field_value );
		}

		if( ! $url ){
			if( $empty_relabel = wcpt_parse_2( $empty_relabel ) ){
				echo '<div class="wcpt-custom-field-empty">' . $empty_relabel . '</div>';
			}
			return;

		}else{
			$label = wcpt_parse_2( $pdf_link_label );
			$label = substr( substr( $label, 0, -6 ), 4 );
			$label = '<span' . $label . '</span>';

			$field_value = '<a class="wcpt-cf-pdf-link" href="'. esc_attr($url) .'" download="'. esc_attr( basename($url) ) .'">'. $label .'</a>';

			break;

		}

	case 'image':
		if( empty( $img_val_type ) || $img_val_type == 'url' ){
			$field_value = '<img class="wcpt-cf-image" src="'. $field_value .'" />';
		}else{
			if( empty( $media_img_size ) ){
				$media_img_size = 'thumbnail';
			}
			$img = wp_get_attachment_image( $field_value, $media_img_size );

			// no media img for id
			if( ! $img ){
				if( $empty_relabel = wcpt_parse_2( $empty_relabel ) ){
					echo '<div class="wcpt-custom-field-empty">' . $empty_relabel . '</div>';
				}
				return;
			}else{
				$style= "";
				if( ! empty( $img_max_width ) ){
					$style = 'style="max-width: '. $img_max_width .'px"';
				}

				$field_value = '<span class="wcpt-cf-image" '. $style .'>'. $img .'</span>';
			}
		}
		break;
}

echo wcpt_parse_2('<div class="wcpt-custom-field '. $html_class .'">' . $field_value . '</div>');
