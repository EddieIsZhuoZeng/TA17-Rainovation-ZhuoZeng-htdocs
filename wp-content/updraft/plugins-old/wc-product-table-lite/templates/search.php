<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

++$GLOBALS['wcpt_search_count'];

$keyword = '';
$table_id = $GLOBALS['wcpt_table_data']['id'];
$param = $table_id . '_search_' . $GLOBALS['wcpt_search_count'];
$char_limit = 70;

if( in_array($target, array('title', 'title+content')) ){

	// pre-selected
	if( $pre_selected = wcpt_get_nav_filter( 'search' ) ){
		if( empty( $_GET[$table_id . '_filtered'] ) ){
			// apply
			$keyword = $_GET[$param] = $_REQUEST[$param] = $pre_selected['keyword'];
		}else{
			// remove
			wcpt_clear_nav_filter( 'search' );
		}
	}

}

if( ! empty( $_GET[ $param ] ) ){
	$keyword =  substr( stripslashes( $_GET[ $param ] ), 0, $char_limit );

	$filter_info = array(
		'filter'    		=> 'search',
    'values'      	=> array( $keyword ),
		'match_type' 		=> isset( $match_type ) ? $match_type : 'LIKE',
		'searches' => array(
			array(
				'keyword' => $keyword,
				'target'	=> $target,
				'custom_fields'	=> empty( $custom_fields ) ? array() : $custom_fields,
				'attributes'		=> empty( $attributes ) ? array() : $attributes,
				'keyword_separator'	=> isset( $keyword_separator ) ? $keyword_separator : ' ',
			)
		)
	);

	if( $prev_search = wcpt_get_nav_filter( 'search' ) ){
		$filter_info['searches'] = array_merge( $filter_info['searches'], $prev_search['searches'] );
	}

	if( ! empty( $clear_label ) ){
		$filter_info['clear_labels_2'] = array(
			$keyword => str_replace( '[kw]', htmlentities( $keyword ), $clear_label ),
		);

	}else{
		$filter_info['clear_labels_2'] = array(
			$keyword => __('Search') . ' : ' . htmlentities( $keyword ),
		);
	}

	$single = false;

	wcpt_update_user_filters( $filter_info, $single );
}

$search_label = '';
$placeholder = ! empty( $placeholder ) ? wcpt_parse_2( $placeholder ) : __( 'Search', 'wcpt' );

?>
<div class="wcpt-search-wrapper">
	<div
		class="wcpt-search <?php if( ! empty( $keyword ) ) echo 'wcpt-active'; echo $html_class; ?>"
		data-wcpt-table-id="<?php echo $GLOBALS['wcpt_table_data']['id']; ?>"
	>

	  <!-- input -->
	  <input
	    class="wcpt-search-input"
	    type="search"
			name="<?php echo $param; ?>"
			data-wcpt-value="<?php echo htmlentities($keyword); ?>"
	    placeholder="<?php echo $placeholder; ?>" value="<?php echo htmlentities($keyword); ?>"
	    autocomplete="off"
	  />

	  <!-- submit -->
	  <span class="wcpt-search-submit">
			<?php wcpt_icon('search', 'wcpt-search-submit-icon'); ?>
	  </span>

	  <!-- clear -->
	  <?php if( ! empty( $keyword ) ) { ?>
	    <a href="javascript:void(0)" class="wcpt-search-clear">
				<?php wcpt_icon('x', 'wcpt-search-clear-icon'); ?>
	    </a>
	  <?php } ?>

	</div>
</div>
