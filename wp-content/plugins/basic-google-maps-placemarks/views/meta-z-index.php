<?php do_action( self::PREFIX . 'meta-z-index-before' ); ?>

<p><?php _e( 'When two markers overlap, the marker with the higher stacking order will be on top. The Default is 0.', 'basic-google-maps-placemarks' ); ?></p>

<p>
	<label for="<?php echo self::PREFIX; ?>zIndex"><?php _e( 'Stacking Order:', 'basic-google-maps-placemarks' ); ?></label>
	<input id="<?php echo self::PREFIX; ?>zIndex" name="<?php echo self::PREFIX; ?>zIndex" type="text" size="4" value="<?php echo $zIndex; ?>" />
</p>

<?php do_action( self::PREFIX . 'meta-z-index-after' ); ?>
