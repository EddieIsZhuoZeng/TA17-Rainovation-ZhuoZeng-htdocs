<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$taxonomy = 'product_cat';

if(
	empty( $display_type ) ||
	( ! empty( $position ) && $position === 'left_sidebar' )
){
  $display_type = 'dropdown';
}

if( empty( $single ) ){
  $single = false;
}

if( $display_type == 'dropdown' ){
  $container_html_class = 'wcpt-dropdown wcpt-filter ' . $html_class;
  $heading_html_class = 'wcpt-dropdown-label';
  $options_container_html_class = 'wcpt-dropdown-menu';
  $single_option_container_html_class = 'wcpt-dropdown-option';

  if( empty( $heading ) ){
    $heading =  __( 'Category', 'wc-product-table' );
  }

}else{
  $container_html_class = 'wcpt-options-row wcpt-filter ' . $html_class;
  $heading_html_class = 'wcpt-options-heading';
  $options_container_html_class = 'wcpt-options';
  $single_option_container_html_class = 'wcpt-option';

}

// heading row
if( ! $heading ){
	$container_html_class .= ' wcpt-no-heading ';
}

// applied filter
$table_id = $GLOBALS['wcpt_table_data']['id'];
$input_field_name = $table_id . '_product_cat';
if( ! empty( $_REQUEST[$input_field_name] ) ){
	$container_html_class .= ' wcpt-filter-open';
}

if( ! empty( $accordion_always_open ) ){
	$container_html_class .= ' wcpt-filter-open';
}

$dropdown_options = array();

$table_data = wcpt_get_table_data();

if( ! empty( $table_data['query']['category'] ) ){
	$term_ids = explode( ',', $table_data['query']['category'] );
}else{
	$term_ids = array();
}

// pre-selected
if( $pre_selected = wcpt_get_nav_filter( 'category' ) ){
	if( empty( $_GET[$table_id . '_filtered'] ) ){
		// apply
		$_GET[$input_field_name] = $_REQUEST[$input_field_name] = $pre_selected['values'];
	}else{
		// remove
		wcpt_clear_nav_filter( 'category' );
	}
}

if( empty( $hide_empty ) ){
	$hide_empty = false;
}

$terms = wcpt_get_terms($taxonomy, $term_ids, $hide_empty);

if( is_wp_error( $terms ) || ! $terms ){
	return;
}

// excludes array
$excludes_arr = array();
if( ! empty( $exclude_terms ) ){
	$excludes_arr = preg_split( '/\r\n|\r|\n/', $exclude_terms );
}

// build dropdown array
foreach( $terms as $term ){

	// exclude
	if( in_array( $term->name, $excludes_arr ) ){
		continue;
	}

	// relabel
	if( isset( $relabels ) ){
		//-- look for a matching rule
		$match = false;
		foreach( $relabels as $rule ){
			if( 
				wp_specialchars_decode( $term->name ) == $rule['term'] ||
				(
					function_exists('icl_object_id') &&
					! empty( $rule['ttid'] ) &&
					$term->term_taxonomy_id == icl_object_id( $rule['ttid'], $taxonomy, false )
				)
			){
				$term->label = str_replace( '[term]', $term->name, wcpt_parse_2( $rule['label'] ) );
				if( ! empty( $rule['clear_label'] ) ){
					$term->clear_label = $rule['clear_label'];
				}
				$match = true;
			}
		}
	}

	if( ! isset( $term->label ) ){
		$term->label = $term->name;
	}

	// option must have value field
	$term->value = $term->term_taxonomy_id;

	// add term in dropdown options
	$dropdown_options[] = $term;

}

