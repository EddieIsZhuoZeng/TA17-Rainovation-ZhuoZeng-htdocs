<?php
/**
 * Plugin Name: Lana Downloads Manager
 * Plugin URI: http://lana.codes/lana-product/lana-downloads-manager/
 * Description: Downloads Manager with counter and log.
 * Version: 1.4.0
 * Author: Lana Codes
 * Author URI: http://lana.codes/
 * Text Domain: lana-downloads-manager
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();
define( 'LANA_DOWNLOADS_MANAGER_VERSION', '1.4.0' );
define( 'LANA_DOWNLOADS_MANAGER_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'LANA_DOWNLOADS_MANAGER_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Language
 * load
 */
load_plugin_textdomain( 'lana-downloads-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Add plugin action links
 *
 * @param $links
 *
 * @return mixed
 */
function lana_downloads_manager_add_plugin_action_links( $links ) {

	$settings_url = esc_url( admin_url( 'edit.php?post_type=lana_download&page=lana-downloads-manager-settings' ) );

	/** add settings link */
	$settings_link = sprintf( '<a href="%s">%s</a>', $settings_url, __( 'Settings', 'lana-downloads-manager' ) );
	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'lana_downloads_manager_add_plugin_action_links' );

/**
 * Lana Download Widget
 */
add_action( 'widgets_init', function () {
	include_once LANA_DOWNLOADS_MANAGER_DIR_PATH . '/includes/class-lana-download-widget.php';
	register_widget( 'Lana_Download_Widget' );
} );

/**
 * Install Lana Downloads Manager
 * - create dir
 * - create log table
 */
function lana_downloads_manager_install() {
	lana_downloads_manager_create_upload_directory();
	lana_downloads_manager_create_logs_table();
}

register_activation_hook( __FILE__, 'lana_downloads_manager_install' );

/**
 * Create logs table
 */
function lana_downloads_manager_create_logs_table() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();
	$table_name      = $wpdb->prefix . 'lana_downloads_manager_logs';

	/** create table */
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . $table_name . " (
	  id bigint(20) NOT NULL auto_increment,
	  user_id bigint(20) DEFAULT NULL,
	  user_ip varchar(255) NOT NULL,
	  user_agent varchar(255) NOT NULL,
	  download_id bigint(20) NOT NULL,
	  download_date datetime DEFAULT NULL,
	  PRIMARY KEY (id),
	  KEY attribute_name (download_id)
	) " . $charset_collate . ";" );
}

/**
 * Create upload directory
 */
function lana_downloads_manager_create_upload_directory() {

	$upload_dir = wp_upload_dir();

	$files = array(
		array(
			'base'    => $upload_dir['basedir'] . '/lana-downloads',
			'file'    => '.htaccess',
			'content' => lana_downloads_manager_get_upload_directory_htaccess()
		),
		array(
			'base'    => $upload_dir['basedir'] . '/lana-downloads',
			'file'    => 'index.php',
			'content' => ''
		)
	);

	foreach ( $files as $file ) {
		if ( wp_mkdir_p( $file['base'] ) ) {
			if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
				fwrite( $file_handle, $file['content'] );
				fclose( $file_handle );
			}
		}
	}

	flush_rewrite_rules();
}

/**
 * Get upload directory .htaccess
 * @return string
 */
function lana_downloads_manager_get_upload_directory_htaccess() {

	$htaccess = 'deny from all' . PHP_EOL;
	$htaccess .= '<FilesMatch "\.(jpg|jpeg|png|gif)$">' . PHP_EOL;
	$htaccess .= '  allow from all' . PHP_EOL;
	$htaccess .= '  RewriteEngine On' . PHP_EOL;
	$htaccess .= '  RewriteRule .*$ ' . get_bloginfo( 'url' ) . '/wp-includes/images/media/default.png [L]' . PHP_EOL;
	$htaccess .= '</FilesMatch>' . PHP_EOL;

	return $htaccess;
}

/**
 * Upload dir
 *
 * @param $param
 *
 * @return mixed
 */
function lana_downloads_manager_upload_dir( $param ) {

	if ( isset( $_POST['type'] ) && 'lana_download' === $_POST['type'] ) {
		if ( empty( $param['subdir'] ) ) {
			$param['path']   = $param['path'] . '/lana-downloads';
			$param['url']    = $param['url'] . '/lana-downloads';
			$param['subdir'] = '/lana-downloads';
		} else {
			$new_subdir = '/lana-downloads' . $param['subdir'];

			$param['path']   = str_replace( $param['subdir'], $new_subdir, $param['path'] );
			$param['url']    = str_replace( $param['subdir'], $new_subdir, $param['url'] );
			$param['subdir'] = str_replace( $param['subdir'], $new_subdir, $param['subdir'] );
		}
	}

	return $param;
}

add_filter( 'upload_dir', 'lana_downloads_manager_upload_dir' );

/**
 * Add Lana Downloads Manager
 * add query vars
 *
 * @param $vars
 *
 * @return array
 */
function lana_downloads_manager_add_query_vars( $vars ) {
	$vars[] = get_option( 'lana_downloads_manager_endpoint', 'download' );
	$vars[] = get_option( 'lana_downloads_manager_post_type_endpoint', 'lana-download' );
	$vars[] = get_option( 'lana_downloads_manager_category_endpoint', 'download-category' );

	return $vars;
}

add_filter( 'query_vars', 'lana_downloads_manager_add_query_vars', 0 );

/**
 * Add Lana Downloads Manager
 * add rewrite endpoint
 */
function lana_downloads_manager_add_rewrite() {
	add_rewrite_endpoint( get_option( 'lana_downloads_manager_endpoint', 'download' ), EP_ALL );
	add_rewrite_endpoint( get_option( 'lana_downloads_manager_post_type_endpoint', 'lana-download' ), EP_ALL );
	add_rewrite_endpoint( get_option( 'lana_downloads_manager_category_endpoint', 'download-category' ), EP_ALL );
	flush_rewrite_rules();
}

add_action( 'init', 'lana_downloads_manager_add_rewrite', 0 );

/**
 * Add Lana Downloads Manager
 * custom wp roles
 */
function lana_downloads_manager_custom_wp_roles() {

	/**
	 * Administrator
	 * role
	 */
	$administrator_role = get_role( 'administrator' );

	if ( is_a( $administrator_role, 'WP_Role' ) ) {
		$administrator_role->add_cap( 'manage_lana_download_logs' );
	}
}

add_action( 'admin_init', 'lana_downloads_manager_custom_wp_roles' );

/**
 * Add Lana Downloads Manager
 * custom post type
 */
