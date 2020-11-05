<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<thead>
	<?php
	$headings_mkp = '';
	$hide_headings = true;
	if( ! empty( $columns ) ){
		foreach( $columns as $col_index => $column ){
			$GLOBALS['wcpt_col_index'] = $col_index;
			wcpt_parse_style_2($column['heading']);
			$col_id = 'wcpt-' . $column['heading']['id'];
			$curr_heading_mkp = wcpt_parse_2($column['heading']['content']);
			if( $curr_heading_mkp ){
				$hide_headings = false;
			}
			$headings_mkp .= '<th class="wcpt-heading ' . $col_id . '">' . $curr_heading_mkp . '</th>';
		}
	}
	?>
	<tr class="wcpt-heading-row <?php echo $hide_headings ? 'wcpt-hide' : ''; ?>"><?php echo $headings_mkp; ?></tr>
</thead>
<?php
