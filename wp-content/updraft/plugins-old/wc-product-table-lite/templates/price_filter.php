<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// min & max inputs
$table_id = $GLOBALS['wcpt_table_data']['id'];

$input_field_name = $table_id . '_price_range';

$min_input_field_name = $input_field_name . '_min';
$max_input_field_name = $input_field_name . '_max';

$selected_min = ! empty( $_GET[ $min_input_field_name ] ) ? $_GET[ $min_input_field_name ] : '';
$selected_max = ! empty( $_GET[ $max_input_field_name ] ) ? $_GET[ $max_input_field_name ] : '';

// pre-selected

if( $pre_selected = wcpt_get_nav_filter( 'price_range' ) ){
	if( empty( $_GET[$table_id . '_filtered'] ) ){
		// apply
		//-- min
		$_GET[ $min_input_field_name ] = $_REQUEST[ $min_input_field_name ] = $pre_selected['min_price'];
		//-- max
		$_GET[ $max_input_field_name ] = $_REQUEST[ $max_input_field_name ] = $pre_selected['max_price'];

	}else{
		// remove
		wcpt_clear_nav_filter( 'price_range' );
	}
}

if(
	empty( $display_type ) ||
	( ! empty( $position ) && $position === 'left_sidebar' )
){
  $display_type = 'dropdown';
}

if( $display_type == 'dropdown' ){
  $container_html_class = 'wcpt-dropdown wcpt-filter wcpt-range-filter ' . $html_class;
  $heading_html_class = 'wcpt-dropdown-label';
  $options_container_html_class = 'wcpt-dropdown-menu';
  $single_option_container_html_class = 'wcpt-dropdown-option';

  if( empty( $heading ) ){
    $heading = __('Price range', 'wc-product-table');
  }

}else{ // row
  $container_html_class = 'wcpt-options-row wcpt-filter wcpt-range-filter ' . $html_class;
  $heading_html_class = 'wcpt-options-heading';
  $options_container_html_class = 'wcpt-options';
  $single_option_container_html_class = 'wcpt-option';

  if( empty( $heading ) ){
    $heading = '';
  }
}

if( empty( $range_options ) ){
	$range_options = array();
}

foreach( $range_options as $option ){
	if( empty( $option['min'] ) ){
		$option['min'] = '';
	}

	if( empty( $option['max'] ) ){
		$option['max'] = '';
	}

	// get selected
	if( $selected_min == $option['min'] && $selected_max == $option['max'] ){
		$selected_range_option = $option;

		if( empty( $selected_range_option['clear_label'] ) ){
			if( empty( $option['min'] ) ){
				$selected_range_option['clear_label'] = 'Price range: Upto ' . wcpt_price($option['max']);

			}else if( empty( $option['max'] ) ){
				$selected_range_option['clear_label'] = 'Price range: ' . wcpt_price($option['min']) . '+';

			}else{
				$selected_range_option['clear_label'] = 'Price range: ' . wcpt_price($option['min']) . ' - ' . wcpt_price($option['max']);

			}
		}
	}
}

// heading row
if( ! $heading = wcpt_parse_2( $heading ) ){
	$container_html_class .= ' wcpt-no-heading wcpt-filter-open';
}

// applied filter
$input_field_name = $GLOBALS['wcpt_table_data']['id'] . '_price_range';
if( ! empty( $_REQUEST[$input_field_name] ) ){
	$container_html_class .= ' wcpt-filter-open';
}

if( ! empty( $accordion_always_open ) ){
	$container_html_class .= ' wcpt-filter-open';
}