function lana_downloads_manager_custom_post_type() {

	/**
	 * Lana Download
	 * default args
	 */
	$lana_download_post_type_args = array(
		'labels'            => array(
			'all_items'          => __( 'All Downloads', 'lana-downloads-manager' ),
			'name'               => __( 'Downloads', 'lana-downloads-manager' ),
			'singular_name'      => __( 'Download', 'lana-downloads-manager' ),
			'add_new'            => __( 'Add New', 'lana-downloads-manager' ),
			'add_new_item'       => __( 'Add Download', 'lana-downloads-manager' ),
			'edit'               => __( 'Edit', 'lana-downloads-manager' ),
			'edit_item'          => __( 'Edit Download', 'lana-downloads-manager' ),
			'new_item'           => __( 'New Download', 'lana-downloads-manager' ),
			'view'               => __( 'View Download', 'lana-downloads-manager' ),
			'view_item'          => __( 'View Download', 'lana-downloads-manager' ),
			'search_items'       => __( 'Search Downloads', 'lana-downloads-manager' ),
			'not_found'          => __( 'No Downloads found', 'lana-downloads-manager' ),
			'not_found_in_trash' => __( 'No Downloads found in trash', 'lana-downloads-manager' ),
			'parent'             => __( 'Parent Download', 'lana-downloads-manager' )
		),
		'description'       => 'Create and manage downloads for your site.',
		'menu_icon'         => 'dashicons-download',
		'show_ui'           => true,
		'capability_type'   => 'post',
		'hierarchical'      => false,
		'supports'          => array(
			'title',
			'editor',
			'thumbnail'
		),
		'show_in_nav_menus' => false,
		'rewrite'           => array(
			'slug' => get_option( 'lana_downloads_manager_post_type_endpoint', 'lana-download' )
		),
		'query_var'         => get_option( 'lana_downloads_manager_post_type_endpoint', 'lana-download' )
	);

	/**
	 * Lana Download Category
	 * default args
	 */
	$lana_download_category_taxonomy_args = array(
		'hierarchical'      => true,
		'labels'            => array(
			'name'          => __( 'Categories', 'lana-downloads-manager' ),
			'singular_name' => __( 'Category', 'lana-downloads-manager' )
		),
		'show_ui'           => true,
		'show_admin_column' => true,
		'rewrite'           => array(
			'slug' => get_option( 'lana_downloads_manager_category_endpoint', 'download-category' )
		),
		'query_var'         => get_option( 'lana_downloads_manager_category_endpoint', 'download-category' )
	);

	/**
	 * Lana Download
	 * public args
	 */
	if ( get_option( 'lana_downloads_manager_public', true ) == true ) {

		$public_args = array(
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true
		);

		$lana_download_post_type_args         = array_merge( $lana_download_post_type_args, $public_args );
		$lana_download_category_taxonomy_args = array_merge( $lana_download_category_taxonomy_args, $public_args );
	}

	/**
	 * Lana Download
	 * not public args
	 */
	if ( get_option( 'lana_downloads_manager_public', true ) == false ) {

		$non_public_args = array(
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false
		);

		$lana_download_post_type_args         = array_merge( $lana_download_post_type_args, $non_public_args );
		$lana_download_category_taxonomy_args = array_merge( $lana_download_category_taxonomy_args, $non_public_args );
	}

	/**
	 * Filter
	 * args
	 */
	$lana_download_post_type_args         = apply_filters( 'lana_downloads_manager_lana_download_post_type_args', $lana_download_post_type_args );
	$lana_download_category_taxonomy_args = apply_filters( 'lana_downloads_manager_lana_download_category_taxonomy_args', $lana_download_category_taxonomy_args );

	/**
	 * Lana Download
	 */
	register_post_type( 'lana_download', $lana_download_post_type_args );

	/**
	 * Lana Download Category
	 */
	register_taxonomy( 'lana_download_category', array( 'lana_download' ), $lana_download_category_taxonomy_args );
}

add_action( 'init', 'lana_downloads_manager_custom_post_type' );

/**
 * Lana Downloads Manager
 * update custom post type
 */
function lana_downloads_manager_update_custom_post_type() {
	global $wpdb, $wp_rewrite;

	$lana_downloads_manager_post_type = get_option( 'lana_downloads_manager_post_type', '' );

	if ( 'lana_download' == $lana_downloads_manager_post_type ) {
		return;
	}

	/**
	 * Update post type
	 */
	$post_type_changes = array( 'lana-download' => 'lana_download' );

	foreach ( $post_type_changes as $post_type_from => $post_type_to ) {
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->posts . " SET post_type = REPLACE(post_type, %s, %s) WHERE post_type LIKE %s", $post_type_from, $post_type_to, $post_type_from ) );
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->posts . " SET guid = REPLACE(guid, %s, %s) WHERE guid LIKE %s", 'post_type=' . $post_type_from, 'post_type=' . $post_type_to, '%post_type=' . $post_type_to . '%' ) );
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->posts . " SET guid = REPLACE(guid, %s, %s) WHERE guid LIKE %s", '/' . $post_type_from . '/', '/' . $post_type_to . '/', '%/' . $post_type_from . '/%' ) );

		/** WPML compatibility */
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->options . " SET option_value = REPLACE(option_value, '%s', '%s') WHERE option_name LIKE 'icl_sitepress_settings'", '"' . $post_type_from . '"', '"' . $post_type_to . '"' ) );
	}

	/**
	 * Update post meta
	 */
	$post_meta_changes = array(
		'_lana_download_file_url' => 'lana_download_file_url',
		'_lana_download_file_id'  => 'lana_download_file_id',
		'_lana_download_count'    => 'lana_download_count'
	);

	foreach ( $post_meta_changes as $post_meta_from => $post_meta_to ) {
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->postmeta . " SET meta_key = REPLACE(meta_key, %s, %s) WHERE meta_key LIKE %s", $post_meta_from, $post_meta_to, $post_meta_from ) );
	}

	/**
	 * Update taxonomy
	 */
	$taxonomy_changes = array( 'lana-download-category' => 'lana_download_category' );

	foreach ( $taxonomy_changes as $taxonomy_from => $taxonomy_to ) {
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->term_taxonomy . " SET taxonomy = REPLACE(taxonomy, %s, %s) WHERE taxonomy LIKE %s", $taxonomy_from, $taxonomy_to, $taxonomy_from ) );

		/** WPML compatibility */
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->options . " SET option_value = REPLACE(option_value, '%s', '%s') WHERE option_name LIKE 'icl_sitepress_settings'", '"' . $taxonomy_from . '"', '"' . $taxonomy_to . '"' ) );
	}

	/** rewrite flush */
	$wp_rewrite->flush_rules();

	update_option( 'lana_downloads_manager_post_type', 'lana_download' );
}

add_action( 'init', 'lana_downloads_manager_update_custom_post_type' );

/**
 * lana download post type
 * add columns
 *
 * @param $columns
 *
 * @return array
 */
