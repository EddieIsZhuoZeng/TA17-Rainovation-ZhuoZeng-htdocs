<div id="tabs-marker-separation" class="ui-tabs-panel">
	<h3><?php _e('Near-Vicinity Marker Control Settings', 'wp-google-maps'); ?></h3>
	
	<p>
		<?php
		_e('This feature will group nearby or overlapping markers together using a placeholder marker. When the placeholder marker is clicked, the group will open, separating the markers on screen. This is intended for when you have several markers at the same address but would like the user to be able to view data from all the markers.', 'wp-google-maps');
		?>
	</p>
	
	<table class="form-table">
		<tr>
			<td width='400' valign='top'>
				<?php
				_e("Enable Near-Vicinity Marker Control","wp-google-maps");
				?>
			</td>
			<td>
				<div class='switch'>
					<input 
						name='wpgmza_near_vicinity_control_enabled' 
						type='checkbox' 
						class='cmn-toggle cmn-toggle-yes-no' 
						id='wpgmza_near_vicinity_control_enabled' 
						value='yes'/>
					<label 
						for='wpgmza_near_vicinity_control_enabled' 
						data-on='<?php _e("Yes", "wp-google-maps"); ?>' 
						data-off='<?php _e("No", "wp-google-maps"); ?>'></label>
				</div>
			</td>
		</tr>
		
		<tr>
			<td width='400' valign='top'>
				<?php
				_e("Near-Vicinity Affected Radius","wp-google-maps");
				?>
			</td>
			<td>
				<input 
					name='wpgmza_near_vicinity_aff_radius' 
					id='wpgmza_near_vicinity_aff_radius' 
					placeholder='50'
					type="number"
					min="1"
					step="1"
					value="50"
					/>
				<?php
				_e("Meters", "wp-google-maps");
				?>
				<br/>
				<p>
					<small>
						<?php
						_e('Markers within this threshold of one another will be grouped together', 'wp-google-maps');
						?>
					</small>
				</p>
			</td>
		</tr>
		
		<tr>
			<td width='400' valign='top'>
				<?php
				_e("Use Legacy Near-Vicinity Module","wp-google-maps");
				?>
			</td>
			<td>
				<div class='switch'>
					<input 
						name='marker_separator_use_legacy_module' 
						type='checkbox' 
						class='cmn-toggle cmn-toggle-yes-no' 
						id='marker_separator_use_legacy_module' 
						value='yes'/>
					<label 
						for='marker_separator_use_legacy_module' 
						data-on='<?php _e("Yes", "wp-google-maps"); ?>' 
						data-off='<?php _e("No", "wp-google-maps"); ?>'></label>
				</div>
			</td>
		</tr>
		
		<tr class="wpgmza-marker-separator-modern-setting">
			<td width='400' valign='top'>
				<?php
				_e("Placeholder Marker", "wp-google-maps");
				?>
			</td>
			<td>
				<div id="marker-separator-placeholder-icon-picker-container">
				</div>
			</td>
		</tr>
		
		<tr class="wpgmza-marker-separator-modern-setting">
			<td width='400' valign='top'>
				<?php
				_e("Near-Vicinity Shape", "wp-google-maps");
				?>
			</td>
			<td>
				<select name="marker_separator_algorithm">
					<option value="circle">
						<?php _e('Circle', 'wp-google-maps'); ?>
					</option>
					<option value="spiral">
						<?php _e('Spiral', 'wp-google-maps'); ?>
					</option>
					<option value="hexagon">
						<?php _e('Hexagon', 'wp-google-maps'); ?>
					</option>
					<option value="line">
						<?php _e('Line', 'wp-google-maps'); ?>
					</option>
					<option value="grid">
						<?php _e('Grid', 'wp-google-maps'); ?>
					</option>
				</select>
			</td>
		</tr>
		
		<tr class="wpgmza-marker-separator-modern-setting">
			<td width='400' valign='top'>
				<?php
				_e("Animate Separation", "wp-google-maps");
				?>
			</td>
			<td>
				<div class='switch'>
					<input 
						id="marker_separator_animate"
						name='marker_separator_animate' 
						type='checkbox' 
						class='cmn-toggle cmn-toggle-yes-no'/>
					<label
						for='marker_separator_animate' 
						data-on='<?php _e("Yes", "wp-google-maps"); ?>'
						data-off='<?php _e("No", "wp-google-maps"); ?>'></label>
				</div>
			</td>
		</tr>
		
		<tr class="wpgmza-marker-separator-modern-setting">
			<td width='400' valign='top'>
				<?php
				_e("Animation Duration", "wp-google-maps");
				?>
			</td>
			<td>
				<div class='switch'>
					<input 
						name='marker_separator_animation_duration' 
						type="number"
						value="0.5"
						min="0"
						step="0.01"/>
					<?php
					_e('Seconds', 'wp-google-maps');
					?>
				</div>
			</td>
		</tr>
		
		<!--<tr class="wpgmza-marker-separator-modern-settings">
			<td width='400' valign='top'>
				<?php
				_e("Animation Easing", "wp-google-maps");
				?>
			</td>
			<td>
				<select name="marker_separation_easing">
					<option value="linear"><?php _e('Linear', 'wp-google-maps'); ?></option>
					<option value="easeInSine"><?php _e('Sine In', 'wp-google-maps'); ?></option>
					<option value="easeOutSine"><?php _e('Sine Out', 'wp-google-maps'); ?></option>
					<option value="easeInOutSine"><?php _e('Sine In &amp; Out', 'wp-google-maps'); ?></option>
					<option value="easeInQuad"><?php _e('Quad In', 'wp-google-maps'); ?></option>
					<option value="easeOutQuad"><?php _e('Quad Out', 'wp-google-maps'); ?></option>
					<option value="easeInOutQuad"><?php _e('Quad In &amp; Out', 'wp-google-maps'); ?></option>
					<option value="easeInCubic"><?php _e('Cubic In', 'wp-google-maps'); ?></option>
					<option value="easeOutCubic"><?php _e('Cubic Out', 'wp-google-maps'); ?></option>
					<option value="easeInOutCubic"><?php _e('Cubic In &amp; Out', 'wp-google-maps'); ?></option>
					<option value="easeInQuart"><?php _e('Quartic In', 'wp-google-maps'); ?></option>
					<option value="easeOutQuart"><?php _e('Quartic Out', 'wp-google-maps'); ?></option>
					<option value="easeInOutQuart"><?php _e('Quartic In &amp; Out', 'wp-google-maps'); ?></option>
					<option value="easeInQuint"><?php _e('Quintic In', 'wp-google-maps'); ?></option>
					<option value="easeOutQuint"><?php _e('Quintic Out', 'wp-google-maps'); ?></option>
					<option value="easeInOutQuint"><?php _e('Quintic In &amp; Out', 'wp-google-maps'); ?></option>
					<option value="easeInExpo"><?php _e('Exponential In', 'wp-google-maps'); ?></option>
					<option value="easeOutExpo"><?php _e('Exponential Out', 'wp-google-maps'); ?></option>
					<option value="easeInOutExpo"><?php _e('Exponential In &amp; Out', 'wp-google-maps'); ?></option>
					<option value="easeInCirc"><?php _e('Circular In', 'wp-google-maps'); ?></option>
					<option value="easeOutCirc"><?php _e('Circular Out', 'wp-google-maps'); ?></option>
					<option value="easeInOutCirc"><?php _e('Circular In &amp; Out', 'wp-google-maps'); ?></option>
					<option value="easeInBack"><?php _e('Back In', 'wp-google-maps'); ?></option>
					<option value="easeOutBack"><?php _e('Back Out', 'wp-google-maps'); ?></option>
					<option value="easeInOutBack"><?php _e('Back In &amp; Out', 'wp-google-maps'); ?></option>
					<option value="easeInElastic"><?php _e('Elastic In', 'wp-google-maps'); ?></option>
					<option value="easeOutElastic"><?php _e('Elastic Out', 'wp-google-maps'); ?></option>
					<option value="easeInOutElastic"><?php _e('Elastic In &amp; Out', 'wp-google-maps'); ?></option>
					<option value="easeInBounce"><?php _e('Bounce In', 'wp-google-maps'); ?></option>
					<option value="easeOutBounce"><?php _e('Bounce Out', 'wp-google-maps'); ?></option>
					<option value="easeInOutBounce"><?php _e('Bounce In &amp; Out', 'wp-google-maps'); ?></option>
				</select>
			</td>
		</tr>-->
		
		<tr class="wpgmza-marker-separator-modern-setting">
			<td width='400' valign='top'>
				<?php
				_e("Stagger Animation", "wp-google-maps");
				?>
			</td>
			<td>
				<div class='switch'>
					<input 
						id='marker_separator_stagger_animation' 
						name='marker_separator_stagger_animation' 
						type='checkbox' 
						class='cmn-toggle cmn-toggle-yes-no'/>
					<label
						for='marker_separator_stagger_animation' 
						data-on='<?php _e("Yes", "wp-google-maps"); ?>'
						data-off='<?php _e("No", "wp-google-maps"); ?>'></label>
				</div>
			</td>
		</tr>
		
		<tr class="wpgmza-marker-separator-modern-setting">
			<td width='400' valign='top'>
				<?php
				_e("Stagger Interval", "wp-google-maps");
				?>
			</td>
			<td>
				<div class='switch'>
					<input 
						name='marker_separator_stagger_interval' 
						type="number"
						value="0.05"
						min="0"
						step="0.01"/>
					<?php
					_e('Seconds', 'wp-google-maps');
					?>
				</div>
			</td>
		</tr>
		
		<tr class="wpgmza-marker-separator-legacy-setting">
			<td width='400' valign='top'>
				<?php
				_e("Near-Vicinity Shape","wp-google-maps");
				?>
			</td>
			<td>
				<div class='switch'>
					<input 
						name='wpgmza_near_vicinity_shape' 
						type='checkbox' 
						class='cmn-toggle cmn-toggle-yes-no' 
						id='wpgmza_near_vicinity_shape' 
						value='yes'/>
					<label
						for='wpgmza_near_vicinity_shape' 
						data-on='<?php _e("Spiral", "wp-google-maps"); ?>'
						data-off='<?php _e("Circle", "wp-google-maps"); ?>'></label>
				</div>
			</td>
		</tr>
		
		<tr class="wpgmza-marker-separator-legacy-setting">
			<td width='400' valign='top'>
				<?php _e("Near-Vicinity Hide Lines","wp-google-maps"); ?>
			</td>
			<td>
				<div class='switch'>
					<input 
						name='wpgmza_near_vicinity_hide_line' 
						type='checkbox' 
						class='cmn-toggle cmn-toggle-yes-no' 
						id='wpgmza_near_vicinity_hide_line' 
						value='yes'/>
					<label 
						for='wpgmza_near_vicinity_hide_line' 
						data-on='<?php _e("Yes", "wp-google-maps"); ?>'
						data-off='<?php _e("No", "wp-google-maps"); ?>'></label>
				</div>
			</td>
		</tr>
		
		<tr class="wpgmza-marker-separator-legacy-setting">
			<td width='400' valign='top'>
				<?php _e("Near-Vicinity Line Color","wp-google-maps"); ?>
			</td>
			<td>
				<input 
					name='wpgmza_near_vicinity_line_col' 
					type='color' 
					id='wpgmza_near_vicinity_line_col' 
					placeholder='#000000'/>
			</td>
		</tr>
		
		<tr class="wpgmza-marker-separator-legacy-setting">
			<td width='400' valign='top'>
				<?php _e("Near-Vicinity Line Opacity","wp-google-maps"); ?>
			</td>
			<td>
				<input 
					name='wpgmza_near_vicinity_line_opacity' 
					type='number' 
					min='0'
					max='1'
					step='0.01'
					id='wpgmza_near_vicinity_line_opacity' 
					placeholder='1.0'/> 
				<?php _e("Value between 0.1 and 1.0", "wp-google-maps"); ?>
			</td>
		</tr>
		
		<tr class="wpgmza-marker-separator-legacy-setting">
			<td width='400' valign='top'>
				<?php _e("Near-Vicinity Line Thinkness","wp-google-maps"); ?>
			</td>
			<td>
				<input 
					name='wpgmza_near_vicinity_line_thickness' 
					type='number'
					min='1'
					step='1'
					id='wpgmza_near_vicinity_line_thickness' 
					placeholder='1'/>
				<?php _e("Value between 1 and 50", "wp-google-maps"); ?>
			</td>
		</tr>
		
		<tr>
			<td>
				<?php
				esc_html_e("Maximum group size", "wp-google-maps");
				?>
			</td>
			<td>
				<input
					name="marker_separator_maximum_group_size"
					type="number"
					min="2"
					max="64"
					step="1"
					value="16"
					/>
				<p>
					<small>
						<?php
						esc_html_e("We recommend using a group size no larger than the maximum number of overlapping markers you anticipate, and that you do not use a higher number than necessary. Setting this too high may result in unnecessarily increased load times.", "wp-google-maps");
						?>
					</small>
				</p>
			</td>
		</tr>
	</table>	
</div>