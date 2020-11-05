<?php

global $wpgmza_pro_version;
global $wpgmza_pro_string;

$wpgmza_pro_string = "pro";

if(!function_exists('wpgmza_require_once'))
{
	function wpgmza_require_once($filename)
	{
		if(!file_exists($filename))
			throw new Exception("Fatal error: wpgmza_require_once(): Failed opening required '$filename'");
		
		require_once($filename);
	}
}

add_action('admin_notices', 'wpgmza_show_ugm_incompatible_notice');
function wpgmza_show_ugm_incompatible_notice()
{
	global $wpgmza;
	global $wpgmza_ugm_version;
	
	if(empty($wpgmza_ugm_version))
		return;
	
	if(version_compare($wpgmza_ugm_version, '3.02', '>=') || $wpgmza->settings->engine != 'open-layers')
		return;
	
	?>
	<div class="notice notice-error">
		<p>
			<?php
			_e('<strong>WP Google Maps Pro:</strong> User Generated Markers add-on 3.01 and below is not compatible with OpenLayers. Please either switch engine to Google under Maps &rarr; Settings, or update User Generated Markers to 3.02 or above', 'wp-google-maps');
			?>
		</p>
	</div>
	<?php
}

add_action('admin_notices', 'wpgmza_show_gold_incompatible_notice');
function wpgmza_show_gold_incompatible_notice()
{
	global $wpgmza;
	global $wpgmza_gold_version;
	
	if(empty($wpgmza_gold_version))
		return;
	
	if(version_compare($wpgmza_gold_version, '4.11', '>=') || $wpgmza->settings->engine != 'open-layers')
		return;
	
	?>
	<div class="notice notice-error">
		<p>
			<?php
			_e('<strong>WP Google Maps Pro:</strong> Gold Add-on versions 4.11 and below are not compatible with OpenLayers. Please update to Gold 4.11 or above to use Gold features with the OpenLayers engine.', 'wp-google-maps');
			?>
		</p>
	</div>
	<?php
}

register_activation_hook(WPGMZA_PRO_FILE, function() {
	
	wpgmza_require_once(plugin_dir_path(__FILE__) . 'includes/class.pro-plugin.php');
	
	WPGMZA\ProPlugin::onActivate();
	
});

register_deactivation_hook(WPGMZA_PRO_FILE, function() {
	
	wpgmza_require_once(plugin_dir_path(__FILE__) . 'includes/class.pro-plugin.php');
	
	WPGMZA\ProPlugin::onDeactivate();
	
});

add_action('plugins_loaded', function() {
	
	// Register Pro classes with auto-loader
	function wpgmza_pro_load()
	{
		wpgmza_require_once(plugin_dir_path(__FILE__) . 'includes/class.pro-plugin.php');
		
		global $wpgmza_auto_loader;
		
		if(!$wpgmza_auto_loader)
			return;
		
		$wpgmza_auto_loader->registerClassesInPath(plugin_dir_path(__FILE__) . 'includes/');
	}
	
	if(method_exists('WPGMZA\\Plugin', 'preloadIsInDeveloperMode') && WPGMZA\Plugin::preloadIsInDeveloperMode())
		wpgmza_pro_load();
	else
		try{
			wpgmza_pro_load();
		}catch(Exception $e) {
			
			add_action('admin_notices', function() use ($e) {
				
				?>
				<div class="notice notice-error is-dismissible">
					<p>
						<strong>
						<?php
						_e('WP Google Maps', 'wp-google-maps');
						?></strong>:
						<?php
						_e('The Pro add-on cannot be registered due to a fatal error. This is usually due to missing files. Please re-install the Pro add-on. Technical details are as follows: ', 'wp-google-maps');
						echo $e->getMessage();
						?>
					</p>
				</div>
				<?php
				
			});
			
		}
	
}, 1);

global $wpgmza_current_map_cat_selection;
global $wpgmza_current_map_shortcode_data;
global $wpgmza_current_map_type;

global $wpgmza_p;
global $wpgmza_t;
$wpgmza_p = true;
$wpgmza_t = "pro";

global $wpdb;

global $wpgmza_count;
$wpgmza_count = 0;

global $wpgmza_post_nonce;
$wpgmza_post_nonce = md5(time());

global $WPGMZA_TABLE_NAME_MARKERS;
$WPGMZA_TABLE_NAME_MARKERS = $wpdb->prefix . 'wpgmza';

global $wpdb;
global $wpgmza_tblname_datasets;
$wpgmza_tblname_datasets = $wpdb->prefix . "wpgmza_datasets";

global $wpgmza_tblname_circles;
$wpgmza_tblname_circles = $wpdb->prefix . "wpgmza_circles";

global $wpgmza_tblname_rectangles;
$wpgmza_tblname_rectangles = $wpdb->prefix . "wpgmza_rectangles";

/*global $WPGMZA_TABLE_NAME_CATEGORIES;
$WPGMZA_TABLE_NAME_CATEGORIES = $wpdb->prefix . 'wpgmza_categories';

global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
$WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES = $wpdb->prefix . 'wpgmza_markers_has_categories';*/

global $wpgmza_override;
$wpgmza_override = array();

global $wpgmza_shortcode_atts_by_map_id;
$wpgmza_shortcode_atts_by_map_id = array();

$plugin_dir_path = plugin_dir_path(__FILE__);

global $wpgmza_default_store_locator_radii;
$wpgmza_default_store_locator_radii = array(1,5,10,25,50,75,100,150,200,300);

// TODO: Favour autoloaders in the future
wpgmza_require_once($plugin_dir_path . 'includes/3rd-party-integration/class.wp-migrate-db-integration.php');
wpgmza_require_once($plugin_dir_path . 'includes/3rd-party-integration/class.acf.php');

wpgmza_require_once($plugin_dir_path . 'includes/class.category.php');

wpgmza_require_once($plugin_dir_path . "includes/legacy/page.legacy-import-export.php");

wpgmza_require_once($plugin_dir_path . "includes/page.categories.php");
wpgmza_require_once($plugin_dir_path . "includes/page.wizard.php");
wpgmza_require_once($plugin_dir_path . "includes/class.legacy-marker-listing.php");

wpgmza_require_once($plugin_dir_path . "includes/import-export/page.import-export.php");
wpgmza_require_once($plugin_dir_path . 'includes/custom-fields/page.custom-fields.php');

wpgmza_require_once($plugin_dir_path . 'includes/custom-fields/class.custom-fields.php');
wpgmza_require_once($plugin_dir_path . 'includes/custom-fields/class.custom-marker-fields.php');
wpgmza_require_once($plugin_dir_path . 'includes/custom-fields/class.custom-field-filter-widget.php');
wpgmza_require_once($plugin_dir_path . 'includes/custom-fields/class.custom-field-filter-controller.php');

wpgmza_require_once($plugin_dir_path . 'includes/class.pro-map.php');
wpgmza_require_once($plugin_dir_path . 'includes/class.pro-marker-filter.php');

// Google API Loader
if(!function_exists('wpgmza_enqueue_scripts'))
{
	function wpgmza_enqueue_scripts()
	{
		global $wpgmza_google_maps_api_loader;
		
		if(!class_exists('WPGMZA\\GoogleMapsAPILoader'))
			return;
		
		$wpgmza_google_maps_api_loader = new WPGMZA\GoogleMapsAPILoader();
		$wpgmza_google_maps_api_loader->registerGoogleMaps();
		
		if(isset($_GET['page']) && preg_match('/wp-google-maps/', $_GET['page']))
			$wpgmza_google_maps_api_loader->enqueueGoogleMaps();
	}
	
	add_action('wp_enqueue_scripts', 'wpgmza_enqueue_scripts');
	add_action('admin_enqueue_scripts', 'wpgmza_enqueue_scripts');
}

add_action('init', function() {
	if(is_admin() && isset($_GET['page']) && $_GET['page'] == 'wp-google-maps-menu' && isset($_GET['map_id']))
	{
		// NB: This is a temporary workaround to show notices from the map, it's done here because the map edit page renders after the admin_notices action. This will be moved
		WPGMZA\Map::createInstance($_GET['map_id']);
	}
});

add_action('admin_head', 'wpgmaps_upload_csv');
add_action('init', 'wpgmza_register_pro_version');

// DEPRECATED: Should be loaded in Basic first.
/*if(!function_exists('wpgmza_get_marker_columns'))
{
	function wpgmza_get_marker_columns()
	{
		global $wpdb;
		global $wpgmza_tblname;
		
		if(empty($wpgmza_tblname))
			return;
		
		$columns = $wpdb->get_col("SHOW COLUMNS FROM $wpgmza_tblname");
		
		if(($index = array_search('lat', $columns)) !== false)
			array_splice($columns, $index, 1);
		if(($index = array_search('lng', $columns)) !== false)
			array_splice($columns, $index, 1);
		
		for($i = count($columns) - 1; $i >= 0; $i--)
			$columns[$i] = '`' . trim($columns[$i], '`') . '`';
		
		$columns[] = 'ST_X(latlng) AS lat';
		$columns[] = 'ST_Y(latlng) AS lng';
		
		return $columns;
	}
}*/

if(!function_exists('wpgmza_get_circles_table'))
{
	function wpgmza_get_circles_table($map_id)
	{
		global $wpdb;
		global $wpgmza_tblname_circles;
		
		$circles_table = "
			<table>
				<thead>
					<tr>
						<th>" . __('ID', 'wp-google-maps') . "</th>
						<th>" . __('Name', 'wp-google-maps') . "</th>
						<th>" . __('Action', 'wp-google-maps') . "</th>
					</tr>
				</thead>
				<tbody>
		";
		
		$stmt = $wpdb->prepare("SELECT * FROM $wpgmza_tblname_circles WHERE map_id = %d", array($map_id));
		$circles = $wpdb->get_results($stmt);
		foreach($circles as $circle)
		{
			$circles_table .= "
				<tr>
					<td>{$circle->id}</td>
					<td>{$circle->name}</td>
					<td width='170' align='left'>
						<a href='" . get_option('siteurl') . "/wp-admin/admin.php?page=wp-google-maps-menu&amp;action=edit_circle&amp;map_id={$map_id}&amp;circle_id={$circle->id}'
							title='" . __('Edit', 'wp-google-maps') . "' 
							class='wpgmza_edit_circle_btn button'
							id='{$circle->id}'>
							<i class='fa fa-edit'> </i>
						</a> 
						<a href='javascript:void(0);'
							title='" . __('Delete this circle', 'wp-google-maps') . "' class='wpgmza_circle_del_btn button' id='{$circle->id}'><i class='fa fa-times'> </i>
						</a>	
					</td>
				</tr>
			";
		}
		
		$circles_table .= "
				</tbody>
			</table>
		";
		
		return $circles_table;
	}
}
	
if(!function_exists('wpgmza_get_rectangles_table'))
{
	function wpgmza_get_rectangles_table($map_id)
	{
		global $wpdb;
		global $wpgmza_tblname_rectangles;
		
		$rectangles_table = "
			<table>
				<thead>
					<tr>
						<th>" . __('ID', 'wp-google-maps') . "</th>
						<th>" . __('Name', 'wp-google-maps') . "</th>
						<th>" . __('Action', 'wp-google-maps') . "</th>
					</tr>
				</thead>
				<tbody>
		";
		
		$stmt = $wpdb->prepare("SELECT * FROM $wpgmza_tblname_rectangles WHERE map_id = %d", array($map_id));
		$rectangles = $wpdb->get_results($stmt);
		foreach($rectangles as $rectangle)
		{
			$rectangles_table .= "
				<tr>
					<td>{$rectangle->id}</td>
					<td>{$rectangle->name}</td>
					<td width='170' align='left'>
						<a href='" . get_option('siteurl') . "/wp-admin/admin.php?page=wp-google-maps-menu&amp;action=edit_rectangle&amp;map_id={$map_id}&amp;rectangle_id={$rectangle->id}'
							title='" . __('Edit', 'wp-google-maps') . "' 
							class='wpgmza_edit_rectangle_btn button'
							id='{$rectangle->id}'>
							<i class='fa fa-edit'> </i>
						</a> 
						<a href='javascript:void(0);'
							title='" . __('Delete this rectangle', 'wp-google-maps') . "' class='wpgmza_rectangle_del_btn button' id='{$rectangle->id}'><i class='fa fa-times'> </i>
						</a>	
					</td>
				</tr>
			";
		}
		
		$rectangles_table .= "
				</tbody>
			</table>
		";
		
		return $rectangles_table;
	}
}

function wpgmaps_pro_activate() { 

    wpgmza_cURL_response_pro("activate");
    wpgmaps_handle_db_pro();
    if (function_exists("wpgmaps_handle_directory")) { wpgmaps_handle_directory(); }
	// Setup import schedules.
    WPGMZA\import_get_schedule();
}

function wpgmaps_pro_deactivate() {
	// Clear all import schedules.
	$crons = _get_cron_array();
	if ( ! empty( $crons ) ) {
		$unset_cron = false;
		foreach ( $crons as $timestamp => $cron ) {
			if ( isset( $crons[ $timestamp ]['wpgmza_import_cron'] ) ) {
				$unset_cron = true;
				unset( $crons[ $timestamp ]['wpgmza_import_cron'] );
				if ( empty( $crons[ $timestamp ] ) ) {
					unset( $crons[ $timestamp ] );
				}
			}
		}
		if ( $unset_cron ) {
			_set_cron_array( $crons );
		}
	}
	wpgmza_cURL_response_pro("deactivate");
}

function wpgmza_user_can_edit_maps() {

	$wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");

	if ( isset( $wpgmza_settings['wpgmza_settings_access_level'] ) ) {
		$access_level = $wpgmza_settings['wpgmza_settings_access_level'];
	} else {
		$access_level = "manage_options";
	}

	return current_user_can( $access_level );

}

function wpgmza_update_basic_v6_notice() {
	?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<?php
			_e('<strong>WP Google Maps Pro:</strong> Experiencing issues? We strongly recommend that you update WP Google Maps (Basic) to Version 8.0.0 in the plugins menu', 'wp-google-maps');
			?>
		</p>
	</div>
	<?php
}

function wpgmza_register_pro_version() {
    global $wpgmza_pro_version;
    global $wpgmza_pro_string;
    global $wpgmza_t;
    global $wpgmza_version;
	
	if(version_compare($wpgmza_version, '7.0.0', '<'))
		add_action('admin_notices', 'wpgmza_update_basic_v6_notice');
	
	// TODO: This should use the admin_post hooks
	if(wpgmza_user_can_edit_maps() && isset($_GET['action']))
	{
		switch($_GET['action']) {
			
			case 'wpgmza_csv_export':
				$export = new WPGMapsImportExport();
				$export->export_markers();
				break;
				
			case 'export_single_map':
				$export = new WPGMapsImportExport();
				$export->export_map( (int)$_GET['mid'] );
				break;
			
			case 'export_all_maps':
				$export = new WPGMapsImportExport();
				$export->export_map();
				break;
				
			case 'export_polygons':
				$export = new WPGMapsImportExport();
				$export->export_polygons();
				break;
			
			case 'export_polylines':
				$export = new WPGMapsImportExport();
				$export->export_polylines();
				break;
				
			case 'import_polylines':
				$export = new WPGMapsImportExport();
				$export->import_polylines();
				break;
				
			case 'import_polygons':
				$export = new WPGMapsImportExport();
				$export->import_polygons();
				break;
			
		}
	}

}

function wpgmza_pro_update_control()
{
	trigger_error("Deprecated as of 8.0.19");
}

/* deprecated from 6.02 */
//add_action('wp_enqueue_scripts','wpgmaps_user_styles_pro');
function wpgmaps_user_styles_pro() {
		global $short_code_active;
		if ($short_code_active) {
			/* only show styles on pages that contain the shortcode for the map */
			global $wpgmza_pro_version;
       		//wp_register_style( 'wpgmaps-style-pro', plugins_url('css/wpgmza_style_pro.css', __FILE__), array(), $wpgmza_pro_version);
       		//wp_enqueue_style( 'wpgmaps-style-pro' );


       	}
}

/**
 * @deprecated Since 8.0.10
 */ 
function wpgmaps_handle_db_pro() {

}

function wpgmza_pro_menu() {
	
    global $wpgmza_pro_version;
    global $wpgmza_p_version;
    global $wpgmza_post_nonce;
    global $wpgmza_tblname_maps;
    global $wpdb;
	global $wpgmza;
	global $wpgmza_gold_version;
	
	if(!$wpgmza)
		return; // Bail out, we're running and older (incompatible) version of Basic

	$real_post_nonce = wp_create_nonce('wpgmza');

	wpgmza_require_once(plugin_dir_path(__FILE__) . 'includes/class.marker-library-dialog.php');
	
    $handle = 'avia-google-maps-api';
    $list = 'enqueued';
    if (wp_script_is( $handle, $list )) {
        wp_deregister_script('avia-google-maps-api');
    }
    
    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
    
    
    $list_markers_by_sel_text = __('Default', 'wp-google-maps');
    
    
    
    if ($_GET['action'] == "edit") {

    }
    else if($_GET['action'] == "wizard"){
    	wpgmaps_wizard_layout();
    }
    else if ($_GET['action'] == "new" || $_GET['action'] == "new-wizard") {


        $def_data = get_option("WPGMZA_SETTINGS");
        if (isset($def_data->map_default_starting_lat)) { $data['map_default_starting_lat'] = $def_data->map_default_starting_lat; }
        if (isset($def_data->map_default_starting_lng)) { $data['map_default_starting_lng'] = $def_data->map_default_starting_lng; }
        if (isset($def_data->map_default_height)) { $data['map_default_height'] = $def_data->map_default_height; }
        if (isset($def_data->map_default_width)) { $data['map_default_width'] = $def_data->map_default_width; }
        if (isset($def_data->map_default_height_type)) { $data['map_default_height_type'] = stripslashes($def_data->map_default_height_type); }
        if (isset($def_data->map_default_width_type)) { $data['map_default_width_type'] =stripslashes($def_data->map_default_width_type); }
        if (isset($def_data->map_default_zoom)) { $data['map_default_zoom'] = $def_data->map_default_zoom; }
        if (isset($def_data->map_default_type)) { $data['map_default_type'] = $def_data->map_default_type; }
        if (isset($def_data->map_default_alignment)) { $data['map_default_alignment'] = $def_data->map_default_alignment; }
        if (isset($def_data->map_default_order_markers_by)) { $data['map_default_order_markers_by'] = $def_data->map_default_order_markers_by; }
        if (isset($def_data->map_default_order_markers_choice)) { $data['map_default_order_markers_choice'] = $def_data->map_default_order_markers_choice; }
        if (isset($def_data->map_default_show_user_location)) { $data['map_default_show_user_location'] = $def_data->map_default_show_user_location; }
        if (isset($def_data->map_default_directions)) { $data['map_default_directions'] = $def_data->map_default_directions; }
        if (isset($def_data->map_default_bicycle)) { $data['map_default_bicycle'] = $def_data->map_default_bicycle; }
        if (isset($def_data->map_default_traffic)) { $data['map_default_traffic'] = $def_data->map_default_traffic; }
        if (isset($def_data->map_default_dbox)) { $data['map_default_dbox'] = $def_data->map_default_dbox; }
        if (isset($def_data->map_default_dbox_width)) { $data['map_default_dbox_width'] = $def_data->map_default_dbox_width; }
        if (isset($def_data->map_default_default_to)) { $data['map_default_default_to'] = $def_data->map_default_default_to; }
        if (isset($def_data->map_default_marker)) { $data['map_default_marker'] = $def_data->map_default_marker; }


        if (isset($def_data['map_default_height_type'])) {
            $wpgmza_height_type = $def_data['map_default_height_type'];
        } else {
            $wpgmza_height_type = "px";
        }
        if (isset($def_data['map_default_width_type'])) {
            $wpgmza_width_type = $def_data['map_default_width_type'];
        } else {
            $wpgmza_width_type = "px";
        }
        
        if (isset($def_data['map_default_height'])) {
            $wpgmza_height = $def_data['map_default_height'];
        } else {
            $wpgmza_height = "400";
        }
        if (isset($def_data['map_default_width'])) {
            $wpgmza_width = $def_data['map_default_width'];
        } else {
            $wpgmza_width = "600";
        }
        if (isset($def_data['map_default_marker'])) {
            $wpgmza_def_marker = $def_data['map_default_marker'];
        } else {
            $wpgmza_def_marker = "0";
        }
        if (isset($def_data['map_default_alignment'])) {
            $wpgmza_def_alignment = $def_data['map_default_alignment'];
        } else {
            $wpgmza_def_alignment = "0";
        }
        if (isset($def_data['map_default_order_markers_by'])) {
            $wpgmza_def_order_markers_by = $def_data['map_default_order_markers_by'];
        } else {
            $wpgmza_def_order_markers_by = "0";
        }
        if (isset($def_data['map_default_order_markers_choice'])) {
            $wpgmza_def_order_markers_choice = $def_data['map_default_order_markers_choice'];
        } else {
            $wpgmza_def_order_markers_choice = "0";
        }
        if (isset($def_data['map_default_show_user_location'])) {
            $wpgmza_def_show_user_location = $def_data['map_default_show_user_location'];
        } else {
            $wpgmza_def_show_user_location = "0";
        }
        if (isset($def_data['map_default_directions'])) {
            $wpgmza_def_directions = $def_data['map_default_directions'];
        } else {
            $wpgmza_def_directions = "0";
        }
        if (isset($def_data['map_default_bicycle'])) {
            $wpgmza_def_bicycle = $def_data['map_default_bicycle'];
        } else {
            $wpgmza_def_bicycle = "0";
        }
        if (isset($def_data['map_default_traffic'])) {
            $wpgmza_def_traffic = $def_data['map_default_traffic'];
        } else {
            $wpgmza_def_traffic = "0";
        }
        if (isset($def_data['map_default_dbox'])) {
            $wpgmza_def_dbox = $def_data['map_default_dbox'];
        } else {
            $wpgmza_def_dbox = "0";
        }
        if (isset($def_data['map_default_dbox_wdith'])) {
            $wpgmza_def_dbox_width = $def_data['map_default_dbox_width'];
        } else {
            $wpgmza_def_dbox_width = "100";
        }
        if (isset($def_data['map_default_default_to'])) {
            $wpgmza_def_default_to = $def_data['map_default_default_to'];
        } else {
            $wpgmza_def_default_to = "";
        }
        if (isset($def_data['map_default_listmarkers'])) {
            $wpgmza_def_listmarkers = $def_data['map_default_listmarkers'];
        } else {
            $wpgmza_def_listmarkers = "0";
        }
        if (isset($def_data['map_default_listmarkers_advanced'])) {
            $wpgmza_def_listmarkers_advanced = $def_data['map_default_listmarkers_advanced'];
        } else {
            $wpgmza_def_listmarkers_advanced = "0";
        }
        if (isset($def_data['map_default_filterbycat'])) {
            $wpgmza_def_filterbycat = $def_data['map_default_filterbycat'];
        } else {
            $wpgmza_def_filterbycat = "0";
        }
        if (isset($def_data['map_default_type'])) {
            $wpgmza_def_type = $def_data['map_default_type'];
        } else {
            $wpgmza_def_type = "1";
        }

        if (isset($def_data['map_default_zoom'])) {
            $start_zoom = $def_data['map_default_zoom'];
        } else {
            $start_zoom = 5;
        }
        
        if (isset($def_data['map_default_ugm_access'])) {
            $ugm_access = $def_data['map_default_ugm_access'];
        } else {
            $ugm_access = 0;
        }
        
        if (isset($def_data['map_default_starting_lat']) && isset($def_data['map_default_starting_lng'])) {
            $wpgmza_lat = $def_data['map_default_starting_lat'];
            $wpgmza_lng = $def_data['map_default_starting_lng'];
        } else {
            $wpgmza_lat = "51.5081290";
            $wpgmza_lng = "-0.1280050";
        }

        $wpgmza_map_data_content = array(
            "map_title" => "New Map",
            "map_start_lat" => "$wpgmza_lat",
            "map_start_lng" => "$wpgmza_lng",
            "map_width" => "$wpgmza_width",
            "map_height" => "$wpgmza_height",
            "map_start_location" => "$wpgmza_lat,$wpgmza_lng",
            "map_start_zoom" => "$start_zoom",
            "default_marker" => "$wpgmza_def_marker",
            "alignment" => "$wpgmza_def_alignment",
            "styling_enabled" => "0",
            "styling_json" => "",
            "active" => "0",
            "directions_enabled" => "$wpgmza_def_directions",
            "default_to" => "",
            "type" => "$wpgmza_def_type",
            "kml" => "",
            "fusion" => "",
            "map_width_type" => "$wpgmza_width_type",
            "map_height_type" => "$wpgmza_height_type",
            "fusion" => "",
            "mass_marker_support" => "0",
            "ugm_enabled" => "0",
            "ugm_category_enabled" => "0",
            "ugm_access" => "$ugm_access",
            "bicycle" => "$wpgmza_def_bicycle",
            "traffic" => "$wpgmza_def_traffic",
            "dbox" => "$wpgmza_def_dbox",
            "dbox_width" => "$wpgmza_def_dbox_width",
            "listmarkers" => "$wpgmza_def_listmarkers",
            "listmarkers_advanced" => "$wpgmza_def_listmarkers_advanced",
            "filterbycat" => "$wpgmza_def_filterbycat",
            "order_markers_by" => "$wpgmza_def_order_markers_by",
            "order_markers_choice" => "$wpgmza_def_order_markers_choice",
            "show_user_location" => "$wpgmza_def_show_user_location",
            "other_settings" => 'a:3:{s:19:"store_locator_style";s:6:"modern";s:33:"wpgmza_store_locator_radius_style";s:6:"modern";s:20:"directions_box_style";s:6:"modern";}'
            );

		//Filter Array if the wizard is in use
		if($_GET['action'] == "new-wizard"){
			if(isset($_GET['wpgmza_keys']) && isset($_GET['wpgmza_values'])){
				$wpgmza_map_data_keys = explode(",", urldecode($_GET['wpgmza_keys']));
				$wpgmza_map_data_values = explode(",", urldecode($_GET['wpgmza_values']));

				$wpgmza_map_data_content = wpgmza_wizard_data_filter($wpgmza_map_data_content, $wpgmza_map_data_keys, $wpgmza_map_data_values);
			}
		}
	    $wpdb->insert( $wpgmza_tblname_maps, $wpgmza_map_data_content);
        $lastid = $wpdb->insert_id;
        //echo $wpdb->last_error;

        $_GET['map_id'] = $lastid;
        //wp_redirect( admin_url('admin.php?page=wp-google-maps-menu&action=edit&map_id='.$lastid) );
        //$wpdb->print_errors();
        
       	echo "<script>window.location = \"".get_option('siteurl')."/wp-admin/admin.php?page=wp-google-maps-menu&action=edit&map_id=".$lastid."\"</script>";
    }


    if (isset($_GET['map_id'])) {
        
        if (function_exists("wpgmaps_marker_permission_check")) { wpgmaps_marker_permission_check(); }



        $res = wpgmza_get_map_data($_GET['map_id']);

        //if (function_exists("google_maps_api_key_warning")) { google_maps_api_key_warning(); }


        if (function_exists("wpgmza_register_gold_version")) {
        	$addon_text = __("including Pro &amp; Gold add-ons","wp-google-maps");
        } else {
        	$addon_text = __("including Pro add-on","wp-google-maps");
        }
        
        if (function_exists("wpgmza_register_gold_version")) { 
            global $wpgmza_gold_version;
            if (floatval($wpgmza_gold_version) < 3.25) {
                $addon_text .= "<div class='error below-h1'><p>".__("Please <a href='update-core.php'>update your WP Google Maps GOLD version</a>. Your current Gold version is not compatible with the current Pro version.")."</p></div>";
            }
            
        }
        
        /* if (!$res->map_id || $res->map_id == "") { $wpgmza_data['map_id'] = 1; } */
        if (!$res->default_marker || $res->default_marker == "" || $res->default_marker == "0") { $display_marker = "<img src=\"".wpgmaps_get_plugin_url()."/images/marker.png\" />"; } else { $display_marker = "<img src=\"".$res->default_marker."\" />"; }
        if ($res->map_start_zoom) { $wpgmza_zoom[intval($res->map_start_zoom)] = "SELECTED"; } else { $wpgmza_zoom[8] = "SELECTED"; }
        if ($res->type) { $wpgmza_map_type[intval($res->type)] = "SELECTED"; } else { $wpgmza_map_type[1] = "SELECTED"; }
        if ($res->alignment) { $wpgmza_map_align[intval($res->alignment)] = "SELECTED"; } else { $wpgmza_map_align[1] = "SELECTED"; }
        if ($res->directions_enabled) { $wpgmza_directions[intval($res->directions_enabled)] = "checked"; } else { $wpgmza_directions[2] = ""; }
        if ($res->bicycle) { $wpgmza_bicycle[intval($res->bicycle)] = "checked"; } else { $wpgmza_bicycle[2] = ""; }
        if ($res->traffic) { $wpgmza_traffic[intval($res->traffic)] = "checked"; } else { $wpgmza_traffic[2] = ""; }
        if ($res->dbox != "1") { $wpgmza_dbox[intval($res->dbox)] = "SELECTED"; } else { $wpgmza_dbox[1] = "SELECTED"; }

        if ($res->order_markers_by) { $wpgmza_map_order_markers_by[intval($res->order_markers_by)] = "SELECTED"; } else { $wpgmza_map_order_markers_by[1] = "SELECTED"; }
        if ($res->order_markers_choice) { $wpgmza_map_order_markers_choice[intval($res->order_markers_choice)] = "SELECTED"; } else { $wpgmza_map_order_markers_choice[2] = "SELECTED"; }

        if ($res->show_user_location) { $wpgmza_show_user_location[intval($res->show_user_location)] = "checked"; } else { $wpgmza_show_user_location[2] = ""; }
        
        $wpgmza_map_width_type_px = "";
        $wpgmza_map_height_type_px = "";
        $wpgmza_map_width_type_percentage = "";
        $wpgmza_map_height_type_percentage = "";
        
       if (stripslashes($res->map_width_type) == "%") { $wpgmza_map_width_type_percentage = "SELECTED"; } else { $wpgmza_map_width_type_px = "SELECTED"; }
       if (stripslashes($res->map_height_type) == "%") { $wpgmza_map_height_type_percentage = "SELECTED"; } else { $wpgmza_map_height_type_px = "SELECTED"; }


        if (isset($res->listmarkers) && $res->listmarkers == "1") { $listmarkers_checked = "CHECKED"; } else { $listmarkers_checked = ""; }
        if (isset($res->filterbycat) && $res->filterbycat == "1") { $listfilters_checked = "CHECKED"; } else { $listfilters_checked = ""; }
        if (isset($res->listmarkers_advanced) && $res->listmarkers_advanced == "1") { $listmarkers_advanced_checked = "CHECKED"; } else { $listmarkers_advanced_checked = ""; }

        
        
        
        
        for ($i=0;$i<22;$i++) {
            if (!isset($wpgmza_zoom[$i])) { $wpgmza_zoom[$i] = ""; }
        }
        for ($i=0;$i<5;$i++) {
            if (!isset($wpgmza_map_type[$i])) { $wpgmza_map_type[$i] = ""; }
        }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_sl_animation[$i])) { $wpgmza_sl_animation[$i] = ""; }
        }        
        for ($i=0;$i<5;$i++) {
            if (!isset($wpgmza_map_align[$i])) { $wpgmza_map_align[$i] = ""; }
        }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_bicycle[$i])) { $wpgmza_bicycle[$i] = ""; }
        }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_traffic[$i])) { $wpgmza_traffic[$i] = ""; }
        }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_directions[$i])) { $wpgmza_directions[$i] = ""; }
        }
        for ($i=0;$i<6;$i++) {
            if (!isset($wpgmza_dbox[$i])) { $wpgmza_dbox[$i] = ""; }
        }
        for ($i=0;$i<9;$i++) {
            if (!isset($wpgmza_map_order_markers_by[$i])) { $wpgmza_map_order_markers_by[$i] = ""; }
        } 
        for ($i=0;$i<6;$i++) {
            if (!isset($wpgmza_map_order_markers_choice[$i])) { $wpgmza_map_order_markers_choice[$i] = ""; }
        }   
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_show_user_location[$i])) { $wpgmza_show_user_location[$i] = ""; }
        }   


        
        
        $other_settings_data = maybe_unserialize($res->other_settings);
        if (isset($other_settings_data['store_locator_enabled'])) { $wpgmza_store_locator_enabled = $other_settings_data['store_locator_enabled']; } else { $wpgmza_store_locator_enabled = 0; }
        if (isset($other_settings_data['wpgmza_store_locator_restrict'])) { $wpgmza_store_locator_restrict = $other_settings_data['wpgmza_store_locator_restrict']; } else { $wpgmza_store_locator_restrict = ""; }
        if (isset($other_settings_data['store_locator_distance'])) { $wpgmza_store_locator_distance = $other_settings_data['store_locator_distance']; } else { $wpgmza_store_locator_distance = 0; }
        if (isset($other_settings_data['store_locator_below'])) { $wpgmza_store_locator_below = $other_settings_data['store_locator_below']; } else { $wpgmza_store_locator_below = 0; }
        if (isset($other_settings_data['wpgmza_sl_animation'])) { $wpgmza_sl_animation[intval($other_settings_data['wpgmza_sl_animation'])] = 'selected'; } else { $wpgmza_sl_animation[1] = 'selected'; }

        if (isset($other_settings_data['store_locator_bounce'])) { $wpgmza_store_locator_bounce = $other_settings_data['store_locator_bounce']; } else { $wpgmza_store_locator_bounce = 0; }
        if (isset($other_settings_data['store_locator_hide_before_search'])) { $wpgmza_store_locator_hide_before_search = $other_settings_data['store_locator_hide_before_search']; } else { $wpgmza_store_locator_hide_before_search = 0; }
        if (isset($other_settings_data['store_locator_use_their_location'])) { $wpgmza_store_locator_use_their_location = $other_settings_data['store_locator_use_their_location']; } else { $wpgmza_store_locator_use_their_location = 0; }
        if (isset($other_settings_data['store_locator_default_address'])) { $wpgmza_store_locator_default_address = stripslashes($other_settings_data['store_locator_default_address']); } else { $wpgmza_store_locator_default_address = ""; }
        if (isset($other_settings_data['store_locator_default_radius'])) { $wpgmza_store_locator_default_radius = stripslashes($other_settings_data['store_locator_default_radius']); } else { $wpgmza_store_locator_default_radius = 2; }
	    if (isset($other_settings_data['store_locator_not_found_message'])) { $wpgmza_store_locator_not_found_message = stripslashes($other_settings_data['store_locator_not_found_message']); } else { $wpgmza_store_locator_not_found_message = __( "No results found in this location. Please try again.", "wp-google-maps" ); }

        if (isset($other_settings_data['store_locator_name_search'])) { $wpgmza_store_locator_name_search = $other_settings_data['store_locator_name_search']; } else { $wpgmza_store_locator_name_search = 0; }
        if (isset($other_settings_data['store_locator_category'])) { $wpgmza_store_locator_category_enabled = $other_settings_data['store_locator_category']; }
        if (isset($other_settings_data['store_locator_query_string'])) { $wpgmza_store_locator_query_string = stripslashes($other_settings_data['store_locator_query_string']); } else { $wpgmza_store_locator_query_string = __("ZIP / Address:","wp-google-maps"); }
        if (isset($other_settings_data['store_locator_name_string'])) { $wpgmza_store_locator_name_string = stripslashes($other_settings_data['store_locator_name_string']); } else { $wpgmza_store_locator_name_string = __("Title / Description:","wp-google-maps"); }
        
        if (isset($other_settings_data['jump_to_nearest_marker_on_initialization'])) { $wpgmza_jump_to_nearest_marker_on_initialization[intval($other_settings_data['jump_to_nearest_marker_on_initialization'])] = "checked"; } else { $wpgmza_jump_to_nearest_marker_on_initialization[0] = "checked";  }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_jump_to_nearest_marker_on_initialization[$i])) { $wpgmza_jump_to_nearest_marker_on_initialization[$i] = ""; }
        }
       
        if (isset($other_settings_data['automatically_pan_to_users_location'])) { $wpgmza_automatically_pan_to_users_location[intval($other_settings_data['automatically_pan_to_users_location'])] = "checked";} else { $wpgmza_automatically_pan_to_users_location[2] = "checked";  }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_automatically_pan_to_users_location[$i])) { $wpgmza_automatically_pan_to_users_location[$i] = ""; }
        }

        if (isset($other_settings_data['override_users_location_zoom_level'])) { $wpgmza_override_users_location_zoom_level[intval($other_settings_data['override_users_location_zoom_level'])] = "checked";} else { $wpgmza_override_users_location_zoom_level[2] = "checked";  }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_override_users_location_zoom_level[$i])) { $wpgmza_override_users_location_zoom_level[$i] = ""; }
        }
        
        if (isset($other_settings_data['click_open_link'])) { $wpgmza_click_open_link[intval($other_settings_data['click_open_link'])] = "checked"; } else { $wpgmza_click_open_link[2] = "checked";  }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_click_open_link[$i])) { $wpgmza_click_open_link[$i] = ""; }
        }
		
		if (isset($other_settings_data['hide_point_of_interest'])) { $wpgmza_hide_point_of_interest[intval($other_settings_data['hide_point_of_interest'])] = "checked";} else { $wpgmza_hide_point_of_interest[2] = "checked";  }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_hide_point_of_interest[$i])) { $wpgmza_hide_point_of_interest[$i] = ""; }
        }

        if (isset($other_settings_data['fit_maps_bounds_to_markers'])) { $wpgmza_fit_maps_bounds_to_markers[intval($other_settings_data['fit_maps_bounds_to_markers'])] = "checked";} else { $wpgmza_fit_maps_bounds_to_markers[2] = "checked";  }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_fit_maps_bounds_to_markers[$i])) { $wpgmza_fit_maps_bounds_to_markers[$i] = ""; }
        }

        if (isset($other_settings_data['fit_maps_bounds_to_markers_after_filtering'])) { $wpgmza_fit_maps_bounds_to_markers_after_filtering[intval($other_settings_data['fit_maps_bounds_to_markers_after_filtering'])] = "checked";} else { $wpgmza_fit_maps_bounds_to_markers_after_filtering[2] = "checked";  }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_fit_maps_bounds_to_markers_after_filtering[$i])) { $wpgmza_fit_maps_bounds_to_markers_after_filtering[$i] = ""; }
        }
        
        if (isset($other_settings_data['weather_layer'])) 
		{
			$wpgmza_weather_option = $other_settings_data['weather_layer'];
		}
		else
		{
			$wpgmza_weather_option = "";
		}
			
        if (isset($other_settings_data['weather_layer_temp_type'])) { $wpgmza_weather_option_temp_type = $other_settings_data['weather_layer_temp_type']; } else { $wpgmza_weather_option_temp_type = 1; } 
        if (isset($other_settings_data['cloud_layer'])) { $wpgmza_cloud_option = $other_settings_data['cloud_layer']; } else { $wpgmza_cloud_option = ""; }
        if (isset($other_settings_data['transport_layer'])) { $wpgmza_transport_option = $other_settings_data['transport_layer']; } else { $wpgmza_transport_option = 0; }
        if (isset($other_settings_data['map_max_zoom'])) { $wpgmza_max_zoom[intval($other_settings_data['map_max_zoom'])] = "SELECTED"; } else { $wpgmza_max_zoom[3] = "SELECTED";  }
        if (isset($other_settings_data['map_min_zoom'])) { $wpgmza_min_zoom[intval($other_settings_data['map_min_zoom'])] = "SELECTED"; } else { $wpgmza_min_zoom[21] = "SELECTED";  }
		
		for($i = 0; $i <= 21; $i++)
		{
			$wpgmza_override_users_location_zoom_levels[$i] = "";
		}
		
        if (isset($other_settings_data['override_users_location_zoom_levels'])) { $wpgmza_override_users_location_zoom_levels[intval($other_settings_data['override_users_location_zoom_levels'])] = "SELECTED"; } else { $wpgmza_override_users_location_zoom_levels[21] = "SELECTED";  }
        
        if (isset($other_settings_data['list_markers_by'])) { $list_markers_by_checked[intval($other_settings_data['list_markers_by'])] = "CHECKED"; $list_markers_by_class[intval($other_settings_data['list_markers_by'])] = "selected"; } else { $list_markers_by = false; }

        if (isset($other_settings_data['push_in_map']) && $other_settings_data['push_in_map'] == "1") { $pushinmap_checked = "CHECKED"; } else { $pushinmap_checked = ""; }
        if (isset($other_settings_data['push_in_map_placement'])) {$push_in_map_placement_checked[$other_settings_data['push_in_map_placement']] = "SELECTED"; } else { $push_in_map_placement_checked[9] = "SELECTED"; }
        if (isset($other_settings_data['wpgmza_push_in_map_width'])) { $wpgmza_push_in_map_width = $other_settings_data['wpgmza_push_in_map_width']; } else { $wpgmza_push_in_map_width = ""; }
        if (isset($other_settings_data['wpgmza_push_in_map_height'])) { $wpgmza_push_in_map_height = $other_settings_data['wpgmza_push_in_map_height']; } else { $wpgmza_push_in_map_height = ""; }

        global $wpgmza_default_store_locator_radii;
		$available_store_locator_radii = $wpgmza_default_store_locator_radii;
		
		if(isset($wpgmza_settings['wpgmza_store_locator_radii']) && preg_match_all('/\d+/', $wpgmza_settings['wpgmza_store_locator_radii'], $m))
			$available_store_locator_radii = $m[0];

        if (empty($list_markers_by) || !$list_markers_by) {
            /* first check what their old setting was before the new options */
            
            
            if ($listmarkers_checked == "CHECKED" && $listmarkers_advanced_checked == "") { 
                /* old basic mode enabled */
                $list_markers_by_checked[1] = "checked";
            }
            else if ($listmarkers_checked == "CHECKED" && $listmarkers_advanced_checked == "CHECKED") { 
                /* old advanced mode enabled */
                
                $list_markers_by_checked[2] = "checked";
                
            } else {
                $list_markers_by_checked[0] = "checked";
            }
        }

       	$show_distance_from_location_checked = isset($other_settings_data['show_distance_from_location']) && $other_settings_data['show_distance_from_location'] == "1" ? 'checked' : '';
		
		wp_localize_script('admin-wpgmaps', 'wpgmza_plugin_dir_url', plugin_dir_url(WPGMAPS_DIR));
		
        for ($i=0;$i<8;$i++) {
            if (!isset($list_markers_by_checked[$i])) { $list_markers_by_checked[$i] = ""; }
        }
        for ($i=0;$i<8;$i++) {
            if (!isset($list_markers_by_class[$i])) { $list_markers_by_class[$i] = ""; }
        }
        if (isset($other_settings_data['list_markers_by'])) {
        	if ($other_settings_data['list_markers_by'] == "0") {
        		$list_markers_by_sel_text = __("No marker list","wp-google-maps");
        	}
        	if ($other_settings_data['list_markers_by'] == "1") {
        		$list_markers_by_sel_text = __("Basic table","wp-google-maps");
        	}
        	if ($other_settings_data['list_markers_by'] == "4") {
        		$list_markers_by_sel_text = __("Basic list","wp-google-maps");
        	}
        	if ($other_settings_data['list_markers_by'] == "2") {
        		$list_markers_by_sel_text = __("Advanced table","wp-google-maps");
        	}
        	if ($other_settings_data['list_markers_by'] == "3") {
        		$list_markers_by_sel_text = __("Carousel","wp-google-maps");
        	}
			if ($other_settings_data['list_markers_by'] == "3") {
				$list_markers_by_sel_text = __("Modern","wp-google-maps");
			}
			if ($other_settings_data['list_markers_by'] == "7") {
				$list_markers_by_sel_text = __("Grid","wp-google-maps");
			}


        } else { 
        	$list_markers_by_sel_text = "";
    	}
        for ($i=0;$i<22;$i++) { if (!isset($wpgmza_max_zoom[$i])) { $wpgmza_max_zoom[$i] = ""; } }
        for ($i=0;$i<22;$i++) { if (!isset($wpgmza_min_zoom[$i])) { $wpgmza_min_zoom[$i] = ""; } }
        for ($i=0;$i<13;$i++) { if (!isset($push_in_map_placement_checked[$i])) { $push_in_map_placement_checked[$i] = ""; } }

        if (isset($other_settings_data['store_marker_listing_below'])) { $wpgmza_marker_listing_below = $other_settings_data['store_marker_listing_below']; } else { $wpgmza_marker_listing_below = 0; }


		$wpgmza_store_locator_enabled_checked = $wpgmza_store_locator_enabled == 1 ? 'checked' : '';

        $wpgmza_store_locator_distance_checked = $wpgmza_store_locator_distance == 1 ? 'checked' : '';

        $wpgmza_store_locator_below_checked = $wpgmza_store_locator_below == 1 ? 'checked' : '';
        
        $wpgmza_marker_listing_below_checked = $wpgmza_marker_listing_below == 1 ? 'checked' : '';
 
        $wpgmza_store_locator_bounce_checked = $wpgmza_store_locator_bounce == 1 ? 'checked' : '';

        $wpgmza_auto_night_enabled_checked = isset($other_settings_data['wpgmza_auto_night']) && $other_settings_data['wpgmza_auto_night'] == 1 ? 'checked' : '';
       
       	$wpgmza_store_locator_category_checked = isset($wpgmza_store_locator_category_enabled) && $wpgmza_store_locator_category_enabled == 1 ? 'checked' : '';
        
        $wpgmza_store_locator_hide_before_search_checked = $wpgmza_store_locator_hide_before_search == 1 ? 'checked' : '';
       
      	$wpgmza_store_locator_use_their_location_checked = $wpgmza_store_locator_use_their_location == 1 ? 'checked' : '';      

      	$wpgmza_store_locator_name_search_checked = $wpgmza_store_locator_name_search == 1 ? 'checked' : '';

		$store_locator_style = (empty($other_settings_data['store_locator_style']) ? 'legacy' : $other_settings_data['store_locator_style']);
		$store_locator_radius_style = (empty($other_settings_data['wpgmza_store_locator_radius_style']) ? 'legacy' : $other_settings_data['wpgmza_store_locator_radius_style']);
		
		$directions_box_style = (empty($other_settings_data['directions_box_style']) ? 'default' : $other_settings_data['directions_box_style']);
        
        if (isset($other_settings_data['sl_stroke_color'])) { $sl_stroke_color = $other_settings_data['sl_stroke_color']; }
        if (isset($other_settings_data['sl_stroke_opacity'])) { $sl_stroke_opacity = $other_settings_data['sl_stroke_opacity']; }
        if (isset($other_settings_data['sl_fill_color'])) { $sl_fill_color = $other_settings_data['sl_fill_color']; }
        if (isset($other_settings_data['sl_fill_opacity'])) { $sl_fill_opacity = $other_settings_data['sl_fill_opacity']; }

        if (!isset($sl_stroke_color) || $sl_stroke_color == "") {
            $sl_stroke_color = "FF0000";
        }
        if (!isset($sl_stroke_opacity) || $sl_stroke_opacity == "") {
            $sl_stroke_opacity = "0.25";
        }
        if ($sl_stroke_opacity == 0) {
            $sl_stroke_opacity = "0.00";
        }
        if (!isset($sl_fill_color) || $sl_fill_color == "") {
            $sl_fill_color = "FF0000";
        }
        if (!isset($sl_fill_opacity) || $sl_fill_opacity == "") {
			$sl_fill_opacity = "0.15";
        }
        if($sl_fill_opacity == 0){
            $sl_fill_opacity = "0.00";
        }
        
        if (isset($other_settings_data['iw_primary_color'])) { $iw_primary_color = $other_settings_data['iw_primary_color']; }
        if (isset($other_settings_data['iw_accent_color'])) { $iw_accent_color = $other_settings_data['iw_accent_color']; }
        if (isset($other_settings_data['iw_text_color'])) { $iw_text_color = $other_settings_data['iw_text_color']; }

        if (!isset($iw_primary_color) || $iw_primary_color == "") {
            $iw_primary_color = "2A3744";
        }
        if (!isset($iw_accent_color) || $iw_accent_color == "") {
            $iw_accent_color = "252F3A";
        }
        if (!isset($iw_text_color) || $iw_text_color == "") {
            $iw_text_color = "FFFFFF";
        }

		if (isset($other_settings_data['wpgmza_iw_type'])) { $infowwindow_sel_checked[$other_settings_data['wpgmza_iw_type']] = "checked"; $wpgmza_iw_class[$other_settings_data['wpgmza_iw_type']] = "wpgmza_mlist_selection_activate"; } else {  $wpgmza_iw_type = false; }



		for ($i=0;$i<5;$i++) {
            if (!isset($wpgmza_iw_class[$i])) { $wpgmza_iw_class[$i] = ""; }
        }
		for ($i=0;$i<5;$i++) {
            if (!isset($infowwindow_sel_checked[$i])) { $infowwindow_sel_checked[$i] = ""; }
        }	
		

        if ($infowwindow_sel_checked[0] == "checked") {
        	$infowwindow_sel_text = __("Default Infowindow","wp-google-maps");
        } else if ($infowwindow_sel_checked[1] == "checked") {
        	$infowwindow_sel_text = __("Modern Infowindow","wp-google-maps");
        } else if ($infowwindow_sel_checked[2] == "checked") {
        	$infowwindow_sel_text = __("Modern Plus Infowindow","wp-google-maps");
        }else if ($infowwindow_sel_checked[3] == "checked") {
        	$infowwindow_sel_text = __("Circular Infowindow","wp-google-maps");
        }else {
        	$infowwindow_sel_text = __("Currently using your selection chosen in the global settings","wp-google-maps");
        }


	    if (isset($other_settings_data['wpgmza_theme_selection'])) { $theme_sel_checked[$other_settings_data['wpgmza_theme_selection']] = "checked"; $wpgmza_theme_class[$other_settings_data['wpgmza_theme_selection']] = "wpgmza_theme_selection_activate"; } else {  $wpgmza_theme = false; $wpgmza_theme_class[0] = "wpgmza_theme_selection_activate"; }
	    for ($i=0;$i<10;$i++) {
	        if (!isset($wpgmza_theme_class[$i])) { $wpgmza_theme_class[$i] = ""; }
	    }
	    for ($i=0;$i<10;$i++) {
	        if (!isset($theme_sel_checked[$i])) { $theme_sel_checked[$i] = ""; }
	    }   
	    global $wpgmza_version;
        
	    if( isset( $other_settings_data['wpgmza_theme_data'] ) ){
	        $wpgmza_theme_data_custom = $other_settings_data['wpgmza_theme_data'];
	    } else {
	        $wpgmza_theme_data_custom  = '';
	    }
        
        $wpgmza_weather_layer_checked[0] = '';
        $wpgmza_weather_layer_checked[1] = '';
        $wpgmza_weather_layer_temp_type_checked[0] = '';
        $wpgmza_weather_layer_temp_type_checked[1] = '';
        
        $wpgmza_cloud_layer_checked[0] = '';
        $wpgmza_cloud_layer_checked[1] = '';
        $wpgmza_transport_layer_checked[0] = '';
        $wpgmza_transport_layer_checked[1] = '';
        
        
        if ($wpgmza_weather_option == 1) {
            $wpgmza_weather_layer_checked[0] = 'checked';
        } else {
            $wpgmza_weather_layer_checked[1] = 'checked';
        }
        if ($wpgmza_weather_option_temp_type == 1) {
            $wpgmza_weather_layer_temp_type_checked[0] = 'checked';
        } else {
            $wpgmza_weather_layer_temp_type_checked[1] = 'checked';
        }
        if ($wpgmza_cloud_option == 1) {
            $wpgmza_cloud_layer_checked[0] = 'checked';
        } else {
            $wpgmza_cloud_layer_checked[1] = 'checked';
        }
        if ($wpgmza_transport_option == 1) {
            $wpgmza_transport_layer_checked[0] = 'checked';
        } else {
            $wpgmza_transport_layer_checked[1] = 'checked';
        }

        $def_ul_marker = isset($other_settings_data['upload_default_ul_marker']) ? $other_settings_data['upload_default_ul_marker'] : "";
        if ($def_ul_marker == "") { $display_ul_marker = "<img src=\"".wpgmaps_get_plugin_url()."/images/marker.png\" />"; } else { $display_ul_marker = "<img src=\"".$def_ul_marker."\" />"; }

        $def_sl_marker = isset($other_settings_data['upload_default_sl_marker']) ? $other_settings_data['upload_default_sl_marker'] : "";
        if ($def_sl_marker == "") { $display_sl_marker = "<img src=\"".wpgmaps_get_plugin_url()."/images/marker.png\" />"; } else { $display_sl_marker = "<img src=\"".$def_sl_marker."\" />"; }
        
        $wpgmza_csv = "<a href=\"".wpgmaps_get_plugin_url()."/csv.php\" title=\"".__("Download this as a CSV file","wp-google-maps")."\">".__("Download this data as a CSV file","wp-google-maps")."</a>";

        

    }

    if ( $_GET['action'] == 'create-map-page' && isset( $_GET['map_id'] ) ) {
    	$res = wpgmza_get_map_data( $_GET['map_id'] );
    	

        // Set the post ID to -1. This sets to no action at moment
        $post_id = -1;
     
        // Set the Author, Slug, title and content of the new post
        $author_id = get_current_user_id();
        if ($author_id) {
	        $slug = 'map';
	        $title = $res->map_title;
	        $content = '[wpgmza id="'.$res->id.'"]';
	        

	        // do we have this slug?
	        $args_posts = array(
			    'post_type'      => 'page',
			    'post_status'    => 'any',
			    'name'           => $slug,
			    'posts_per_page' => 1,
			);
			$loop_posts = new WP_Query( $args_posts );
			if ( ! $loop_posts->have_posts() ) {
			    
			    // we dont
			    $post_id = wp_insert_post(
	                array(
	                    'comment_status'    =>   'closed',
	                    'ping_status'       =>   'closed',
	                    'post_author'       =>   $author_id,
	                    'post_name'         =>   $slug,
	                    'post_title'        =>   $title,
	                    'post_content'      =>  $content,
	                    'post_status'       =>   'publish',
	                    'post_type'         =>   'page'
	                )
	            );
	            echo '<script>window.location.href = "post.php?post='.$post_id.'&action=edit";</script>';
	            return;
			} else {
			    $loop_posts->the_post();
			    
			    // we do!
			    $post_id = wp_insert_post(
	                array(
	                    'comment_status'    =>   'closed',
	                    'ping_status'       =>   'closed',
	                    'post_author'       =>   $author_id,
	                    'post_name'         =>   $slug."-".$res->id,
	                    'post_title'        =>   $title,
	                    'post_content'      =>  $content,
	                    'post_status'       =>   'publish',
	                    'post_type'         =>   'page'
	                )
	            );
	            
	            echo '<script>window.location.href = "post.php?post='.$post_id.'&action=edit";</script>';
	            return;
			}
		} else {
			echo "There was a problem creating the map page.";
			return;
		}


        
            
        return;
   
 


    }
    if($_GET['action'] != "wizard"){

	    global $wpgmza_version;
	    
	    if (version_compare($wpgmza_version, '6.3.12', '<')) {
	    	$wpgmza_string_heatmaps = "<span class='update-nag update-blue'>".__("Please update your basic version to use this function.","wp-google-maps")."</span>";
	    } else {
			$wpgmza_string_heatmaps = "<span id=\"wpgmza_addheatmap_div\"><a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-google-maps-menu&action=add_heatmap&map_id=".$_GET['map_id']."' id='wpgmza_addheatmap' class='button-primary wpgmza-button__top-right' value='".__("Add a New Dataset","wp-google-maps")."' />".__("Add a New Dataset","wp-google-maps")."</a></span><div id=\"wpgmza_heatmap_holder\">".wpgmza_b_return_heatmaps_list($_GET['map_id'])."</div>";
	    }

    	if( function_exists( 'wpgmza_caching_notice_changes' ) ){    		
    		$wpgmza_caching_notices = wpgmza_caching_notice_changes( true, true );
    	} else {
    		$wpgmza_caching_notices = "";
    	}
		
		$open_layers_feature_coming_soon = '';
		$open_layers_feature_unavailable = '';
		$maps_engine_dialog = new WPGMZA\MapsEngineDialog();
		$maps_engine_dialog_html = $maps_engine_dialog->html();
		$gdpr_privacy_policy_notice = '';
		
		global $wpgmza;
		if($wpgmza->settings->engine == 'open-layers' && defined('WPGMZA_FILE'))
		{
			ob_start();
			include(plugin_dir_path(WPGMZA_FILE) . 'html/ol-feature-coming-soon.html.php');
			$open_layers_feature_coming_soon = ob_get_clean();
			
			ob_start();
			include(plugin_dir_path(WPGMZA_FILE) . 'html/ol-feature-unavailable.html.php');
			$open_layers_feature_unavailable = ob_get_clean();
		}
		
		if(property_exists($wpgmza, 'gdprCompliance'))
			$gdpr_privacy_policy_notice = $wpgmza->gdprCompliance->getPrivacyPolicyNoticeHTML();
		else
			$gdpr_privacy_policy_notice = '';
		
		// Admin marker table
		$ajaxParameters = array(
			'map_id' => $_GET['map_id']
		);
		
		$map = WPGMZA\Map::createInstance($_GET['map_id']);
		
		$directionsBoxSettingsPanel = new WPGMZA\DirectionsBoxSettingsPanel($map);
		
		$adminMarkerTable = WPGMZA\AdminMarkerDataTable::createInstance($ajaxParameters);
		$adminMarkerTableHTML = $adminMarkerTable->html();
		
		// 3rd party integration panel
		$integrationPanel = new WPGMZA\DOMDocument();
		$integrationPanel->loadHTML('<div id="wpgmza-integration-panel"/>');
		$integrationPanel = apply_filters('wpgmza_map_integration_panel', $integrationPanel, $map);
		
		// Advanced settings marker icon picker
		$options = array(
			'name'			=> 'upload_default_marker',
			'retina_name'	=> 'upload_default_marker_retina'
		);
		
		if(!empty($map->default_marker))
			$options['value'] = new WPGMZA\MarkerIcon($map->default_marker);
		
		$advancedSettingsMarkerIconPicker = new WPGMZA\MarkerIconPicker($options);
		
		$map = \WPGMZA\Map::createInstance($_GET['map_id']);
		$themePanel = new WPGMZA\ThemePanel($map);
		
    echo "
		$open_layers_feature_coming_soon
		$open_layers_feature_unavailable
		$maps_engine_dialog_html
	
       <div class='wrap'>
    
    
    
    
    
    
        <!--<h1>WP Google Maps <span id='wpgmza-title-label' class='wpgmza-label-amber'><small>$addon_text</small></span></h1>-->
        <div class='wide'>
                <h2>
					<!--<a href=\"admin.php?page=wp-google-maps-menu&action=new\" class=\"add-new-h2 add-new-editor\">".__("New","wp-google-maps")."</a> <div class='update-nag update-blue update-slim' id='wpmgza_unsave_notice' style='display:none;'> Unsaved data will be lost</div>-->
				</h2>

				$gdpr_privacy_policy_notice
    
        <form action='' method='post' id='wpgmaps_options' name='wpgmza_map_form'>

        <div id=\"wpgmaps_tabs\">
                <ul>
                        <li><a href=\"#tabs-1\">".__("General Settings","wp-google-maps")."</a></li>
                        <li><a href=\"#tabs-7\">".__("Themes","wp-google-maps")."</a></li>
                        <li><a href=\"#tabs-2\">".__("Directions","wp-google-maps")."</a></li>
                        <li><a href=\"#tabs-3\">".__("Store Locator","wp-google-maps")."</a></li>
                        <li><a href=\"#tabs-4\">".__("Advanced Settings","wp-google-maps")."</a></li>
                        <li><a href=\"#tabs-5\">".__("Marker Listing Options","wp-google-maps")."</a></li>
                        <li><a href=\"#marker-filtering\">".__("Marker Filtering","wp-google-maps")."</a></li>
                        ".apply_filters("wpgmaps_filter_pro_map_editor_tabs","")."
                </ul>
                <div id=\"tabs-1\">
                	<h3>".__("Map Settings", "wp-google-maps").":</h3>

                        <input type='hidden' name='http_referer' value='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "' />
                        <input type='hidden' name='wpgmza_id' id='wpgmza_id' value='".$res->id."' />
                        <input id='wpgmza_start_location' name='wpgmza_start_location' type='hidden' size='40' maxlength='100' value='".$res->map_start_location."' />
                        <select id='wpgmza_start_zoom' name='wpgmza_start_zoom' style=\"display:none;\">
                            <option value=\"1\" ".$wpgmza_zoom[1].">1</option>
                            <option value=\"2\" ".$wpgmza_zoom[2].">2</option>
                            <option value=\"3\" ".$wpgmza_zoom[3].">3</option>
                            <option value=\"4\" ".$wpgmza_zoom[4].">4</option>
                            <option value=\"5\" ".$wpgmza_zoom[5].">5</option>
                            <option value=\"6\" ".$wpgmza_zoom[6].">6</option>
                            <option value=\"7\" ".$wpgmza_zoom[7].">7</option>
                            <option value=\"8\" ".$wpgmza_zoom[8].">8</option>
                            <option value=\"9\" ".$wpgmza_zoom[9].">9</option>
                            <option value=\"10\" ".$wpgmza_zoom[10].">10</option>
                            <option value=\"11\" ".$wpgmza_zoom[11].">11</option>
                            <option value=\"12\" ".$wpgmza_zoom[12].">12</option>
                            <option value=\"13\" ".$wpgmza_zoom[13].">13</option>
                            <option value=\"14\" ".$wpgmza_zoom[14].">14</option>
                            <option value=\"15\" ".$wpgmza_zoom[15].">15</option>
                            <option value=\"16\" ".$wpgmza_zoom[16].">16</option>
                            <option value=\"17\" ".$wpgmza_zoom[17].">17</option>
                            <option value=\"18\" ".$wpgmza_zoom[18].">18</option>
                            <option value=\"19\" ".$wpgmza_zoom[19].">19</option>
                            <option value=\"20\" ".$wpgmza_zoom[20].">20</option>
                            <option value=\"21\" ".$wpgmza_zoom[21].">21</option>
                        </select>

                    <table>
                        <tr>

                            <td>".__("Short code","wp-google-maps").":</td>
                            <td><input type='text' readonly name='shortcode' class='wpgmza_copy_shortcode' style='font-size:18px; text-align:center;' onclick=\"this.select()\" value='[wpgmza id=\"".$res->id."\"]' /> <small class='wpgmza-info__small wpgmza-info-right'><i>".__("copy this into your post or page to display the map","wp-google-maps").". ".__(sprintf("Or <a href='%s' target='BLANK'>click here to automatically create a Map Page now</a>.","admin.php?page=wp-google-maps-menu&action=create-map-page&map_id=".$res->id),"wp-google-maps")."</i></td>
                        </tr>
                        <tr>
                            <td>".__("Map Name","wp-google-maps").":</td>
                            <td><input id='wpgmza_title' name='wpgmza_title' class='regular-text' type='text' size='20' maxlength='50' value='".$res->map_title."' /></td>
                        </tr>
                        <tr>
                            <td>".__("Zoom Level","wp-google-maps").":</td>
                            <td>
                            <input type=\"text\" id=\"amount\" style=\"display:none;\"  value=\"$res->map_start_zoom\"><div id=\"slider-range-max\"></div>
                            </td>
                        </tr>

                        <tr>
                                     <td>".__("Width","wp-google-maps").":</td>
                                     <td>
                                     <input id='wpgmza_width' name='wpgmza_width' type='text' size='4' maxlength='4' value='".$res->map_width."' />
                                     <select id='wpgmza_map_width_type' name='wpgmza_map_width_type'>
                                        <option value=\"px\" $wpgmza_map_width_type_px>px</option>
                                        <option value=\"%\" $wpgmza_map_width_type_percentage>%</option>
                                     </select>
                                     <small><em>".__("Set to 100% for a responsive map","wp-google-maps")."</em></small>

                                    </td>
                                </tr>
                                <tr>
                                    <td>".__("Height","wp-google-maps").":</td>
                                    <td><input id='wpgmza_height' name='wpgmza_height' type='text' size='4' maxlength='4' value='".$res->map_height."' />
                                     <select id='wpgmza_map_height_type' name='wpgmza_map_height_type'>
                                        <option value=\"px\" $wpgmza_map_height_type_px>px</option>
                                        <option value=\"%\" $wpgmza_map_height_type_percentage>%</option>
                                     </select><span style='display:none; width:200px; font-size:10px;' id='wpgmza_height_warning'>".__("We recommend that you leave your height in PX. Depending on your theme, using % for the height may break your map.","wp-google-maps")."</span>

                                    </td>
                                </tr>

                    </table>
                    
            
            
                </div>

                <div id=\"tabs-7\" class='make-left wpgmza-open-layers-feature-unavailable'>
					" . $themePanel->html . "
                </div>
                <div id=\"tabs-2\">
					{$directionsBoxSettingsPanel->html}
                </div>
                <div id=\"tabs-3\">
                	<h3>".__("General options","wp-google-maps").":</h3>
                    <table class='' id='wpgmaps_directions_options'>
                        <tr>
                            <td width='400'>".__("Enable Store Locator","wp-google-maps").":</td>
                            <td><div class='switch'>
                                    <input type='checkbox' id='wpgmza_store_locator' name='wpgmza_store_locator' class='postform cmn-toggle cmn-toggle-yes-no' ".$wpgmza_store_locator_enabled_checked."> <label class='cmn-override-big' for='wpgmza_store_locator' data-on='".__("Yes","wp-google-maps")."' data-off='".__("No","wp-google-maps")."''></label>
                                </div>
                            </td>
                        </tr>
						
						" . ($wpgmza->settings->user_interface_style == 'legacy' ? "
						<tr>
							<td width='400'>".__("Store Locator Style","wp-google-maps").":</td>
							<td>
								<ul>
									<li>
										<input type='radio' 						
											name='store_locator_style' 
											value='legacy'"
											. ($store_locator_style == 'legacy' ? 'checked="checked"' : '') . 
											"/>" 
											. __("Legacy", "wp-google-maps") . 
											" 
									</li>
									<li>
										<input type='radio' 
											name='store_locator_style' 
											value='modern'"
											. ($store_locator_style == 'modern' ? 'checked="checked"' : '') . 
											"/>" 
											. __("Modern", "wp-google-maps") . 
											"
									</li>
								</ul>
							</td>
						</tr>
						" : "
						<tr>
							<td>
								" . __("Store Locator Style", "wp-google-maps") . "
							</td>
							<td>
								" . 
								sprintf(
									__("Looking for styling settings? Try our new <a href='%s' target='_blank'>User Interface Style</a> setting.", "wp-google-maps")
								, admin_url('admin.php?page=wp-google-maps-menu-settings')
								)
								. "
							</td>
						</tr>
						") . "
						
						<tr>
							<td>
								".__("Search Area", "wp-google-maps")."
							</td>
							<td>
								<ul>
									<li>
										<input type='radio'
											name='store_locator_search_area'
											value='radial'
											" . (!$map->store_locator_search_area || $map->store_locator_search_area == 'radial' ? 'checked="checked"' : '') . "
											/>
										" . __("Radial", "wp-google-maps") . "
										
										<p>
											<small>
												" . __("Allows the user to select a radius from a predefined list", "wp-google-maps") . "
											</small>
										</p>
									</li>
									<li>
										<input type='radio'
											name='store_locator_search_area'
											value='auto'
											" . ($map->store_locator_search_area == 'auto' ? 'checked="checked"' : '') . "
											/>
										" . __("Auto", "wp-google-maps") . "
										
										<p>
											<small>
												" . __("Intelligently detects the zoom level based on the location entered", "wp-google-maps") . "
											</small>
										</p>
									</li>
								<ul>
							</td>
						</tr>
						
						<tr data-search-area='radial'>
							<td width='200'>".__("Radius Style","wp-google-maps").":</td>
							<td>
								<ul>
									<li>
										<input type='radio' 						
											name='wpgmza_store_locator_radius_style' 
											value='legacy'"
											. ($store_locator_radius_style == 'legacy' ? 'checked="checked"' : '') . 
											"/>" 
											. __("Legacy", "wp-google-maps") . 
											" 
									</li>
									<li>
										<input type='radio' 
											name='wpgmza_store_locator_radius_style' 
											value='modern'"
											. ($store_locator_radius_style == 'modern' ? 'checked="checked"' : '') . 
											"/>" 
											. __("Modern", "wp-google-maps") . 
											"
									</li>
								</ul>
							</td>
						</tr>
						<tr data-search-area='radial'>
                            <td>".__("Default radius","wp-google-maps").":</td>
                            <td>
								<div>";

									$suffix = ($wpgmza_store_locator_distance == 1 ? __('mi', 'wp-google-maps') : __('km', 'wp-google-maps'));

									echo "<select name='wpgmza_store_locator_default_radius' class='wpgmza-store-locator-default-radius'>";

									$default_radius = '10';
									if(!empty($other_settings_data['store_locator_default_radius']))
										$default_radius = $other_settings_data['store_locator_default_radius'];
									
									foreach($available_store_locator_radii as $radius)
									{
										$selected = ($radius == $default_radius ? 'selected=\"selected\"' : '');
										echo "<option value='$radius' $selected>{$radius}{$suffix}</option>";
									}

									echo "
								</div>
                            </td>
                        </tr>
						
						<tr data-search-area='auto'>
                            <td>".__("Maximum zoom","wp-google-maps").":</td>
                            <td>
								<input name='store_locator_auto_area_max_zoom'
									type='number'
									min='1'
									max='21'
									value='" . ($map->store_locator_auto_area_max_zoom ? $map->store_locator_auto_area_max_zoom : '19') . "'
									/>
                            </td>
                        </tr>
						
                        <tr>
                            <td width='400'>".__("Restrict to country","wp-google-maps").":</td>
                            <td>";
                            if( function_exists('wpgmza_return_country_tld_array') ){ 

                                echo "<select name='wpgmza_store_locator_restrict' id='wpgmza_store_locator_restrict'>";
                                
                                $countries = wpgmza_return_country_tld_array();

                                if( $countries ){
                                    echo "<option value=''>".__('No country selected', 'wp-google-maps')."</option>";
                                    foreach( $countries as $key => $val ){

                                        if( $key == $wpgmza_store_locator_restrict ){ $selected = 'selected'; } else { $selected = ''; }
                                        echo "<option value='$key' $selected>$val</option>";

                                    }

                                }
                                echo "</select></td>";
                            } else {
                            	echo "
                            <input type=\"text\" name=\"wpgmza_store_locator_restrict\" id=\"wpgmza_store_locator_restrict\" value=\"$wpgmza_store_locator_restrict\" style='width:110px;' placeholder='Country TLD'> <small><em>".__("Insert country TLD. For example, use DE for Germany.","wp-google-maps")." ".__("Leave blank for no restrictions.","wp-google-maps")."</em></small></td>";
                        }
                        echo "
                        </tr>

                        <tr>
                            <td>".__("Show distance in","wp-google-maps").":</td>
                            <td>
                            	<div class='switch'>
                                        <input type='checkbox' id='wpgmza_store_locator_distance' name='wpgmza_store_locator_distance' class='postform cmn-toggle cmn-toggle-yes-no' ".$wpgmza_store_locator_distance_checked."> <label class='cmn-override-big-wide' for='wpgmza_store_locator_distance' data-on='".__("Miles","wp-google-maps")."' data-off='".__("Kilometers","wp-google-maps")."''></label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>".__("Store Locator Placement","wp-google-maps").":</td>
                            <td>
                            	<div class='switch'>
                                        <input type='checkbox' id='wpgmza_store_locator_position' name='wpgmza_store_locator_position' class='postform cmn-toggle cmn-toggle-yes-no' ".$wpgmza_store_locator_below_checked."> <label class='cmn-override-big-wide' for='wpgmza_store_locator_position' data-on='".__("Below Map","wp-google-maps")."' data-off='".__("Above Map","wp-google-maps")."''></label>
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>".__("Allow category selection","wp-google-maps").":</td>
                            <td>
                            	<div class='switch'>
                                        <input type='checkbox' id='wpgmza_store_locator_category_enabled' name='wpgmza_store_locator_category_enabled' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_store_locator_category_checked."> <label for='wpgmza_store_locator_category_enabled'></label>
                                </div>
                            </td>
                        </tr>
						<tr>
                            <td width='400'>".__("Allow users to use their location as the starting point","wp-google-maps").":</td>
                            <td>
                                <div class='switch'>
                                        <input type='checkbox' id='wpgmza_store_locator_use_their_location' name='wpgmza_store_locator_use_their_location' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_store_locator_use_their_location_checked."> <label for='wpgmza_store_locator_use_their_location'></label>
                                </div>
                            </td>
                        </tr>
                        <tr class='wpgmza-store-locator-radial-setting'>
                            <td width='400'>".__("Show center point as an icon","wp-google-maps").":</td>
                            <td>
                                <div class='switch'>
                                        <input type='checkbox' id='wpgmza_store_locator_bounce' name='wpgmza_store_locator_bounce' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_store_locator_bounce_checked."> <label for='wpgmza_store_locator_bounce'></label>
                                </div>
                            </td>
                        </tr>
                        <tr id='wpgmza_store_locator_bounce_conditional' style='display:none;'>
                            <td><label for=\"upload_default_sl_marker\">".__("Default Icon","wp-google-maps")."</label></td>
							<td><span id=\"wpgmza_mm_sl\">$display_sl_marker</span> <input id=\"upload_default_sl_marker\" name=\"upload_default_sl_marker\" type='hidden' size='35' class='regular-text' maxlength='700' value='".$def_sl_marker."' /> <input style='position: relative;' class='wpgmza_general_btn' id=\"upload_default_sl_marker_btn\" type=\"button\" value=\"".__("Upload Icon","wp-google-maps")."\"  /> <input class='wpgmza_general_btn wpgmza-marker-library wpgmza-marker-directions-library' data-target-name='upload_default_sl_marker' type=\"button\" value=\"".__("Marker Library","wp-google-maps")."\"  /> <a class='wpgmza_file_select_btn' style='position: relative;' href=\"javascript:void(0);\" onClick=\"document.forms['wpgmza_map_form'].upload_default_sl_marker.value = ''; var span = document.getElementById('wpgmza_mm_sl'); while( span.firstChild ) { span.removeChild( span.firstChild ); } span.appendChild( document.createTextNode('')); return false;\" title=\"Reset to default\">Reset</a> &nbsp; &nbsp;</td>
                        </tr>
                        <tr>
                            <td>".__("Marker animation","wp-google-maps").": </td>
                            <td>
                                <select name=\"wpgmza_sl_animation\" id=\"wpgmza_sl_animation\">
                                    <option value=\"0\" ".$wpgmza_sl_animation[0].">".__("None","wp-google-maps")."</option>
                                    <option value=\"1\" ".$wpgmza_sl_animation[1].">".__("Bounce","wp-google-maps")."</option>
                                    <option value=\"2\" ".$wpgmza_sl_animation[2].">".__("Drop","wp-google-maps")."</option>
                            </td>
                        </tr>

                        <tr>
                            <td width='400'>".__("Hide all markers until a search is done","wp-google-maps").":</td>
                            <td>
                                <div class='switch'>
                                        <input type='checkbox' id='wpgmza_store_locator_hide_before_search' name='wpgmza_store_locator_hide_before_search' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_store_locator_hide_before_search_checked."> <label for='wpgmza_store_locator_hide_before_search'></label>
                                </div>

                            </td>
                        </tr>
                        <tr style='height:20px;'>
                            <td></td>
                            <td></td>

                        </tr>
                        <tr>
                            <td>".__("Query String","wp-google-maps").":</td>
                            <td><input type=\"text\" name=\"wpgmza_store_locator_query_string\" id=\"wpgmza_store_locator_query_string\" value=\"$wpgmza_store_locator_query_string\">
                            </td>
                        </tr>
                        <tr>
                            <td>".__("Default address","wp-google-maps").":</td>
                            <td><input type=\"text\" name=\"wpgmza_store_locator_default_address\" id=\"wpgmza_store_locator_default_address\" value=\"".esc_attr($wpgmza_store_locator_default_address)."\">
                            </td>
                        </tr>

                        <tr>
                            <td width='400'>".__("Enable title search","wp-google-maps").":</td>
                            <td>

                                <div class='switch'>
                                        <input type='checkbox' id='wpgmza_store_locator_name_search' name='wpgmza_store_locator_name_search' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_store_locator_name_search_checked."> <label for='wpgmza_store_locator_name_search'></label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>".__("Title search String","wp-google-maps").":</td>
                            <td><input type=\"text\" name=\"wpgmza_store_locator_name_string\" id=\"wpgmza_store_locator_name_string\" value=\"$wpgmza_store_locator_name_string\">
                            </td>
                        </tr>
                        <tr class='wpgmza-store-locator-radial-setting'>
                            <td>" . __( "Not found message" ,"wp-google-maps" ) . ":</td>
                            <td><input type=\"text\" name=\"wpgmza_store_locator_not_found_message\" id=\"wpgmza_store_locator_not_found_message\" value=\"".esc_attr($wpgmza_store_locator_not_found_message)."\">
                            </td>
                        </tr>
                        <tr class='wpgmza-store-locator-radial-setting'>
                            <td><h3>".__("Style options","wp-google-maps").":</h3></td>
                            <td></td>
                        </tr>
                        <tr class='wpgmza-store-locator-radial-setting'>
                            <td>
                                ".__("Line color","wp-google-maps")."
                            </td>
                            <td>
                                <input id=\"sl_stroke_color\" name=\"sl_stroke_color\" type=\"text\" class=\"color\" value=\"$sl_stroke_color\" />
                            </td>
                        </tr>
                        <tr class='wpgmza_legacy_sl_style_option_area wpgmza-store-locator-radial-setting'>
                            <td>
                                ".__("Line opacity","wp-google-maps")."
                            </td>
                            <td>
                                <input id=\"sl_stroke_opacity\" name=\"sl_stroke_opacity\" type=\"text\" value=\"$sl_stroke_opacity\" /> ".__("(0 - 1.0) example: 0.5 for 50%","wp-google-maps")."
                            </td>
                        </tr>
                        <tr class='wpgmza_legacy_sl_style_option_area wpgmza-store-locator-radial-setting'>
                            <td>
                                ".__("Fill color","wp-google-maps")."
                            </td>
                            <td>
                                <input id=\"sl_fill_color\" name=\"sl_fill_color\" type=\"text\" class=\"color\" value=\"$sl_fill_color\" />
                            </td>
                        </tr>
                        <tr class='wpgmza_legacy_sl_style_option_area wpgmza-store-locator-radial-setting'>
                            <td>
                                ".__("Fill opacity","wp-google-maps")."
                            </td>
                            <td>
                                <input id=\"sl_fill_opacity\" name=\"sl_fill_opacity\" type=\"text\" value=\"$sl_fill_opacity\" /> ".__("(0 - 1.0) example: 0.5 for 50%","wp-google-maps")."
                            </td>
                        </tr>
                                 

                        </table>
                        <p><em>".__('View','wp-google-maps')." <a href='http://wpgmaps.com/documentation/store-locator' target='_BLANK'>".__('Store Locator Documentation','wp-google-maps')."</a></em></p>
                        <p><em>Please note: the store locator functionality is still in Beta. If you find any bugs, please <a href='http://wpgmaps.com/contact-us/'>let us know</a></em></p>


                        
                            

            
    
                </div>
                <div id=\"tabs-4\">
                	<h3>".__("Advanced Settings:", "wp-google-maps")."</h3>
                    <table class='' id='wpgmaps_advanced_options'>
                        <tr>
                            <td><label for=\"upload_default_marker\">".__("Default Marker Image","wp-google-maps")."</label></td>
							<td id='advanced-settings-marker-icon-picker-container'>
								{$advancedSettingsMarkerIconPicker->html}
                            </td>
                        </tr>

                        <tr>
                            <td><label for=\"wpgmza_map_type\">".__("Map type","wp-google-maps")."</label></td>
                            <td>
								<select id='wpgmza_map_type' name='wpgmza_map_type' class='postform'>
									<option value=\"1\" ".$wpgmza_map_type[1].">".__("Roadmap","wp-google-maps")."</option>
									<option value=\"2\" ".$wpgmza_map_type[2].">".__("Satellite","wp-google-maps")."</option>
									<option value=\"3\" ".$wpgmza_map_type[3].">".__("Hybrid","wp-google-maps")."</option>
									<option value=\"4\" ".$wpgmza_map_type[4].">".__("Terrain","wp-google-maps")."</option>
								</select>
								<div class='make-left wpgmza-open-layers-feature-unavailable'></div>
                            </td>
							
                        </tr>
                        
                        <tr>
                            <td><label for=\"wpgmza_map_align\">".__("Map Alignment","wp-google-maps")."</label></td>
                            <td><select id='wpgmza_map_align' name='wpgmza_map_align' class='postform'>
                                <option value=\"1\" ".$wpgmza_map_align[1].">".__("Left","wp-google-maps")."</option>
                                <option value=\"2\" ".$wpgmza_map_align[2].">".__("Center","wp-google-maps")."</option>
                                <option value=\"3\" ".$wpgmza_map_align[3].">".__("Right","wp-google-maps")."</option>
                                <option value=\"4\" ".$wpgmza_map_align[4].">".__("None","wp-google-maps")."</option>
                            </select>
                            </td>
                        </tr>

                        <tr>
                            <td><label for=\"wpgmza_show_user_location\">".__("Show User's Location?","wp-google-maps")."</label></td>
                            <td>
                            <div class='switch'>
                               	<input 
									type='checkbox' 
									id='wpgmza_show_user_location' 
									name='wpgmza_show_user_location' 
									class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_show_user_location[1]."> 
								<label 
									class='wpgmza-geolocation-setting'
									for='wpgmza_show_user_location' 
									data-on='".__("Yes","wp-google-maps")."' 
									data-off='".__("No","wp-google-maps")."''></label>
                            </div> 
                            </td>
                        </tr>
                        <tr id='wpgmza_show_user_location_conditional' style='display:none;'>
                            <td><label for=\"upload_default_ul_marker\">".__("Default User Location Icon","wp-google-maps")."</label></td>
                            <td><span id=\"wpgmza_mm_ul\">$display_ul_marker</span> <input id=\"upload_default_ul_marker\" name=\"upload_default_ul_marker\" type='hidden' size='35' class='regular-text' maxlength='700' value='".$def_ul_marker."' /> <input style='position: relative;' class='wpgmza_general_btn' id=\"upload_default_ul_marker_btn\" type=\"button\" value=\"".__("Upload Icon","wp-google-maps")."\"  /> <a class='wpgmza_file_select_btn' style='position: relative;' href=\"javascript:void(0);\" onClick=\"document.forms['wpgmza_map_form'].upload_default_ul_marker.value = ''; var span = document.getElementById('wpgmza_mm_ul'); while( span.firstChild ) { span.removeChild( span.firstChild ); } span.appendChild( document.createTextNode('')); return false;\" title=\"Reset to default\">Reset</a> &nbsp; &nbsp;</td>
                        </tr>

                        <tr>
                        
                        <td><label for=\"wpgmza_jump_to_nearest_marker_on_initialization\">".__("Jump to nearest marker on initialization?","wp-google-maps")."</label></td>
                        <td>
                        <div class='switch'>
                               <input 
                                type='checkbox' 
                                id='wpgmza_jump_to_nearest_marker_on_initialization' 
                                name='wpgmza_jump_to_nearest_marker_on_initialization' 
                                class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_jump_to_nearest_marker_on_initialization[1]."> 
                            <label 
                                class='wpgmza-geolocation-setting'
                                for='wpgmza_jump_to_nearest_marker_on_initialization' 
                                data-on='".__("Yes","wp-google-maps")."' 
                                data-off='".__("No","wp-google-maps")."''></label>
                        </div>
                        </td>
                        </tr> 

                        <tr>
                        <td><label for=\"wpgmza_automatically_pan_to_users_location\">".__("Automatically pan to users location?","wp-google-maps")."</label></td>
                        <td>
                        <div class='switch'>
                            <input type='checkbox' id='wpgmza_automatically_pan_to_users_location' name='wpgmza_automatically_pan_to_users_location' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_automatically_pan_to_users_location[1]."> <label for='wpgmza_automatically_pan_to_users_location' data-on='".__("Yes","wp-google-maps")."' data-off='".__("No","wp-google-maps")."''></label>
                            <label 
                            class='wpgmza-geolocation-setting'
                            for='wpgmza_automatically_pan_to_users_location' 
                            data-on='".__("Yes","wp-google-maps")."' 
                            data-off='".__("No","wp-google-maps")."''></label>
                        </div>
                        </td>
                        </tr>

                        <tr id='wpgmza_override_user_location_setting' style='display:none;'>
                        <td><label for=\"wpgmza_override_users_location_zoom_level\">".__("Override the zoom level when the users location is detected: ","wp-google-maps")."</label></td>
                        <td>
                        <div class='switch'>
                            <input type='checkbox' id='wpgmza_override_users_location_zoom_level' name='wpgmza_override_users_location_zoom_level' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_override_users_location_zoom_level[1]."> <label for='wpgmza_override_users_location_zoom_level' data-on='".__("Yes","wp-google-maps")."' data-off='".__("No","wp-google-maps")."''></label>
                        </div>
                        </td>
                        </tr>

                        <tr id='wpgmza_override_user_location_zoom_value_option' style='display:none;'>
                        <td width='320'><label for=\"wpgmza_override_users_location_zoom_levels\">".__("Override users location zoom level:","wp-google-maps")."</label></td>
                        <td>
                            <select id='wpgmza_override_users_location_zoom_levels' name='wpgmza_override_users_location_zoom_levels' >
                                <option value=\"0\" ".$wpgmza_override_users_location_zoom_levels[0].">0</option>
                                <option value=\"1\" ".$wpgmza_override_users_location_zoom_levels[1].">1</option>
                                <option value=\"2\" ".$wpgmza_override_users_location_zoom_levels[2].">2</option>
                                <option value=\"3\" ".$wpgmza_override_users_location_zoom_levels[3].">3</option>
                                <option value=\"4\" ".$wpgmza_override_users_location_zoom_levels[4].">4</option>
                                <option value=\"5\" ".$wpgmza_override_users_location_zoom_levels[5].">5</option>
                                <option value=\"6\" ".$wpgmza_override_users_location_zoom_levels[6].">6</option>
                                <option value=\"7\" ".$wpgmza_override_users_location_zoom_levels[7].">7</option>
                                <option value=\"8\" ".$wpgmza_override_users_location_zoom_levels[8].">8</option>
                                <option value=\"9\" ".$wpgmza_override_users_location_zoom_levels[9].">9</option>
                                <option value=\"10\" ".$wpgmza_override_users_location_zoom_levels[10].">10</option>
                                <option value=\"11\" ".$wpgmza_override_users_location_zoom_levels[11].">11</option>
                                <option value=\"12\" ".$wpgmza_override_users_location_zoom_levels[12].">12</option>
                                <option value=\"13\" ".$wpgmza_override_users_location_zoom_levels[13].">13</option>
                                <option value=\"14\" ".$wpgmza_override_users_location_zoom_levels[14].">14</option>
                                <option value=\"15\" ".$wpgmza_override_users_location_zoom_levels[15].">15</option>
                                <option value=\"16\" ".$wpgmza_override_users_location_zoom_levels[16].">16</option>
                                <option value=\"17\" ".$wpgmza_override_users_location_zoom_levels[17].">17</option>
                                <option value=\"18\" ".$wpgmza_override_users_location_zoom_levels[18].">18</option>
                                <option value=\"19\" ".$wpgmza_override_users_location_zoom_levels[19].">19</option>
                                <option value=\"20\" ".$wpgmza_override_users_location_zoom_levels[20].">20</option>
                                <option value=\"21\" ".$wpgmza_override_users_location_zoom_levels[21].">21</option>
                            </select>
                        </td>
                        </tr> 

						
						<tr>
                             <td>".__("Show distance from location?","wp-google-maps")."</td>
                             <td>
                                <div class='switch wpgmza-geolocation-setting'>
								
									<input type='checkbox' 
										id='wpgmza_show_distance_from_location' 
										name='wpgmza_show_distance_from_location'
										class='postform cmn-toggle cmn-toggle-round-flat'
										value='1'
										$show_distance_from_location_checked
										/>
									
									<label 
										for='wpgmza_show_distance_from_location'
										data-on='".__("Yes","wp-google-maps")."' 
										data-off='".__("No","wp-google-maps")."''></label>
								
									<small>
										<em>
											".__("This feature will use the users location (where available) or the searched address when a store locator search is performed.","wp-google-maps")."
										</em>
									</small>
									
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td><label for=\"wpgmza_click_open_link\">".__("Click marker opens link","wp-google-maps")."</label></td>
                            <td>
                            <div class='switch'>
                               	<input type='checkbox' id='wpgmza_click_open_link' name='wpgmza_click_open_link' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_click_open_link[1]."> <label for='wpgmza_click_open_link' data-on='".__("Yes","wp-google-maps")."' data-off='".__("No","wp-google-maps")."''></label>
                            </div>
                            </td>
                        </tr>
                        <tr>
                            <td><label for=\"wpgmza_fit_maps_bounds_to_markers\">".__("Fit map bounds to markers?","wp-google-maps")."</label></td>
                            <td>
                                <div class='switch'>
                                    <input type='checkbox' id='wpgmza_fit_maps_bounds_to_markers' name='wpgmza_fit_maps_bounds_to_markers' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_fit_maps_bounds_to_markers[1]."> <label for='wpgmza_fit_maps_bounds_to_markers' data-on='".__("Yes","wp-google-maps")."' data-off='".__("No","wp-google-maps")."''></label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><label for=\"wpgmza_fit_maps_bounds_to_markers_after_filtering\">".__("Fit map bounds to markers after filtering?","wp-google-maps")."</label></td>
                            <td>
                                <div class='switch'>
                                    <input type='checkbox' id='wpgmza_fit_maps_bounds_to_markers_after_filtering' name='wpgmza_fit_maps_bounds_to_markers_after_filtering' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_fit_maps_bounds_to_markers_after_filtering[1]."> <label for='wpgmza_fit_maps_bounds_to_markers_after_filtering' data-on='".__("Yes","wp-google-maps")."' data-off='".__("No","wp-google-maps")."''></label>
                                </div>
                            </td>
                        </tr>
						<tr>
                        <td><label for=\"wpgmza_hide_point_of_interest\">".__("Hide point of interest","wp-google-maps")."</label></td>
                        <td>
                        <div class='switch'>
                               <input type='checkbox' id='wpgmza_hide_point_of_interest' name='wpgmza_hide_point_of_interest' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_hide_point_of_interest[1]."> <label for='wpgmza_hide_point_of_interest' data-on='".__("Yes","wp-google-maps")."' data-off='".__("No","wp-google-maps")."''></label>
                        </div>
                        </td>
                        </tr>
                        
                        <tr>
                            <td width='320'><label for=\"wpgmza_max_zoom\">".__("Maximum Zoom Out Level","wp-google-maps")."</label></td>
                            <td>
                                <select id='wpgmza_max_zoom' name='wpgmza_max_zoom' >
                                    <option value=\"0\" ".$wpgmza_max_zoom[0].">0</option>
                                    <option value=\"1\" ".$wpgmza_max_zoom[1].">1</option>
                                    <option value=\"2\" ".$wpgmza_max_zoom[2].">2</option>
                                    <option value=\"3\" ".$wpgmza_max_zoom[3].">3</option>
                                    <option value=\"4\" ".$wpgmza_max_zoom[4].">4</option>
                                    <option value=\"5\" ".$wpgmza_max_zoom[5].">5</option>
                                    <option value=\"6\" ".$wpgmza_max_zoom[6].">6</option>
                                    <option value=\"7\" ".$wpgmza_max_zoom[7].">7</option>
                                    <option value=\"8\" ".$wpgmza_max_zoom[8].">8</option>
                                    <option value=\"9\" ".$wpgmza_max_zoom[9].">9</option>
                                    <option value=\"10\" ".$wpgmza_max_zoom[10].">10</option>
                                    <option value=\"11\" ".$wpgmza_max_zoom[11].">11</option>
                                    <option value=\"12\" ".$wpgmza_max_zoom[12].">12</option>
                                    <option value=\"13\" ".$wpgmza_max_zoom[13].">13</option>
                                    <option value=\"14\" ".$wpgmza_max_zoom[14].">14</option>
                                    <option value=\"15\" ".$wpgmza_max_zoom[15].">15</option>
                                    <option value=\"16\" ".$wpgmza_max_zoom[16].">16</option>
                                    <option value=\"17\" ".$wpgmza_max_zoom[17].">17</option>
                                    <option value=\"18\" ".$wpgmza_max_zoom[18].">18</option>
                                    <option value=\"19\" ".$wpgmza_max_zoom[19].">19</option>
                                    <option value=\"20\" ".$wpgmza_max_zoom[20].">20</option>
                                    <option value=\"21\" ".$wpgmza_max_zoom[21].">21</option>
                                </select>
                            </td>
                        </tr> 
                        <tr>
                            <td width='320'><label for=\"wpgmza_min_zoom\">".__("Maximum Zoom In Level","wp-google-maps")."</label></td>
                            <td>
                                <select id='wpgmza_min_zoom' name='wpgmza_min_zoom' >
                                    <option value=\"0\" ".$wpgmza_min_zoom[0].">0</option>
                                    <option value=\"1\" ".$wpgmza_min_zoom[1].">1</option>
                                    <option value=\"2\" ".$wpgmza_min_zoom[2].">2</option>
                                    <option value=\"3\" ".$wpgmza_min_zoom[3].">3</option>
                                    <option value=\"4\" ".$wpgmza_min_zoom[4].">4</option>
                                    <option value=\"5\" ".$wpgmza_min_zoom[5].">5</option>
                                    <option value=\"6\" ".$wpgmza_min_zoom[6].">6</option>
                                    <option value=\"7\" ".$wpgmza_min_zoom[7].">7</option>
                                    <option value=\"8\" ".$wpgmza_min_zoom[8].">8</option>
                                    <option value=\"9\" ".$wpgmza_min_zoom[9].">9</option>
                                    <option value=\"10\" ".$wpgmza_min_zoom[10].">10</option>
                                    <option value=\"11\" ".$wpgmza_min_zoom[11].">11</option>
                                    <option value=\"12\" ".$wpgmza_min_zoom[12].">12</option>
                                    <option value=\"13\" ".$wpgmza_min_zoom[13].">13</option>
                                    <option value=\"14\" ".$wpgmza_min_zoom[14].">14</option>
                                    <option value=\"15\" ".$wpgmza_min_zoom[15].">15</option>
                                    <option value=\"16\" ".$wpgmza_min_zoom[16].">16</option>
                                    <option value=\"17\" ".$wpgmza_min_zoom[17].">17</option>
                                    <option value=\"18\" ".$wpgmza_min_zoom[18].">18</option>
                                    <option value=\"19\" ".$wpgmza_min_zoom[19].">19</option>
                                    <option value=\"20\" ".$wpgmza_min_zoom[20].">20</option>
                                    <option value=\"21\" ".$wpgmza_min_zoom[21].">21</option>
                                </select>
                            </td>
                        </tr> 

                        <tr style='height:20px;'>
                            <td></td>
                            <td></td>
                        </tr>
                        
                        <tr>
                            <td valign='top'><label for=\"wpgmza_bicycle\">".__("Enable Layers","wp-google-maps")."</label></td>
                            <td>
                                <div class='switch'>
                                	<input type='checkbox' id='wpgmza_bicycle' name='wpgmza_bicycle' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_bicycle[1]."> <label for='wpgmza_bicycle' data-on='".__("Yes","wp-google-maps")."' data-off='".__("No","wp-google-maps")."''></label>
                            	</div> ".__("Bicycle Layer","wp-google-maps")."<br />
								
								
                                <div class='switch'>
                                	<input type='checkbox' id='wpgmza_traffic' name='wpgmza_traffic' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_traffic[1]."> <label for='wpgmza_traffic' data-on='".__("Yes","wp-google-maps")."' data-off='".__("No","wp-google-maps")."''></label>
                            	</div> ".__("Traffic Layer","wp-google-maps")."<br />
								<div class='wpgmza-open-layers-feature-unavailable'></div>
                                <div class='switch'>
                                	<input type='checkbox' id='wpgmza_transport' name='wpgmza_transport' class='postform cmn-toggle cmn-toggle-round-flat' ".$wpgmza_transport_layer_checked[0]."> <label for='wpgmza_transport' data-on='".__("Yes","wp-google-maps")."' data-off='".__("No","wp-google-maps")."''></label>
                           		</div> ".__("Transit Layer","wp-google-maps")."
								<div class='wpgmza-open-layers-feature-unavailable'></div>
                            </td>
                        </tr>
						
						<tr>
							<td>
								<label for='polygon_labels'>
									" . __("Enable Polygon Labels", "wp-google-maps") . "
								</label>
							</td>
							<td>
								<div class='switch'>
									<input type='checkbox' id='polygon_labels' name='polygon_labels' class='postform cmn-toggle cmn-toggle-round-flat' " . (!empty($map->polygon_labels) ? 'checked="checked"' : '') . "/>
									<label for='polygon_labels' data-on='".__("Yes","wp-google-maps")."' data-off='".__("No","wp-google-maps")."''></label>
								</div>
							</td>
						</tr>

                        <tr>
                            <td><label for=\"wpgmza_kml\">".__("KML/GeoRSS URL","wp-google-maps")."</label></td>
                            <td>
                             <input id='wpgmza_kml' name='wpgmza_kml' type='text' size='100' class='regular-text' value='".$res->kml."' /> <em><small class='wpgmza-text-field__description'>".__("The KML/GeoRSS layer will over-ride most of your map settings","wp-google-maps").". ".__("For multiple sources, separate each one by a comma.","wp-google-maps")."</small></em>
							</td>
                        </tr>
                        <!--<tr>
                            <td><label for=\"wpgmza_fusion\">".__("Fusion table ID","wp-google-maps")."</label></td>
                            <td>
                             <input id='wpgmza_fusion' name='wpgmza_fusion' type='text' size='20' maxlength='200' class='small-text' value='".$res->fusion."' /> <em><small class='wpgmza-text-field__description'>".__("Read data directly from your Fusion Table. For more information, see <a href='http://googlemapsmania.blogspot.com/2010/05/fusion-tables-google-maps-api.html'>http://googlemapsmania.blogspot.com/2010/05/fusion-tables-google-maps-api.html</a>","wp-google-maps")."</small></em>
							 <div class='wpgmza-open-layers-feature-coming-soon'></div>
                            </td>
                        </tr>-->
						
						<tr>
							<td>
								<label>
									" . __('Integration', 'wp-google-maps') . "
								</label>
							</td>
							<td>
								" . $integrationPanel->html . "
							</td>
						</tr>
						
						<tr>
							<td>
								<label>
									" . __('Enable Marker Ratings', 'wp-google-maps') . "
								</label>
							</td>
							<td>
								<div class='switch'>
									<input type='checkbox' id='enable_marker_ratings' name='enable_marker_ratings' class='postform cmn-toggle cmn-toggle-round-flat' " . (!empty($map->enable_marker_ratings) ? 'checked="checked"' : '') . "/>
									<label for='enable_marker_ratings'></label>
								</div>
							</td>
						</tr>
						
				    	<tr>
					        <td>
					        	<label for=\"wpgmza_iw_type\">".__("Infowindow Style","wp-google-maps")."</label>
				        	</td>
					        <td>
					        	<div class='wpgmza-infowindow-style-picker wpgmza-flex'>
					        		<div class='wpgmza-flex-item wpgmza-infowindow-picker__item ".$wpgmza_iw_class[0]."'>
					        			<div class='wpgmza-card wpgmza-card-border__hover'>
						            		<img src=\"".WPGMAPS_DIR."/images/marker_iw_type_1.png\" title=\"Default\" id=\"wpgmza_iw_selection_1\" width=\"250\" class=\"iw_custom_click_hide wpgmza_mlist_selection ".$wpgmza_iw_class[0]."\">
						            		<span class='wpgmza-infowindow-style__name'>" . __( 'Default Infowindow', 'wpgooglemaps' ) . "</span>
						            	</div>
						            </div>

						            <div class='wpgmza-flex-item wpgmza-infowindow-picker__item ".$wpgmza_iw_class[1]."'>
					        			<div class='wpgmza-card wpgmza-card-border__hover'>
						            		<img src=\"".WPGMAPS_DIR."/images/marker_iw_type_2.png\" title=\"Modern\" id=\"wpgmza_iw_selection_2\" width=\"250\" class=\"iw_custom_click_show wpgmza_mlist_selection ".$wpgmza_iw_class[1]."\">
						            		<span class='wpgmza-infowindow-style__name'>" . __( 'Modern Infowindow', 'wpgooglemaps' ) . "</span>
						            	</div>
						            </div>
						            <div class='wpgmza-flex-item wpgmza-infowindow-picker__item ".$wpgmza_iw_class[2]."'>
					        			<div class='wpgmza-card wpgmza-card-border__hover'>
						            		<img src=\"".WPGMAPS_DIR."/images/marker_iw_type_3.png\" title=\"Modern\" id=\"wpgmza_iw_selection_3\" width=\"250\" class=\"iw_custom_click_show wpgmza_mlist_selection ".$wpgmza_iw_class[2]."\">
						            		<span class='wpgmza-infowindow-style__name'>" . __( 'Modern Plus Infowindow', 'wpgooglemaps' ) . "</span>  
						            	</div>
						            </div>

						            <div class='wpgmza-flex-item wpgmza-infowindow-picker__item ".$wpgmza_iw_class[3]."'>
					        			<div class='wpgmza-card wpgmza-card-border__hover'>
						            		<img src=\"".WPGMAPS_DIR."/images/marker_iw_type_4.png\" title=\"Modern\" id=\"wpgmza_iw_selection_4\" width=\"250\" class=\"iw_custom_click_show wpgmza_mlist_selection ".$wpgmza_iw_class[3]."\">
						            		<span class='wpgmza-infowindow-style__name'>" . __( 'Circular Infowindow', 'wpgooglemaps' ) . "</span>  
						            	</div>
						            </div>

						            <div class='wpgmza-flex-item wpgmza-infowindow-picker__item ".$wpgmza_iw_class[4]."'>
					        			<div class='wpgmza-card wpgmza-card-border__hover'>
											<div class=\"iw_custom_click_hide wpgmza_mlist_selection ".$wpgmza_iw_class[4]."\"  id=\"wpgmza_iw_selection_null\" title=\"Inherit\">
												" . __('Inherit Global Setting', 'wp-google-maps') . "
											</div>
										</div>
									</div>
	                                <input type=\"radio\" name=\"wpgmza_iw_type\" id=\"rb_wpgmza_iw_selection_1\" value=\"0\" ".$infowwindow_sel_checked[0]." class=\"wpgmza_hide_input\">
	                                <input type=\"radio\" name=\"wpgmza_iw_type\" id=\"rb_wpgmza_iw_selection_2\" value=\"1\" ".$infowwindow_sel_checked[1]." class=\"wpgmza_hide_input\">
	                                <input type=\"radio\" name=\"wpgmza_iw_type\" id=\"rb_wpgmza_iw_selection_3\" value=\"2\" ".$infowwindow_sel_checked[2]." class=\"wpgmza_hide_input\">
	                                <input type=\"radio\" name=\"wpgmza_iw_type\" id=\"rb_wpgmza_iw_selection_4\" value=\"3\" ".$infowwindow_sel_checked[3]." class=\"wpgmza_hide_input\">
	                                <input type=\"radio\" name=\"wpgmza_iw_type\" id=\"rb_wpgmza_iw_selection_null\" value=\"-1\" ".$infowwindow_sel_checked[4]." class=\"wpgmza_hide_input\">
	                            </div>
					        </td>
				    	</tr>
				    	<tr>
					        <th>
					        	&nbsp;
				        	</th>
					        <td>     
					       	 ".__("Your selection:","wp-google-maps")."   
					            <span class=\"wpgmza_iw_sel_text\" style=\"font-weight:bold;\">".$infowwindow_sel_text."</span>
					        </td>
					        <script>
                                	jQuery(document).ready(function(){
                                		
                                		if(jQuery('#rb_wpgmza_iw_selection_2').attr('checked')){
						          			jQuery('#iw_custom_colors_row').fadeIn();
						          		}else if(jQuery('#rb_wpgmza_iw_selection_3').attr('checked')){
						          			jQuery('#iw_custom_colors_row').fadeIn();
						          		}else if(jQuery('#rb_wpgmza_iw_selection_4').attr('checked')){
						          			jQuery('#iw_custom_colors_row').fadeIn();
						          		}else{
						          			jQuery('#iw_custom_colors_row').fadeOut();
							          	}

							          	jQuery('.iw_custom_click_show').click(function(){
							          		jQuery('#iw_custom_colors_row').fadeIn();
							          	});

										jQuery('.iw_custom_click_hide').click(function(){
							          		jQuery('#iw_custom_colors_row').fadeOut();
							          	});

							          });
                            </script>

				    	</tr>

				    	<tr id='iw_custom_colors_row' style='display:none;'>
				    		<td>
				    		</td>
				    		<td>
				    			<br><strong>".__("Infowindow Colors","wp-google-maps")."</strong><br>
				    			<table>
				    				<tr>
				    					<td>
				    						".__("Primary Color", "wp-google-maps")."
				    					</td>
				    					<td>
				    						<input id=\"iw_primary_color\" name=\"iw_primary_color\" type=\"text\" class=\"color\" value=\"$iw_primary_color\" /><br>
				    					</td>
				    				</tr>
				    				<tr>
				    					<td>
				    						".__("Accent Color", "wp-google-maps")."
				    					</td>
				    					<td>
				    						<input id=\"iw_accent_color\" name=\"iw_accent_color\" type=\"text\" class=\"color\" value=\"$iw_accent_color\" /><br>
				    					</td>
				    				</tr>
				    				<tr>
				    					<td>
				    						".__("Text Color", "wp-google-maps")."
				    					</td>
				    					<td>
				    						<input id=\"iw_text_color\" name=\"iw_text_color\" type=\"text\" class=\"color\" value=\"$iw_text_color\" /><br>
				    					</td>
				    				</tr>
				    			</table>
                       
				    		</td>
				    	</td>
					    	
				    </table>
                            

            
    
                </div> 
                <div id=\"tabs-5\">
                	<h3>" . __( 'Marker Listing', 'wp-google-maps' ) . ":</h3>
					<table class=\"form-table\">
					    <tbody>
					    	<tr>
						        <th>
						        	<label for=\"\">".__("Marker Listing Style","wp-google-maps")."</label>
					        	</th>
						        <td class='wpgmza-marker-listing-style-menu'>
								
									<img style='display:none;'id='wpgmza-marker-listing-preview' src='" . WPGMAPS_DIR . "/images/marker_list_0.png' 
										title='$'
										/>
								
									<ul style='display: none;'>
										<li>
											<input id='wpgmza-listing-picker__option-0' name='wpgmza_listmarkers_by' value='0' {$list_markers_by_checked[0]} type='radio'/>
											<label>" . __('No marker listing', 'wp-google-maps') . "</label>
										</li>
										<li>
											<input id='wpgmza-listing-picker__option-1' name='wpgmza_listmarkers_by' value='1' {$list_markers_by_checked[1]} type='radio'/>
											<label>" . __('Basic table', 'wp-google-maps') . "</label>
										</li>
										<li>
											<input id='wpgmza-listing-picker__option-2' name='wpgmza_listmarkers_by' value='4' {$list_markers_by_checked[4]} type='radio'/>
											<label>" . __('Basic list', 'wp-google-maps') . "</label>
										</li>
										<li>
											<input id='wpgmza-listing-picker__option-3' name='wpgmza_listmarkers_by' value='2' {$list_markers_by_checked[2]} type='radio'/>
											<label>" . __('Advanced table', 'wp-google-maps') . "</label>
										</li>
										<li>
											<input id='wpgmza-listing-picker__option-4' name='wpgmza_listmarkers_by' value='3' {$list_markers_by_checked[3]} type='radio'/>
											<label>" . __('Carousel', 'wp-google-maps') . "</label>
										</li>
										<li>
											<input id='wpgmza-listing-picker__option-modern' name='wpgmza_listmarkers_by' value='6' {$list_markers_by_checked[6]} type='radio'/>
											<label>" . __('Modern', 'wp-google-maps') . "</label>
										</li>
										<li>
											<input id='wpgmza-listing-picker__option-grid' name='wpgmza_listmarkers_by' value='7' {$list_markers_by_checked[7]} type='radio'/>
											<label>" . __('Grid', 'wp-google-maps') . "</label>
										</li>
									</ul>
									
									<div class='wpgmza-marker-listing-picker wpgmza-flex'>
										<div class='wpgmza-marker-listing-picker__item {$list_markers_by_class[0]}'>
											<label for='wpgmza-listing-picker__option-0' class='wpgmza-card wpgmza-card-border__hover'>
												<img class='wpgmza-listing-item__img' src='" . WPGMAPS_DIR . "/images/marker_list_0.png' />
												<span class='wpgmza-listing-item__title'>" . __('No marker listing', 'wp-google-maps') . "</span>
											</label>
										</div>
										<div class='wpgmza-marker-listing-picker__item {$list_markers_by_class[1]}'>
											<label for='wpgmza-listing-picker__option-1' class='wpgmza-card wpgmza-card-border__hover'>
												<img class='wpgmza-listing-item__img' src='" . WPGMAPS_DIR . "/images/marker_list_1.png' />
												<span class='wpgmza-listing-item__title'>" . __('Basic table', 'wp-google-maps') . "</span>
											</label>
										</div>
										<div class='wpgmza-marker-listing-picker__item {$list_markers_by_class[4]}'>
											<label for='wpgmza-listing-picker__option-2' class='wpgmza-card wpgmza-card-border__hover'>
												<img class='wpgmza-listing-item__img' src='" . WPGMAPS_DIR . "/images/marker_list_2.png' />
												<span class='wpgmza-listing-item__title'>" . __('Basic list', 'wp-google-maps') . "</span>
											</label>
										</div>
										<div class='wpgmza-marker-listing-picker__item {$list_markers_by_class[2]}'>
											<label for='wpgmza-listing-picker__option-3' class='wpgmza-card wpgmza-card-border__hover'>
												<img class='wpgmza-listing-item__img' src='" . WPGMAPS_DIR . "/images/marker_list_3.png' />
												<span class='wpgmza-listing-item__title'>" . __('Advanced table', 'wp-google-maps') . "</span>
											</label>
										</div>
										<div class='wpgmza-marker-listing-picker__item {$list_markers_by_class[3]}'>
											<label for='wpgmza-listing-picker__option-4' class='wpgmza-card wpgmza-card-border__hover'>
												<img class='wpgmza-listing-item__img' src='" . WPGMAPS_DIR . "/images/marker_list_4.png' />
												<span class='wpgmza-listing-item__title'>" . __('Carousel', 'wp-google-maps') . "</span>
											</label>
										</div>
										<div class='wpgmza-marker-listing-picker__item {$list_markers_by_class[6]}'>
											<label for='wpgmza-listing-picker__option-modern' class='wpgmza-card wpgmza-card-border__hover'>
												<img class='wpgmza-listing-item__img' src='" . WPGMAPS_DIR . "/images/marker_list_modern.png' />
												<span class='wpgmza-listing-item__title'>" . __('Modern', 'wp-google-maps') . "</span>
											</label>
										</div>
										<div class='wpgmza-marker-listing-picker__item {$list_markers_by_class[7]}'>
											<label for='wpgmza-listing-picker__option-grid' class='wpgmza-card wpgmza-card-border__hover'>
												<img class='wpgmza-listing-item__img' src='" . WPGMAPS_DIR . "/images/marker_list_grid.png' />
												<span class='wpgmza-listing-item__title'>" . __('Grid', 'wp-google-maps') . "</span>
											</label>
										</div>
									</div>
									
						        </td>
					    	</tr>
					    	
					    </table>

                    <table class='' id='wpgmaps_marker_listing_options'>
                       
                    	<tr class='wpgmza_modern_marker_hide'" . ( empty( $list_markers_by_checked[6] ) ? "" : " style='display:none;'" ) . ">
                            <td>".__("Marker Listing Placement","wp-google-maps").":</td>
                            <td>
                            	<div class='switch'>
                                        <input type='checkbox' id='wpgmza_marker_listing_position' name='wpgmza_marker_listing_position' class='postform cmn-toggle cmn-toggle-yes-no' ".$wpgmza_marker_listing_below_checked."> <label class='cmn-override-big-wide' for='wpgmza_marker_listing_position' data-on='".__("Above Map","wp-google-maps")."' data-off='".__("Below Map","wp-google-maps")."''></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                             <td>".__("Order markers by","wp-google-maps").":</td>
                             <td>
                                <select id='wpgmza_order_markers_by' name='wpgmza_order_markers_by' class='postform'>
                                    <option value=\"1\" ".$wpgmza_map_order_markers_by[1].">".__("ID","wp-google-maps")."</option>
                                    <option value=\"2\" ".$wpgmza_map_order_markers_by[2].">".__("Title","wp-google-maps")."</option>
                                    <option value=\"3\" ".$wpgmza_map_order_markers_by[3].">".__("Address","wp-google-maps")."</option>
                                    <option value=\"4\" ".$wpgmza_map_order_markers_by[4].">".__("Description","wp-google-maps")."</option>
                                    <option value=\"5\" ".$wpgmza_map_order_markers_by[5].">".__("Category","wp-google-maps")."</option>
                                    <option value=\"6\" ".$wpgmza_map_order_markers_by[6].">".__('Category Priority','wp-google-maps')."</option>
									<option value=\"7\" ".$wpgmza_map_order_markers_by[7].">".__('Distance', 'wp-google-maps')."</option>
									" .
									
									(!empty($wpgmza_gold_version) && version_compare($wpgmza_gold_version, '5.0.0', '>=') ? "<option value=\"7\" ".$wpgmza_map_order_markers_by[8].">".__('Rating', 'wp-google-maps')."</option>" : "")
									
									. "
                                </select>
                                <select id='wpgmza_order_markers_choice' name='wpgmza_order_markers_choice' class='postform'>
                                    <option value=\"1\" ".$wpgmza_map_order_markers_choice[1].">".__("Ascending","wp-google-maps")."</option>
                                    <option value=\"2\" ".$wpgmza_map_order_markers_choice[2].">".__("Descending","wp-google-maps")."</option>
                                </select>

                            </td>
                        </tr>
						<tr class='wpgmza_modern_marker_hide' style='height:20px;" . ( empty( $list_markers_by_checked[6] ) ? "" : "display:none;" ) . "'>
                             <td></td>
                             <td></td>
                        </tr>

                        <tr class='wpgmza_modern_marker_hide'" . ( empty( $list_markers_by_checked[6] ) ? "" : " style='display:none;'" ) . ">
                             <td valign='top'>".__("Move list inside map","wp-google-maps").":</td>
                             <td>
                                <div class='switch'>
                                	<input id='wpgmza_push_in_map' name='wpgmza_push_in_map' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' value='1' $pushinmap_checked /> <label for='wpgmza_push_in_map'></label></div> <span class='wpgmza-info__small'>".__("Move your marker list inside the map area","wp-google-maps")." <span style='color:red;'>".__("(still in beta)","wp-google-maps")."</span></span><br />
                                </div>
                                <script>
                                	jQuery(document).ready(function(){
                                		
                                		if(jQuery('#wpgmza_push_in_map').attr('checked')){
						          			jQuery('#wpgmza_marker_list_conditional').fadeIn();
						          		}else{
						          			jQuery('#wpgmza_marker_list_conditional').fadeOut();
							          	}

							          	jQuery('#wpgmza_push_in_map').on('change', function(){
							          		if(jQuery(this).attr('checked')){
							          			jQuery('#wpgmza_marker_list_conditional').fadeIn();
							          		}else{
							          			jQuery('#wpgmza_marker_list_conditional').fadeOut();
							          		}
							          	});
							          });
                                </script>
                                <div id='wpgmza_marker_list_conditional'>
									<br>".__("Placement: ","wp-google-maps")."
									<select id='wpgmza_push_in_map_placement' name='wpgmza_push_in_map_placement' class='postform'>
	                                    <option value=\"1\" ".$push_in_map_placement_checked[1].">".__("Top Center","wp-google-maps")."</option>
	                                    <option value=\"2\" ".$push_in_map_placement_checked[2].">".__("Top Left","wp-google-maps")."</option>
	                                    <option value=\"3\" ".$push_in_map_placement_checked[3].">".__("Top Right","wp-google-maps")."</option>
	                                    <option value=\"4\" ".$push_in_map_placement_checked[4].">".__("Left Top ","wp-google-maps")."</option>
	                                    <option value=\"5\" ".$push_in_map_placement_checked[5].">".__("Right Top","wp-google-maps")."</option>
	                                    <option value=\"6\" ".$push_in_map_placement_checked[6].">".__("Left Center","wp-google-maps")."</option>
	                                    <option value=\"7\" ".$push_in_map_placement_checked[7].">".__("Right Center","wp-google-maps")."</option>
	                                    <option value=\"8\" ".$push_in_map_placement_checked[8].">".__("Left Bottom","wp-google-maps")."</option>
	                                    <option value=\"9\" ".$push_in_map_placement_checked[9].">".__("Right Bottom","wp-google-maps")."</option>
	                                    <option value=\"10\" ".$push_in_map_placement_checked[10].">".__("Bottom Center","wp-google-maps")."</option>
	                                    <option value=\"11\" ".$push_in_map_placement_checked[11].">".__("Bottom Left","wp-google-maps")."</option>
	                                    <option value=\"12\" ".$push_in_map_placement_checked[12].">".__("Bottom Right","wp-google-maps")."</option>
	                                </select> <br />
	                                ".__("Container Width: ","wp-google-maps")."<input type=\"text\" name=\"wpgmza_push_in_map_width\" id=\"wpgmza_push_in_map_width\" value=\"$wpgmza_push_in_map_width\" style='width:70px;' placeholder='% or px'> <em>Set as % or px, eg. 30% or 400px</em><br />
	                                ".__("Container Height: ","wp-google-maps")."<input type=\"text\" name=\"wpgmza_push_in_map_height\" id=\"wpgmza_push_in_map_height\" value=\"$wpgmza_push_in_map_height\" style='width:70px;' placeholder='% or px'>
                            	</div>
                            </td>
                        </tr>
						<tr style='height:20px;'>
                             <td></td>
                             <td></td>
                        </tr>

                         <tr>
                             <td>".__("Filter by Category","wp-google-maps").":</td>
                             <td>
                                <div class='switch'>
                                	<input id='wpgmza_filterbycat' name='wpgmza_filterbycat' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' value='1' $listfilters_checked /> <label for='wpgmza_filterbycat'></label></div><span class='wpgmza-info__small'>".__("Allow users to filter by category?","wp-google-maps")."</span>
                                </div>
                            </td>
                        </tr>

                        </table>
                            

            
    
                </div>  <!-- end of tab5 -->     
				
				";
				
				$map = WPGMZA\Map::createInstance($_GET['map_id']);
				$map->element->setInlineStyle('min-height', '400px');	// Safeguard for map edit page zero height
				$map->element->setAttribute('id', 'wpgmza_map');		// Legacy HTML
				
				include(plugin_dir_path(__FILE__) . 'html/marker-filtering-tab.html.php');
				$markerPanel = new WPGMZA\MarkerPanel($_GET['map_id']);
				
                echo apply_filters("wpgmaps_filter_pro_map_editor_tab_content","")."
            </div>   




				<input type='hidden' name='real_post_nonce' value='$real_post_nonce'/>
				
				<p class='submit'><input type='submit' name='wpgmza_savemap' class='button-primary' value='".__("Save Map","wp-google-maps")." &raquo;' /></p>

                <p style=\"width:600px; color:#808080;\" class='wpgmza-map-edit__mouse-tip'>
                    ".__("Tip: Use your mouse to change the layout of your map. When you have positioned the map to your desired location, press \"Save Map\" to keep your settings.","wp-google-maps")."</p>

                <div id='wpgmza-marker-tabs__wrap' style='display:block; width:100%;'>
                    <div style='display:block; width:49%; margin-right:1%; float:left;;'>
                

                        <a name=\"wpgmaps_marker\" /></a>

                        <div id=\"wpgmaps_tabs_markers\">
                        <ul>
                                <li><a href=\"#tabs-m-1\" class=\"tabs-m-1\">".__("Markers","wp-google-maps")."</a></li>
                                <li><a href=\"#tabs-m-2\" class=\"tabs-m-2\">".__("Polygons","wp-google-maps")."</a></li>
                                <li><a href=\"#tabs-m-3\" class=\"tabs-m-3\">".__("Polylines","wp-google-maps")."</a></li>
                                <li><a href=\"#tabs-m-4\" class=\"tabs-m-4\">".__("Heatmaps","wp-google-maps")."</a></li>
								<li><a href=\"#tabs-m-5\" class=\"tabs-m-5\">".__("Circles","wp-google-maps")."</a></li>
								<li><a href=\"#tabs-m-6\" class=\"tabs-m-6\">".__("Rectangles","wp-google-maps")."</a></li>
                        </ul>
                        <div id=\"tabs-m-1\">
							" . $markerPanel->html . "
                        </div>
                        <div id=\"tabs-m-2\" class='wpgmza-open-layers-feature-coming-soon'>
                                <h2 style=\"padding-top:0; margin-top:0;\">".__("Add a Polygon","wp-google-maps")."</h2>
                                <span id=\"wpgmza_addpolygon_div\"><a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-google-maps-menu&action=add_poly&map_id=".$_GET['map_id']."' id='wpgmza_addpoly' class='button-primary wpgmza-button__top-right' value='".__("Add a New Polygon","wp-google-maps")."' />".__("Add a New Polygon","wp-google-maps")."</a></span>
                                <div id=\"wpgmza_poly_holder\">".wpgmza_b_return_polygon_list($_GET['map_id'])."</div>
                        </div>
                        <div id=\"tabs-m-3\" class='wpgmza-open-layers-feature-coming-soon'>
                                <h2 style=\"padding-top:0; margin-top:0;\">".__("Add a Polyline","wp-google-maps")."</h2>
                                <span id=\"wpgmza_addpolyline_div\"><a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-google-maps-menu&action=add_polyline&map_id=".$_GET['map_id']."' id='wpgmza_addpolyline' class='button-primary wpgmza-button__top-right' value='".__("Add a New Polyline","wp-google-maps")."' />".__("Add a New Polyline","wp-google-maps")."</a></span>
                                <div id=\"wpgmza_polyline_holder\">".wpgmza_b_return_polyline_list($_GET['map_id'])."</div>
                        </div>
                        <div id=\"tabs-m-4\" class='wpgmza-open-layers-feature-coming-soon'>
                                <h2 style=\"padding-top:0; margin-top:0;\">".__("Add a dataset","wp-google-maps")."</h2>
                                ".$wpgmza_string_heatmaps."
                        </div>
						
						<div id=\"tabs-m-5\" class='wpgmza-open-layers-feature-coming-soon'>
							<h2>
								" . __('Add a Circle', 'wp-google-maps') . "
							</h2>
							<span><a class=\"button-primary wpgmza-button__top-right\" href=\"" . get_option('siteurl') . "/wp-admin/admin.php?page=wp-google-maps-menu&action=add_circle&map_id=" . $_GET['map_id'] . "\">" . __("Add a Circle", "wp-google-maps") . "</a></span>
							" . wpgmza_get_circles_table($_GET['map_id']) . "
						</div>
						
						<div id=\"tabs-m-6\" class='wpgmza-open-layers-feature-coming-soon'>
							<h2>
								" . __('Add a Rectangle', 'wp-google-maps') . "
							</h2>
							<span><a class=\"button-primary wpgmza-button__top-right\" href=\"" . get_option('siteurl') . "/wp-admin/admin.php?page=wp-google-maps-menu&action=add_rectangle&map_id=" . $_GET['map_id'] . "\">" . __("Add a Rectangle", "wp-google-maps") . "</a></span>
							" . wpgmza_get_rectangles_table($_GET['map_id']) . "
						</div>
                    </div>
                </div>
                <div style='display:block; width:50%; overflow:auto; float:left;'>
					".wpgmaps_check_if_no_api_key()."
                    " . $map->element->html . "
                        <div class='clear'></div>
                    <div id='wpgmaps_save_reminder' style='display: none;'>
                        <div class='wpgmza-nag wpgmza-update-nag' style='text-align:center;'>                                        
                            <h4>Remember to save your map!</h4>                                        
                        </div>
                    </div>
                    </div>

                    <div id='wpgmaps_marker_cache_reminder' style='display: none;'>                                
                        ".(function_exists("wpgmza_caching_notice_changes") ? wpgmza_caching_notice_changes(true, true) : '')."
                    </div>
                </div>
            </div>
            <div class='clear'></div>
							<h2 style=\"padding-top:0; margin-top:0;\">".__("Your Markers","wp-google-maps")."</h2>
                            <div id=\"wpgmza_marker_holder\">
							" . $adminMarkerTableHTML . "
                            </div>
                
            </form>

            

            ".wpgmza_return_pro_add_ons()." 
            <p class='wpgmza-center'><br /><br />".__("WP Google Maps encourages you to make use of the amazing icons at ", "wp-google-maps")."<a href='https://mappity.org'>https://mappity.org</a></p>


            </div>
            
        </div>
    ";
	$markerLibraryDialog = new WPGMZA\MarkerLibraryDialog();
	$markerLibraryDialog->html();


	}

}

/**
 * This function takes field data from POST and updates the marker field data with it
 * @return void
 */
function wpgmza_update_marker_custom_fields($marker_id, $field_data)
{
	$custom_fields = new WPGMZA\CustomMarkerFields($marker_id);
	
	for($i = 0; $i < count($_POST['custom_fields']); $i++)
	{
		$field_data = $_POST['custom_fields'][$i];
		$custom_fields->{$field_data['field_id']} = stripslashes($field_data['value']);
	}
}

function wpgmaps_action_callback_pro() {
        global $wpdb;
		global $wpgmza;
        global $wpgmza_tblname;
        global $wpgmza_tblname_poly;
        global $wpgmza_tblname_polylines;
		
        $check = check_ajax_referer( 'wpgmza', 'security' );
        $table_name = $wpdb->prefix . "wpgmza";
        $wpgmza_tags = wpgmza_get_allowed_tags();
		
        if ($check == 1) {

            if ($_POST['action'] == "add_marker") {
				
				foreach($_POST as $key => $value)
				{
					if(is_string($_POST[$key]))
						$_POST[$key] = stripslashes($_POST[$key]);
				}
                
                if (is_array($_POST['category'])) { $cat = implode(",",$_POST['category']); } else { $cat = $_POST['category']; }
                

                $other_data = array();
                if ( $_POST['icon_on_click'] ) {
                	$other_data['icon_on_click'] = sanitize_text_field( $_POST['icon_on_click'] );
                }
				
				$qstr = "INSERT INTO $wpgmza_tblname (
						map_id,
						title,
						address,
						description,
						pic,
						icon,
						link,
						lat,
						lng,
						latlng,
						anim,
						category,
						infoopen,
						approved,
						retina,
						other_data
					)
					VALUES(
						%d, # map_id
						%s, # title
						%s, # address
						%s, # description
						%s, # pic
						%s, # icon
						%s, # link
						%f, # lat
						%f, # lng
						{$wpgmza->spatialFunctionPrefix}GeomFromText('POINT(%f %f)'), # latlng
						%d, # anim
						%s, # category
						%d, # infoopen
						%d, # approved
						%d, # retina
						%s # other_data
					)";
				
				$description = \WPGMZA\DOMDocument::convertUTF8ToHTMLEntities($_POST['desc']);
				if(!current_user_can('administrator'))
					$description = wp_kses( $description, $wpgmza_tags );
				
				$params = array(
					$_POST['map_id'],
					sanitize_text_field( $_POST['title'] ),
					sanitize_text_field( $_POST['address'] ),
					$description,
					sanitize_text_field( $_POST['pic'] ),
					sanitize_text_field( $_POST['icon'] ),
					$_POST['link'],
					sanitize_text_field( $_POST['lat'] ),
					sanitize_text_field( $_POST['lng'] ),
					sanitize_text_field( $_POST['lat'] ),
					sanitize_text_field( $_POST['lng'] ),
					sanitize_text_field( $_POST['anim'] ),
					sanitize_text_field( $cat ),
					sanitize_text_field( $_POST['infoopen'] ),
					sanitize_text_field( $_POST['approved'] ),
					sanitize_text_field( $_POST['retina'] ),
					maybe_serialize( $other_data ),
				);
				
				$stmt = $wpdb->prepare($qstr, $params);
				$rows_affected = $wpdb->query($stmt);
				$insert_id = $wpdb->insert_id;
				
				if(isset($_POST['custom_fields']))
				{
					$insert_id = $wpdb->insert_id;
					wpgmza_update_marker_custom_fields($insert_id, $_POST['custom_fields']);
				}
				
                wpgmaps_update_xml_file($_POST['map_id']);
                $return_a = array(
                    "marker_id" => $insert_id,
                    "marker_data" => wpgmaps_return_markers_pro($_POST['map_id'])
                );
				
				$marker = new WPGMZA\Marker($insert_id);
				do_action('wpgmza_marker_saved', $marker);
				
                echo json_encode($return_a);
            }
           
 
            if ($_POST['action'] == "edit_marker") {
				
				foreach($_POST as $key => $value)
				{
					if(is_string($_POST[$key]))
						$_POST[$key] = stripslashes($_POST[$key]);
				}
				
				$description = \WPGMZA\DOMDocument::convertUTF8ToHTMLEntities($_POST['desc']);
				if(!current_user_can('administrator'))
					$description = wp_kses( $description, $wpgmza_tags );
				
                $link = $_POST['link'];
                $pic = $_POST['pic'];
                $icon = $_POST['icon'];
                $anim = $_POST['anim'];
                $retina = $_POST['retina'];
                $approved = $_POST['approved'];

                $other_data = array();
                $other_data['0'] = '0';
                if ( $_POST['icon_on_click'] ) {
                	$other_data['icon_on_click'] = sanitize_text_field( $_POST['icon_on_click'] );
                }

                if (is_array($_POST['category'])) { $category = implode(",",$_POST['category']); } else { $category = $_POST['category']; }
                $infoopen = $_POST['infoopen'];
                $cur_id = intval($_POST['edit_id']);
                // $wpgmza_tags = wpgmza_get_allowed_tags();
                $rows_affected = $wpdb->query(
                	$wpdb->prepare(
                		"UPDATE $table_name SET 
                		`title` = %s, 
                		`address` = %s, 
                		`description` = %s, 
                		`link` = %s, 
                		`icon` = %s, 
                		`pic` = %s, 
                		`lat` = %f, 
                		`lng` = %f, 
						`latlng` = {$wpgmza->spatialFunctionPrefix}GeomFromText('POINT(%f %f)'),
                		`anim` = %s, 
                		`category` = %s, 
                		`infoopen` = %s, 
                		`approved` = %s, 
                		`retina` = %s,
                		`other_data` = %s 
                		WHERE `id`  = %d",
                		sanitize_text_field($_POST['title']),
                		sanitize_text_field($_POST['address']),
                		$description,
                		$link,
                		sanitize_text_field($icon),
                		sanitize_text_field($pic),
                		sanitize_text_field($_POST['lat']),
                		sanitize_text_field($_POST['lng']),
						$_POST['lat'],
						$_POST['lng'],
                		sanitize_text_field($anim),
                		sanitize_text_field($category),
                		sanitize_text_field($infoopen),
                		sanitize_text_field($approved),
                		sanitize_text_field($retina),
                		maybe_serialize( $other_data ),
                		intval($cur_id)
                		)
                	);
                wpgmaps_update_xml_file($_POST['map_id']);
                $return_a = array(
                    "marker_id" => $cur_id,
                    "marker_data" => wpgmaps_return_markers_pro($_POST['map_id'])
                );
				
				if(isset($_POST['custom_fields']))
					wpgmza_update_marker_custom_fields($cur_id, $_POST['custom_fields']);
				
				$marker = new WPGMZA\Marker($cur_id);
				do_action('wpgmza_marker_saved', $marker);
				
                echo json_encode($return_a);
           }

            if ($_POST['action'] == "delete_marker") {
                $marker_id = (int)$_POST['marker_id'];
                $wpdb->query(
                        "
                        DELETE FROM $wpgmza_tblname
                        WHERE `id` = '$marker_id'
                        LIMIT 1
                        "
                );
                $wpgmza_check = wpgmaps_update_xml_file($_POST['map_id']);
                if ( is_wp_error($wpgmza_check) && function_exists('wpgmza_return_error') ) wpgmza_return_error($wpgmza_check);
                $return_a = array(
                    "marker_id" => $marker_id,
                    "marker_data" => wpgmaps_return_markers_pro($_POST['map_id'])
                );
				
				do_action('wpgmza_marker_deleted', $marker_id);
				
                echo json_encode($return_a);

            }
            if ($_POST['action'] == "approve_marker") {
                $marker_id = (int)$_POST['marker_id'];
                $wpdb->query("
					UPDATE $wpgmza_tblname
					SET `approved` = 1
					WHERE `id` = '$marker_id'
					LIMIT 1
				");
                wpgmaps_update_xml_file($_POST['map_id']);
                $return_a = array(
                    "marker_id" => $marker_id,
                    "marker_data" => wpgmaps_return_markers_pro($_POST['map_id'])
                );
                echo json_encode($return_a);

            }
            if ($_POST['action'] == "delete_poly") {
                $poly_id = (int)$_POST['poly_id'];
                
                $wpdb->query(
                        "
                        DELETE FROM $wpgmza_tblname_poly
                        WHERE `id` = '$poly_id'
                        LIMIT 1
                        "
                );
                
                echo wpgmza_b_return_polygon_list($_POST['map_id']);

            }
            if ($_POST['action'] == "delete_polyline") {
                $poly_id = (int)$_POST['poly_id'];
                
                $wpdb->query(
                        "
                        DELETE FROM $wpgmza_tblname_polylines
                        WHERE `id` = '$poly_id'
                        LIMIT 1
                        "
                );
                
                echo wpgmza_b_return_polyline_list($_POST['map_id']);

            }
            if ($_POST['action'] == "delete_dataset") {
                $poly_id = $_POST['poly_id'];
                global $wpgmza_tblname_datasets;
                $wpdb->query("DELETE FROM ".$wpgmza_tblname_datasets." WHERE `id` = '$poly_id' LIMIT 1");
                
                echo wpgmza_b_return_heatmaps_list($_POST['map_id']);


            }
			
			if($_POST['action'] == "delete_circle") {
				global $wpgmza_tblname_circles;
				$stmt = $wpdb->prepare("DELETE FROM $wpgmza_tblname_circles WHERE id=%d", array($_POST['circle_id']));
				$wpdb->query($stmt);
				
				echo wpgmza_get_circles_table($_POST['map_id']);
			}
			
			if($_POST['action'] == "delete_rectangle") {
				global $wpgmza_tblname_rectangles;
				$stmt = $wpdb->prepare("DELETE FROM $wpgmza_tblname_rectangles WHERE id=%d", array($_POST['rectangle_id']));
				$wpdb->query($stmt);
				
				echo wpgmza_get_rectangles_table($_POST['map_id']);
			}
        }

        die(); // this is required to return a proper result

}
function wpgmza_return_pro_add_ons() {
    $wpgmza_ret = "";
    if (function_exists("wpgmza_register_gold_version")) { $wpgmza_ret .= wpgmza_gold_addon_display(); } else { $wpgmza_ret  .= ""; }
    if (function_exists("wpgmza_register_ugm_version")) { $wpgmza_ret .= wpgmza_ugm_addon_display_mapspage(); } else { $wpgmza_ret  .= ""; }
    return $wpgmza_ret;
}


function wpgmaps_tag_pro( $atts ) {
	
	if(!wpgmza_is_basic_compatible())
		return wpgmza_get_basic_incompatible_notice();

	global $wpgmza;
	global $short_code_active;
	global $wpdb;
	global $wpgmza_shortcode_atts_by_map_id;
	
	global $wpgmza_google_maps_api_loader;
	
	$short_code_active = true;
	if($wpgmza_google_maps_api_loader)
		$wpgmza_google_maps_api_loader->enqueueGoogleMaps();
	
	wpgmza_localize_category_data();

	wpgmza_enqueue_fontawesome();
	
	if(!isset($atts['id']))
	{
		// Let's use the first ID
		$atts['id'] = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}wpgmza_maps LIMIT 1");
	}
	
	$overrides = array_merge(array(), $atts);
	unset($overrides['id']);

	try{
		$map = WPGMZA\Map::createInstance($atts['id'], $overrides);
	}catch(\Exception $e) {
		
		if($wpgmza->isInDeveloperMode())
			throw $e;
		
		echo '
			<div class="notice notice-error">
				<p>
					' . __('The map ID you have entered does not exist. Please enter a map ID that exists.', 'wp-google-maps') . '
				</p>
			</div>
		';
		
		return "";
	}
	
	$map->shortcodeAttributes = $atts;
	
	$mashup_ids_attributes = '';
	if(isset($atts['mashup_ids']))
		$mashup_ids_attributes = "data-mashup-ids='{$atts['mashup_ids']}'";
	
	wp_register_style('wpgmaps-admin-style', plugins_url('css/wpgmaps-admin.css', __FILE__));
	wp_enqueue_style('wpgmaps-admin-style');

	wp_enqueue_script('wpgmza_canvas_layer_options', plugin_dir_url(__FILE__) . 'lib/CanvasLayerOptions.js', array('wpgmza_api_call'));
	wp_enqueue_script('wpgmza_canvas_layer', plugin_dir_url(__FILE__) . 'lib/CanvasLayer.js', array('wpgmza_api_call'));
	
	$stmt = $wpdb->prepare("SELECT `map_title` FROM `".$wpdb->prefix.'wpgmza_maps'."` WHERE `id` = %d AND `active` = 0", array($atts['id']));
	$result = $wpdb->get_row($stmt);
	
	if( $result == null ){
		return("<p>".__('The map ID you have entered does not exist. Please enter a map ID that exists.', 'wp-google-maps')."</p>");
	}
	
	$additionalClasses = "";
	if(!empty($atts['classname']))
		$additionalClasses = $atts['classname'];
	
	$wpgmza_shortcode_atts_by_map_id[$atts['id']] = $atts;

    global $wpgmza_current_map_id;
    global $wpgmza_current_map_cat_selection;
    global $wpgmza_current_map_shortcode_data;
    global $wpgmza_current_map_type;
    global $wpgmza_current_mashup;
    global $wpgmza_mashup_ids;
    global $wpgmza_mashup_all;
    global $wpgmza_override;
    $wpgmza_current_mashup = false;
    extract( shortcode_atts( array(
        'id' => '1',
        'mashup' => false,
        'mashup_ids' => false,
        'cat' => 'all',
        'type' => 'default',
        'parent_id' => false,
        'lat' => false,
        'lng' => false
    ), $atts ) );
    
    
    /* first check if we are using custom fields to generate the map */
    if (isset($atts['lng']) && isset($atts['lat']) && isset($atts['parent_id']) && $atts['lat'] && $atts['lng']) {
        $atts['id'] = $atts['parent_id']; /* set the main ID as the specified parent id */
        $wpgmza_current_map_id = $atts['parent_id'];
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lat'] = $atts['lat'];
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lng'] = $atts['lng'];
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['parent_id'] = $atts['parent_id'];
        $wpgmza_using_custom_meta = true;
        
    } else {
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lat'] = false;
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lng'] = false;
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['parent_id'] = false;
        $wpgmza_using_custom_meta = false;
    }    
    
    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");

    if (isset($atts['mashup']))
	{
		// $wpgmza_mashup = $atts['mashup'];
		$wpgmza_mashup = true;
	}

    if (isset($atts['parent_id'])) { $wpgmza_mashup_parent_id = $atts['parent_id']; }

    if (isset($wpgmza_mashup_ids) && $wpgmza_mashup_ids == "ALL") {

    } else {
        if (isset($atts['mashup_ids'])) {
            $wpgmza_mashup_ids[$atts['id']] = explode(",",$atts['mashup_ids']);
        }
    }
	
    if (isset($wpgmza_mashup)) { $wpgmza_current_mashup = true; }

    if (isset($wpgmza_mashup)) {
        $wpgmza_current_map_id = $wpgmza_mashup_parent_id;
        $res = wpgmza_get_map_data($wpgmza_mashup_parent_id);
    } else {
        $wpgmza_current_map_id = $atts['id'];
        
        
        if (isset($wpgmza_settings['wpgmza_settings_marker_pull']) && $wpgmza_settings['wpgmza_settings_marker_pull'] == '0') {
        } else {
            /* only check if marker file exists if they are using the XML method */
            wpgmza_check_if_marker_file_exists($wpgmza_current_map_id);
        }
        
        $res = wpgmza_get_map_data($atts['id']);
    }
	
    if (!isset($atts['cat']) || $atts['cat'] == "all" || $atts['cat'] == "0") {
        $wpgmza_current_map_cat_selection[$wpgmza_current_map_id] = 'all';
    } else {
        $wpgmza_current_map_cat_selection[$wpgmza_current_map_id] = explode(",",$atts['cat']);
    }
    

    if (!isset($atts['type']) || $atts['type'] == "default" || $atts['type'] == "") {
        $wpgmza_current_map_type[$wpgmza_current_map_id] = '';
    } else {
        $wpgmza_current_map_type[$wpgmza_current_map_id] = $atts['type'];
    }
	
	$map_other_settings = maybe_unserialize($res->other_settings);
	$res->other_settings = $map_other_settings;
	
	$iw_output = "";
	$iw_custom_styles ="";
    /* handle new modern infowindow HTML output */
	
	$infoWindowType = 1;
	if(isset($wpgmza_settings['wpgmza_iw_type']) && (int)$wpgmza_settings['wpgmza_iw_type'] != -1)
		$infoWindowType = (int)$wpgmza_settings['wpgmza_iw_type'];
	if(isset($map_other_settings['wpgmza_iw_type']) && (int)$map_other_settings['wpgmza_iw_type'] != -1)
		$infoWindowType = (int)$map_other_settings['wpgmza_iw_type'];
	
	$map_id = $atts['id'];
	
    if ($infoWindowType >= 1) {

		$mapCSSSelector = "[data-map-id='$map_id']";

    	/* Enqueue Modern Styles */

    	wp_enqueue_style("wpgmza_modern_base", plugin_dir_url(__FILE__) . "/css/wpgmza_style_pro_modern_base.css");

    	switch($infoWindowType){
    		case 2: //Modern Plus
    			wp_enqueue_style("wpgmza_modern_plus", plugin_dir_url(__FILE__) . "/css/wpgmza_style_pro_modern_plus.css");
    			break;
    		case 3: //Circular
				wp_enqueue_style("wpgmza_modern_circular", plugin_dir_url(__FILE__) . "/css/wpgmza_style_pro_modern_circular.css");
    			break;
    	}

    	if (isset($wpgmza_settings['wpgmza_settings_infowindow_link_text'])) { $wpgmza_settings_infowindow_link_text = $wpgmza_settings['wpgmza_settings_infowindow_link_text']; } else { $wpgmza_settings_infowindow_link_text = __("More details","wp-google-maps"); }
 
    	$iw_custom_styles .=  "$mapCSSSelector .wpgmza_modern_infowindow { background-color: " . (isset($map_other_settings['iw_primary_color']) ? "#" . $map_other_settings['iw_primary_color'] : "#2A3744") . "; }";
    	
    	if($infoWindowType !== 1){
    		$iw_custom_styles .=  "$mapCSSSelector .wpgmza_iw_title { color: " . (isset($map_other_settings['iw_text_color']) ? "#" . $map_other_settings['iw_text_color'] : "#ffffff") . "; }";
    	} else{
    		$iw_custom_styles .=  "$mapCSSSelector .wpgmza_iw_title { ";
    		$iw_custom_styles .=  "		color: " . (isset($map_other_settings['iw_text_color']) ? "#" . $map_other_settings['iw_text_color'] : "#ffffff") . "; " ;
    		$iw_custom_styles .=  "		background-color: " . (isset($map_other_settings['iw_accent_color']) ? "#" . $map_other_settings['iw_accent_color'] : "#252F3A") . ";";
    		$iw_custom_styles .=  " }";
    	}

    	$iw_custom_styles .=  "$mapCSSSelector .wpgmza_iw_description { color: " . (isset($map_other_settings['iw_text_color']) ? "#" . $map_other_settings['iw_text_color'] : "#ffffff") . "; }";
    	$iw_custom_styles .=  "$mapCSSSelector .wpgmza_iw_address_p { color: " . (isset($map_other_settings['iw_text_color']) ? "#" . $map_other_settings['iw_text_color'] : "#ffffff") . "; }";


    	$iw_custom_styles .=  "$mapCSSSelector .wpgmza_button { ";
    	$iw_custom_styles .=  "			color: " . (isset($map_other_settings['iw_text_color']) ? "#" . $map_other_settings['iw_text_color'] : "#ffffff") . ";";
    	$iw_custom_styles .=  "			background-color: " . (isset($map_other_settings['iw_accent_color']) ? "#" . $map_other_settings['iw_accent_color'] : "#252F3A") . ";";
    	$iw_custom_styles .=  " }";
	}
	
    	if (isset($wpgmza_settings['wpgmza_settings_infowindow_link_text'])) { $wpgmza_settings_infowindow_link_text = $wpgmza_settings['wpgmza_settings_infowindow_link_text']; } else { $wpgmza_settings_infowindow_link_text = __("More details","wp-google-maps"); }

    	$iw_output = "<div id='wpgmza_iw_holder_".$wpgmza_current_map_id."' style='display:none;'>";

    	$iw_output .= 	"<div class='wpgmza_modern_infowindow_inner wpgmza_modern_infowindow_inner_".$wpgmza_current_map_id."'>";
    	$iw_output .= 		"<div class='wpgmza_modern_infowindow_close'> x </div>";

    	$iw_output .= 		"<div class='wpgmza_iw_image'>";
    	$iw_output .= 			"<img src='' style='max-width:100% !important;' class='wpgmza_iw_marker_image' />";
    	
    	$iw_output .= 			"<div class='wpgmza_iw_title'>";
    	$iw_output .= 				"<p class='wpgmza_iw_title_p'></p>";
    	$iw_output .= 			"</div>";

    	$iw_output .= 			"";
    	$iw_output .= 		"</div>";
    	$iw_output .= 		"<div class='wpgmza_iw_address'>";
    	$iw_output .= 			"<p class='wpgmza_iw_address_p'></p>";
    	$iw_output .= 		"</div>";
    	$iw_output .= 		"<div class='wpgmza_iw_description'>";
    	$iw_output .= 			"<p class='wpgmza_iw_description_p'></p>";
    	$iw_output .= 		"</div>";
    	$iw_output .= 		"<div class='wpgmza_iw_buttons'>";
    	$iw_output .= 			"<a href='#' class='wpgmza_button wpgmza_left wpgmza_directions_button'>".__("Directions","wp-google-maps")."</a>";
    	$iw_output .= 			"<a href='#' class='wpgmza_button wpgmza_right wpgmza_more_info_button'>$wpgmza_settings_infowindow_link_text</a>";
    	$iw_output .= 		"</div>";
    	$iw_output .= 	"</div>";
    	$iw_output .= "</div>";


    //}
    

   
    if (isset($wpgmza_settings['wpgmza_settings_markerlist_category'])) { $hide_category_column = $wpgmza_settings['wpgmza_settings_markerlist_category']; }
    if (isset($wpgmza_settings['wpgmza_settings_markerlist_icon'])) { $hide_icon_column = $wpgmza_settings['wpgmza_settings_markerlist_icon']; }
	if (isset($wpgmza_settings['wpgmza_settings_markerlist_link'])) { $hide_link_column = $wpgmza_settings['wpgmza_settings_markerlist_link']; }
    if (isset($wpgmza_settings['wpgmza_settings_markerlist_title'])) { $hide_title_column = $wpgmza_settings['wpgmza_settings_markerlist_title']; }
    if (isset($wpgmza_settings['wpgmza_settings_markerlist_address'])) { $hide_address_column = $wpgmza_settings['wpgmza_settings_markerlist_address']; }
    if (isset($wpgmza_settings['wpgmza_settings_markerlist_description'])) { $hide_description_column = $wpgmza_settings['wpgmza_settings_markerlist_description']; }
    if (isset($wpgmza_settings['wpgmza_settings_filterbycat_type'])) { $filterbycat_type = $wpgmza_settings['wpgmza_settings_filterbycat_type']; } else { $filterbycat_type = false; }
    if (!$filterbycat_type) { $filterbycat_type = 1; }
    
    $map_width_type = stripslashes($res->map_width_type);
    $map_height_type = stripslashes($res->map_height_type);
    if (!isset($map_width_type)) { $map_width_type = "px"; }
    if (!isset($map_height_type)) { $map_height_type = "px"; }
    if ($map_width_type == "%" && intval($res->map_width) > 100) { $res->map_width = 100; }
    if ($map_height_type == "%" && intval($res->map_height) > 100) { $res->map_height = 100; }
    $map_align = $res->alignment;
    if (!$map_align || $map_align == "" || $map_align == "1") { $map_align = "float:left;"; }
    else if ($map_align == "2") { $map_align = "margin-left:auto !important; margin-right:auto !important; align:center;"; }
    else if ($map_align == "3") { $map_align = "float:right;"; }
    else if ($map_align == "4") { $map_align = "clear:both;"; }
    $map_style = "style=\"display:block; overflow:auto; width:".$res->map_width."".$map_width_type."; height:".$res->map_height."".$map_height_type."; $map_align\"";
    global $short_code_active;
    $short_code_active = true;
    global $wpgmza_pro_version;

	// The settings are about to be written to an element here
	// Before that happens, let's see what the value of $res->kml is
    if(!empty($res->kml)){
        $site_url = site_url();
        $res->kml = str_replace("{site_url}", $site_url, $res->kml);
    }
	
	// Using DOMDocument here to properly format the data-settings attribute
	$document = new WPGMZA\DOMDocument();
	$document->loadHTML('<div id="debug"></div>');
	
	$el = $document->querySelector("#debug");
	
	if(isset($res->other_settings) && is_string($res->other_settings))
	{
		$temp = clone $res;
		$temp->other_settings = unserialize($res->other_settings);
		
		$el->setAttribute('data-settings', json_encode($temp));
	}
	else
		$el->setAttribute('data-settings', json_encode($res));
	
	$html = $document->saveHTML();
	
	if(preg_match('/data-settings=".+"/', $html, $m) || preg_match('/data-settings=\'.+\'/', $html, $m))
	{
		$map_attributes = $m[0];
	}
	else
	{
		// Fallback if for some reason we can't match the attribute string
		$escaped = esc_attr(json_encode($res));
		$attr = str_replace('\\\\%', '%', $escaped);
		$attr = stripslashes($attr);
		$map_attributes = "data-settings='" . $attr . "'";
	}
	
	// Using DOMDocument here to properly format the data-shortcode-attributes attribute
	$document = new WPGMZA\DOMDocument();
	$document->loadHTML('<div id="debug"></div>');
	
	$el = $document->querySelector("#debug");
	$el->setAttribute('data-shortcode-attributes', json_encode($atts));
	
	$html = $document->saveHTML();
	
	if(preg_match('/data-shortcode-attributes=".+"/', $html, $m) || preg_match('/data-shortcode-attributes=\'.+\'/', $html, $m))
	{
		$map_attributes .= ' ' . $m[0];
	}
	else
	{
		// Fallback if for some reason we can't match the attribute string
		$escaped = esc_attr(json_encode($atts));
		$attr = str_replace('\\\\%', '%', $escaped);
		$attr = stripslashes($attr);
		$map_attributes = " data-shortcode-attributes='" . $attr . "'";
	}

	wp_enqueue_style( 'wpgmaps-style-pro', plugins_url('css/wpgmza_style_pro.css', __FILE__), array(), $wpgmza_pro_version );

	if(!empty($wpgmza->settings->user_interface_style))
	{
		switch($wpgmza->settings->user_interface_style)
		{
			case "legacy":
			case "modern":
				wp_enqueue_style('wpgmza_legacy_modern_pro_style', plugin_dir_url(__FILE__) . 'css/styles/legacy-modern.css', $wpgmza_pro_version);
				break;
		}
	}
	
	$wpgmaps_extra_css = ".wpgmza_map img { max-width:none; }
        .wpgmza_widget { overflow: auto; }";
    wp_add_inline_style( 'wpgmaps-style-pro', $wpgmaps_extra_css );
	wp_add_inline_style( 'wpgmaps-style-pro', $iw_custom_styles );


    $wpgmza_main_settings = get_option("WPGMZA_OTHER_SETTINGS");
    if (isset($wpgmza_main_settings['wpgmza_custom_css']) && $wpgmza_main_settings['wpgmza_custom_css'] != "") { 
		// TODO: Slashes should be stripped on input really, however please bear in mind removing this call may break CSS for existing users. A version check is in order here
		$style = html_entity_decode(stripslashes($wpgmza_main_settings['wpgmza_custom_css']));
        wp_add_inline_style( 'wpgmaps-style-pro', $style );
    }

    global $wpgmza_short_code_array;
    $wpgmza_short_code_array[] = $wpgmza_current_map_id;
    
    
    $filterbycat = $res->filterbycat;
    $map_width = $res->map_width;
    $map_width_type = $res->map_width_type;
    // for marker list
    $default_marker = $res->default_marker;

    if (isset($atts['zoom'])) {
        $zoom_override = $atts['zoom'];
        if (!isset($wpgmza_override['zoom'])) {
        	$wpgmza_override['zoom'] = array();
        }
        $wpgmza_override['zoom'][$wpgmza_current_map_id] = $zoom_override;
    }    

     if (isset($atts['new_window_link'])) {
        $new_window_link = $atts['new_window_link'];
        $wpgmza_override['new_window_link'][$wpgmza_current_map_id] = $new_window_link;
    }
	
    $show_location = $res->show_user_location;
    
	$use_location_from = "";
	$use_location_to = "";
	
    if ($default_marker) { $default_marker = "<img src='".$default_marker."' />"; } else { $default_marker = "<img src='".wpgmaps_get_plugin_url()."/images/marker.png' />"; }
  
    $wpgmza_marker_list_output = "";
    $wpgmza_marker_filter_output = "";
    // Filter by category
    

   	/**
	 * Handle 'category' filter override attribute
	 */
    if (isset($atts['enable_category'])) { 
    	$filterbycat = intval($atts['enable_category']);
    }

    
   if ($filterbycat == 1) {
        
		$wpgmza_marker_filter_output .= "<div class='wpgmza-marker-listing-category-filter' data-map-id='$wpgmza_current_map_id' id='wpgmza_filter_".$wpgmza_current_map_id."' style='text-align:left; margin-bottom:0px;'><span>".__("Filter by","wp-google-maps")."</span>";
		
		if (intval($filterbycat_type) == 2)
		{	
            $wpgmza_marker_filter_output .= "<div style=\"overflow:auto; display:block; width:100%; height:auto; margin-top:10px;\">";
			
            $wpgmza_marker_filter_output .= $map->categoryFilterWidget->html;
			
            $wpgmza_marker_filter_output .= "</div>";
		}
		else
            $wpgmza_marker_filter_output .= $map->categoryFilterWidget->html;
		
		$wpgmza_marker_filter_output .= "</div>";
    }
	
    $wpgmza_marker_datatables_output = "";
    if (isset($hide_category_column) && $hide_category_column == "yes") { $wpgmza_marker_datatables_output .= "<style>.wpgmza_table_category { display: none !important; }</style>"; }
    if (isset($hide_icon_column) && $hide_icon_column == "yes") { $wpgmza_marker_datatables_output .= "<style>.wpgmza_table_marker { display: none; }</style>"; }
    if (isset($hide_title_column) && $hide_title_column == "yes") { $wpgmza_marker_datatables_output .= "<style>.wpgmza_table_title { display: none; }</style>"; }
    if (isset($hide_address_column) && $hide_address_column == "yes") { $wpgmza_marker_datatables_output .= "<style>.wpgmza_table_address { display: none; }</style>"; }
    if (isset($hide_description_column) && $hide_description_column == "yes") { $wpgmza_marker_datatables_output .= "<style>.wpgmza_table_description { display: none; }</style>"; }
    
	$sl_data = "";
	if($map->storeLocator)
		$sl_data = $map->storeLocator->html;
	
	$columns = implode(', ', wpgmza_get_marker_columns());
	
	if(isset($map_other_settings['list_markers_by']) && $map_other_settings['list_markers_by'] == '6') {
		
		switch($res->order_markers_by)
		{
			case 2:
				$order_by = "title";
				break;
				
			case 3:
				$order_by = "address";
				break;
				
			case 4:
				$order_by = "desc";
				break;
				
			case 5:
				$order_by = "category";
				break;
				
			case 6:
				$order_by = "priority";
				break;
			
			default:
				$order_by = "id";
				break;
		}
		
		$order_dir = ($res->order_markers_choice == '2' ? 'DESC' : 'ASC');
		
		$where = "WHERE map_id = " . (int)$atts['id'];
		
		if(!empty($atts['mashup_ids']))
			$where = "WHERE map_id IN (" . implode(', ', array_map('intval', explode(',', $atts['mashup_ids']))) . ")";
		
		$where .= ' AND approved = 1';
		
		if($order_by == 'priority')
		{
			$qstr = "SELECT {$wpdb->prefix}wpgmza.id 
				FROM `{$wpdb->prefix}wpgmza` 
				LEFT JOIN {$wpdb->prefix}wpgmza_categories ON SUBSTRING_INDEX(category, ',', 1) = {$wpdb->prefix}wpgmza_categories.id 
				$where
				ORDER BY priority $order_dir";
		}
		else
		{
			$qstr = "SELECT id FROM {$wpdb->prefix}wpgmza $where ORDER BY $order_by $order_dir";
		}
		
		$marker_id_order = $wpdb->get_col($qstr);
		
		wp_enqueue_script('wpgmza_dummy', plugin_dir_url(__FILE__) . 'dummy.js');
		
		wp_localize_script('wpgmza_dummy', 'wpgmza_modern_marker_listing_marker_order_by_id_for_map_' . (int)$atts['id'], $marker_id_order);
		
		do_action('wpgmza_modern_marker_listing_marker_order', (int)$atts['id'], $marker_id_order);
	}
	
    if (!empty($map_other_settings['list_markers_by'])) {
		
		$style = $map_other_settings['list_markers_by'];
		$params = array(
			'map_id'	=> $wpgmza_current_map_id
		);
		
		if($wpgmza_current_mashup)
			$params['mashup_ids'] = $wpgmza_mashup_ids[$wpgmza_current_map_id];
		
		$listing = WPGMZA\MarkerListing::createInstanceFromStyle($style, $wpgmza_current_map_id);
		$listing->setAjaxParameters($params);
		
		$wpgmza_marker_list_output = $listing->html();
		
    } else {
    
        if ($res->listmarkers == 1 && $res->listmarkers_advanced == 1) {
            if ($wpgmza_current_mashup) {
                $wpgmza_marker_list_output .= wpgmza_return_marker_list($wpgmza_mashup_parent_id,false,$map_width.$map_width_type,$wpgmza_current_mashup,$wpgmza_mashup_ids[$atts['id']]);
            } else {
                $wpgmza_marker_list_output .= wpgmza_return_marker_list($wpgmza_current_map_id,false,$map_width.$map_width_type,false);
            }
        }
        else if ($res->listmarkers == 1 && $res->listmarkers_advanced == 0) {

            global $wpdb;
            global $wpgmza_tblname;

            // marker sorting functionality
            if ($res->order_markers_by == 1) { $order_by = "id"; }
            else if ($res->order_markers_by == 2) { $order_by = "title"; }
            else if ($res->order_markers_by == 3) { $order_by = "address"; }
            else if ($res->order_markers_by == 4) { $order_by = "description"; }
            else if ($res->order_markers_by == 5) { $order_by = "category"; }
            else { $order_by = "id"; }
            if ($res->order_markers_choice == 1) { $order_choice = "ASC"; }
            else { $order_choice = "DESC"; }

            if ($wpgmza_current_mashup) {

                $wpgmza_cnt = 0;
                $sql_string1 = "";
                if ($wpgmza_mashup_ids[$atts['id']][0] == "ALL") {
                    $wpgmza_sql1 ="SELECT $columns FROM $wpgmza_tblname ORDER BY `$order_by` $order_choice";
                } else {
                    $wpgmza_id_cnt = count($wpgmza_mashup_ids[$atts['id']]);
                    foreach ($wpgmza_mashup_ids[$atts['id']] as $wpgmza_map_id) {
						
						$wpgmza_map_id = (int)$wpgmza_map_id;
						
                        $wpgmza_cnt++;
                        if ($wpgmza_cnt == 1) { $sql_string1 .= "`map_id` = '$wpgmza_map_id' "; }
                        elseif ($wpgmza_cnt > 1 && $wpgmza_cnt < $wpgmza_id_cnt) { $sql_string1 .= "OR `map_id` = '$wpgmza_map_id' "; }
                        else { $sql_string1 .= "OR `map_id` = '$wpgmza_map_id' "; }

                    }
                    $wpgmza_sql1 ="SELECT $columns FROM $wpgmza_tblname WHERE $sql_string1 ORDER BY `$order_by` $order_choice";
                }
            } else {
                $wpgmza_sql1 ="SELECT $columns FROM $wpgmza_tblname WHERE `map_id` = '" . intval($wpgmza_current_map_id) . "' ORDER BY `$order_by` $order_choice";
            }

            $results = $wpdb->get_results($wpgmza_sql1);

            $wpgmza_marker_list_output .= "
                    <div style='clear:both;'>
                    <table id=\"wpgmza_marker_list\" class=\"wpgmza_marker_list_class\" cellspacing=\"0\" cellpadding=\"0\" style='width:".$map_width."".$map_width_type."'>
                    <tbody>
            ";


            $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
			if (isset($wpgmza_settings['wpgmza_settings_image_resizing']) && $wpgmza_settings['wpgmza_settings_image_resizing'] == 'yes') { $wpgmza_image_resizing = true; } else { $wpgmza_image_resizing = false; }
            if (isset($wpgmza_settings['wpgmza_settings_image_height'])) { $wpgmza_image_height = $wpgmza_settings['wpgmza_settings_image_height']; } else { $wpgmza_image_height = false; }
            if (isset($wpgmza_settings['wpgmza_settings_image_height'])) { $wpgmza_image_height = $wpgmza_settings['wpgmza_settings_image_height']."px"; } else { $wpgmza_image_height = false; }
            if (isset($wpgmza_settings['wpgmza_settings_image_width'])) { $wpgmza_image_width = $wpgmza_settings['wpgmza_settings_image_width']."px"; } else { $wpgmza_image_width = false; }
            if (!$wpgmza_image_height || !isset($wpgmza_image_height)) { $wpgmza_image_height = "auto"; }
            if (!$wpgmza_image_width || !isset($wpgmza_image_width)) { $wpgmza_image_width = "auto"; }
            $wmcnt = 0;
            foreach ( $results as $result ) {
                $wmcnt++;
                $img = $result->pic;
                $wpgmaps_id = $result->id;
                $link = $result->link;
                $icon = $result->icon;
                $wpgmaps_lat = $result->lat;
                $wpgmaps_lng = $result->lng;
                $wpgmaps_address = $result->address;
            	/* added in 5.52 - phasing out timthumb */
            	/* timthumb completely removed in 5.54 */
                /*if ($wpgmza_use_timthumb == "" || !isset($wpgmza_use_timthumb)) {
					$pic = "<img src='".wpgmaps_get_plugin_url()."/timthumb.php?src=".$result->pic."&h=".$wpgmza_image_height."&w=".$wpgmza_image_width."&zc=1' />";
                } else {*/
		            if (!$img) { $pic = ""; } else {
		        		if ($wpgmza_image_resizing) {
		                    $pic = "<img src='".$result->pic."' class='wpgmza_map_image' style=\"margin:5px; height:".$wpgmza_image_height."px; width:".$wpgmza_image_width.".px\" />";
		                } else {
		                    $pic = "<img src='".$result->pic."' class='wpgmza_map_image' style=\"margin:5px;\" />";
		                }
                   	}
                /*}*/
                if (!$icon) { $icon = $default_marker; } else { $icon = "<img src='".$result->icon."' />"; }
                if ($d_enabled == "1") {
                    $wpgmaps_dir_text = "<br />
						<a href=\"javascript:void(0);\" 
							id=\"$wpgmza_current_map_id\" 
							data-map-id=\"$wpgmza_current_map_id\"
							title=\"".__("Get directions to","wp-google-maps")." ".$result->title."\" 
							class=\"wpgmza_gd\" 
							wpgm_addr_field=\"".$wpgmaps_address."\" 
							gps=\"$wpgmaps_lat,$wpgmaps_lng\"
							>".__("Directions","wp-google-maps")."</a>";
                } else { $wpgmaps_dir_text = ""; }
                if ($result->description) {
                    $wpgmaps_desc_text = "<br />".$result->description."";
                } else {
                    $wpgmaps_desc_text = "";
                }
                if ($wmcnt%2) { $oddeven = "wpgmaps_odd"; } else { $oddeven = "wpgmaps_even"; }



                $wpgmza_marker_list_output .= "
                    <tr id=\"wpgmza_marker_".$result->id."\" mid=\"".$result->id."\" mapid=\"".$result->map_id."\" class=\"wpgmaps_mlist_row $oddeven\">
                        <td height=\"40\" class=\"wpgmaps_mlist_marker\">".$icon."</td>
                        <td class=\"wpgmaps_mlist_pic\" style=\"width:".($wpgmza_image_width+20)."px;\">$pic</td>
                        <td  valign=\"top\" align=\"left\" class=\"wpgmaps_mlist_info\">
                            <strong><a href=\"javascript:openInfoWindow($wpgmaps_id);\" id=\"wpgmaps_marker_$wpgmaps_id\" title=\"".stripslashes($result->title)."\">".stripslashes($result->title)."</a></strong>
                            ".stripslashes($wpgmaps_desc_text)."
                            $wpgmaps_dir_text
                        </td>

                    </tr>";
            }
            $wpgmza_marker_list_output .= "</tbody></table></div>";

        } else { $wpgmza_marker_list_output = ""; }
    }

	global $wpgmza;
	
	$dbox_option = $res->dbox;
	
	if($map->isDirectionsEnabled())
		$dbox_div = $map->directionsBox->html;
	else
		$dbox_div = "";
		
    if ($dbox_option == "5" || $dbox_option == "1" || !isset($dbox_option)) {
        

        if ($wpgmza_current_mashup) {
            $wpgmza_anchors = $wpgmza_mashup_ids[$atts['id']];
        } else {
            $wpgmza_anchors = $wpgmza_current_map_id;
        }

        $ret_msg = "
            $wpgmza_marker_datatables_output
            ".wpgmaps_check_approval_string()."
            ".wpgmaps_return_marker_anchors($wpgmza_anchors)."
            <a name='map".$wpgmza_current_map_id."'></a>
            $wpgmza_marker_filter_output
            ".apply_filters("wpgooglemaps_filter_map_output","",$wpgmza_current_map_id)."
            ".(!isset($map_other_settings['store_locator_below']) ? "$sl_data" : "")."
            ".(isset($map_other_settings['store_marker_listing_below']) ? "$wpgmza_marker_list_output" : "")."

            ".apply_filters("wpgooglemaps_filter_map_div_output","<div class=\"wpgmza_map $additionalClasses\" $mashup_ids_attributes id=\"wpgmza_map_".$wpgmza_current_map_id."\" $map_style $map_attributes> </div>",$wpgmza_current_map_id)."
            ".(isset($map_other_settings['store_locator_below']) ? "$sl_data" : "")."
            ".(!isset($map_other_settings['store_marker_listing_below']) ? "$wpgmza_marker_list_output" : "")."   
        ";

        if ($map->isDirectionsEnabled()) {
        	$ret_msg .= "<div style=\"display:block; width:100%;\">
				
				$dbox_div
				
				<div id=\"wpgmaps_directions_notification_".$wpgmza_current_map_id."\" style=\"display:none;\">".__("Fetching directions...","wp-google-maps")."...</div>
				
				<div id=\"wpgmaps_directions_reset_".$wpgmza_current_map_id."\" style=\"display:none;\">
					<a href='javascript:void(0)' onclick='wpgmza_reset_directions(".$wpgmza_current_map_id.");' id='wpgmaps_reset_directions' title='".__("Reset directions","wp-google-maps")."'>".__("Reset directions","wp-google-maps")."</a>
					<br />
					<a href='javascript: ;' id='wpgmaps_print_directions_".$wpgmza_current_map_id."' target='_blank' title='".__("Print directions","wp-google-maps")."'>".__("Print directions","wp-google-maps")."</a>
				</div>
				
				<div id=\"directions_panel_".$wpgmza_current_map_id."\"></div>

			</div>";
        }

    } else {
        if ($wpgmza_current_mashup) {
            $wpgmza_anchors = $wpgmza_mashup_ids[$atts['id']];
        } else {
            $wpgmza_anchors = $wpgmza_current_map_id;
        }

        
        $ret_msg = "
			$wpgmza_marker_datatables_output

			<div style=\"display:block; width:100%;\">

				$dbox_div
			
				<div id=\"wpgmaps_directions_notification_".$wpgmza_current_map_id."\" style=\"display:none;\">".__("Fetching directions...","wp-google-maps")."...</div>
				<div id=\"wpgmaps_directions_reset_".$wpgmza_current_map_id."\" style=\"display:none;\">
					<a href='javascript:void(0)' onclick='wpgmza_reset_directions(".$wpgmza_current_map_id.");' id='wpgmaps_reset_directions' title='".__("Reset directions","wp-google-maps")."'>".__("Reset directions","wp-google-maps")."</a>
					<br />
					<a href='javascript: ;' id='wpgmaps_print_directions_".$wpgmza_current_map_id."' target='_blank' title='".__("Print directions","wp-google-maps")."'>".__("Print directions","wp-google-maps")."</a>
				</div>
			
				<div id=\"directions_panel_".$wpgmza_current_map_id."\"></div>
			
			</div>

			$wpgmza_marker_filter_output
			".(!isset($map_other_settings['store_locator_below']) ? "$sl_data" : "")."
			".(isset($map_other_settings['store_marker_listing_below']) ? "$wpgmza_marker_list_output" : "")."

			".wpgmaps_return_marker_anchors($wpgmza_anchors)."
            <a name='map".$wpgmza_current_map_id."'></a>

			".apply_filters("wpgooglemaps_filter_map_div_output","<div class=\"wpgmza_map $additionalClasses\" id=\"wpgmza_map_".$wpgmza_current_map_id."\" $map_style $map_attributes $mashup_ids_attributes> </div>", $wpgmza_current_map_id)."   
			".(isset($map_other_settings['store_locator_below']) ? "$sl_data" : "")."

			".(!isset($map_other_settings['store_marker_listing_below']) ? "$wpgmza_marker_list_output" : "")."
			

        ";

    }

    if (function_exists("wpgmza_register_ugm_version")) {
        $ugm_enabled = $res->ugm_enabled;
        if ($ugm_enabled == 1) {

     		if (isset($atts['disable_vgm_form']) && $atts['disable_vgm_form'] == '1') {
     			/* do nothing */
     		} else {
            	$ret_msg .= wpgmaps_ugm_user_form($wpgmza_current_map_id, false, false);
            }
        }
    }
    
    
    if ($wpgmza_using_custom_meta) {
        /* we're using meta fields to generate the map, ignore default functionality */
        
        $ret_msg = "
            ".apply_filters("wpgooglemaps_filter_map_div_output","<div class=\"wpgmza_map $additionalClasses\" id=\"wpgmza_map_".$wpgmza_current_map_id."\" $map_style $map_attributes $mashup_ids_attributes> </div>", $wpgmza_current_map_id)."

            ";
    }
    

    




    if (isset($atts['marker'])) {
        $wpgmza_focus_marker = $atts['marker'];
        if (!isset($wpgmza_override['marker'])) {
        	$wpgmza_override['marker'] = array();
        }
        $wpgmza_override['marker'][$wpgmza_current_map_id] = $wpgmza_focus_marker;
    }    

	if(empty($wpgmza->settings->disable_autoptimize_compatibility_fix))
	{
		// Autoptimize fix, bypass CSS where our map is present as large amounts of inline JS (our localized data) crashes their plugin. Added at their advice.
		add_filter('autoptimize_filter_css_noptimize', '__return_true');
	}
	
    return $ret_msg;
}


add_action('wp_footer','wpgmza_output_user_js_pro');
 /**
 * Output user JS
 */
function wpgmza_output_user_js_pro() {
	wpgmaps_user_javascript_pro();

}

/**
 * @deprecated 6.4.00
 */
function wpgmza_generate_marker_list($map_id, $type) {
    global $wpdb;
    
}

function wpgmaps_check_approval_string() {
    if (isset($_POST['wpgmza_approval'] ) && $_POST['wpgmza_approval'] == "1") {
        return "<p class='wpgmza_marker_approval_msg'>".__("Thank you. Your marker is awaiting approval.","wp-google-maps")."</p>";

    }
}

function wpgmaps_return_marker_anchors($mid) {
	/* deprecated in 6.09 - causes irrelevant anchors (for each marker) to be displayed on the map only for the event of clicking on the marker and centering the page to the top of the map. A single anchor can achieve the same */
	return "";
}
function wpgmza_return_all_map_ids() {
    global $wpdb;
    global $wpgmza_tblname_maps;
    $sql = "SELECT `id` FROM `".$wpgmza_tblname_maps."` WHERE `active` = 0";
    $results = $wpdb->get_results($sql);
    $tarr = array();
    foreach ($results as $result) {
        array_push($tarr,$result->id);
    }
    return $tarr;

}

if(!function_exists('wpgmza_get_circle_data'))
{
	function wpgmza_get_circle_data($map_id)
	{
		global $wpdb;
		global $wpgmza;
		global $wpgmza_tblname_circles;
		
		$stmt = $wpdb->prepare("SELECT *, {$wpgmza->spatialFunctionPrefix}AsText(center) AS center FROM $wpgmza_tblname_circles WHERE map_id=%d", array($map_id));
		$results = $wpdb->get_results($stmt);
		
		$circles = array();
		foreach($results as $obj)
			$circles[$obj->id] = $obj;
		
		return $circles;
	}
}
	
if(!function_exists('wpgmza_get_rectangle_data'))
{
	function wpgmza_get_rectangle_data($map_id)
	{
		global $wpdb;
		global $wpgmza;
		global $wpgmza_tblname_rectangles;
		
		$stmt = $wpdb->prepare("SELECT *, {$wpgmza->spatialFunctionPrefix}AsText(cornerA) AS cornerA, {$wpgmza->spatialFunctionPrefix}AsText(cornerB) AS cornerB FROM $wpgmza_tblname_rectangles WHERE map_id=%d", array($map_id));
		$results = $wpdb->get_results($stmt);
		
		$rectangles = array();
		foreach($results as $obj)
			$rectangles[$obj->id] = $obj;
		
		return $rectangles;
	}
}

function wpgmza_apply_setting_overrides($input, $atts = null)
{
	if(empty($input))
		return $input;
	
	if(is_object($input))
		$input = (array)$input;
	
	if(!is_array($input))
		throw new Exception("Input must be an array");
	
	if(!empty($atts))
		$input = array_merge($input, $atts);
	
	if(!empty($_GET))
	{
		$clone = array_merge(array(), $_GET);
		unset($clone['id']);
		
		$input = array_merge($input, $clone);
	}
	
	return $input;
}

function wpgmaps_user_javascript_pro($atts = false) {

    global $short_code_active;

    if ($short_code_active == true) {

	    global $wpgmza_count;
	    $wpgmza_count++;
	    if ($wpgmza_count >1) {  } else {
	    global $wpgmza_current_map_id;
	    global $wpgmza_short_code_array;
	    global $wpgmza_current_mashup;
	    global $wpgmza_pro_version;
	    
	    global $wpgmza_current_map_cat_selection;
	    global $wpgmza_current_map_shortcode_data;
	    global $wpgmza_current_map_type;
		
	    if ($wpgmza_current_mashup) { $wpgmza_current_mashup_string = "true"; } else { $wpgmza_current_mashup_string = "false"; }
	    
	    global $wpgmza_mashup_ids;
	    if (isset($wpgmza_mashup_ids)) {
	        if (isset($wpgmza_mashups_ids) && $wpgmza_mashups_ids == "ALL") {
	            $wpgmza_mashup_ids = wpgmza_return_all_map_ids();
	        }
	    }
		
		$wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
		
		global $wpgmza_google_maps_api_loader;
		$wpgmza_google_maps_api_loader->enqueueGoogleMaps();
        
        global $wpgmza_pro_version;
        $ajax_nonce = wp_create_nonce("wpgmza");
		
		// If wpgmza_do_not_enqueue_datatables is set, do not load datatables.
        if (empty($wpgmza_settings['wpgmza_do_not_enqueue_datatables'])) {
	        wp_register_script('wpgmaps_datatables', plugins_url(plugin_basename(dirname(__FILE__)))."/js/jquery.dataTables.js", true);
	        wp_enqueue_script( 'wpgmaps_datatables' );
			
	        wp_register_script('wpgmaps_datatables-responsive', plugins_url(plugin_basename(dirname(__FILE__)))."/js/dataTables.responsive.js", true);
	        wp_enqueue_script( 'wpgmaps_datatables-responsive' );

	        wp_register_style('wpgmaps_datatables_style', plugins_url(plugin_basename(dirname(__FILE__)))."/css/data_table_front.css", array(), $wpgmza_pro_version);
	        wp_enqueue_style( 'wpgmaps_datatables_style' );
	        wp_register_style('wpgmaps_datatables_responsive-style', plugin_dir_url(__FILE__) . "lib/dataTables.responsive.css", array(), $wpgmza_pro_version);
	        wp_enqueue_style( 'wpgmaps_datatables_responsive-style' );
       	}
		
		$circle_data_array = array();
		$rectangle_data_array = array();

        $wpgmza_using_custom_fields = false;
            
            $res = array();
            $marker_data_array = array();
            

            /**
             * Used for acquiring category data for all the maps on the page
             * @var array
             */
            $map_id_array = array();


            $include_owl = false;
			$mashup_js_string = "";

            if (isset($wpgmza_short_code_array)) {

				
                foreach ($wpgmza_short_code_array as $wpgmza_cmd) {

					$map_id_array[$wpgmza_cmd] = "1";


					if (isset($wpgmza_mashup_ids[$wpgmza_cmd])) { $mashup_js_string .= "wpgmaps_map_mashup[$wpgmza_cmd] = true;\n"; }
                	$marker_data_array[$wpgmza_cmd] = array();
		 			
					/*if ($wpgmza_settings['wpgmza_settings_marker_pull'] == "0") {
		            	if (isset($wpgmza_mashup_ids[$wpgmza_cmd])) {


			                foreach ($wpgmza_mashup_ids[$wpgmza_cmd] as $mashup_id) {
			                    
								
			                	$temp_marker_array = wpgmaps_return_markers($mashup_id);
			                	
			                	foreach ($temp_marker_array as $temp_array) {
	                				array_push($marker_data_array[$wpgmza_cmd], $temp_array);
			                	}

		                        
		            		}
		            	} else {
		                    if ($wpgmza_settings['wpgmza_settings_marker_pull'] == "0" || $wpgmza_settings['wpgmza_settings_marker_pull'] == 0) {
								
		                        $marker_data_array[$wpgmza_cmd] = wpgmaps_return_markers($wpgmza_cmd);
		                    }

		            	}
		            }*/
					
					
                    $res[$wpgmza_cmd] = wpgmza_get_map_data($wpgmza_cmd);
                    
                    /* Added in version 5.44
                     */
                    
                    /* check if we are using custom fields instead of traditional map data */
                    if (isset($wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lat']) && isset($wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lng']) && isset($wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['parent_id']) && $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lng'] && $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lat']) {
                        /* we are using custom fields, get the parent map data */
                            $wpgmza_using_custom_fields = true;
							
							
                            $res[$wpgmza_cmd] = wpgmza_get_map_data($wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['parent_id']);
                            $temp_other_settings = maybe_unserialize($res[$wpgmza_cmd]->other_settings);
                            $temp_other_settings['store_locator_enabled'] = 0;
                            $res[$wpgmza_cmd]->other_settings['store_locator_enabled'] = 0;
                            $res[$wpgmza_cmd]->other_settings = maybe_serialize($temp_other_settings);
                    } else {
                        $wpgmza_using_custom_fields = false;
                    }

                    
                    /* end of 5.44 addition */
                    
                    
                    
					
                    if ($res[$wpgmza_cmd]->styling_json != '') {
                        $res[$wpgmza_cmd]->styling_json = html_entity_decode(stripslashes($res[$wpgmza_cmd]->styling_json));
                    }
                    if ($res[$wpgmza_cmd]->other_settings != '') {
                        $res[$wpgmza_cmd]->other_settings = $other_settings = maybe_unserialize($res[$wpgmza_cmd]->other_settings);
						
                        if (isset($other_settings['list_markers_by']) && $other_settings['list_markers_by'] == '3') { $include_owl = true; }
                        if (isset($other_settings['wpgmza_theme_data']) && $other_settings['wpgmza_theme_data'] != false) { $res[$wpgmza_cmd]->other_settings['wpgmza_theme_data'] = html_entity_decode(stripslashes($other_settings['wpgmza_theme_data'])); }
                    }
                    $res[$wpgmza_cmd]->map_width_type = stripslashes($res[$wpgmza_cmd]->map_width_type);
					
					
                    $res[$wpgmza_cmd]->total_markers = wpgmza_return_marker_count($wpgmza_cmd);
                    
                    
					$circle_data_array[$wpgmza_cmd] = wpgmza_get_circle_data($wpgmza_cmd);
					
					
					$rectangle_data_array[$wpgmza_cmd] = wpgmza_get_rectangle_data($wpgmza_cmd);
					
                	/** handle directions override attribute from shortcode */
                	if ($atts) {
					    if (isset($atts['enable_directions'])) { 
					    	$res[$wpgmza_cmd]->directions_enabled = $atts['enable_directions'];
					        // carousel marker listing fix
					        echo '<style>.wpgmza_marker_directions_link { display:none; }</style>';

					    }
				    }
                    
                    

                }
            }
			
			


            /**
             * Get all category data for all current maps for localization
             */
            $category_data_array = array();

			
            foreach ( $map_id_array as $key_map => $key_val ) {
            	$category_data_array_tmp = array();

            	$category_data_array[$key_map] = array();
            	
				
            	$tmp_cat_data = wpgmza_get_category_localized_data( $key_map );
            	foreach ( $tmp_cat_data as $tmp_cat_data_single ) {

            		$category_data_array_tmp[intval( $tmp_cat_data_single->cat_id )] = intval( $tmp_cat_data_single->parent );
            	}
            	$category_data_array[$key_map] = $category_data_array_tmp;
            } 

            
            
   
            if (function_exists("wpgmaps_gold_activate")) {
                wp_register_script('wpgmaps_user_marker_clusterer_js', wpgmaps_get_plugin_url() .'/js/markerclusterer.js',array(),"1.0",false);
                wp_enqueue_script( 'wpgmaps_user_marker_clusterer_js' );
                
            }

            if ($include_owl || true) {
				
                /*wp_register_script('owl_carousel', plugin_dir_url(__FILE__) .'js/owl.carousel.min.js', array(), $wpgmza_pro_version.'p' , false);
                wp_enqueue_script( 'owl_carousel' );
                wp_register_style('owl_carousel_style', plugin_dir_url(__FILE__) .'css/owl.carousel.css', array(), $wpgmza_pro_version);
                wp_enqueue_style( 'owl_carousel_style' );
                wp_register_style('owl_carousel_style_theme', plugin_dir_url(__FILE__) .'css/owl.theme.css', array(), $wpgmza_pro_version);
                wp_enqueue_style( 'owl_carousel_style_theme' );*/
				
                if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'sky') { 
                    wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'/css/carousel_sky.css', array(), $wpgmza_pro_version);
                    wp_enqueue_style( 'owl_carousel_style_theme_select' );
                } else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'sun') { 
                    wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'/css/carousel_sun.css', array(), $wpgmza_pro_version);
                    wp_enqueue_style( 'owl_carousel_style_theme_select' );
                } else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'earth') { 
                    wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'/css/carousel_earth.css', array(), $wpgmza_pro_version);
                    wp_enqueue_style( 'owl_carousel_style_theme_select' );
                } else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'monotone') { 
                    wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'/css/carousel_monotone.css', array(), $wpgmza_pro_version);
                    wp_enqueue_style( 'owl_carousel_style_theme_select' );
                } else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'pinkpurple') { 
                    wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'/css/carousel_pinkpurple.css', array(), $wpgmza_pro_version);
                    wp_enqueue_style( 'owl_carousel_style_theme_select' );
                } else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'white') { 
                    wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'/css/carousel_white.css', array(), $wpgmza_pro_version);
                    wp_enqueue_style( 'owl_carousel_style_theme_select' );
                } else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'black') { 
                    wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'/css/carousel_black.css', array(), $wpgmza_pro_version);
                    wp_enqueue_style( 'owl_carousel_style_theme_select' );
                } else {
                    wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'/css/carousel_sky.css', array(), $wpgmza_pro_version);
                    wp_enqueue_style( 'owl_carousel_style_theme_select' );
                }
                
            }
			
			
			global $wpgmza;
			$wpgmza->loadScripts();

			
            wp_enqueue_script('wpgmaps_core', plugin_dir_url(__FILE__) .'js/core.js', array('wpgmza'), $wpgmza_pro_version.'p' , false);
			wpgmza_enqueue_fontawesome();
			
			wp_localize_script('wpgmaps_core', 'wpgmza_localized_strings', array(
				'no_results_found' 			=> __('No results found', 'wp-google-maps'),
				'zero_results' 				=> __('Zero results', 'wp-google-maps'),
				'max_waypoints_exceeded' 	=> __('Max waypoints exceeded', 'wp-google-maps'),
				'max_route_length_exceeded' => __('Max route length exceeded', 'wp-google-maps'),
				'invalid_request' 			=> __('Invalid request', 'wp-google-maps'),
				'over_query_limit' 			=> __('Over query limit', 'wp-google-maps'),
				'request_denied' 			=> __('Request denied', 'wp-google-maps'),
				'unknown_error' 			=> __('Unknown error', 'wp-google-maps'),
				
				'link'						=> __('Link', 'wp-google-maps'),
				'directions'				=> __('Directions', 'wp-google-maps'),
				'zoom'						=> __('Zoom', 'wp-google-maps')
			));
			
			wp_localize_script('wpgmaps_core', 'wpgmza_ajax_loader_gif', array(
				'src' => plugin_dir_url(__FILE__) . 'images/AjaxLoader.gif'
			));
			
			
            do_action("wpgooglemaps_hook_user_js_after_core");

            
            if ( function_exists( "wpgmaps_ugm_activate" ) ) {
                global $wpgmza_ugm_version;
			    $wpgmza_vgmc = floatval(str_replace(".","",$wpgmza_ugm_version));
			    
			    if ($wpgmza_vgmc < 300) {
			    	/* only load this if the version is less than 3.00 */
                	wp_enqueue_script('wpgmaps_ugm_core', plugins_url('wp-google-maps-ugm') .'/js/ugm-core.js', array('wpgmaps_core'), $wpgmza_ugm_version.'vgm' , false);

                }
                
            }
            
            if (function_exists("wpgmaps_sl_activate")) {
                global $wpgmza_sl_version;
                wp_enqueue_script('wpgmaps_sl_core', plugins_url('wp-google-maps-store-locator') .'/js/sl-core.js', array(), $wpgmza_sl_version.'sl' , false);
            }
            
            
            global $wpgmza_pro_version;
            
            
            if (isset($wpgmza_settings['list_markers_by'])) { } else { $wpgmza_settings['list_markers_by'] = false; }
            
			global $wpgmza_shortcode_atts_by_map_id;
			foreach($res as $map_id => $settings)
			{
				$atts = $wpgmza_shortcode_atts_by_map_id[$settings->id];
				
				
				$res[$map_id]->other_settings = wpgmza_apply_setting_overrides($res[$map_id]->other_settings, $atts);
				$res[$map_id] = wpgmza_apply_setting_overrides($res[$map_id], $atts);
			}
			
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_localize', $res);
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_localize_mashup_ids', $wpgmza_mashup_ids);
            if ($wpgmza_settings['wpgmza_settings_marker_pull'] == "0") {
				
                wp_localize_script( 'wpgmaps_core', 'wpgmaps_localize_marker_data', $marker_data_array);
            }
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_localize_cat_ids', $wpgmza_current_map_cat_selection);
            if ($wpgmza_using_custom_fields) {
                wp_localize_script( 'wpgmaps_core', 'wpgmaps_localize_shortcode_data', $wpgmza_current_map_shortcode_data);
            }
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_localize_map_types', $wpgmza_current_map_type);

            $wpgmza_settings = apply_filters("wpgmza_filter_localize_settings",$wpgmza_settings);

            wp_localize_script( 'wpgmaps_core', 'wpgmaps_localize_global_settings', $wpgmza_settings);
            


            wp_localize_script( 'wpgmaps_core', 'wpgmaps_localize_categories', $category_data_array);



            if ($wpgmza_mashup_ids !== null) {
            	wp_localize_script( 'wpgmaps_core', 'wpgmza_mashup_ids', $wpgmza_mashup_ids);
            }
            
			
            do_action("wpgooglemaps_hook_user_js_after_localize",$res);
            
            $polygonoptions = array();
            $datasetoptions = array();
            $polylineoptions = array();
            
            // get polyline and polygon settings and localize it
            if (isset($wpgmza_short_code_array)) {

				
                foreach ($wpgmza_short_code_array as $wpgmza_cmd) {
                    if ($wpgmza_current_mashup) {
                         foreach ($wpgmza_mashup_ids as $wpgmza_tmp_plg_array) {
                         	foreach ($wpgmza_tmp_plg_array as $wpgmza_tmp_plg) {
	                            $total_poly_array = wpgmza_b_return_dataset_id_array($wpgmza_tmp_plg);
	                            if ($total_poly_array > 0) {
	                                foreach ($total_poly_array as $dataset_id) {
	                                    $datasetoptions[$wpgmza_cmd][$dataset_id] = wpgmza_b_return_dataset_options($dataset_id);
	                                    $dataset_second_options[$wpgmza_cmd][$dataset_id] = maybe_unserialize($datasetoptions[$wpgmza_cmd][$dataset_id]->options);

	                                    $tmp_poly_array = wpgmza_b_return_dataset_array($dataset_id);
	                                    $poly_data_raw_array = array();
	                                    foreach ($tmp_poly_array as $single_poly) {
	                                        $poly_data_raw = str_replace(" ","",$single_poly);
	                                        $poly_data_raw = explode(",",$poly_data_raw);
	                                        $lat = $poly_data_raw[0];
	                                        $lng = $poly_data_raw[1];
	                                        $poly_data_raw_array[] = $poly_data_raw;
	                                    }
	                                    $datasetoptions[$wpgmza_cmd][$dataset_id]->polydata = $poly_data_raw_array;

                                    if (isset($dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_gradient'])) { $datasetoptions[$wpgmza_cmd][$dataset_id]->gradient = stripslashes(html_entity_decode($dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_gradient'])); } else { $datasetoptions[$wpgmza_cmd][$dataset_id]->gradient = ""; }
                                   	if (isset($dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_radius'])) { $datasetoptions[$wpgmza_cmd][$dataset_id]->radius = $dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_radius']; } else { $datasetoptions[$wpgmza_cmd][$dataset_id]->radius = 20; }
                                    if (isset($dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_opacity'])) { $datasetoptions[$wpgmza_cmd][$dataset_id]->opacity = $dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_opacity']; } else { $datasetoptions[$wpgmza_cmd][$dataset_id]->opacity = 0.6; }
	                                }
	                            }
                        	}
                         }
                        } else {
                             $total_poly_array = wpgmza_b_return_dataset_id_array($wpgmza_cmd);

                            if ($total_poly_array > 0) {
                                foreach ($total_poly_array as $dataset_id) {
                                    $datasetoptions[$wpgmza_cmd][$dataset_id] = wpgmza_b_return_dataset_options($dataset_id);
                                    $dataset_second_options[$wpgmza_cmd][$dataset_id] = maybe_unserialize($datasetoptions[$wpgmza_cmd][$dataset_id]->options);

                                    $tmp_poly_array = wpgmza_b_return_dataset_array($dataset_id);
                                    $poly_data_raw_array = array();
                                    foreach ($tmp_poly_array as $single_poly) {
                                        $poly_data_raw = str_replace(" ","",$single_poly);
                                        $poly_data_raw = explode(",",$poly_data_raw);
                                        $lat = $poly_data_raw[0];
                                        $lng = $poly_data_raw[1];
                                        $poly_data_raw_array[] = $poly_data_raw;
                                    }
                                    $datasetoptions[$wpgmza_cmd][$dataset_id]->polydata = $poly_data_raw_array;

                                    if (isset($dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_gradient'])) { $datasetoptions[$wpgmza_cmd][$dataset_id]->gradient = stripslashes(html_entity_decode($dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_gradient'])); } else { $datasetoptions[$wpgmza_cmd][$dataset_id]->gradient = ""; }
                                   	if (isset($dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_radius'])) { $datasetoptions[$wpgmza_cmd][$dataset_id]->radius = $dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_radius']; } else { $datasetoptions[$wpgmza_cmd][$dataset_id]->radius = 20; }
                                    if (isset($dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_opacity'])) { $datasetoptions[$wpgmza_cmd][$dataset_id]->opacity = $dataset_second_options[$wpgmza_cmd][$dataset_id]['heatmap_opacity']; } else { $datasetoptions[$wpgmza_cmd][$dataset_id]->opacity = 0.6; }
                                }
                            }  else { $datasetoptions = array(); }     
                        }
                }


	            
                foreach ($wpgmza_short_code_array as $wpgmza_cmd) {
                    if ($wpgmza_current_mashup) {
                         foreach ($wpgmza_mashup_ids as $wpgmza_tmp_plg_array) {
                         	foreach ($wpgmza_tmp_plg_array as $wpgmza_tmp_plg) {
	                            $total_poly_array = wpgmza_b_return_polygon_id_array($wpgmza_tmp_plg);
	                            if ($total_poly_array > 0) {
	                                foreach ($total_poly_array as $poly_id) {
	                                    $polygonoptions[$wpgmza_cmd][$poly_id] = wpgmza_b_return_poly_options($poly_id);

	                                    $tmp_poly_array = wpgmza_b_return_polygon_array($poly_id);
	                                    $poly_data_raw_array = array();
	                                    foreach ($tmp_poly_array as $single_poly) {
	                                        $poly_data_raw = str_replace(" ","",$single_poly);
	                                        $poly_data_raw = explode(",",$poly_data_raw);
	                                        if (isset($poly_data_raw[0]) && isset($poly_data_raw[1])) {
		                                        $lat = $poly_data_raw[0];
		                                        $lng = $poly_data_raw[1];
		                                        $poly_data_raw_array[] = $poly_data_raw;
		                                    }
	                                    }
	                                    $polygonoptions[$wpgmza_cmd][$poly_id]->polydata = $poly_data_raw_array;

	                                    $linecolor = $polygonoptions[$wpgmza_cmd][$poly_id]->linecolor;
	                                    $fillcolor = $polygonoptions[$wpgmza_cmd][$poly_id]->fillcolor;
	                                    $fillopacity = $polygonoptions[$wpgmza_cmd][$poly_id]->opacity;
	                                    if (!$linecolor) { $polygonoptions[$wpgmza_cmd][$poly_id]->linecolor = "000000"; }
	                                    if (!$fillcolor) { $polygonoptions[$wpgmza_cmd][$poly_id]->fillcolor = "66FF00"; }
	                                    if (!$fillopacity) { $polygonoptions[$wpgmza_cmd][$poly_id]->opacity = "0.5"; }
	                                }
	                            }
                        	}
                         }
                        } else {
                             $total_poly_array = wpgmza_b_return_polygon_id_array($wpgmza_cmd);

                            if ($total_poly_array > 0) {
                                foreach ($total_poly_array as $poly_id) {
                                    $polygonoptions[$wpgmza_cmd][$poly_id] = wpgmza_b_return_poly_options($poly_id);

                                    $tmp_poly_array = wpgmza_b_return_polygon_array($poly_id);
                                    $poly_data_raw_array = array();
                                    foreach ($tmp_poly_array as $single_poly) {
                                        $poly_data_raw = str_replace(" ","",$single_poly);
                                        $poly_data_raw = explode(",",$poly_data_raw);
                                        if (isset($poly_data_raw[0]) && isset($poly_data_raw[1])) {
	                                        $lat = $poly_data_raw[0];
	                                        $lng = $poly_data_raw[1];
	                                        $poly_data_raw_array[] = $poly_data_raw;
	                                    }
                                    }
                                    $polygonoptions[$wpgmza_cmd][$poly_id]->polydata = $poly_data_raw_array;

                                    $linecolor = $polygonoptions[$wpgmza_cmd][$poly_id]->linecolor;
                                    $fillcolor = $polygonoptions[$wpgmza_cmd][$poly_id]->fillcolor;
                                    $fillopacity = $polygonoptions[$wpgmza_cmd][$poly_id]->opacity;
                                    if (!$linecolor) { $polygonoptions[$wpgmza_cmd][$poly_id]->linecolor = "000000"; }
                                    if (!$fillcolor) { $polygonoptions[$wpgmza_cmd][$poly_id]->fillcolor = "66FF00"; }
                                    if (!$fillopacity) { $polygonoptions[$wpgmza_cmd][$poly_id]->opacity = "0.5"; }
                                }
                            }  else { $polygonoptions = array(); }     
                        }
                }


                
                foreach ($wpgmza_short_code_array as $wpgmza_cmd) {
                    if ($wpgmza_current_mashup) {
                         foreach ($wpgmza_mashup_ids as $wpgmza_tmp_plg_array) {
                         	foreach ($wpgmza_tmp_plg_array as $wpgmza_tmp_plg) {

		                        $total_poly_array = wpgmza_b_return_polyline_id_array($wpgmza_tmp_plg);
		                        if ($total_poly_array > 0) {
		                            foreach ($total_poly_array as $poly_id) {
		                                $polylineoptions[$wpgmza_cmd][$poly_id] = wpgmza_b_return_polyline_options($poly_id);

		                                $tmp_poly_array = wpgmza_b_return_polyline_array($poly_id);
		                                $poly_data_raw_array = array();
		                                foreach ($tmp_poly_array as $single_poly) {
		                                    $poly_data_raw = str_replace(" ","",$single_poly);
		                                    $poly_data_raw = str_replace(")","",$poly_data_raw );
		                                    $poly_data_raw = str_replace("(","",$poly_data_raw );
		                                    $poly_data_raw = explode(",",$poly_data_raw);
	                                        if (isset($poly_data_raw[0]) && isset($poly_data_raw[1])) {
	                                       	    $lat = $poly_data_raw[0];
			                                    $lng = $poly_data_raw[1];
			                                    $poly_data_raw_array[] = $poly_data_raw;
			                                }
		                                }
		                                $polylineoptions[$wpgmza_cmd][$poly_id]->polydata = $poly_data_raw_array;


		                                if (isset($polylineoptions[$wpgmza_cmd][$poly_id]->linecolor)) { $linecolor = $polylineoptions[$wpgmza_cmd][$poly_id]->linecolor; } else { $linecolor = false; } 
		                                if (isset($polylineoptions[$wpgmza_cmd][$poly_id]->fillcolor)) { $fillcolor = $polylineoptions[$wpgmza_cmd][$poly_id]->fillcolor; } else { $fillcolor = false; } 
		                                if (isset($polylineoptions[$wpgmza_cmd][$poly_id]->opacity)) { $fillopacity = $polylineoptions[$wpgmza_cmd][$poly_id]->opacity; } else { $fillopacity = false; } 
		                                if (!$linecolor) { $polylineoptions[$wpgmza_cmd][$poly_id]->linecolor = "000000"; }
		                                if (!$fillcolor) { $polylineoptions[$wpgmza_cmd][$poly_id]->fillcolor = "66FF00"; }
		                                if (!$fillopacity) { $polylineoptions[$wpgmza_cmd][$poly_id]->opacity = "0.5"; }
		                            }
		                        } 
		                    }
                         }
                        } else {
                            $total_poly_array = wpgmza_b_return_polyline_id_array($wpgmza_cmd);
                            if ($total_poly_array > 0) {
                                foreach ($total_poly_array as $poly_id) {
                                    $polylineoptions[$wpgmza_cmd][$poly_id] = wpgmza_b_return_polyline_options($poly_id);

                                    $tmp_poly_array = wpgmza_b_return_polyline_array($poly_id);
									
                                    $poly_data_raw_array = array();
                                    foreach ($tmp_poly_array as $single_poly) {
                                        $poly_data_raw = str_replace(" ","",$single_poly);
                                        $poly_data_raw = str_replace(")","",$poly_data_raw );
                                        $poly_data_raw = str_replace("(","",$poly_data_raw );
                                        $poly_data_raw = explode(",",$poly_data_raw);
                                        if (isset($poly_data_raw[0]) && isset($poly_data_raw[1])) {
	                                        $lat = $poly_data_raw[0];
	                                        $lng = $poly_data_raw[1];
	                                        $poly_data_raw_array[] = $poly_data_raw;
	                                    }
                                    }
                                    $polylineoptions[$wpgmza_cmd][$poly_id]->polydata = $poly_data_raw_array;

 
                                    if (isset($polylineoptions[$wpgmza_cmd][$poly_id]->linecolor)) { $linecolor = $polylineoptions[$wpgmza_cmd][$poly_id]->linecolor; } else { $linecolor = false; }
                                    if (isset($polylineoptions[$wpgmza_cmd][$poly_id]->fillcolor)) { $fillcolor = $polylineoptions[$wpgmza_cmd][$poly_id]->fillcolor; } else { $fillcolor = false; }
                                    if (isset($polylineoptions[$wpgmza_cmd][$poly_id]->opacity)) { $fillopacity = $polylineoptions[$wpgmza_cmd][$poly_id]->opacity; } else { $fillopacity = false; }
                                    if (!$linecolor) { $polylineoptions[$wpgmza_cmd][$poly_id]->linecolor = "000000"; }
                                    if (!$fillcolor) { $polylineoptions[$wpgmza_cmd][$poly_id]->fillcolor = "66FF00"; }
                                    if (!$fillopacity) { $polylineoptions[$wpgmza_cmd][$poly_id]->opacity = "0.5"; }
                                }
                            } else { $polylineoptions = array(); }       
                        }
                }
            }
            
			
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_localize_polygon_settings', apply_filters('wpgmza_legacy_localize_polygon_data', $polygonoptions) );
			
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_localize_polyline_settings', $polylineoptions);
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_localize_heatmap_settings', $datasetoptions);
			
			wp_localize_script( 'wpgmaps_core', 'wpgmza_circle_data_array', $circle_data_array);
			wp_localize_script( 'wpgmaps_core', 'wpgmza_rectangle_data_array', $rectangle_data_array);

            if (isset($wpgmza_settings['wpgmza_force_greedy_gestures']) && $wpgmza_settings['wpgmza_force_greedy_gestures'] == "yes") {
			    wp_localize_script( 'wpgmaps_core', 'wpgmza_force_greedy_gestures', "greedy");
			}

            if (isset($wpgmza_settings['wpgmza_api_version'])) { $api_version = $wpgmza_settings['wpgmza_api_version']; } else { $api_version = ""; }
            if (isset($api_version) && $api_version != "") {
                $api_version_string = "v=$api_version&";
            } else {
                $api_version_string = "v=3.exp&";
            }
            
             if (isset($wpgmza_settings['wpgmza_settings_marker_pull'])) { $marker_pull = $wpgmza_settings['wpgmza_settings_marker_pull']; } else { $marker_pull = "1"; }



        	/* moved the old call of Google Maps API from here */



            global $wpgmza_version;
            if (floatval($wpgmza_version) < 6 || $wpgmza_version == "6.0.4" || $wpgmza_version == "6.0.3" || $wpgmza_version == "6.0.2" || $wpgmza_version == "6.0.1" || $wpgmza_version == "6.0.0") {
                if (is_multisite()) { 
                    global $blog_id;
                    $wurl = wpgmaps_get_plugin_url()."/".$blog_id."-";
                }
                else {
                    $wurl = wpgmaps_get_plugin_url()."/";
                }
            } else {
                /* later versions store marker files in wp-content/uploads/wp-google-maps director */
              
                
                
                
                if (function_exists("wpgmza_return_marker_url")) {
                    if (get_option("wpgmza_xml_url") == "") {
                        add_option("wpgmza_xml_url",'{uploads_dir}/wp-google-maps/');
                    }
                    $xml_marker_url = wpgmza_return_marker_url();
                } else {
                    if (get_option("wpgmza_xml_url") == "") {
                        $upload_dir = wp_upload_dir();
                        add_option("wpgmza_xml_url",$upload_dir['baseurl'].'/wp-google-maps/');
                    }
                    $xml_marker_url = get_option("wpgmza_xml_url");
                }

                if (is_multisite()) { 
                    global $blog_id;
                    $wurl = $xml_marker_url.$blog_id."-";

            		$wurl = preg_replace('#^http?:#', '', $wurl);
           			$wurl = preg_replace('#^https?:#', '', $wurl);

                }
                else {
                    $wurl = $xml_marker_url;

            		$wurl = preg_replace('#^http?:#', '', $wurl);
           			$wurl = preg_replace('#^https?:#', '', $wurl);
           			
                }
            }
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_markerurl', $wurl);

            
            
            if (isset($wpgmza_settings['wpgmza_settings_infowindow_link_text'])) { $wpgmza_settings_infowindow_link_text = $wpgmza_settings['wpgmza_settings_infowindow_link_text']; } else { $wpgmza_settings_infowindow_link_text = false; }
            if (!$wpgmza_settings_infowindow_link_text) { $wpgmza_settings_infowindow_link_text = __("More details","wp-google-maps"); }
            
			
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_more_details', $wpgmza_settings_infowindow_link_text);
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_get_dir', apply_filters( "wpgmza_filter_change_get_directions_string", __( "Get directions", "wp-google-maps" ) ) );
            
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_km_away', apply_filters( "wpgmza_filter_change_km_away_string", __( "km away", "wp-google-maps" ) ) );
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_m_away', apply_filters( "wpgmza_filter_change_miles_away_string", __( "miles away", "wp-google-maps" ) ) );
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_directions', apply_filters( "wpgmza_filter_change_directions_string", __( "Directions", "wp-google-maps" ) ) );
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_more_info', $wpgmza_settings_infowindow_link_text );
            //wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_error1', __("Please fill out both the \"from\" and \"to\" fields","wp-google-maps") );
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_getting_location', __('Getting your current location address...','wp-google-maps') );

            wp_localize_script( 'wpgmaps_core', 'ajaxurl', admin_url( 'admin-ajax.php' ) );

        	if (function_exists("wpgmaps_ugm_activate")) {
	            /* VGM variables */
	            wp_localize_script( 'wpgmaps_core', 'vgm_human_error_string', __("Please prove that you are human by checking the checkbox above","wp-google-maps") );
	            $ajax_nonce_ugm = wp_create_nonce("wpgmza_ugm");
	            wp_localize_script( 'wpgmaps_core', 'wpgmaps_nonce', $ajax_nonce_ugm );
	        }

        	$ajax_nonce_pro = wp_create_nonce("wpgmza_pro_ugm");
			wp_localize_script( 'wpgmaps_core', 'wpgmaps_pro_nonce', $ajax_nonce_pro );
			wp_localize_script( 'wpgmaps_core', 'wpgmaps_plugurl', wpgmaps_get_plugin_url() );
			wp_localize_script( 'wpgmaps_core', 'marker_pull', $marker_pull );
            if (function_exists("wpgmaps_gold_activate")) { 
            	wp_localize_script( 'wpgmaps_core', 'wpgm_g_e', '1' );
            } else {
            	wp_localize_script( 'wpgmaps_core', 'wpgm_g_e', '0' );
            }


        }
    }

}


function wpgmza_return_marker_count($map_id) {
    global $wpdb;
    global $wpgmza_tblname;
    
	$map_id = (int)$map_id;
	
    $wpgmza_sql1 = "
        SELECT COUNT(`id`) as `total_markers`
        FROM $wpgmza_tblname
        WHERE `map_id` = '$map_id'
        ";

    $results = $wpdb->get_row($wpgmza_sql1);
    return intval($results->total_markers);
}

function wpgmaps_admin_javascript_pro() {
    global $wpdb;
    global $wpgmza_tblname_maps;
    $ajax_nonce = wp_create_nonce("wpgmza");

    if( isset( $_POST['wpgmza_save_google_api_key_list'] ) ){  
        if( $_POST['wpgmza_google_maps_api_key'] !== '' ){      
            update_option('wpgmza_google_maps_api_key', sanitize_text_field($_POST['wpgmza_google_maps_api_key']) );
            echo "<div class='updated'><p>";
            $settings_page = "<a href='".admin_url('/admin.php?page=wp-google-maps-menu-settings#tabs-4')."'>".__('settings', 'wp-google-maps')."</a>";
            echo sprintf( __('Your Google Maps API key has been successfully saved. This API key can be changed in the %s page', 'wp-google-maps'), $settings_page );
			echo "<script> window.location.href=window.location.href; return false </script>";
            echo "</p></div>";
        }
    }
	
    if (is_admin() && isset($_GET['page']) && isset($_GET['action']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit_marker") { wpgmaps_admin_edit_marker_javascript(); }
    else if (is_admin() && isset($_GET['page']) && isset($_GET['action']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "add_poly") { wpgmaps_b_admin_add_poly_javascript(sanitize_text_field($_GET['map_id'])); }
    else if (is_admin() && isset($_GET['page']) && isset($_GET['action']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit_poly") { wpgmaps_b_admin_edit_poly_javascript(sanitize_text_field($_GET['map_id']),sanitize_text_field($_GET['poly_id'])); }
    else if (is_admin() && isset($_GET['page']) && isset($_GET['action']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "add_polyline") { wpgmaps_b_admin_add_polyline_javascript(sanitize_text_field($_GET['map_id'])); }
    else if (is_admin() && isset($_GET['page']) && isset($_GET['action']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit_polyline") { wpgmaps_b_admin_edit_polyline_javascript(sanitize_text_field($_GET['map_id']),sanitize_text_field($_GET['poly_id'])); }
    else if (is_admin() && isset($_GET['page']) && isset($_GET['action']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "add_heatmap") { wpgmaps_b_admin_add_heatmap_javascript(sanitize_text_field($_GET['map_id']),sanitize_text_field($_GET['map_id'])); }
    else if (is_admin() && isset($_GET['page']) && isset($_GET['action']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit_heatmap") { wpgmaps_b_admin_edit_heatmap_javascript(sanitize_text_field($_GET['map_id']),sanitize_text_field($_GET['id'])); }
    else if (is_admin() && isset($_GET['page']) && isset($_GET['action']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit") {
		
		if(!empty($_POST))
			wpgmaps_update_xml_file($_GET['map_id']);
			
        $res = wpgmza_get_map_data($_GET['map_id']);
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
		
        $wpgmza_lat = $res->map_start_lat;
        $wpgmza_lng = $res->map_start_lng;
        $wpgmza_width = $res->map_width;
        $wpgmza_height = $res->map_height;
        $wpgmza_width_type = stripslashes($res->map_width_type);
        $wpgmza_height_type = $res->map_height_type;
        $wpgmza_map_type = $res->type;
        $wpgmza_default_icon = $res->default_marker;
        $kml = $res->kml;
        $fusion = $res->fusion;
        $wpgmza_traffic = $res->traffic;
        $wpgmza_bicycle = $res->bicycle;
        
        $map_other_settings = maybe_unserialize($res->other_settings);
        if (isset($map_other_settings['weather_layer'])) { $weather_layer = $map_other_settings['weather_layer']; } else { $weather_layer = ""; }
        if (isset($map_other_settings['weather_layer_temp_type'])) { $weather_layer_temp_type = $map_other_settings['weather_layer_temp_type']; } else { $weather_layer_temp_type = 0; }
        if (isset($map_other_settings['cloud_layer'])) { $cloud_layer = $map_other_settings['cloud_layer']; } else { $cloud_layer = ""; }
        if (isset($map_other_settings['transport_layer'])) { $transport_layer = $map_other_settings['transport_layer']; } else { $transport_layer = ""; }
        if (isset($map_other_settings['map_max_zoom'])) { $wpgmza_max_zoom = intval($map_other_settings['map_max_zoom']); } else { $wpgmza_max_zoom = 0; }
        if (isset($map_other_settings['map_min_zoom'])) { $wpgmza_min_zoom = intval($map_other_settings['map_min_zoom']); } else { $wpgmza_min_zoom = 21; }
        if (isset($map_other_settings['override_users_location_zoom_levels'])) { $wpgmza_override_users_location_zoom_levels = intval($map_other_settings['override_users_location_zoom_levels']); } else { $wpgmza_override_users_location_zoom_levels = 21; }
        if (isset($map_other_settings['wpgmza_theme_data'])) { $wpgmza_theme_data = $map_other_settings['wpgmza_theme_data']; } else { $wpgmza_theme_data = false; }

        if (isset($wpgmza_settings['wpgmza_settings_map_open_marker_by'])) { $wpgmza_open_infowindow_by = $wpgmza_settings['wpgmza_settings_map_open_marker_by']; } else { $wpgmza_open_infowindow_by = false; }
        if ($wpgmza_open_infowindow_by == null || !isset($wpgmza_open_infowindow_by)) { $wpgmza_open_infowindow_by = '1'; }

        if ($wpgmza_default_icon == "0") { $wpgmza_default_icon = ""; }
        if (!$wpgmza_map_type || $wpgmza_map_type == "" || $wpgmza_map_type == "1") { $wpgmza_map_type = "ROADMAP"; }
        else if ($wpgmza_map_type == "2") { $wpgmza_map_type = "SATELLITE"; }
        else if ($wpgmza_map_type == "3") { $wpgmza_map_type = "HYBRID"; }
        else if ($wpgmza_map_type == "4") { $wpgmza_map_type = "TERRAIN"; }
        else { $wpgmza_map_type = "ROADMAP"; }

        $start_zoom = $res->map_start_zoom;
        if ($start_zoom < 1 || !$start_zoom) {
            $start_zoom = 5;
        }
        if (!$wpgmza_lat || !$wpgmza_lng) {
            $wpgmza_lat = "51.5081290";
            $wpgmza_lng = "-0.1280050";
        }
        
        
        // marker sorting functionality
        if ($res->order_markers_by == 1) { $order_by = 0; }
        else if ($res->order_markers_by == 2) { $order_by = 2; }
        else if ($res->order_markers_by == 3) { $order_by = 4; }
        else if ($res->order_markers_by == 4) { $order_by = 5; }
        else if ($res->order_markers_by == 5) { $order_by = 3; }
        else { $order_by = 0; }
        if ($res->order_markers_choice == 1) { $order_choice = "asc"; }
        else { $order_choice = "desc"; }
        if (isset($wpgmza_settings['wpgmza_api_version'])) { $api_version = $wpgmza_settings['wpgmza_api_version']; } 
        if (isset($api_version) && $api_version != "") {
            $api_version_string = "v=$api_version&";
        } else {
            $api_version_string = "v=3.exp&";
        }
        
        if (isset($wpgmza_settings['wpgmza_settings_marker_pull'])) { $marker_pull = $wpgmza_settings['wpgmza_settings_marker_pull']; } else { $marker_pull = "1"; }
        if (isset($marker_pull) && $marker_pull == "0") {
            if (!defined('PHP_VERSION_ID')) {
                $phpversion = explode('.', PHP_VERSION);
                define('PHP_VERSION_ID', ($phpversion[0] * 10000 + $phpversion[1] * 100 + $phpversion[2]));
            }
            /*if (PHP_VERSION_ID < 50300) {
                $markers = json_encode(wpgmaps_return_markers_pro($_GET['map_id']));
            } else {
                $markers = json_encode(wpgmaps_return_markers_pro($_GET['map_id']),JSON_HEX_APOS);    
            }*/
        }

	$wpgmza_locale = get_locale();
	$wpgmza_suffix = ".com";
	/* Hebrew correction */
	if ($wpgmza_locale == "he_IL") { $wpgmza_locale = "iw"; }

	/* Chinese integration */
	if ($wpgmza_locale == "zh_CN") { $wpgmza_suffix = ".cn"; } else { $wpgmza_suffix = ".com"; } 

	$wpgmza_locale = substr( $wpgmza_locale, 0, 2 );
	?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo wpgmaps_get_plugin_url(); ?>/css/data_table.css" />
    <!--<script type="text/javascript" src="<?php echo wpgmaps_get_plugin_url(); ?>/js/jquery.dataTables.min.js"></script>-->
	<?php
	
	////////////////////////////////////////////
		// PASS LEGACY VARIABLES ///////////////////
		////////////////////////////////////////////
			
		$db_marker_array = array();
		
		$WPGM_PathData = array();
		$WPGM_PathLineData = array();
		$heatmaps = array();
		
		$polygon_options_by_id = array();
		$polyline_options_by_id = array();
		$heatmap_options_by_id = array();
		
		// Markers
		if(!empty($markers))
			$db_marker_array = json_decode($markers);
		
		// Polgons
		$total_poly_array = wpgmza_b_return_polygon_id_array(sanitize_text_field($_GET['map_id']));
		
		foreach ($total_poly_array as $poly_id) {
			
			$polyoptions = wpgmza_b_return_poly_options($poly_id);
			
			$linecolor = $polyoptions->linecolor;
			$lineopacity = $polyoptions->lineopacity;
			$fillcolor = $polyoptions->fillcolor;
			$fillopacity = $polyoptions->opacity;
			
			if (!$linecolor) { $linecolor = "000000"; }
			if (!$fillcolor) { $fillcolor = "66FF00"; }
			if ($fillopacity == "") { $fillopacity = "0.5"; }
			if ($lineopacity == "") { $lineopacity = "1"; }
			
			$linecolor = "#".$linecolor;
			$fillcolor = "#".$fillcolor;
			
			$WPGM_PathData[$poly_id] = wpgmza_b_return_polygon_array($poly_id);
			
			$polygon_options_by_id[$poly_id] = array(
				'strokeColor'	=> $linecolor,
				'strokeOpacity'	=> $lineopacity,
				'fillOpacity'	=> $fillopacity,
				'fillColor'		=> $fillcolor,
				'strokeWeight'	=> 2
			);
		}

		// Polylines
		$total_polyline_array = wpgmza_b_return_polyline_id_array(sanitize_text_field($_GET['map_id']));
		
		foreach ($total_polyline_array as $poly_id) {
			
			$polyoptions = wpgmza_b_return_polyline_options($poly_id);
			
			$linecolor = $polyoptions->linecolor;
			$fillopacity = $polyoptions->opacity;
			$linethickness = $polyoptions->linethickness;
			
			if (!$linecolor) { $linecolor = "000000"; }
			if (!$linethickness) { $linethickness = "4"; }
			if (!$fillopacity) { $fillopacity = "0.5"; }
			
			$linecolor = "#".$linecolor;
			
			$poly_array = wpgmza_b_return_polyline_array($poly_id);
			
			$WPGM_PathLineData[$poly_id] = $poly_array;
			
			$polyline_options_by_id[$poly_id] = array(
				'strokeColor'	=> $linecolor,
				'strokeOpacity'	=> $fillopacity,
				'strokeWeight'	=> $linethickness
			);
			
		}
		
		// Heatmaps
		$total_dataset_array = wpgmza_b_return_dataset_id_array(sanitize_text_field($_GET['map_id']));
		
		foreach($total_dataset_array as $dataset_id) {
			
			$datasetoptions = wpgmza_b_return_dataset_options($dataset_id);
			$datasetoptions = maybe_unserialize($datasetoptions->options);
			
			$dataset_array = wpgmza_b_return_dataset_array($dataset_id);
			
			if (isset($datasetoptions['heatmap_opacity']))
				$opacity = floatval($datasetoptions['heatmap_opacity']);
			else
				$opacity = floatval(0.6);
			
			if (isset($datasetoptions['heatmap_gradient']))
				$gradient = stripslashes(html_entity_decode($datasetoptions['heatmap_gradient']));
			else
				$gradient = false;
			
			if (isset($datasetoptions['heatmap_radius']))
				$radius = intval($datasetoptions['heatmap_radius']);
			else
				$radius = intval(20);
			
			$heatmaps[$dataset_id] = $dataset_array;
			
			$heatmap_options_by_id[$dataset_id] = array(
				'opacity'		=> $opacity,
				'gradient'		=> $gradient,
				'radius'		=> $radius
			);
			
		}
		
		global $wpgmza;
		
		$dependencies = array();
		
		$scriptLoader = new WPGMZA\ScriptLoader($wpgmza->isProVersion());
		$scripts = $scriptLoader->getPluginScripts();
		
		foreach($scripts as $handle => $script)
			$dependencies[] = $handle;
		
		// TODO: Quick fix: This will need editing
		if(!empty($wpgmza_settings['wpgmza_maps_engine']) && $wpgmza_settings['wpgmza_maps_engine'] == 'open-layers')
		{
			$google_vertex_context_menu = array_search('wpgmza-google-vertex-context-menu', $dependencies);
			
			unset($dependencies[$google_vertex_context_menu]);
		}
		
		// TODO: Quick fix, this will need editing
		if($wpgmza->isInDeveloperMode())
			$dependencies = array('wpgmza');
		
		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-sortable');

		wp_enqueue_script('exif-js', plugin_dir_url(__FILE__) . 'lib/exif.js', array(), $wpgmza->getProVersion());
		wp_enqueue_script('wpgmza-legacy-map-edit-page', plugin_dir_url(__FILE__) .  'js/legacy-map-edit-page.js', $dependencies, $wpgmza->getProVersion());
		
		wp_localize_script('wpgmza-legacy-map-edit-page', 'wpgmza_legacy_map_edit_page_vars', array(
		
			'ajax_nonce'				=> $ajax_nonce,
			'map_id'					=> $_GET['map_id'],
			'marker_pull'				=> $marker_pull,
			'db_marker_array'			=> $db_marker_array,
			'order_by'					=> $order_by,
			'order_choice'				=> $order_choice,
			'wpgmza_lat'				=> $wpgmza_lat,
			'wpgmza_lng'				=> $wpgmza_lng,
			'start_zoom'				=> $start_zoom,
			'max_zoom'					=> (int)$wpgmza_max_zoom,
			'marker_url' 				=> wpgmaps_get_marker_url($_GET['map_id']),
			'wpgmza_height'				=> $wpgmza_height,
			'wpgmza_height_type'		=> $wpgmza_height_type,
			'wpgmza_width'				=> $wpgmza_width,
			'wpgmza_width_type'			=> $wpgmza_width_type,
			'geocode_unsuccessful'		=> __("Geocode was not successful for the following reason","wp-google-maps"),
			'map_type'					=> $wpgmza_map_type,
			'theme_data'				=> stripslashes($wpgmza_theme_data),
			'bicycle_layer'				=> $wpgmza_bicycle,
			'traffic_layer'				=> $wpgmza_traffic,
			'transport_layer'			=> $transport_layer,
			'kml_urls'					=> $kml,
			'fusion_table'				=> $fusion,
			'default_marker_icon'		=> $wpgmza_default_icon,
			'hide_infowindow_address'	=> isset($wpgmza_settings['wpgmza_settings_infowindow_address']),
			
			'infowindow_width'			=> isset($wpgmza_settings['wpgmza_settings_infowindow_width']) ? intval($wpgmza_settings['wpgmza_settings_infowindow_width']) : 0,
			'infowindow_link_text'		=> (empty($wpgmza_settings['wpgmza_settings_infowindow_link_text']) ? __("More details","wp-google-maps") : $wpgmza_settings['wpgmza_settings_infowindow_link_text']),
			'infowindow_resize_image'	=> (isset($wpgmza_settings['wpgmza_settings_image_resizing']) && $wpgmza_settings['wpgmza_settings_image_resizing'] == 'yes'),
			'infowindow_image_height'	=> (!empty($wpgmza_settings['wpgmza_settings_image_height']) ? $wpgmza_settings['wpgmza_settings_image_height'] : 'auto'),
			'infowindow_image_width'	=> (!empty($wpgmza_settings['wpgmza_settings_image_width']) ? $wpgmza_settings['wpgmza_settings_image_width'] : 'auto'),
            'infowindow_link_target'	=> (isset($wpgmza_settings['wpgmza_settings_infowindow_links']) && $wpgmza_settings['wpgmza_settings_infowindow_links'] == "yes" ? "target='_BLANK'" : ''),
			'infowindow_open_by'		=> $wpgmza_open_infowindow_by,
			
			'retina_width'				=> (!empty($wpgmza_settings['wpgmza_settings_retina_width']) ? $wpgmza_settings['wpgmza_settings_retina_width'] : 31),
			'retina_height'				=> (!empty($wpgmza_settings['wpgmza_settings_retina_height']) ? $wpgmza_settings['wpgmza_settings_retina_height'] : 45),
			
			'mapOptions' => array(
				'scrollwheel'				=> !isset($wpgmza_settings['wpgmza_settings_map_scroll']) 				|| $wpgmza_settings['wpgmza_settings_map_scroll']				!= "yes",
				'zoomControl'				=> !isset($wpgmza_settings['wpgmza_settings_map_zoom']) 				|| $wpgmza_settings['wpgmza_settings_map_zoom']					!= "yes",
				'panControl'				=> !isset($wpgmza_settings['wpgmza_settings_map_pan']) 					|| $wpgmza_settings['wpgmza_settings_map_pan']					!= "yes",
				'mapTypeControl'			=> !isset($wpgmza_settings['wpgmza_settings_map_type']) 				|| $wpgmza_settings['wpgmza_settings_map_type']					!= "yes",
				'streetViewControl'			=> !isset($wpgmza_settings['wpgmza_settings_map_streetview']) 			|| $wpgmza_settings['wpgmza_settings_map_streetview']			!= "yes",
				'fullscreenControl'			=> !isset($wpgmza_settings['wpgmza_settings_map_full_screen_control']) 	|| $wpgmza_settings['wpgmza_settings_map_full_screen_control']	!= "yes"
			),
			
			'WPGM_PathData'				=> $WPGM_PathData,
			'WPGM_PathLineData'			=> $WPGM_PathLineData,
			'heatmaps'					=> $heatmaps,
			'polygon_options_by_id'		=> $polygon_options_by_id,
			'polyline_options_by_id'	=> $polyline_options_by_id,
			'heatmap_options_by_id'		=> $heatmap_options_by_id
			
		));
		
	}

}


function wpgmaps_upload_csv() {
    if (!function_exists("wpgmaps_activate")) {
        //echo "<div id='message' class='updated' style='padding:10px; '><span style='font-weight:bold; color:red;'>".__("WP Google Maps","wp-google-maps").":</span> ".__("Please ensure you have <strong>both</strong> the <strong>Basic</strong> and <strong>Pro</strong> versions of WP Google Maps installed and activated at the same time in order for the plugin to function correctly.","wp-google-maps")."<br /></div>";
    }
    
    if (isset($_POST['wpgmza_uploadcsv_btn'])) {

		check_ajax_referer( 'wpgmza', 'real_post_nonce' );
	
		if(!current_user_can('administrator'))
		{
			http_response_code(401);
			exit;
		}

    	if( isset( $_FILES['wpgmza_csvfile'] ) ){

    		$import = new WPGMapsImportExport();
    		$import->import_markers();

        } else if ( isset( $_FILES['wpgmza_csv_map_import'] ) ){

        	$import = new WPGMapsImportExport();
    		$import->import_maps();

        }  else if ( isset( $_FILES['wpgmza_csv_polygons_import'] ) ){

        	$import = new WPGMapsImportExport();
    		$import->import_polygons();

        }  else if ( isset( $_FILES['wpgmza_csv_polylines_import'] ) ){

        	$import = new WPGMapsImportExport();
    		$import->import_polylines();

        } 
    }

}

function wpgmza_cURL_response_pro($action) {
    if (function_exists('curl_version')) {
        global $wpgmza_pro_version;
        global $wpgmza_pro_string;
        $request_url = "http://www.wpgmaps.com/api/rec.php?action=$action&dom=".$_SERVER['HTTP_HOST']."&ver=".$wpgmza_pro_version.$wpgmza_pro_string;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
    }

}

function wpgmza_pro_advanced_menu() {
	
	global $wpgmza;
    global $wpgmza_post_nonce;
	
	$real_post_nonce = wp_create_nonce('wpgmza');
	
    $wpgmza_csv_marker = "<a class='button button-primary' href=\"?page=wp-google-maps-menu-advanced&action=wpgmza_csv_export\" target=\"_BLANK\" title=\"".__("Download ALL marker data to a CSV file","wp-google-maps")."\">".__("Download ALL marker data to a CSV file","wp-google-maps")."</a>";
    $wpgmza_csv_map = "<a class='button button-primary' href=\"?page=wp-google-maps-menu-advanced&action=export_all_maps\" target=\"_BLANK\" title=\"".__("Download ALL map data to a CSV file","wp-google-maps")."\">".__("Download ALL map data to a CSV file","wp-google-maps")."</a>";
    $wpgmza_csv_polygon = "<a class='button button-primary' href=\"?page=wp-google-maps-menu-advanced&action=export_polygons\" target=\"_BLANK\" title=\"".__("Download ALL polygon data to a CSV file","wp-google-maps")."\">".__("Download ALL polygon data to a CSV file","wp-google-maps")."</a>";
    $wpgmza_csv_polyline = "<a class='button button-primary' href=\"?page=wp-google-maps-menu-advanced&action=export_polylines\" target=\"_BLANK\" title=\"".__("Download ALL polyline data to a CSV file","wp-google-maps")."\">".__("Download ALL polyline data to a CSV file","wp-google-maps")."</a>";

	?>
	<div class="wrap"><h1><?php esc_html_e( 'Advanced Options' , 'wp-google-maps' ); ?></h1>
		<script>
			jQuery(document).ready(function(){
			    jQuery('#wpgmza_geocode').on('change', function(){
			        if(jQuery(this).attr('checked')){
			            jQuery('#wpgmza_geocode_conditional').fadeIn();
			        }else{
			            jQuery('#wpgmza_geocode_conditional').fadeOut();
			        }
			    });
			});
		</script>
		<div id="wpgmaps_tabs">
			<ul>
				<?php
					/**
					 * Output advanced options tabs html.
					 *
					 * @since 7.0.0
					 */
					do_action( 'wpgmza_admin_advanced_options_tabs' );
				?>
				<li><a href="#tabs-1"><?php esc_html_e( 'Map Data', 'wp-google-maps' ); ?></a></li>
				<li><a href="#tabs-2"><?php esc_html_e( 'Marker Data', 'wp-google-maps' ); ?></a></li>
				<li><a href="#tabs-3"><?php esc_html_e( 'Polygon Data', 'wp-google-maps' ); ?></a></li>
				<li><a href="#tabs-4"><?php esc_html_e( 'Polyline Data', 'wp-google-maps' ); ?></a></li>
				<li><a href="#utilities"><?php esc_html_e( 'Utilities', 'wp-google-maps' ); ?></a></li>
			</ul>
			<?php
				/**
				 * Output advanced options html.
				 *
				 * @since 7.0.0
				 */
				do_action( 'wpgmza_admin_advanced_options' );
			?>
<?php echo "            <div id=\"tabs-1\">
            	<form enctype=\"multipart/form-data\" method=\"POST\">
				
					<input name='real_post_nonce' value='$real_post_nonce' type='hidden'/>
	                
	                <strong style='font-size:18px'>".__("Upload Map CSV File","wp-google-maps")."</strong><br /><br />
	                
	                <input name=\"wpgmza_csv_map_import\" id=\"wpgmza_csv_map_import\" type=\"file\" style='display:none'/>
	                
	                <label for='wpgmza_csv_map_import' class='wpgmza_file_select_btn'><i class='fa fa-download'></i> Select File</label><br />
	                
	                <input name=\"wpgmza_security\" type=\"hidden\" value=\"$wpgmza_post_nonce\" /><br /><br>
	                
	                <div class='switch'><input name=\"wpgmza_csvreplace_map\" id='wpgmza_csvreplace_map' class='cmn-toggle cmn-toggle-round-flat' type=\"checkbox\" value=\"Yes\" /> <label for='wpgmza_csvreplace_map'></label></div> ".__("Replace existing data with data in file","wp-google-maps")."<br />
	                

	                <br /><input class='wpgmza_general_btn' type=\"submit\" name=\"wpgmza_uploadcsv_btn\" value=\"".__("Upload File","wp-google-maps")."\" />
	                <div class='wpgmza-buttons__float-right'>$wpgmza_csv_map</div>
	            </form>
            </div>
            <div id=\"tabs-2\">
                <form enctype=\"multipart/form-data\" method=\"POST\">
					<input name='real_post_nonce' value='$real_post_nonce' type='hidden'/>
				
	                <strong style='font-size:18px'>".__("Upload Marker CSV File","wp-google-maps")."</strong><br /><br />
	                <input name=\"wpgmza_csvfile\" id=\"wpgmza_csvfile\" type=\"file\" style='display:none'/>
	                <label for='wpgmza_csvfile' class='wpgmza_file_select_btn'><i class='fa fa-download'></i> Select File</label><br />
	                <input name=\"wpgmza_security\" type=\"hidden\" value=\"$wpgmza_post_nonce\" /><br /><br>
	                <div class='switch'><input name=\"wpgmza_csvreplace\" id='wpgmza_csvreplace' class='cmn-toggle cmn-toggle-round-flat' type=\"checkbox\" value=\"Yes\" /> <label for='wpgmza_csvreplace'></label></div> ".__("Replace existing data with data in file","wp-google-maps")."<br />
	                <div class='switch'><input name=\"wpgmza_geocode\" id='wpgmza_geocode' class='cmn-toggle cmn-toggle-round-flat' type=\"checkbox\" value=\"Yes\" /> <label for='wpgmza_geocode'></label></div> (Beta) ".__("Automatically geocode addresses to GPS co-ordinates if none are supplied","wp-google-maps")." <br>
	                
	                <br><div style='display:none;' id='wpgmza_geocode_conditional'><strong>".__("Google API Key (Required)","wp-google-maps").": </strong><input name=\"wpgmza_api_key\" type=\"text\" value=\"".get_option("wpgmza_geocode_api_key")."\" /> 
	                (".__("You will need a Google Maps Geocode API key for this to work. See <a href='https://developers.google.com/maps/documentation/geocoding/#Limits'>Geocoding Documentation</a>","wp-google-maps")."). <br> ".__("There is a 0.12second delay between each request","wp-google-maps")."<br /></div>
						<input class='wpgmza_general_btn' type=\"submit\" name=\"wpgmza_uploadcsv_btn\" value=\"".__("Upload File","wp-google-maps")."\" />
	                <div class='wpgmza-buttons__float-right'>$wpgmza_csv_marker</div>
	            </form>
            </div>
            <div id=\"tabs-3\">
            	<form enctype=\"multipart/form-data\" method=\"POST\">
					<input name='real_post_nonce' value='$real_post_nonce' type='hidden'/>
	                
	                <strong style='font-size:18px'>".__("Upload Polygon CSV File","wp-google-maps")."</strong><br /><br />
	                
	                <input name=\"wpgmza_csv_polygons_import\" id=\"wpgmza_csv_polygons_import\" type=\"file\" style='display:none'/>
	                
	                <label for='wpgmza_csv_polygons_import' class='wpgmza_file_select_btn'><i class='fa fa-download'></i> Select File</label><br />
	                
	                <input name=\"wpgmza_security\" type=\"hidden\" value=\"$wpgmza_post_nonce\" /><br /><br>
	                
	                <div class='switch'><input name=\"wpgmza_csvreplace_polygon\" id='wpgmza_csvreplace_polygon' class='cmn-toggle cmn-toggle-round-flat' type=\"checkbox\" value=\"Yes\" /> <label for='wpgmza_csvreplace_polygon'></label></div> ".__("Replace existing data with data in file","wp-google-maps")."<br />
	                

	                <br /><input class='wpgmza_general_btn' type=\"submit\" name=\"wpgmza_uploadcsv_btn\" value=\"".__("Upload File","wp-google-maps")."\" />
	                <div class='wpgmza-buttons__float-right'>$wpgmza_csv_polygon</div>
	            </form>
            </div>
            <div id=\"tabs-4\">
            	<form enctype=\"multipart/form-data\" method=\"POST\">
					<input name='real_post_nonce' value='$real_post_nonce' type='hidden'/>
	                
	                <strong style='font-size:18px'>".__("Upload Polyline CSV File","wp-google-maps")."</strong><br /><br />
	                
	                <input name=\"wpgmza_csv_polylines_import\" id=\"wpgmza_csv_polylines_import\" type=\"file\" style='display:none'/>
	                
	                <label for='wpgmza_csv_polylines_import' class='wpgmza_file_select_btn'><i class='fa fa-download'></i> Select File</label><br />
	                
	                <input name=\"wpgmza_security\" type=\"hidden\" value=\"$wpgmza_post_nonce\" /><br /><br>
	                
	                <div class='switch'><input name=\"wpgmza_csvreplace_polyline\" id='wpgmza_csvreplace_polyline' class='cmn-toggle cmn-toggle-round-flat' type=\"checkbox\" value=\"Yes\" /> <label for='wpgmza_csvreplace_polyline'></label></div> ".__("Replace existing data with data in file","wp-google-maps")."<br />
	                

	                <br /><input class='wpgmza_general_btn' type=\"submit\" name=\"wpgmza_uploadcsv_btn\" value=\"".__("Upload File","wp-google-maps")."\" />
	                
	                <div class='wpgmza-buttons__float-right'>$wpgmza_csv_polyline</div>
	            </form>
            </div>
			
			<div id='utilities'>
				<h2>
					" . __('Utilities', 'wp-google-maps');
	
	if(version_compare($wpgmza->getBasicVersion(), '8.0.4', '>='))
	{
		echo "<p>
					
			<button id='wpgmza-remove-duplicates' type='button' class='button button-primary' title='" . __('Delete all markers with matching coordinates, address, title, link and description', 'wp-google-maps') . "'>
				" . __('Remove duplicate markers', 'wp-google-maps') . "
			</button>
		
		</p>";
	}
	else
	{
		echo "<p>" . __('Please update the core plugin to 8.0.4 or above to use utilities.', 'wp-google-maps') . "</p>";
	}

	echo "
				</h2>
			</div>
			
            <br /><br /><a href='http://www.wpgmaps.com/documentation/exporting-and-importing-your-markers/' target='_BLANK'>".__("Need help? Read the documentation.","wp-google-maps")."</a><br />
        </div>
    ";


}

function wpgmza_pro_support_menu() {
?>  
	<div class="wrap">
	    <h1><?php _e("WP Google Maps Support","wp-google-maps"); ?></h1>
	    <div id="wpgmza-support__row" class="wpgmza_row">
	        <div class='wpgmza_row_col wpgmza-support__col'>
	        	<div class="wpgmza-card">
	                <h2><i class="fa fa-book"></i> <?php _e("Documentation","wp-google-maps"); ?></h2>
	                <p><?php _e("Getting started? Read through some of these articles to help you along your way.","wp-google-maps"); ?></p>
	                <p><strong><?php _e("Documentation:","wp-google-maps"); ?></strong></p>
	                <ul>
	                    <li><a href='https://www.wpgmaps.com/documentation/creating-your-first-map/' target='_BLANK' title='<?php _e("Creating your first map","wp-google-maps"); ?>'><?php _e("Creating your first map","wp-google-maps"); ?></a></li>
	                    <li><a href='https://www.wpgmaps.com/documentation/using-your-map-in-a-widget/' target='_BLANK' title='<?php _e("Using your map as a Widget","wp-google-maps"); ?>'><?php _e("Using your map as a Widget","wp-google-maps"); ?></a></li>
	                    <li><a href='https://www.wpgmaps.com/documentation/changing-the-google-maps-language/' target='_BLANK' title='<?php _e("Changing the Google Maps language","wp-google-maps"); ?>'><?php _e("Changing the Google Maps language","wp-google-maps"); ?></a></li>
	                    <li><a href='https://www.wpgmaps.com/documentation/' target='_BLANK' title='<?php _e("WP Google Maps Documentation","wp-google-maps"); ?>'><?php _e("View all documentation.","wp-google-maps"); ?></a></li>
	                </ul>
	            </div>
	        </div>
	        <div class='wpgmza_row_col wpgmza-support__col'>
	        	<div class="wpgmza-card">
	                <h2><i class="fa fa-exclamation-circle"></i> <?php _e("Troubleshooting","wp-google-maps"); ?></h2>
	                <p><?php _e("WP Google Maps has a diverse and wide range of features which may, from time to time, run into conflicts with the thousands of themes and other plugins on the market.","wp-google-maps"); ?></p>
	                <p><strong><?php _e("Common issues:","wp-google-maps"); ?></strong></p>
	                <ul>
	                    <li><a href='https://www.wpgmaps.com/documentation/troubleshooting/my-map-is-not-showing-on-my-website/' target='_BLANK' title='<?php _e("My map is not showing on my website","wp-google-maps"); ?>'><?php _e("My map is not showing on my website","wp-google-maps"); ?></a></li>
	                    <li><a href='https://www.wpgmaps.com/documentation/troubleshooting/my-markers-are-not-showing-on-my-map/' target='_BLANK' title='<?php _e("My markers are not showing on my map in the front-end","wp-google-maps"); ?>'><?php _e("My markers are not showing on my map in the front-end","wp-google-maps"); ?></a></li>
	                    <li><a href='https://www.wpgmaps.com/documentation/troubleshooting/im-getting-jquery-errors-showing-on-my-website/' target='_BLANK' title='<?php _e("I'm getting jQuery errors showing on my website","wp-google-maps"); ?>'><?php _e("I'm getting jQuery errors showing on my website","wp-google-maps"); ?></a></li>
	                </ul>
	            </div>
	        </div>
	        <div class='wpgmza_row_col wpgmza-support__col'>
	        	<div class="wpgmza-card">
	                <h2><i class="fa fa-bullhorn "></i> <?php _e("Support","wp-google-maps"); ?></h2>
	                <p><?php _e("Still need help? Use one of these links below.","wp-google-maps"); ?></p>
	                <ul>
	                    <li><a href='https://www.wpgmaps.com/forums/' target='_BLANK' title='<?php _e("Support forum","wp-google-maps"); ?>'><?php _e("Support forum","wp-google-maps"); ?></a></li>
	                    <li><a href='https://www.wpgmaps.com/contact-us/' target='_BLANK' title='<?php _e("Contact us","wp-google-maps"); ?>'><?php _e("Contact us","wp-google-maps"); ?></a></li>
	                </ul>
	            </div>
	        </div>
	    </div>
	</div>
<?php
}



function wpgmaps_settings_page_pro() {


    echo"<div class=\"wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"></div><h1>".__("WP Google Map Settings","wp-google-maps")."</h1>";
    
    if (function_exists("wpgmza_register_pro_version")) {
        $pro_settings1 = wpgmaps_settings_page_sub('infowindow');
        $pro_settings2 = wpgmaps_settings_page_sub('mapsettings');
        $pro_settings3 = wpgmaps_settings_page_sub('ugm');
        $pro_settings4 = wpgmaps_settings_page_sub('advanced');
        $pro_settings5 = wpgmaps_settings_page_sub('mlisting');
		$pro_settings6 = wpgmaps_settings_page_sub('store-locator');
		
        global $wpgmza_version;
        if (floatval($wpgmza_version) < 5) {
            $prov_msg = "<div class='error below-h1'><p>Please update your BASIC version of this plugin for all of these settings to work.</p></div>";
        } else { $prov_msg = ''; }
    }
    if (function_exists('wpgmza_register_ugm_version')) {
        $pro_settings3 = wpgmaps_settings_page_sub('ugm');
    }

    echo "
        <form action='" . get_admin_url() . "admin-post.php' method='POST' id='wpgmaps_options'>
		
		<input name='action' value='wpgmza_settings_page_post_pro' type='hidden'/>
		" . wp_nonce_field('wpgmza_settings_page_post_pro', 'wpgmza_settings_page_post_pro_nonce') . "
		
        <p>$prov_msg</p>
            
            <div id=\"wpgmaps_tabs\">
                <ul>
                        <li><a href=\"#tabs-1\">".__("Maps","wp-google-maps")."</a></li>
                        <li><a href=\"#tabs-2\">".__("InfoWindows","wp-google-maps")."</a></li>
                        <li><a href=\"#tabs-3\">".__("Marker Listing","wp-google-maps")."</a></li>
                        <li><a href=\"#tabs-4\">".__("Advanced","wp-google-maps")."</a></li>
                        <li><a href=\"#tabs-5\">".__("Visitor Generated Markers","wp-google-maps")."</a></li>
						<li><a href=\"#tabs-6\">".__('Store Locator', 'wp-google-maps')."</a></li>
                        ".apply_filters("wpgmza_global_settings_tabs", "")."
                </ul>
                <div id=\"tabs-1\">
                    $pro_settings2
                </div>
                <div id=\"tabs-2\">
                    $pro_settings1
                </div>
                <div id=\"tabs-3\">
                    $pro_settings5
                </div>
                <div id=\"tabs-4\">
                    $pro_settings4
                </div>
                <div id=\"tabs-5\">
                    $pro_settings3
                </div>
				<div id=\"tabs-6\">
					$pro_settings6
				</div>
                ".apply_filters("wpgmza_global_settings_tab_content", "")."
            </div>
            
                

                
                
                

                <p class='submit'><input type='submit' name='wpgmza_save_settings' class='button-primary' value='".__("Save Settings","wp-google-maps")." &raquo;' /></p>


            </form>
            
            
    ";

    echo "</div>";






}
register_activation_hook( __FILE__, 'wpgmaps_pro_activate' );
register_deactivation_hook( __FILE__, 'wpgmaps_pro_deactivate' );


$wpgmaps_api_url = 'http://ccplugins.co/api-wpgmza-v8/';
$wpgmaps_plugin_slug = basename(dirname(__FILE__));

// Take over the update check
add_filter('pre_set_site_transient_update_plugins', 'wpgmaps_check_for_plugin_update');

function wpgmaps_check_for_plugin_update($checked_data) {
	global $wpgmaps_api_url, $wpgmaps_plugin_slug, $wp_version, $wpgmza_pro_version, $wpgmza;
	
	// Comment out these two lines during testing.
	if (empty($checked_data->checked))
		return $checked_data;
		
	$args = array(
		'name' => 'WP Google Maps Pro add-on',
		'slug' => $wpgmaps_plugin_slug,
		'version' => trim( $wpgmza_pro_version ),
	);
	
	$request_string = array(
		'body' => array(
			'action' => 'basic_check', 
			'request' => serialize($args),
			'api-key' => md5(get_bloginfo('url'))
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
	);
	
	// Start checking for an update
	$raw_response = wp_remote_post($wpgmaps_api_url, $request_string);
	
	if (isset($raw_response)) {
		if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
		{
			$response = unserialize($raw_response['body']);
			
			if($wpgmza && $wpgmza->isInDeveloperMode() && !$response)
				var_dump($raw_response['body']);
		}
		
		if (is_object($response) && !empty($response)) // Feed the update data into WP updater
			$checked_data->response[$wpgmaps_plugin_slug .'/'. $wpgmaps_plugin_slug .'.php'] = $response;
	}
	
	return $checked_data;
}



add_filter('plugins_api', 'wpgmaps_plugin_api_call', 10, 3);

function wpgmaps_plugin_api_call($def, $action, $args) {
	global $wpgmaps_plugin_slug, $wpgmaps_api_url, $wp_version;
	
	if (!isset($args->slug) || ($args->slug != $wpgmaps_plugin_slug))
		return false;
	
	// Get the current version
	$plugin_info = get_site_transient('update_plugins');
	$current_version = $plugin_info->checked[$wpgmaps_plugin_slug .'/'. $wpgmaps_plugin_slug .'.php'];
	$args->version = $current_version;
	
	$request_string = array(
			'body' => array(
				'action' => $action, 
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
	
	$request = wp_remote_post($wpgmaps_api_url, $request_string);
	
	if (is_wp_error($request)) {
		$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
	} else {
		$res = unserialize($request['body']);
		
		$res->name = 'WP Google Maps - Pro add-on';
		
		if ($res === false)
			$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
	}
	
	return $res;
}

function wpgmaps_settings_page_sub($section) {
	
	global $wpgmza;
	
    if ($section == "ugm") {
        if (function_exists('wpgmaps_ugm_settings_page')) { return wpgmaps_ugm_settings_page(); }
        else { 
            $ret = "<h3>".__("Visitor Generated Markers Settings","wp-google-maps")."</h3>";
            $ret .= "<a href='http://www.wpgmaps.com/visitor-generated-markers-add-on/?utm_source=plugin&utm_medium=link&utm_campaign=vgm_addon' target='_BLANK'>".__("Purchase the Visitor Generated Markers Add-on","wp-google-maps")."</a> ".__("to enable this feature. <br /><br />If you have already purchased it please ensure that you have uploaded activated the plugin.","wp-google-maps");
            return $ret;
        }
    }
    if ($section == "mlisting") {
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
        if (isset($wpgmza_settings['wpgmza_settings_markerlist_category'])) { $wpgmza_settings_markerlist_category = $wpgmza_settings['wpgmza_settings_markerlist_category']; } else { $wpgmza_settings_markerlist_category = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_markerlist_icon'])) { $wpgmza_settings_markerlist_icon = $wpgmza_settings['wpgmza_settings_markerlist_icon']; } else { $wpgmza_settings_markerlist_icon = ""; }
		if (isset($wpgmza_settings['wpgmza_settings_markerlist_link'])) { $wpgmza_settings_markerlist_link = $wpgmza_settings['wpgmza_settings_markerlist_link']; } else { $wpgmza_settings_markerlist_link = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_markerlist_title'])) { $wpgmza_settings_markerlist_title = $wpgmza_settings['wpgmza_settings_markerlist_title']; } else { $wpgmza_settings_markerlist_title = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_markerlist_description'])) { $wpgmza_settings_markerlist_description = $wpgmza_settings['wpgmza_settings_markerlist_description']; } else { $wpgmza_settings_markerlist_description = ""; }
         if (isset($wpgmza_settings['wpgmza_do_not_enqueue_datatables'])) { $wpgmza_do_not_enqueue_datatables = $wpgmza_settings['wpgmza_do_not_enqueue_datatables']; } else { $wpgmza_do_not_enqueue_datatables = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_markerlist_address'])) { $wpgmza_settings_markerlist_address = $wpgmza_settings['wpgmza_settings_markerlist_address']; } else { $wpgmza_settings_markerlist_address = ""; }
        if ($wpgmza_settings_markerlist_category == "yes") { $wpgmza_hide_category_checked = "checked='checked'"; } else { $wpgmza_hide_category_checked = ''; }
        if ($wpgmza_settings_markerlist_icon == "yes") { $wpgmza_hide_icon_checked = "checked='checked'"; } else { $wpgmza_hide_icon_checked = ''; }
		if ($wpgmza_settings_markerlist_link == "yes") { $wpgmza_hide_link_checked = "checked='checked'"; } else { $wpgmza_hide_link_checked = ''; }
        if ($wpgmza_settings_markerlist_title == "yes") { $wpgmza_hide_title_checked = "checked='checked'"; } else { $wpgmza_hide_title_checked = ''; }
        if ($wpgmza_settings_markerlist_address == "yes") { $wpgmza_hide_address_checked = "checked='checked'"; } else { $wpgmza_hide_address_checked = ''; }
        if ($wpgmza_settings_markerlist_description == "yes") { $wpgmza_hide_description_checked = "checked='checked'"; } else { $wpgmza_hide_description_checked = ''; }
        if ($wpgmza_do_not_enqueue_datatables == "yes") { $wpgmza_do_not_enqueue_datatables_checked = "checked='checked'"; } else { $wpgmza_do_not_enqueue_datatables_checked = ''; }

        if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_image'])) { $wpgmza_settings_carousel_markerlist_image = $wpgmza_settings['wpgmza_settings_carousel_markerlist_image']; } else { $wpgmza_settings_carousel_markerlist_image = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_icon'])) { $wpgmza_settings_carousel_markerlist_icon = $wpgmza_settings['wpgmza_settings_carousel_markerlist_icon']; } else { $wpgmza_settings_carousel_markerlist_icon = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_title'])) { $wpgmza_settings_carousel_markerlist_title = $wpgmza_settings['wpgmza_settings_carousel_markerlist_title']; } else { $wpgmza_settings_carousel_markerlist_title = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_address'])) { $wpgmza_settings_carousel_markerlist_address = $wpgmza_settings['wpgmza_settings_carousel_markerlist_address']; } else { $wpgmza_settings_carousel_markerlist_address = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_description'])) { $wpgmza_settings_carousel_markerlist_description = $wpgmza_settings['wpgmza_settings_carousel_markerlist_description']; } else { $wpgmza_settings_carousel_markerlist_description = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_directions'])) { $wpgmza_settings_carousel_markerlist_directions = $wpgmza_settings['wpgmza_settings_carousel_markerlist_directions']; } else { $wpgmza_settings_carousel_markerlist_directions = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_marker_link'])) { $wpgmza_settings_carousel_markerlist_marker_link = $wpgmza_settings['wpgmza_settings_carousel_markerlist_marker_link']; } else { $wpgmza_settings_carousel_markerlist_marker_link = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_resize_image'])) { $wpgmza_settings_carousel_markerlist_resize_image = $wpgmza_settings['wpgmza_settings_carousel_markerlist_resize_image']; } else { $wpgmza_settings_carousel_markerlist_resize_image = ""; }

        if (isset($wpgmza_settings['carousel_lazyload'])) { $wpgmza_settings_carousel_markerlist_lazyload = $wpgmza_settings['carousel_lazyload']; } else { $wpgmza_settings_carousel_markerlist_lazyload = ""; }
        if (isset($wpgmza_settings['carousel_autoplay'])) { $wpgmza_settings_carousel_markerlist_autoplay = $wpgmza_settings['carousel_autoplay']; } else { $wpgmza_settings_carousel_markerlist_autoplay = "5000"; }
        if (isset($wpgmza_settings['carousel_autoheight'])) { $wpgmza_settings_carousel_markerlist_autoheight = $wpgmza_settings['carousel_autoheight']; } else { $wpgmza_settings_carousel_markerlist_autoheight = ""; }
        if (isset($wpgmza_settings['carousel_pagination'])) { $wpgmza_settings_carousel_markerlist_pagination = $wpgmza_settings['carousel_pagination']; } else { $wpgmza_settings_carousel_markerlist_pagination = ""; }
        if (isset($wpgmza_settings['carousel_items'])) { $wpgmza_settings_carousel_markerlist_items = $wpgmza_settings['carousel_items']; } else { $wpgmza_settings_carousel_markerlist_items = "5"; }
        if (isset($wpgmza_settings['carousel_items_tablet'])) { $wpgmza_settings_carousel_markerlist_items_tablet = $wpgmza_settings['carousel_items_tablet']; } else { $wpgmza_settings_carousel_markerlist_items_tablet = "3"; }
        if (isset($wpgmza_settings['carousel_items_mobile'])) { $wpgmza_settings_carousel_markerlist_items_mobile = $wpgmza_settings['carousel_items_mobile']; } else { $wpgmza_settings_carousel_markerlist_items_mobile = "1"; }
        if (isset($wpgmza_settings['carousel_navigation'])) { $wpgmza_settings_carousel_markerlist_navigation = $wpgmza_settings['carousel_navigation']; } else { $wpgmza_settings_carousel_markerlist_navigation = ""; }

        if (isset($wpgmza_settings['wpgmza_default_items'])) { $wpgmza_settings_default_items = $wpgmza_settings['wpgmza_default_items']; } else { $wpgmza_settings_default_items = "10"; }

        if ($wpgmza_settings_carousel_markerlist_image == "yes") { $wpgmza_hide_carousel_image_checked = "checked='checked'"; } else { $wpgmza_hide_carousel_image_checked = ''; }
        if ($wpgmza_settings_carousel_markerlist_icon == "yes") { $wpgmza_hide_carousel_icon_checked = "checked='checked'"; } else { $wpgmza_hide_carousel_icon_checked = ''; }
        if ($wpgmza_settings_carousel_markerlist_title == "yes") { $wpgmza_hide_carousel_title_checked = "checked='checked'"; } else { $wpgmza_hide_carousel_title_checked = ''; }
        if ($wpgmza_settings_carousel_markerlist_address == "yes") { $wpgmza_hide_carousel_address_checked = "checked='checked'"; } else { $wpgmza_hide_carousel_address_checked = ''; }
        if ($wpgmza_settings_carousel_markerlist_description == "yes") { $wpgmza_hide_carousel_description_checked = "checked='checked'"; } else { $wpgmza_hide_carousel_description_checked = ''; }
        if ($wpgmza_settings_carousel_markerlist_directions == "yes") { $wpgmza_hide_carousel_directions_checked = "checked='checked'"; } else { $wpgmza_hide_carousel_directions_checked = ''; }
        if ($wpgmza_settings_carousel_markerlist_marker_link == "yes") { $wpgmza_hide_carousel_marker_link_checked = "checked='checked'"; } else { $wpgmza_hide_carousel_marker_link_checked = ''; }
        if ($wpgmza_settings_carousel_markerlist_resize_image == "yes") { $wpgmza_hide_carousel_resize_image_checked = "checked='checked'"; } else { $wpgmza_hide_carousel_resize_image_checked = ''; }

        
        if ($wpgmza_settings_carousel_markerlist_lazyload == "yes") { $wpgmza_settings_carousel_markerlist_lazyload_checked = "checked='checked'"; } else { $wpgmza_settings_carousel_markerlist_lazyload_checked = ''; }
        if ($wpgmza_settings_carousel_markerlist_autoheight == "yes") { $wpgmza_settings_carousel_markerlist_autoheight_checked = "checked='checked'"; } else { $wpgmza_settings_carousel_markerlist_autoheight_checked = ''; }
        if ($wpgmza_settings_carousel_markerlist_pagination == "yes") { $wpgmza_settings_carousel_markerlist_pagination_checked = "checked='checked'"; } else { $wpgmza_settings_carousel_markerlist_pagination_checked = ''; }
        if ($wpgmza_settings_carousel_markerlist_navigation == "yes") { $wpgmza_settings_carousel_markerlist_navigation_checked = "checked='checked'"; } else { $wpgmza_settings_carousel_markerlist_navigation_checked = ''; }
        
        if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'])) { $wpgmza_carousel_theme = $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']; }
        
        $wpgmza_carousel_theme_selected = array();
        for ($i=0;$i<=7;$i++) {
            $wpgmza_carousel_theme_selected[$i] = "";
        }
        
        for ($i=0;$i<=5;$i++) {
            $wpgmza_default_show_items_selected[$i] = "";
        }
        if ($wpgmza_settings_default_items == "10") { $wpgmza_default_show_items_selected[0] = "selected"; }
        else if ($wpgmza_settings_default_items == "25") { $wpgmza_default_show_items_selected[1] = "selected"; }
        else if ($wpgmza_settings_default_items == "50") { $wpgmza_default_show_items_selected[2] = "selected"; }
        else if ($wpgmza_settings_default_items == "100") { $wpgmza_default_show_items_selected[3] = "selected"; }
        else if ($wpgmza_settings_default_items == "-1") { $wpgmza_default_show_items_selected[4] = "selected"; }

        if (isset($wpgmza_carousel_theme) && $wpgmza_carousel_theme == "sky") { $wpgmza_carousel_theme_selected[0] = "selected"; }
        else if (isset($wpgmza_carousel_theme) && $wpgmza_carousel_theme == "sun") { $wpgmza_carousel_theme_selected[1] = "selected"; }
        else if (isset($wpgmza_carousel_theme) && $wpgmza_carousel_theme == "earth") { $wpgmza_carousel_theme_selected[2] = "selected"; }
        else if (isset($wpgmza_carousel_theme) && $wpgmza_carousel_theme == "monotone") { $wpgmza_carousel_theme_selected[3] = "selected"; }
        else if (isset($wpgmza_carousel_theme) && $wpgmza_carousel_theme == "pinkpurple") { $wpgmza_carousel_theme_selected[4] = "selected"; }
        else if (isset($wpgmza_carousel_theme) && $wpgmza_carousel_theme == "white") { $wpgmza_carousel_theme_selected[5] = "selected"; }
        else if (isset($wpgmza_carousel_theme) && $wpgmza_carousel_theme == "black") { $wpgmza_carousel_theme_selected[6] = "selected"; }
        else { $wpgmza_api_version_selected[0] = "selected"; }
        
            $ret = "<h3>".__("Marker Listing Settings","wp-google-maps")."</h3>";
            $ret .= "<p>".__("Changing these settings will alter the way the marker list appears on your website.","wp-google-maps")."</p>";
            $ret .= "<hr />";
            
            $ret .= "<h4>".__("Advanced Marker Listing","wp-google-maps")." & ".__("Basic Marker Listings","wp-google-maps")."</h4>";
            $ret .= "<table class='form-table'>";
            $ret .= "   <tr>";
            $ret .= "   <td width='200' valign='top' style='vertical-align:top;'>".__("Column settings","wp-google-maps")."</td>";
            $ret .= "   <td>";
            $ret .= "           <div class='switch'><input name='wpgmza_settings_markerlist_icon' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_markerlist_icon' value='yes' $wpgmza_hide_icon_checked /> <label for='wpgmza_settings_markerlist_icon'></label></div> ".__("Hide the Icon column","wp-google-maps")."<br />";
			$ret .= "           <div class='switch'><input name='wpgmza_settings_markerlist_link' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_markerlist_link' value='yes' $wpgmza_hide_link_checked /> <label for='wpgmza_settings_markerlist_link'></label></div> ".__("Hide the Link column","wp-google-maps")."<br />";
            $ret .= "           <div class='switch'><input name='wpgmza_settings_markerlist_title' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_markerlist_title' value='yes' $wpgmza_hide_title_checked /> <label for='wpgmza_settings_markerlist_title'></label></div> ".__("Hide the Title column","wp-google-maps")."<br />";
            $ret .= "           <div class='switch'><input name='wpgmza_settings_markerlist_address' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_markerlist_address' value='yes' $wpgmza_hide_address_checked /> <label for='wpgmza_settings_markerlist_address'></label></div> ".__("Hide the Address column","wp-google-maps")."<br />";
            $ret .= "           <div class='switch'><input name='wpgmza_settings_markerlist_category' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_markerlist_category' value='yes' $wpgmza_hide_category_checked /> <label for='wpgmza_settings_markerlist_category'></label></div> ".__("Hide the Category column","wp-google-maps")."<br />";
            $ret .= "           <div class='switch'><input name='wpgmza_settings_markerlist_description' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_markerlist_description' value='yes' $wpgmza_hide_description_checked /> <label for='wpgmza_settings_markerlist_description'></label></div> ".__("Hide the Description column","wp-google-maps")."<br />";
            $ret .= "           <div class='switch'><input name='wpgmza_do_not_enqueue_datatables' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_do_not_enqueue_datatables' value='yes' $wpgmza_do_not_enqueue_datatables_checked /> <label for='wpgmza_do_not_enqueue_datatables'></label></div> ".__("Do not Enqueue Datatables","wp-google-maps")."<br />";
            $ret .= "       </td>";
            $ret .= "   </tr>";
            $ret .= "   <tr>";
            $ret .= "   <td width='200' valign='top' style='vertical-align:top;'>".__("Show X items by default","wp-google-maps")."</td>";
            $ret .= "   <td>";
            $ret .= "           <select id='wpgmza_default_items' name='wpgmza_default_items'  >";
            $ret .= "               <option value=\"5\" ".$wpgmza_default_show_items_selected[5].">5</option>";
            $ret .= "               <option value=\"10\" ".$wpgmza_default_show_items_selected[0].">10</option>";
            $ret .= "               <option value=\"25\" ".$wpgmza_default_show_items_selected[1].">25</option>";
            $ret .= "               <option value=\"50\" ".$wpgmza_default_show_items_selected[2].">50</option>";
            $ret .= "               <option value=\"100\" ".$wpgmza_default_show_items_selected[3].">100</option>";
            $ret .= "               <option value=\"-1\" ".$wpgmza_default_show_items_selected[4].">ALL</option>";
            $ret .= "           </select>";
            $ret .= "       </td>";
            $ret .= "   </tr>";
            $ret .= "</table>";
            $ret .= "<hr/>";
             
            $ret .= "<h4>".__("Carousel Marker Listing","wp-google-maps")."</h4>";
            $ret .= "<table class='form-table'>";
            $ret .= "   <tr>";
            $ret .= "   <td width='200' valign='top' style='vertical-align:top;'>".__("Theme selection","wp-google-maps")."</td>";
            $ret .= "   <td>";
            $ret .= "   <select id='wpgmza_settings_carousel_markerlist_theme' name='wpgmza_settings_carousel_markerlist_theme'  >";
            $ret .= "   <option value=\"sky\" ".$wpgmza_carousel_theme_selected[0].">".__("Sky","wp-google-maps")."</option>";
            $ret .= "   <option value=\"sun\" ".$wpgmza_carousel_theme_selected[1].">".__("Sun","wp-google-maps")."</option>";
            $ret .= "   <option value=\"earth\" ".$wpgmza_carousel_theme_selected[2].">".__("Earth","wp-google-maps")."</option>";
            $ret .= "   <option value=\"monotone\" ".$wpgmza_carousel_theme_selected[3].">".__("Monotone","wp-google-maps")."</option>";
            $ret .= "   <option value=\"pinkpurple\" ".$wpgmza_carousel_theme_selected[4].">".__("PinkPurple","wp-google-maps")."</option>";
            $ret .= "   <option value=\"white\" ".$wpgmza_carousel_theme_selected[5].">".__("White","wp-google-maps")."</option>";
            $ret .= "   <option value=\"black\" ".$wpgmza_carousel_theme_selected[6].">".__("Black","wp-google-maps")."</option>";

            $ret .= "   </select>";
            $ret .= "    </td>";
            $ret .= "    </tr>";
            $ret .= "   <td width='200' valign='top' style='vertical-align:top;'>".__("Carousel settings","wp-google-maps")."</td>";
            $ret .= "   <td>";
            $ret .= "       <div class='switch'><input name='wpgmza_settings_carousel_markerlist_image' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_carousel_markerlist_image' value='yes' $wpgmza_hide_carousel_image_checked /><label for='wpgmza_settings_carousel_markerlist_image'></label></div> ".__("Hide the Image","wp-google-maps")."<br />";
            $ret .= "       <div class='switch'><input name='wpgmza_settings_carousel_markerlist_title' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_carousel_markerlist_title' value='yes' $wpgmza_hide_carousel_title_checked /><label for='wpgmza_settings_carousel_markerlist_title'></label></div> ".__("Hide the Title","wp-google-maps")."<br />";
            $ret .= "       <div class='switch'><input name='wpgmza_settings_carousel_markerlist_icon' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_carousel_markerlist_icon' value='yes' $wpgmza_hide_carousel_icon_checked /><label for='wpgmza_settings_carousel_markerlist_icon'></label></div> ".__("Hide the Marker Icon","wp-google-maps")."<br />";
            $ret .= "       <div class='switch'><input name='wpgmza_settings_carousel_markerlist_address' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_carousel_markerlist_address' value='yes' $wpgmza_hide_carousel_address_checked /><label for='wpgmza_settings_carousel_markerlist_address'></label></div> ".__("Hide the Address","wp-google-maps")."<br />";
            $ret .= "       <div class='switch'><input name='wpgmza_settings_carousel_markerlist_description' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_carousel_markerlist_description' value='yes' $wpgmza_hide_carousel_description_checked /><label for='wpgmza_settings_carousel_markerlist_description'></label></div> ".__("Hide the Description","wp-google-maps")."<br />";
            $ret .= "       <div class='switch'><input name='wpgmza_settings_carousel_markerlist_marker_link' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_carousel_markerlist_marker_link' value='yes' $wpgmza_hide_carousel_marker_link_checked /><label for='wpgmza_settings_carousel_markerlist_marker_link'></label></div> ".__("Hide the Marker Link","wp-google-maps")."<br />";
            $ret .= "       <div class='switch'><input name='wpgmza_settings_carousel_markerlist_directions' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_carousel_markerlist_directions' value='yes' $wpgmza_hide_carousel_directions_checked /><label for='wpgmza_settings_carousel_markerlist_directions'></label></div> ".__("Hide the Directions Link","wp-google-maps")."<br />";
            //$ret .= "       <br /><div class='switch'><input name='wpgmza_settings_carousel_markerlist_resize_image' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_carousel_markerlist_resize_image' value='yes' $wpgmza_hide_carousel_resize_image_checked /><label for='wpgmza_settings_carousel_markerlist_resize_image'></label></div> ".__("Resize Images with Timthumb","wp-google-maps")."<br />";
            $ret .= "       <br /><div class='switch'><input name='carousel_lazyload' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='carousel_lazyload' value='yes' $wpgmza_settings_carousel_markerlist_lazyload_checked /><label for='carousel_lazyload'></label></div> ".__("Enable lazyload of images","wp-google-maps")."<br />";
            $ret .= "       <div class='switch'><input name='carousel_autoheight' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='carousel_autoheight' value='yes' $wpgmza_settings_carousel_markerlist_autoheight_checked /><label for='carousel_autoheight'></label></div> ".__("Enable autoheight","wp-google-maps")."<br />";
            $ret .= "       <div class='switch'><input name='carousel_pagination' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='carousel_pagination' value='yes' $wpgmza_settings_carousel_markerlist_pagination_checked /> <label for='carousel_pagination'></label></div>".__("Enable pagination","wp-google-maps")."<br />";
            $ret .= "       <div class='switch'><input name='carousel_navigation' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='carousel_navigation' value='yes' $wpgmza_settings_carousel_markerlist_navigation_checked /><label for='carousel_navigation'></label></div> ".__("Enable navigation","wp-google-maps")."<br />";
            $ret .= "       <input name='carousel_items' type='text' id='carousel_items' value='$wpgmza_settings_carousel_markerlist_items' /> ".__("Items","wp-google-maps")."<br />";
            $ret .= "       <input name='carousel_items_tablet' type='text' id='carousel_items_tablet' value='$wpgmza_settings_carousel_markerlist_items_tablet' /> ".__("Items (Tablet)","wp-google-maps")."<br />";
            $ret .= "       <input name='carousel_items_mobile' type='text' id='carousel_items_mobile' value='$wpgmza_settings_carousel_markerlist_items_mobile' /> ".__("Items (Mobile)","wp-google-maps")."<br />";
            $ret .= "       <input name='carousel_autoplay' type='text' id='carousel_autoplay' value='$wpgmza_settings_carousel_markerlist_autoplay' /> ".__("Autoplay after x milliseconds (1000 = 1 second)","wp-google-maps")."<br />";
            $ret .= "    </td>";
            $ret .= "    </tr>";
            $ret .= "   </table>";
            return $ret;


        
    }
    if ($section == "advanced") {
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
        if (isset($wpgmza_settings['wpgmza_custom_css'])) { $wpgmza_custom_css = $wpgmza_settings['wpgmza_custom_css']; } else { $wpgmza_custom_css  = ""; }
        if (isset($wpgmza_settings['wpgmza_custom_js'])) { $wpgmza_custom_js = $wpgmza_settings['wpgmza_custom_js']; } else { $wpgmza_custom_js  = ""; }
        if (function_exists("wpgmza_return_marker_url")) {
            $marker_location = wpgmza_return_marker_path();
            $marker_url = wpgmza_return_marker_url();
            $wpgmza_use_url = __("You can use the following","wp-google-maps").": {wp_content_url},{plugins_url},{uploads_url}<br /><br />";
            $wpgmza_use_dir = __("You can use the following","wp-google-maps").": {wp_content_dir},{plugins_dir},{uploads_dir}<br /><br />";
        } else {
            $marker_location = get_option("wpgmza_xml_location");
            $marker_url = get_option("wpgmza_xml_url");
            $wpgmza_use_url = "";
            $wpgmza_use_dir = "";
        }
        
        $show_advanced_marker_tr = 'style="visibility:hidden; display:none;"';
        $wpgmza_settings_marker_pull_checked[0] = "";
        $wpgmza_settings_marker_pull_checked[1] = "";
        if (isset($wpgmza_settings['wpgmza_settings_marker_pull'])) { $wpgmza_settings_marker_pull = $wpgmza_settings['wpgmza_settings_marker_pull']; } else { $wpgmza_settings_marker_pull = false; }
        if ($wpgmza_settings_marker_pull == '0' || $wpgmza_settings_marker_pull == 0) { $wpgmza_settings_marker_pull_checked[0] = "checked='checked'"; $show_advanced_marker_tr = 'style="visibility:hidden; display:none;"'; }
        else if ($wpgmza_settings_marker_pull == '1' || $wpgmza_settings_marker_pull == 1) { $wpgmza_settings_marker_pull_checked[1] = "checked='checked'";  $show_advanced_marker_tr = 'style="visibility:visible; display:table-row;"'; }
        else { $wpgmza_settings_marker_pull_checked[0] = "checked='checked'"; $show_advanced_marker_tr = 'style="visibility:hidden; display:none;"'; }   

        
        
        
        $wpgmza_file_perms = @substr(sprintf('%o', fileperms($marker_location)), -4);
        $fpe = false;
        $fpe_error = "";
        if ($wpgmza_file_perms == "0777" || $wpgmza_file_perms == "0755" || $wpgmza_file_perms == "0775" || $wpgmza_file_perms == "0705" || $wpgmza_file_perms == "2777" || $wpgmza_file_perms == "2755" || $wpgmza_file_perms == "2775" || $wpgmza_file_perms == "2705") { 
            $fpe = true;
            $fpe_error = "";
        }
        else if ($wpgmza_file_perms == "0") {
            $fpe = false;
            $fpe_error = __("This folder does not exist. Please create it.","wp-google-maps");
        } else if (@is_writable($marker_location)) {
            $fpe = true;
            $fpe_error = "";
        } else { 
            $fpe = false;
            $fpe_error = __("File Permissions:","wp-google-maps").$wpgmza_file_perms." ".__(" - The plugin does not have write access to this folder. Please CHMOD this folder to 755 or 777, or change the location","wp-google-maps");
        }

        if (!$fpe) {
            $wpgmza_file_perms_check = "<span style='color:red;'>$fpe_error</span>";
        } else {
            $wpgmza_file_perms_check = "<span style='color:green;'>$fpe_error</span>";

        }
        
        $upload_dir = wp_upload_dir();
		
		$developer_mode_checked = (empty($wpgmza->settings->developer_mode) ? '' : 'checked="checked"');
		
        return "
        <h3>".__("Advanced Settings")."</h3>

        		<table class='form-table'>
					
					<tr data-required-maps-engine='google-maps'>

						<td valign='top' width='200' style='vertical-align:top;'>".__('Google Maps API Key', 'wp-google-maps')."</td>
						<td>
							<input type='text' id='wpgmza_google_maps_api_key' name='wpgmza_google_maps_api_key' value='".get_option('wpgmza_google_maps_api_key')."' style='width: 400px;' />
							<p>".__("This API key can be obtained from the <a href='https://console.developers.google.com' target='_BLANK'>Google Developers Console</a>. Our <a href='http://www.wpgmaps.com/documentation/creating-a-google-maps-api-key/' target='_BLANK'>documentation</a> provides a full guide on how to obtain this. ","wp-google-maps")."</p>
						</td>
					
					</tr>
					
					<tr data-required-maps-engine='open-layers'>

						<td valign='top' width='200' style='vertical-align:top;'>".__('OpenRouteService Key', 'wp-google-maps')."</td>
						<td>
							<input type='text' id='open_route_service_key' name='open_route_service_key' value='" . $wpgmza->settings->open_route_service_key . "' style='width: 400px;' />
							<p>".__("This API key can be obtained from the <a href='https://openrouteservice.org/dev/#/login' target='_BLANK'>OpenRouteService Developers Console</a>.", "wp-google-maps")."
						</td>
					
					</tr>
										

	                <p>".__("We suggest that you change the two fields below ONLY if you are experiencing issues when trying to save the marker XML files.","wp-google-maps")."</p>
    
                    <tr>
                        <td valign='top' width='200' style='vertical-align:top;'>".__("Pull marker data from","wp-google-maps")." </td>
                            <td>
                                     <input name='wpgmza_settings_marker_pull' type='radio' id='wpgmza_settings_marker_pull_db' class='wpgmza_settings_marker_pull' value='0' ".$wpgmza_settings_marker_pull_checked[0]." />".__("Database (Great for small amounts of markers)","wp-google-maps")." <br />
                                     <input name='wpgmza_settings_marker_pull' type='radio' id='wpgmza_settings_marker_pull_xml' class='wpgmza_settings_marker_pull' value='1' ".$wpgmza_settings_marker_pull_checked[1]." />".__("XML File  (Great for large amounts of markers)","wp-google-maps")." 
                                  </td>
                   </tr>
				   
					<tr>
						<td>
							" . __('Disable Compressed Path Variables', 'wp-google-maps') . "
						</td>
						<td>
							<input 
								name='disable_compressed_path_variables' 
								" . (!empty($wpgmza->settings->disable_compressed_path_variables) ? "checked='checked'" : '') . " 
								" . (version_compare($wpgmza->getBasicVersion(), '7.11.29', '<') ? "disabled='disabled'" : '') . "
								type='checkbox'
								/>
							<br/>
							" . __('We recommend using this setting if you frequently experience HTTP 414 - Request URI too long. We do not recommend using this setting if your site uses REST caching or a CDN.', 'wp-google-maps') . "
							" . (version_compare($wpgmza->getBasicVersion(), '7.11.29', '<') ? "<p class='notice notice-error'>" . __('Requires WP Google Maps 7.11.29 or above.', 'wp-google-maps') . "</p>" : '') . "
						</td>
					</tr>
					
					<tr>
						<td width='200' valign='top' style='vertical-align:top;'>".__("Disable Autoptimize Compatibility Fix","wp-google-maps").":</td>
						<td>
							<input 
							type='checkbox' 
							name='disable_autoptimize_compatibility_fix'
							" . (!empty($wpgmza->settings->disable_autoptimize_compatibility_fix) ? "checked='checked'" : "") . "
							/>
							<div>" . __("Use this setting if you are experiencing issues with Autoptimize's CSS aggregation. This may cause issues on setups with a large amount of marker data.", "wp-google-maps") . "</div>
						</td>
					</tr>
				   
                     <tr class='wpgmza_marker_dir_tr' $show_advanced_marker_tr>
                            <td width='200' valign='top' style='vertical-align:top;'>".__("Marker data XML directory","wp-google-maps").":</td>
                            <td>
                                <input id='wpgmza_marker_xml_location' name='wpgmza_marker_xml_location' value='".get_option("wpgmza_xml_location")."' class='regular-text code' /> $wpgmza_file_perms_check
                                <br />

                                <small>$wpgmza_use_dir
                                ".__("Currently using","wp-google-maps").": <strong><em>$marker_location</em></strong></small>
                        </td>
                    </tr>
                     <tr class='wpgmza_marker_url_tr' $show_advanced_marker_tr>
                            <td width='200' valign='top' style='vertical-align:top;'>".__("Marker data XML URL","wp-google-maps").":</td>
                         <td>
                            <input id='wpgmza_marker_xml_url' name='wpgmza_marker_xml_url' value='".get_option("wpgmza_xml_url")."' class='regular-text code' />
                                <br />
                                <br />
                                <small>$wpgmza_use_url
                                ".__("Currently using","wp-google-maps").": <strong><em>$marker_url</em></strong></small>
                        </td>
                    </tr>
                    </table>
                    <h4>".__("Custom Scripts","wp-google-maps")."</h4>
                               <table class='form-table'>
                                <tr>
                                       <td width='200' valign='top' style='vertical-align:top;'>".__("Custom CSS","wp-google-maps").":</td>
                                       <td>
                                           <textarea name=\"wpgmza_custom_css\" id=\"wpgmza_custom_css\" cols=\"70\" rows=\"10\" placeholder='".__("Custom CSS","wp-google-maps")."'>".stripslashes($wpgmza_custom_css)."</textarea>
                                   </td>
                               </tr>
                               <tr>
                                       <td width='200' valign='top' style='vertical-align:top;'>".__("Custom JS","wp-google-maps").":</td>
                                       <td>
                                           <textarea name=\"wpgmza_custom_js\" id=\"wpgmza_custom_js\" cols=\"70\" rows=\"10\" placeholder='".__("Custom JS","wp-google-maps")."'>".esc_textarea( stripslashes( $wpgmza_custom_js ) )."</textarea>
                                   </td>
                               </tr>
                               </table>
					<div id='wpgmza-developer-mode'>
					<h4>" . __('Developer Mode', 'wp-google-maps') . "</h4>
					<input type='checkbox' name='wpgmza_developer_mode' $developer_mode_checked/>
					" . __('Always rebuilds combined script files, does not load combined and minified scripts. Returns SQL queries on REST responses for some routes.', 'wp-google-maps') . "
					<span class='notice notice-warning'>
						&#9888;
						" . __('Enabling this setting causes poor performance and may cause issues which may necessitate re-installing WP Google Maps - Pro add-on. Please only enable this setting if you are a developer intending to debug or work on the plugin.', 'wp-google-maps') . "
					</span>
					</div>
							   
							   ";
        
        
    }

	$wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
	if (isset($wpgmza_settings['wpgmza_settings_access_level'])) { $access_level = $wpgmza_settings['wpgmza_settings_access_level']; } else { $access_level = "manage_options"; }
	
    if ($section == "mapsettings" && current_user_can($access_level)) {
        
		
        if (isset($wpgmza_settings['wpgmza_settings_map_full_screen_control'])) { $wpgmza_settings_map_full_screen_control = $wpgmza_settings['wpgmza_settings_map_full_screen_control']; } else { $wpgmza_settings_map_full_screen_control = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_map_streetview'])) { $wpgmza_settings_map_streetview = $wpgmza_settings['wpgmza_settings_map_streetview']; } else { $wpgmza_settings_map_streetview = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_map_zoom'])) { $wpgmza_settings_map_zoom = $wpgmza_settings['wpgmza_settings_map_zoom']; } else { $wpgmza_settings_map_zoom = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_map_pan'])) { $wpgmza_settings_map_pan = $wpgmza_settings['wpgmza_settings_map_pan']; } else { $wpgmza_settings_map_pan = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_map_type'])) { $wpgmza_settings_map_type = $wpgmza_settings['wpgmza_settings_map_type']; } else { $wpgmza_settings_map_type = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_map_scroll'])) { $wpgmza_settings_map_scroll = $wpgmza_settings['wpgmza_settings_map_scroll']; } else { $wpgmza_settings_map_scroll = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_map_draggable'])) { $wpgmza_settings_map_draggable = $wpgmza_settings['wpgmza_settings_map_draggable']; } else { $wpgmza_settings_map_draggable = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_map_clickzoom'])) { $wpgmza_settings_map_clickzoom = $wpgmza_settings['wpgmza_settings_map_clickzoom']; } else { $wpgmza_settings_map_clickzoom = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_cat_display_qty'])) { $wpgmza_settings_cat_display_qty = $wpgmza_settings['wpgmza_settings_cat_display_qty']; } else { $wpgmza_settings_cat_display_qty = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_infowindow_links'])) { $wpgmza_settings_infowindow_links = $wpgmza_settings['wpgmza_settings_infowindow_links']; } else { $wpgmza_settings_infowindow_links = ""; }
        if (isset($wpgmza_settings['wpgmza_settings_remove_api'])) { $wpgmza_remove_api = $wpgmza_settings['wpgmza_settings_remove_api']; }
        if (isset($wpgmza_settings['wpgmza_force_greedy_gestures'])) { $wpgmza_force_greedy_gestures = $wpgmza_settings['wpgmza_force_greedy_gestures']; }
        if (isset($wpgmza_settings['wpgmza_settings_filterbycat_type'])) { $wpgmza_settings_filterbycat_type = $wpgmza_settings['wpgmza_settings_filterbycat_type']; } else { $wpgmza_settings_filterbycat_type = ""; }
        
        if ($wpgmza_settings_map_full_screen_control == "yes") { $wpgmza_fullscreen_checked = "checked='checked'"; } else { $wpgmza_fullscreen_checked = ''; }
        if ($wpgmza_settings_map_streetview == "yes") { $wpgmza_streetview_checked = "checked='checked'"; } else { $wpgmza_streetview_checked = ''; }
        if ($wpgmza_settings_map_zoom == "yes") { $wpgmza_zoom_checked = "checked='checked'"; } else { $wpgmza_zoom_checked = ''; }
        if ($wpgmza_settings_map_pan == "yes") { $wpgmza_pan_checked = "checked='checked'"; } else { $wpgmza_pan_checked = ''; }
        if ($wpgmza_settings_map_type == "yes") { $wpgmza_type_checked = "checked='checked'"; } else { $wpgmza_type_checked = ''; }
        if ($wpgmza_settings_map_scroll == "yes") { $wpgmza_scroll_checked = "checked='checked'"; } else { $wpgmza_scroll_checked = ''; }
        if ($wpgmza_settings_map_draggable == "yes") { $wpgmza_draggable_checked = "checked='checked'"; } else { $wpgmza_draggable_checked = ''; }
        if ($wpgmza_settings_map_clickzoom == "yes") { $wpgmza_clickzoom_checked = "checked='checked'"; } else { $wpgmza_clickzoom_checked = ''; }


        if ($wpgmza_settings_cat_display_qty == "yes") { $wpgmza_cat_qty_checked = "checked='checked'"; } else { $wpgmza_cat_qty_checked = ''; }
        if ($wpgmza_settings_infowindow_links == "yes") { $wpgmza_linkschecked = "checked='checked'"; } else { $wpgmza_linkschecked = ''; }
        // if ($wpgmza_force_jquery == "yes") { $wpgmza_force_jquery_checked = "checked='checked'"; } else { $wpgmza_force_jquery_checked = ''; }
        	
        if (isset($wpgmza_remove_api)) { if ($wpgmza_remove_api == "yes") { $wpgmza_remove_api_checked = "checked='checked'"; } else { $wpgmza_remove_api_checked = ""; } } else { $wpgmza_remove_api_checked = ""; }
        
        if (isset($wpgmza_force_greedy_gestures)) { if ($wpgmza_force_greedy_gestures == "yes") { $wpgmza_force_greedy_gestures_checked = "checked='checked'"; } else { $wpgmza_force_greedy_gestures_checked = ""; } } else { $wpgmza_force_greedy_gestures_checked = ""; }

    	
        if (isset($wpgmza_settings['wpgmza_api_version'])) { $wpgmza_api_version = $wpgmza_settings['wpgmza_api_version']; }
        $wpgmza_api_version_selected = array();
        $wpgmza_api_version_selected[0] = "";
        $wpgmza_api_version_selected[1] = "";
        $wpgmza_api_version_selected[2] = "";
        if (isset($wpgmza_api_version) && $wpgmza_api_version == "3.25") { $wpgmza_api_version_selected[0] = "selected"; }
        else if (isset($wpgmza_api_version) && $wpgmza_api_version == "3.26") { $wpgmza_api_version_selected[1] = "selected"; }
        else if (isset($wpgmza_api_version) && $wpgmza_api_version == "3.exp") { $wpgmza_api_version_selected[2] = "selected"; }
        else { $wpgmza_api_version_selected[0] = "selected"; }



        
        $wpgmza_settings_map_open_marker_by_checked[0] = '';
        $wpgmza_settings_map_open_marker_by_checked[1] = '';
        if (isset($wpgmza_settings['wpgmza_settings_map_open_marker_by'])) { $wpgmza_settings_map_open_marker_by = $wpgmza_settings['wpgmza_settings_map_open_marker_by']; } else {$wpgmza_settings_map_open_marker_by = false; }
        if ($wpgmza_settings_map_open_marker_by == '1') { $wpgmza_settings_map_open_marker_by_checked[0] = "checked='checked'"; }
        else if ($wpgmza_settings_map_open_marker_by == '2') { $wpgmza_settings_map_open_marker_by_checked[1] = "checked='checked'"; }
        else { $wpgmza_settings_map_open_marker_by_checked[0] = "checked='checked'"; }



		$user_interface_style_checked[0] = '';
		$user_interface_style_checked[1] = '';
		$user_interface_style_checked[2] = '';
		$user_interface_style_checked[3] = '';
		$user_interface_style_checked[4] = '';
        $user_interface_style_checked[5] = '';
        if (isset($wpgmza_settings['user_interface_style'])) { $user_interface_style = $wpgmza_settings['user_interface_style']; } else {$user_interface_style = false; }
		if ($user_interface_style == 'bare-bones') { $user_interface_style_checked[0] = "checked='checked'";
			$wpgmza->settings->user_interface_style = 'bare-bones'; }
		else if ($user_interface_style == 'default') { $user_interface_style_checked[1] = "checked='checked'";
				$wpgmza->settings->user_interface_style = 'default'; }
		else if ($user_interface_style == 'legacy') { $user_interface_style_checked[2] = "checked='checked'"; 
			$wpgmza->settings->user_interface_style = 'legacy';}
	
		else if ($user_interface_style == 'compact') { $user_interface_style_checked[3] = "checked='checked'";
			$wpgmza->settings->user_interface_style = 'compact'; }
		else if ($user_interface_style == 'modern') { $user_interface_style_checked[4] = "checked='checked'";
			$wpgmza->settings->user_interface_style = 'modern'; }
		else if ($user_interface_style == 'minimal') { $user_interface_style_checked[5] = "checked='checked'";
			$wpgmza->settings->user_interface_style = 'minimal'; }
		else { $user_interface_style_checked[2] = "checked='checked'"; }



        if (isset($wpgmza_settings['wpgmza_settings_cat_logic'])) { $wpgmza_settings_cat_logic = $wpgmza_settings['wpgmza_settings_cat_logic']; } else {$wpgmza_settings_cat_logic = false; }
        if ($wpgmza_settings_cat_logic == '0') { $wpgmza_settings_cat_logic_checked[0] = "checked='checked'"; $wpgmza_settings_cat_logic_checked[1] = ''; }
        else if ($wpgmza_settings_cat_logic == '1') { $wpgmza_settings_cat_logic_checked[1] = "checked='checked'"; $wpgmza_settings_cat_logic_checked[0] = ''; }
        else { $wpgmza_settings_cat_logic_checked[0] = "checked='checked'"; $wpgmza_settings_cat_logic_checked[1] = ''; }




        $wpgmza_access_level_checked[0] = "";
        $wpgmza_access_level_checked[1] = "";
        $wpgmza_access_level_checked[2] = "";
        $wpgmza_access_level_checked[3] = "";
        $wpgmza_access_level_checked[4] = "";
        if (isset($wpgmza_settings['wpgmza_settings_access_level'])) { $wpgmza_access_level = $wpgmza_settings['wpgmza_settings_access_level']; } else { $wpgmza_access_level = ""; }
        if ($wpgmza_access_level == "manage_options") { $wpgmza_access_level_checked[0] = "selected"; }
        else if ($wpgmza_access_level == "edit_pages") { $wpgmza_access_level_checked[1] = "selected"; }
        else if ($wpgmza_access_level == "publish_posts") { $wpgmza_access_level_checked[2] = "selected"; }
        else if ($wpgmza_access_level == "edit_posts") { $wpgmza_access_level_checked[3] = "selected"; }
        else if ($wpgmza_access_level == "read") { $wpgmza_access_level_checked[4] = "selected"; }
        else { $wpgmza_access_level_checked[0] = "selected"; }
        

        if ($wpgmza_settings_filterbycat_type == "1" || $wpgmza_settings_filterbycat_type == "" || !$wpgmza_settings_filterbycat_type) { 
            $wpgmza_settings_filterbycat_type_checked_dropdown = "checked='checked'";
            $wpgmza_settings_filterbycat_type_checked_checkbox = "";
        } else {
            $wpgmza_settings_filterbycat_type_checked_checkbox = "checked='checked'";
            $wpgmza_settings_filterbycat_type_checked_dropdown = "";
        }


        if (isset($wpgmza_settings['wpgmza_settings_retina_width'])) { $wpgmza_settings_retina_width = $wpgmza_settings['wpgmza_settings_retina_width']; } else { $wpgmza_settings_retina_width = "31"; }
        if (isset($wpgmza_settings['wpgmza_settings_retina_height'])) { $wpgmza_settings_retina_height = $wpgmza_settings['wpgmza_settings_retina_height']; } else { $wpgmza_settings_retina_height = "45"; }

		$use_fontawesome = (isset($wpgmza_settings['use_fontawesome']) ? $wpgmza_settings['use_fontawesome'] : '4.*');
		$use_fontawesome_5_selected		= ($use_fontawesome == '5.*' ? 'selected="selected"' : '');
		$use_fontawesome_4_selected		= ($use_fontawesome == '4.*' ? 'selected="selected"' : '');
		$use_fontawesome_none_selected	= ($use_fontawesome == 'none' ? 'selected="selected"' : '');
		
		if($wpgmza)
		{
			// This can only be called if Basic is up to date
			$google_maps_api_loader = new WPGMZA\GoogleMapsAPILoader();
			$google_maps_api_settings_html = $google_maps_api_loader->getSettingsHTML();
		}
		else
			$google_maps_api_settings_html = '';
		
		$use_google_maps_selected			= (empty($wpgmza_settings['wpgmza_maps_engine']) || $wpgmza_settings['wpgmza_maps_engine'] == 'google-maps' ? 'selected="selected"' : "");
		$use_open_street_map_selected 		= (isset($wpgmza_settings['wpgmza_maps_engine']) && $wpgmza_settings['wpgmza_maps_engine'] == 'open-layers' ? 'selected="selected"' : "");
        
		$tileServerHTML = "";
		$tileServerHTMLFilename = plugin_dir_path(WPGMZA_FILE) . 'html/tile-server-fieldset.html.php';
		if(file_exists($tileServerHTMLFilename))
		{
			$tileServerSelect = new WPGMZA\DOMDocument();
			$tileServerSelect->loadPHPFile($tileServerHTMLFilename);
			// TODO: In Pro, check this property exists
			
			if(isset($wpgmza_settings['tile_server_url']))
			{
				$option = $tileServerSelect->querySelector('option[value="' . $wpgmza_settings['tile_server_url'] . '"]');
				if($option)
					$option->setAttribute('selected', 'selected');
			}
			
			$tileServerHTML = $tileServerSelect->html;
		}
		
        return "
            <h3>".__("Map Settings","wp-google-maps")."</h3>
                

                

                <table class='form-table'>
					<tr>
						<td>
							" . __("Maps Engine:", "wp-google-maps") . "
						</td>
						<td>
							<select name='wpgmza_maps_engine'>
								<option $use_open_street_map_selected value='open-layers'>OpenLayers</option>
								<option $use_google_maps_selected value='google-maps'>Google Maps</option>
							</select>
						</td>
					</tr>
					
					" . $tileServerHTML . "


                    <tr>
                         <td width='200' valign='top' style='vertical-align:top;'>".__("General Map Settings","wp-google-maps").":</td>
                         <td>
							<div class='switch'>
								<input name='wpgmza_settings_map_full_screen_control' 
									type='checkbox' 
									class='cmn-toggle cmn-toggle-round-flat' 
									id='wpgmza_settings_map_full_screen_control' 
									value='yes' 
									$wpgmza_fullscreen_checked />
								<label for='wpgmza_settings_map_full_screen_control'></label>
							</div>
							".__("Disable Full Screen Control")."<br />
							
							<div data-required-maps-engine='google-maps'>
								<div class='switch'>
									<input name='wpgmza_settings_map_streetview' 
										type='checkbox' 
										class='cmn-toggle cmn-toggle-round-flat' 
										id='wpgmza_settings_map_streetview' 
										value='yes' 
										$wpgmza_streetview_checked /> 
									<label for='wpgmza_settings_map_streetview'></label>
								</div>
								".__("Disable StreetView")."
							</div>
							
                            <div class='switch'>
								<input name='wpgmza_settings_map_zoom' 
									type='checkbox' 
									class='cmn-toggle cmn-toggle-round-flat' 
									id='wpgmza_settings_map_zoom' 
									value='yes' 
									$wpgmza_zoom_checked /> 
								<label for='wpgmza_settings_map_zoom'></label>
							</div>
							".__("Disable Zoom Controls")."<br />
							
							<div data-required-maps-engine='google-maps'>
								<div class='switch'>
									<input name='wpgmza_settings_map_pan' 
										type='checkbox' 
										class='cmn-toggle cmn-toggle-round-flat' 
										id='wpgmza_settings_map_pan' 
										value='yes' 
										$wpgmza_pan_checked /> 
										
									<label for='wpgmza_settings_map_pan'></label>
								</div>
								".__("Disable Pan Controls")."
							</div>
							
							<div data-required-maps-engine='google-maps'>
								<div class='switch'>
									<input name='wpgmza_settings_map_type' 
										type='checkbox' 
										class='cmn-toggle cmn-toggle-round-flat' 
										id='wpgmza_settings_map_type' 
										value='yes' 
										$wpgmza_type_checked /> 
									<label for='wpgmza_settings_map_type'></label>
								</div>
								".__("Disable Map Type Controls")."
							</div>
									
							
                            <div class='switch'>
								<input name='wpgmza_settings_map_scroll' 
									type='checkbox' 
									class='cmn-toggle cmn-toggle-round-flat' 
									id='wpgmza_settings_map_scroll' 
									value='yes' 
									$wpgmza_scroll_checked /> 
								<label for='wpgmza_settings_map_scroll'></label>
							</div>
							".__("Disable Mouse Wheel Zoom","wp-google-maps")."
							
							<br />
							
                            <div class='switch'>
								<input name='wpgmza_settings_map_draggable' 
									type='checkbox' 
									class='cmn-toggle cmn-toggle-round-flat' 
									id='wpgmza_settings_map_draggable' 
									value='yes' 
									$wpgmza_draggable_checked />
								<label for='wpgmza_settings_map_draggable'></label>
							</div>
							".__("Disable Mouse Dragging","wp-google-maps")."
							
							<br />
							
                            <div class='switch'>
								<input name='wpgmza_settings_map_clickzoom' 
									type='checkbox' 
									class='cmn-toggle cmn-toggle-round-flat' 
									id='wpgmza_settings_map_clickzoom' 
									value='yes' 
									$wpgmza_clickzoom_checked />
								<label for='wpgmza_settings_map_clickzoom'></label>
							</div>
							".__("Disable Mouse Double Click Zooming","wp-google-maps")."
							
							<br />
						</td>
                    </tr>
					
					<tr>
						<td>" . __("User Interface Style:", "wp-google-maps") . "</td>
		
					<td>
						<input type='radio'
						name='user_interface_style'
						id='user_interface_style_bare_bones'
						value='bare-bones' " 
						. $user_interface_style_checked[0] . 
						 " />" . __("Bare Bones - Applies no styling to the components at all. This is recommended for designers and developers who want to style the components from scratch.", "wp-google-maps") . "
						
									<br />

									<input type='radio'
									name='user_interface_style'
									id='user_interface_style_default'
									value='default' " 
									. $user_interface_style_checked[1] . 
									 " />" . __("Default - The default front end.", "wp-google-maps") . "
									
									<br />

						<input type='radio'
						name='user_interface_style'
						id='user_interface_style_legacy'
						value='legacy' " 
						. $user_interface_style_checked[2] . 
						 " />" . __("Legacy - This setting is the same as Default, but provides options to change individual components to the modern style.", "wp-google-maps") . "
						
						<br />
														
						<input type='radio'
						name='user_interface_style'
						id='user_interface_style_compact'
						value='compact' " 
						. $user_interface_style_checked[3] . 
						 " />" . __("Compact - Puts all components and their labels inline.", "wp-google-maps") . "
						
						<br />
													
						<input type='radio'
						name='user_interface_style'
						id='user_interface_style_modern'
						value='modern' " 
						. $user_interface_style_checked[4] . 
						 " />" . __("Modern - Puts components inside the map, with pull-out panels.", "wp-google-maps") . "
						
						<br />
													
						<input type='radio'
						name='user_interface_style'
						id='user_interface_style_minimal'
						value='minimal' " 
						. $user_interface_style_checked[5] . 
						 " />" . __("Minimal - The same as Compact, but with icons instead of text labels.", "wp-google-maps") . "
						
						<br />
								
						</td>
					</tr>
					
                    <tr>
                        <td valign='top' style='vertical-align:top;'>".__("Open Marker InfoWindows by","wp-google-maps")." </td>
                            <td>
								<input name='wpgmza_settings_map_open_marker_by' type='radio' id='wpgmza_settings_map_open_marker_by_click' value='1' ".$wpgmza_settings_map_open_marker_by_checked[0]." />
								" . __("Click", "wp-google-maps") . "
								<br />
								<input name='wpgmza_settings_map_open_marker_by' type='radio' id='wpgmza_settings_map_open_marker_by_hover' value='2' ".$wpgmza_settings_map_open_marker_by_checked[1]." />Hover </td>
                    </tr>
                    <tr>
                        <td valign='top' style='vertical-align:top;'>".__("Category Selection Logic","wp-google-maps")." </td>
                            <td>
                            	<input name='wpgmza_settings_cat_logic' type='radio' id='wpgmza_settings_cat_logic_or' value='0' ".$wpgmza_settings_cat_logic_checked[0]." />".__("OR"," wp-google-maps")." &nbsp; (<span class='description'>".__("Example: Show the marker if it belongs to Cat A _OR_ Cat B.", "wp-google-maps")."</span>)<br />
                            	<input name='wpgmza_settings_cat_logic' type='radio' id='wpgmza_settings_cat_logic_and' value='1' ".$wpgmza_settings_cat_logic_checked[1]." />".__("AND"," wp-google-maps")." &nbsp; (<span class='description'>".__("Example: Only show the marker if it belongs to Cat A _AND_ Cat B.", "wp-google-maps")."</span>)

                        	</td>
                    </tr>
                    <tr>
                         <td width='200' valign='top' style='vertical-align:top;'>".__("Filter by category displayed as","wp-google-maps").":</td>
                         <td>
                                <input name='wpgmza_settings_filterbycat_type' type='radio' id='wpgmza_settings_filterbycat_type_dropdown' value='1' $wpgmza_settings_filterbycat_type_checked_dropdown /> ".__("Dropdown","wp-google-maps")."<br />
                                <input name='wpgmza_settings_filterbycat_type' type='radio' id='wpgmza_settings_filterbycat_type_checkboxes' value='2' $wpgmza_settings_filterbycat_type_checked_checkbox /> ".__("Checkboxes","wp-google-maps")."<br />
                            </td>
                    </tr>
					<tr>
						<td>
							" . __('Order category filter items by', 'wp-google-maps') . "
						</td>
						<td>
							<select name='order_categories_by'>
								<option value='priority' " . ($wpgmza->settings->order_categories_by == 'priority' ? 'selected="selected"' : '') . ">
									" . __('Priority', 'wp-google-maps') . "
								</option>
								<option value='id'" . ($wpgmza->settings->order_categories_by == 'id' ? 'selected="selected"' : '') . ">
									" . __('ID', 'wp-google-maps') . "
								</option>
								<option value='category_name'" . ($wpgmza->settings->order_categories_by == 'category_name' ? 'selected="selected"' : '') . ">
									" . __('Name', 'wp-google-maps') . "
								</option>
							</select>
						</td>
					</tr>
                    <tr>
                         <td width='200' valign='top' style='vertical-align:top;'>".__("Additional Category Settings","wp-google-maps").":</td>
                         <td>
                                <div class='switch'><input name='wpgmza_settings_cat_display_qty' type='checkbox' class='cmn-toggle cmn-toggle-round-flat' id='wpgmza_settings_cat_display_qty' value='yes' $wpgmza_cat_qty_checked /> <label for='wpgmza_settings_cat_display_qty'></label></div>".__("Enable Category Count")." <span class='description'>(Displays a count of the markers per category on the front end)</span><br />
                            </td>
                    </tr>

                    <tr data-required-maps-engine='google-maps'>
                         <td width='200' valign='top'>".__("Troubleshooting Options","wp-google-maps").":</td>
                         <td>
                                 <div class='switch'><input name='wpgmza_settings_remove_api' type='checkbox' class='cmn-toggle cmn-toggle-yes-no' id='wpgmza_settings_remove_api' value='yes' $wpgmza_remove_api_checked /> <label for='wpgmza_settings_remove_api' data-on='".__("Yes", "wp-google-maps")."' data-off='".__("No", "wp-google-maps")."'></label></div> ".__("Do not load the Google Maps API (Only check this if your theme loads the Google Maps API by default)", 'wp-google-maps')."<br />
                        </td>
                    </tr>
					
					" . $google_maps_api_settings_html . "
					
					<tr>
						<td>
							" . __("Use FontAwesome:", "wp-google-maps") . "
						</td>
						<td>
							<select name='wpgmza_use_fontawesome'>
								<option value='5.*' $use_fontawesome_5_selected>5.*</option>
								<option value='4.*' $use_fontawesome_4_selected>4.*</option>
								<option value='none' $use_fontawesome_none_selected>" . __("None", "wp-google-maps") . "</option>
							</select>
						</td>
					</tr>


				   
            <tr>
                    <td width='200' valign='top'>".__("Lowest level of access to the map editor","wp-google-maps").":</td>
                 <td>
                    <select id='wpgmza_access_level' name='wpgmza_access_level'  >
                                <option value=\"manage_options\" ".$wpgmza_access_level_checked[0].">Admin</option>
                                <option value=\"edit_pages\" ".$wpgmza_access_level_checked[1].">Editor</option>
                                <option value=\"publish_posts\" ".$wpgmza_access_level_checked[2].">Author</option>
                                <option value=\"edit_posts\" ".$wpgmza_access_level_checked[3].">Contributor</option>
                                <option value=\"read\" ".$wpgmza_access_level_checked[4].">Subscriber</option>
                    </select>    
                </td>
            </tr>
                    <tr>
                         <td width='400'>".__("Retina Icon Width","wp-google-maps").":</td>
                         <td><input id='wpgmza_settings_retina_width' name='wpgmza_settings_retina_width' type='text' size='4' maxlength='4' value='$wpgmza_settings_retina_width' /> px </td>
                    </tr>
                    <tr>
                         <td>".__("Retina Icon Height","wp-google-maps").":</td>
                         <td><input id='wpgmza_settings_retina_height' name='wpgmza_settings_retina_height' type='text' size='4' maxlength='4' value='$wpgmza_settings_retina_height' /> px </td>
                    </tr> 

		            <tr>
		            	<td width='200' valign='top'>".__("Greedy Gesture Handling","wp-google-maps").":</td>
		           		<td>
		            		<div><input name='wpgmza_force_greedy_gestures' type='checkbox' class='cmn-toggle cmn-toggle-yes-no' id='wpgmza_force_greedy_gestures' value='yes' $wpgmza_force_greedy_gestures_checked /> <label for='wpgmza_force_greedy_gestures' data-on='".__("Yes", "wp-google-maps")."' data-off='".__("No", "wp-google-maps")."'></label></div> " . __("Removes the need to use two fingers to move the map on mobile devices, removes 'Use ctrl + scroll to zoom the map'", "wp-google-maps") . "
		               </td>
		            </tr>           
					
					
					
                    
                </table>
                ".apply_filters("wpgooglemaps_map_settings_output_bottom","",$wpgmza_settings)."
                
            ";




    }


    if ($section == "infowindow") {
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");

        if (isset($wpgmza_settings['wpgmza_settings_image_width'])) { $wpgmza_set_img_width = $wpgmza_settings['wpgmza_settings_image_width']; }
        if (isset($wpgmza_settings['wpgmza_settings_image_height'])) { $wpgmza_set_img_height = $wpgmza_settings['wpgmza_settings_image_height']; }
        if (isset($wpgmza_settings['wpgmza_settings_infowindow_links'])) { $wpgmza_settings_infowindow_links = $wpgmza_settings['wpgmza_settings_infowindow_links']; }
        if (isset($wpgmza_settings['wpgmza_settings_infowindow_address'])) { $wpgmza_settings_infowindow_address = $wpgmza_settings['wpgmza_settings_infowindow_address']; }
        if (isset($wpgmza_settings['wpgmza_settings_infowindow_link_text'])) { $wpgmza_link_text = $wpgmza_settings['wpgmza_settings_infowindow_link_text']; } else { $wpgmza_link_text = false; }

        if (isset($wpgmza_settings['wpgmza_settings_image_resizing'])) { $wpgmza_set_resize_img = $wpgmza_settings['wpgmza_settings_image_resizing']; }
        /**
         * Deprecated in 6.09
         * if (isset($wpgmza_settings['wpgmza_settings_use_timthumb'])) { $wpgmza_set_use_timthumb = $wpgmza_settings['wpgmza_settings_use_timthumb']; }
         */
        
        
        if (!$wpgmza_link_text) { $wpgmza_link_text = __("More details","wp-google-maps"); }
		
		$wpgmza_settings_disable_infowindows = '';
		if(!empty($wpgmza_settings['wpgmza_settings_disable_infowindows']))
			$wpgmza_settings_disable_infowindows = ' checked="checked"';
		
        if (isset($wpgmza_settings['wpgmza_settings_infowindow_width'])) { $wpgmza_settings_infowindow_width = $wpgmza_settings['wpgmza_settings_infowindow_width'];} else { $wpgmza_settings_infowindow_width = ""; }

        if (isset($wpgmza_set_resize_img) && $wpgmza_set_resize_img == "yes") { $wpgmza_resizechecked = "checked='checked'"; } else { $wpgmza_resizechecked = ""; }
		/**
		 * Deprecated in 6.09
		 * if (isset($wpgmza_set_use_timthumb) && $wpgmza_set_use_timthumb == "yes") { $wpgmza_timchecked = "checked='checked'";  } else { $wpgmza_timchecked = ""; }
		 */


		if (isset($wpgmza_settings['wpgmza_iw_type'])) { 
			$infowwindow_sel_checked[$wpgmza_settings['wpgmza_iw_type']] = "checked"; $wpgmza_iw_class[$wpgmza_settings['wpgmza_iw_type']] = "wpgmza_mlist_selection_activate"; 
		} else {  
			$wpgmza_iw_type = false; 
		}


		

		for ($i=0;$i<5;$i++) {
            if (!isset($wpgmza_iw_class[$i])) { $wpgmza_iw_class[$i] = ""; }
        }
		for ($i=0;$i<5;$i++) {
            if (!isset($infowwindow_sel_checked[$i])) { $infowwindow_sel_checked[$i] = ""; }
        }	

        if ($infowwindow_sel_checked[0] == "checked") {
        	$infowwindow_sel_text = __("Default Infowindow","wp-google-maps");
        } else if ($infowwindow_sel_checked[1] == "checked") {
        	$infowwindow_sel_text = __("Modern Infowindow","wp-google-maps");
        }else if ($infowwindow_sel_checked[2] == "checked") {
        	$infowwindow_sel_text = __("Modern Plus Infowindow","wp-google-maps");
        }else if ($infowwindow_sel_checked[3] == "checked") {
        	$infowwindow_sel_text = __("Circular Infowindow","wp-google-maps");
        } else if ($infowwindow_sel_checked[4] == "checked") {
        	$infowwindow_sel_text = __("No Global Setting","wp-google-maps");
        } else {
        	$infowwindow_sel_text = __("No Global Setting","wp-google-maps");
        }




        if (!isset($wpgmza_set_img_width) || $wpgmza_set_img_width == "") { $wpgmza_set_img_width = ""; }
        if (!isset($wpgmza_set_img_height) || $wpgmza_set_img_height == "" ) { $wpgmza_set_img_height = ""; }
        if (!isset($wpgmza_settings_infowindow_width) || $wpgmza_settings_infowindow_width == "") { $wpgmza_settings_infowindow_width = ""; }
        if (isset($wpgmza_settings_infowindow_links) && $wpgmza_settings_infowindow_links == "yes") { $wpgmza_linkschecked = "checked='checked'"; } else { $wpgmza_linkschecked = ""; }
        if (isset($wpgmza_settings_infowindow_address) && $wpgmza_settings_infowindow_address == "yes") { $wpgmza_addresschecked = "checked='checked'"; } else { $wpgmza_addresschecked = ""; }

        return "
                <h3>".__("InfoWindow Settings","wp-google-maps")."</h3>

				<table class=\"form-table\"><form method=\"post\"></form>
				    <tbody>
				    	<tr>
					        <th>
					        	<label for=\"\">".__("Infowindow Style","wp-google-maps")."</label>
				        	</th>
					        <td>
					        	<div class='wpgmza-infowindow-style-picker wpgmza-flex'>
					        		<div class='wpgmza-flex-item wpgmza-infowindow-picker__item ".$wpgmza_iw_class[0]."'>
					        			<div class='wpgmza-card wpgmza-card-border__hover'>
					        				<img src=\"".WPGMAPS_DIR."/images/marker_iw_type_1.png\" title=\"Default\" id=\"wpgmza_iw_selection_1\" width=\"250\" class=\"wpgmza_mlist_selection ".$wpgmza_iw_class[0]."\">
					        				<span class='wpgmza-infowindow-style__name'>" . __( 'Default Infowindow', 'wpgooglemaps' ) . "</span>
					        			</div>
					        		</div>
						             
					        		<div class='wpgmza-flex-item wpgmza-infowindow-picker__item ".$wpgmza_iw_class[1]."'>
					        			<div class='wpgmza-card wpgmza-card-border__hover'>
						           		<img src=\"".WPGMAPS_DIR."/images/marker_iw_type_2.png\" title=\"Modern\" id=\"wpgmza_iw_selection_2\" width=\"250\" class=\"wpgmza_mlist_selection ".$wpgmza_iw_class[1]."\"> 
						           		<span class='wpgmza-infowindow-style__name'>" . __( 'Modern InfoWindow', 'wpgooglemaps' ) . "</span>
						           		</div>
						           	</div>

						           	<div class='wpgmza-flex-item wpgmza-infowindow-picker__item ".$wpgmza_iw_class[2]."'>
					        			<div class='wpgmza-card wpgmza-card-border__hover'>
						            		<img src=\"".WPGMAPS_DIR."/images/marker_iw_type_3.png\" title=\"Plus\" id=\"wpgmza_iw_selection_3\" width=\"250\" class=\"wpgmza_mlist_selection ".$wpgmza_iw_class[2]."\">   
						            		<span class='wpgmza-infowindow-style__name'>" . __( 'Modern Plus Infowindow', 'wpgooglemaps' ) . "</span>
						            	</div>
						            </div>

						            <div class='wpgmza-flex-item wpgmza-infowindow-picker__item ".$wpgmza_iw_class[3]."'>
					        			<div class='wpgmza-card wpgmza-card-border__hover'>
						            		<img src=\"".WPGMAPS_DIR."/images/marker_iw_type_4.png\" title=\"Circular\" id=\"wpgmza_iw_selection_4\" width=\"250\" class=\"wpgmza_mlist_selection ".$wpgmza_iw_class[3]."\">
						            		<span class='wpgmza-infowindow-style__name'>" . __( 'Circular Infowindow', 'wpgooglemaps' ) . "</span>
						            	</div>
						            </div>

						            <div class='wpgmza-flex-item wpgmza-infowindow-picker__item ".$wpgmza_iw_class[4]."'>
					        			<div class='wpgmza-card wpgmza-card-border__hover'>
						            		<img src=\"".WPGMAPS_DIR."/images/marker_iw_type_null.png\" title=\"No Global\" id=\"wpgmza_iw_selection_null\" width=\"250\" class=\"wpgmza_mlist_selection ".$wpgmza_iw_class[4]."\">

						            	</div>
						            </div>

	                                <input type=\"radio\" name=\"wpgmza_iw_type\" id=\"rb_wpgmza_iw_selection_1\" value=\"0\" ".$infowwindow_sel_checked[0]." class=\"sola_t_hide_input\">
	                                <input type=\"radio\" name=\"wpgmza_iw_type\" id=\"rb_wpgmza_iw_selection_2\" value=\"1\" ".$infowwindow_sel_checked[1]." class=\"sola_t_hide_input\">
	                                <input type=\"radio\" name=\"wpgmza_iw_type\" id=\"rb_wpgmza_iw_selection_3\" value=\"2\" ".$infowwindow_sel_checked[2]." class=\"sola_t_hide_input\">
	                                <input type=\"radio\" name=\"wpgmza_iw_type\" id=\"rb_wpgmza_iw_selection_4\" value=\"3\" ".$infowwindow_sel_checked[3]." class=\"sola_t_hide_input\">
	                                <input type=\"radio\" name=\"wpgmza_iw_type\" id=\"rb_wpgmza_iw_selection_null\" value=\"-1\" ".$infowwindow_sel_checked[4]." class=\"sola_t_hide_input\">
	                            </div>
					        </td>
				    	</tr>
				    	<tr>
					        <th>
					        	&nbsp;
				        	</th>
					        <td>     
					       	 ".__("Your selection:","wp-google-maps")."   
					            <span class=\"wpgmza_iw_sel_text\" style=\"font-weight:bold;\">".$infowwindow_sel_text."</span>
					        </td>
				    	</tr>
				    </table>


                <table class='form-table'>
                    <tr>
                         <td>".__("Resize Images","wp-google-maps").":</td>
                         <td>
                                <div class='switch'><input name='wpgmza_settings_image_resizing' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_image_resizing' value='yes' $wpgmza_resizechecked /> <label for='wpgmza_settings_image_resizing'></label></div> ".__("Resize all images to the below sizes","wp-google-maps")."
                        </td>
                    </tr>
                    <tr>
                         <td width='200'>".__("Default Image Width","wp-google-maps").":</td>
                         <td><input id='wpgmza_settings_image_width' name='wpgmza_settings_image_width' type='text' size='4' maxlength='4' value='$wpgmza_set_img_width' /> px  <em>".__("(can be left blank - max width will be limited to max infowindow width)","wp-google-maps")."</em></td>
                    </tr>
                    <tr>
                         <td>".__("Default Image Height","wp-google-maps").":</td>
                         <td><input id='wpgmza_settings_image_height' name='wpgmza_settings_image_height' type='text' size='4' maxlength='4' value='$wpgmza_set_img_height' /> px <em>".__("(can be left blank - leaving both the width and height blank will revert to full size images being used)","wp-google-maps")."</em></td>
                    </tr>
                    <tr>
                         <td>".__("Max InfoWindow Width","wp-google-maps").":</td>
                         <td><input id='wpgmza_settings_infowindow_width' name='wpgmza_settings_infowindow_width' type='text' size='4' maxlength='4' value='$wpgmza_settings_infowindow_width' /> px <em>".__("(Minimum: 200px)","wp-google-maps")."</em></td>
                    </tr>
                    <tr>
                         <td>".__("Other settings","wp-google-maps").":</td>
                         <td>
                                <div class='switch'><input name='wpgmza_settings_infowindow_links' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_infowindow_links' value='yes' $wpgmza_linkschecked /> <label for='wpgmza_settings_infowindow_links'></label></div>".__("Open links in a new window","wp-google-maps")." <em>
                                ".__("(Tick this if you want to open your links in a new window)","wp-google-maps")."</em>
                                <br /><div class='switch'><input name='wpgmza_settings_infowindow_address' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='wpgmza_settings_infowindow_address' value='yes' $wpgmza_addresschecked /> <label for='wpgmza_settings_infowindow_address'></label></div>".__("Hide the address field","wp-google-maps")."<br />
                        </td>
                    </tr>
                    <tr>
                         <td>".__("Link text","wp-google-maps").":</td>
                         <td>
                                <input name='wpgmza_settings_infowindow_link_text' type='text' id='wpgmza_settings_infowindow_link_text' value='$wpgmza_link_text' /> 
                        </td>
                    </tr>
					
					" . '
					<tr>
						<td valign="top" width="200" style="vertical-align:top;">' . __("Disable InfoWindows", "wp-google-maps") . '</td>
						<td>
							<div class="switch">
								<input name="wpgmza_settings_disable_infowindows" type="checkbox" class="cmn-toggle cmn-toggle-round-flat" id="wpgmza_settings_disable_infowindows" value="yes" ' . $wpgmza_settings_disable_infowindows . ' />
								<label for="wpgmza_settings_disable_infowindows"></label>
							</div>
						</td>
					</tr>
					' . "
					
                </table>
                <br /><br />
        ";


    }
	
	if($section == 'store-locator')
	{
		$wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
		
		global $wpgmza_default_store_locator_radii;
		$wpgmza_store_locator_radii = implode(',', $wpgmza_default_store_locator_radii);
		
		if (!empty($wpgmza_settings['wpgmza_store_locator_radii']))
			$wpgmza_store_locator_radii = $wpgmza_settings['wpgmza_store_locator_radii'];
		
		$ret = "";
		$ret .= "   <div id=\"tabs-4\">";
		$ret .= "      <h3>".__("Store Locator", "wp-google-maps")."</h3>";
		$ret .= "      <table class='form-table'>";
		$ret .= "         <tr>";
		$ret .= "            <td valign='top' width='200' style='vertical-align:top;padding-left:0px;'>".__('Store Locator Radii', 'wp-google-maps')."</td>";
		$ret .= "            <td>";
		$ret .= "               <input type='text' id='wpgmza_store_locator_radii' name='wpgmza_store_locator_radii' value='".trim($wpgmza_store_locator_radii)."' class='wpgmza_store_locator_radii' required='required' pattern='^\d+(,\s*\d+)*$' style='width: 400px;' />";
		$ret .= "               <p style='font-style:italic;' class='store_locator_text_input_tip'>" . __('Use a comma to separate values, eg: 1, 5, 10, 50, 100', 'wp-google-maps') . "</p>";
		$ret .= "            </td>";
		$ret .= "         </tr>";
		$ret .= "      </table>";
		$ret .= "   </div>";
		
		return $ret;
	}
}
    
function wpgmza_version_check()
{
	trigger_error("Deprecated since 8.0.25");
}

/**
 * Handle POST for settings page
 * @return void
 */
add_action('admin_post_wpgmza_settings_page_post_pro', 'wpgmza_settings_page_post_pro');

function wpgmza_settings_page_post_pro()
{
	global $wpgmza;
	
	if(!wp_verify_nonce($_POST['wpgmza_settings_page_post_pro_nonce'], 'wpgmza_settings_page_post_pro'))
	{
		http_response_code(403);
		exit;
	}
	
	if($wpgmza)
	{
		$wpgmza->gdprCompliance->onPOST();
		
		if(class_exists('WPGMZA\\MarkerRating'))
			WPGMZA\MarkerRating::onSaveSettings();
	}
	
	$wasUsingDBPull = $wpgmza->settings->wpgmza_settings_marker_pull == 0;
	
	$checkboxes = array(
		"wpgmza_settings_map_full_screen_control",
		"wpgmza_settings_map_streetview",
		"wpgmza_settings_map_zoom",
		"wpgmza_settings_map_pan",
		"wpgmza_settings_map_type",
		"wpgmza_settings_map_scroll",
		"wpgmza_settings_map_draggable",
		"wpgmza_settings_map_clickzoom",
		"wpgmza_settings_cat_display_qty",
		"wpgmza_settings_force_jquery",
		"wpgmza_settings_remove_api",
		"wpgmza_force_greedy_gestures",
		"wpgmza_settings_image_resizing",
		"wpgmza_settings_infowindow_links",
		"wpgmza_settings_infowindow_address",
		"wpgmza_settings_disable_infowindows",
		"wpgmza_settings_markerlist_icon",
		"wpgmza_settings_markerlist_link",
		"wpgmza_settings_markerlist_title",
		"wpgmza_settings_markerlist_address",
		"wpgmza_settings_markerlist_category",
		"wpgmza_settings_markerlist_description",
		"wpgmza_do_not_enqueue_datatables",
		"wpgmza_settings_carousel_markerlist_image",
		"wpgmza_settings_carousel_markerlist_title",
		"wpgmza_settings_carousel_markerlist_icon",
		"wpgmza_settings_carousel_markerlist_address",
		"wpgmza_settings_carousel_markerlist_description",
		"wpgmza_settings_carousel_markerlist_marker_link",
		"wpgmza_settings_carousel_markerlist_directions",
		"carousel_lazyload",
		"carousel_autoheight",
		"carousel_pagination",
		"carousel_navigation",
		"wpgmza_developer_mode",
		'wpgmza_prevent_other_plugins_and_theme_loading_api',
		"wpgmza_gdpr_require_consent_before_load",
		"wpgmza_gdpr_override_notice",
		"wpgmza_gdpr_require_consent_before_vgm_submit",
        "wpgmza_settings_cat_display_qty",
        "wpgmza_settings_infowindow_links",
		'disable_autoptimize_compatibility_fix',
		'disable_compressed_path_variables'
	);
	
	foreach($checkboxes as $name) {
		$remap = $name;
		
		switch($name)
		{
			case 'wpgmza_developer_mode':
				$remap = preg_replace('/^wpgmza_/', '', $name);
				break;
		}
		
		if(!empty($_POST[$name]))
			$wpgmza->settings[$remap] = sanitize_text_field( $_POST[$name] );
		else if(isset($wpgmza->settings[$remap]))
			unset($wpgmza->settings[$remap]);
	}
	
	if (isset($_POST['wpgmza_settings_image_resizing']))
		$wpgmza->settings['wpgmza_settings_image_resizing'] = esc_attr($_POST['wpgmza_settings_image_resizing']);
	else
		$wpgmza->settings['wpgmza_settings_image_resizing'] = 'no';

	if (isset($_POST['wpgmza_settings_image_width']))
		$wpgmza->settings['wpgmza_settings_image_width'] = esc_attr($_POST['wpgmza_settings_image_width']);
	else
		$wpgmza->settings['wpgmza_settings_image_width'] = "";
	if (isset($_POST['wpgmza_settings_image_height'])) 
		$wpgmza->settings['wpgmza_settings_image_height'] = esc_attr($_POST['wpgmza_settings_image_height']);
	else 
		$wpgmza->settings['wpgmza_settings_image_height'] = "";

	if (isset($_POST['wpgmza_settings_disable_infowindows'])) 
		$wpgmza->settings['wpgmza_settings_disable_infowindows'] = true;
	else 
		$wpgmza->settings['wpgmza_settings_disable_infowindows'] = false;

	if (isset($_POST['wpgmza_settings_infowindow_width'])) 
		$wpgmza->settings['wpgmza_settings_infowindow_width'] = esc_attr($_POST['wpgmza_settings_infowindow_width']);

	if (isset($_POST['wpgmza_settings_infowindow_links'])) 
		$wpgmza->settings['wpgmza_settings_infowindow_links'] = esc_attr($_POST['wpgmza_settings_infowindow_links']);

	if (isset($_POST['wpgmza_settings_infowindow_address'])) 
		$wpgmza->settings['wpgmza_settings_infowindow_address'] = esc_attr($_POST['wpgmza_settings_infowindow_address']);

	if (isset($_POST['wpgmza_settings_infowindow_link_text'])) 
		$wpgmza->settings['wpgmza_settings_infowindow_link_text'] = esc_attr($_POST['wpgmza_settings_infowindow_link_text']);

	if (isset($_POST['wpgmza_settings_map_full_screen_control'])) 
		$wpgmza->settings['wpgmza_settings_map_full_screen_control'] = esc_attr($_POST['wpgmza_settings_map_full_screen_control']);
	
	if (isset($_POST['wpgmza_settings_map_streetview'])) 
		$wpgmza->settings['wpgmza_settings_map_streetview'] = esc_attr($_POST['wpgmza_settings_map_streetview']);

	if (isset($_POST['wpgmza_settings_map_zoom'])) 
		$wpgmza->settings['wpgmza_settings_map_zoom'] = esc_attr($_POST['wpgmza_settings_map_zoom']);

	if (isset($_POST['wpgmza_settings_map_pan'])) 
		$wpgmza->settings['wpgmza_settings_map_pan'] = esc_attr($_POST['wpgmza_settings_map_pan']);

	if (isset($_POST['wpgmza_settings_map_type'])) 
		$wpgmza->settings['wpgmza_settings_map_type'] = esc_attr($_POST['wpgmza_settings_map_type']);

	if (isset($_POST['wpgmza_settings_map_scroll'])) 
		$wpgmza->settings['wpgmza_settings_map_scroll'] = esc_attr($_POST['wpgmza_settings_map_scroll']);

	if (isset($_POST['wpgmza_settings_map_draggable'])) 
		$wpgmza->settings['wpgmza_settings_map_draggable'] = esc_attr($_POST['wpgmza_settings_map_draggable']);

	if (isset($_POST['wpgmza_settings_map_clickzoom'])) 
		$wpgmza->settings['wpgmza_settings_map_clickzoom'] = esc_attr($_POST['wpgmza_settings_map_clickzoom']);

	if (isset($_POST['wpgmza_settings_cat_display_qty'])) 
		$wpgmza->settings['wpgmza_settings_cat_display_qty'] = esc_attr($_POST['wpgmza_settings_cat_display_qty']);

	if (isset($_POST['wpgmza_settings_map_striptags'])) 
		$wpgmza->settings['wpgmza_settings_ugm_striptags'] = esc_attr($_POST['wpgmza_settings_map_striptags']);
	else 
		$wpgmza->settings['wpgmza_settings_map_striptags'] = '0';

	if (isset($_POST['wpgmza_settings_ugm_autoapprove'])) 
		$wpgmza->settings['wpgmza_settings_ugm_autoapprove'] = esc_attr($_POST['wpgmza_settings_ugm_autoapprove']);
	else 
		$wpgmza->settings['wpgmza_settings_ugm_autoapprove'] = '0';

	if (isset($_POST['wpgmza_settings_ugm_email_new_marker'])) 
		$wpgmza->settings['wpgmza_settings_ugm_email_new_marker'] = esc_attr($_POST['wpgmza_settings_ugm_email_new_marker']);
	else 
		$wpgmza->settings['wpgmza_settings_ugm_email_new_marker'] = '0';
	
	if (isset($_POST['wpgmza_settings_ugm_email_address'])) 
		$wpgmza->settings['wpgmza_settings_ugm_email_address'] = esc_attr($_POST['wpgmza_settings_ugm_email_address']);
	else 
		$wpgmza->settings['wpgmza_settings_ugm_email_address'] = get_option('admin_email');

	if (isset($_POST['wpgmza_settings_force_jquery'])) 
		$wpgmza->settings['wpgmza_settings_force_jquery'] = esc_attr($_POST['wpgmza_settings_force_jquery']);

	if (isset($_POST['wpgmza_settings_remove_api'])) 
		$wpgmza->settings['wpgmza_settings_remove_api'] = esc_attr($_POST['wpgmza_settings_remove_api']);

	if(isset($_POST['tile_server_url']))
		$wpgmza->settings['tile_server_url'] = $_POST['tile_server_url'];
	
	if(isset($_POST['wpgmza_load_engine_api_condition']))
		$wpgmza->settings['wpgmza_load_engine_api_condition'] = $_POST['wpgmza_load_engine_api_condition'];
	
	if(isset($_POST['wpgmza_always_include_engine_api_on_pages']))
		$wpgmza->settings['wpgmza_always_include_engine_api_on_pages'] = $_POST['wpgmza_always_include_engine_api_on_pages'];
	
	if(isset($_POST['wpgmza_always_exclude_engine_api_on_pages']))
		$wpgmza->settings['wpgmza_always_exclude_engine_api_on_pages'] = $_POST['wpgmza_always_exclude_engine_api_on_pages'];
	
	if (isset($_POST['wpgmza_use_fontawesome']))
		$wpgmza->settings['use_fontawesome'] = $_POST['wpgmza_use_fontawesome'];
	
	if(isset($_POST['wpgmza_maps_engine']))
		$wpgmza->settings['wpgmza_maps_engine'] = $_POST['wpgmza_maps_engine'];

	if (isset($_POST['wpgmza_force_greedy_gestures'])) 
		$wpgmza->settings['wpgmza_force_greedy_gestures'] = esc_attr($_POST['wpgmza_force_greedy_gestures']);

	if (isset($_POST['wpgmza_settings_markerlist_category'])) 
		$wpgmza->settings['wpgmza_settings_markerlist_category'] = esc_attr($_POST['wpgmza_settings_markerlist_category']);

	if (isset($_POST['wpgmza_settings_markerlist_icon'])) 
		$wpgmza->settings['wpgmza_settings_markerlist_icon'] = esc_attr($_POST['wpgmza_settings_markerlist_icon']);
	
	if (isset($_POST['wpgmza_settings_markerlist_link'])) 
		$wpgmza->settings['wpgmza_settings_markerlist_link'] = esc_attr($_POST['wpgmza_settings_markerlist_link']);

	if (isset($_POST['wpgmza_settings_markerlist_title'])) 
		$wpgmza->settings['wpgmza_settings_markerlist_title'] = esc_attr($_POST['wpgmza_settings_markerlist_title']);

	if (isset($_POST['wpgmza_settings_markerlist_address'])) 
		$wpgmza->settings['wpgmza_settings_markerlist_address'] = esc_attr($_POST['wpgmza_settings_markerlist_address']);

	if (isset($_POST['wpgmza_settings_markerlist_description'])) 
		$wpgmza->settings['wpgmza_settings_markerlist_description'] = esc_attr($_POST['wpgmza_settings_markerlist_description']);

	if (isset($_POST['wpgmza_do_not_enqueue_datatables'])) 
		$wpgmza->settings['wpgmza_do_not_enqueue_datatables'] = esc_attr($_POST['wpgmza_do_not_enqueue_datatables']);

	if (isset($_POST['wpgmza_custom_css'])) 
		$wpgmza->settings['wpgmza_custom_css'] = esc_attr($_POST['wpgmza_custom_css']);

	if (isset($_POST['wpgmza_custom_js'])) 
		$wpgmza->settings['wpgmza_custom_js'] = $_POST['wpgmza_custom_js'];
	
	if(isset($_POST['wpgmza_developer_mode']))
		$wpgmza->settings['developer_mode'] = true;

	if (isset($_POST['wpgmza_settings_carousel_markerlist_image'])) 
		$wpgmza->settings['wpgmza_settings_carousel_markerlist_image'] = esc_attr($_POST['wpgmza_settings_carousel_markerlist_image']);

	if (isset($_POST['wpgmza_settings_carousel_markerlist_title'])) 
		$wpgmza->settings['wpgmza_settings_carousel_markerlist_title'] = esc_attr($_POST['wpgmza_settings_carousel_markerlist_title']);

	if (isset($_POST['wpgmza_settings_carousel_markerlist_icon'])) 
		$wpgmza->settings['wpgmza_settings_carousel_markerlist_icon'] = esc_attr($_POST['wpgmza_settings_carousel_markerlist_icon']);

	if (isset($_POST['wpgmza_settings_carousel_markerlist_address'])) 
		$wpgmza->settings['wpgmza_settings_carousel_markerlist_address'] = esc_attr($_POST['wpgmza_settings_carousel_markerlist_address']);

	if (isset($_POST['wpgmza_settings_carousel_markerlist_description'])) 
		$wpgmza->settings['wpgmza_settings_carousel_markerlist_description'] = esc_attr($_POST['wpgmza_settings_carousel_markerlist_description']);

	if (isset($_POST['wpgmza_settings_carousel_markerlist_marker_link'])) 
		$wpgmza->settings['wpgmza_settings_carousel_markerlist_marker_link'] = esc_attr($_POST['wpgmza_settings_carousel_markerlist_marker_link']);

	if (isset($_POST['wpgmza_settings_carousel_markerlist_directions'])) 
		$wpgmza->settings['wpgmza_settings_carousel_markerlist_directions'] = esc_attr($_POST['wpgmza_settings_carousel_markerlist_directions']);

	if (isset($_POST['wpgmza_settings_carousel_markerlist_theme'])) 
		$wpgmza->settings['wpgmza_settings_carousel_markerlist_theme'] = esc_attr($_POST['wpgmza_settings_carousel_markerlist_theme']);

	if (isset($_POST['wpgmza_default_items'])) 
		$wpgmza->settings['wpgmza_default_items'] = esc_attr($_POST['wpgmza_default_items']);

	if (isset($_POST['carousel_items'])) 
		$wpgmza->settings['carousel_items'] = esc_attr($_POST['carousel_items']);

	if (isset($_POST['carousel_items_tablet'])) 
		$wpgmza->settings['carousel_items_tablet'] = esc_attr($_POST['carousel_items_tablet']);

	if (isset($_POST['carousel_items_mobile'])) 
		$wpgmza->settings['carousel_items_mobile'] = esc_attr($_POST['carousel_items_mobile']);

	if (isset($_POST['carousel_autoplay'])) 
		$wpgmza->settings['carousel_autoplay'] = esc_attr($_POST['carousel_autoplay']);

	if (isset($_POST['carousel_lazyload'])) 
		$wpgmza->settings['carousel_lazyload'] = esc_attr($_POST['carousel_lazyload']);

	if (isset($_POST['carousel_autoheight'])) 
		$wpgmza->settings['carousel_autoheight'] = esc_attr($_POST['carousel_autoheight']);

	if (isset($_POST['carousel_navigation'])) 
		$wpgmza->settings['carousel_navigation'] = esc_attr($_POST['carousel_navigation']);

	if (isset($_POST['carousel_pagination'])) 
		$wpgmza->settings['carousel_pagination'] = esc_attr($_POST['carousel_pagination']);

	if (isset($_POST['wpgmza_settings_filterbycat_type'])) 
		$wpgmza->settings['wpgmza_settings_filterbycat_type'] = esc_attr($_POST['wpgmza_settings_filterbycat_type']);
	
	if(isset($_POST['order_categories_by']))
		$wpgmza->settings['order_categories_by'] = $_POST['order_categories_by'];

	if (isset($_POST['wpgmza_settings_map_open_marker_by'])) 
		$wpgmza->settings['wpgmza_settings_map_open_marker_by'] = esc_attr($_POST['wpgmza_settings_map_open_marker_by']);

	if (isset($_POST['user_interface_style'])) 
		$wpgmza->settings['user_interface_style'] = esc_attr($_POST['user_interface_style']);
		
	if (isset($_POST['wpgmza_settings_cat_logic'])) 
		$wpgmza->settings['wpgmza_settings_cat_logic'] = esc_attr($_POST['wpgmza_settings_cat_logic']);

	if (isset($_POST['wpgmza_api_version'])) 
		$wpgmza->settings['wpgmza_api_version'] = esc_attr($_POST['wpgmza_api_version']);

	if (isset($_POST['wpgmza_marker_xml_location'])) 
		update_option("wpgmza_xml_location", stripslashes($_POST['wpgmza_marker_xml_location']));

	if (isset($_POST['wpgmza_marker_xml_url'])) 
		update_option("wpgmza_xml_url", $_POST['wpgmza_marker_xml_url']);

	if (isset($_POST['wpgmza_access_level'])) 
		$wpgmza->settings['wpgmza_settings_access_level'] = esc_attr($_POST['wpgmza_access_level']);

	if (isset($_POST['wpgmza_settings_retina_width'])) 
		$wpgmza->settings['wpgmza_settings_retina_width'] = esc_attr($_POST['wpgmza_settings_retina_width']);

	if (isset($_POST['wpgmza_settings_retina_height'])) 
		$wpgmza->settings['wpgmza_settings_retina_height'] = esc_attr($_POST['wpgmza_settings_retina_height']);

	if (isset($_POST['wpgmza_settings_marker_pull'])) 
		$wpgmza->settings['wpgmza_settings_marker_pull'] = esc_attr($_POST['wpgmza_settings_marker_pull']);
	
	if (isset($_POST['wpgmza_store_locator_radii']))
		$wpgmza->settings['wpgmza_store_locator_radii'] = sanitize_text_field($_POST['wpgmza_store_locator_radii']);

	if (isset($_POST['wpgmza_iw_type'])) 
		$wpgmza->settings['wpgmza_iw_type'] = esc_attr($_POST['wpgmza_iw_type']);
	else 
		$wpgmza->settings['wpgmza_iw_type'] = '-1';
	
	if(isset($_POST['open_route_service_key']))
		$wpgmza->settings->open_route_service_key = $_POST['open_route_service_key'];

	$arr = apply_filters("wpgooglemaps_filter_save_settings", $wpgmza->settings);
	$wpgmza->settings->set($arr);

	if (isset($_POST['wpgmza_google_maps_api_key'])) 
		update_option('wpgmza_google_maps_api_key', $_POST['wpgmza_google_maps_api_key']);

	if($_POST['wpgmza_settings_marker_pull'] == 1 && $wasUsingDBPull)
		wpgmaps_update_all_xml_file();
	
	$wpgmza->settings->user_interface_style = $_POST['user_interface_style'];

	wp_redirect(get_admin_url() . 'admin.php?page=wp-google-maps-menu-settings');
	exit;

}

function wpgmaps_head_pro() {
	global $wpgmza;
    global $wpgmza_tblname_maps;
	
	if (!$wpgmza->isUserAllowedToEdit()) {
	   return false;
	}
	
	// TODO: Move this to admin_post
    if (isset($_POST['wpgmza_savemap'])){
        global $wpdb;
		
		if(!wp_verify_nonce($_POST['real_post_nonce'], 'wpgmza'))
		{
			wp_die( __( 'You do not have permission to perform this function', 'wp-google-maps' ) );
			exit;
		}

        $map_id = esc_attr($_POST['wpgmza_id']);
		
		$map = WPGMZA\Map::createInstance($map_id);
		
        $map_title = esc_attr(stripslashes($_POST['wpgmza_title']));
		
        $map_height = esc_attr($_POST['wpgmza_height']);
        $map_width = esc_attr($_POST['wpgmza_width']);


        $map_width_type = esc_attr($_POST['wpgmza_map_width_type']);
        if ($map_width_type == "%") { $map_width_type = "\%"; }
        $map_height_type = esc_attr($_POST['wpgmza_map_height_type']);
        if ($map_height_type == "%") { $map_height_type = "\%"; }
        $map_start_location = esc_attr($_POST['wpgmza_start_location']);
        if (isset($_POST['wpgmza_start_zoom'])) { $map_start_zoom = intval($_POST['wpgmza_start_zoom']); } else { $map_start_zoom = ""; }
		
		if(isset($_POST['wpgmza_map_type']))
			$type = intval($_POST['wpgmza_map_type']);
		else
			$type = 1;
		
        $directions_enabled = isset($_POST['directions_enabled']) ? 1 : 0;
        $traffic_enabled = isset($_POST['wpgmza_traffic']) ? 1 : 2;
        $wpgmza_auto_night_enabled = isset($_POST['wpgmza_auto_night']);
        
        $alignment = intval($_POST['wpgmza_map_align']);
		$order_markers_by = intval($_POST['wpgmza_order_markers_by']);
        $order_markers_choice = intval($_POST['wpgmza_order_markers_choice']);
        $bicycle_enabled = isset($_POST['wpgmza_bicycle']) ? 1 : 2;
        $show_user_location = isset($_POST['wpgmza_show_user_location']) ? 1 : 2;

        if (isset($_POST['wpgmza_listmarkers'])) { $listmarkers = intval($_POST['wpgmza_listmarkers']); } else { $listmarkers = ""; }
        if (isset($_POST['wpgmza_listmarkers_advanced'])) { $listmarkers_advanced = intval($_POST['wpgmza_listmarkers_advanced']); } else { $listmarkers_advanced = ""; }
        if (isset($_POST['wpgmza_filterbycat'])) { $filterbycat = intval($_POST['wpgmza_filterbycat']); } else { $filterbycat = ""; }
		
        $other_settings = array();
		
		// Enabled filters
		$field_ids = array();
		
		foreach($_POST as $key => $value)
		{
			$m = null;
			
			if(!preg_match('/^enable_filter_custom_field_(\d+)/', $key, $m))
				continue;
			
			$field_ids[] = (int)$m[1];
		}

		wpgmza_require_once(plugin_dir_path(__FILE__) . 'includes/custom-fields/class.custom-field-filter.php');
		WPGMZA\CustomFieldFilter::setEnabledFilters($map_id, $field_ids);
        
        if (isset($_POST['wpgmza_store_locator'])) { $other_settings['store_locator_enabled'] = isset($_POST['wpgmza_store_locator']) ? 1 : 2; }
        if (isset($_POST['wpgmza_store_locator_restrict'])) { $other_settings['wpgmza_store_locator_restrict'] = esc_attr($_POST['wpgmza_store_locator_restrict']); }
        if (isset($_POST['wpgmza_sl_animation'])) { $other_settings['wpgmza_sl_animation'] = esc_attr($_POST['wpgmza_sl_animation']); }
        if (isset($_POST['wpgmza_store_locator_distance'])) { $other_settings['store_locator_distance'] = isset($_POST['wpgmza_store_locator_distance']) ? 1 : 2; }
        if (isset($_POST['wpgmza_store_locator_position'])) { $other_settings['store_locator_below'] = isset($_POST['wpgmza_store_locator_position']) ? 1 : 2; }
        if (isset($_POST['wpgmza_store_locator_bounce'])) { $other_settings['store_locator_bounce'] = isset($_POST['wpgmza_store_locator_bounce']) ? 1 : 2; }
        if (isset($_POST['wpgmza_store_locator_hide_before_search'])) { $other_settings['store_locator_hide_before_search'] = isset($_POST['wpgmza_store_locator_hide_before_search']) ? 1 : 2; }
        if (isset($_POST['wpgmza_store_locator_use_their_location'])) { $other_settings['store_locator_use_their_location'] = isset($_POST['wpgmza_store_locator_use_their_location']) ? 1 : 2; }
        if (isset($_POST['wpgmza_store_locator_name_search'])) { $other_settings['store_locator_name_search'] = isset($_POST['wpgmza_store_locator_name_search']) ? 1 : 2; }
        if (isset($_POST['wpgmza_store_locator_category_enabled'])) { $other_settings['store_locator_category'] = isset($_POST['wpgmza_store_locator_category_enabled']) ? 1 : 2; }
        if (isset($_POST['wpgmza_store_locator_query_string'])) { $other_settings['store_locator_query_string'] = esc_attr($_POST['wpgmza_store_locator_query_string']); }
        if (isset($_POST['wpgmza_store_locator_name_string'])) { $other_settings['store_locator_name_string'] = esc_attr($_POST['wpgmza_store_locator_name_string']); }
        if (isset($_POST['wpgmza_store_locator_default_address'])) { $other_settings['store_locator_default_address'] = sanitize_text_field($_POST['wpgmza_store_locator_default_address']); }
        if (isset($_POST['wpgmza_store_locator_default_radius'])) { $other_settings['store_locator_default_radius'] = sanitize_text_field($_POST['wpgmza_store_locator_default_radius']); }
	    if (isset($_POST['wpgmza_store_locator_not_found_message'])) { $other_settings['store_locator_not_found_message'] = sanitize_text_field( $_POST['wpgmza_store_locator_not_found_message'] ); }

		$other_settings['store_locator_style'] = (!empty($_POST['store_locator_style']) ? $_POST['store_locator_style'] : 'legacy');
		$other_settings['wpgmza_store_locator_radius_style'] = (!empty($_POST['wpgmza_store_locator_radius_style']) ? $_POST['wpgmza_store_locator_radius_style'] : 'legacy');
		
		if(isset($_POST['store_locator_search_area']))
			$other_settings['store_locator_search_area'] = $_POST['store_locator_search_area'];
		
		if(isset($_POST['wpgmza_directions_box_style']))
			$other_settings['directions_box_style'] = $_POST['wpgmza_directions_box_style'];
		else
			$other_settings['directions_box_style'] = 'modern';

        if (isset($_POST['wpgmza_marker_listing_position'])) { $other_settings['store_marker_listing_below'] = isset($_POST['wpgmza_marker_listing_position']) ? 1 : 2; }

        $other_settings['show_distance_from_location'] = isset($_POST['wpgmza_show_distance_from_location']) ? 1 : 0;


        $map_max_zoom = intval($_POST['wpgmza_max_zoom']);
        $other_settings['map_max_zoom'] = sanitize_text_field($map_max_zoom);
		
		if(isset($_POST['wpgmza_override_users_location_zoom_levels']))
		{
			$override_users_location_zoom_levels = intval($_POST['wpgmza_override_users_location_zoom_levels']);
			$other_settings['override_users_location_zoom_levels'] = sanitize_text_field($override_users_location_zoom_levels);
		}

        $map_min_zoom = intval($_POST['wpgmza_min_zoom']);
        $other_settings['map_min_zoom'] = sanitize_text_field($map_min_zoom);
		$other_settings['sl_stroke_color'] 		= (empty($_POST['sl_stroke_color'])		? '' : $_POST['sl_stroke_color']);
		$other_settings['sl_stroke_opacity'] 	= (!isset($_POST['sl_stroke_opacity'])	? '' : $_POST['sl_stroke_opacity']);
		$other_settings['sl_fill_color']		= (empty($_POST['sl_fill_color'])		? '' : $_POST['sl_fill_color']);
		$other_settings['sl_fill_opacity'] 		= (!isset($_POST['sl_fill_opacity'])		? '' : $_POST['sl_fill_opacity']);
		
        $other_settings['jump_to_nearest_marker_on_initialization'] = isset($_POST['wpgmza_jump_to_nearest_marker_on_initialization']) ? 1 : 0;
        $other_settings['automatically_pan_to_users_location'] = isset($_POST['wpgmza_automatically_pan_to_users_location']) ? 1 : 0;
        $other_settings['override_users_location_zoom_level'] = isset($_POST['wpgmza_override_users_location_zoom_level']) ? 1 : 0;
        
        $other_settings['click_open_link'] = isset($_POST['wpgmza_click_open_link']) ? 1 : 2;
        $other_settings['hide_point_of_interest'] = isset($_POST['wpgmza_hide_point_of_interest']) ? 1 : 2;
        $other_settings['fit_maps_bounds_to_markers'] = isset($_POST['wpgmza_fit_maps_bounds_to_markers']) ? 1 : 2;
        $other_settings['fit_maps_bounds_to_markers_after_filtering'] = isset($_POST['wpgmza_fit_maps_bounds_to_markers_after_filtering']) ? 1 : 2;
        $other_settings['wpgmza_auto_night'] = $wpgmza_auto_night_enabled ? 1 : 0;
		
        //$other_settings['weather_layer'] = intval($_POST['wpgmza_weather']);
        //$other_settings['weather_layer_temp_type'] = intval($_POST['wpgmza_weather_temp_type']);
        //$other_settings['cloud_layer'] = intval($_POST['wpgmza_cloud']);
        $other_settings['transport_layer'] = isset($_POST['wpgmza_transport']) ? 1 : 2;
		$other_settings['polygon_labels'] = isset($_POST['polygon_labels']);
		$other_settings['enable_marker_ratings'] = isset($_POST['enable_marker_ratings']);

        $other_settings['iw_primary_color'] = $_POST['iw_primary_color'];
        $other_settings['iw_accent_color'] = $_POST['iw_accent_color'];
        $other_settings['iw_text_color'] = $_POST['iw_text_color'];

        if (isset($_POST['wpgmza_iw_type'])) { $other_settings['wpgmza_iw_type'] = $_POST['wpgmza_iw_type']; } else { $other_settings['wpgmza_iw_type'] = "0"; }

        
        if (isset($_POST['wpgmza_listmarkers_by'])) { $other_settings['list_markers_by'] = $_POST['wpgmza_listmarkers_by']; } else { $other_settings['list_markers_by'] = ""; }
        if (isset($_POST['wpgmza_push_in_map'])) { $other_settings['push_in_map'] = $_POST['wpgmza_push_in_map']; } else { $other_settings['push_in_map'] = ""; }
        if (isset($_POST['wpgmza_push_in_map_placement'])) { $other_settings['push_in_map_placement'] = $_POST['wpgmza_push_in_map_placement']; } else { $other_settings['push_in_map_placement'] = ""; }
        if (isset($_POST['wpgmza_push_in_map_width'])) { $other_settings['wpgmza_push_in_map_width'] = esc_attr($_POST['wpgmza_push_in_map_width']); }
        if (isset($_POST['wpgmza_push_in_map_height'])) { $other_settings['wpgmza_push_in_map_height'] = esc_attr($_POST['wpgmza_push_in_map_height']); }
		
		if(isset($_POST['wpgmza_theme_data']))
			$other_settings['wpgmza_theme_data'] = sanitize_text_field(stripslashes($_POST['wpgmza_theme_data']));

        $map_default_ul_marker = str_replace('http:', '', $_POST['upload_default_ul_marker']);
        $other_settings['upload_default_ul_marker'] = $map_default_ul_marker;

        $map_default_sl_marker = str_replace('http:', '', (empty($_POST['upload_default_sl_marker']) ? '' : $_POST['upload_default_sl_marker']));
        $other_settings['upload_default_sl_marker'] = $map_default_sl_marker;
        

        $other_settings = apply_filters("wpgmza_pro_filter_save_map_other_settings",$other_settings);

        $other_settings_data = maybe_serialize($other_settings);

        $gps = explode(",",$map_start_location);
        $map_start_lat = $gps[0];
        $map_start_lng = $gps[1];
		
        $map_default_marker = str_replace('http:', '', $_POST['upload_default_marker']);
		
		$mapDefaultMarkerIcon = new WPGMZA\MarkerIcon(array(
			'url'		=> $map_default_marker,
			'retina'	=> isset($_POST['upload_default_marker_retina']) ? 1 : 0
		));
		
		if($mapDefaultMarkerIcon->isDefault)
			$mapDefaultMarkerIcon = "";
		else
			$mapDefaultMarkerIcon = json_encode($mapDefaultMarkerIcon);
		
		if(isset($_POST['wpgmza_kml']))
			$kml = esc_attr($_POST['wpgmza_kml']);
		else
			$kml = '';
		
		if(isset($_POST['wpgmza_fusion']))
			$fusion = esc_attr($_POST['wpgmza_fusion']);
		else
			$fusion = '';

        $data['map_default_starting_lat'] = $map_start_lat;
        $data['map_default_starting_lng'] = $map_start_lng;
        $data['map_default_height'] = $map_height;
        $data['map_default_width'] = $map_width;
        $data['map_default_zoom'] = $map_start_zoom;
        $data['map_default_max_zoom'] = $map_max_zoom;
        $data['map_default_min_zoom'] = $map_min_zoom;
        $data['map_default_type'] = $type;
        $data['map_default_alignment'] = $alignment;
        $data['map_default_order_markers_by'] = $order_markers_by;
        $data['map_default_order_markers_choice'] = $order_markers_choice;
        $data['map_default_show_user_location'] = $show_user_location;
        $data['map_default_directions'] = $directions_enabled;
        $data['map_default_bicycle'] = $bicycle_enabled;
        $data['map_default_traffic'] = $traffic_enabled;
        $data['map_default_listmarkers'] = $listmarkers;
        $data['map_default_listmarkers_advanced'] = $listmarkers_advanced;
        $data['map_default_filterbycat'] = $filterbycat;
        $data['map_default_marker'] = $map_default_marker;
        $data['map_default_ul_marker'] = $map_default_ul_marker;
        $data['map_default_sl_marker'] = $map_default_sl_marker;
        $data['map_default_width_type'] = $map_width_type;
        $data['map_default_height_type'] = $map_height_type;





        $rows_affected = $wpdb->query( $wpdb->prepare(
                "UPDATE $wpgmza_tblname_maps SET
                map_title = %s,
                map_width = %s,
                map_height = %s,
                map_start_lat = %f,
                map_start_lng = %f,
                map_start_location = %s,
                map_start_zoom = %d,
                default_marker = %s,
                type = %d,
                alignment = %d,
                order_markers_by = %d,
                order_markers_choice = %d,
                show_user_location = %d,
                directions_enabled = %d,
                kml = %s,
                bicycle = %d,
                traffic = %d,
                listmarkers = %d,
                listmarkers_advanced = %d,
                filterbycat = %d,
                fusion = %s,
                map_width_type = %s,
                map_height_type = %s,
                other_settings = %s
                WHERE id = %d",

                $map_title,
                $map_width,
                $map_height,
                $map_start_lat,
                $map_start_lng,
                $map_start_location,
                $map_start_zoom,
                $mapDefaultMarkerIcon,
                $type,
                $alignment,
                $order_markers_by,
                $order_markers_choice,
                $show_user_location,
                $directions_enabled,
                $kml,
                $bicycle_enabled,
                $traffic_enabled,
                $listmarkers,
                $listmarkers_advanced,
                $filterbycat,
                $fusion,
                $map_width_type,
                $map_height_type,
                $other_settings_data,
                $map_id)
        );

        //echo $wpdb->print_error();


        update_option('WPGMZA_SETTINGS', $data);
		
		// Legacy action
        do_action("wpgooglemaps_hook_save_map",$map_id);
		
		// Real action
		$map = WPGMZA\Map::createInstance($map_id);
		
		if(isset($_POST['store_locator_auto_area_max_zoom']))
			$map->store_locator_auto_area_max_zoom = $_POST['store_locator_auto_area_max_zoom'];
		
		do_action('wpgmza_map_saved', $map);
		do_action('clear_rest_cache', 'wpgmza');
		
        echo "<div class='updated'>";
        _e("Your settings have been saved.","wp-google-maps");
        echo "</div>";

        if( function_exists( 'wpgmza_caching_notice_changes' ) ){
        	add_action( 'admin_notices', 'wpgmza_caching_notice_changes' );
        }

    }

    else if (isset($_POST['wpgmza_save_maker_location'])){
        
		$marker 			= \WPGMZA\Marker::createInstance($_POST['wpgmaps_marker_id']);
		$latlng				= new \WPGMZA\LatLng($_POST['wpgmaps_marker_lat'], $_POST['wpgmaps_marker_lng']);
		
		if(preg_match(\WPGMZA\LatLng::REGEXP, $marker->address))
		{
			$currentAddressPosition = new \WPGMZA\LatLng($marker->address);
			$distance				= \WPGMZA\Distance::between($currentAddressPosition, $marker->getPosition());
			$meters					= $distance / 1000;
			
			if($meters < 1)
			{
				// The marker has an address which looks like coordinates, and they're very close to the markers latitude and longitude
				// Therefore, it would seem that the user has placed this with coordinates and is now looking to move those coordinates here
				// Because of this, we'll update the address with the new coordinates
				$marker->address	= (string)$latlng;
			}
		}
		
		$lat				=  $_POST['wpgmaps_marker_lat'];
		$lng				=  $_POST['wpgmaps_marker_lng'];
		
		$marker->lat		= $lat;
		$marker->lng		= $lng;
		
        echo "<div class='updated'>";
        _e("Your marker location has been saved.","wp-google-maps");
        echo "</div>";


    }
    else if (isset($_POST['wpgmza_save_poly'])){
        global $wpdb;
        global $wpgmza_tblname_poly;
         if (!isset($_POST['wpgmza_polygon']) || $_POST['wpgmza_polygon'] == "") {
            echo "<div class='error'>";
            _e("You cannot save a blank polygon","wp-google-maps");
            echo "</div>";
            
        } else {
        
	        $mid = esc_attr($_POST['wpgmaps_map_id']);
	        $wpgmaps_polydata = esc_attr($_POST['wpgmza_polygon']);
	        $linecolor = esc_attr($_POST['poly_line']);
	        $fillcolor = esc_attr($_POST['poly_fill']);
	        $polyname = esc_attr($_POST['poly_name']);
	        $description = esc_attr($_POST['poly_description']);
	        $line_opacity = esc_attr($_POST['poly_line_opacity']);
	        if (!isset ($line_opacity) || $line_opacity == "" ) { $line_opacity = "1"; }
	        $opacity = esc_attr($_POST['poly_opacity']);
	        $ohlinecolor = esc_attr($_POST['poly_line_hover_line_color']);
	        $ohfillcolor = esc_attr($_POST['poly_hover_fill_color']);
	        $ohopacity = esc_attr($_POST['poly_hover_opacity']);
	        $title = esc_attr($_POST['poly_title']);
	        $link = esc_attr($_POST['poly_link']);

	        $rows_affected = $wpdb->query( $wpdb->prepare(
	                "INSERT INTO $wpgmza_tblname_poly SET
	                map_id = %d,
	                polydata = %s,
	                polyname = %s,
	                description = %s,
	                linecolor = %s,
	                lineopacity = %s,
	                fillcolor = %s,
	                opacity = %s,
	                ohlinecolor = %s,
	                ohfillcolor = %s,
	                ohopacity = %s,
	                title = %s,
	                link = %s
	                ",

	                $mid,
	                $wpgmaps_polydata,
	                $polyname,
	                $description,
	                $linecolor,
	                $line_opacity,
	                $fillcolor,
	                $opacity,
	                $ohlinecolor,
	                $ohfillcolor,
	                $ohopacity,
	                $title,
	                $link
	            )
	        );

	        echo "<div class='updated'>";
	        _e("Your polygon has been created.","wp-google-maps");
	        echo "</div>";
	    }


    }
    else if (isset($_POST['wpgmza_edit_poly'])){
        global $wpdb;
        global $wpgmza_tblname_poly;
        
        if (!isset($_POST['wpgmza_polygon']) || $_POST['wpgmza_polygon'] == "") {
            echo "<div class='error'>";
            _e("You cannot save a blank polygon","wp-google-maps");
            echo "</div>";
    
        } else {
        
	        $mid = esc_attr($_POST['wpgmaps_map_id']);
	        $pid = esc_attr($_POST['wpgmaps_poly_id']);
	        $wpgmaps_polydata = esc_attr($_POST['wpgmza_polygon']);
	        
	        
	        $polyname = esc_attr($_POST['poly_name']);
	        $description = esc_attr($_POST['poly_description']);
	        $linecolor = esc_attr($_POST['poly_line']);
	        $fillcolor = esc_attr($_POST['poly_fill']);
	        $line_opacity = esc_attr($_POST['poly_line_opacity']);
	        if (!isset ($line_opacity) || $line_opacity == "" ) { $line_opacity = "1"; }
	        $opacity = esc_attr($_POST['poly_opacity']);
	        $ohlinecolor = esc_attr($_POST['poly_line_hover_line_color']);
	        $ohfillcolor = esc_attr($_POST['poly_hover_fill_color']);
	        $ohopacity = esc_attr($_POST['poly_hover_opacity']);
	        $title = esc_attr($_POST['poly_title']);
	        $link = esc_attr($_POST['poly_link']);

	        $rows_affected = $wpdb->query( $wpdb->prepare(
	                "UPDATE $wpgmza_tblname_poly SET
	                polydata = %s,
	                polyname = %s,
	                description = %s,
	                linecolor = %s,
	                lineopacity = %s,
	                fillcolor = %s,
	                opacity = %s,
	                ohlinecolor = %s,
	                ohfillcolor = %s,
	                ohopacity = %s,
	                title = %s,
	                link = %s
	                WHERE `id` = %d"
	                ,

	                $wpgmaps_polydata,
	                $polyname,
	                $description,
	                $linecolor,
	                $line_opacity,
	                $fillcolor,
	                $opacity,
	                $ohlinecolor,
	                $ohfillcolor,
	                $ohopacity,
	                $title,
	                $link,
	                $pid
	            )
	        );
	        
	        echo "<div class='updated'>";
	        _e("Your polygon has been saved.","wp-google-maps");
	        echo "</div>";
	    }


    }
    else if (isset($_POST['wpgmza_save_polyline'])){
        global $wpdb;
        global $wpgmza_tblname_polylines;
        if (!isset($_POST['wpgmza_polyline']) || $_POST['wpgmza_polyline'] == "") {
            echo "<div class='error'>";
            _e("You cannot save a blank polyline","wp-google-maps");
            echo "</div>";
    
        } else {        
	        $mid = esc_attr($_POST['wpgmaps_map_id']);
	        $wpgmaps_polydata = esc_attr($_POST['wpgmza_polyline']);
	        $polyname = esc_attr($_POST['poly_name']);
	        $linecolor = esc_attr($_POST['poly_line']);
	        $linethickness = esc_attr($_POST['poly_thickness']);
	        $opacity = esc_attr($_POST['poly_opacity']);

	        $rows_affected = $wpdb->query( $wpdb->prepare(
	                "INSERT INTO $wpgmza_tblname_polylines SET
	                map_id = %d,
	                polydata = %s,
	                polyname = %s,
	                linecolor = %s,
	                linethickness = %s,
	                opacity = %s
	                ",

	                $mid,
	                $wpgmaps_polydata,
	                $polyname,
	                $linecolor,
	                $linethickness,
	                $opacity
	            )
	        );
	        echo "<div class='updated'>";
	        _e("Your polyline has been created.","wp-google-maps");
	        echo "</div>";
	    }


    }
    else if (isset($_POST['wpgmza_edit_polyline'])){
        global $wpdb;
        global $wpgmza_tblname_polylines;
        if (!isset($_POST['wpgmza_polyline']) || $_POST['wpgmza_polyline'] == "") {
            echo "<div class='error'>";
            _e("You cannot save a blank polyline","wp-google-maps");
            echo "</div>";
    
        } else {        
	        $mid = esc_attr($_POST['wpgmaps_map_id']);
	        $pid = esc_attr($_POST['wpgmaps_poly_id']);
	        $polyname = esc_attr($_POST['poly_name']);
	        $wpgmaps_polydata = esc_attr($_POST['wpgmza_polyline']);
	        $linecolor = esc_attr($_POST['poly_line']);
	        $linethickness = esc_attr($_POST['poly_thickness']);
	        $opacity = esc_attr($_POST['poly_opacity']);

	        $rows_affected = $wpdb->query( $wpdb->prepare(
	                "UPDATE $wpgmza_tblname_polylines SET
	                polydata = %s,
	                polyname = %s,
	                linecolor = %s,
	                linethickness = %s,
	                opacity = %s
	                WHERE `id` = %d"
	                ,

	                $wpgmaps_polydata,
	                $polyname,
	                $linecolor,
	                $linethickness,
	                $opacity,
	                $pid
	            )
	        );
	        echo "<div class='updated'>";
	        _e("Your polyline has been saved.","wp-google-maps");
	        echo "</div>";
	    }


    }
    else if (isset($_POST['wpgmza_save_heatmap'])){
        global $wpdb;
        global $wpgmza_tblname_datasets;
        
        $mid = esc_attr($_POST['wpgmaps_map_id']);
        $wpgmaps_polydata = esc_attr($_POST['wpgmza_heatmap_data']);
        $wpgmaps_polydata = esc_attr($_POST['wpgmza_heatmap_data']);

        $wpgmaps_polydata = trim($wpgmaps_polydata);
        $wpgmaps_polydata = str_replace("\r\n", "", $wpgmaps_polydata); // windows -> unix
		$wpgmaps_polydata = str_replace("\r", "", $wpgmaps_polydata);   // remaining -> unix


		$dataset_option = array(
			'poly_name' => esc_attr($_POST['poly_name']),
			'heatmap_opacity' => esc_attr($_POST['heatmap_opacity']),
			'heatmap_radius' => esc_attr($_POST['heatmap_radius']),
			'heatmap_gradient' => esc_attr($_POST['heatmap_gradient'])
		);

        $polyname = esc_attr($_POST['poly_name']);
        $rows_affected = $wpdb->query( $wpdb->prepare(
                "INSERT INTO $wpgmza_tblname_datasets SET
                `map_id` = %d,
                `dataset` = %s,
                `dataset_name` = %s,
                `options` = %s
                ",

                $mid,
                $wpgmaps_polydata,
                $polyname,
                maybe_serialize($dataset_option)

        )
        );

        echo "<div class='updated'>";
        _e("Your dataset has been created.","wp-google-maps");
        echo "</div>";


    }
    else if (isset($_POST['wpgmza_edit_heatmap'])){
        global $wpdb;
        global $wpgmza_tblname_datasets;
        
        
        
        $mid = esc_attr($_POST['wpgmaps_map_id']);
        $pid = esc_attr($_POST['wpgmaps_poly_id']);
        $wpgmaps_polydata = esc_attr($_POST['wpgmza_heatmap_data']);
        $wpgmaps_polydata = trim($wpgmaps_polydata);
        $wpgmaps_polydata = str_replace("\r\n", "", $wpgmaps_polydata); // windows -> unix
		$wpgmaps_polydata = str_replace("\r", "", $wpgmaps_polydata);   // remaining -> unix

		$dataset_option = array(
			'poly_name' => esc_attr($_POST['poly_name']),
			'heatmap_opacity' => esc_attr($_POST['heatmap_opacity']),
			'heatmap_radius' => esc_attr($_POST['heatmap_radius']),
			'heatmap_gradient' => esc_attr($_POST['heatmap_gradient'])
		);

        

        $polyname = esc_attr($_POST['poly_name']);

        $rows_affected = $wpdb->query( $wpdb->prepare(
                "UPDATE $wpgmza_tblname_datasets SET
                `dataset` = %s,
                `dataset_name` = %s,
                `options` = %s
                WHERE `id` = %d"
                ,

                trim($wpgmaps_polydata),
                $polyname,
                maybe_serialize($dataset_option),
                $pid
            )
        );
        
        echo "<div class='updated'>";
        _e("Your dataset has been saved.","wp-google-maps");
        echo "</div>";


    }
	else if (isset($_POST['wpgmza_save_circle'])){
        global $wpdb;
		global $wpgmza;
        global $wpgmza_tblname_circles;
        
		$center = preg_replace('/[(),]/', '', $_POST['center']);
		
		if(isset($_POST['circle_id']))
		{
			$stmt = $wpdb->prepare("
				UPDATE $wpgmza_tblname_circles SET
				center = {$wpgmza->spatialFunctionPrefix}GeomFromText(%s),
				name = %s,
				color = %s,
				opacity = %f,
				radius = %f
				WHERE id = %d
			", array(
				"POINT($center)",
				$_POST['circle_name'],
				$_POST['circle_color'],
				$_POST['circle_opacity'],
				$_POST['circle_radius'],
				$_POST['circle_id']
			));
		}
		else
		{
			$stmt = $wpdb->prepare("
				INSERT INTO $wpgmza_tblname_circles
				(center, map_id, name, color, opacity, radius)
				VALUES
				({$wpgmza->spatialFunctionPrefix}GeomFromText(%s), %d, %s, %s, %f, %f)
			", array(
				"POINT($center)",
				$_POST['wpgmaps_map_id'],
				$_POST['circle_name'],
				$_POST['circle_color'],
				$_POST['circle_opacity'],
				$_POST['circle_radius']
			));
		}
		
		$wpdb->query($stmt);
		
		?>
		<script type='text/javascript'>
		
		jQuery(document).ready(function() {
			window.location.reload();
		});
		
		</script>
		<?php
		
    }
	else if (isset($_POST['wpgmza_save_rectangle'])){
        global $wpdb;
		global $wpgmza;
        global $wpgmza_tblname_rectangles;
        
		$m = null;
		preg_match_all('/-?\d+(\.\d+)?/', $_POST['bounds'], $m);
		
		$north = $m[0][0];
		$east = $m[0][1];
		$south = $m[0][2];
		$west = $m[0][3];
		
		$cornerA = "POINT($north $east)";
		$cornerB = "POINT($south $west)";
		
		if(isset($_POST['rectangle_id']))
		{
			$stmt = $wpdb->prepare("
				UPDATE $wpgmza_tblname_rectangles SET
				name = %s,
				color = %s,
				opacity = %f,
				cornerA = {$wpgmza->spatialFunctionPrefix}GeomFromText(%s),
				cornerB = {$wpgmza->spatialFunctionPrefix}GeomFromText(%s)
				WHERE id = %d
			", array(
				$_POST['rectangle_name'],
				$_POST['rectangle_color'],
				$_POST['rectangle_opacity'],
				$cornerA,
				$cornerB,
				$_POST['rectangle_id']
			));
		}
		else
		{
			$stmt = $wpdb->prepare("
				INSERT INTO $wpgmza_tblname_rectangles
				(map_id, name, color, opacity, cornerA, cornerB)
				VALUES
				(%d, %s, %s, %f, {$wpgmza->spatialFunctionPrefix}GeomFromText(%s), {$wpgmza->spatialFunctionPrefix}GeomFromText(%s))
			", array(
				$_POST['wpgmaps_map_id'],
				$_POST['rectangle_name'],
				$_POST['rectangle_color'],
				$_POST['rectangle_opacity'],
				$cornerA,
				$cornerB
			));
		}
		
		$rows = $wpdb->query($stmt);
		
		?>
		<script type='text/javascript'>
		
		jQuery(document).ready(function() {
			window.location.reload();
		});
		
		</script>
		<?php
    }



}


function wpgmza_b_real_pro_add_poly($mid) {
    global $wpgmza_tblname_maps;
    global $wpdb;
    if ($_GET['action'] == "add_poly" && isset($mid)) {
        $res = wpgmza_get_map_data($mid);
        echo "
            

            
          
           <div class='wrap'>
                <h1>WP Google Maps</h1>
                <div class='wide'>

                    <h2>".__("Add a Polygon","wp-google-maps")."</h2>
                    <form action='?page=wp-google-maps-menu&action=edit&map_id=".$mid."' method='post' id='wpgmaps_add_poly_form'>
                    <input type='hidden' name='wpgmaps_map_id' id='wpgmaps_map_id' value='".$mid."' />
                    
                    <table class='wpgmza-listing-comp' style='width:30%;float:left;'>
                    <tr>
                        <td>".__("Name","wp-google-maps")."</td><td><input id=\"poly_name\" name=\"poly_name\" type=\"text\" value=\"\" /></td>
                    </tr>
                    <tr>
                        <td>".__("Title","wp-google-maps")."</td><td><input id=\"poly_title\" name=\"poly_title\" type=\"text\" value=\"\" /></td>
                    </tr>
                    <tr>
                        <td>".__("Description","wp-google-maps")."</td><td><textarea id=\"poly_description\" name=\"poly_description\"value=\"\"></textarea></td>
                    </tr>
                    <tr>
                        <td>".__("Link","wp-google-maps")."</td><td><input id=\"poly_link\" name=\"poly_link\" type=\"text\" value=\"\" /></td> 
                    </tr>
                    <tr>
                        <td>".__("Line Color","wp-google-maps")."</td><td><input id=\"poly_line\" name=\"poly_line\" type=\"text\" class=\"color\" value=\"000000\" /></td>   
                    </tr>
                    <tr>
                        <td>".__("Line Opacity","wp-google-maps")."</td><td><input id=\"poly_line_opacity\" name=\"poly_line_opacity\" type=\"text\" value=\"0.5\" /> (0 - 1.0) example: 0.5 for 50%</td>   
                    </tr>
                    <tr>
                        <td>".__("Fill Color","wp-google-maps")."</td><td><input id=\"poly_fill\" name=\"poly_fill\" type=\"text\" class=\"color\" value=\"66ff00\" /></td>  
                    </tr>
                    <tr>
                        <td>".__("Opacity","wp-google-maps")."</td><td><input id=\"poly_opacity\" name=\"poly_opacity\" type=\"text\" value=\"0.5\" /> (0 - 1.0) example: 0.5 for 50%</td>   
                    </tr>
                    <tr>
                        <td>".__("On Hover Line Color","wp-google-maps")."</td><td><input id=\"poly_line_hover_line_color\" name=\"poly_line_hover_line_color\" class=\"color\" type=\"text\" value=\"737373\" /></td>   
                    </tr>
                    <tr>
                        <td>".__("On Hover Fill Color","wp-google-maps")."</td><td><input id=\"poly_hover_fill_color\" name=\"poly_hover_fill_color\" type=\"text\" class=\"color\" value=\"57FF78\" /></td>  
                    </tr>
                    <tr>
                        <td>".__("On Hover Opacity","wp-google-maps")."</td><td><input id=\"poly_hover_opacity\" name=\"poly_hover_opacity\" type=\"text\" value=\"0.7\" /> (0 - 1.0) example: 0.5 for 50%</td>   
                    </tr>
                        
                    </table>

                    <div class='wpgmza_map_seventy'> 
	                    <div id=\"wpgmza_map\">&nbsp;</div>
	                    <p>
	                            <ul style=\"list-style:initial;\" class='update-nag update-blue update-slim update-map-overlay'>
	                                <li style=\"margin-left:30px;\">".__("Click on the map to insert a vertex.","wp-google-maps")."</li>
	                                <li style=\"margin-left:30px;\">".__("Click on a vertex to remove it.","wp-google-maps")."</li>
	                                <li style=\"margin-left:30px;\">".__("Drag a vertex to move it.","wp-google-maps")."</li>
	                            </ul>
	                    </p>
                    </div>
                    

                    <div class='clear'></div>
                     <p>Polygon data:<br /><textarea name=\"wpgmza_polygon\" id=\"poly_line_list\" style=\"height:100px; background-color:#FFF; padding:5px; overflow:auto;\"></textarea>
                    <p class='submit'><a href='javascript:history.back();' class='button button-secondary' title='".__("Cancel")."'>".__("Cancel")."</a> <input type='submit' name='wpgmza_save_poly' class='button-primary' value='".__("Save Polygon","wp-google-maps")." &raquo;' /></p>

                    </form>
                </div>


            </div>



        ";

    }



}

function wpgmza_b_real_pro_edit_poly($mid) {
    global $wpgmza_tblname_maps;
    global $wpdb;
	
	//wpgmza_enqueue_fontawesome();
	
    if ($_GET['action'] == "edit_poly" && isset($mid)) {
        $res = wpgmza_get_map_data($mid);
        $pol = wpgmza_b_return_poly_options(sanitize_text_field($_GET['poly_id']));
echo "
            

            
          
           <div class='wrap'>
                <h1>WP Google Maps</h1>
                <div class='wide'>

                    <h2>".__("Add a Polygon","wp-google-maps")."</h2>
                    <form action='?page=wp-google-maps-menu&action=edit&map_id=".$mid."' method='post' id='wpgmaps_edit_poly_form'>
                    <input type='hidden' name='wpgmaps_map_id' id='wpgmaps_map_id' value='".$mid."' />
                    <input type='hidden' name='wpgmaps_poly_id' id='wpgmaps_poly_id' value='".$_GET['poly_id']."' />
                    
                    <table class='wpgmza-listing-comp' style='width:30%;float:left;'>
                    <tr>
                        <td>".__("Name","wp-google-maps")."</td><td><input id=\"poly_name\" name=\"poly_name\" type=\"text\" value=\"".stripslashes($pol->polyname)."\" /></td>
                    </tr>
                    <tr>
                        <td>".__("Title","wp-google-maps")."</td><td><input id=\"poly_title\" name=\"poly_title\" type=\"text\" value=\"".stripslashes($pol->title)."\" /></td>
                    </tr>
                    <tr>
                        <td>".__("Description","wp-google-maps")."</td><td><textarea id=\"poly_description\" name=\"poly_description\" />".stripslashes($pol->description)."</textarea></td>
                    </tr>
                    <tr>
                        <td>".__("Link","wp-google-maps")."</td><td><input id=\"poly_link\" name=\"poly_link\" type=\"text\" value=\"".$pol->link."\" /></td> 
                    </tr>
                    <tr>
                        <td>".__("Line Color","wp-google-maps")."</td><td><input id=\"poly_line\" name=\"poly_line\" type=\"text\" class=\"color\" value=\"".$pol->linecolor."\" /></td>   
                    </tr>
                    <tr>
                        <td>".__("Line Opacity","wp-google-maps")."</td><td><input id=\"poly_line_opacity\" name=\"poly_line_opacity\" type=\"text\" value=\"".$pol->lineopacity."\" /> (0 - 1.0) example: 0.5 for 50%</td>   
                    </tr>
                    <tr>
                        <td>".__("Fill Color","wp-google-maps")."</td><td><input id=\"poly_fill\" name=\"poly_fill\" type=\"text\" class=\"color\" value=\"".$pol->fillcolor."\" /></td>  
                    </tr>
                    <tr>
                        <td>".__("Opacity","wp-google-maps")."</td><td><input id=\"poly_opacity\" name=\"poly_opacity\" type=\"text\" value=\"".$pol->opacity."\" /> (0 - 1.0) example: 0.5 for 50%</td>   
                    </tr>
                    <tr>
                        <td>".__("On Hover Line Color","wp-google-maps")."</td><td><input id=\"poly_line_hover_line_color\" name=\"poly_line_hover_line_color\" class=\"color\" type=\"text\" value=\"".$pol->ohlinecolor."\" /></td>   
                    </tr>
                    <tr>
                        <td>".__("On Hover Fill Color","wp-google-maps")."</td><td><input id=\"poly_hover_fill_color\" name=\"poly_hover_fill_color\" type=\"text\" class=\"color\" value=\"".$pol->ohfillcolor."\" /></td>  
                    </tr>
                    <tr>
                        <td>".__("On Hover Opacity","wp-google-maps")."</td><td><input id=\"poly_hover_opacity\" name=\"poly_hover_opacity\" type=\"text\" value=\"".$pol->ohopacity."\" /> (0 - 1.0) example: 0.5 for 50%</td>   
                    </tr>
                        
					<tr>
						<td>".__('Show Polygon', 'wp-google-maps')."</td>
						<td>
							<button id='fit-bounds-to-shape' 
								class='button button-secondary' 
								type='button' 
								title='" . __('Fit map bounds to shape', 'wp-google-maps') . "'
								data-fit-bounds-to-shape='poly'>
								<i class='fas fa-eye'></i>
							</button>
						</td>
					</tr>
						
                    </table>
                     <div class='wpgmza_map_seventy'> 
                        <div id=\"wpgmza_map\">&nbsp;</div>
		                    <p>
		                            <ul style=\"list-style:initial;\" class='update-nag update-blue update-slim update-map-overlay'>
		                                <li style=\"margin-left:30px;\">".__("Click on the map to insert a vertex.","wp-google-maps")."</li>
		                                <li style=\"margin-left:30px;\">".__("Click on a vertex to remove it.","wp-google-maps")."</li>
		                                <li style=\"margin-left:30px;\">".__("Drag a vertex to move it.","wp-google-maps")."</li>
		                            </ul>
		                    </p>
                     </div>
                    

                     <div class='clear'></div>
                     <p>Polygon data:<br /><textarea name=\"wpgmza_polygon\" id=\"poly_line_list\" style=\"height:100px; width:100%; background-color:#FFF; padding:5px; overflow:auto;\">".$pol->polydata."</textarea>
                    <p class='submit'><a href='javascript:history.back();' class='button button-secondary' title='".__("Cancel")."'>".__("Cancel")."</a> <input type='submit' name='wpgmza_edit_poly' class='button-primary' value='".__("Save Polygon","wp-google-maps")." &raquo;' /></p>

                    </form>
                </div>


            </div>



        ";

    }



}

add_action('admin_print_scripts', 'wpgmaps_admin_scripts_pro');
add_action('admin_print_styles', 'wpgmaps_admin_styles_pro');


function wpgmaps_admin_scripts_pro() {
    
	global $wpgmza;
	
	if(!$wpgmza)
		return; // Bail out, running an older (incompatible) version of Basic
	
	$wpgmza_lang_strings = array(
		"wpgm_mlist_sel_1" =>__("Carousel","wp-google-maps"),
		"wpgm_mlist_sel_2" => __("No marker listing","wp-google-maps"),
		"wpgm_mlist_sel_3" => __("Basic list","wp-google-maps"),
		"wpgm_mlist_sel_4" => __("Basic table","wp-google-maps"),
		"wpgm_mlist_sel_5" => __("Advanced table","wp-google-maps"),
		"wpgm_iw_sel_1" => __("Default Infowindow","wp-google-maps"),
		"wpgm_iw_sel_2" => __("Modern Infowindow","wp-google-maps"),
		"wpgm_copy_string" => __("Copied to clipboard","wp-google-maps"),
		"wpgm_iw_sel_3" => __("Modern Plus Infowindow","wp-google-maps"),
		"wpgm_iw_sel_4" => __("Circular Infowindow","wp-google-maps"),
		"wpgm_iw_sel_null" => __("No Global Setting","wp-google-maps")
	);

    if (isset($_GET['page'])) {

		if ($_GET['page'] == "wp-google-maps-menu-settings" || $_GET['page'] == "wp-google-maps-menu-advanced") {

			$wpgmza->loadScripts();
			
            wp_enqueue_script( 'jquery-ui-tabs');
            if (wp_script_is('my-wpgmaps-tabs','registered')) {  } else {
                //wp_register_style('jquery-ui-smoothness', 'https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
                //wp_enqueue_style('jquery-ui-smoothness');
                wp_register_script('my-wpgmaps-tabs', WPGMAPS_DIR.'js/wpgmaps_tabs.js', array('jquery-ui-core'), '1.0.1', true);
                wp_enqueue_script('my-wpgmaps-tabs');
            }

            wp_register_script('admin-wpgmaps', plugins_url('js/wpgmaps-admin.js', __FILE__));
			wp_enqueue_script('admin-wpgmaps');
			wp_localize_script( 'admin-wpgmaps', 'wpgmaps_localize_strings', $wpgmza_lang_strings);
        }
        if ($_GET['page'] == "wp-google-maps-menu") {
				$wpgmza->loadScripts();
		
                wp_register_script('admin-wpgmaps', plugins_url('js/wpgmaps-admin.js', __FILE__));
                wp_enqueue_script('admin-wpgmaps');
                wp_localize_script( 'admin-wpgmaps', 'wpgmaps_localize_strings', $wpgmza_lang_strings);
				
				wp_localize_script('admin-wpgmaps', 'wpgmza_plugin_dir_url', WPGMAPS_DIR);

				if(isset($_GET['map_id']))
				{
					$map_id = $_GET['map_id'];
					wp_localize_script('admin-wpgmaps', 'wpgmza_circle_data_array', wpgmza_get_circle_data($map_id));
					wp_localize_script('admin-wpgmaps', 'wpgmza_rectangle_data_array', wpgmza_get_rectangle_data($map_id));
				}
				
        }
    }
}

function wpgmaps_admin_styles_pro() {
    if (isset($_GET['page'])) {
        if(strpos($_GET['page'], "wp-google-maps") !== false){
            //wpgmza_enqueue_fontawesome();
			
            wp_register_style('wpgmaps-admin-style', plugins_url('css/wpgmaps-admin.css', __FILE__));
            wp_enqueue_style('wpgmaps-admin-style');
        }
    }
}

function wpgmaps_sl_user_output_pro($map_id,$atts = false)
{
	trigger_error("Deprecated since 8.0.25");
}

function wpgmza_localize_category_data()
{
	global $wpdb;
	global $wpgmza_tblname_categories;
	
	$data = array();
	$categories = $wpdb->get_results("SELECT * FROM $wpgmza_tblname_categories");
	
	foreach($categories as $category) {
		$category->category_icon = (empty($category->category_icon) ? WPGMAPS_DIR . 'images/marker.png' : $category->category_icon);
		$data[$category->id] = $category;
	}
	
	wp_enqueue_script('wpgmza_dummy', plugin_dir_url(__FILE__) . 'dummy.js');
	wp_localize_script('wpgmza_dummy', 'wpgmza_category_data', $data);
}

function wpgmza_content_filter($content) {

    $lat = get_post_meta( get_the_ID(), 'lat', true );
    $lng = get_post_meta( get_the_ID(), 'lng', true );
    $parent_id = get_post_meta( get_the_ID(), 'map_parent_id', true );
    $map_data = "";
    
    // check if the custom field has a value
    if( ! empty( $lat ) && ! empty( $lng ) ) {
        
       /* check if they have a parent ID set, if not, take first active available map ID */
       if (empty($parent_id) || !$parent_id) {
           global $wpdb;
           global $wpgmza_tblname_maps;
           $result = $wpdb->get_row(
            "
                SELECT *
                FROM `$wpgmza_tblname_maps`
                WHERE `active` = 0
                ORDER BY `id` ASC
                LIMIT 1
            ");
           if ($result) {
                $parent_id = $result->id;
           } else { $parent_id = false; }
       } 
        
       $map_data = do_shortcode("[wpgmza id='1' lat='$lat' lng='$lng' parent_id='$parent_id']");
    }   
    
    
    return $content.$map_data;
}
add_filter( 'the_content', 'wpgmza_content_filter' );

function wpgmaps_return_markers_pro($mapid = false) {

    if (!$mapid) {
        return;
    }
    global $wpdb;
	global $wpgmza_tblname;
	
    $table_name = $wpgmza_tblname;
	$columns = implode(', ', wpgmza_get_marker_columns());
	
    $sql = "SELECT $columns FROM $table_name WHERE `map_id` = '$mapid' AND `approved` = 1";
	
    $results = $wpdb->get_results($sql);
    $m_array = array();
    $cnt = 0;
    foreach ( $results as $result ) {   

        $id = $result->id;
        $address = addslashes($result->address);
        $description = do_shortcode(addslashes($result->description));
        $pic = $result->pic;
        if (!$pic) { $pic = ""; }
        $icon = $result->icon;
        if (!$icon) { $icon = ""; }
        $link_url = $result->link;
        if ($link_url) {  } else { $link_url = ""; }
        $lat = $result->lat;
        $lng = $result->lng;
        $anim = $result->anim;
        $retina = $result->retina;
        $category = $result->category;
        // $other_data = $result->other_settings;
		if (isset($result->other_data)) { $other_data = maybe_unserialize($result->other_data); } else { $other_data = ''; }
        
        if ($icon == "") {
            if (function_exists('wpgmza_get_category_data')) {
                $category_data = wpgmza_get_category_data($category);
                if (isset($category_data->category_icon) && isset($category_data->category_icon) != "") {
                    $icon = $category_data->category_icon;
                } else {
                   $icon = "";
                }
                if (isset($category_data->retina)) {
                    $retina = $category_data->retina;
                }
            }
        }
        $infoopen = $result->infoopen;
        $approved = $result->approved;
        
        $mtitle = addslashes($result->title);
        $map_id = $result->map_id;
        
        
        $m_array[$cnt] = array(
            'map_id' => $map_id,
            'marker_id' => $id,
            'title' => $mtitle,
            'address' => $address,
            'desc' => trim(preg_replace('/\s+/', ' ', nl2br($description))),
            'pic' => $pic,
            'icon' => $icon,
            'linkd' => $link_url,
            'lat' => $lat,
            'lng' => $lng,
            'anim' => $anim,
            'retina' => $retina,
            'category' => $category,
            'infoopen' => $infoopen,
            'approved' => $approved,
            'other_data' => $other_data
        );
        $cnt++;
        
    }

    return $m_array;
   
}

function wpgmaps_list_maps_pro() 
{

	$adminMapDataTableOptions = array(
		"pageLength" => 25,
		 "order" => [[ 1, "desc" ]]
	);

	$adminMapDataTable = new \WPGMZA\AdminMapDataTable(null, $adminMapDataTableOptions);
	echo $adminMapDataTable->document->html;
}

function wpgmaps_duplicate_map($map_id) {
    global $wpdb;
    global $wpgmza_tblname;
    global $wpgmza_tblname_maps;
    
    global $wpgmza_tblname_polylines;
    global $wpgmza_tblname_poly;
    global $wpgmza_tblname_category_maps;
	
	$map_id = (int)$map_id;
    
    $map_row_data = $wpdb->get_row(
        "
	SELECT *
	FROM $wpgmza_tblname_maps
        WHERE `id` = $map_id
        LIMIT 1
	"
    );
    $insert_row = "";
    $cnt = 1;
    $max_cnt = count(get_object_vars($map_row_data));
    foreach ($map_row_data as $key => $val) {
        if ($key == 'id') { $cnt++; /* dont include the ID column */ } else {
            $insert_array[$key] = $val;
            $cnt++;
        }
    }
    
    
    $rows_affected = $wpdb->insert( $wpgmza_tblname_maps, $insert_array );
    $new_map_id = $wpdb->insert_id;
    
    if (!$new_map_id) { return "Error duplicating the map"; }
    
    $map_id = (int)$map_id;
    
    $marker_data = $wpdb->get_results(
        "
	SELECT *
	FROM $wpgmza_tblname
        WHERE `map_id` = $map_id
	"
    );
    
    unset($insert_array);
    $insert_array = array();
    foreach ($marker_data as $marker) {
		
		$insert_array = (array)$marker;
		
		unset($insert_array['id']);
		$insert_array['map_id'] = $new_map_id;
		
        $rows_affected = $wpdb->insert( $wpgmza_tblname, $insert_array );

		$old_marker_id = $marker->id;
		$new_marker_id = $wpdb->insert_id;
		
		$old_marker_custom_fields = new WPGMZA\CustomMarkerFields($old_marker_id);
		$new_marker_custom_fields = new WPGMZA\CustomMarkerFields($new_marker_id);
		
		foreach($old_marker_custom_fields as $key => $value)
			$new_marker_custom_fields->{$key} = $value;
    }
    
	$map_id = (int)$map_id;
    
    $polyline_data = $wpdb->get_results(
        "
	SELECT *
	FROM $wpgmza_tblname_polylines
        WHERE `map_id` = $map_id
	"
    );
    
    
    unset($insert_array);
    $insert_array = array();
    foreach ($polyline_data as $polyline) {
        $cnt = 1;
        $max_cnt = count(get_object_vars($polyline));
        foreach ($polyline as $key => $val) {
            if ($key == 'id' || $key == 'map_id') { $cnt++; /* dont include the ID column */ } else {
                $insert_array[$key] = $val;
                $cnt++;
            }
        }
        $insert_array['map_id'] = $new_map_id;
        $rows_affected = $wpdb->insert( $wpgmza_tblname_polylines, $insert_array );

    }
    
	$map_id = (int)$map_id;
    
    $polygon_data = $wpdb->get_results(
        "
	SELECT *
	FROM $wpgmza_tblname_poly
        WHERE `map_id` = $map_id
	"
    );
    
    
    unset($insert_array);
    $insert_array = array();
    foreach ($polygon_data as $polygon) {
        $cnt = 1;
        $max_cnt = count(get_object_vars($polygon));
        foreach ($polygon as $key => $val) {
            if ($key == 'id' || $key == 'map_id') { $cnt++; /* dont include the ID column */ } else {
                $insert_array[$key] = $val;
                $cnt++;
            }
        }
        $insert_array['map_id'] = $new_map_id;
        $rows_affected = $wpdb->insert( $wpgmza_tblname_poly, $insert_array );

    }
    
    $map_id = (int)$map_id;
    
    $cat_data = $wpdb->get_results(
        "
	SELECT *
	FROM $wpgmza_tblname_category_maps
        WHERE `map_id` = $map_id
	"
    );
    unset($insert_array);
    $insert_array = array();
    foreach ($cat_data as $cat) {
        $cnt = 1;
        $max_cnt = count(get_object_vars($cat));
        foreach ($cat as $key => $val) {
            if ($key == 'id' || $key == 'map_id') { $cnt++; /* dont include the ID column */ } else {
                $insert_array[$key] = $val;
                $cnt++;
            }
        }
        $insert_array['map_id'] = $new_map_id;
        $rows_affected = $wpdb->insert( $wpgmza_tblname_category_maps, $insert_array );

    }

    return $new_map_id;
    
}


function wpgmza_b_return_heatmaps_list($map_id,$admin = true,$width = "100%") {
    global $wpdb;
    global $wpgmza_tblname_datasets;
    $wpgmza_tmp = "";

	$map_id = (int)$map_id;
	
    $results = $wpdb->get_results("
	SELECT *
	FROM `".$wpgmza_tblname_datasets."`
	WHERE `map_id` = '".$map_id."' ORDER BY `id` DESC
    ");
    
    $wpgmza_tmp .= "
        
        <table id=\"wpgmza_table_heatmaps\" class=\"display\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:$width;\">
        <thead>
        <tr>
            <th align='left'><strong>".__("ID","wp-google-maps")."</strong></th>
            <th align='left'><strong>".__("Name","wp-google-maps")."</strong></th>
            <th align='left' style='width:182px;'><strong>".__("Action","wp-google-maps")."</strong></th>
        </tr>
        </thead>
        <tbody>
    ";
    $res = wpgmza_get_map_data($map_id);
    $default_marker = "<img src='".$res->default_marker."' />";
    
    foreach ( $results as $result ) {
        unset($data_data);
        unset($data_array);
        $data_data = '';
        if (isset($result->dataset_name) && $result->dataset_name != "") { $dataset_name = $result->dataset_name; } else { $dataset_name = "Dataset".$result->id; }

        $wpgmza_tmp .= "
            <tr id=\"wpgmza_poly_tr_".$result->id."\">
                <td height=\"40\">".$result->id."</td>
                <td height=\"40\">$dataset_name</td>
                <td width='170' align='left'>
                    <a href=\"".get_option('siteurl')."/wp-admin/admin.php?page=wp-google-maps-menu&action=edit_heatmap&map_id=".$map_id."&id=".$result->id."\" title=\"".__("Edit","wp-google-maps")."\" class=\"wpgmza_edit_dataset_btn button\" id=\"".$result->id."\"><i class=\"fa fa-edit\"> </i></a> 
                    <a href=\"javascript:void(0);\" title=\"".__("Delete this dataset","wp-google-maps")."\" class=\"wpgmza_dataset_del_btn button\" id=\"".$result->id."\"><i class=\"fa fa-times\"> </i></a>
                </td>
            </tr>";
        
    }
    $wpgmza_tmp .= "</tbody></table>";
    

    return $wpgmza_tmp;
    
}
function wpgmza_b_return_dataset_array($id) {
    global $wpdb;
    global $wpgmza_tblname_datasets;
	
	$id = (int)$id;
	
    $results = $wpdb->get_results("
	SELECT *
	FROM $wpgmza_tblname_datasets
	WHERE `id` = '$id' LIMIT 1
    ");
    foreach ( $results as $result ) {
        $current_polydata = $result->dataset;
        $new_polydata = str_replace("),(","|",$current_polydata);
        $new_polydata = str_replace("(","",$new_polydata);
        $new_polydata = str_replace("),","",$new_polydata);
        $new_polydata = explode("|",$new_polydata);
        foreach ($new_polydata as $poly) {
            
            $ret[] = $poly;
        }
        return $ret;
    }

}

function wpgmza_b_return_dataset_options($id) {
    global $wpdb;
    global $wpgmza_tblname_datasets;
	
	$id = (int)$id;
	
    $results = $wpdb->get_results("
	SELECT *
	FROM $wpgmza_tblname_datasets
	WHERE `id` = '$id' LIMIT 1
    ");
    foreach ( $results as $result ) {
    	if (isset($result)) { return $result; }
    	else { return false; }
    }
}
function wpgmza_b_return_dataset_id_array($map_id) {
    global $wpdb;
    global $wpgmza_tblname_datasets;
    $ret = array();
	
	$map_id = (int)$map_id;
	
    $results = $wpdb->get_results("
	SELECT *
	FROM $wpgmza_tblname_datasets
	WHERE `map_id` = '$map_id'
    ");
    foreach ( $results as $result ) {
        $current_id = $result->id;
        $ret[] = $current_id;
        
    }
    return $ret;
}

function wpgmaps_b_admin_edit_heatmap_javascript($mapid,$polyid) {
        $res = wpgmza_get_map_data($mapid);
        
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");


        $wpgmza_lat = $res->map_start_lat;
        
        $wpgmza_lng = $res->map_start_lng;
        $wpgmza_map_type = $res->type;
        $wpgmza_width = $res->map_width;
        $wpgmza_height = $res->map_height;
        $wpgmza_width_type = $res->map_width_type;
        $wpgmza_height_type = $res->map_height_type;
        if (!$wpgmza_map_type || $wpgmza_map_type == "" || $wpgmza_map_type == "1") { $wpgmza_map_type = "ROADMAP"; }
        else if ($wpgmza_map_type == "2") { $wpgmza_map_type = "SATELLITE"; }
        else if ($wpgmza_map_type == "3") { $wpgmza_map_type = "HYBRID"; }
        else if ($wpgmza_map_type == "4") { $wpgmza_map_type = "TERRAIN"; }
        else { $wpgmza_map_type = "ROADMAP"; }
        $start_zoom = $res->map_start_zoom;
        if ($start_zoom < 1 || !$start_zoom) {
            $start_zoom = 5;
        }

        
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");

        ?>
        <link rel='stylesheet' id='wpgooglemaps-css'  href='<?php echo wpgmaps_get_plugin_url(); ?>/css/wpgmza_style.css' type='text/css' media='all' />
        <script type="text/javascript" >
             // polygons variables
            var poly;
            var heatmap = [];
            var poly_markers = [];
            var poly_path = [];
            var WPGM_PathLineData = [];
            var WPGM_Path = [];
            var poly_path = new google.maps.MVCArray;
            var enable_draw = false;

                
            jQuery(document).ready(function(){
                
                    function wpgmza_InitMap() {
                        var myLatLng = new google.maps.LatLng(<?php echo $wpgmza_lat; ?>,<?php echo $wpgmza_lng; ?>);
                        MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                    }
                    jQuery("#wpgmza_map").css({
                        height:'<?php echo $wpgmza_height; ?><?php echo $wpgmza_height_type; ?>',
                        width:'<?php echo $wpgmza_width; ?><?php echo $wpgmza_width_type; ?>'
                    });
                    wpgmza_InitMap();

                    var gradient = jQuery("#heatmap_gradient").html();
                    var opacity = jQuery("#heatmap_opacity").val();
                    var radius = jQuery("#heatmap_radius").val();
					jQuery("#heatmap_radius").focusout();
					jQuery("#heatmap_opacity").keyup();
					jQuery("#heatmap_gradient").keyup();
					poly.set('opacity', jQuery("#heatmap_opacity").val());
                	poly.set('radius', jQuery("#heatmap_radius").val());
	            	 var tmp = jQuery("#heatmap_gradient").html();
					 if (tmp !== "") { var gradient = JSON.parse(tmp); } else { var gradient = null; }
	            	 if (gradient == '1') { poly.set('gradient', null); } else { poly.set('gradient', gradient); }


                    jQuery("#heatmap_radius").focusout(function() {
                    	poly.set('radius', jQuery("#heatmap_radius").val());
                    });
                    jQuery("#heatmap_opacity").keyup(function() {
                    	poly.set('opacity', jQuery("#heatmap_opacity").val());
                    });
                    jQuery("body").on("keyup", "#heatmap_gradient", function() {
		            	 var tmp = jQuery(this).html();
						 var gradient = JSON.parse(tmp);
                    	 if (gradient == '1') { poly.set('gradient', null); } else { poly.set('gradient', gradient); }
                    });

                    
            });
            

            var MYMAP = {
                map: null,
                bounds: null
            }
            MYMAP.init = function(selector, latLng, zoom) {
                  var myOptions = {
                    zoom:zoom,
                    center: latLng,
                    zoomControl: true,
                    panControl: true,
                    mapTypeControl: true,
                    streetViewControl: false
                }
				
				if(WPGMZA.settings.engine == "google-maps")
					myOptions.mapTypeId = google.maps.MapTypeId.<?php echo $wpgmza_map_type; ?>;
				
                this.map = new google.maps.Map(jQuery(selector)[0], myOptions);
                this.bounds = new WPGMZA.LatLngBounds();
				
                // polygons
                
                <?php
                $total_dataset_array = wpgmza_b_return_dataset_id_array(sanitize_text_field($_GET['map_id']));
                if ($total_dataset_array > 0) {
                foreach ($total_dataset_array as $poly_id) {
                    $polyoptions = wpgmza_b_return_dataset_options($poly_id);
                    $poly_array = wpgmza_b_return_dataset_array($poly_id);                    





                    if ($polyid != $poly_id) {
						if (sizeof($poly_array) >= 1) { ?>
		                    WPGM_PathLineData[<?php echo $poly_id; ?>] = [
		                    <?php
		                    $poly_array = wpgmza_b_return_dataset_array($poly_id);

		                    foreach ($poly_array as $single_poly) {
		                        $poly_data_raw = str_replace(" ","",$single_poly);
		                        $poly_data_raw = explode(",",$poly_data_raw);
		                        $lat = floatval($poly_data_raw[0]);
		                        $lng = floatval($poly_data_raw[1]);
		                        echo "new google.maps.LatLng($lat, $lng),";
		                    }
		                    ?>
		                ];
	              	heatmap[<?php echo $poly_id; ?>] = new google.maps.visualization.HeatmapLayer({data: WPGM_PathLineData[<?php echo $poly_id; ?>]});

                	heatmap[<?php echo $poly_id; ?>].setMap(this.map);

                <?php } } } ?>

                <?php } ?>


                
                addCurrentHeatMap();
                

            }
            function addCurrentHeatMap() {
                <?php
                $poly_array = wpgmza_b_return_dataset_array($polyid);
                    
                $polyoptions = wpgmza_b_return_dataset_options($polyid);
                foreach ($poly_array as $single_poly) {
                    $poly_data_raw = str_replace(" ","",$single_poly);
                    $poly_data_raw = explode(",",$poly_data_raw);
                    $lat = $poly_data_raw[0];
                    $lng = $poly_data_raw[1];
                    ?>
                    var temp_gps = new google.maps.LatLng(<?php echo floatval($lat); ?>, <?php echo floatval($lng); ?>);
                    addExistingPoint(temp_gps);
                    updatePolyPath(poly_path);
                    
                    
                    
                    <?php
                }
                ?>
                poly = new google.maps.visualization.HeatmapLayer({
                  data: poly_path
                });
                poly.setMap(MYMAP.map);


				google.maps.event.addListener(MYMAP.map, 'rightclick', change_draw);

				document.onkeydown = function(evt) {
				    evt = evt || window.event;
				    var isEscape = false;
				    if ("key" in evt) {
				        isEscape = evt.key == "Escape";
				    } else {
				        isEscape = evt.keyCode == 27;
				    }
				    if (isEscape) {
				    	if(!enable_draw){
				        	change_draw(); //Only if draw mode is active
				        }
				    }
				};

				change_draw();
            }
            function addExistingPoint(temp_gps) {
                poly_path.insertAt(poly_path.length, temp_gps);
                var poly_marker = new google.maps.Marker({
                  position: temp_gps,
                  map: MYMAP.map,
                  draggable: true
                });
                poly_markers.push(poly_marker);
                poly_marker.setTitle("#" + poly_path.length);
                google.maps.event.addListener(poly_marker, 'click', function() {
                      poly_marker.setMap(null);
                      for (var i = 0, I = poly_markers.length; i < I && poly_markers[i] != poly_marker; ++i);
                      poly_markers.splice(i, 1);
                      poly_path.removeAt(i);
                      updatePolyPath(poly_path);    
                      }
                    );

                    google.maps.event.addListener(poly_marker, 'dragend', function() {
                      for (var i = 0, I = poly_markers.length; i < I && poly_markers[i] != poly_marker; ++i);
                      poly_path.setAt(i, poly_marker.getPosition());
                      updatePolyPath(poly_path);    
                      }
                    );
            }
            function addPoint(event) {
                
                    poly_path.insertAt(poly_path.length, event.latLng);

                    var poly_marker = new google.maps.Marker({
                      position: event.latLng,
                      map: MYMAP.map,
                      icon: "<?php echo wpgmaps_get_plugin_url()."/images/marker.png"; ?>",
                      draggable: true
                    });
                    

                    
                    poly_markers.push(poly_marker);
                    poly_marker.setTitle("#" + poly_path.length);

                    google.maps.event.addListener(poly_marker, 'click', function() {
                      poly_marker.setMap(null);
                      for (var i = 0, I = poly_markers.length; i < I && poly_markers[i] != poly_marker; ++i);
                      poly_markers.splice(i, 1);
                      poly_path.removeAt(i);
                      updatePolyPath(poly_path);    
                      }
                    );

                    google.maps.event.addListener(poly_marker, 'dragend', function() {
                      for (var i = 0, I = poly_markers.length; i < I && poly_markers[i] != poly_marker; ++i);
                      poly_path.setAt(i, poly_marker.getPosition());
                      updatePolyPath(poly_path);    
                      }
                    );
                        
                        
                    updatePolyPath(poly_path);    
              }
              
              function updatePolyPath(poly_path) {
                var temp_array;
                temp_array = "";
                poly_path.forEach(function(latLng, index) { 
//                  temp_array = temp_array + " ["+ index +"] => "+ latLng + ", ";
                  temp_array = temp_array + latLng + ",";
                }); 
                jQuery("#wpgmza_heatmap_data").html(temp_array);
              }     
              function change_draw() {
            	 if (enable_draw) {
            	 	google.maps.event.clearListeners(MYMAP.map, 'click');
					google.maps.event.addListener(MYMAP.map, 'mousemove', addPoint);
				} else {
            	 	google.maps.event.clearListeners(MYMAP.map, 'mousemove');
					google.maps.event.addListener(MYMAP.map, 'click', addPoint);
				}
            	if (enable_draw) { enable_draw = false; } else { enable_draw = true; }
            }

       
             

        </script>
        <?php
}

function wpgmaps_b_admin_add_heatmap_javascript($mapid) {
        $res = wpgmza_get_map_data(sanitize_text_field($_GET['map_id']));
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");


        $wpgmza_lat = $res->map_start_lat;
        $wpgmza_lng = $res->map_start_lng;
        $wpgmza_map_type = $res->type;
        $wpgmza_width = $res->map_width;
        $wpgmza_height = $res->map_height;
        $wpgmza_width_type = $res->map_width_type;
        $wpgmza_height_type = $res->map_height_type;
        if (!$wpgmza_map_type || $wpgmza_map_type == "" || $wpgmza_map_type == "1") { $wpgmza_map_type = "ROADMAP"; }
        else if ($wpgmza_map_type == "2") { $wpgmza_map_type = "SATELLITE"; }
        else if ($wpgmza_map_type == "3") { $wpgmza_map_type = "HYBRID"; }
        else if ($wpgmza_map_type == "4") { $wpgmza_map_type = "TERRAIN"; }
        else { $wpgmza_map_type = "ROADMAP"; }
        $start_zoom = $res->map_start_zoom;
        if ($start_zoom < 1 || !$start_zoom) {
            $start_zoom = 5;
        }

        
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
    	global $api_version_string;
        ?>

        <link rel='stylesheet' id='wpgooglemaps-css'  href='<?php echo wpgmaps_get_plugin_url(); ?>/css/wpgmza_style.css' type='text/css' media='all' />
        <script type="text/javascript" >
            	var myLatLng = new google.maps.LatLng(<?php echo $wpgmza_lat; ?>,<?php echo $wpgmza_lng; ?>);
            jQuery(document).ready(function(){
                    function wpgmza_InitMap() {
                        
                        MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                    }
                    jQuery("#wpgmza_map").css({
                        height:'<?php echo $wpgmza_height; ?><?php echo $wpgmza_height_type; ?>',
                        width:'<?php echo $wpgmza_width; ?><?php echo $wpgmza_width_type; ?>'
                    });
                    wpgmza_InitMap();
                    jQuery("#heatmap_radius").focusout(function() {
                    	poly.set('radius', jQuery("#heatmap_radius").val());
                    });
                    jQuery("#heatmap_opacity").keyup(function() {
                    	poly.set('opacity', jQuery("#heatmap_opacity").val());
                    });
                    jQuery("body").on("keyup", "#heatmap_gradient", function() {
		            	 var tmp = jQuery(this).html();
						 var gradient = JSON.parse(tmp);
                    	 if (gradient == '1') { poly.set('gradient', null); } else { poly.set('gradient', gradient); }
                    });
                    
                    
            });
             // polygons variables
            var poly;
            var poly_markers = [];
            var poly_path = [];
            var heatmap = [];
            var enable_draw = false;
            var WPGM_PathLineData = [];
            var poly_path = new google.maps.MVCArray;
            <?php 
			$total_dataset_array = wpgmza_b_return_dataset_id_array(sanitize_text_field($_GET['map_id']));
            foreach ($total_dataset_array as $poly_id) {
				
             ?>
            WPGM_PathLineData[<?php echo $poly_id; ?>];
            <?php } ?>
            

            var MYMAP = {
                map: null,
                bounds: null
            }
            MYMAP.init = function(selector, latLng, zoom) {
                  var myOptions = {
                    zoom:zoom,
                    center: latLng,
                    zoomControl: true,
                    panControl: true,
                    mapTypeControl: true,
                    streetViewControl: true
                  }
				  
				if(WPGMZA.settings.engine == "google-maps")
					myOptions.mapTypeId = google.maps.MapTypeId.<?php echo $wpgmza_map_type; ?>;
				
                this.map = new google.maps.Map(jQuery(selector)[0], myOptions);
                this.bounds = new WPGMZA.LatLngBounds();
                
                
                poly = new google.maps.visualization.HeatmapLayer({
                  data: poly_path
                });
                poly.setMap(this.map);

				google.maps.event.addListener(this.map, 'rightclick', change_draw);

				document.onkeydown = function(evt) {
				    evt = evt || window.event;
				    var isEscape = false;
				    if ("key" in evt) {
				        isEscape = evt.key == "Escape";
				    } else {
				        isEscape = evt.keyCode == 27;
				    }
				    if (isEscape) {
				        if(!enable_draw){
				        	change_draw(); //Only if draw mode is active
				        }
				    }
				};

				change_draw();
                
               
//				google.maps.event.addListener(this.map, 'click', addPoint);
                <?php
                /* datasets */
                    
                    if ($total_dataset_array > 0) {
                    foreach ($total_dataset_array as $poly_id) {
                        $polyoptions = wpgmza_b_return_dataset_options($poly_id);
                        ?>                
<?php
                        $poly_array = wpgmza_b_return_dataset_array($poly_id);
						if (sizeof($poly_array) >= 1) { ?>
		                    WPGM_PathLineData[<?php echo $poly_id; ?>] = [
		                    <?php
		                    $poly_array = wpgmza_b_return_dataset_array($poly_id);

		                    foreach ($poly_array as $single_poly) {
		                        $poly_data_raw = str_replace(" ","",$single_poly);
		                        $poly_data_raw = explode(",",$poly_data_raw);
		                        $lat = floatval($poly_data_raw[0]);
		                        $lng = floatval($poly_data_raw[1]);
		                        echo "new google.maps.LatLng($lat, $lng),";
		                    }
		                    ?>
		                ];
	              	heatmap[<?php echo $poly_id; ?>] = new google.maps.visualization.HeatmapLayer({data: WPGM_PathLineData[<?php echo $poly_id; ?>]});

                	heatmap[<?php echo $poly_id; ?>].setMap(this.map.googleMap);
                <?php } } } ?> 

            }

            function change_draw() {
            	 if (enable_draw) {
            	 	google.maps.event.clearListeners(MYMAP.map, 'click');
					google.maps.event.addListener(MYMAP.map, 'mousemove', addPoint);
				} else {
            	 	google.maps.event.clearListeners(MYMAP.map, 'mousemove');
					google.maps.event.addListener(MYMAP.map, 'click', addPoint);
				}
            	if (enable_draw) { enable_draw = false; } else { enable_draw = true; }
            }

            function addPoint(event) {
            	poly_path.push(event.latLng);

                var poly_marker = new google.maps.Marker({
                  position: event.latLng,
                  map: MYMAP.map,
                  icon: "<?php echo wpgmaps_get_plugin_url()."/images/marker.png"; ?>",
                  draggable: true
                });
                

                
                poly_markers.push(poly_marker);
                poly_marker.setTitle("#" + poly_path.length);

                google.maps.event.addListener(poly_marker, 'click', function() {
                  poly_marker.setMap(null);
                  for (var i = 0, I = poly_markers.length; i < I && poly_markers[i] != poly_marker; ++i);
                  poly_markers.splice(i, 1);
                  poly_path.removeAt(i);
                  updatePolyPath(poly_path);    
                  }
                );

                google.maps.event.addListener(poly_marker, 'dragend', function() {
                  for (var i = 0, I = poly_markers.length; i < I && poly_markers[i] != poly_marker; ++i);
                  poly_path.setAt(i, poly_marker.getPosition());
                  updatePolyPath(poly_path);    
                  }
                );
                    
                    
                updatePolyPath(poly_path);    
          	}
			function updatePolyPath(poly_path) {
				var temp_array;
				temp_array = "";
				poly_path.forEach(function(latLng, index) { 
					temp_array = temp_array + latLng + ",";
				}); 				
				jQuery("#wpgmza_heatmap_data").html(temp_array);
                
			}           	

                    


        </script>
        <?php
}


function wpgmza_b_pro_add_heatmap($mid) {
    global $wpgmza_tblname_maps;
    global $wpdb;
    if ($_GET['action'] == "add_heatmap" && isset($mid)) {
        $res = wpgmza_get_map_data($mid);
        echo "
            

            
          
           <div class='wrap'>
                <h1>WP Google Maps</h1>
                <div class='wide'>

                    <h2>".__("Add heatmap data","wp-google-maps")."</h2>
                    <form action='?page=wp-google-maps-menu&action=edit&map_id=".$mid."' method='post' id='wpgmaps_add_heatmap_form'>
                    <input type='hidden' name='wpgmaps_map_id' id='wpgmaps_map_id' value='".$mid."' />
                    <table class='wpgmza-listing-comp' style='width:30%;float:left;'>
                        <tr>
                            <td>
                                ".__("Name","wp-google-maps")."
                            </td>
                            <td>
                                <input id=\"poly_line\" name=\"poly_name\" type=\"text\" value=\"\" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                ".__("Gradient","wp-google-maps")."
                            </td>
                            <td>
                                <textarea id=\"heatmap_gradient\" name=\"heatmap_gradient\" style='display:none; width:200px; height:200px;' /></textarea><button id='wpgmza_gradient_show' gtype='default' class='wpgmza_gradient_show button button-secondary' />".__("Default","wp-google-maps")."</button> <button id='wpgmza_gradient_show' gtype='blue' class='wpgmza_gradient_show button button-secondary' />".__("Blue","wp-google-maps")."</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                ".__("Opacity","wp-google-maps")."
                            </td>
                            <td>
                                <input id=\"heatmap_opacity\" name=\"heatmap_opacity\" type=\"text\" value=\"0.6\" /> (0 - 1.0) example: 0.6 for 60%
                            </td>
                        </tr>
                        <tr>
                            <td>
                                ".__("Radius","wp-google-maps")."
                            </td>
                            <td>
                                <input id=\"heatmap_radius\" name=\"heatmap_radius\" type=\"text\" value=\"20\" />
                            </td>
                                
                    	</tr>
                    </table>
                    <div class='wpgmza_map_seventy'> 

	                    <div id=\"wpgmza_map\">&nbsp;</div>
	                    <p>
	                            <ul style=\"list-style:initial; margin-top: -145px !important;\" class='update-nag update-blue update-slim update-map-overlay'>
	                                <li style=\"margin-left:30px;\">Click on the map to insert a vertex.</li>
	                                <li style=\"margin-left:30px;\">Click on a vertex to remove it.</li>
	                                <li style=\"margin-left:30px;\">Drag a vertex to move it.</li>
	                                <li style=\"margin-left:30px;\">Right-Click to activate 'Draw Mode'.</li>
	                                <li style=\"margin-left:30px;\">Press the 'Escape' key to deactivate 'Draw Mode'.</li>
	                            </ul>
	                    </p>
	                </div>

	                <div class='clear'></div>
                    <p>Heatmap data:<br /><textarea name=\"wpgmza_heatmap_data\" id=\"wpgmza_heatmap_data\" style=\"height:100px; background-color:#FFF; padding:5px; overflow:auto;\"></textarea>
                    


                    <p class='submit'><a href='javascript:history.back();' class='button button-secondary' title='".__("Cancel")."'>".__("Cancel")."</a> <input type='submit' name='wpgmza_save_heatmap' class='button-primary' value='".__("Save Dataset","wp-google-maps")." &raquo;' /></p>

                    </form>
                </div>


            </div>
        ";

    }
}

function wpgmza_b_pro_edit_heatmap($mid) {
    global $wpgmza_tblname_maps;
    global $wpdb;
    if ($_GET['action'] == "edit_heatmap" && isset($mid)) {
        $res = wpgmza_get_map_data($mid);
        $pol = wpgmza_b_return_dataset_options(sanitize_text_field($_GET['id']));
        $options = maybe_unserialize($pol->options);

        echo "
            

           <div class='wrap'>
                <h1>WP Google Maps</h1>
                <div class='wide'>

                    <h2>".__("Edit Dataset","wp-google-maps")."</h2>
                    <form action='?page=wp-google-maps-menu&action=edit&map_id=".$mid."' method='post' id='wpgmaps_edit_heatmap_form'>
                    <input type='hidden' name='wpgmaps_map_id' id='wpgmaps_map_id' value='".$mid."' />
                    <input type='hidden' name='wpgmaps_poly_id' id='wpgmaps_poly_id' value='".sanitize_text_field($_GET['id'])."' />
                    <table class='wpgmza-listing-comp' style='width:30%;float:left;'>
                        <tr>
                            <td>
                                ".__("Name","wp-google-maps")."
                            </td>
                            <td>
                                <input id=\"poly_line\" name=\"poly_name\" type=\"text\" value=\"".$pol->dataset_name."\" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                ".__("Gradient","wp-google-maps")."
                            </td>
                            <td>
                                <textarea id=\"heatmap_gradient\" name=\"heatmap_gradient\" style='display:none; width:200px; height:200px;' />".stripslashes($options['heatmap_gradient'])."</textarea><button id='wpgmza_gradient_show' gtype='default' class='wpgmza_gradient_show button button-secondary' />".__("Default","wp-google-maps")."</button> <button id='wpgmza_gradient_show' gtype='blue' class='wpgmza_gradient_show button button-secondary' />".__("Blue","wp-google-maps")."</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                ".__("Opacity","wp-google-maps")."
                            </td>
                            <td>
                                <input id=\"heatmap_opacity\" name=\"heatmap_opacity\" type=\"text\" value=\"".$options['heatmap_opacity']."\" /> (0 - 1.0) example: 0.6 for 60%
                            </td>
                        </tr>
                        <tr>
                            <td>
                                ".__("Radius","wp-google-maps")."
                            </td>
                            <td>
                                <input id=\"heatmap_radius\" name=\"heatmap_radius\" type=\"text\" value=\"".$options['heatmap_radius']."\" />
                            </td>
                                
                    	</tr>                        
                    </table>
                    <div class='wpgmza_map_seventy'> 
                    	<div id=\"wpgmza_map\">&nbsp;</div>
							<p>
	                            <ul style=\"list-style:initial; margin-top: -145px !important;\" class='update-nag update-blue update-slim update-map-overlay'>
	                                <li style=\"margin-left:30px;\">Click on the map to insert a vertex.</li>
	                                <li style=\"margin-left:30px;\">Click on a vertex to remove it.</li>
	                                <li style=\"margin-left:30px;\">Drag a vertex to move it.</li>
	                                <li style=\"margin-left:30px;\">Right-Click to activate 'Draw Mode'.</li>
	                                <li style=\"margin-left:30px;\">Press the 'Escape' key to deactivate 'Draw Mode'.</li>
	                            </ul>
		                    </p>
		            </div>
	                
	                <div class='clear'></div>
	                <p>Heatmap data:<br /><textarea name=\"wpgmza_heatmap_data\" id=\"wpgmza_heatmap_data\" style=\"height:100px; background-color:#FFF; padding:5px; overflow:auto;\">".$pol->dataset."</textarea>
	                    
                    <p class='submit'><input type='submit' name='wpgmza_edit_heatmap' class='button-primary' value='".__("Save Dataset","wp-google-maps")." &raquo;' /></p>

                    </form>
                </div>
            </div>
        ";
    }
}

//add_action("wpgooglemaps_hook_user_js_after_core","wpgooglemaps_pro_full_screen_hook_control_user_js_after_core",10);
function wpgooglemaps_pro_full_screen_hook_control_user_js_after_core() {
    global $wpgmza_p_version;
    wp_register_style( 'wp-google-maps-full-screen', WPGMAPS_DIR.'/css/wp-google-maps-full-screen-map.css',array(),$wpgmza_p_version);
    wp_enqueue_style( 'wp-google-maps-full-screen' );
    wp_register_script('wp-google-maps-full-screen-js', plugins_url('/js/wp-google-maps-full-screen-map.js',__FILE__), array(), $wpgmza_p_version, false);
    wp_enqueue_script('wp-google-maps-full-screen-js');
}



add_filter("wpgmza_filter_marker_add_table_tr","wpgmza_pro_filter_control_marker_add_table_tr",10,3);
function wpgmza_pro_filter_control_marker_add_table_tr($content,$map_data,$settings) {
	$content .= "<tr>".PHP_EOL;
	$content .= "<td>".__("Display on front end","wp-google-maps")."</td>".PHP_EOL;
	$content .= "<td>".PHP_EOL;
    $content .= "	<select name=\"wpgmza_approved\" id=\"wpgmza_approved\">".PHP_EOL;
    $content .= "		<option value=\"1\">".__("Yes","wp-google-maps")."</option>".PHP_EOL;
    $content .= "		<option value=\"0\">".__("No","wp-google-maps")."</option>".PHP_EOL;
    $content .= "	</select>".PHP_EOL;
	$content .= "</td>".PHP_EOL;
	$content .= "</tr>";
	return $content;
}

//add_filter("wpgmza_filter_marker_add_table_tr","wpgmza_pro_filter_control_marker_add_custom_icon_click_tr",10,3);
function wpgmza_pro_filter_control_marker_add_custom_icon_click_tr($content,$map_data,$settings) {
	$content .= "<tr>".PHP_EOL;
	$content .= "<td>".__("On click, change icon to","wp-google-maps")."</td>".PHP_EOL;
	$content .= "<td>".PHP_EOL;
    $content .= "	<span id=\"wpgmza_cmm_custom\"><img src='".wpgmaps_get_plugin_url()."/images/marker.png' border='0' /></span><input id='wpgmza_add_custom_marker_on_click' name=\"wpgmza_add_custom_marker_on_click\" type='hidden' size='35' maxlength='700' value='' />";
    $content .= " 	<input id=\"upload_custom_marker_click_button\" type=\"button\" value=\"".__("Upload Image","wp-google-maps")."\"  /> &nbsp; <small><i>(".__("ignore if you want to use the normal marker","wp-google-maps").")</i></small><br />";
	$content .= "</td>".PHP_EOL;
	$content .= "</tr>";
	return $content;
}

/* Takes three arrays and filters default map data accordingly
 * Data Content Array -  Array with default values
 * Data Keys - Keys to override default values
 * Data Values - Values associated to each key in array
*/
function wpgmza_wizard_data_filter($wpgmza_map_data_content, $wpmgza_map_data_keys, $wpmgza_map_data_values){

    for($i = 0; $i < count($wpmgza_map_data_keys); $i++){
    	if($i < count($wpmgza_map_data_keys) -1){
    		$wpgmza_map_data_content[$wpmgza_map_data_keys[$i]] = $wpmgza_map_data_values[$i]; //Change value at index
    	} else {
    		//Deal with other settings here
    		$new_other_settings = explode("@", $wpmgza_map_data_values[$i]);
    		$other_settings_to_pass = array();

    		for($b = 0; $b <  count($new_other_settings); $b ++){
    			if($b % 2 == 0){
    				//Is key
    				$other_settings_to_pass[ $new_other_settings[ $b ] ] = $new_other_settings[ $b+1 ];
    			}
    		}
    		$wpgmza_map_data_content[$wpmgza_map_data_keys[$i]] = maybe_serialize($other_settings_to_pass);
    	}
    }
    return $wpgmza_map_data_content;
}

if( isset( $_GET['page'] ) && $_GET['page'] == 'wp-google-maps-menu' ){
    if( is_admin() ){
        add_action('admin_enqueue_styles', 'wpgmza_pro_deregister_styles',999);
        add_action('admin_enqueue_scripts', 'wpgmza_pro_deregister_styles',999);        
        add_action('admin_head', 'wpgmza_pro_deregister_styles',999);
        add_action('init', 'wpgmza_pro_deregister_styles',999);
        add_action('admin_footer', 'wpgmza_pro_deregister_styles',999);
        add_action('admin_print_styles', 'wpgmza_pro_deregister_styles',999);        
    }
}

function wpgmza_pro_deregister_styles() {
    global $wp_styles;            
    if (isset($wp_styles->registered) && is_array($wp_styles->registered)) {                
        foreach ( $wp_styles->registered as $script) {                    
            if (strpos($script->src, 'jquery-ui.theme.css') !== false || strpos($script->src, 'jquery-ui.css') !== false) {
                $script->handle = "";
                $script->src = "";
            }
        }
    }
}

/**
 * This changes the global setting variable should the user add "new_window_link='1'" to the short code
 */
add_filter("wpgmza_filter_localize_settings","wpgooglemaps_hook_control_overrides_user_js_settings",10);
function wpgooglemaps_hook_control_overrides_user_js_settings($wpgmza_settings) {
	global $wpgmza_override;
	if (isset($wpgmza_override['new_window_link'])) {
        $wpgmza_settings['wpgmza_settings_infowindow_links'] = (string) $wpgmza_override['new_window_link'];
	}
	return $wpgmza_settings;
}

add_action("wpgooglemaps_hook_user_js_after_core","wpgooglemaps_hook_control_overrides_user_js_after_core",10);
function wpgooglemaps_hook_control_overrides_user_js_after_core() {
	global $wpgmza_override;
	if (isset($wpgmza_override['zoom'])) {
        wp_localize_script( 'wpgmaps_core', 'wpgmza_override_zoom', $wpgmza_override['zoom']);
	}
}
add_action("wpgooglemaps_hook_user_js_after_core","wpgooglemaps_hook_control_overrides_user_js_after_core_markeroverride",10);
function wpgooglemaps_hook_control_overrides_user_js_after_core_markeroverride() {
	global $wpgmza_override;
	if (isset($wpgmza_override['marker'])) {
        wp_localize_script( 'wpgmaps_core', 'wpgmza_override_marker', $wpgmza_override['marker']);
	}
}

function wpgmza_get_allowed_tags(){
	
	$tags = wp_kses_allowed_html("post");
	
	$tags['iframe'] = array(
		'src'             => true,
		'width'           => true,
		'height'          => true,
		'align'           => true,
		'class'           => true,
		'style'           => true,
		'name'            => true,
		'id'              => true,
		'frameborder'     => true,
		'seamless'        => true,
		'srcdoc'          => true,
		'sandbox'         => true,
		'allowfullscreen' => true
	);
	
	$tags['input'] = array(
		'type'            => true,
		'value'           => true,
		'placeholder'     => true,
		'class'           => true,
		'style'           => true,
		'name'            => true,
		'id'              => true,
		'checked'         => true,
		'readonly'        => true,
		'disabled'        => true,
		'enabled'         => true
	);
	
	$tags['select'] = array(
		'value'           => true,
		'class'           => true,
		'style'           => true,
		'name'            => true,
		'id'              => true
	);
	
	$tags['option'] = array(
		'value'           => true,
		'class'           => true,
		'style'           => true,
		'name'            => true,
		'id'              => true,
		'selected'        => true
	);

	$tags['img'] = array(
		'src'		=> true,
		'alt'		=> true,
		'style'		=> true,
		'width'		=> true,
		'height'	=> true,
		'srcset'	=> true,
		'sizes'		=> true
	);
	
	return apply_filters('wpgmza_get_kses_allowed_tags', $tags);
}

/**
 * Migrates text lat/lng columns into spatial latlng column if necessary
 * @return void
 */
if(!function_exists('wpgmza_migrate_spatial_data'))
{
	function wpgmza_migrate_spatial_data() {
		
		global $wpdb;
		global $wpgmza_tblname;
		
		if(empty($wpgmza_tblname))
			return;
		
		if(!$wpdb->get_var("SHOW COLUMNS FROM ".$wpgmza_tblname." LIKE 'latlng'"))
			$wpdb->query('ALTER TABLE '.$wpgmza_tblname.' ADD latlng POINT');
		
		if($wpdb->get_var("SELECT COUNT(id) FROM $wpgmza_tblname WHERE latlng IS NULL LIMIT 1") == 0)
			return; // Nothing to migrate
		
		$wpdb->query("UPDATE ".$wpgmza_tblname." SET latlng=PointFromText(CONCAT('POINT(', CAST(lat AS DECIMAL(18,10)), ' ', CAST(lng AS DECIMAL(18,10)), ')'))");
	}
	
	add_action('init', 'wpgmza_migrate_spatial_data', 1);
}

// Get admin path
function wpgmza_pro_get_admin_path()
{
	return ABSPATH . 'wp-admin/';
}

// Add circles and rectangles to database
function wpgmza_pro_db_install_circles()
{
	trigger_error("Deprecated as of 8.0.19");
}

function wpgmza_pro_db_install_rectangles()
{
	trigger_error("Deprecated as of 8.0.19");
}

function wpgmza_pro_db_install_v7_tables()
{
	trigger_error("Deprecated as of 8.0.19");
}

function wpgmaps_admin_menu_custom_fields()
{
	global $wpgmza;
	
	if(method_exists($wpgmza, 'getAccessCapability'))
		$access_level = $wpgmza->getAccessCapability();
	else
	{
		$wpgmza_settings = get_option('WPGMZA_OTHER_SETTINGS');
	
		if (isset($wpgmza_settings['wpgmza_settings_access_level'])) { $access_level = $wpgmza_settings['wpgmza_settings_access_level']; } else { $access_level = "manage_options"; }
	}
	
	add_submenu_page(
		'wp-google-maps-menu',
		'WP Google Maps - Custom Fields',
		__('Custom Fields', 'wp-google-maps'),
		$access_level,
		'wp-google-maps-menu-custom-fields',
		'WPGMZA\\show_custom_fields_page'
	);
}

function wpgmaps_admin_menu_custom_fields_bind()
{
	add_action( 'admin_menu', 'wpgmaps_admin_menu_custom_fields' );
}
	
add_action('plugins_loaded', 'wpgmaps_admin_menu_custom_fields_bind');

function maybe_install_v7_tables_pro()
{
	trigger_error("Deprecated as of 8.0.19");
}

// add_action('init', 'maybe_install_v7_tables_pro');

function wpgmza_upload_base64_image()
{
	global $wpgmza;
	
	// Load media functions
	wpgmza_require_once( ABSPATH . 'wp-admin/includes/file.php' );
	wpgmza_require_once( ABSPATH . 'wp-admin/includes/media.php' );
	wpgmza_require_once( ABSPATH . 'wp-admin/includes/image.php' );
	
	// Security checks
	check_ajax_referer( 'wpgmza', 'security' );
	
	if(!$wpgmza->isUserAllowedToEdit())
	{
		http_response_code(401);
		exit;
	}
	
	// Handle upload
	$upload_dir = wp_upload_dir();
	$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
	$base64_img = $_POST['data'];
	$image_data = preg_replace('/^data:.+?;base64,/', '', $base64_img);
	$image_data = base64_decode($image_data);
	
	$filename = uniqid('', true);
	
	switch($_POST['mimeType'])
	{
		case 'image/jpg':
		case 'image/jpeg':
			$filename .= '.jpg';
			break;
			
		default:
			$filename .= '.png';
			break;
	}
	
	$tmp_name = $upload_path . $filename;
	
	file_put_contents($tmp_name, $image_data);
	
	$file = array(
		'error'		=> 0,
		'tmp_name'	=> $tmp_name,
		'name'		=> $filename,
		'type'		=> $_POST['mimeType'],
		'size'		=> filesize($tmp_name)
	);
	
	$result = wp_handle_sideload($file, array('test_form' => false));
	
	$attachment	= array(
		'post_title' 		=> basename($result['file']),
		'post_content'		=> '',
		'post_status'		=> 'inherit',
		'post_mime_type'	=> $result['type']
	);
	
	$attachment_id = wp_insert_attachment(
		$attachment,
		$result['file']
	);
	
	$meta_data = wp_generate_attachment_metadata($attachment_id, $result['file']);
	
	wp_update_attachment_metadata($attachment_id, $meta_data);
	
	wp_send_json($result);
	exit;
}

add_action('wp_ajax_wpgmza_upload_base64_image', 'wpgmza_upload_base64_image');

if(!function_exists('wpgmza_enqueue_fontawesome'))
{
	function wpgmza_enqueue_fontawesome()
	{
		// Deprecated as of 8.0.19
	}
}

if(!function_exists('wpgmza_get_icon'))
{
	function wpgmza_get_icon( $icon )
	{
		// Deprecated as of 8.0.25
	}
}