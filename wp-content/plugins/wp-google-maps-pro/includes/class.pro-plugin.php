<?php

namespace WPGMZA;

if(!defined('WPGMZA_DIR_PATH'))
	return;

// TODO: Might be wise to create an override file rathen than explicitly including there here
// TODO: Research factory design method and autoloaders

wpgmza_require_once(WPGMZA_DIR_PATH . 'includes/class.plugin.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.category.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.marker-icon.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.pro-rest-api.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.pro-marker.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.pro-store-locator.php');
// NB: Moved to Gold 5.0.0
//wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.marker-rating.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.directions-box-settings-panel.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'tables/class.pro-admin-marker-datatable.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . '3rd-party-integration/class.pro-gutenberg.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . '3rd-party-integration/class.marker-source.php');
/*wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.batched-operation.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'import/class.batched-import.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'import/class.csv.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'import/class.json.php');*/

class ProPlugin extends Plugin
{
	private $cachedProVersion;
	
	public function __construct()
	{
		Plugin::__construct();
		
		$this->acfIntegration 					= new \WPGMZA\Integration\ACF();
		$this->toolsetWooCommerceIntegration 	= new \WPGMZA\Integration\ToolsetWooCommerce();
		
		$this->proDatabase = new ProDatabase();
	}
	
	public static function assertClassExists($class)
	{
		if(!class_exists($class))
		{
			if(wpgmza_preload_is_in_developer_mode())
				return false;
			
			add_action('admin_notices', function() {
				
				?>
				<div class="notice notice-error is-dismissible">
					<p>
						<strong>
						<?php
						_e('WP Google Maps', 'wp-google-maps');
						?></strong>:
						<?php
						_e("The Pro add-on failed to assert that the class dependency $class exists. This could be due to truncated or empty PHP scripts in the core plugin. We recommend re-installing WP Google Maps to attempt to solve this issue.", 'wp-google-maps');
						?>
					</p>
				</div>
				<?php
			
			});
			
			return false;
		}
		
		return true;
	}
	
	public static function onActivate()
	{
		require_once(plugin_dir_path(__FILE__) . 'class.pro-database.php');
		
		$db = new ProDatabase();
		$db->install();
	}
	
	public static function onDeactivate()
	{
		
	}
	
	public function getLocalizedData()
	{
		$data = Plugin::getLocalizedData();
		
		$categoryTree = CategoryTree::createInstance();
		
		if(empty($data['ajaxnonce']))
			$data['ajaxnonce'] = wp_create_nonce('wpgmza_ajaxnonce');
		
		return array_merge($data, array(
			'mediaRestUrl'			=> rest_url('/wp/v2/media/'),
			'categoryTreeData'		=> $categoryTree,
			'defaultPreloaderImage'	=> plugin_dir_url(__DIR__) . 'images/AjaxLoader.gif',
			'pro_version' 			=> $this->getProVersion()
		));
	}
	
	public static function getDirectoryURL()
	{
		return plugin_dir_url(__DIR__);
	}
	
	public function isProVersion()
	{
		return true;
	}
	
	public function getProVersion()
	{
		if($this->cachedProVersion != null)
			return $this->cachedProVersion;
		
		$subject = file_get_contents(plugin_dir_path(__DIR__) . 'wp-google-maps-pro.php');
		if(preg_match('/Version:\s*(.+)/', $subject, $m))
			$this->cachedProVersion = trim($m[1]);
		
		return $this->cachedProVersion;
	}
}

add_filter('wpgmza_create_WPGMZA\\Plugin', function() {
	
	return new ProPlugin();
	
}, 10, 0);
