<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wcpt_settings = wcpt_get_settings_data();
$settings = $wcpt_settings['modals'];

$labels =& $settings['labels'];
$locale = get_locale();

$strings = array();

if( ! empty( $labels ) ){
	foreach( $labels as $key => $translations ){
		$strings[$key] = array();
		$translations = preg_split ('/$\R?^/m', $translations);
		foreach( $translations as $translation ){
			$array = explode( ':', $translation );
			$strings[$key][ trim( $array[0] ) ] = trim( $array[1] );
		}
	}
}


$style =& $settings['style'];
?>
<div class="wcpt-nav-modal-tpl">
  <style media="screen">
    .wcpt-nm-apply {
      background-color: <?php echo $style['.wcpt-nm-apply']['background-color']; ?>
    }
  </style>
  <div class="wcpt-nav-modal" data-wcpt-table-id="<?php echo $GLOBALS['wcpt_table_data']['id']; ?>">
    <div class="wcpt-nm-content wcpt-noselect">
      <div class="wcpt-nm-heading">
				<a class="wcpt-nm-close" href="javascript:void(0)">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-reactid="271"><polyline points="15 18 9 12 15 6"></polyline>
					</svg>
				</a>
        <span class="wcpt-nm-heading-text wcpt-on-filters-show">
          <?php echo ! empty( $strings['filters'][$locale] ) ? $strings['filters'][$locale] : __('Filters', 'wc-product-table'); ?>
        </span>
        <span class="wcpt-nm-heading-text wcpt-on-sort-show">
          <?php echo ! empty( $strings['sort'][$locale] ) ? $strings['sort'][$locale] : __('Sort', 'wc-product-table'); ?>
        </span>
				<div class="wcpt-nm-action">
					<a href="javascript:void(0)" class="wcpt-nm-reset">
						<?php echo ( ! empty( $strings['reset'] ) && ! empty( $strings['reset'][$locale] ) ) ?  $strings['reset'][$locale] : __('Reset', 'wc-product-table'); ?>
					</a>
	        <a href="javascript:void(0)" class="wcpt-nm-apply">
						<?php echo ( ! empty( $strings['apply'] ) && ! empty( $strings['apply'][$locale] ) ) ?  $strings['apply'][$locale] : __('Apply', 'wc-product-table'); ?>
					</a>
				</div>
      </div>
      <div class="wcpt-navigation wcpt-left-sidebar">
        <div class="wcpt-nm-filters">
          <span class="wcpt-nm-filters-placeholder"></span>
        </div>
        <div class="wcpt-nm-sort">
          <span class="wcpt-nm-sort-placeholder"></span>
        </div>
      </div>
    </div>
  </div>
</div>
