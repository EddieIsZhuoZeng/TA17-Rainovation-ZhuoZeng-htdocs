<?php

/**
 *
 */
class Graph_Lite_Shortcode
{

	public function __construct()
	{
		add_shortcode( 'graph_lite', [$this, 'graph_lite_shortcode'] );
	}

	public function graph_lite_shortcode($atts)
	{
		ob_start();

		extract(shortcode_atts( array(
			'id'	=> '',
		), $atts ));

		$chart_data = unserialize(get_post_meta( $id, 'graphs_lite_data', true ));
		$rand_id = uniqid();

		wp_localize_script( GRAPHS_LITE_NAME, 'gnc_plugin_data_' . $rand_id, [
			'chart_id'   => $id,
			'chart_data' =>	json_encode( $chart_data ),
		] );

		echo '<div data-attr="' . $rand_id . '" class="gnc-plugin-chart_area gnc-plugin-charts"> <canvas id="gnc-plugin-chart-' . $rand_id . '"></canvas></div>';

		return ob_get_clean();
	}
}