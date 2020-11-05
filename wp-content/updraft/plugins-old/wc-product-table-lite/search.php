<?php
// TODO:
// Combined results from multiple search instances are sorted only as per last search 

// fulfil search 
function wcpt_search($filter_info, &$post__in){

  if( empty( $filter_info['keyword'] ) ){
    return;
  }

  $search_ids = array(
    'title' => array(
      'phrase_exact' => array(), // $keyword_phrase === $title
      'phrase_like' => array(), // $title = ...$keyword_phrase...
      'keyword_exact' => array(), // $title = $word1 $keyword $word2
      'keyword_like' => array(), // $title = $word1 ...$keyword... $word2
    ),
    'sku' => array(
      'phrase_exact' => array(),
      'phrase_like' => array(),
      'keyword_exact' => array(),
      'keyword_like' => array(),
    ),
    'category' => array(
      'phrase_exact' => array(),
      'phrase_like' => array(),
      'keyword_exact' => array(),
      'keyword_like' => array(),								
    ),
    'attribute' => array(
      'phrase_exact' => array(),
      'phrase_like' => array(),
      'keyword_exact' => array(),
      'keyword_like' => array(),
      'items' => array(),
    ),
    'tag' => array(
      'phrase_exact' => array(),
      'phrase_like' => array(),
      'keyword_exact' => array(),
      'keyword_like' => array(),
    ),
    'content' => array(
      'phrase_exact' => array(),
      'phrase_like' => array(),
      'keyword_exact' => array(),
      'keyword_like' => array(),
    ),
    'excerpt' => array(
      'phrase_exact' => array(),
      'phrase_like' => array(),
      'keyword_exact' => array(),
      'keyword_like' => array(),
    ),
    'custom_field' => array(
      'phrase_exact' => array(),
      'phrase_like' => array(),
      'keyword_exact' => array(),
      'keyword_like' => array(),
      'items' => array(),
    ),
  );

  $keyword_phrase = $filter_info['keyword'];

  $settings = wcpt_get_settings_data();

  // replacements
  foreach( preg_split( '/\r\n|\r|\n/', $settings['search']['replacements'] ) as $line ){
    $split1 = array_map( 'trim', explode( ':', $line ) );
    $correction = $split1[0];
    if( ! empty( $split1[1] ) ){
      $incorrect = array_map( 'trim', explode( '|', $split1[1] ) );
      $keyword_phrase = str_replace( $incorrect, $correction, $keyword_phrase );
    }
  }

  $keyword_separator = ! empty( $settings['search']['separator'] ) ? $settings['search']['separator'] : ' ';
  $stopwords = array_map( 'trim', explode( ',', $settings['search']['stopwords'] ) );
  $keywords = array_diff( explode( $keyword_separator, $keyword_phrase ), $stopwords );

  if( empty( $filter_info['target'] ) ){
    $filter_info['target'] = array('title', 'content');
  }

  if( in_array( 'custom_field', $filter_info['target'] ) ){
    $custom_fields__custom_rules = array();
    $custom_fields__default_rules = array();
  
    foreach( $settings['search']['custom_field']['items'] as $item ){
      if(
        ! in_array( $item['item'], $filter_info['custom_fields'] ) 
      ){
        continue;
      }
  
      if( $item['custom_rules_enabled'] ){
        $custom_fields__custom_rules[] = $item['item'];
      }else{
        $custom_fields__default_rules[] = $item['item'];
      }
    }
  }

  if( in_array( 'attribute', $filter_info['target'] ) ){
    $attributes__custom_rules = array();
    $attributes__default_rules = array();
  
    foreach( $settings['search']['attribute']['items'] as $item ){
      if(
        ! in_array( $item['item'], $filter_info['attributes'] ) 
      ){
        continue;
      }
  
      if( $item['custom_rules_enabled'] ){
        $attributes__custom_rules[] = $item['item'];
      }else{
        $attributes__default_rules[] = $item['item'];
      }
    }
  }

  global $wpdb;

  if( in_array( 'title', $filter_info['target'] ) ){
    $field = 'title';
    $item = null;

    $query = "
      SELECT ID 
      FROM $wpdb->posts 
      WHERE $wpdb->posts.post_type = 'product' 
      AND post_title 
    ";
    wcpt_search__query( $field, $item, $query, $keyword_phrase, $keywords, $search_ids );
  }

  if( in_array( 'category', $filter_info['target'] ) ){
    $field = 'category';
    $item = null;

    $query = "
      SELECT $wpdb->term_relationships.object_id 
      FROM $wpdb->terms
      INNER JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
      INNER JOIN $wpdb->term_relationships ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
      WHERE $wpdb->term_taxonomy.taxonomy = 'product_cat' 
      AND name
    ";
    wcpt_search__query( $field, $item, $query, $keyword_phrase, $keywords, $search_ids );
  }

  if( in_array( 'attribute', $filter_info['target'] ) ){

    $field = 'attribute';
    $item = null;

    $query = "
      SELECT $wpdb->term_relationships.object_id 
      FROM $wpdb->terms
      INNER JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
      INNER JOIN $wpdb->term_relationships ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
      WHERE $wpdb->term_taxonomy.taxonomy %s 
      AND name 
    ";

    // default rule items
    if( count( $attributes__default_rules ) ){
      $var = "IN ('pa_". implode("','pa_", $attributes__default_rules) ."')";
      wcpt_search__query( $field, null, sprintf( $query, $var ), $keyword_phrase, $keywords, $search_ids );
    }

    // custom rule items
    if( count($attributes__custom_rules) ){
      foreach( $attributes__custom_rules as $item ){
        $var = "= 'pa_$item'";
        wcpt_search__query( $field, $item, sprintf( $query, $var ), $keyword_phrase, $keywords, $search_ids );
      }
    }

  }

  if( in_array( 'tag', $filter_info['target'] ) ){
    $field = 'tag';
    $item = null;
    $query = "
      SELECT $wpdb->term_relationships.object_id 
      FROM $wpdb->terms
      INNER JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
      INNER JOIN $wpdb->term_relationships ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
      WHERE $wpdb->term_taxonomy.taxonomy = 'product_tag' 
      AND name 
    ";
    wcpt_search__query( $field, $item, $query, $keyword_phrase, $keywords, $search_ids );
  }

  if( in_array( 'content', $filter_info['target'] ) ){
    $field = 'content';
    $item = null;
    $query = "
      SELECT ID 
      FROM $wpdb->posts 
      WHERE post_type = 'product' 
      AND post_content 
    ";
    wcpt_search__query( $field, $item, $query, $keyword_phrase, $keywords, $search_ids );
  }

  if( in_array( 'excerpt', $filter_info['target'] ) ){
    $field = 'excerpt';
    $item = null;
    $query = "
      SELECT ID 
      FROM $wpdb->posts 
      WHERE post_type = 'product' 
      AND post_excerpt 
    ";
    wcpt_search__query( $field, $item, $query, $keyword_phrase, $keywords, $search_ids );
  }

  if( in_array( 'sku', $filter_info['target'] ) ){
    $field = 'sku';
    $item = null;
    $query = "
      SELECT post_id 
      FROM $wpdb->postmeta 
      WHERE meta_key = '_sku'
      AND meta_value 
    ";
    wcpt_search__query( $field, $item, $query, $keyword_phrase, $keywords, $search_ids );
  }

  if( in_array( 'custom_field', $filter_info['target'] ) ){
    $field = 'custom_field';
    $item = null;

    $query = "
      SELECT post_id 
      FROM $wpdb->postmeta 
      WHERE meta_key %s
      AND meta_value 
    ";

    // default rule items
    if( count($custom_fields__default_rules) ){
      $var = "IN ('". implode("','", $custom_fields__default_rules) ."')";
      wcpt_search__query( $field, null, sprintf( $query, $var ), $keyword_phrase, $keywords, $search_ids );
    }

    // custom rule items
    if( count($custom_fields__custom_rules) ){
      foreach( $custom_fields__custom_rules as $item ){
        $var = "= '$item'";
        wcpt_search__query( $field, $item, sprintf( $query, $var ), $keyword_phrase, $keywords, $search_ids );
      }
    }

  }

  wcpt_search__combine( $search_ids, $post__in );

}

