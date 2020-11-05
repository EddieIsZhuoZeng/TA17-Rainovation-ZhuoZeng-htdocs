<div>
	<h3>
		<?php
		esc_html_e('Live Tracking Settings', 'wp-google-maps');
		?>
	</h3>
	
	<p>
		<?php
		esc_html_e('Please use the setting below to control Live Tracking broadcasting.', 'wp-google-maps');
		?>
	</p>
	
	<p>
		<?php
		esc_html_e('Please note this does not affect recording - you can always record your live location and polyline routes, this setting enables visitors to your site to receive your updated location as they view your pages.', 'wp-google-maps');
		?>
	</p>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Enable Broadcasting', 'wp-google-maps');
			?>
		</label>
		<div class="switch">
			<input 
				id="enable_live_tracking" 
				name="enable_live_tracking" 
				class="cmn-toggle cmn-toggle-yes-no"
				type="checkbox"/> 
				
			<label
				data-on="<?php esc_html_e('Yes', 'wp-google-maps'); ?>" 
				data-off="<?php esc_html_e('No', 'wp-google-maps'); ?>"
				for="enable_live_tracking"></label>
		</div>
	</fieldset>
	
	<h3>
		<?php
		esc_html_e('Live Tracking Devices', 'wp-google-maps');
		?>
		
		<i id="wpgmza-refresh-live-tracking-devices" class="fa fa-refresh" aria-hidden="true"></i>
	</h3>
	
	<p>
		<?php
		esc_html_e('Devices which have attempted to pair with your site will appear here. You must approve devices before they will appear on the map.', 'wp-google-maps');
		?>
	</p>
	
	<table id="wpgmza-live-tracking-devices" class="wp-list-table widefat fixed wpgmza-listing">
		<thead>
			<tr>
				<td>
					<?php
					esc_html_e('Device ID', 'wp-google-maps');
					?>
				</td>
				<td>
					<?php
					esc_html_e('Name', 'wp-google-maps');
					?>
				</td>
				<td>
					<?php
					esc_html_e('Draw Polylines', 'wp-google-maps');
					?>
				</td>
				<td>
					<?php
					esc_html_e('Line Color and Weight', 'wp-google-maps');
					?>
				</td>
				<td>
					<?php
					esc_html_e('Approved', 'wp-google-maps');
					?>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td data-name="deviceID"></td>
				<td data-name="name"></td>
				<td>
					<input data-ajax-name="drawPolylines" type="checkbox"/>
				</td>
				<td>
					<input data-ajax-name="polylineColor" type="color"/>
					<input data-ajax-name="polylineWeight" type="number" min="1" max="50"/>
				</td>
				<td>
					<input data-ajax-name="approved" type="checkbox"/>
					
					<input type="hidden" data-ajax-name="id"/>
				</td>
			</tr>
		</tbody>
	</table>
	
	<!--<h3>
		<?php
		esc_html_e('Settings', 'wp-google-maps');
		?>
	</h3>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('New Polyline Distance Threshold (meters)', 'wp-google-maps');
			?>
		</label>
		<input name="live_tracking_new_polyline_distance_threshold" type="number" min="1" value="50"/>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('New Polyline Time Threshold (minutes)', 'wp-google-maps');
			?>
		</label>
		<input name="live_tracking_new_polyline_distance_threshold" type="number" min="1" value="60"/>
	</fieldset>-->
</div>