<?php

function wcpt_condition( $condition ){

  // no condition was set
  $no_condition = true;
  $conditions = array(
    'product_type_enabled',
    'custom_field_enabled',
    'attribute_enabled',
    'category_enabled',
    'stock_enabled',
    'price_enabled',
    'user_role_enabled',
  );
  foreach( $conditions as $i ){
    if( ! empty( $condition[$i] ) ){
      $no_condition = false;
    }
  }

  if( $no_condition ){
    return true;
  }

  // evaluate condition
  if( empty( $condition['action'] ) ){
    $condition['action'] = 'show';
  }

  $result = wcpt_condition_helper( $condition ); // boolean

  return ( $condition['action'] == 'show' ) ? $result : ! $result;

}

function wcpt_condition_helper( $condition ){

  global $product;

  extract( $condition );

  // product type
  if( ! empty( $product_type_enabled ) && ! empty( $product_type ) && ! in_array( $product->get_type(), $product_type ) ){
  	return false;
  }

  // user roles
  if( ! empty( $user_role_enabled ) && ! empty( $user_role ) ){
    // site viewer
    if( ! is_user_logged_in() ){
      if( ! in_array( '_visitor', $user_role ) ){
        return false;
      }

    // logged in user
    }else{
      $user = wp_get_current_user();
      $role = ( array ) $user->roles;
      $role = $role[0];

      if( ! in_array( $role, $user_role ) ){
        return false;
      }

    }
  }

  // custom field condition
  if( ! empty( $custom_field_enabled ) && ! empty( $custom_field ) ){

    if( empty( $custom_field_value ) ){
      $custom_field_value = '';
    }

    $val = get_post_meta( $product->get_id(), $custom_field, true );

    // no val permitted
    if( $custom_field_value == '-' ){
      if( $val !== '' ){
        return false;
      }

    // any value permitted
    }else if( ! $custom_field_value ){
      if( $val === '' ){
        return false;
      }

    }else{

      $arr = array_map( 'trim', explode( '||',  $custom_field_value ) );
      $arr2 = array_map( 'trim', explode( '-',  $custom_field_value ) );

      // range
      if( count( $arr2 ) == 2 ){

        if( ! ( (int)$arr2[0] <= (int)$val && (int)$val <= (int)$arr2[1] ) ){
          return false;
        }

      }else{

        // multi/single
        if( ! in_array( $val, $arr ) ){
          return false;
        }

      }

    }

  }

  // attribute condition
  if( ! empty( $attribute_enabled ) && ! empty( $attribute ) ){

    $terms = get_the_terms( $product->get_id(), 'pa_' . $attribute );

    if( empty( $attribute_term ) ){
      $attribute_term = '';
    }

    // no term permitted
    if( $attribute_term == '-' ){
      if( $terms ){
        return false;
      }

    // any value permitted
    }else if( ! $attribute_term ){
      if( ! $terms ){
        return false;
      }

    }else{

      if( ! $terms ){
        return false;
      }

      $arr = array_map( 'trim', explode( '||',  $attribute_term ) );

      $term_slugs = array();
      foreach( $terms as $term ){
        $term_slugs[] = $term->slug;
      }

      if( ! count( array_intersect( $arr, $term_slugs ) ) ){
        return false;
      }

    }

  }

  // category condition
  if( ! empty( $category_enabled ) && ! empty( $category ) ){

    $terms = get_the_terms( $product->get_id(), 'product_cat' );

    if( empty( $category ) ){
      $category = '';
    }

    if( ! $terms ){
      return false;
    }

    $arr = array_map( 'trim', explode( '||',  $category ) );

    $term_slugs = array();
    foreach( $terms as $term ){
      $term_slugs[] = $term->slug;
    }

    if( ! count( array_intersect( $arr, $term_slugs ) ) ){
      return false;
    }

  }

  // stock condition
  if( ! empty( $stock_enabled ) && ! empty( $stock ) && $product->get_manage_stock() == 'yes' ){

    $val = $product->get_stock_quantity();

    $arr = array_map( 'trim', explode( '||',  $stock ) );
    $arr2 = array_map( 'trim', explode( '-',  $stock ) );

    // range
    if( count( $arr2 ) == 2 ){

      if( ! ( (int)$arr2[0] <= (int)$val && (int)$val <= (int)$arr2[1] ) ){
        return false;
      }

    }else{

      // multi/single
      if( ! in_array( $val, $arr ) ){
        return false;
      }

    }

  }

  // stock condition
  if( ! empty( $price_enabled ) && ! empty( $price ) &&  ! in_array( $product->get_type(), array( 'variable', 'grouped' ) ) ){

    $val = $product->get_price();

    $arr = array_map( 'trim', explode( '||',  $price ) );
    $arr2 = array_map( 'trim', explode( '-',  $price ) );

    // range
    if( count( $arr2 ) == 2 ){

      if( ! ( (int)$arr2[0] <= (int)$val && (int)$val <= (int)$arr2[1] ) ){
        return false;
      }

    }else{

      // multi/single
      if( ! in_array( $val, $arr ) ){
        return false;
      }

    }

  }

  return true;

}


?>
