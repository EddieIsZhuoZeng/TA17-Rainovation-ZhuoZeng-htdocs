<?php

namespace WPGMZA;

class MarkerSeparatorSettings extends DOMDocument
{
	private $markerLibraryDialog;
	
	public function __construct()
	{
		global $wpgmza;
		
		DOMDocument::__construct();
		
		if(class_exists('WPGMZA\\MarkerLibraryDialog'))
			$this->markerLibraryDialog = new MarkerLibraryDialog();
		
		$this->loadPHPFile(plugin_dir_path(__DIR__) . 'html/marker-separator-settings.html.php');
		
		$container = $this->querySelector("#marker-separator-placeholder-icon-picker-container");
		if(class_exists('\\WPGMZA\\MarkerIconPicker'))
		{
			$params = array(
				'name' => 'marker_separator_placeholder_icon'
			);
			
			if(!empty($wpgmza->settings->marker_separator_placeholder_icon))
				$params['value'] = $wpgmza->settings->marker_separator_placeholder_icon;
			
			$markerSeparatorPlaceholderIconPicker = new MarkerIconPicker($params);
			$container->import($markerSeparatorPlaceholderIconPicker);
		}
		else
			$container->appendText(__("Requires WP Google Maps - Pro add-on Version 8", "wp-google-maps"));
		
		$this->handleLegacySettings();
		
		add_action('admin_enqueue_scripts', array($this, 'onAdminEnqueueScripts'));
		add_action('wpgooglemaps_filter_save_settings', array($this, 'onSaveSettings'));
		
		add_filter("wpgmza_global_settings_tabs", array($this, 'onSettingsTabs'), 9, 1);
		add_filter("wpgooglemaps_map_settings_output_bottom", array($this, 'onSettingsOutputBottom'), 10, 2);
		add_filter("wpgmza_global_settings_tab_content", array($this, 'onSettingsTabContent'), 10, 1);
	}
	
	protected function handleLegacySettings()
	{
		$settings = get_option('WPGMZA_OTHER_SETTINGS');
		
		if(!$settings)
			return;
		
		if(isset($settings['marker_separator_algorithm']))
			return;
		
		$settings['marker_separator_algorithm'] = 'circle';
		
		if(!empty($settings['wpgmza_near_vicinity_shape']))
			$settings['marker_separator_algorithm'] = 'spiral';
		
		update_option('WPGMZA_OTHER_SETTINGS', $settings);
	}
	
	public function onSaveSettings($settings)
	{
		foreach($this->querySelectorAll("input, select, textarea") as $input)
		{
			$name = $input->getAttribute('name');
			
			if(!$name)
				continue;
			
			switch($input->nodeName)
			{
				case 'input':
				
					if($input->getAttribute('type') == 'checkbox')
					{
						$settings[$name] = isset($_POST[$name]);
						break;
					}
				
				default:
				
					$settings[$name] = $_POST[$name];
				
					break;
			}
		}
		
		return $settings;
	}
	
	public function onAdminEnqueueScripts()
	{
		global $wpgmza;
		
		if(empty($wpgmza))
			return;
		
		if($wpgmza->getCurrentPage() == Plugin::PAGE_SETTINGS)
			wp_enqueue_script('wpgmza-gold-global-settings', plugin_dir_url(__DIR__) . 'js/global-settings.js');
	}
	
	public function onSettingsTabs($content)
	{
		$content .= "<li style='margin: 0px 3px;'><a href=\"#tabs-marker-separation\">".__("Marker Separation","wp-google-maps")."</a></li>";
		return $content;
	}
	
	public function onSettingsOutputBottom($content, $settings)
	{
		$content .= '<div>' . __('Looking for Near-Vicinity settings? See the new <a href="#tabs-marker-separation">Marker Separation</a> tab.', 'wp-google-maps') . '</div>';
		return $content;
	}
	
	public function onSettingsTabContent($content)
	{
		$settings = get_option('WPGMZA_OTHER_SETTINGS');
		$this->populate($settings);
		
		$content .= $this->html;
		
		if($this->markerLibraryDialog)
			$this->markerLibraryDialog->html();
		
		return $content;
	}
}

$wpgmza_marker_separator_settings = new MarkerSeparatorSettings();
