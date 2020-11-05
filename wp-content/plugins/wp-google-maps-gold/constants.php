<?php

namespace WPGMZA;

global $wpdb;
global $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES;
global $WPGMZA_TABLE_NAME_RATINGS;
global $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS;

$WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES	= $wpdb->prefix . 'wpgmza_live_tracking_devices';
$WPGMZA_TABLE_NAME_RATINGS					= $wpdb->prefix . 'wpgmza_ratings';
$WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS		= $wpdb->prefix . 'wpgmza_markers_has_ratings';
