<?php

/**
 * The file that defines the crude operation by ajax
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
class Graph_Lite_Ajax
{

	public function __construct()
	{
		add_action( 'wp_ajax_save_chart', [$this, 'save_chart'] );
		add_action( 'wp_ajax_update_chart', [$this, 'update_chart'] );
		add_action( 'wp_ajax_delete_chart', [$this, 'delete_chart'] );
	}

	public function save_chart() {

		$data = $_POST['graph_data'];

		$post_id = wp_insert_post( [
			'post_type'      => 'graphs_lite',
			'post_title'     => $data['title_text'],
			'post_status'    => 'publish',
			'comment_status' => 'closed'
		] );

		update_post_meta( $post_id, 'graphs_lite_data', serialize($data) );

		wp_send_json( $post_id );

	}

	public function update_chart() {

		$post_id  = $_POST['graph_id'];

		if (empty($post_id)) {
			wp_send_json_error( __( 'Post id is not correct', 'graphs-lite' ) );
		}

		$new_data = $_POST['updated_graph_data'];

		wp_update_post( [
			'ID'           => $post_id,
			'post_title'   => $new_data['title_text'],
		] );

		update_post_meta( $post_id, 'graphs_lite_data', serialize($new_data) );

		wp_send_json( $post_id );

	}

	public function delete_chart() {

		wp_delete_post( $_POST['graph_id'], true );

		delete_post_meta( $_POST['graph_id'], 'graphs_lite_data' );

		delete_option( 'graphs_lite_all_data' )[gl_get_graph_index($_POST['graph_id'])];

		wp_send_json( __( 'Graph '.$_POST['graph_id'].' deleted', 'graphs-lite') );

	}
}