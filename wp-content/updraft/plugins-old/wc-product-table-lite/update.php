<?php 
// update table data based on current version
add_filter( 'wcpt_data', 'wcpt_update_table_data', 10, 1 );
function wcpt_update_table_data( $data ){
  // ensure version number
  // version was not stored in table data before 1.9.0
  if( empty( $data['version'] ) ){
    $data['version'] = '1.8.0';
  }

  // skip update
  if(
    ! empty( $data['version'] ) &&
    $data['version'] === WCPT_VERSION 
  ){
    return $data;
  }

  // backup current data
  update_post_meta( $data['id'], 'wcpt_' . $data['version'], $data );

  // update to 1.9.0
  if( version_compare( $data['version'], '1.9.0', '<' ) ){

    // nav: search filter
    $searches = wcpt_get_nav_elms_ref( $data, 'search' );

    foreach( $searches as &$search ){
      $search['attributes'] = array();
      if( $search['custom_fields'] && gettype( $search['custom_fields'] ) === 'string' ){
        $search['custom_fields'] =  array_map( 'trim', preg_split( '/\r\n|\r|\n/', $search['custom_fields'] ) );        
      }

      if( gettype( $search['target'] ) === 'string' ){
        $target = array();

        if( ! empty( $search['target'] ) ){
          foreach( array( 'title', 'content', 'custom_field' ) as $field ){
            if( FALSE !== strrpos( $search['target'], $field ) ){
              $target[] = $field;
            }
          }
  
        }else{
          $target = array( 'title', 'content' );
        }
  
        $search['target'] = $target;
      }

    }

    $data['version'] = '1.9.0';

  }

  $data['version'] = WCPT_VERSION;
  
  // update meta
  update_post_meta( $data['id'], 'wcpt', $data );

  return $data;
}

// gets refrence to the requested nav element
// TODO
function wcpt_get_nav_reference( $type, &$arr, &$container = false ){
  if( FALSE === $container ){
    $container = array();
  }

  foreach( $arr as $key => &$val ){
    if(
      $key === 'type' &&
      $val === $type
    ){
      $container[] = $arr;
      break;

    }else if( gettype( $val ) == 'array' ){
      $container = array_merge( $container, wcpt_get_nav_reference( $type, $val, $container ) );

    }
  }

  return $container;
}

// returns references for specific nav filter type 
function wcpt_get_nav_elms_ref( &$data, $type= false ){
  $navigation =& $data['navigation']['laptop'];
  $rows = array( &$navigation['left_sidebar'][0] );

  foreach( $navigation['header']['rows'] as &$header_row ){
    foreach( $header_row['columns'] as &$column ){
      $rows[] =& $column['template'][0];
    }
  }

  $elements = array();
  foreach( $rows as &$row ){
    if( ! empty( $row['elements'] ) ){
      foreach( $row['elements'] as &$element ){
        if( 
          $type &&
          $type !== $element['type']
        ){
          continue;
        }
  
        $elements[] =& $element;
      }
    }
  }

  return $elements;
}


function wcpt_update_settings_data(){
  $data = json_decode( stripslashes( get_option( 'wcpt_settings', '' ) ), true );

  // ensure version number
  // version was not stored in settings before 1.9.0
  if( empty( $data['version'] ) ){
    $data['version'] = '1.8.0';
  }

  // skip update
  if(
    ! empty( $data['version'] ) &&
    $data['version'] === WCPT_VERSION 
  ){
    return FALSE;
  }

  // backup current data
  update_option( 'wcpt_settings_' . $data['version'], $data );

  // update to 1.9.0
  if( version_compare( $data['version'], '1.9.0', '<' ) ){
    $data = apply_filters('wcpt_settings', $data, 'edit' );
    $data['version'] = '1.9.0';

  }

  $data['version'] = WCPT_VERSION;
  
  // update meta
  update_option( 'wcpt_settings', addslashes( json_encode($data) ) );

  return $data;
}