function lana_downloads_manager_add_lana_download_post_type_columns( $columns ) {
	$column_meta = array(
		'url'       => __( 'URL', 'lana-downloads-manager' ),
		'shortcode' => __( 'Shortcode', 'lana-downloads-manager' ),
		'count'     => __( 'Download Count', 'lana-downloads-manager' )
	);
	$columns     = array_slice( $columns, 0, 2, true ) + $column_meta + array_slice( $columns, 2, null, true );

	/** counter */
	$lana_downloads_manager_counter = get_option( 'lana_downloads_manager_counter', true );

	/** remove counter if disabled */
	if ( ! $lana_downloads_manager_counter ) {
		unset( $columns['count'] );
	}

	return $columns;
}

add_filter( 'manage_lana_download_posts_columns', 'lana_downloads_manager_add_lana_download_post_type_columns' );

/**
 * lana download post type
 * add data for columns
 *
 * @param $column
 * @param $post_id
 */
function lana_downloads_manager_add_data_lana_download_post_type_columns( $column, $post_id ) {

	switch ( $column ) {
		case 'url':
			echo '<input type="text" class="lana-download-url" value="' . esc_attr( lana_downloads_manager_get_download_url( $post_id ) ) . '" readonly>';
			break;
		case 'shortcode':
			echo '<input type="text" class="lana-download-shortcode" value="' . esc_attr( lana_downloads_manager_get_download_shortcode( $post_id ) ) . '" readonly>';
			break;
		case 'count':
			echo lana_downloads_manager_get_download_count();
			break;
	}
}

add_action( 'manage_lana_download_posts_custom_column', 'lana_downloads_manager_add_data_lana_download_post_type_columns', 10, 2 );

/**
 * lana download post type
 * sortable columns
 *
 * @param $columns
 *
 * @return mixed
 */
function lana_downloads_manager_sortable_lana_download_post_type_columns( $columns ) {
	$columns['count'] = 'lana_download_count';

	return $columns;
}

add_filter( 'manage_edit-lana_download_sortable_columns', 'lana_downloads_manager_sortable_lana_download_post_type_columns' );

/**
 * Lana Downloads Manager
 * order query by count
 *
 * @param WP_Query $query
 */
function lana_downloads_manager_order_query_by_count( $query ) {
	global $pagenow, $typenow;

	if ( 'edit.php' != $pagenow ) {
		return;
	}

	if ( 'lana_download' != $typenow ) {
		return;
	}

	$orderby = $query->get( 'orderby' );

	if ( 'lana_download_count' == $orderby ) {
		$query->set( 'orderby', 'meta_value_num' );

		$query->set( 'meta_query', array(
			'relation' => 'OR',
			array(
				'key'     => 'lana_download_count',
				'compare' => 'NOT EXISTS'
			),
			array(
				'key'     => 'lana_download_count',
				'compare' => 'EXISTS'
			)
		) );
	}
}

add_action( 'pre_get_posts', 'lana_downloads_manager_order_query_by_count' );

/**
 * Lana Downloads Manager
 * download category filter
 */
function lana_downloads_manager_restrict_listings_by_download_category() {
	global $typenow;

	if ( 'lana_download' != $typenow ) {
		return;
	}

	$args = array(
		'show_option_all'  => __( 'All Categories', 'lana-downloads-manager' ),
		'show_option_none' => __( 'None', 'lana-downloads-manager' ),
		'name'             => 'lana_download_category',
		'taxonomy'         => 'lana_download_category'
	);

	/** selected */
	if ( isset( $_GET['lana_download_category'] ) ) {
		$args['selected'] = $_GET['lana_download_category'];
	}

	wp_dropdown_categories( $args );
}

add_action( 'restrict_manage_posts', 'lana_downloads_manager_restrict_listings_by_download_category' );

/**
 * Lana Downloads Manager
 * filter query by download category
 *
 * @param WP_Query $query
 */
function lana_downloads_manager_filter_query_by_download_category( $query ) {
	global $pagenow, $typenow;

	if ( 'edit.php' != $pagenow ) {
		return;
	}

	if ( 'lana_download' != $typenow ) {
		return;
	}

	if ( ! isset( $_GET['lana_download_category'] ) ) {
		return;
	}

	$lana_download_category = intval( $_GET['lana_download_category'] );

	$tax_query = (array) $query->get( 'tax_query' );

	/** default tax query */
	if ( empty( $tax_query ) ) {
		$tax_query = array();
	}

	/**
	 * All Category
	 * default query
	 */
	if ( 0 == $lana_download_category ) {
		return;
	}

	/**
	 * None Category
	 * custom tax query
	 */
	if ( - 1 == $lana_download_category ) {

		$tax_query[] = array(
			'taxonomy' => 'lana_download_category',
			'operator' => 'NOT EXISTS'
		);

		$query->set( 'tax_query', $tax_query );

		return;
	}

	$tax_query[] = array(
		'taxonomy' => 'lana_download_category',
		'field'    => 'term_id',
		'terms'    => $lana_download_category
	);

	$query->set( 'tax_query', $tax_query );
}

add_action( 'pre_get_posts', 'lana_downloads_manager_filter_query_by_download_category' );

/**
 * Lana Downloads Manager
 * load styles
 */
function lana_downloads_manager_styles() {

	wp_register_style( 'lana-downloads-manager', LANA_DOWNLOADS_MANAGER_DIR_URL . '/assets/css/lana-downloads-manager.css', array(), LANA_DOWNLOADS_MANAGER_VERSION );
	wp_enqueue_style( 'lana-downloads-manager' );
}

add_action( 'wp_enqueue_scripts', 'lana_downloads_manager_styles' );

/**
 * Lana Downloads Manager
 * load admin styles
 */
function lana_downloads_manager_admin_styles() {

	wp_register_style( 'lana-downloads-manager-admin', LANA_DOWNLOADS_MANAGER_DIR_URL . '/assets/css/lana-downloads-manager-admin.css', array(), LANA_DOWNLOADS_MANAGER_VERSION );
	wp_enqueue_style( 'lana-downloads-manager-admin' );
}

add_action( 'admin_enqueue_scripts', 'lana_downloads_manager_admin_styles' );

/**
 * Lana Downloads Manager
 * load admin scripts
 */
function lana_downloads_manager_admin_scripts() {

	/** lana downloads manager admin js */
	wp_register_script( 'lana-downloads-manager-admin', LANA_DOWNLOADS_MANAGER_DIR_URL . '/assets/js/lana-downloads-manager-admin.js', array( 'jquery' ), LANA_DOWNLOADS_MANAGER_VERSION );
	wp_enqueue_script( 'lana-downloads-manager-admin' );
}

add_action( 'admin_enqueue_scripts', 'lana_downloads_manager_admin_scripts' );

/**
 * Lana Downloads Manager
 * add admin page
 */
