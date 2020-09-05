<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://orchestra.ltd
 * @since      1.0.0
 *
 * @package    Graph_Lite
 * @subpackage Graph_Lite/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Graph_Lite
 * @subpackage Graph_Lite/public
 * @author     Orchestra Technologies <ask@orchestra.ltd>
 */
class Graph_Lite_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_public_assets'] );

	}

	/**
	 * Register the JavaScript and Stylesheet for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_public_assets() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/graphs-lite-public.css', array(), $this->version, 'all' );

		wp_enqueue_script( 'Chartjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/graphs-lite-public.js', array( 'jquery' ), $this->version, true );

	}

}
