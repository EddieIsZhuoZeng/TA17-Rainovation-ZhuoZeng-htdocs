<?php

namespace WPGMZA;

class GoldDatabase
{
	public function __construct()
	{
		global $wpgmza;
		global $wpgmza_gold_version;
		
		$this->version = get_option('wpgmza_gold_db_version');
		
		if(version_compare($this->version, $wpgmza_gold_version, '<'))
			$this->install();
	}
	
	public function install()
	{
		global $wpgmza_gold_version;
		
		require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
		
		$this->installRatingsTable();
		$this->installMarkersHasRatingsTable();
		
		MarkerRating::install();
		
		update_option('wpgmza_gold_db_version', $wpgmza_gold_version);
	}
	
	protected function installRatingsTable()
	{
		global $WPGMZA_TABLE_NAME_RATINGS;
		
		$sql = "CREATE TABLE `$WPGMZA_TABLE_NAME_RATINGS` (
			id int(11) NOT NULL AUTO_INCREMENT,
			created DATETIME NOT NULL,
			amount DECIMAL(9,3),
			ip_address VARCHAR(40),
			user_agent TEXT,
			user_guid VARCHAR(36),
			author int(11) NULL,
			PRIMARY KEY  (id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			";

		dbDelta($sql);
	}
	
	protected function installMarkersHasRatingsTable()
	{
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS;
		
		$sql = "CREATE TABLE `$WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS` (
			marker_id int(11) NOT NULL,
			rating_id int(11) NOT NULL,
			PRIMARY KEY  (marker_id, rating_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			";

		dbDelta($sql);
	}
}