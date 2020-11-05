<?php

namespace WPGMZA;

$dir = wpgmza_get_basic_dir();

wpgmza_require_once($dir . 'includes/class.factory.php');
wpgmza_require_once($dir . 'includes/class.crud.php');
wpgmza_require_once($dir . 'includes/class.map.php');

class ProMap extends Map
{
	protected $_proSettingsMigrator;
	protected $_directionsBox;
	protected $_storeLocator;
	protected $_categoryTree;
	protected $_categoryFilterWidget;
	
	public function __construct($id_or_fields=-1, $overrides=null)
	{
		global $wpgmza;
		
		Map::__construct($id_or_fields, $overrides);
		
		$this->_proSettingsMigrator = new ProSettingsMigrator();
		$this->_proSettingsMigrator->migrateMapSettings($this);
		
		$base = plugin_dir_url( wpgmza_get_basic_dir() . 'wp-google-maps.php' );
		
		// TODO: Check carousel style
		wp_enqueue_script('owl-carousel', 						$base . 'lib/owl.carousel.js', array('jquery'), $wpgmza->getProVersion());
		wp_enqueue_style('owl-carousel_style',					$base . 'lib/owl.carousel.min.css', array(), $wpgmza->getProVersion());
		// wp_enqueue_style('owl-carousel_style_theme',			$base . 'lib/owl.theme.css', array(), $wpgmza->getProVersion());
		wp_enqueue_style('owl-carousel_style__default_theme',	$base . 'lib/owl.theme.default.min.css', array(), $wpgmza->getProVersion());
		
		$base = plugin_dir_url(__DIR__);
		
		wp_enqueue_script('featherlight',				$base . 'lib/featherlight.min.js', array('jquery'), $wpgmza->getProVersion());
		wp_enqueue_style('featherlight',				$base . 'lib/featherlight.min.css', array(), $wpgmza->getProVersion());
		
		// wp_enqueue_script('polylabel',					$base . 'lib/polylabel.js', array(), $wpgmza->getProVersion());
		wp_enqueue_script('polyline',					$base . 'lib/polyline.js', array(), $wpgmza->getProVersion());
		
		if($this->isDirectionsEnabled())
			$this->_directionsBox = new DirectionsBox($this);
		
		if(is_admin() && !empty($this->fusion))
		{
			add_action('admin_notices', function() {
				
				?>
				
				<div class="notice notice-error is-dismissible">
					<p>
						<?php
						_e('<strong>WP Google Maps:</strong> Fusion Tables are deprecated and will be turned off as of December the 3rd, 2019. Google Maps will no longer support Fusion Tables from this date forward.', 'wp-google-maps');
						?>
					</p>
				</div>
				
				<?php
				
			});
		}
		
		$this->_categoryTree = CategoryTree::createInstance($this);
		$this->_categoryFilterWidget = CategoryFilterWidget::createInstance($this);
		
		$this->onInit();
	}
	
	public function __get($name)
	{
		switch($name)
		{
			case "directionsBox":
			case "storeLocator":
			case "categoryTree":
			case "categoryFilterWidget":
				return $this->{"_$name"};
				break;
				
			case "mashupIDs":
				
				if(empty($this->shortcodeAttributes['mashup_ids']))
					return array();
				
				$ids = explode(",", $this->shortcodeAttributes['mashup_ids']);
				
				return array_map('intval', $ids);
			
				break;
		}
		
		return Map::__get($name);
	}
	
	public function isStoreLocatorEnabled()
	{
		return $this->store_locator_enabled == "1";
	}
	
	public function isDirectionsEnabled()
	{
		global $wpgmza;
		
		if($this->directions_enabled == "1")
			return true;
		
		if(!empty($this->overrides['enable_directions']))
			return true;
		
		return false;
	}

	protected function getMarkersQuery()
	{
		global $wpdb, $WPGMZA_TABLE_NAME_MARKERS;
		
		$columns = array();
		
		foreach($wpdb->get_col("SHOW COLUMNS FROM $WPGMZA_TABLE_NAME_MARKERS") as $name)
		{
			switch($name)
			{
				case "icon":
					$columns[] = ProMarker::getIconSQL($this->id);
					break;
				
				default:
					$columns[] = $name;
					break;
			}
		}
		
		$stmt = $wpdb->prepare("SELECT " . implode(", ", $columns) . " FROM $WPGMZA_TABLE_NAME_MARKERS WHERE approved=1 AND map_id=%d", array($this->id));
		
		return $stmt;
	}
	
