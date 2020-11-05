<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( isset( $gap ) ){
  $style =" style='height:$gap;' ";
}
?>
<span class="wcpt-clear" <?php if( ! empty( $style ) ) echo $style; ?>></span>