function lana_downloads_manager_admin_menu() {
	global $lana_downloads_manager_logs_page;

	/** Logs page */
	$lana_downloads_manager_logs_page = add_submenu_page( 'edit.php?post_type=lana_download', __( 'Logs', 'lana-downloads-manager' ), __( 'Logs', 'lana-downloads-manager' ), 'manage_lana_download_logs', 'lana-downloads-manager-logs', 'lana_downloads_manager_logs' );

	/** add screen options */
	add_action( 'load-' . $lana_downloads_manager_logs_page, 'lana_downloads_manager_logs_page_screen_options' );

	/** Settings page */
	add_submenu_page( 'edit.php?post_type=lana_download', __( 'Settings', 'lana-downloads-manager' ), __( 'Settings', 'lana-downloads-manager' ), 'manage_options', 'lana-downloads-manager-settings', 'lana_downloads_manager_settings' );

	/** call register settings function */
	add_action( 'admin_init', 'lana_downloads_manager_register_settings' );
}

add_action( 'admin_menu', 'lana_downloads_manager_admin_menu', 12 );

/**
 * Lana Downloads Manager
 * logs page screen options - add per page option
 */
function lana_downloads_manager_logs_page_screen_options() {
	global $lana_downloads_manager_logs_page;

	$screen = get_current_screen();

	if ( $screen->id != $lana_downloads_manager_logs_page ) {
		return;
	}

	$args = array(
		'label'   => __( 'Logs per page', 'lana-downloads-manager' ),
		'default' => 25,
		'option'  => 'lana_downloads_manager_logs_per_page'
	);
	add_screen_option( 'per_page', $args );
}

/**
 * Lana Downloads Manager
 * logs page - set screen options
 *
 * @param $screen_value
 * @param $option
 * @param $value
 *
 * @return mixed
 */
function lana_downloads_manager_logs_page_set_screen_option( $screen_value, $option, $value ) {

	if ( 'lana_downloads_manager_logs_per_page' == $option ) {
		$screen_value = $value;
	}

	return $screen_value;
}

add_filter( 'set-screen-option', 'lana_downloads_manager_logs_page_set_screen_option', 10, 3 );

/**
 * Register settings
 */
function lana_downloads_manager_register_settings() {
	register_setting( 'lana-downloads-manager-settings-group', 'lana_downloads_manager_endpoint' );
	register_setting( 'lana-downloads-manager-settings-group', 'lana_downloads_manager_endpoint_type' );
	register_setting( 'lana-downloads-manager-settings-group', 'lana_downloads_manager_post_type_endpoint' );
	register_setting( 'lana-downloads-manager-settings-group', 'lana_downloads_manager_category_endpoint' );
	register_setting( 'lana-downloads-manager-settings-group', 'lana_downloads_manager_public' );
	register_setting( 'lana-downloads-manager-settings-group', 'lana_downloads_manager_logs' );
	register_setting( 'lana-downloads-manager-settings-group', 'lana_downloads_manager_counter' );
}

/**
 * Lana Downloads Manager
 * logs page
 */
function lana_downloads_manager_logs() {
	if ( ! get_option( 'lana_downloads_manager_logs', false ) ) :
		?>
        <div class="wrap">
            <h2>
				<?php _e( 'Lana Downloads Manager Logs', 'lana-downloads-manager' ); ?>
            </h2>

            <p>
				<?php printf( __( 'Logs are disabled. Go to the <a href="%s">Settings</a> page to enable it.', 'lana-downloads-manager' ), esc_url( admin_url( 'edit.php?post_type=lana_download&page=lana-downloads-manager-settings' ) ) ); ?>
            </p>
        </div>
		<?php
		return;
	endif;

	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	require_once LANA_DOWNLOADS_MANAGER_DIR_PATH . '/includes/class-lana-downloads-manager-logs-list-table.php';

	$lana_downloads_manager_logs_list_table = new Lana_Downloads_Manager_Logs_List_Table();

	/** manage actions */
	$action = $lana_downloads_manager_logs_list_table->current_action();

	if ( $action ) {

		/** delete logs */
		if ( 'delete_logs' == $action ) {

			if ( ! current_user_can( 'manage_lana_download_logs' ) ) {
				wp_die( __( 'Sorry, you are not allowed to delete logs.', 'lana-downloads-manager' ) );
			}

			check_admin_referer( 'bulk-lana_downloads_manager_logs' );

			$table_name = $wpdb->prefix . 'lana_downloads_manager_logs';
			$wpdb->query( "TRUNCATE TABLE " . $table_name . ";" );
		}
	}

	/** prepare items */
	$lana_downloads_manager_logs_list_table->prepare_items();
	?>
    <div class="wrap">
        <h2>
			<?php _e( 'Lana Downloads Manager Logs', 'lana-downloads-manager' ); ?>
        </h2>
        <br/>

        <form id="lana-downloads-manager-logs-form" method="post">
			<?php $lana_downloads_manager_logs_list_table->display(); ?>
        </form>
    </div>
	<?php
}

/**
 * Lana Downloads Manager
 * settings page
 */
