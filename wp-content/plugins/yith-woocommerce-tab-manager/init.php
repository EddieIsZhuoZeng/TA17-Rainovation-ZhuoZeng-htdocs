<?php
/**
 * Plugin Name: YITH WooCommerce Tab Manager
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-tab-manager/
 * Description: <code><strong>YITH WooCommerce Tab Manager</strong></code> allows you to add additional tabs in the product page. <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.2.25
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-tab-manager
 * Domain Path: /languages/
 * WC requires at least: 3.8.0
 * WC tested up to: 4.6
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Tab Manager
 * @version 1.2.25
 */

/*  Copyright 2013  Your Inspiration Themes  (email : plugins@yithemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !defined( 'ABSPATH' ) ){

    exit;
}// Exit if accessed directly


if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( !function_exists( 'WC' ) ) {
    function yith_ywtm_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Tab Manager is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-tab-manager' ); ?></p>
        </div>
    <?php
    }

}
    function yith_ywtm_install_free_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'You can\'t activate the free version of YITH WooCommerce Tab Manager while you are using the premium one.', 'yith-woocommerce-tab-manager' ); ?></p>
        </div>
    <?php
    }


if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}


if ( !defined( 'YWTM_VERSION' ) ) {
	define( 'YWTM_VERSION', '1.2.25' );
}

if ( !defined( 'YWTM_FREE_INIT' ) ) {
	define( 'YWTM_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YWTM_FILE' ) ) {
	define( 'YWTM_FILE', __FILE__ );
}

if ( !defined( 'YWTM_DIR' ) ) {
	define( 'YWTM_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YWTM_URL' ) ) {
	define( 'YWTM_URL', plugins_url( '/', __FILE__ ) );
}

if ( !defined( 'YWTM_ASSETS_URL' ) ) {
	define( 'YWTM_ASSETS_URL', YWTM_URL . 'assets/' );
}

if ( !defined( 'YWTM_TEMPLATE_PATH' ) ) {
	define( 'YWTM_TEMPLATE_PATH', YWTM_DIR . 'templates/' );
}

if ( !defined( 'YWTM_INC' ) ) {
	define( 'YWTM_INC', YWTM_DIR . 'includes/' );
}

if( !defined('YWTM_SLUG' ) ){
	define( 'YWTM_SLUG', 'yith-woocommerce-tab-manager' );
}

/* Plugin Framework Version Check */
if( !function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWTM_DIR . 'plugin-fw/init.php' ) ) {
    require_once(YWTM_DIR . 'plugin-fw/init.php');
}
yit_maybe_plugin_fw_loader( YWTM_DIR  );

if ( ! function_exists( 'YITH_Tab_Manager_Init' ) ) {
	/**
	 * Unique access to instance of YITH_Tab_Manager class
	 *
	 * @return YITH_Tab_Manager
	 * @since 1.0.5
	 */
	function YITH_Tab_Manager_Init() {

        /* Load YWTM text domain */
        load_plugin_textdomain( 'yith-woocommerce-tab-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Load required classes and functions

		require_once( YWTM_INC . 'class.yith-woocommerce-tab-manager.php' );
		require_once( YWTM_INC . 'class.yith-wctm-admin.php' );
		require_once( YWTM_INC . 'class.yith-wctm-frontend.php' );
		require_once( YWTM_INC . 'class.yith-wctm-post-type.php' );

		global $YIT_Tab_Manager;
		$YIT_Tab_Manager= YITH_Tab_Manager();
	}
}

add_action('yith_wc_tabmanager_init', 'YITH_Tab_Manager_Init' );

if( !function_exists( 'yith_tab_manager_install' ) ){
    /**
     * install tab manager
     * @author YITHEMES
     * @since 1.0.5
     */
    function yith_tab_manager_install(){

        if( !function_exists( 'WC' ) ){
            add_action( 'admin_notices', 'yith_ywtm_install_woocommerce_admin_notice' );
        }elseif( defined( 'YWTM_PREMIUM' ) ){
            add_action( 'admin_notices', 'yith_ywtm_install_free_admin_notice' );
            deactivate_plugins( plugin_basename( __FILE__ ) );
        }
        else
            do_action( 'yith_wc_tabmanager_init' );

    }
}

add_action( 'plugins_loaded', 'yith_tab_manager_install', 11 );




