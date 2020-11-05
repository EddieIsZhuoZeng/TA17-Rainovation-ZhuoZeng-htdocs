<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( empty( $color ) ){
  $color = 'black';
}

if( empty( $shape ) ){
  $shape = 'circle';
}

if( empty( $size ) ){
  $dim = '';
}else{
  $dim = ' width:' . $size . '; height:' . $size . ';';
}

if( empty( $tooltip ) ){
  $tooltip = '';
}

?>
<span class="wcpt-color wcpt-tooltip-parent wcpt-shape-<?php echo $shape; ?>" style="background: <?php echo $color; ?>; <?php echo $dim; ?>">
  <span class="wcpt-tooltip"><?php echo $tooltip; ?></span>
</span>
