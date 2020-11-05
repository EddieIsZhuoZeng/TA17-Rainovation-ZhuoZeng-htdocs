<?php

function wcpt_styles(){
  ob_start();
  echo '<style>';
  echo wcpt_parse_media_query_toggle();
  echo wcpt_parse_style();
  echo wcpt_parse_elements_style();

  echo wcpt_parse_columns_style();
  echo wcpt_parse_css();

  echo '</style>';
  echo str_replace( array( '\r', '\n', '\t', '  ', '   ' ) , array(''), ob_get_clean() );
}

$wcpt_breakpoints = array(
  'tablet' => '1200',
  'phone'  => '700',
);

function wcpt_parse_style( ){

  $data = wcpt_get_table_data();
  $style = $data['style'];

  $css_string = '';

  $devices = array(
    'laptop',
    'tablet',
    'phone',
  );

  // iterate over all devices
  foreach( $devices as $device ){

    if( empty( $style[$device] ) ){
      continue; // device has no selectors
    }

    $device_style_string = '';

    // for the device iterate over all its selectors
    foreach( $style[$device] as $selector => $props ){

      if( empty( $props ) || ! is_array( $props ) ){
        // selector has no props
        // OR not a selector bur special prop like
        //-- inheritance
        continue; // selector has no props or props are
      }

      // build selector props string
      $props_string = '';
      foreach( $props as $prop => $val ){
        $arr = apply_filters( 'wcpt_style_prop_val', array(
          'prop' => $prop,
          'val'=> $val,
          'selector'=> $selector,
        ) );
        $prop = $arr['prop'];
        $val = $arr['val'];

        if( $val === '' ){
          continue;
        }
        $props_string .= $prop . ':' . $val . ';';
      }

      // deliver selector props string
      if( $props_string ){
        $device_style_string .= ' ' . $selector . '{'. $props_string .'}';
      }

    }

    // $device_style_string = str_replace( '[device]', '.wcpt-device-' . $device, $device_style_string );
    $device_style_string = str_replace( '[device]', '', $device_style_string );

    // media query

    //-- laptop
    if( $device == 'laptop' ){

      $min_width = '0';

      // apply above phone breakpoint
      if( empty( $style['phone']['inherit_tablet_style'] ) ){
        $min_width = (int) $GLOBALS['wcpt_breakpoints']['phone'] + 1;
      }

      // apply above tablet breakpoint
      if( empty( $style['tablet']['inherit_laptop_style'] ) ){
        $min_width = (int) $GLOBALS['wcpt_breakpoints']['tablet'] + 1;
      }

      $device_style_string = '@media(min-width:'. $min_width .'px){' . $device_style_string . '}';

    //-- tablet
    }else if( $device == 'tablet' ){

      $max_width = $GLOBALS['wcpt_breakpoints']['tablet'];
      $min_width = '0';

      // apply above phone breakpoint
      if( empty( $style['phone']['inherit_tablet_style'] ) ){
        $min_width = (int) $GLOBALS['wcpt_breakpoints']['phone'] + 1;
      }

      $device_style_string = '@media(max-width: '. $max_width .'px) and (min-width:'. $min_width .'px){' . $device_style_string . '}';

    //-- phone
    }else if( $device == 'phone' ){

      $max_width = $GLOBALS['wcpt_breakpoints']['phone'];

      // apply upto phone breakpoint
      $device_style_string = '@media(max-width:'. $max_width .'px){' . $device_style_string . '}';

    }

    // deliver device style string
    $css_string .= $device_style_string;

  }

  $css_string = str_replace('[container]', '#wcpt-' . $data['id'], $css_string);

  return $css_string;
}

