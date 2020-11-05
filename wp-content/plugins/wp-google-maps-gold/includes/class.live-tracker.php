<?php

namespace WPGMZA;

class LiveTracker
{
	public function __construct()
	{
		$this->install();
		
		add_filter('wpgmza_global_settings_tabs', array($this, 'onGlobalSettingsTabs'));
		add_filter('wpgmza_global_settings_tab_content', array($this, 'onGlobalSettingsTabContent'));
		
		// add_action('wp_enqueue_scripts', array($this, 'onEnqueueScripts'));
		
		add_action('admin_post_wpgmza_settings_page_post_pro', array($this, 'onSaveSettings'), 9, 0);
		
		add_action('wpgmza_register_rest_api_routes', array($this, 'onRegisterRestAPIRoutes'));
	}
	
	protected function install()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES;
		
		require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
		
		\dbDelta("CREATE TABLE {$WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES} (
			id int(11) NOT NULL AUTO_INCREMENT,
			deviceID VARCHAR(32) NOT NULL,
			name VARCHAR(256) NULL DEFAULT NULL,
			last_contact DATETIME NOT NULL,
			marker_id int(11) NOT NULL DEFAULT -1,
			polyline_id int(11) NOT NULL DEFAULT -1,
			approved int(1) NOT NULL DEFAULT 0,
			other_data TEXT NULL,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
	}
	
	// NB: Removed as of 5.0.0
	/*public function onEnqueueScripts()
	{
		global $wpgmza_gold_version;
		
		wp_enqueue_script('wpgmza-live-tracker', plugin_dir_url(__DIR__) . 'js/v8/live-tracker.js', array('wpgmza'), $wpgmza_gold_version);
	}*/
	
	public function onGlobalSettingsTabs($str)
	{
		return $str . "<li style='margin-right: 3px;'><a href=\"#tabs-rtlt\">".__("Location Tracking","wp-google-maps")."</a></li>";
	}
	
	public function onGlobalSettingsTabContent($str)
	{
		require_once(plugin_dir_path(__FILE__) . 'class.live-tracking-settings-panel.php');
	
		$document = new LiveTrackingSettingsPanel();
		return $str . "<div id='tabs-rtlt'>" . $document->html . "</div>";
	}
	
	public function onSaveSettings()
	{
		require_once(plugin_dir_path(__FILE__) . 'class.live-tracking-settings-panel.php');
		
		$document = new LiveTrackingSettingsPanel();
		$document->onSaveSettings();
	}
	
	public function onRegisterRestAPIRoutes()
	{
		global $wpgmza;
		
		if(empty($wpgmza) || !isset($wpgmza->restAPI) || !method_exists($wpgmza->restAPI, 'registerRoute'))
		{
			trigger_error("Please update WP Google Maps. The currently installed version is not compatible with this version of WP Google Maps - Gold add-on", E_USER_WARNING);
			return;
		}
		
		$wpgmza->restAPI->registerRoute('/live-tracker/devices/([0-9a-f]+)', array(
			'methods'				=> array('GET'),
			'callback'				=> array($this, 'devices')
		));
		
		$wpgmza->restAPI->registerRoute('/live-tracker/devices', array(
			'methods'				=> array('GET'),
			'callback'				=> array($this, 'devices')
		));
		
		$wpgmza->restAPI->registerRoute('/live-tracker', array(
			'methods'				=> array('GET'),
			'callback'				=> array($this, 'onRestRequest')
		));
		
		$wpgmza->restAPI->registerRoute('/live-tracker', array(
			'methods'				=> array('POST'),
			'callback'				=> array($this, 'onRestRequest')
		));
		
		$wpgmza->restAPI->registerRoute('/live-tracker/devices/([0-9a-f]+)', array(
			'methods'				=> array('POST'),
			'callback'				=> array($this, 'devices'),
			'skipNonceCheck'		=> true
		));
	}
	
	public function devices($request)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES;
		
		$route = $request->get_route();
		
		$mapIDs = null;
		if(isset($_GET['map_ids']))
			$mapIDs = explode(',', $_GET['map_ids']);
		
		switch($_SERVER['REQUEST_METHOD'])
		{
			case "GET":
				$qstr = "SELECT id FROM $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES";
				$canViewSensitiveFields = (current_user_can('administrator') && (is_admin() || defined('REST_REQUEST')));
				
				// Only show approved devices to non-administrators
				if(!$canViewSensitiveFields)
					$qstr .= " WHERE approved=1";
			
				$ids = $wpdb->get_col($qstr);
				$devices = array();
				
				if(empty($ids))
					return $devices;
				
				foreach($ids as $id)
				{
					$device = new LiveTrackingDevice((int)$id);
					$data = json_decode( json_encode($device) );
					
					if($device->marker_id > -1)
					{
						try{
							$data->marker = Marker::createInstance($device->marker_id);
						}catch(\Exception $e) {
							// Marker probably deleted
						}
						
						// NB: Removed - I'm not sure why we're checking markers have a valid map
						//if(!empty($mapIDs) && array_search($data->marker->map_id, $mapIDs) === false)
							//continue;
					}
					
					// Hide device ID from non-administrators
					if(!$canViewSensitiveFields)
						unset($data->deviceID);
					
					$devices[] = $data;
				}
				
				return $devices;
				break;
			
			case "POST":
			
				$input = file_get_contents('php://input');
				$json = json_decode($input);
				
				if(!empty($json->location))
				{
					if(!preg_match('/[0-9a-f]+$/', $route, $m))
						throw new \Exception('Invalid ID');
					
					// Device ID is considered cryptographically secure, allow POST
					$device = new LiveTrackingDevice($m[0]);
					
					if($device->approved != 1)
						return $device;
					
					if(!$json)
						return array('success' => 0, 'message' => 'Failed to parse JSON - ' . json_last_error_msg());
					
					if(empty($json->location->coords))
						return array('success' => 0, 'message' => 'No coordinate data found in request');
					
					$device->updateFromApp($json);
					
					return array('success' => 1);
				}
				else
				{
					if(!preg_match('/\d+$/', $route, $m))
						throw new \Exception('Invalid ID');
					
					// Only admins can POST to update devices by numeric ID
					if(!current_user_can('administrator'))
						throw new \Exception('Permission denied');
					
					$id = (int)$m[0];
					$device = new LiveTrackingDevice($id);
					
					$data = (array)$_POST;
					unset($data['id']);
					
					$device->set($data);
					
					return $device;
				}
				
				break;
		}
	}
	
	public function onRestRequest($request)
	{
		$route = $request->get_route();
		
		switch($_SERVER['REQUEST_METHOD'])
		{
			case "GET":
			
				if(isset($_GET['deviceID']))
				{
					$device = new LiveTrackingDevice($_GET['deviceID']);
					
					if(!empty($_GET['name']))
						$device->name = $_GET['name'];
					
					$device->touch();
					
					return $device;
				}
				
				return array('serviceStatus' => 'ready');
				
				break;
			
			case "POST":
			
				
				break;
		}
	}
}
