<?php
/**
 * The WP Cloudy Theme 1 template
 *
 */
?>
<div class="wpc-loading-spinner" style="display:none">
	<img src="<?php echo plugins_url( 'img/ajax-loader.gif', dirname(__FILE__)); ?>" alt="loader"/>
</div>

<!-- Start #wpc-weather -->
<?php echo $wpc_html_container_start; ?>

	<div class="top">
		<!-- Geolocation Add-on -->
		<?php echo $wpc_html_geolocation; ?>
		<?php echo $wpc_html_today_temp_day; ?>
		<?php echo $wpc_html_now_location_name; ?>
	</div>
	
	<!-- Current weather -->
	<?php echo $wpc_html_now_start; ?>
	
		<?php echo $wpc_html_display_now_time_symbol; ?>
		
		<?php echo $wpc_html_display_now_time_temperature; ?>
		<?php echo $wpc_html_weather; ?>
		<?php if ($wpc_html_today_temp_day || $wpc_html_infos_wind || $wpc_html_infos_humidity || $wpc_html_infos_pressure || $wpc_html_infos_cloudiness || $wpc_html_today_sun ) { ?>
			<button class="wpc-btn-toggle-infos">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</button>
		<?php } ?>
	<?php echo $wpc_html_now_end; ?>
	
	<div class="wpc-toggle-infos">
		<!-- Current infos: wind, humidity, pressure, cloudiness, precipitation -->
		<div class="infos">
			<div class="wpc-flexslider">
				<ul class="wpc-slides">
					<?php if ( $wpc_html_infos_wind || $wpc_html_infos_humidity || $wpc_html_infos_pressure || $wpc_html_infos_cloudiness ) { ?>
					<li>
						<?php echo $wpc_html_infos_wind; ?>
						<?php echo $wpc_html_infos_humidity; ?>
						<?php echo $wpc_html_infos_pressure; ?>
						<?php echo $wpc_html_infos_cloudiness; ?>
					</li>
					<?php } ?>
					<?php if ( $wpc_html_today_sun || $wpc_html_infos_precipitation ) { ?>
					<li>
						<?php echo $wpc_html_infos_precipitation; ?>
						<?php echo $wpc_html_today_sun; ?>	
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	
		<?php if ($wpc_html_hour) { ?>
			<!-- Hourly Forecast -->
			<?php echo $wpc_html_hour_start; ?>
			<?php
				if( $wpcloudy_hour_forecast && $wpcloudy_hour_forecast_nd) {
					
					echo $display_hours_0;
					
					for ($i = 0; $i < $wpcloudy_hour_forecast_nd - 1; $i++) {
						echo $wpc_html_hour[$i];
					};
					
				}
			?>
			<?php echo $wpc_html_hour_end; ?>
		<?php } ?>
		<?php if ($wpc_html_forecast) { ?>	
			<!-- Daily Forecast -->
			<div class="wpc-flexslider-forecast">
				<?php echo $wpc_html_forecast_start; ?>
					<?php
					if( $wpcloudy_forecast && $wpcloudy_forecast_nd) {
						
						for ($i = 0; $i < $wpcloudy_forecast_nd; $i++) {
							if ($i == 0 || $i == 3 || $i == 6 || $i == 9 || $i == 12 ) {
								echo '<li>';
							}
							
							echo $wpc_html_forecast[$i];
						
							if ( $i == 2 || $i == 5 || $i == 8 || $i == 11) {
								echo '</li>';
							}
						};
						
					}
					?>
				<?php echo $wpc_html_forecast_end; ?>
			</div>
		<?php } ?>
		
		<!-- Weather Map -->
		<?php echo $wpc_html_map; ?>
	</div><!-- End .toggle-infos -->
	<!-- CSS -->
	<?php echo $wpc_html_custom_css; ?>
	<?php echo $wpc_html_css3_anims; ?>
	<?php echo $wpc_html_temp_unit_metric; ?>
	<?php echo $wpc_html_temp_unit_imperial; ?>
	
	<!-- OWM Link -->
	<?php echo $wpc_html_owm_link; ?>
	
	<!-- OWM Last Update -->
	<?php echo $wpc_html_last_update; ?>

<!-- End #wpc-weather -->
<?php echo $wpc_html_container_end; ?>

<script type="text/javascript" charset="utf-8">
	jQuery( ".wpc-btn-toggle-infos" ).click(function() {
		jQuery( ".wpc-toggle-infos" ).toggleClass( "wpc-slide-down" );
	});
	jQuery(window).ready(function() {
		jQuery('.wpc-flexslider').flexslider({
			namespace: "wpc-",
			selector: ".wpc-slides > li",
			directionNav: false,
		});
		jQuery('.wpc-flexslider-forecast').flexslider({
			namespace: "wpc-",
			selector: " .forecast > li",
			directionNav: false,
			controlNav: true,
			itemWidth: '100',
			itemMargin: 0, 
			minItems: 1, 
			maxItems: 3,
			touch: true,
		});
	});
</script>