// based on current device width, using media query, show the appropriate table
function wcpt_parse_media_query_toggle(){

  $data = wcpt_get_table_data();

  ob_start();

  // device columns
  $laptop_columns = wcpt_get_device_columns_2('laptop', $data);
  $tablet_columns = wcpt_get_device_columns_2('tablet', $data);
  $phone_columns = wcpt_get_device_columns_2('phone', $data);

  // tablet
  if( $tablet_columns ){
    ?>
    @media (max-width:<?php echo $GLOBALS['wcpt_breakpoints']['tablet']; ?>px) and (min-width:<?php echo $GLOBALS['wcpt_breakpoints']['phone']; ?>px){
      #wcpt-<?php echo $data['id']; ?> .wcpt-device-tablet {
        display: block;
      }

      #wcpt-<?php echo $data['id']; ?> .wcpt-device-laptop,
      #wcpt-<?php echo $data['id']; ?> .wcpt-device-phone {
        display: none;
      }
    }
    <?php
  }

  // phone
  if( $phone_columns ){
    ?>
    @media (max-width:<?php echo $GLOBALS['wcpt_breakpoints']['phone']; ?>px) {
      #wcpt-<?php echo $data['id']; ?> .wcpt-device-phone {
        display: block;
      }

      #wcpt-<?php echo $data['id']; ?> .wcpt-device-tablet,
      #wcpt-<?php echo $data['id']; ?> .wcpt-device-laptop {
        display: none;
      }
    }
    <?php
  }

  return ob_get_clean();
}

// css shortcodes facility
function wcpt_parse_css(){
  $data = wcpt_get_table_data();

  if( empty( $data['style']['css'] ) ){
    return;
  }

  $wcpt_selector = '#wcpt-' . $data['id'];
  $arr = array(
    '[container]'     => $wcpt_selector,
    '[table]'         => $wcpt_selector . ' .wcpt-table',
    '[heading_row]'   => $wcpt_selector . ' .wcpt-heading-row',

    '[heading_cell]'      => $wcpt_selector . ' .wcpt-heading-row .wcpt-heading',
    '[heading_cell_even]' => $wcpt_selector . ' .wcpt-heading-row .wcpt-heading:nth-child(even)',
    '[heading_cell_odd]'  => $wcpt_selector . ' .wcpt-heading-row .wcpt-heading:nth-child(odd)',

    '[row]'           => $wcpt_selector . ' .wcpt-row',
    '[row_even]'      => $wcpt_selector . ' .wcpt-row:nth-child(even)',
    '[row_odd]'       => $wcpt_selector . ' .wcpt-row:nth-child(odd)',

    '[cell]'          => $wcpt_selector . ' .wcpt-cell',
    '[cell_even]'     => $wcpt_selector . ' .wcpt-cell:nth-child(even)',
    '[cell_odd]'      => $wcpt_selector . ' .wcpt-cell:nth-child(odd)',

    '[tablet]'  => ' @media(max-width: 1199px){',
    '[/tablet]' => '} ',

    '[phone]'   => ' @media(max-width: 700px){',
    '[/phone]'  => '} ',
  );

  $search = array_keys( $arr );
  $replace = array_values( $arr );

  return str_replace( $search , $replace , $data['style']['css'] );
}

// pull out css from the elements
function wcpt_parse_elements_style(){
  $data = wcpt_get_table_data();
  $wcpt_selector = '#wcpt-' . $data['id'];
  $elements = wcpt_get_column_elements($data);
  $css = '';

  foreach( $elements as $element_type => $element_rows ){
    foreach( $element_rows as $element_settings ){
      if( ! empty( $element_settings['style'] ) ){
        $element_settings_style = '';

        $css_string = '';
        $style = &$element_settings['style'];
        foreach( $style as $selector => $props ){
          if( empty( $props ) ){
            continue;
          }

          $string = '';
          // collect style props for selector
          foreach( $props as $prop => $val ){
            $arr = apply_filters( 'wcpt_style_prop_val', array(
              'prop' => $prop,
              'val'=> $val,
              'selector'=> $selector,
            ) );
            $prop = $arr['prop'];
            $val = $arr['val'];

            if( $val != '' ){
              $string .= $prop . ':' . $val . ';';
            }

          }

          if( $string ){
            $css_string .= ' ' . $selector . '{'. $string .'}';
          }
        }

        if( $css_string && $element_settings['settings'] ){
          $container_selector = $wcpt_selector . ' .wcpt-'. $element_type . '-' . sanitize_title( $element_settings['settings'] );
          $css_string = str_replace('[container]', $container_selector, $css_string);

          $css .= $css_string;
        }

      }
    }
  }

  return $css;
}

