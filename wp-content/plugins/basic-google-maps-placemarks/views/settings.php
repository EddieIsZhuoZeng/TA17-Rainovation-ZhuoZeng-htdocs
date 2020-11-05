<div class="wrap">
	<?php // maybe rename this so it doesn't match settings.php in the root dir ?>

	<div id="icon-options-general" class="icon32"><br /></div> <?php // @todo - why br here? use style instaed? ?>
	<h2><?php printf( __( '%s Settings', 'basic-google-maps-placemarks' ), BGMP_NAME ); ?></h2>

	<?php if ( $show_api_key_notice ) : ?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php echo wp_kses( __( '<strong>WARNING:</strong> You have not configured API keys for the Google Maps and Geocoding services.', 'basic-google-maps-placemarks' ), 'data' ); ?>
			</p>

			<p>
				<?php esc_html_e( 'This plugin often works without them, but as of June 2016, Google appears to be enforcing their API key requirement more aggressively than they have in the past. If you do not configure API keys, then the map may not work, or may stop working in the future without notice.', 'basic-google-maps-placemarks' ); ?>
			</p>

			<p>
				<?php echo wp_kses(
					sprintf(
						__( 'To obtain the keys, first <a href="%1$s">get a Maps API Standard/Browser key</a>, and then <a href="%2$s">get a Geocoding API Standard/Server key</a>. Paste both keys into the fields below.', 'basic-google-maps-placemarks' ),
						'https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key',
						'https://developers.google.com/maps/documentation/geocoding/get-api-key#get-an-api-key'
					),
				'data' ); ?>
			</p>
		</div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php do_action( BasicGoogleMapsPlacemarks::PREFIX . 'settings-before' ); ?>

		<?php // @todo add nonce for settings? ?>

		<div id="<?php echo BasicGoogleMapsPlacemarks::PREFIX; ?>settings-fields">
			<?php settings_fields( BasicGoogleMapsPlacemarks::PREFIX . 'settings' ); ?>
			<?php do_settings_sections( BasicGoogleMapsPlacemarks::PREFIX . 'settings' ); ?>

			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
			</p>
		</div> <!-- /#<?php echo BasicGoogleMapsPlacemarks::PREFIX; ?>settings-fields -->

		<div id="<?php echo BasicGoogleMapsPlacemarks::PREFIX; ?>settings-meta-boxes" class="metabox-holder">
			<div class="postbox-container">
				<?php do_meta_boxes( 'settings_page_' . BasicGoogleMapsPlacemarks::PREFIX . 'settings', 'side', null ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce' ); ?>
			</div>
		</div>

		<?php do_action( BasicGoogleMapsPlacemarks::PREFIX . 'settings-after' ); ?>
	</form>
</div> <!-- .wrap -->
