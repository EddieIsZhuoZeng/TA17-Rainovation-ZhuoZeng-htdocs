<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://orchestra.ltd
 * @since      1.0.0
 *
 * @package    Graph_Lite
 * @subpackage Graph_Lite/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Graph_Lite
 * @subpackage Graph_Lite/includes
 * @author     Orchestra Technologies <ask@orchestra.ltd>
 */
class Graph_Lite_Init
{
	public function __construct()
	{
		add_action( 'init', array($this, 'register_custom_post_type') );
	}

	public function register_custom_post_type() {

		register_post_type( 'graphs_lite', [

              'label'               => __( 'Graphs Light', 'graphs-light' ),
              'public'              => true,
              'show_ui'             => false,
              'show_in_menu'        => false,
              'hierarchical'        => true,
              'query_var'           => true,
              'supports'            => array('title'),
              'publicly_queryable'  => true,
              'exclude_from_search' => false,
              'has_archive'         => true,
              'rewrite'             => true,
              'show_in_admin_bar'   => false,

        ] );
	}

	public static function activate() {

	}

	public static function deactivate() {

	}
}