<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wcpt_settings = wcpt_get_settings_data();
$string = '';
if( ! empty( $wcpt_settings['no_results'] ) ){
	$settings = $wcpt_settings['no_results'];

	$label =& $settings['label'];
	$locale = get_locale();

	$translations = preg_split ('/$\R?^/m', $label);

	foreach( $translations as $translation ){
		$array = explode( ':', $translation );
		if( trim( $array[0] ) == $locale ){
			$string = trim( $array[1] );
		}
	}

	if( ! $string ){
		$string = $label;
	}

	if( ! $string ){
		$string = '<br>No results found. <br><a href="." class="wcpt-clear-filters">Clear filters and try again</a>?';
	}
}

?>
<div class="wcpt-no-results  wcpt-device-<?php echo $device; ?>">
	<?php echo str_replace(array( '[link]', '[/link]' ), array( '<a href="." class="wcpt-clear-filters">', '</a>' ), $string); ?>
</div>