// pull out css from the columns
function wcpt_parse_columns_style(){
  $data = wcpt_get_table_data();
  $wcpt_selector = '#wcpt-' . $data['id'];
  $devices = array(
    'laptop' => '',
    'tablet' => '1200px',
    'phone' => '700px',
  );

  ob_start();

  foreach( $devices as $device => $max_width ){

    $device_columns = wcpt_get_device_columns( $device, $data );

    if( ! $device_columns ){
      continue;
    }

    if( $max_width ){
      echo ' @media(max-width:'. $max_width .'){';
    }

    foreach( $device_columns as $column_key => $column ){
      if( empty( $column['style'] ) ){
        continue;
      }

      echo ' ' . $wcpt_selector . ' .wcpt-cell:nth-child('. ( $column_key + 1 ) .') {';

      foreach( $column['style'] as $prop => $val ){
        $arr = apply_filters( 'wcpt_style_prop_val', array(
          'prop' => $prop,
          'val'=> $val,
          'selector'=> $selector,
        ) );
        $prop = $arr['prop'];
        $val = $arr['val'];

        echo $prop . ':' . $val . ';';
      }

      echo '}';

    }

    if( $max_width ){
      echo '}';
    }

  }

  return ob_get_clean();

}

add_filter( 'wcpt_style_prop_val', 'wcpt_style_prop_val_filter' );
function wcpt_style_prop_val_filter( $arr ){

  // append 'px'
  if( is_numeric( $arr['val'] ) && ! in_array( $arr['prop'], array( 'opacity' ) ) ){
    $arr['val'] .= 'px';
  }

  return $arr;
}


/*
 * NEW
 */
function wcpt_parse_style_2( $item, $important= false ){

  if( empty( $item['style'] ) ){
    return;
  }

  // image width fix
  if(
    ! empty( $item['type'] ) &&
    $item['type'] == 'product_image' &&
    ! empty( $item['style'] ) &&
    ! empty( $item['style']['[id]'] ) &&
    ! empty( $item['style']['[id]']['max-width'] )
  ){
    $item['style']['[id]']['min-width'] = $item['style']['[id]']['max-width'];
  }

  $id = '.wcpt-' . $item['id'];
  // 'style' => { '[id]' => { 'style-prop' : 'style-val', }, '[id] .sub-elm-selector' => { '..' : '..', }, ... };
  foreach( $item['style'] as $selector => $style ){
    if( ! isset( $GLOBALS['wcpt_table_data']['style_items'][$selector] ) ){ // elm not already parsed
      $style_props = '';
      $hover_style = array();

      // border-solid property fix
      if(
        ! empty( $style['border-color'] ) ||
        ! empty( $style['border-width'] ) &&
        empty( $style['border-style'] )
      ){
        $style['border-style'] = 'solid';
      }else if(
        ! empty( $style['border-style'] ) &&
        empty( $style['border-width'] ) &&
        empty( $style['border-color'] )
      ){
        $style['border-style'] = '';
      }

      foreach( $style as $prop => $val ){
        if(
          ! empty( $val ) &&
          empty( $style['border-style'] ) &&
          in_array( $prop, array( 'border-color', 'border-width' ) )
        ){
          $style['border-style'] = 'solid';
        }

        // collect hover state props
        if( strlen( $prop ) > 6 && ':hover' == substr( $prop, -6 ) ){
          $hover_style[substr( $prop, 0, -6 )] = $val;

        // process normal state props
        }else{
          $arr = apply_filters( 'wcpt_style_prop_val', array(
            'prop' => $prop,
            'val'=> $val,
            'selector'=> $selector,
          ) );
          $prop = $arr['prop'];
          $val = $arr['val'];

          if( $val && gettype($val) == 'string' ){
            $style_props .= $prop . ':' . $val . ( $important ? ' !important' : '' ) . ';';
          }

        }

      }

      $selector = str_replace("[id]", $id, $selector);
      $GLOBALS['wcpt_table_data']['style_items'][$selector] = ' ' . $selector . '{'. $style_props .'} ';

      // parse hover
      if( count( $hover_style ) ){
        wcpt_parse_style_2( array( // dummy elm with bare info
          'id' => $item['id'],
          'style' => array(
            $selector . ':hover' => $hover_style
          )
        ), $important );
      }

    }
  }
}

function wcpt_item_styles(){
  $data = wcpt_get_table_data();
  if( empty( $data['style_items'] ) ){
    return;
  }

  $style_markup = '<style>';
  foreach( $data['style_items'] as $itm_selector => $itm_style_props ){
    $style_markup .= ' #wcpt-' . $data['id'] . ' ' . $itm_style_props;
    $style_markup .= ' body ' . $itm_style_props;
  }
  $style_markup .= '</style>';
  echo $style_markup;
}


?>