	public function trash()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_POLYGONS;
		global $WPGMZA_TABLE_NAME_POLYLINES;
		global $WPGMZA_TABLE_NAME_HEATMAPS;
		global $WPGMZA_TABLE_NAME_CIRCLES;
		global $WPGMZA_TABLE_NAME_RECTANGLES;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;
		global $WPGMZA_TABLE_NAME_CATEGORY_MAPS;
		
		$types = array(
			$WPGMZA_TABLE_NAME_MARKERS							=> 'WPGMZA\\Marker',
			$WPGMZA_TABLE_NAME_POLYGONS							=> null,
			$WPGMZA_TABLE_NAME_POLYLINES						=> null,
			$WPGMZA_TABLE_NAME_HEATMAPS							=> null,
			$WPGMZA_TABLE_NAME_CIRCLES							=> null,
			$WPGMZA_TABLE_NAME_RECTANGLES						=> null,
			$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS	=> null,	
			$WPGMZA_TABLE_NAME_CATEGORY_MAPS					=> null
		);
		
		foreach($types as $table => $class)
		{
			if($class && class_exists($class))
			{
				$stmt = $wpdb->prepare("SELECT id FROM $table WHERE map_id=%d", $this->id);
				$ids = $wpdb->get_col($stmt);
				
				foreach($ids as $id)
				{
					$instance = $class::createInstance($id);
					$instance->trash();
				}
			}
			else
			{
				$stmt = $wpdb->prepare("DELETE FROM $table WHERE map_id=%d", array($this->id));
				$wpdb->query($stmt);
			}
		}
		
		Map::trash();
	}
	
	public function duplicate()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_POLYGONS;
		global $WPGMZA_TABLE_NAME_POLYLINES;
		global $WPGMZA_TABLE_NAME_HEATMAPS;
		global $WPGMZA_TABLE_NAME_CIRCLES;
		global $WPGMZA_TABLE_NAME_RECTANGLES;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;
		global $WPGMZA_TABLE_NAME_CATEGORY_MAPS;
		
		$newMap = Map::duplicate();
		
		// TODO: 8.1.0 - Make this function dynamically detect feature types and iterate over them
		$types = array(
			$WPGMZA_TABLE_NAME_MARKERS							=> 'WPGMZA\\Marker',
			$WPGMZA_TABLE_NAME_POLYGONS							=> null,
			$WPGMZA_TABLE_NAME_POLYLINES						=> null,
			$WPGMZA_TABLE_NAME_HEATMAPS							=> null,
			$WPGMZA_TABLE_NAME_CIRCLES							=> null,
			$WPGMZA_TABLE_NAME_RECTANGLES						=> null,
			$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS	=> null,	
			$WPGMZA_TABLE_NAME_CATEGORY_MAPS					=> null
		);
		
		foreach($types as $table => $class)
		{
			if($class && class_exists($class))
			{
				$stmt = $wpdb->prepare("SELECT id FROM $table WHERE map_id=%d", $this->id);
				$ids = $wpdb->get_col($stmt);
				
				foreach($ids as $id)
				{
					$instance = $class::createInstance($id);
					
					$newFeature = $instance->duplicate();
					$newFeature->map_id = $newMap->id;
				}
			}
			else
			{
				$stmt = $wpdb->prepare("SELECT * FROM $table WHERE map_id=%d", array($this->id));
				$data = $wpdb->get_results($stmt);
				
				foreach($data as $obj)
				{
					$arr = (array)$obj;
					
					$src_id = $arr['id'];
					unset($arr['id']);
					
					$columns = array_keys($arr);
					$imploded = implode(',', $columns);
					
					$qstr = "INSERT INTO $table ($imploded) SELECT $imploded FROM $table WHERE id = %d";
					$stmt = $wpdb->prepare($qstr, array($src_id));
					$wpdb->query($stmt);
					
					$qstr = "UPDATE $table SET map_id = %d WHERE id = %d";
					$stmt = $wpdb->prepare($qstr, array($newMap->id, $wpdb->insert_id));
					$wpdb->query($stmt);
				}
			}
		}
		
		return $newMap;
	}
}

add_filter('wpgmza_create_WPGMZA\\Map', function($id_or_fields, $overrides=null) {
	
	return new ProMap($id_or_fields, $overrides);
	
}, 10, 2);
