<?php

/**
 * Graphs & Charts
 *
 * A very user-friendly WordPress plugin to create beautiful, interactive charts and graphs with supports for 8 commonly 
 * used chart types. No lock-ins or paid upgrades necessary. 
 *
 * @link              https://developerhero.net
 * @since             1.0.0
 * @package           Graph_Lite
 *
 * Plugin Name:       Graphs & Charts
 * Plugin URI:        https://wordpress.org/plugins/graphs-lite
 * Description:       A very user-friendly WordPress plugin to create beautiful, interactive charts and graphs. 
 * It has an interactive builder to show you live updates of the charts as you enter the data. 
 * The plugin supports 8 commonly used chart types. 
 * It's completely free to use, well-designed and no paid upgrades needed. Supported chart types are pie, doughnut, polar, bar, line, radar, bubble and scatter chart. 
 * 
 * Version:           2.0.8
 * Author:            Developer Hero
 * Author URI:        https://developerhero.net
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       graphs-lite
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'GRAPHS_LITE_VERSION', '2.0.8' );
define( 'GRAPHS_LITE_NAME', 'Graphs & Charts' );

require plugin_dir_path( __FILE__ ) . 'includes/class-graphs-lite-init.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-graphs-lite-activator.php
 */
function activate_graphs_lite() {
	Graph_Lite_Init::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-graphs-lite-deactivator.php
 */
function deactivate_graphs_lite() {
	Graph_Lite_Init::deactivate();
}

register_activation_hook( __FILE__, 'activate_graphs_lite' );
register_deactivation_hook( __FILE__, 'deactivate_graphs_lite' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'functions.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-ajax.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-shortcode.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-graphs-lite-admin.php';
require plugin_dir_path( __FILE__ ) . 'public/class-graphs-lite-public.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function graph_light_run() {
	new Graph_Lite_Init;
	new Graph_Lite_Ajax;
	new Graph_Lite_Shortcode;
	new Graph_Lite_Admin( GRAPHS_LITE_NAME, GRAPHS_LITE_VERSION );
	new Graph_Lite_Public( GRAPHS_LITE_NAME, GRAPHS_LITE_VERSION );
}

graph_light_run();