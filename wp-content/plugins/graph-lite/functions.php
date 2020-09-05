<?php

function gl_get_graph_index($id) {

	$all_graph_data = get_option( 'graphs_lite_all_data' );

	foreach ($all_graph_data as $key => $value) {

		if ($id == $value['graph_id']) {
			return $key;
		}

	}

	return;

}