?>
<div class="<?php echo $container_html_class; ?>" data-wcpt-filter="price_range">

	<div class="wcpt-filter-heading">
		<!-- label -->
	  <span class="<?php echo $heading_html_class; ?>"><?php echo wcpt_parse_2( $heading );?></span>
	  <!-- icon -->
	  <?php wcpt_icon('chevron-down'); ?>
	</div>

  <!-- options menu -->
  <div class="<?php echo $options_container_html_class; ?>">
    <?php

			$min_input_field_name = $input_field_name . '_min';
			$max_input_field_name = $input_field_name . '_max';

      $selected_min = ! empty( $_GET[ $min_input_field_name ] ) ? $_GET[ $min_input_field_name ] : '';
      $selected_max = ! empty( $_GET[ $max_input_field_name ] ) ? $_GET[ $max_input_field_name ] : '';

			$value = ( $selected_min ? wcpt_price( $selected_min ) : 0 ) . ( $selected_max ? ' - ' . wcpt_price( $selected_max ) : '+' );

      // use filter in query
      $filter_info = array(
        'filter'        => 'price_range',
        'values'        => array( $value ),
        'min_price'     => $selected_min,
        'max_price'     => $selected_max,
        'clear_label'   => 'Price range',
      );

			// clear label
			if( ! empty( $selected_range_option ) ){
				$filter_info['clear_labels_2'] = array(
					$value => $selected_range_option['clear_label'],
				);
			}else{
				$filter_info['clear_labels_2'] = array(
					$value => 'Price range : ' . $value,
				);

				if( empty( $selected_min ) ){
					if( empty( $no_min_clear_label ) ){
						$no_min_clear_label = '[filter] : Upto [max]';
					}
					$clear_label = $no_min_clear_label;

				}else if( empty( $selected_max ) ){
					if( empty( $no_max_clear_label ) ){
						$no_max_clear_label = '[filter] : [min]+';
					}
					$clear_label = $no_max_clear_label;

				}else{
					if( empty( $min_max_clear_label ) ){
						$min_max_clear_label = '[filter] : [min] - [max]';
					}
					$clear_label = $min_max_clear_label;

				}

				$clear_label =  str_replace(
					array( '[filter]', '[min]', '[max]' ),
					array( 'Price range', $selected_min ? wcpt_price( $selected_min ) : '', $selected_max ? wcpt_price( $selected_max ) : '' ),
					$clear_label
				);

				$filter_info['clear_labels_2'] = array(
					$value => $clear_label,
				);

			}

      wcpt_update_user_filters( $filter_info, true );

			// show any option
			if( empty( $show_all_label ) ){
				$show_all_label = __('Show any', 'wc-product-table');
			}

			if( empty( $_GET[$input_field_name] ) && empty( $selected_min ) && empty( $selected_max ) ){
				$checked = ' checked="checked" ';
			}else{
				$checked = '';
			}

			?>
			<label class="<?php echo $single_option_container_html_class; ?>">
				<!-- radio -->
				<input
					type="radio"
					value=""
					class="wcpt-filter-radio" <?php echo $checked; ?>
					name="<?php echo $input_field_name; ?>"
					data-wcpt-range-min=""
					data-wcpt-range-max=""
					<?php echo $checked; ?>
				>
				<!-- option label -->
				<span><?php echo wcpt_parse_2( $show_all_label ) ?></span>
			</label>
			<?php

			// range options

      if( ! empty( $range_options ) ){

        foreach ( $range_options as $option_index => $option ) {

          if( empty( $option['min'] ) ){
            $option['min']  = '';
          }

          if( empty( $option['max'] ) ){
            $option['max']  = '';
          }

          // option was selected or not?
          if(
            (int) $option['min'] == $selected_min &&
            (int) $option['max'] == $selected_max
          ){

            $selected = ' checked="checked" ';

          }else{
            $selected = '';
          }

          ?>
          <label class="<?php echo $single_option_container_html_class; ?>">
            <!-- radio -->
            <input
              type="radio"
              value="option_<?php echo $option_index; ?>"
              class="wcpt-filter-radio" <?php echo $selected; ?>
              name="<?php echo $input_field_name; ?>"
              data-wcpt-range-min="<?php echo ! empty( $option['min'] ) ? $option['min'] : ''; ?>"
              data-wcpt-range-max="<?php echo ! empty( $option['max'] ) ? $option['max'] : ''; ?>"
            >
            <!-- option label -->
            <span><?php echo ! empty( $option['label'] ) ? wcpt_parse_2( $option['label'] ) : 'Range option'; ?></span>
          </label>
          <?php
        }
      }
    ?>

    <?php
      $html_maybe_hide_class = '';
      if( empty( $custom_min_max_enabled ) ){
        $html_maybe_hide_class = 'wcpt-hide';
      }
    ?>
    <div class="wcpt-range-options-main <?php echo $single_option_container_html_class . ' ' . $html_maybe_hide_class; ?>">
      <input type="number" class="wcpt-range-input-min" name="<?php echo $min_input_field_name; ?>" value="<?php echo $selected_min; ?>" placeholder="<?php echo ! empty( $min_label ) ? $min_label : 'Min'; ?>" min="0">
      <span class="wcpt-range-input-separator">
        <?php echo ! empty( $to_label ) ? $to_label : 'to'; ?>
      </span>
      <input type="number" class="wcpt-range-input-max" name="<?php echo $max_input_field_name; ?>" value="<?php echo $selected_max; ?>" placeholder="<?php echo ! empty( $max_label ) ? $max_label : 'Max'; ?>">
      <span class="wcpt-range-submit-button">
        <?php echo ! empty( $go_label ) ? $go_label : 'GO'; ?>
      </span>
    </div>

  </div>

</div>
