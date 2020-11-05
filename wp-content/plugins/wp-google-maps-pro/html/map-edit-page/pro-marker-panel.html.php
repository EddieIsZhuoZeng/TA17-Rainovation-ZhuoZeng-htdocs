<div class="wpgmza-marker-panel">
	<div class="wpgmza-panel-preloader"></div>
	<h2>
		<?php
		esc_html_e('Add a Marker', 'wp-google-maps');
		?>
	</h2>
	
	<input data-ajax-name="id" type="hidden" value="-1"/>
	<input data-ajax-name="map_id" type="hidden" value="-1"/>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Title', 'wp-google-maps');
			?>
		</label>
		<input type="text" data-ajax-name="title" placeholder="<?php _e('Title', 'wp-google-maps'); ?>"/>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Address/GPS', 'wp-google-maps');
			?>
		</label>
		<div class="wpgmza-input-button__line">
			<input type="text" data-ajax-name="address"/>
			<button 
				type="button"
				title="<?php esc_html_e('Extract address from picture', 'wp-google-maps'); ?>"
				class="wpgmza-get-location-from-picture button-secondary" 
				data-source="[data-picture-url]"
				data-destination="[data-ajax-name='address']"
				data-destination-lat="[data-ajax-name='lat']"
				data-destination-lng="[data-ajax-name='lng']"
				>
				<i class="fa fa-file-image-o" aria-hidden="true"></i>
			</button>
		</div>
		
		<input data-ajax-name="lat" type="hidden"/>
		<input data-ajax-name="lng" type="hidden"/>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Description', 'wp-google-maps');
			?>
		</label>
		<?php
		wp_editor('', 'wpgmza-description-editor', array(
			'teeny' 			=> false,
			'media_buttons'		=> true,
			'textarea_name'		=> 'wpgmza-description',
			'textarea_rows'		=> 5
		));
		?>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Gallery', 'wp-google-maps');
			?>
		</label>
		<div class="wpgmza-marker-gallery-input-container">
			<input data-ajax-name="gallery"/>
		</div>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Link URL', 'wp-google-maps');
			?>
		</label>
		<input data-ajax-name="link"/>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Custom Marker', 'wp-google-maps');
			?>
		</label>
		<div>
		
			<?php
			
			$markerIconPicker = new \WPGMZA\MarkerIconPicker(array(
				'ajaxName' => 'icon'
			));
			echo $markerIconPicker->html;
			
			?>
		
		</div>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Category', 'wp-google-maps');
			?>
		</label>
		<div class="wpgmza-category-picker-container"></div>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Animation', 'wp-google-maps');
			?>
		</label>
		<select data-ajax-name="anim">
			<option value="0">
				<?php
				esc_html_e('None', 'wp-google-maps');
				?>
			</option>
			<option value="1">
				<?php
				esc_html_e('Bounce', 'wp-google-maps');
				?>
			</option>
			<option value="2">
				<?php
				esc_html_e('Drop', 'wp-google-maps');
				?>
			</option>
		</select>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('InfoWindow open by default', 'wp-google-maps');
			?>
		</label>
		<select data-ajax-name="infoopen">
			<option value="0">
				<?php
				esc_html_e('No', 'wp-google-maps');
				?>
			</option>
			<option value="1">
				<?php
				esc_html_e('Yes', 'wp-google-maps');
				?>
			</option>
		</select>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Display on front end', 'wp-google-maps');
			?>
		</label>
		<select data-ajax-name="approved">
			<option value="1">
				<?php
				esc_html_e('Yes', 'wp-google-maps');
				?>
			</option>
			<option value="0">
				<?php
				esc_html_e('No', 'wp-google-maps');
				?>
			</option>
		</select>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Sticky', 'wp-google-maps');
			?>
		</label>
		<div>
			<input data-ajax-name="sticky" type="checkbox"/>
			<small>
				<?php
				esc_html_e('Always on top in Marker Listings', 'wp-google-maps');
				?>
			</small>
		</div>
	</fieldset>
	
	<fieldset class="wpgmza-save-marker-container">
		<label></label>
		<button type="button" class="button button-primary wpgmza-save-marker">
			<?php
			esc_html_e('Add Marker', 'wp-google-maps');
			?>
		</button>
	</fieldset>
	
</div>