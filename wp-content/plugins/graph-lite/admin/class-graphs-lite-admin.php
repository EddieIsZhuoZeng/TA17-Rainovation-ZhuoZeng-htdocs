<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://orchestra.ltd
 * @since      1.0.0
 *
 * @package    Graphs_Lite
 * @subpackage Graphs_Lite/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Graphs_Lite
 * @subpackage Graphs_Lite/admin
 * @author     Orchestra Technologies <ask@orchestra.ltd>
 */
class Graph_Lite_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// add_action('admin_menu', array( $this, 'setting_page' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets') );
		add_action( 'admin_head', array( $this, 'mce_button' ) );
		add_action( 'admin_head', array( $this, 'global_data' ) );
		add_action( 'add_meta_boxes', [ $this, 'adding_custom_meta_boxes' ], 10, 2 );
		add_action( 'init', [$this, 'graphs_data_store'], 9 );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_assets() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Graph_Lite_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Graph_Lite_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'sweet_modal', plugin_dir_url( __FILE__ ) . 'css/jquery.sweet-modal.min.css', array(), '1.3.3' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/graphs-lite-admin.css', array(), $this->version, 'all' );

		// wp_enqueue_script( 'Vuejs', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script( 'Chartjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script( 'sweet_modal', plugin_dir_url( __FILE__ ) . 'js/jquery.sweet-modal.min.js', array( 'jquery' ), '1.3.3', true );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/graphs-lite-admin.js', array( 'jquery' ), $this->version, true );

		wp_localize_script( $this->plugin_name, 'gl', [
			'all_graphs'      =>	get_option('graphs_lite_all_data', true),
			'ajax_url'        =>	admin_url( 'admin-ajax.php' ),
			'save_ajax_url'   =>	admin_url( 'admin-ajax.php?action=save_chart' ),
			'update_ajax_url' =>	admin_url( 'admin-ajax.php?action=update_chart' ),
			'delete_ajax_url' =>	admin_url( 'admin-ajax.php?action=delete_chart' ),
			'admin_dir_url'   =>	plugin_dir_url( __FILE__ ),
		] );

		wp_enqueue_script( 'graphs-light-response', plugin_dir_url( __FILE__ ) . 'assets/graphs-lite-admin-response.js', array( 'jquery' ), $this->version, true );

		// wp_enqueue_style( 'graphs-light-npm-style', plugin_dir_url( __FILE__ ) . 'css/style.css' );

		// wp_enqueue_script( 'graphs-light-npm-js', plugin_dir_url( __FILE__ ) . 'js/scripts.js', array(), false, true );

	}

	// public function setting_page() {

	// 	add_options_page('Graph Lite', 'Graph Lite', 'manage_options', 'gl-admin-dashboard', array( $this, 'admin_dashboard' ));

	// }

	/**
	 * Register new button in TinyMCE
	 *
	 * @return array
	 */
	public function register_mce_button( $buttons ) {

		array_push( $buttons, 'graphs_lite_mce_btn' );

		return $buttons;
	}

	/**
	 * Adding button to TinyMCE
	 *
	 * @return void
	 */
	public function mce_button() {

		// check user permissions
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
			return;
		}

		// check if WYSIWYG is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'button_for_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_mce_button' ) );
		}

	}

	/**
	 * Declareing script for new button
	 *
	 * @return array
	 *
	 */
	public function button_for_tinymce_plugin( $plugin_array ) {

		$plugin_array['graphs_lite_mce_btn'] = plugins_url( '/js/tinyMCE_hooks.js', __FILE__ );

		return $plugin_array;
	}


	public function adding_custom_meta_boxes( $post_type, $post ) {
	    add_meta_box(
	        'gl-admin-meta-box',
	        __( 'Graph Light', 'graphs-light' ),
	        [$this, 'render_graph_light_admin_metabox'],
	        array('post','page'),
	        'normal',
	        'default'
	    );
	}

	public function graphs_data_store() {

		$args = [
			'post_type'      =>	'graphs_lite',
			'posts_per_page' => -1,
		];

		$graphs = get_posts( $args );

		$graphs_data = [];

		foreach ($graphs as $key => $graph) {

			$get_graph_meta = unserialize( get_post_meta( $graph->ID, 'graphs_lite_data', true ) );

			$get_graph_meta['graph_id'] = $graph->ID;

			array_push($graphs_data, $get_graph_meta);

		}

		update_option( 'graphs_lite_all_data', $graphs_data );

	}

	public function render_graph_light_admin_metabox(){

		include plugin_dir_path( __FILE__ ) . '/partials/graphs-lite-admin-display.php';

	}

	public function global_data() {

		$all_graph_data = array_reverse( get_option( 'graphs_lite_all_data', true ) ); ?>

		<script>
		var global_chart_data = <?php echo json_encode( $all_graph_data );?>;
		function gl_findAndReplace(object, value, replacevalue){
		  for(var x in object){
		    if(typeof object[x] == typeof {}){
		      gl_findAndReplace(object[x], value, replacevalue);
		    }
		    if(object[x] == value){
		      object[x] = replacevalue;
		      // break;
		    }
		  }
		}

		gl_findAndReplace(global_chart_data, 'true', true);
		gl_findAndReplace(global_chart_data, 'false', false);

		// var ctx = document.getElementById("Chart");
		// new Chart(ctx, global_chart_data); </script> <?php
	}

}