// query and store post ids
function wcpt_search__query( $field, $item= null, $query, $keyword_phrase, $keywords, &$search_ids ){
  $settings = wcpt_get_settings_data();

  $permitted = array(
    'phrase_like' => true,
    'phrase_exact' => true,
    'keyword_like' => true,
    'keyword_exact' => true,
  );

  $rules = $settings['search'][$field]['rules'];

  if( $item ){
    foreach( $settings['search'][$field]['items'] as $_item ){
      if( 
        $_item['item'] == $item &&
        $_item['custom_rules_enabled']
      ){
        $rules = $_item['rules'];
      }
    }
  }

  foreach( $permitted as $key => &$val ){
    $val = $rules[$key . '_enabled'];
  }

  global $wpdb;

  if( ! empty( $item ) ){
    $search_ids[$field]['items'][$item] = array();
    $location =& $search_ids[$field]['items'][$item];

  }else{
    $location =& $search_ids[$field];

  }

  if( empty( $location ) ){
    $location = array(
      'phrase_exact' => array(),
      'phrase_like' => array(),
      'keyword_exact' => array(),
      'keyword_like' => array(),
    );
  }

  // phrase exact
  if( $permitted['phrase_exact'] ){
    $esc_keyword_phrase = esc_sql( $keyword_phrase );
    $post_ids = $wpdb->get_col( $query . " ='$esc_keyword_phrase' " );
    $location['phrase_exact'] = array_merge( $location['phrase_exact'], $post_ids );
  }

  // phrase like
  if( $permitted['phrase_like'] ){
    $esc_keyword_phrase = $wpdb->esc_like( $keyword_phrase );
    $post_ids = $wpdb->get_col( $query . " LIKE '%$esc_keyword_phrase%'" );
    $location['phrase_like'] = array_merge( $location['phrase_like'], $post_ids );
  }

  foreach( $keywords as $k=> $keyword ){
    $esc_keyword = $wpdb->esc_like( $keyword );

    // keyword exact
    if( $permitted['keyword_exact'] ){
      $post_ids = $wpdb->get_col( $query . " = '$esc_keyword'" );    
      $location['keyword_exact'] = array_merge( $location['keyword_exact'], $post_ids );
      
      // -- between
      $post_ids = $wpdb->get_col( $query . " LIKE '% $esc_keyword %'" );
      $location['keyword_exact'] = array_merge( $location['keyword_exact'], $post_ids );

      // -- starting
      $post_ids = $wpdb->get_col( $query . " LIKE '$esc_keyword %'" );
      $location['keyword_exact'] = array_merge( $location['keyword_exact'], $post_ids );

      // -- ending
      $post_ids = $wpdb->get_col( $query . " LIKE '% $esc_keyword'" );
      $location['keyword_exact'] = array_merge( $location['keyword_exact'], $post_ids );
    }

    // keyword like
    if( $permitted['keyword_like'] ){
      $post_ids = $wpdb->get_col( $query . " LIKE '%$esc_keyword%'" );
      $location['keyword_like'] = array_merge( $location['keyword_like'], $post_ids );
    }

  }

}

