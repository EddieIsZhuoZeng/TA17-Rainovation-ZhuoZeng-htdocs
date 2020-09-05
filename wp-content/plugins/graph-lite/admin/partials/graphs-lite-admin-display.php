<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://orchestra.ltd
 * @since      1.0.0
 *
 * @package    Graph_Lite
 * @subpackage Graph_Lite/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<!-- <button type="submit" class="close_graph_modal">X</button> -->

<!-- <div class="gl_nav">
	<nav id="gl_new_chart">Add New Chart</nav>
	<nav id="gl_old_chart">Charts</nav>
</div> -->

<?php include plugin_dir_path( __FILE__ ). 'graphs-lite-admin-new-chart.php'; ?>

<?php //include plugin_dir_path( __FILE__ ). 'graphs-lite-admin-old-chart.php'; ?>