<?php if ( $field['label_for'] == BasicGoogleMapsPlacemarks::PREFIX . 'marker-clustering' ) : ?>

	<input
		id="<?php esc_attr_e( BasicGoogleMapsPlacemarks::PREFIX ); ?>marker-clustering"
	    name="<?php esc_attr_e( BasicGoogleMapsPlacemarks::PREFIX ); ?>marker-clustering"
		type="checkbox" <?php echo checked( $this->markerClustering, 'on', false ); ?>
	/>
	<label for="<?php esc_attr_e( BasicGoogleMapsPlacemarks::PREFIX ); ?>marker-clustering">
		<?php esc_html_e( ' Enable marker clustering', 'basic-google-maps-placemarks' ); ?>
	</label>

<?php elseif ( $field['label_for'] == BasicGoogleMapsPlacemarks::PREFIX . 'cluster-max-zoom' ) : ?>

	<input
		id="<?php esc_attr_e( BasicGoogleMapsPlacemarks::PREFIX ); ?>cluster-max-zoom"
		name="<?php esc_attr_e( BasicGoogleMapsPlacemarks::PREFIX ); ?>cluster-max-zoom"
		type="text"
		value="<?php esc_attr_e( $this->clusterMaxZoom ); ?>"
		class="small-text"
	/>
	<?php printf( __( '%d (farthest) to %d (closest)', 'basic-google-maps-placemarks' ), BasicGoogleMapsPlacemarks::ZOOM_MIN, BasicGoogleMapsPlacemarks::ZOOM_MAX ); ?>
	<p class="description">
		<?php esc_html_e( 'When the maximum zoom level is reached, all markers will be shown without clustering.', 'basic-google-maps-placemarks' ); ?>
	</p>

<?php elseif ( $field['label_for'] == BasicGoogleMapsPlacemarks::PREFIX . 'cluster-grid-size' ) : ?>

	<input
		id="<?php esc_attr_e( BasicGoogleMapsPlacemarks::PREFIX ); ?>cluster-grid-size"
		name="<?php esc_attr_e( BasicGoogleMapsPlacemarks::PREFIX ); ?>cluster-grid-size"
		type="text"
		value="<?php esc_attr_e( $this->clusterGridSize ); ?>"
		class="small-text"
	/>
	<p class="description">
		<?php esc_html_e( 'The grid size of a cluster, in pixels. Each cluster will be a square. Larger grids can be rendered faster.', 'basic-google-maps-placemarks' ); ?>
	</p>

<?php elseif ( $field['label_for'] == BasicGoogleMapsPlacemarks::PREFIX . 'cluster-style' ) : ?>

	<select id="<?php esc_attr_e( BasicGoogleMapsPlacemarks::PREFIX ); ?>cluster-style" name="<?php esc_attr_e( BasicGoogleMapsPlacemarks::PREFIX ); ?>cluster-style">
		<option value="default" <?php echo selected( $this->clusterStyle, 'default', false ); ?> >
			<?php esc_html_e( 'Default', 'basic-google-maps-placemarks' ); ?>
		</option>

		<option value="people" <?php echo selected( $this->clusterStyle, 'people', false ); ?> >
			<?php esc_html_e( 'People', 'basic-google-maps-placemarks' ); ?>
		</option>

		<option value="hearts" <?php echo selected( $this->clusterStyle, 'hearts', false ); ?> >
			<?php esc_html_e( 'Hearts', 'basic-google-maps-placemarks' ); ?>
		</option>

		<option value="conversation" <?php echo selected( $this->clusterStyle, 'conversation', false ); ?> >
			<?php esc_html_e( 'Conversation', 'basic-google-maps-placemarks' ); ?>
		</option>
	</select>

<?php endif; ?>