function lana_downloads_manager_settings() {
	?>
    <div class="wrap">
        <h2><?php _e( 'Lana Downloads Manager Settings', 'lana-downloads-manager' ); ?></h2>

		<?php settings_errors(); ?>

        <hr/>
        <a href="<?php echo esc_url( 'http://lana.codes/' ); ?>" target="_blank">
            <img src="<?php echo esc_url( LANA_DOWNLOADS_MANAGER_DIR_URL . '/assets/img/plugin-header.png' ); ?>"
                 alt="<?php esc_attr_e( 'Lana Codes', 'lana-downloads-manager' ); ?>"/>
        </a>
        <hr/>

        <form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php settings_fields( 'lana-downloads-manager-settings-group' ); ?>

            <h2 class="title"><?php _e( 'General Settings', 'lana-downloads-manager' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="lana-downloads-manager-endpoint">
							<?php _e( 'Endpoint', 'lana-downloads-manager' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="lana_downloads_manager_endpoint" id="lana-downloads-manager-endpoint"
                               value="<?php echo esc_attr( get_option( 'lana_downloads_manager_endpoint', 'download' ) ); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="lana-downloads-manager-endpoint-type">
							<?php _e( 'Endpoint Type', 'lana-downloads-manager' ); ?>
                        </label>
                    </th>
                    <td>
                        <select name="lana_downloads_manager_endpoint_type" id="lana-downloads-manager-endpoint-type">
                            <option value="ID"
								<?php selected( get_option( 'lana_downloads_manager_endpoint_type', 'ID' ), 'ID' ); ?>>
								<?php _e( 'ID', 'lana-downloads-manager' ); ?>
                            </option>
                            <option value="slug"
								<?php selected( get_option( 'lana_downloads_manager_endpoint_type', 'ID' ), 'slug' ); ?>>
								<?php _e( 'slug', 'lana-downloads-manager' ); ?>
                            </option>
                        </select>
                    </td>
                </tr>
            </table>

            <h2 class="title"><?php _e( 'Custom Post Type Settings', 'lana-downloads-manager' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="lana-downloads-manager-post-type-endpoint">
							<?php _e( 'Post Type Endpoint', 'lana-downloads-manager' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="lana_downloads_manager_post_type_endpoint"
                               id="lana-downloads-manager-post-type-endpoint"
                               value="<?php echo esc_attr( get_option( 'lana_downloads_manager_post_type_endpoint', 'lana-download' ) ); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="lana-downloads-manager-category-endpoint">
							<?php _e( 'Category Endpoint', 'lana-downloads-manager' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="lana_downloads_manager_category_endpoint"
                               id="lana-downloads-manager-category-endpoint"
                               value="<?php echo esc_attr( get_option( 'lana_downloads_manager_category_endpoint', 'download-category' ) ); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="lana-downloads-manager-public">
							<?php _e( 'Public', 'lana-downloads-manager' ); ?>
                        </label>
                    </th>
                    <td>
                        <select name="lana_downloads_manager_public" id="lana-downloads-manager-public">
                            <option value="0"
								<?php selected( get_option( 'lana_downloads_manager_public', true ), false ); ?>>
								<?php _e( 'Disabled', 'lana-downloads-manager' ); ?>
                            </option>
                            <option value="1"
								<?php selected( get_option( 'lana_downloads_manager_public', true ), true ); ?>>
								<?php _e( 'Enabled', 'lana-downloads-manager' ); ?>
                            </option>
                        </select>
                    </td>
                </tr>
            </table>

            <h2 class="title"><?php _e( 'Log Settings', 'lana-downloads-manager' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="lana-downloads-manager-logs">
							<?php _e( 'Logs', 'lana-downloads-manager' ); ?>
                        </label>
                    </th>
                    <td>
                        <select name="lana_downloads_manager_logs" id="lana-downloads-manager-logs">
                            <option value="0"
								<?php selected( get_option( 'lana_downloads_manager_logs', false ), false ); ?>>
								<?php _e( 'Disabled', 'lana-downloads-manager' ); ?>
                            </option>
                            <option value="1"
								<?php selected( get_option( 'lana_downloads_manager_logs', false ), true ); ?>>
								<?php _e( 'Enabled', 'lana-downloads-manager' ); ?>
                            </option>
                        </select>
                    </td>
                </tr>
            </table>

            <h2 class="title"><?php _e( 'Counter Settings', 'lana-downloads-manager' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="lana-downloads-manager-counter">
							<?php _e( 'Counter', 'lana-downloads-manager' ); ?>
                        </label>
                    </th>
                    <td>
                        <select name="lana_downloads_manager_counter" id="lana-downloads-manager-counter">
                            <option value="0"
								<?php selected( get_option( 'lana_downloads_manager_counter', true ), false ); ?>>
								<?php _e( 'Disabled', 'lana-downloads-manager' ); ?>
                            </option>
                            <option value="1"
								<?php selected( get_option( 'lana_downloads_manager_counter', true ), true ); ?>>
								<?php _e( 'Enabled', 'lana-downloads-manager' ); ?>
                            </option>
                        </select>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button-primary"
                       value="<?php esc_attr_e( 'Save Changes', 'lana-downloads-manager' ); ?>"/>
            </p>

        </form>
    </div>
	<?php
}

/**
 * Lana Downloads Manager
 * validate endpoint
 *
 * @param $new_value
 * @param $old_value
 *
 * @return mixed
 */
function lana_downloads_manager_validate_endpoint( $new_value, $old_value ) {

	$lana_download = get_post_type_object( 'lana_download' );

	if ( $new_value == $lana_download->rewrite ) {
		return $old_value;
	}

	if ( get_option( 'lana_downloads_manager_post_type_endpoint', 'lana-download' ) == $new_value ) {
		return $old_value;
	}

	return $new_value;
}

/**
 * Lana Downloads Manager
 * validate post type endpoint
 *
 * @param $new_value
 * @param $old_value
 *
 * @return mixed
 */
function lana_downloads_manager_validate_post_type_endpoint( $new_value, $old_value ) {

	if ( get_option( 'lana_downloads_manager_endpoint', 'lana-download' ) == $new_value ) {
		return $old_value;
	}

	return $new_value;
}

/**
 * Lana Downloads Manager
 * validate endpoints
 */
function lana_downloads_manager_validate_endpoints() {
	add_filter( 'pre_update_option_lana_downloads_manager_endpoint', 'lana_downloads_manager_validate_endpoint', 10, 2 );
	add_filter( 'pre_update_option_lana_downloads_manager_post_type_endpoint', 'lana_downloads_manager_validate_post_type_endpoint', 10, 2 );
}

add_action( 'init', 'lana_downloads_manager_validate_endpoints' );

/**
 * Add Lana Downloads Manager metaboxes
 * - File Manager to normal
 * - Download Information to side
 */
function lana_downloads_manager_add_meta_box() {
	add_meta_box( 'lana-downloads-manager', 'File Manager', 'lana_downloads_manager_meta_box_render', 'lana_download', 'normal', 'core' );
	add_meta_box( 'lana-downloads-manager-info', 'Download Information', 'lana_downloads_manager_info_meta_box_render', 'lana_download', 'side', 'core' );
}

add_action( 'add_meta_boxes', 'lana_downloads_manager_add_meta_box' );

/**
 * Lana Tickets Manager
 * download static info after title
 *
 * @param $post
 */
function lana_downloads_manager_download_static_info( $post ) {

	if ( 'lana_download' != $post->post_type ) {
		return;
	}

	include_once LANA_DOWNLOADS_MANAGER_DIR_PATH . '/views/lana-download-static-info.php';
}

add_action( 'edit_form_after_title', 'lana_downloads_manager_download_static_info' );

/**
 * File Manager
 * metabox
 *
 * @param $post
 */
function lana_downloads_manager_meta_box_render( $post ) {
	include_once LANA_DOWNLOADS_MANAGER_DIR_PATH . '/views/lana-downloads-manager-metabox.php';
}

/**
 * Download Information
 * metabox
 *
 * @param $post
 */
function lana_downloads_manager_info_meta_box_render( $post ) {
	include_once LANA_DOWNLOADS_MANAGER_DIR_PATH . '/views/lana-downloads-manager-info-metabox.php';
}

/**
 * Lana Downloads Manager
 * Request Handler
 */
function lana_downloads_manager_download_handler() {
	global $wp;

	error_reporting( 0 );

	$endpoint      = get_option( 'lana_downloads_manager_endpoint', 'download' );
	$endpoint_type = get_option( 'lana_downloads_manager_endpoint_type', 'ID' );

	if ( ! empty( $_GET[ $endpoint ] ) ) {
		$wp->query_vars[ $endpoint ] = $_GET[ $endpoint ];
	}

	if ( ! empty( $wp->query_vars[ $endpoint ] ) ) {

		define( 'DONOTCACHEPAGE', true );

		$download_id = sanitize_title( stripslashes( $wp->query_vars[ $endpoint ] ) );

		if ( 'ID' == $endpoint_type ) {
			$download_id = absint( $download_id );
		}

		if ( 'slug' == $endpoint_type ) {
			$page = get_page_by_path( $download_id, OBJECT, 'lana_download' );

			if ( $page ) {
				$download_id = $page->ID;
			}
		}

		if ( empty( $download_id ) ) {
			wp_die( __( 'No download_id defined.', 'lana-downloads-manager' ) );
		}

		$file_url = get_post_meta( $download_id, 'lana_download_file_url', true );

		if ( empty( $file_url ) ) {
			$error_message = vsprintf( '%s <a href="%s">%s</a>', array(
				__( 'No file URL defined.', 'lana-downloads-manager' ),
				home_url(),
				__( 'Go to homepage &rarr;', 'lana-downloads-manager' )
			) );

			wp_die( $error_message, __( 'Download Error', 'lana-downloads-manager' ) );
		}

		list( $file_path, $remote_file, $local_file ) = lana_downloads_manager_parse_file_path( $file_url );

		if ( empty( $file_path ) ) {
			$error_message = vsprintf( '%s <a href="%s">%s</a>', array(
				__( 'No file path defined.', 'lana-downloads-manager' ),
				home_url(),
				__( 'Go to homepage &rarr;', 'lana-downloads-manager' )
			) );

			wp_die( $error_message, __( 'Download Error', 'lana-downloads-manager' ) );
		}

		$lana_downloads_manager_counter = get_option( 'lana_downloads_manager_counter', true );
		$increment_download_count       = false;

		/**
		 * Check counter is enabled?
		 */
		if ( $lana_downloads_manager_counter ) {

			/**
			 * Check Cookie
			 */
			if ( lana_downloads_manager_cookie_exists( $download_id ) == false ) {
				$increment_download_count = true;
				lana_downloads_manager_set_cookie( $download_id );
			}

			/**
			 * Check Log
			 */
			if ( lana_downloads_manager_get_log_user_ip_has_downloaded( $download_id ) ) {
				$increment_download_count = false;
			}
		}

		/**
		 * Output configs
		 */
		if ( ! ini_get( 'safe_mode' ) ) {
			@set_time_limit( 0 );
		}

		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}

		@session_write_close();

		if ( ini_get( 'zlib.output_compression' ) ) {
			@ini_set( 'zlib.output_compression', 'Off' );
		}

		@ob_end_clean();

		while ( ob_get_level() > 0 ) {
			@ob_end_clean();
		}

		do_action( 'lana_downloads_manager_before_file_download', $download_id );

		/**
		 * Local file
		 */
		if ( $file_path && $local_file ) {
			lana_downloads_manager_add_log( $download_id );
			lana_downloads_manager_add_download_count( $download_id, $increment_download_count );

			$filename = basename( parse_url( $file_path, PHP_URL_PATH ) );

			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Content-Length:' . filesize( $file_path ) );
			header( 'Connection: Keep-Alive' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Pragma: public' );
			readfile( $file_path );
			exit;
		}

		/**
		 * Remote file
		 */
		if ( $file_path && $remote_file ) {
			lana_downloads_manager_add_log( $download_id );
			lana_downloads_manager_add_download_count( $download_id, $increment_download_count );

			$filename = basename( parse_url( $file_path, PHP_URL_PATH ) );
			$file_ext = pathinfo( basename( $file_path ), PATHINFO_EXTENSION );

			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Connection: Keep-Alive' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Pragma: public' );

			/** file without extension */
			if ( empty( $file_ext ) ) {

				/** check remote file header content disposition */
				if ( function_exists( 'get_headers' ) ) {
					$file_headers = get_headers( $file_path, 1 );

					if ( ! empty( $file_headers['Content-Disposition'] ) ) {
						header( 'Content-Disposition: ' . $file_headers['Content-Disposition'] );
					}
				}
			}

			readfile( $file_path );
			exit;
		}
	}
}

add_action( 'parse_request', 'lana_downloads_manager_download_handler', 0 );

/**
 * Parse file path
 *
 * @param $file_path
 *
 * @return array
 */
function lana_downloads_manager_parse_file_path( $file_path ) {

	$remote_file      = true;
	$parsed_file_path = parse_url( $file_path );

	$wp_uploads     = wp_upload_dir();
	$wp_uploads_dir = $wp_uploads['basedir'];
	$wp_uploads_url = $wp_uploads['baseurl'];

	if ( ( ! isset( $parsed_file_path['scheme'] ) || ! in_array( $parsed_file_path['scheme'], array(
				'http',
				'https',
				'ftp'
			) ) ) && isset( $parsed_file_path['path'] ) && file_exists( $parsed_file_path['path'] ) ) {

		/** This is an absolute path */
		$remote_file = false;

	} elseif ( strpos( $file_path, $wp_uploads_url ) !== false ) {

		/** This is a local file given by URL so we need to figure out the path */
		$remote_file = false;
		$file_path   = trim( str_replace( $wp_uploads_url, $wp_uploads_dir, $file_path ) );
		$file_path   = realpath( $file_path );

	} elseif ( is_multisite() && ( ( strpos( $file_path, network_site_url( '/', 'http' ) ) !== false ) || ( strpos( $file_path, network_site_url( '/', 'https' ) ) !== false ) ) ) {

		/** This is a local file outside of wp-content so figure out the path */
		$remote_file = false;
		$file_path   = str_replace( network_site_url( '/', 'https' ), ABSPATH, $file_path );
		$file_path   = str_replace( network_site_url( '/', 'http' ), ABSPATH, $file_path );
		$file_path   = str_replace( $wp_uploads_url, $wp_uploads_dir, $file_path );
		$file_path   = realpath( $file_path );

	} elseif ( strpos( $file_path, site_url( '/', 'http' ) ) !== false || strpos( $file_path, site_url( '/', 'https' ) ) !== false ) {

		/** This is a local file outside of wp-content so figure out the path */
		$remote_file = false;
		$file_path   = str_replace( site_url( '/', 'https' ), ABSPATH, $file_path );
		$file_path   = str_replace( site_url( '/', 'http' ), ABSPATH, $file_path );
		$file_path   = realpath( $file_path );

	} elseif ( file_exists( ABSPATH . $file_path ) ) {

		/** Path needs an abspath to work */
		$remote_file = false;
		$file_path   = ABSPATH . $file_path;
		$file_path   = realpath( $file_path );
	}

	$local_file = $remote_file == false;

	return array( $file_path, $remote_file, $local_file );
}

/**
 * Lana Downloads Manager
 * Cookie exists?
 *
 * @param $download_id
 *
 * @return bool
 */
function lana_downloads_manager_cookie_exists( $download_id ) {
	$exists = false;
	$cdata  = lana_downloads_manager_get_cookie();

	if ( ! empty( $cdata ) ) {
		if ( $cdata['download_id'] == $download_id ) {
			$exists = true;
		}
	}

	return $exists;
}

/**
 * Lana Downloads Manager
 * Get Cookie
 * @return array|mixed|null|object
 */
function lana_downloads_manager_get_cookie() {
	$cdata = null;

	if ( ! empty( $_COOKIE['lana_downloads_manager'] ) ) {
		$cdata = json_decode( base64_decode( $_COOKIE['lana_downloads_manager'] ), true );
	}

	return $cdata;
}

/**
 * Lana Downloads Manager
 * Set Cookie
 *
 * @param $download_id
 */
function lana_downloads_manager_set_cookie( $download_id ) {
	setcookie( 'lana_downloads_manager', base64_encode( json_encode( array(
		'download_id' => $download_id
	) ) ), time() + 3600, COOKIEPATH, COOKIE_DOMAIN, false, true );
}

/**
 * Lana Downloads Manager
 * User IP has downloaded (in last hours)
 *
 * @param $download_id
 *
 * @return bool
 */
function lana_downloads_manager_get_log_user_ip_has_downloaded( $download_id ) {
	global $wpdb;

	$table_name    = $wpdb->prefix . 'lana_downloads_manager_logs';
	$user_ip       = sanitize_text_field( lana_downloads_manager_get_user_ip() );
	$download_date = date( 'Y-m-d H:i:s', strtotime( '-1 hour' ) );

	return ( absint( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM " . $table_name . " WHERE download_id = '%d' AND user_ip = '%s' AND download_date > '%s'", $download_id, $user_ip, $download_date ) ) ) > 0 );
}

/**
 * Lana Downloads Manager
 * add log to database
 *
 * @param $download_id
 */
function lana_downloads_manager_add_log( $download_id ) {
	global $wpdb;

	if ( get_option( 'lana_downloads_manager_logs', false ) ) {

		$wpdb->hide_errors();

		$wpdb->insert( $wpdb->prefix . 'lana_downloads_manager_logs', array(
			'user_id'       => absint( get_current_user_id() ) > 0 ? absint( get_current_user_id() ) : null,
			'user_ip'       => sanitize_text_field( lana_downloads_manager_get_user_ip() ),
			'user_agent'    => sanitize_text_field( lana_downloads_manager_get_user_agent() ),
			'download_id'   => absint( $download_id ),
			'download_date' => current_time( 'mysql' )
		), array( '%s', '%s', '%s', '%d', '%s' ) );
	}
}

/**
 * Add Download Count
 *
 * @param $download_id
 * @param $increment_download_count
 */
function lana_downloads_manager_add_download_count( $download_id, $increment_download_count = true ) {
	$lana_downloads_manager_counter = get_option( 'lana_downloads_manager_counter', true );

	/** check counter is enabled? */
	if ( ! $lana_downloads_manager_counter ) {
		return;
	}

	/** check counter increment */
	if ( ! $increment_download_count ) {
		return;
	}

	update_post_meta( $download_id, 'lana_download_count', absint( get_post_meta( $download_id, 'lana_download_count', true ) ) + 1 );
}

/**
 * Get Download Count
 *
 * @param string $download_id
 *
 * @return int|mixed
 */
function lana_downloads_manager_get_download_count( $download_id = '' ) {
	$lana_downloads_manager_counter = get_option( 'lana_downloads_manager_counter', true );

	if ( ! $lana_downloads_manager_counter ) {
		if ( is_admin() ) {
			return __( 'disabled', 'lana-downloads-manager' );
		}

		return null;
	}

	$post = get_post();

	$abs_download_id = absint( $download_id );

	if ( ! empty( $download_id ) && ! empty( $abs_download_id ) && is_numeric( $download_id ) ) {
		$post = get_post( absint( $download_id ) );
	}

	if ( ! isset( $post ) || ! is_a( $post, 'WP_Post' ) ) {
		return false;
	}

	$lana_download_count = get_post_meta( $post->ID, 'lana_download_count', true );

	if ( $lana_download_count ) {
		return $lana_download_count;
	}

	return 0;
}

/**
 * Get Download URL
 *
 * @param string $download_id
 *
 * @return mixed
 */
function lana_downloads_manager_get_download_url( $download_id = '' ) {
	$post = get_post();

	$abs_download_id = absint( $download_id );

	if ( ! empty( $download_id ) && ! empty( $abs_download_id ) && is_numeric( $download_id ) ) {
		$post = get_post( absint( $download_id ) );
	}

	if ( ! isset( $post ) || ! is_a( $post, 'WP_Post' ) ) {
		return false;
	}

	$scheme        = parse_url( get_option( 'home' ), PHP_URL_SCHEME );
	$endpoint      = get_option( 'lana_downloads_manager_endpoint', 'download' );
	$endpoint_type = get_option( 'lana_downloads_manager_endpoint_type', 'ID' );
	$value         = $post->ID;

	if ( 'ID' == $endpoint_type ) {
		$value = $post->ID;
	}

	if ( 'slug' == $endpoint_type ) {
		$value = $post->post_name;
	}

	if ( get_option( 'permalink_structure' ) ) {
		$link = home_url( '/' . $endpoint . '/' . $value . '/', $scheme );
	} else {
		$link = add_query_arg( $endpoint, $value, home_url( '', $scheme ) );
	}

	return apply_filters( 'lana_downloads_manager_get_download_url', esc_url_raw( $link ) );
}

/**
 * Get lana download shortcode
 *
 * @param int|null $download_id
 *
 * @return string
 */
function lana_downloads_manager_get_download_shortcode( $download_id = null ) {
	$post = get_post( $download_id );

	if ( ! isset( $post ) || ! is_a( $post, 'WP_Post' ) ) {
		return false;
	}

	$endpoint_type = get_option( 'lana_downloads_manager_endpoint_type', 'ID' );

	$shortcode_pairs = array();
	$shortcode_atts  = array();

	if ( 'ID' == $endpoint_type ) {
		$shortcode_pairs = array( 'id' => '' );
		$shortcode_atts  = array( 'id' => esc_attr( $post->ID ) );
	}

	if ( 'slug' == $endpoint_type ) {
		$shortcode_pairs = array( 'file' => '' );
		$shortcode_atts  = array( 'file' => esc_attr( $post->post_name ) );
	}

	return lana_downloads_manager_get_lana_download_shortcode_str( 'lana_download', $shortcode_pairs, $shortcode_atts );
}


/**
 * Lana Downloads Manager - get lana download shortcode str
 *
 * @param $shortcode
 * @param array $pairs
 * @param array $atts
 * @param string $content
 *
 * @return string
 */
function lana_downloads_manager_get_lana_download_shortcode_str( $shortcode, $pairs = array(), $atts = array(), $content = '' ) {

	foreach ( $pairs as $name => $default ) {
		if ( array_key_exists( $name, $atts ) ) {
			$pairs[ $name ] = $atts[ $name ];
		} else {
			$pairs[ $name ] = $default;
		}
	}

	$out = '[' . $shortcode;

	if ( empty( $pairs ) ) {
		/** get default shortcode pairs */
		$pairs = array();
	}

	foreach ( $pairs as $name => $value ) {
		$out .= ' ' . $name . '="' . $value . '"';
	}

	if ( ! empty( $content ) ) {
		if ( ! is_string( $content ) ) {
			if ( is_bool( $content ) ) {
				$content = '';
			}
			$content = strval( $content );
		}

		$out .= ']';
		$out .= $content;
		$out .= '[/' . $shortcode . ']';
	} else {
		$out .= ']';
	}

	return $out;
}

/**
 * Lana Download Shortcode
 * with Bootstrap
 *
 * @param $atts
 *
 * @return string
 */
function lana_download_shortcode( $atts ) {
	$a = shortcode_atts( array(
		'id'      => '',
		'file'    => '',
		'text'    => __( 'Download', 'lana-downloads-manager' ),
		'counter' => true
	), $atts );

	$lana_downloads_manager_counter = get_option( 'lana_downloads_manager_counter', true );

	if ( ! empty( $a['id'] ) ) {
		$lana_download = get_post( $a['id'] );
	}

	if ( ! empty( $a['file'] ) ) {
		$lana_download = get_page_by_path( $a['file'], OBJECT, 'lana_download' );
	}

	/** check lana download */
	if ( ! isset( $lana_download ) ) {
		return '';
	}

	/** check is post */
	if ( ! is_a( $lana_download, 'WP_Post' ) ) {
		return '';
	}

	/** post title to text */
	if ( '%post_title%' == $a['text'] ) {
		$a['text'] = $lana_download->post_title;
	}

	$output = '<div class="lana-download-shortcode">';

	/** download button */
	$output .= '<p>';
	$output .= '<a class="btn btn-primary lana-download" href="' . esc_attr( lana_downloads_manager_get_download_url( $lana_download->ID ) ) . '" role="button">';
	$output .= esc_html( $a['text'] ) . ' ';

	/** counter */
	if ( $a['counter'] && $lana_downloads_manager_counter ) {
		$output .= '<span class="badge">';
		$output .= lana_downloads_manager_get_download_count( $lana_download->ID );
		$output .= '</span>';
	}

	$output .= '</a>';
	$output .= '</p>';

	$output .= '</div>';

	return $output;
}

add_shortcode( 'lana_download', 'lana_download_shortcode' );

/**
 * TinyMCE
 * Register Plugins
 *
 * @param $plugins
 *
 * @return mixed
 */
function lana_downloads_manager_add_mce_plugin( $plugins ) {

	$plugins['lana_download'] = LANA_DOWNLOADS_MANAGER_DIR_URL . '/assets/js/lana-download.js';

	return $plugins;
}

/**
 * TinyMCE
 * Register Buttons
 *
 * @param $buttons
 *
 * @return mixed
 */
function lana_downloads_manager_add_mce_button( $buttons ) {

	array_push( $buttons, 'lana_download' );

	return $buttons;
}

/**
 * TinyMCE
 * Add Custom Buttons
 */
function lana_downloads_manager_add_mce_shortcodes_buttons() {
	add_filter( 'mce_external_plugins', 'lana_downloads_manager_add_mce_plugin' );
	add_filter( 'mce_buttons_3', 'lana_downloads_manager_add_mce_button' );
}

add_action( 'init', 'lana_downloads_manager_add_mce_shortcodes_buttons' );

/**
 * Lana Downloads Manager - ajax
 * get lana download list
 */
function lana_downloads_manager_ajax_get_lana_download_list() {

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array(
			'code'    => 'current_user_can_edit_posts',
			'message' => __( 'Error: You do not have permission to view downloads!', 'lana-downloads-manager' )
		) );
	}

	$lana_download_list = array();

	/** @var WP_Post[] $lana_downloads */
	$lana_downloads = get_posts( array(
		'post_type'   => 'lana_download',
		'post_status' => 'publish',
		'numberposts' => - 1
	) );

	if ( $lana_downloads ) {
		foreach ( $lana_downloads as $lana_download ) {
			$lana_download_list[ $lana_download->ID ] = $lana_download->post_title;
		}
	}

	wp_send_json_success( array(
		'lana_download_list' => $lana_download_list,
		'version'            => LANA_DOWNLOADS_MANAGER_VERSION
	) );
}

add_action( 'wp_ajax_lana_downloads_manager_get_lana_download_list', 'lana_downloads_manager_ajax_get_lana_download_list' );

/**
 * Lana Downloads Manager
 * save post
 *
 * @param $post_id
 */
function lana_downloads_manager_save_post( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	/**
	 * User can't edit
	 * this post
	 */
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	/**
	 * in Lana Downloads Manager
	 * initialized nonce field
	 */
	if ( empty( $_POST['lana_downloads_manager_nonce_field'] ) ) {
		return;
	}

	/**
	 * in Lana Downloads Manager
	 * initialized nonce field
	 */
	if ( ! wp_verify_nonce( $_POST['lana_downloads_manager_nonce_field'], 'save' ) ) {
		return;
	}

	$lana_download_file_url = sanitize_text_field( $_POST['lana_download_file_url'] );
	$lana_download_file_id  = null;

	/** check url and set id */
	if ( ! empty( $lana_download_file_url ) ) {
		$lana_download_file_id = absint( $_POST['lana_download_file_id'] );
	}

	update_post_meta( $post_id, 'lana_download_file_url', $lana_download_file_url );
	update_post_meta( $post_id, 'lana_download_file_id', $lana_download_file_id );
}

add_action( 'save_post', 'lana_downloads_manager_save_post' );

/**
 * Get user IP
 * @return mixed
 */
function lana_downloads_manager_get_user_ip() {

	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];

	if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
		$ip = $client;
	} elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
		$ip = $forward;
	} else {
		$ip = $remote;
	}

	return $ip;
}

/**
 * Get user agent
 * @return mixed
 */
function lana_downloads_manager_get_user_agent() {

	if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
		return '';
	}

	if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
		return '';
	}

	return $_SERVER['HTTP_USER_AGENT'];
}