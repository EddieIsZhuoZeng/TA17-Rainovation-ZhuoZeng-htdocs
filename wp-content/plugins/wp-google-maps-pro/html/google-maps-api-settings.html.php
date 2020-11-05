<tr>
	<td>
		<label><?php _e('Use Google Maps API:', 'wp-google-maps'); ?></label>
	</td>
	<td>
		<select name="wpgmza_api_version">
			<option value="3.exp">
				<?php
				_e('3.exp (Experimental)', 'wp-google-maps');
				?>
			</option>
			<option value="3.31">
				<?php
				_e('3.31', 'wp-google-maps');
				?>
			</option>
			<option value="3.30">
				<?php
				_e('3.30 (Retired)', 'wp-google-maps');
				?>
			</option>
		</select>
	</td>
</tr>
<tr>
	<td>
		<label><?php _e('Load Google Maps API:', 'wp-google-maps'); ?></label>
	</td>
	<td>
		<select name="wpgmza_load_engine_api_condition">
			<option value="where-required">
				<?php
				_e('Where required', 'wp-google-maps');
				?>
			</option>
			<option value="always">
				<?php
				_e('Always', 'wp-google-maps');
				?>
			</option>
			<option value="only-front-end">
				<?php
				_e('Only Front End', 'wp-google-maps');
				?>
			</option>
			<option value="only-back-end">
				<?php
				_e('Only Back End', 'wp-google-maps');
				?>
			</option>
			<option value="never">
				<?php
				_e('Never', 'wp-google-maps');
				?>
			</option>
		</select>
	</td>
</tr>
<tr>
	<td>
		<label><?php _e('Always include Google Maps API on pages:'); ?></label>
	</td>
	<td>
		<input name="wpgmza_always_include_engine_api_on_pages" placeholder="<?php _e('Page IDs'); ?>" pattern="\d+(,\s*\d+)*"/>
	</td>
</tr>
<tr>
	<td>
		<label><?php _e('Always exclude Google Maps API on pages:'); ?></label>
	</td>
	<td>
		<input name="wpgmza_always_exclude_engine_api_on_pages" placeholder="<?php _e('Page IDs'); ?>" pattern="\d+(,\s*\d+)*"/>
	</td>
</tr>