?>
<div class="<?php echo $container_html_class; ?>" data-wcpt-filter="category" data-wcpt-taxonomy="product_cat">

	<div class="wcpt-filter-heading">
		<!-- label -->
	  <span class="<?php echo $heading_html_class; ?>"><?php echo wcpt_parse_2($heading); ?></span>

		<!-- active count -->
	  <?php if( ! empty( $_GET[$input_field_name] ) && ! $single ){
		?>
	  <span class="wcpt-active-count"><?php echo count( $_GET[$input_field_name] ); ?></span>
		<?php } ?>

	  <!-- icon -->
	  <?php wcpt_icon('chevron-down'); ?>
	</div>

  <!-- options menu -->
	<div class="wcpt-hierarchy <?php echo $options_container_html_class; ?>">

	<?php
		// "Show all" option - when only one option is allowed to be selected
		if( 
			$single && 
			! wcpt_is_template_empty($show_all_label)
		){

			if(
				empty( $_GET[$input_field_name] ) ||
				! count( $_GET[$input_field_name] ) ||
				( count( $_GET[$input_field_name] ) == 1 && ! $_GET[$input_field_name][0] )
			){
				$checked = true;
			}else{
				$checked = false;
			}

			?>
			<label class="wcpt-show-all-option <?php echo $single_option_container_html_class; ?> <?php echo $checked ? 'wcpt-active' : ''; ?>" data-wcpt-value="">
				<input type="radio" value="" class="wcpt-filter-checkbox" <?php echo $checked ? ' checked="checked" ' : ''; ?> name="<?php echo $input_field_name; ?>[]"><?php echo wcpt_parse_2($show_all_label); ?>
			</label>
			<?php
		}
	?>
	
	<?php if( $display_type == 'dropdown' ){

		foreach( $dropdown_options as &$option ){
			$option = apply_filters( 'wcpt_nav_filter_option', $option, 'category', array( 'taxonomy' => $taxonomy ) );
		}

		wcpt_include_taxonomy_walker();
		$walker = new WCPT_Taxonomy_Walker(array(
			'field_name' => $input_field_name,
			'exclude' => $excludes_arr,
			'single' => $single,
			'hide_empty' => $hide_empty,
			'taxonomy' => $taxonomy,
			'pre_open_depth' => ! empty( $pre_open_depth ) ? (int) $pre_open_depth : 0,
			'option_class' => $single_option_container_html_class,
		));
		echo $walker->walk( $dropdown_options, 0 );

		// $option = apply_filters( 'wcpt_nav_filter_option', (array) $option, 'category', null );

	// row
	}else{

		if( ! empty( $dropdown_options ) ){

			foreach ( $dropdown_options as $option ) {
				// option was selected or not?
				$option = apply_filters( 'wcpt_nav_filter_option', (array) $option, 'category', array( 'taxonomy' => $taxonomy ) );

				if(
					! empty( $_GET[ $input_field_name ] ) &&
					(
						$_GET[ $input_field_name ] == $option['value'] ||
						(
							is_array( $_GET[ $input_field_name ] ) &&
							in_array( $option['value'], $_GET[ $input_field_name ] )
						)
					)
				){

					$checked = true;

					// use filter in query
					$filter_info = array(
						'filter'      => 'category',
						'values'      => array( $option['value'] ),
						'taxonomy'    => $taxonomy,
						'operator'    => ! empty( $operator ) ? $operator : 'IN',
						'clear_label' => __('Category', 'woocommerce'),
					);

					if( ! empty( $option['clear_label'] ) ){
						$filter_info['clear_labels_2'] = array(
							$option['value'] => str_replace( array( '[option]', '[filter]' ), array( $option['name'], __('Category', 'woocommerce') ), $option['clear_label'] ),
						);
					}else{
						$filter_info['clear_labels_2'] = array(
							$option['value'] => __('Category', 'woocommerce') . ' : ' .$option['name'],
						);
					}

					wcpt_update_user_filters( $filter_info, $single );

				}else{
					$checked = false;
				}

				?>
				<label class="<?php echo $single_option_container_html_class; ?> <?php echo $checked ? 'wcpt-active' : ''; ?>" data-wcpt-slug="<?php echo $option['slug']; ?>" data-wcpt-value="<?php echo $option['value']; ?>">
					<input type="<?php echo $single ? 'radio' : 'checkbox'; ?>" value="<?php echo $option['value']; ?>" class="wcpt-filter-checkbox" <?php echo $checked ? ' checked="checked" ' : ''; ?> name="<?php echo $input_field_name; ?>[]" data-wcpt-clear-filter-label="<?php echo esc_attr( $option['name'] ); ?>"><?php echo $option['label']; ?>
				</label>
				<?php
			}
		}

	} ?>
  </div>

</div>