// combine current search ids into the query post__in 
function wcpt_search__combine( $search_ids, &$post__in ){
  // restrict to search results
  if( is_array( $search_ids ) ){

    $arr = array();
    $settings = wcpt_get_settings_data();
    $search_settings = $settings['search'];

    foreach( $search_ids as $field => $matches ){
      foreach( $matches as $match_type => $ids ){
        $rules = $search_settings[$field]['rules'];

        if( $match_type === 'items' ){
          foreach( $matches['items'] as $item => $matches ){
            $item_rules = $rules;
            // maybe use custom rules
            foreach( $search_settings[$field]['items'] as $item2 ){
              if( 
                $item2['item'] === $item &&
                ! empty( $item2['custom_rules_enabled'] )
              ){
                $item_rules = $item2['rules'];
                break;
              }
            }

            foreach( $matches as $match_type => $ids ){
              foreach( $ids as $id ){
                if( ! isset( $arr[$id] ) ){
                  $arr[$id] = 0;
                }
                $arr[$id] += $item_rules[$match_type . '_score'];
              }
            }
          }
        }else{
          foreach( $ids as $id ){
            if( ! isset( $arr[$id] ) ){
              $arr[$id] = 0;
            }
            $arr[$id] += $rules[$match_type . '_score'];
          }
        }
      }
    }

    arsort( $arr );
    $post_ids = array_keys($arr);

    if( empty( $post_ids ) ){
      $post__in = array(0);

    }else if( empty( $post__in ) ){
      $post__in = $post_ids;

    }else{
      $post__in = array_intersect( $post__in, $post_ids );
      
      if( ! count( $post__in ) ){
      // if 1 search instance fails, fail all
        $post__in = array(0);
      }

    }

  }
}