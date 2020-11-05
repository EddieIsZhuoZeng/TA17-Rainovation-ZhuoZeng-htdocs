<?php

namespace WPGMZA;

class LiveTrackingSettingsPanel extends DOMDocument
{
	public function __construct()
	{
		global $wpgmza;
		global $wpgmza_gold_version;
		
		DOMDocument::__construct();
		
		$this->loadPHPFile(plugin_dir_path(__DIR__) . 'html/live-tracking-settings-panel.html.php');
		
		if($wpgmza->settings->enable_live_tracking)
			$this->querySelector('input[name="enable_live_tracking"]')->setAttribute('checked', 'checked');
		
		// NB: Removed as of 5.0.0
		// wp_enqueue_script('wpgmza-live-tracking-settings-panel', plugin_dir_url(__DIR__) . 'js/v8/live-tracking-settings-panel.js', array(), $wpgmza_gold_version);
	}
	
	public function onSaveSettings()
	{
		global $wpgmza;
		
		$wpgmza->settings->enable_live_tracking = isset($_POST['enable_live_tracking']);
	}
}
