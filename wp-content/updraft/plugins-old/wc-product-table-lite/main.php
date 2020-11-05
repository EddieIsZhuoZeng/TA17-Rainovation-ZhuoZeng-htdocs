<?php
/*
 * Plugin Name: WooCommerce Product Table Lite
 * Plugin URI: https://wcproducttable.com/
 * Description: Display your WooCommerce product catalog in beautiful responsive tables.
 * Author: WC Product Table
 * Author URI: https://profiles.wordpress.org/wcproducttable/
 * Version: 1.9.3
 * 
 * WC requires at least: 3.4.4
 * WC tested up to: 4.0.1
 *
 * Text Domain: wc-product-table
 * Domain Path: /languages/
 *
 */
 
if ( ! defined( 'ABSPATH' ) ) {
 exit; // Exit if accessed directly
}

define('WCPT_VERSION', '1.9.3');
define('WCPT_PLUGIN_PATH', plugin_dir_path( __FILE__));
define('WCPT_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require_once( WCPT_PLUGIN_PATH . 'update.php' );

// suggest to deactivate Lite if PRO is installed
add_action('admin_notices', 'wcpt_suggest_uninstall_lite');
function wcpt_suggest_uninstall_lite() {
  if(
    FALSE !== strpos( dirname(__FILE__), 'wc-product-table-lite' ) && // if this is lite...
    file_exists( WP_PLUGIN_DIR . '/wc-product-table-pro/main.php' ) // ...and pro is installed
  ){ // ...suggest deactivating this
    $class = 'notice notice-warning';
  	$message = __( 'Please deactivate WCPT Lite before activating WCPT PRO to avoid conflict errors.', 'wc-product-table' );
  	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
  }
}

// get / cache global settings 
function wcpt_get_settings_data( $ctx= 'view' ){
  global $wcpt_settings;

  if( empty( $wcpt_settings ) ){
    if( ! $wcpt_settings = wcpt_update_settings_data() ){
      $data = json_decode( stripslashes( get_option( 'wcpt_settings', '' ) ), true );
      $wcpt_settings = apply_filters('wcpt_settings', $data, $ctx );
    }
  }

  return $wcpt_settings;
}

/* record initial plugin data */
function wcpt_ensure_default_settings() {

  if( ! get_option('wcpt_settings') ){
    update_option( 'wcpt_settings', addslashes( json_encode( array(
      'version' => WCPT_VERSION,
      'archive_override' => array(
        'default' => '',
        'shop' => 'default',
        'search' => 'default',

        'category' => array(
          'default' => 'default',
          'other_rules' => array(
            array(
              'category' => array(),
              'table_id' => '',
            ),
          ),
        ),

        'attribute' => array(
          'default' => 'default',
          'other_rules' => array(
            array(
              'attribute' => array(),
              'table_id' => '',
            ),
          ),
        ),

        'tag' => array(
          'default' => 'default',
          'other_rules' => array(
            array(
              'tag' => array(),
              'table_id' => '',
            ),
          ),
        ),

      ),
      'cart_widget' => array(
        'toggle' => 'enabled',
        'r_toggle' => 'enabled',
        'labels' => array(
          'item'          => "en_US: Item\r\nfr_FR: Article",
          'items'         => "en_US: Items\r\nfr_FR: Articles",
          'view_cart'     => "en_US: View Cart\r\nfr_FR: Voir le panier",
          'extra_charges'  => "en_US: Extra charges may apply\r\nfr_FR: Les taxes peuvent s\'appliquer",
        ),
        'style' => array(
          'background-color' => '#4CAF50',
          'border-color' => 'rgba(0, 0, 0, .1)',
          'bottom' => '50',
        ),
      ),

      'modals' => array(
        'labels' => array(
          'filters'   => "en_US: Filters\r\nfr_FR: Filtres",
          'sort'      => "en_US: Sort results\r\nfr_FR: Trier les résultats",
          'reset'     => "en_US: Reset\r\nfr_FR: Rafraîchir",
          'apply'     => "en_US: Apply\r\nfr_FR: Appliquer",
        ),
        'style' => array(
          '.wcpt-nm-apply' => array(
            'background-color' => '#4CAF50',
          ),
        ),
      ),

      'no_results' => array(
        'label' => 'No results found. [link]Clear filters[/link] and try again?',
      ),
    ) ) ) );
  }

}

/* load plugin textdomain. */
add_action( 'plugins_loaded', 'wcpt_load_textdomain' );
function wcpt_load_textdomain() {
  load_plugin_textdomain( 'wc-product-table', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

/* register wcpt cpt */
add_action( 'init', 'wcpt_register_posttype' );
function wcpt_register_posttype() {
  register_post_type( 'wc_product_table',
    array(
      'labels' => array(
        'name' => __( 'Product Tables', 'wc-product-table' ),
        'singular_name' => __( 'Product Table', 'wc-product-table' ),
        'menu_name' => __( 'Product Tables', 'wc-product-table' ),
        'add_new' => __( 'Add New Product Table', 'wc-product-table' ),
      ),
      'description' => __( 'Easily display your WooCommerce products in responsive tables.', 'wc-product-table' ),
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-editor-justify',
      'rewrite' => array('slug' => 'product-table'),
      'capability_type' => 'post',
      'capabilities' => array(
          'edit_post' => 'edit_wc_product_table',
          'edit_posts' => 'edit_wc_product_tables',
          'edit_others_posts' => 'edit_others_wc_product_tables',
          'publish_posts' => 'publish_wc_product_tables',
          'read_post' => 'read_wc_product_table',
          'read_private_posts' => 'read_private_wc_product_tables',
          'delete_post' => 'delete_wc_product_table',
      ),
      'map_meta_cap' => true,
      'supports'=> array(),
      'hierarchical' => false,
      'show_in_nav_menus' => true,
      'publicly_queryable' => false,
      'exclude_from_search' => true,
      'can_export' => true,
    )
  );
}

/* flush rewrites upon activation */
register_activation_hook( __FILE__, 'wcpt_activate' );
function wcpt_activate() {
  wcpt_register_posttype();
  flush_rewrite_rules();
  wcpt_ensure_default_settings();

  $admins = get_role( 'administrator' );

  $admins->add_cap( 'edit_wc_product_table' );
  $admins->add_cap( 'edit_wc_product_tables' );
  $admins->add_cap( 'edit_others_wc_product_tables' );
  $admins->add_cap( 'publish_wc_product_tables' );
  $admins->add_cap( 'read_wc_product_table' );
  $admins->add_cap( 'read_private_wc_product_tables' );
  $admins->add_cap( 'delete_wc_product_table' );
}

/* redirect to table editor */
add_action('plugins_loaded', 'wcpt_redirect_to_table_editor');
function wcpt_redirect_to_table_editor( ) {
  global $pagenow;

  // edit
  if($pagenow == 'post.php' && isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] == 'edit'){
    $post_id = (int) $_GET['post'];
    $post = get_post_type( $post_id );
    if($post === 'wc_product_table'){
      wp_redirect( admin_url( '/edit.php?post_type=wc_product_table&page=wcpt-edit&post_id=' . $post_id ) );
      exit;
    }
  }

  // add
  if($pagenow == 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'wc_product_table'){
    wp_redirect(admin_url('/edit.php?post_type=wc_product_table&page=wcpt-edit'));
    exit;
  }

}

/* plugin's table editor and global settings page */
define('WCPT_CAP', 'edit_wc_product_tables');
add_action('admin_menu', 'wcpt_hook_menu_pages');
function wcpt_hook_menu_pages(){
  // editor page
  add_submenu_page( 'edit.php?post_type=wc_product_table', 'WC Product Table', 'Add New Table', WCPT_CAP, 'wcpt-edit', 'wcpt_editor_page' );
  if( class_exists( 'WooCommerce' ) ) { // check if WC is installed
    add_action( 'admin_enqueue_scripts', 'wcpt_enqueue_admin_scripts' );
  }

  // global settings page
  add_submenu_page( 'edit.php?post_type=wc_product_table', 'WCPT Settings', 'Settings', WCPT_CAP, 'wcpt-settings', 'wcpt_settings_page' );
  if( class_exists( 'WooCommerce' ) ) { // check if WC is installed
    add_action( 'admin_enqueue_scripts', 'wcpt_enqueue_admin_scripts' );
  }
}

/* highlight the WC Product Table menu item when editing an existing wcpt table post */
add_action('admin_menu', 'wcpt_correct_menu_highlight');
function wcpt_correct_menu_highlight(){
  if(
    isset( $_GET['post_type'] ) &&
    $_GET['post_type'] === 'wc_product_table' &&
    isset( $_GET['page'] ) &&
    $_GET['page'] === 'wcpt-edit' &&
    ! empty( $_GET['post_id'] )
  ){
    global $submenu_file;
    $submenu_file = "edit.php?post_type=wc_product_table";
  }
}

/* create table editor page */
function wcpt_editor_page(){
  if( ! class_exists( 'WooCommerce' ) ) return;

  if( ! empty( $_GET['post_id'] )){
    $post_id = (int) $_GET['post_id'];
  } else {
    $post_id = wp_insert_post( array( 'post_type'=> 'wc_product_table' ) );
    wp_redirect( admin_url( 'edit.php?post_type=wc_product_table&page=wcpt-edit&post_id=' . $post_id ) );
  }

  if( get_post_meta( $post_id, 'wcpt_data', true ) ){
    // previously saved table data
    $GLOBALS['wcpt_table_data'] = wcpt_get_table_data($post_id, 'edit');

  }else{
    // starter data
    $GLOBALS['wcpt_table_data'] = array(
      'query' => array(
        'category' => array(),
        'orderby' => 'price',
        'order' => 'ASC',
        'limit' => 10,
        'paginate' => true,
        'visibility' => 'visible',
      ),
      'columns' => array(
        'laptop'  => array(),
        'tablet'  => array(),
        'phone'   => array(),
      ),
      'navigation' => array(
        'laptop' => array(
          'header' => array(
            'rows' => array(
              array(
                'columns_enabled' => 'left-right',
                'columns' => array(
                  'left' => array(
                    'template' => '',
                  ),
                  'right' => array(
                    'template' => '',
                  ),
                  'center' => array(
                    'template' => '',
                  ),
                ),
              ),
            ),
          ),
          'left_sidebar' => false,
        ),
        'tablet' => false,
        'phone' => false,
      ),
      'style' => array(
        'css' => '',
        'laptop' => array(),
        'tablet' => array(
          'inherit_laptop_style' => true,
        ),
        'phone' => array(
          'inherit_tablet_style' => false,
        ),
      ),
      'elements' => array(
        'column' => array(),
        'navigation' => array(),
      ),
    );
  }

  ?>
  <script>
    var wcpt = {
        model: {},
        view: {},
        controller: {},
        data: <?php echo json_encode( $GLOBALS['wcpt_table_data'] ); ?>,
      };
  </script>
  <?php
  // editor template
  require( WCPT_PLUGIN_PATH . 'editor/editor.php' );
}

/* esc data fields */
function wcpt_esc_attr( &$info ){
  foreach( $info as $key=> &$val ){
    if( is_string( $val ) && ! in_array( $key, array( "heading", "css" ) ) ){
      $val = esc_attr( $val );
    }else if( is_array( $val ) ){
      wcpt_esc_attr( $val );
    }
  }
}

/* save table data */
add_action( 'wp_ajax_wcpt_save_table_settings', 'wcpt_save_table_settings' );
function wcpt_save_table_settings() {

  // check for errors first
  $errors = array();

  // error: no table settings
  if( empty( $_POST['wcpt_data'] ) ){
    $errors[] = 'Table settings were not received.';
  }

  // error: no post ID
  if( empty( $_POST['wcpt_post_id'] ) ){
    $errors[] = 'Post ID was not received.';

  // error: unathorized user
  }else if( ! current_user_can( 'edit_wc_product_table', (int) $_POST['wcpt_post_id'] ) ){
    $user = wp_get_current_user();
    $errors[] = 'User ('. implode( ", ", $user->roles ) .') is not authorized to edit product tables.';
  }

  // error: no nonce
  if( empty( $_POST['wcpt_nonce'] ) ){
    $errors[] = 'Nonce string was not received.';

  // error: wrong nonce
  }else if( ! wp_verify_nonce( $_POST['wcpt_nonce'], 'wcpt' ) ){
    $errors[] = 'Nonce verification failed.';

  }

  if( count( $errors ) ){ // failure
    $error_message = 'WCPT error: Table data was not saved because:';
    foreach( $errors as $i => $error ){
      $error_message .= ' (' . ( $i + 1 ) . ') ' . $error;
    }

    $remedy = ' Please contact plugin author at https://wcproducttable.com/support/ for prompt assistance with this issue!';

    echo $error_message . $remedy;

  }else{ // success
    $post_id = (int) $_POST['wcpt_post_id'];
    $data = apply_filters( 'wcpt_save_table_settings', $_POST['wcpt_data'] );
    update_post_meta( $post_id, 'wcpt_data', $data );
    $my_post = array(
        'ID'=> $post_id,
        'post_title'=> (string) $_POST['wcpt_title'],
        'post_status'=> 'publish',
    );
    wp_update_post($my_post);

    echo "WCPT success: Table data was saved.";

  }

  wp_die();

}

/* create plugin settings page */
function wcpt_settings_page(){
  wcpt_ensure_default_settings();
  $settings = wcpt_get_settings_data( 'edit' );
  ?>
  <script>
    var wcpt = {
        model: {},
        view: {},
        controller: {},
        data: <?php echo json_encode( $settings ); ?>,
      };
  </script>
  <?php
  // settings page template
  require( WCPT_PLUGIN_PATH . 'editor/settings.php' );
}

add_action( 'wp_ajax_wcpt_save_global_settings', 'wcpt_save_global_settings' );
function wcpt_save_global_settings() {
  if(
    ! empty( $_POST['wcpt_data'] ) &&
    wp_verify_nonce( $_POST['wcpt_nonce'], 'wcpt' )
  ){
    update_option('wcpt_settings', apply_filters( 'wcpt_global_settings', $_POST['wcpt_data'] ));
    echo "WCPT success: Global settings saved.";
  }
  wp_die();
}

/* display error if minimum specifications to run WCPT are not met */
function wcpt_min_spec_warning() {
  $errors = false;

  // check if php version is compatible
  if( version_compare( PHP_VERSION, '5.4.0' ) < 0 ){
    $errors = true;
    ?>
    <div class="notice notice-error wcpt-needs-woocommerce">
      <p>
        <?php _e( 'WooCommerce Product Table requires at least PHP 5.4.0. Please request you webhost to update your PHP version or run the plugin on another server to avoid incompatibility issues and unexpected behaviour.', 'wc-product-table' ); ?>
      </p>
    </div>
    <?php
  }

  // check if wordpress version is compatible
  if(
    version_compare( $GLOBALS['wp_version'], '4.9.0' ) < 0
  ){
    $errors = true;
    ?>
    <div class="notice notice-error wcpt-needs-woocommerce">
        <p>
          <?php _e( 'WooCommerce Product Table requires at least WordPress 4.9.0. Please update your WordPress version to avoid incompatibility issues and unexpected behaviour.', 'wc-product-table' ); ?>
        </p>
    </div>
    <?php
  }

  // check if woocommerce is installed
  if( ! class_exists( 'WooCommerce' ) ){
    $errors = true;
    ?>
    <div class="notice notice-error wcpt-needs-woocommerce">
        <p>
          <?php _e( 'WooCommerce Product Table needs the WooCommerce plugin to be installed and activated on your site!', 'wc-product-table' ); ?>
          <a href="<?php echo get_admin_url( false, "/plugin-install.php?s=woocommerce&tab=search&type=term" ); ?>" target="_blank"><?php _e( 'Install now?', 'wc-product-table' ) ?></a>
        </p>
    </div>
    <?php
  }

  // check if woocommerce version is compatible
  $wc_version_compat = true;
  if( class_exists( 'WooCommerce' ) ){
    $wc_info = get_plugin_data(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php');
  }
  if(
    class_exists( 'WooCommerce' ) &&
    version_compare( $wc_info['Version'], '3.4.4' ) < 0
  ){
    $errors = true;
    $wc_version_compat = false;
    ?>
    <div class="notice notice-error wcpt-needs-woocommerce">
        <p>
          <?php _e( 'WooCommerce Product Table requires at least WooCommerce 3.4.4. Please update your WooCommerce version to avoid incompatibility issues and unexpected behaviour.', 'wc-product-table' ); ?>
        </p>
    </div>
    <?php
  }

  // check if woocommerce products exist version is compatible
  if( class_exists( 'WooCommerce' ) && $wc_version_compat ){
    $query = new WP_Query( array(
      'post_type' => 'product',
      'posts_per_page' => 1,
      'post_status' => 'publish',
    ) );

    if( ! $query->found_posts ){
      ?>
      <div class="notice notice-error wcpt-needs-woocommerce">
          <p>
            <?php _e( 'WooCommerce Product Table (WCPT) could not find a single \'published\' WooCommerce product on your site. WCPT cannot dispaly any products in tables if you do not have any published products on your site. See:', 'wc-product-table' ); ?>
            <a href="https://docs.woocommerce.com/document/managing-products/" target="_blank"><?php _e( 'How to add WooCommerce products', 'wc-product-table' ) ?></a>
          </p>
      </div>
      <?php
    }

  }

  ?>

  <?php
    if( ! $errors ) return;
  ?>
  <style media="screen">
    .wp-admin.post-type-wcpt #posts-filter,
    .wp-admin.post-type-wcpt .subsubsub,
    #menu-posts-wcpt .wp-submenu,
    #menu-posts-wcpt:after {
      display: none;
    }

    .wp-admin.post-type-wcpt .wcpt-needs-woocommerce {
      margin-top: 10px;
    }

    .wp-admin.post-type-wcpt .wcpt-needs-woocommerce p {
      font-size: 18px;
    }

    .plugin-card-woocommerce {
      border: 4px solid #03A9F4;
      animation: wcpt-pulse 1s infinite;
    }

    .plugin-card-woocommerce:hover {
      animation: none;
    }

    @-webkit-keyframes wcpt-pulse {
      0% {
        -webkit-box-shadow: 0 0 0 0 rgba(3,169,244, 1);
      }
      70% {
          -webkit-box-shadow: 0 0 0 15px rgba(3,169,244, 0);
      }
      100% {
          -webkit-box-shadow: 0 0 0 0 rgba(3,169,244, 0);
      }
    }
    @keyframes wcpt-pulse {
      0% {
        -moz-box-shadow: 0 0 0 0 rgba(3,169,244, 1);
        box-shadow: 0 0 0 0 rgba(3,169,244, 1);
      }
      70% {
          -moz-box-shadow: 0 0 0 15px rgba(3,169,244, 0);
          box-shadow: 0 0 0 15px rgba(3,169,244, 0);
      }
      100% {
          -moz-box-shadow: 0 0 0 0 rgba(3,169,244, 0);
          box-shadow: 0 0 0 0 rgba(3,169,244, 0);
      }
    }
  </style>
  <?php
}
add_action( 'admin_notices', 'wcpt_min_spec_warning' );

/* back end scripts */
add_action( 'admin_enqueue_scripts', 'wcpt_enqueue_admin_scripts' );
function wcpt_enqueue_admin_scripts (){
  if( ! isset($_GET['page']) || ! in_array( $_GET['page'], array( 'wcpt-edit', 'wcpt-settings' ) ) ) return;

  // Google font: Ubuntu
  wp_enqueue_style( 'Ubuntu', 'https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,600' );

  // CSS
  // -- editor
  wp_enqueue_style( 'wcpt-editor',  plugin_dir_url( __FILE__ ) . 'editor/assets/css/editor.css', null, WCPT_VERSION );

  // -- spectrum
  wp_enqueue_style( 'spectrum',  plugin_dir_url( __FILE__ ) . 'editor/assets/css/spectrum.min.css', null, WCPT_VERSION );

  // -- block editor
  wp_enqueue_style( 'wcpt-block-editor',  plugin_dir_url( __FILE__ ) . 'editor/partials/element-editor/block-editor/block-editor.css', null, WCPT_VERSION );

  // -- tabs
  wp_enqueue_style( 'wcpt-tabs',  plugin_dir_url( __FILE__ ) . 'editor/partials/element-editor/tabs/tabs.css', null, WCPT_VERSION );

  // -- element editor
  wp_enqueue_style( 'wcpt-element-editor',  plugin_dir_url( __FILE__ ) . 'editor/partials/element-editor/element-editor.css', null, WCPT_VERSION );

  // -- select2
  wp_enqueue_style( 'wcpt-select2',  plugin_dir_url( __FILE__ ) . 'editor/assets/css/select2.css' );

  // JS
  // -- dominator
  wp_enqueue_script( 'wcpt-dominator',  plugin_dir_url( __FILE__ ) . 'editor/assets/js/dominator_ui.js', array('jquery'), null, WCPT_VERSION );

  // -- util
  wp_enqueue_script( 'wp-util' );

  // -- spectrum
  wp_enqueue_script( 'spectrum',  plugin_dir_url( __FILE__ ) . 'editor/assets/js/spectrum.min.js', array('jquery'), null, false );

  // -- wp.media
  wp_enqueue_media();

  // -- block editor
  wp_enqueue_script( 'wcpt-block-editor',  plugin_dir_url( __FILE__ ) . 'editor/partials/element-editor/block-editor/block-editor.js', array('jquery'), WCPT_VERSION, true );
  wp_enqueue_script( 'wcpt-block-editor-model',  plugin_dir_url( __FILE__ ) . 'editor/partials/element-editor/block-editor/block-editor-model.js', array('jquery', 'wcpt-block-editor'), WCPT_VERSION, true );
  wp_enqueue_script( 'wcpt-block-editor-view',  plugin_dir_url( __FILE__ ) . 'editor/partials/element-editor/block-editor/block-editor-view.js', array('jquery', 'wcpt-block-editor'), WCPT_VERSION, true );
  wp_enqueue_script( 'wcpt-block-editor-controller',  plugin_dir_url( __FILE__ ) . 'editor/partials/element-editor/block-editor/block-editor-controller.js', array('jquery', 'wcpt-block-editor'), WCPT_VERSION, true );

  // -- tabs
  wp_enqueue_script( 'wcpt-tabs',  plugin_dir_url( __FILE__ ) . 'editor/partials/element-editor/tabs/tabs.js', array('jquery'), WCPT_VERSION, true );

  // -- element editor
  wp_enqueue_script( 'wcpt-element-editor',  plugin_dir_url( __FILE__ ) . 'editor/partials/element-editor/element-editor.js', array('jquery', 'wcpt-dominator'), WCPT_VERSION, true );

  // -- controller
  wp_enqueue_script( 'wcpt-controller',  plugin_dir_url( __FILE__ ) . 'editor/assets/js/controller.js', array('jquery', 'wcpt-dominator'), WCPT_VERSION, true );

  // -- feedback anim
  wp_enqueue_script( 'wcpt-feedback-anim', plugin_dir_url( __FILE__ ) . 'editor/assets/js/feedback_anim.js', array('wcpt-controller'), WCPT_VERSION, true );

  // -- select2
  wp_enqueue_script( 'wcpt-select2',  plugin_dir_url( __FILE__ ) . 'editor/assets/js/select2.min.js' );

  // -- jquery ui
  wp_enqueue_script( 'jquery-ui-sortable', array('jquery'), false, true );

}

add_action( 'admin_print_scripts', 'wcpt_admin_print_scripts' );
function wcpt_admin_print_scripts(){
  ?>
  <script>var wcpt_icons = "<?php echo WCPT_PLUGIN_URL . 'assets/feather/'; ?>"; </script>
  <style media="screen">
    #menu-posts-wc_product_table .wp-submenu li:nth-child(3){
      display: none;
    }
  </style>
  <?php
}

/* front end scripts */
add_action('wp_enqueue_scripts', 'wcpt_enqueue_scripts');
function wcpt_enqueue_scripts (){

  if( defined('WCPT_DEV') ){
    $min = '';
  }else{
    $min = '.min';
  }

  // antiscroll
  wp_enqueue_script( 'antiscroll',  plugin_dir_url( __FILE__ ) . 'assets/antiscroll/js.js', 'jquery', WCPT_VERSION, true );
  wp_enqueue_style( 'antiscroll',  plugin_dir_url( __FILE__ ) . 'assets/antiscroll/css.css', false, WCPT_VERSION );

  // freeze table
  wp_enqueue_script( 'freeze_table',  plugin_dir_url( __FILE__ ) . 'assets/freeze_table/js.js', array('jquery', 'antiscroll'), WCPT_VERSION, true );
  include( WCPT_PLUGIN_PATH . 'assets/freeze_table/tpl.html' );
  wp_enqueue_style( 'freeze_table',  plugin_dir_url( __FILE__ ) . 'assets/freeze_table/css.css', false, WCPT_VERSION );


  // wcpt
  // -- scripts
  wp_enqueue_script( 'wcpt',  plugin_dir_url( __FILE__ ) . 'assets/js'. $min .'.js', array('jquery', 'freeze_table'), WCPT_VERSION, true );
  wp_localize_script( 'wcpt', 'wcpt_i18n', array(
    'ajax_url' => admin_url( 'admin-ajax.php' ),
    'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
    'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woocommerce' ),
    'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
  ) );
  wp_enqueue_script( 'wc-add-to-cart-variation', apply_filters( 'woocommerce_get_asset_url', plugins_url( 'assets/js/frontend/add-to-cart-variation'. $min .'.js', WC_PLUGIN_FILE ), 'assets/js/frontend/add-to-cart-variation'. $min .'.js' ), array( 'jquery', 'wp-util' ), WC_VERSION );
  wp_enqueue_script('wp-mediaelement');
  include( WCPT_PLUGIN_PATH . 'templates/form-loading-screen.php' );

  // -- styles
  wp_enqueue_style( 'wcpt',  plugin_dir_url( __FILE__ ) . 'assets/css'. $min .'.css', false, WCPT_VERSION );
  wp_enqueue_style( 'wp-mediaelement' );

  // theme specific
  $theme_slug = trim( get_option('stylesheet') );

  if( substr( $theme_slug, -6 ) == '-child' ){
    $theme_slug = substr( $theme_slug, 0, -6 );
  }

  // echo $theme_slug;

  switch ( $theme_slug ) {

    //-- the7
    case 'dt-the7':
      wp_add_inline_style( 'wcpt',
      ' .woocommerce-variation-add-to-cart .minus,
        .woocommerce-variation-add-to-cart .plus {
          padding: 0 !important;
          height: 40px !important;
          width: 25px !important;
          text-align: center !important;
        }' );

      break;

    //-- jupiter
    case 'jupiter':
      wp_add_inline_style( 'wcpt',
      '.wcpt-modal .cart select {
        height: 45px !important;
        font-size: 18px !important;
        line-height: 20px !important;
        font-weight: normal !important;
      }

      .wcpt-modal .cart input.qty {
        width: 80px !important;
        text-align: center !important;
        padding-right: 36px !important;
      }

      .woocommerce .wcpt-modal .cart .quantity {
        margin-left: 20px !important;
      }

      .wcpt-modal .cart .single_variation_wrap .single_variation {
        float: none !important;
      }

      .wcpt-product-form table.variations tr td {
        text-align: left
      }

      .wcpt-product-form table.variations tr td.label label {
        margin-top: 10px !important;
        display: inline-block !important;
      }' );

      break;

    //-- shopkeeper
    case 'shopkeeper':
      wp_add_inline_style( 'wcpt',
      '.wcpt-modal .cart select {
        height: 45px !important;
        font-size: 18px !important;
        line-height: 20px !important;
        font-weight: normal !important;
      }

      .wcpt-product-form table.variations tr td.label label {
        margin-top: 10px !important;
        display: inline-block !important;
      }' );

      break;

    //-- flatsome
    case 'flatsome':
      wp_add_inline_style( 'wcpt','
      .wcpt-product-form .woocommerce-variation-add-to-cart .plus,
      .wcpt-product-form .woocommerce-variation-add-to-cart .minus {
        display: none;
      }

      .wcpt-product-form .variations .reset_variations {
          position: relative !important;
          right: 0 !important;
          bottom: 0 !important;
          color: currentColor !important;
          opacity: 0.6;
          font-size: 11px;
          text-transform: uppercase;
      }

      .wcpt-product-form .cart .button,
      .wcpt .cart .button {
        margin-bottom: 0 !important;
      }
      ' );

      break;

    //-- x
    case 'x':
      wp_add_inline_style( 'wcpt','
      .wcpt-product-form input.input-text[type="number"] {
        height: 44px !important;
      }
      ' );

      break;

    //-- woodmart
    case 'woodmart':
      wp_add_inline_style( 'wcpt','
      .wcpt-product-form .swatches-select {
        display: none !important;
      }

      .wcpt-product-form .woocommerce-variation-price .price {
        margin: 0 20px 0 0 !important;
      }
      ' );

      break;

    //-- martfury
    case 'martfury':
      wp_add_inline_style( 'wcpt','
      .wcpt-table {
        min-width: 100%;
      }
      ' );

      break;

    //-- divi
    case 'Divi':
      wp_add_inline_style( 'wcpt','
      .wcpt-table {
        min-width: 100%;
      }

      .wcpt-add-to-cart-wrapper .quantity {
        width: auto !important;
      }

      .wcpt-add-to-cart-wrapper .quantity + button {
        vertical-align: middle !important;
      }
      ' );

      break;

    //-- avada
    case 'Avada':
      wp_add_inline_style( 'wcpt','
      .wcpt-table {
        min-width: 100%;
      }

      body .wcpt-table input[type=number].qty {
        line-height: 17px !important;
        font-size: 14px !important;
        margin: 0 !important;
      }

      .wcpt-product-form .wcpt-quantity-wrapper > input:not([type="number"]),
      .wcpt-table .wcpt-quantity-wrapper > input:not([type="number"]) {
        display: none !important;
      }

      .wcpt-table .product-addon {
        width: 100% !important;
      }

      .wcpt-modal-content .woocommerce-variation.single_variation {
        display: none !important;
      }
      ' );

      break;

    //-- enfold
    case 'Enfold':
      wp_add_inline_style( 'wcpt','
      .wcpt-range-options-main input[type=number] {
          width: 60px !important;
          height: 36px !important;
          margin-right: 5px !important;
          margin-bottom: 0 !important;
          display: inline-block !important;
          padding: 0 0 0 5px !important;
      }
      ' );

      break;

    default:
      // code...
      break;

  }

  // product addons
  if( class_exists( 'WC_Product_Addons_Helper' ) ){
    // fix tipTip error
    wp_enqueue_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), WC_VERSION, true );
    // enqueue fixed addons script
		wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/accounting/accounting.min.js', array( 'jquery' ), '0.4.2' );
    wp_enqueue_script( 'wcpt-woocommerce-addons-fix', WCPT_PLUGIN_URL . 'pro/assets/js/addons.js', array( 'jquery', 'accounting' ), WC_VERSION, true );

		$params = array(
			'price_display_suffix'         => esc_attr( get_option( 'woocommerce_price_display_suffix' ) ),
			'tax_enabled'                  => wc_tax_enabled(),
			'price_include_tax'            => 'yes' === esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
			'display_include_tax'          => ( wc_tax_enabled() && 'incl' === esc_attr( get_option( 'woocommerce_tax_display_shop' ) ) ) ? true : false,
			'ajax_url'                     => WC()->ajax_url(),
			'i18n_sub_total'               => __( 'Subtotal', 'woocommerce-product-addons' ),
			'i18n_remaining'               => __( 'characters remaining', 'woocommerce-product-addons' ),
			'currency_format_num_decimals' => absint( get_option( 'woocommerce_price_num_decimals' ) ),
			'currency_format_symbol'       => get_woocommerce_currency_symbol(),
			'currency_format_decimal_sep'  => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep' => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
			'trim_trailing_zeros'          => apply_filters( 'woocommerce_price_trim_zeros', false ),
			'is_bookings'                  => class_exists( 'WC_Bookings' ),
			'trim_user_input_characters'   => apply_filters( 'woocommerce_product_addons_show_num_chars', 1000 ),
			'quantity_symbol'              => 'x ',
		);

		if ( ! function_exists( 'get_woocommerce_price_format' ) ) {
			$currency_pos = get_option( 'woocommerce_currency_pos' );

			switch ( $currency_pos ) {
				case 'left' :
					$format = '%1$s%2$s';
					break;
				case 'right' :
					$format = '%2$s%1$s';
					break;
				case 'left_space' :
					$format = '%1$s&nbsp;%2$s';
					break;
				case 'right_space' :
					$format = '%2$s&nbsp;%1$s';
					break;
			}

			$params['currency_format'] = esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), $format ) );
		} else {
			$params['currency_format'] = esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) );
		}

		wp_localize_script( 'wcpt-woocommerce-addons-fix', 'woocommerce_addons_params', apply_filters( 'woocommerce_product_addons_params', $params ) );

  }

}

/* permitted shortcode attributes */
add_action('init', 'wcpt_set_permitted_shortcode_attributes');
function wcpt_set_permitted_shortcode_attributes(){
  $GLOBALS['wcpt_permitted_shortcode_attributes'] = apply_filters('wcpt_permitted_shortcode_attributes', array(
    'id',
    'name',
    'offset',
    'limit',
    'category',
    'ids',
    'skus',
    'laptop_scroll_offset',
    'tablet_scroll_offset',
    'phone_scroll_offset',
  ));
}

/* wcpt ajax shortcode */
add_action( 'wp_ajax_wcpt_ajax', 'wcpt_ajax' );
add_action( 'wp_ajax_nopriv_wcpt_ajax', 'wcpt_ajax' );
function wcpt_ajax( ){
  if( ! empty( $_REQUEST['id'] ) ){
    $sc_attrs = '';
    if( ! empty( $_REQUEST[ $_REQUEST['id'] . '_sc_attrs'] ) ){
      $_REQUEST[ $_REQUEST['id'] . '_sc_attrs'] = json_decode( stripslashes( $_REQUEST[ $_REQUEST['id'] . '_sc_attrs'] ) );

      foreach( $_REQUEST[ $_REQUEST['id'] . '_sc_attrs'] as $key=> $val ){
        if( in_array( $key, $GLOBALS['wcpt_permitted_shortcode_attributes'] ) ){
          $sc_attrs .= ' ' . $key . ' ="' . $val . '" ';
        }
      }
    }
    echo do_shortcode( '[product_table id="'. $_REQUEST['id'] .'" '. $sc_attrs .' ]' );
  }
  die();
}

// removes other woocommerce arguments from the pagination links
function wcpt_paginate_links( $link ) {
    $remove = array( 'add-to-cart', 'variation_id', 'product_id', 'quantity' );
    foreach( $_GET as $key=> $val ){
      if( substr( $key, 0, 10 ) === 'attribute_' ){
        $remove[] = $key;
      }
    }
    return remove_query_arg( $remove, $link );
}

// remove inline editor buttons from 'ALL Tables' page
add_filter('post_row_actions', 'wcpt_row_buttons', 10, 2);
function wcpt_row_buttons($actions, $post) {
  if ($post->post_type=='wc_product_table'){
    unset($actions['inline hide-if-no-js'], $actions['view']);
  }
  return $actions;
}

/* ajax add to cart */
add_action( 'wc_ajax_wcpt_add_to_cart', 'wcpt_add_to_cart' );
add_action( 'wp_ajax_wcpt_add_to_cart', 'wcpt_add_to_cart' );
add_action( 'wp_ajax_nopriv_wcpt_add_to_cart', 'wcpt_add_to_cart' );
function wcpt_add_to_cart(){
  if( $_POST['return_notice'] == "false" ){
    wp_die();
  }

  // success
  if( wc_notice_count('success') ){
    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();

    $data = array(
      'success' => true,
      'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
          'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
        )
      ),
      'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() ),
      'cart_quantity' => WC()->cart->get_cart_contents_count(),
    );

  // error
  }else{
		$data = array(
			'error' => true,
			'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $_REQUEST['product_id'] ), $_REQUEST['product_id'] ),
		);

  }

  // get notice markup
  $data['notice'] = "";
  if( wc_notice_count() ){
    ob_start();
    wc_print_notices();
    $data['notice'] = ob_get_clean();
  }

	wp_send_json( $data );
}

/* additional wcpt fragments hook */
add_filter( 'woocommerce_add_to_cart_fragments', 'wcpt_add_to_cart_fragments', 10, 1 );
function wcpt_add_to_cart_fragments( $fragments ) {
  ob_start();
  include_once( WCPT_PLUGIN_PATH . 'templates/cart-widget.php' );
  $cart_widget = ob_get_clean();
  $fragments['div.wcpt-cart-widget'] = $cart_widget;
  return $fragments;
}

/* cart widget */
add_action( 'wp_ajax_wcpt_cart_widget', 'wcpt_cart_widget' );
add_action( 'wp_ajax_nopriv_wcpt_cart_widget', 'wcpt_cart_widget' );
function wcpt_cart_widget(){
  wp_die( include_once( WCPT_PLUGIN_PATH . 'templates/cart-widget.php' ) );
}

function wcpt_get_product_details_in_cart_including_variations( $product_id ){
  $result = array(
    "quantity" => 0,
    "cart_item_keys_arr" => array(),
  );
  $cart_contents = WC()->cart->get_cart();
  foreach($cart_contents as $cart_item_key=> $item_details){
    if( $product_id === $item_details["product_id"] ){
      $result["cart-item-keys-arr"][] = $cart_item_key;
      $result["quantity"] += $item_details["quantity"];
    }
  }
  return $result;
}

add_action( 'wp_ajax_nopriv_wcpt_get_cart', 'wcpt_get_cart' );
add_action( 'wp_ajax_wcpt_get_cart', 'wcpt_get_cart' );
function wcpt_get_cart(){
  wp_send_json( WC()->cart->get_cart() );
}

/**
 * Enable the shortcode
 */
add_shortcode( 'product_table', 'wcpt_shortcode_product_table' );
function wcpt_shortcode_product_table($atts){
  $atts = apply_filters('wcpt_shortcode_attributes', (array) $atts);

  foreach( $atts as $key => &$val ){
    if( ! in_array( $key, $GLOBALS['wcpt_permitted_shortcode_attributes'] ) ){
      unset( $atts[$key] );
    }
  }

  if( empty( $atts['id'] ) ){
    $post_title = ! empty( $atts['name'] ) ? $atts['name'] : ( ! empty( $atts['title'] ) ? $atts['title'] : '' );

    $loop = new WP_Query(array(
      'posts_per_page' => 1,
      'post_type' => 'wc_product_table',
      'post_status' => 'publish',
      'title' => $post_title,
  		'no_found_rows' => false,
      'fields' => 'ids',
    ));

    if( $loop->have_posts() ){
      $atts['id'] = $loop->posts[0];
    }

    wp_reset_postdata();
  }

  if( empty( $atts['id'] ) ){
    return;
  }

  // gets table data, applies fitlers and caches in global variable
  $GLOBALS['wcpt_table_data'] = wcpt_get_table_data( $atts['id'], 'view' );

  if( $error_message = wcpt_sc_error_checks( $GLOBALS['wcpt_table_data'] ) ){
    $markup = $error_message;

  }else{
    require_once( WCPT_PLUGIN_PATH . 'class-wc-shortcode-product-table.php' );
    $GLOBALS['wcpt_table_instance'] = new WC_Shortcode_Product_Table( $atts );
    $markup = do_shortcode( $GLOBALS['wcpt_table_instance']->get_content() );
    unset($GLOBALS['wcpt_table_data']);
    unset($GLOBALS['wcpt_table_instance']);
  }

  return $markup;
}

/**
 * Process styles from $data of a table
 */
include( WCPT_PLUGIN_PATH . 'style-functions.php' );

/**
 * Parse tpl with shortcodes
 */
function wcpt_parse_2( $template, $product= false ){

  if( gettype($template) !== 'array' ){
    return $template;
  }

  if( ! $product && isset( $GLOBALS['product'] ) ){
    $product = $GLOBALS['product'];
  }

  $markup = '';
  // parse rows
  foreach( $template as $row ){

    // row condition
    if( ! empty( $row['condition'] ) && ! wcpt_condition( $row['condition'] ) ){
      continue;
    }

    // row condition
    if( empty( $row['html_class'] ) ){
      $row['html_class']  = '';
    }

    $row_markup = '';
    // parse elements
    if( ! empty( $row['elements'] ) && gettype($template) == 'array' ){
      foreach( $row['elements'] as $element ){
        wcpt_parse_style_2( $element );
        $reg_tmpl = WCPT_PLUGIN_PATH . 'templates/' . $element['type'] . '.php';
        $pro_tmpl = WCPT_PLUGIN_PATH . 'pro/templates/' . $element['type'] . '.php';

        // lite template
        if( file_exists($reg_tmpl) ){
          $row_markup .= wcpt_parse_ctx_2( $element, $reg_tmpl, $element['type'], $product );

        // pro template
        }else if( file_exists($pro_tmpl) ){
          $row_markup .= wcpt_parse_ctx_2( $element, $pro_tmpl, $element['type'], $product );

        }

      }
    }

    if( $row_markup ){
      $markup .= '<div class="wcpt-item-row wcpt-'. $row['id'] .' '. $row['html_class'] .'">' . $row_markup . '</div>';
      wcpt_parse_style_2( $row );
    }
  }

  return $markup;
}

function wcpt_parse_ctx_2( $element, $elm_tpl, $elm_type, $product = false ){
  extract( $element );
  ob_start();

  if( empty( $html_class ) ){
    $html_class = '';
  }

  $html_class .= ' wcpt-' . $element['id'];

  include $elm_tpl;
  return ob_get_clean();
}

/**
 * Error checking for shortcode - in case user has made some mistake
 */
function wcpt_sc_error_checks( &$table_data ){
  $message = '';

  if( empty( $GLOBALS['wcpt_table_data'] ) ){
    $message = __( 'No product table settings were found for this id. Please try clicking the "Save settings" button at the bottom of the table editor page to save your table settings. If you have already done this and the issue still persists, the cause may be incompatibility with another script on your site. In that case you should contact the plugin developer <a href="https://wcproducttable.com/support/">here</a> for prompt support.', 'wc-product-table');

  }else if(
    empty( $GLOBALS['wcpt_table_data']['columns']['laptop'] ) ||
    ! is_array( $GLOBALS['wcpt_table_data']['columns']['laptop'] ) ||
    ! count( $GLOBALS['wcpt_table_data']['columns']['laptop'] )
  ){
    $message = __( 'It appears you have not set any Laptop Columns for your product table. Therefore, without any columns, your table does not have any content to display. Please follow these steps:
                    <ol>
                      <li>Go to the table editor > Columns tab > Laptop Columns section and use the \'Add a Column\' button to add at least one column.</li>
                      <li>Within this column that you have added, either in the \'Heading\' or \'Cell template\' please add at least one element using the \'+ Add Element\' button. Otherwise this column will simply be empty.</li>
                      <li>Save your table settings after following the above two steps, and then reload this page.</li>
                    </ol>
                    If you have created at least one Laptop Column, with at least one element in it, and your table is saved, this warning message will be removed and your table will be presented. Please visit the <a href="https://wcproducttable.com/tutorials" target="_blank">plugin\'s  tutorials</a> for a clear guide on how to use this plugin and get the most out of it.', 'wc-product-table');

  }else if( wcpt_device_columns_empty( $GLOBALS['wcpt_table_data']['columns']['laptop'] ) ){
    $message = __( 'While you have created at least one column in the Laptop Columns section for this table, it seems you have not created any elements in the columns. Please create at least one element in at least one Laptop Column for this table, then save your table settings and reload this page to see your table.', 'wc-product-table');

  }

  if( $message && current_user_can( 'edit_wc_product_table', (int) $GLOBALS['wcpt_table_data']['id'] ) ){
    return '<div class="wcpt-notice"><span class="wcpt-notice-heading">'. __( 'WooCommerce Product Table Notice', 'wc-product-table') .'</span>'. $message .'</div>';

  }else{
    return false;

  }

}

/**
 * Error checking for shortcode - in case user has made some mistake
 */
function wcpt_device_columns_empty( $device_columns ){
  $no_element = true;
  foreach( $device_columns as $column ){
    // iterate rows
    //-- heading
    foreach( $column['heading']['content'] as $row ){
      if( count( $row['elements'] ) ){
        $no_element = false;
      }
    }
    //-- cell
    foreach( $column['cell']['template'] as $row ){
      if( count( $row['elements'] ) ){
        $no_element = false;
      }
    }
  }

  if( $no_element ){
    return true;

  }else{
    return false;

  }

}

function wcpt_get_cheapest_variation( $product, $available_variations ){

  $lowest_price = false;
  $variation_id = false;

  foreach ( $available_variations as $variation_details ){
    if( false === $lowest_price || $variation_details['display_price'] < $lowest_price ){
      $lowest_price = $variation_details['display_price'];
      $variation_id = $variation_details['variation_id'];
    }
  }

  return wc_get_product( $variation_id );
}

function wcpt_get_most_expensive_variation( $product, $available_variations ){

  $highest_price = false;
  $variation_id = false;

  foreach ( $available_variations as $variation_details ){
    if( false === $highest_price || $variation_details['display_price'] > $highest_price ){
      $highest_price = $variation_details['display_price'];
      $variation_id = $variation_details['variation_id'];
    }
  }

  return wc_get_product( $variation_id );
}

function wcpt_woocommerce_available_variation_filter( $variation_details, $product, $variation ){
  global $wcpt_table_data;

  foreach($wcpt_table_data['columns'] as $key => $column){
    $variation_details['column_'. $key] = wcpt_parse_2( $column['template'], $product, $variation, $variation_details );
  }

  return $variation_details;
}

function wcpt_update_user_filters( $new_filter, $single = true ){
  $found_filter = false;

  foreach( $GLOBALS['wcpt_user_filters'] as &$filter_info ){
    if( $filter_info['filter'] !== $new_filter['filter'] ){
      continue;
    }

    // orderby
    if( in_array( $new_filter['filter'], array( 'orderby', 'price_range', 'search', 'on_sale', 'rating' ) ) ){
      $found_filter = true;
      break;
    }

    // taxonomy
    if(
      in_array( $filter_info['filter'], array( 'taxonomy', 'attribute', 'category' ) ) &&
      $filter_info['taxonomy'] == $new_filter['taxonomy']
    ){
      $found_filter = true;
      break;
    }

    // custom field
    if(
      $filter_info['filter'] == 'custom_field' &&
      strtolower( $filter_info['meta_key'] ) == strtolower( $new_filter['meta_key'] )
    ){
      $found_filter = true;
      break;
    }
  }

  if( $found_filter ){
    foreach( $new_filter as $key => $val ){
      // add value
      if( $key == 'values' ){
        if( ! $single ){
          if( ! is_array( $filter_info['values'] ) ){
            $filter_info['values'] = array();
          }

          if( $filter_info['filter'] == 'custom_field' ){ // avoid duplicates
            $new_filter['values'] = array_map( 'strtolower', $new_filter['values'] );
            $filter_info['values'] = array_map( 'strtolower', $filter_info['values'] );
          }

          $diff = array_diff( $new_filter['values'], $filter_info['values'] );
          $filter_info['values'] = array_merge($filter_info['values'], $diff);
        }else{
          $filter_info['values'] = $val;
        }

      // add clear label
      }else if( $key == 'clear_labels_2' ){
        if( ! $single ){
          if( ! is_array( $filter_info['clear_labels_2'] ) ){
            $filter_info['clear_labels_2'] = array();
          }
          if( $new_filter['clear_labels_2'] ){
            foreach( $new_filter['clear_labels_2'] as $key=> $val ){
              if( empty( $filter_info['clear_labels_2'][$key] ) || $filter_info['clear_labels_2'][$key] !== $val ){
                $filter_info['clear_labels_2'][$key] = $val;
              }
            }
          }
        }else{
          $filter_info['clear_labels_2'] = $val;
        }

      // other key
      }else{
        $filter_info[$key] = $val;
      }

    }

  }else{
    $GLOBALS['wcpt_user_filters'][] = $new_filter;

  }
}

// Relabel items
function wcpt_relabel_items( &$items, $relabels= array() ){
  foreach( $items as &$item ){
    foreach( $relabels as $relabel ){
      if( strtolower($item['item']) === strtolower($relabel['item']) ){
        $item['label'] = wcpt_parse_2( $relabel['label'] );
      }
    }
  }

  return $items;
}

add_action( 'wp_ajax_nopriv_wcpt_get_product_form_modal', 'wcpt_get_product_form_modal' );
add_action( 'wp_ajax_wcpt_get_product_form_modal', 'wcpt_get_product_form_modal' );
function wcpt_get_product_form_modal() {
  $product_id = (int) $_REQUEST['product_id'];
  if( get_post_status( $product_id ) == 'publish' ){
    ob_start();
    echo wcpt_get_product_form( array( 'id' => $product_id ) );
    echo ob_get_clean();
  }
  wp_die();
}

function wcpt_price( $price ){
  $price = number_format( (float) $price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() );
  $currency = '<span class="wcpt-currency">' . esc_attr( get_woocommerce_currency_symbol() ) . '</span>';

  return str_replace( array( '%1$s', '%2$s' ), array( $currency, $price ), get_woocommerce_price_format() );
}

function wcpt_get_product_form( $atts ){

  if( ! empty( $GLOBALS['post'] ) ){
    $_post = $GLOBALS['post'];
  }

  $product_id = $atts['id'];

  $product = apply_filters('wcpt_product', wc_get_product($product_id), null);

  $GLOBALS['product'] = $product;

  $product_type = $product->get_type();

  ob_start();
  ?>
  <div class="wcpt-product-form wcpt-modal" data-wcpt-product-id="<?php echo $product_id; ?>">
    <div class="wcpt-modal-content">
      <div class="wcpt-close-modal">
        <?php echo wcpt_icon('x', 'wcpt-close-modal-icon'); ?>
      </div>
      <?php
      echo '<span class="wcpt-product-form-title">' . $product->get_title() . ' <span class="wcpt-product-form-price">' . $product->get_price_html() . '</span></span>';
      call_user_func('woocommerce_'. $product_type .'_add_to_cart');
      ?>
    </div>
  </div>
  <?php

  if( ! empty( $_post ) ){
    global $post;
    $post = $_post;
  }

  return ob_get_clean();
}

function wcpt_icon( $icon_name, $html_class= '', $style= null, $tooltip='', $title='' ){
  $icon_file = WCPT_PLUGIN_PATH . 'assets/feather/'. $icon_name .'.svg';
  if( file_exists( $icon_file ) ){
    if( $style ){
      $style = ' style="'. $style .'"';
    }

    $tooltip_html_class = '';
    if( $tooltip ){
      $tooltip_html_class = 'wcpt-tooltip';
    }

    if( $title ){
      $title = 'title="'. htmlentities( $title ) .'"';
    }

    echo '<span class="wcpt-icon wcpt-icon-'. $icon_name .' '. $html_class .' '. $tooltip_html_class .'" '. $style .' '. $title .'>';

    if( $tooltip ){
      echo '<span class="wcpt-tooltip-content">'. $tooltip .'</span>';
    }

    include( $icon_file );
    echo '</span>';
  }
}

function wcpt_get_column_by_index( $column_index= 0, $device= 'laptop', &$data= false ){

  $device_columns = wcpt_get_device_columns($device, $data);

  if( ! $device_columns ){
    return false;

  }else{
    return $device_columns[$column_index];
  }

}

function wcpt_sortby_get_matching_option_index( $match_user_filter, $available_options ){
  if( ! $available_options ){
    return false;
  }
  foreach( $available_options as $option_index => $option ){
    if( wcpt_check_sort_match( $option, $match_user_filter ) ){
      return $option_index;
    }
  }
  return false;
}

function wcpt_check_sort_match( $set_1, $set_2 ){
  // match begins from 'orderby'
  if( $set_1['orderby'] !== $set_2['orderby'] ){
    return false;
  }

  // no other params need to match
  if( in_array( $set_1['orderby'], array( 'price', 'price-desc', 'date', 'rating', 'popularity', 'rand' ) ) ){
    return true;
  }

  // order must matching for remaining - title, custom field
  if( strtolower( $set_1['order'] ) != strtolower( $set_2['order'] ) ){
    return false;
  }

  // enough match for title
  if( $set_1['orderby'] == 'title' ){
    return true;
  }

  // finally, enough match for custom field
  if( 
    ! empty( $set_1['meta_key'] ) &&
    ! empty( $set_2['meta_key'] ) &&
    $set_1['meta_key'] == $set_2['meta_key']
  ){
    return true;
  }
}

function wcpt_get_column_sort_filter_info(){

  $field_name_prefix = $GLOBALS['wcpt_table_data']['id'] . '_';

  $column_index = (int) substr( $_GET[ $field_name_prefix . 'orderby' ], 7 );
  $device = $_GET[ $field_name_prefix . 'device' ];
  $order = $_GET[ $field_name_prefix . 'order' ];

  $column = wcpt_get_column_by_index( $column_index, $device );

  $filter_info = array(
    'filter' => 'orderby',
  );

  if( $column['sorting_enabled'] ){
    $filter_info['orderby'] = $column['orderby'];
    $filter_info['order'] = $order;
    if( $column['orderby'] == 'meta_value' || $column['orderby'] == 'meta_value_num' ) {
      $filter_info['meta_key'] = $column['meta_key'];
    }

    // special case price-desc
    if( $column['orderby'] == 'price' && $order == 'DESC' ){
      $filter_info['orderby'] = 'price-desc';
    }
  }

  return $filter_info;

}

function wcpt_get_nav_filter( $name, $second= false ){
  foreach( $GLOBALS['wcpt_user_filters'] as $filter_info ){
    if( $filter_info['filter'] == $name ){
      if( ! $second  ){
        return $filter_info;
      }else{
        switch ($name) {
          case 'custom_field':
            if( strtolower( $filter_info['meta_key'] ) == strtolower( $second ) ){
              return $filter_info;
            }
            break;

          default: // attribute / taxonomy / product_cat
            if( $filter_info['taxonomy'] == $second ){
              return $filter_info;
            }
            break;
        }
      }

    }
  }

  return false;
}

function wcpt_clear_nav_filter( $name, $second= false ){
  foreach( $GLOBALS['wcpt_user_filters'] as $key => &$filter_info ){
    if( $filter_info['filter'] == $name ){
      if( ! $second  ){
        unset( $GLOBALS['wcpt_user_filters'][$key] );
      }else{
        switch ($name) {
          case 'custom_field':
            if( strtolower( $filter_info['meta_key'] ) == strtolower( $second ) ){
              unset( $GLOBALS['wcpt_user_filters'][$key] );
            }
            break;

          default: // attribute / taxonomy / product_cat
            if( ! empty( $filter_info['taxonomy'] ) && $filter_info['taxonomy'] == $second ){
              unset( $GLOBALS['wcpt_user_filters'][$key] );
            }
            break;
        }
      }

    }
  }
}

function wcpt_get_sorting_html_classes( $col_orderby, $col_meta_key ){
  extract( wcpt_get_current_sorting() );

  $col_sorted = false;

  if( $current_orderby == $col_orderby ){
    if( in_array( $current_orderby, array( 'meta_value', 'meta_value_num' ) ) ){
      if( $current_meta_key == $col_meta_key ){
        $col_sorted = true;
      }
    }else if( in_array( $current_orderby, array( 'rating', 'date' ) ) ){
    // fixed order
      $current_order = 'desc';
      $col_sorted = true;

    }else{
      $col_sorted = true;
    }

  }else if( $current_orderby == 'price-desc' && $col_orderby == 'price' ){
    $current_order = 'desc';
    $col_sorted = true;

  }else if( in_array( $current_orderby, array( 'meta_value', 'meta_value_num' ) ) && $current_meta_key == '_sku' && in_array( $col_orderby, array( 'sku', 'sku_num' ) ) ){
    $col_sorted = true;

  }

  if( $col_sorted ){

    if( $col_orderby == 'rating' || $col_orderby == 'date' ){
      return array(
        'sorting_class' => 'wcpt-sorting-' . $current_order,
        'sorting_class_asc' => 'wcpt-hide',
        'sorting_class_desc' => $current_order == 'desc' ? 'wcpt-active' : 'wcpt-inactive',
      );
    }

    return array(
      'sorting_class' => 'wcpt-sorting-' . $current_order,
      'sorting_class_asc' => $current_order == 'asc' ? 'wcpt-active' : 'wcpt-inactive',
      'sorting_class_desc' => $current_order == 'desc' ? 'wcpt-active' : 'wcpt-inactive',
    );
  }

  // column not sorted
  return array(
    'sorting_class' => '',
    'sorting_class_asc' => ( $col_orderby == 'rating' || $col_orderby == 'date' ) ? 'wcpt-hide' : 'wcpt-inactive',
    'sorting_class_desc' => 'wcpt-inactive',
  );

}

function wcpt_get_current_sorting(){
  $sorting = wcpt_get_nav_filter( 'orderby' );

  return array(
    'current_orderby' => $sorting['orderby'],
    'current_order' => strtolower( $sorting['order'] ),
    'current_meta_key' => $sorting['meta_key'],
  );
}

function wcpt_get_column_sorting_info( $col_index, $device= 'laptop' ){
  $col_index = (int) $col_index;
  if( ! in_array( $device, array( 'laptop', 'tablet', 'phone' ) ) ){
    $device = 'laptop';
  }

  // rows
  foreach( $GLOBALS['wcpt_table_data']['columns'][$device][$col_index]['heading']['content'] as $row ){
    // elements
    foreach( $row['elements'] as $element ){
      if( $element['type'] == 'sorting' ){
        return $element;
      }
    }
  }

  return NULL;
}

/* get table data from post or cache */
function wcpt_get_table_data($id= false, $context= 'view'){
  if( $id ){
  // return table data based on post id

    if( get_post_type( $id ) !== 'wc_product_table' ){
      return false;
    }

    $table_data = json_decode( get_post_meta( $id, 'wcpt_data', true ), true );
    $table_data['id'] = $id;

    return apply_filters( 'wcpt_data', $table_data, $context );

  }else{
  // return current cached table
    return $GLOBALS['wcpt_table_data'];

  }
}

// modify table data
add_filter( 'wcpt_data', 'wcpt_modify_table_data', 10, 2 );
function wcpt_modify_table_data( $data, $context ){

  // auto-include new category children
  if( ! empty( $data ) && ! empty( $data['query']['category'] ) ){
    $terms = wcpt_get_terms( 'product_cat', $data['query']['category'] );
    if( $terms && ! is_wp_error( $terms ) ){
      $term_taxonomy_id = array();
      foreach( $terms as $term ){
        $term_taxonomy_id[] = (string) $term->term_taxonomy_id;
      }

      $data['query']['category'] = $term_taxonomy_id;
    }
  }

  // fix tooltip style
  // $start = microtime(true);
  wcpt_tooltip_selector_fix( $data['columns'] );
  // wcpt_console_log( microtime(true) - $start );
 
  return $data;
}

// fixes the tooltip selector 
function wcpt_tooltip_selector_fix ( &$arr ){
  // is tooltip element
  if( 
    isset( $arr['type'] ) &&
    $arr['type'] == 'tooltip'
  ){

    if( 
      isset( $arr['style'] ) &&
      isset( $arr['style']['[id] > .wcpt-tooltip-content'] )
    ){
      $arr['style']['[id] > .wcpt-tooltip-content-wrapper > .wcpt-tooltip-content'] = $arr['style']['[id] > .wcpt-tooltip-content'];
      unset( $arr['style']['[id] > .wcpt-tooltip-content'] );
    }
    return;    
  }

  // else go hunting recursively
  foreach( $arr as $key=> &$val ){
    if( gettype( $val ) == 'array' ){
      wcpt_tooltip_selector_fix ( $val );
    }
  }
}

/* columns related */
function wcpt_get_device_columns( $device, &$data= false ){
  if( ! $data ){
    $data =& $GLOBALS['wcpt_table_data'];
  }

  return ! empty( $data['columns'][$device] ) ? $data['columns'][$device] : false;
}

/* columns related */
function wcpt_get_device_columns_2( $device, &$data= false ){
  if( ! $data ){
    $data =& $GLOBALS['wcpt_table_data'];
  }

  return ! empty( $data['columns'][$device] ) ? $data['columns'][$device] : false;
}

/* elements related */
function wcpt_get_shortcode_element_manager($shortcode_tag){
  if( '_filter' == substr($shortcode_tag, -7) || in_array( $shortcode_tag, array( 'sort_by', 'result_count' ) ) ){
    return 'navigation';
  }else{
    return 'column';
  }
}

function wcpt_get_column_elements($data= false){
  if( ! $data ){
    $data = wcpt_get_table_data();
  }
  return $data['elements']['column'];
}

function wcpt_get_navigation_elements($data= false){
  if( ! $data ){
    $data = wcpt_get_table_data();
  }
  return $data['elements']['navigation'];
}

/* debug */
function wcpt_console_log( $obj, $label= false, $echo = true ){
  $mkp = '<script>console.log( '. ($label ? ' "'. $label .'", ' : '') . json_encode( $obj ) .')</script>';
  if( $echo ){
	  echo $mkp;
  }else{
	  return $mkp;
  }
}


/* navigation */
// header
function wcpt_parse_navigation ( $data= false ){

  if( ! $data ){
    $data = wcpt_get_table_data();
  }

  if( empty( $data['navigation'] ) ){
    return;
  }

  ob_start();

  // laptop
  if(
    isset( $data['navigation']['laptop']['left_sidebar'] ) &&
    ! empty( $data['navigation']['laptop']['left_sidebar'][0] ) &&
    count( $data['navigation']['laptop']['left_sidebar'][0]['elements'] )
  ){
    //  {{maybe-always}} placeholder for hiding in responsive mode
    ?>
    <div class="wcpt-navigation wcpt-left-sidebar {{maybe-always}}">
      <?php echo wcpt_parse_2( $data['navigation']['laptop']['left_sidebar'] ); ?>
    </div>
    <?php
  }

  echo '<div class="wcpt-navigation wcpt-header {{maybe-always}}">';
  foreach( $data['navigation']['laptop']['header']['rows'] as $row ){

    if( empty( $row['ratio'] ) ) $row['ratio'] = '100-0'; // default val
    $empty_row = true;

    ob_start(); // will feed $row_markup
    ?>
    <div class="wcpt-filter-row wcpt-ratio-<?php echo $row['ratio']; ?> %maybe_hide%">
      <?php
        foreach( array( 'left', 'center', 'right' ) as $position ){
          if( false !== strpos( $row['columns_enabled'], $position ) ){
            echo '<div class="wcpt-filter-column wcpt-'. $position .'">';
            if( $column_content = wcpt_parse_2( $row['columns'][$position]['template'] ) ){
              $empty_row = false;
            }
            echo $column_content;
            echo '</div>';
          }
        }
      ?>
    </div>
    <?php
    $row_markup = ob_get_clean();

    if( $empty_row ){
      $row_markup = str_replace('%maybe_hide%', 'wcpt-hide', $row_markup);
    }else{
      $row_markup = str_replace('%maybe_hide%', '', $row_markup);
    }

    echo $row_markup;
  }
  echo '</div>';

  // phone and tablet
  ?>
  <div class="wcpt-responsive-navigation"><?php
    if( empty( $data['navigation']['phone'] ) ){
      $data['navigation']['phone'] = '';
    }
    $res_nav = wcpt_parse_2( $data['navigation']['phone'] );
    echo $res_nav;
  ?></div>
  <?php
  include( WCPT_PLUGIN_PATH . 'templates/modals.php' );

  $mkp = ob_get_clean();
  $always_show = 'wcpt-always-show';
  if( $res_nav ){
    $always_show = '';
  }
  $mkp = str_replace('{{maybe-always}}', $always_show, $mkp);

  return $mkp;
}

// fitler header
add_filter( 'wcpt_navigation', 'wcpt_navigation_filter' );
function wcpt_navigation_filter( $navigation_header ){

  global $wcpt_products;

  $paged    = max( 1, $wcpt_products->get( 'paged' ) );
  $per_page = $wcpt_products->get( 'posts_per_page' );
  $total    = $wcpt_products->found_posts;
  $first    = ( $per_page * $paged ) - $per_page + 1;
  $last     = min( $total, $wcpt_products->get( 'posts_per_page' ) * $paged );

  $result_count_html_class = '';

  if ( $total == 1 ) {
    $result_count_html_class = 'wcpt-single-result';
  } else if( $total == 0 ) {
    $result_count_html_class = 'wcpt-no-results';
  } else if( $total <= (int) $per_page || -1 === (int) $per_page ) {
    $result_count_html_class = 'wcpt-single-page';
  }

  $search = array(
    '[result-count-html-class]',
    '[displayed_results]',
    '[total_results]',
    '[first_result]',
    '[last_result]',
  );
  $replace = array(
    $result_count_html_class,
    $last - $first + 1,
    $total,
    $first,
    $last,
  );

  return str_replace( $search, $replace, $navigation_header );

}

function wcpt_corner_options(){
  ?>
  <div class="wcpt-editor-corner-options">
    <i class="wcpt-editor-row-move-up wcpt-sortable-handle" wcpt-move-up title="Move row up">
      <?php wcpt_icon('chevron-up'); ?>
    </i>
    <i class="wcpt-editor-row-move-down wcpt-sortable-handle" wcpt-move-down title="Move row down">
      <?php wcpt_icon('chevron-down'); ?>
    </i>
    <i class="wcpt-editor-row-duplicate" wcpt-duplicate-row title="Copy row">
      <?php wcpt_icon('copy'); ?>
    </i>
    <i class="wcpt-editor-row-remove" wcpt-remove-row title="Delete row">
      <?php wcpt_icon('x'); ?>
    </i>
  </div>

  <?php
}

function wcpt_get_cart_item_quantity( $product_id ){
  global $woocommerce;
  $in_cart = 0;

  if( is_object( $woocommerce->cart ) ){
    $contents = $woocommerce->cart->cart_contents;
    if( $contents ){
      foreach( $contents as $key=> $details ){
        if( $details['product_id'] == $product_id ){
          $in_cart += $details['quantity'];
        }
      }
    }
  }

  return $in_cart;
}

add_action( 'wp_ajax_wcpt_get_terms', 'wcpt_get_terms_ajax' );
function wcpt_get_terms_ajax(){
  $term_taxonomy_id = ! empty( $_POST['limit_terms'] ) ? $_POST['limit_terms'] : false;
  $terms = wcpt_get_terms($_POST['taxonomy'], $term_taxonomy_id);

  $relabels = array();
  $timestamp = time();
  foreach ($terms as $term) {
    // code...
    $relabels[] = array(
      'term' => wp_specialchars_decode($term->name),
      'ttid' => $term->term_taxonomy_id,
      'label' => array(
        array(
          'id' => $timestamp++,
          'style' => array(),
          'elements' => array(
            array(
              'id' => $timestamp++,
              'style' => array(),
              'type' => 'text',
              'text' => '[term]',
            ),
          ),
        )
      ),
      'tooltip' => '',
      'link' => '',
      'target' => '_self',
      'id' => $timestamp++,
    );
  }

  wp_send_json($relabels);
}

// gets terms include children
function wcpt_get_terms( $taxonomy, $term_taxonomy_ids= false, $hide_empty= false ){
	// user has set terms
	if( ! empty( $term_taxonomy_ids ) ){
		// include all child terms
		foreach( $term_taxonomy_ids as $term_taxonomy_id ){
			// get its children
			$child_terms = get_term_children($term_taxonomy_id, $taxonomy);
			// include if not already there
			if( $child_terms && ! is_wp_error( $child_terms ) ){
				$diff = array_diff( $child_terms, $term_taxonomy_ids );
				$term_taxonomy_ids = array_merge( $term_taxonomy_ids, $diff );
			}
		}

    global $sitepress;
    if( 
      ! empty( $sitepress ) &&
      $taxonomy == 'product_cat'
    ){
      $filter_exists = remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10 );
    }

		// get terms
		$terms = get_terms( array(
			'taxonomy' => $taxonomy,
			'hide_empty' => $hide_empty,
			'term_taxonomy_id' => $term_taxonomy_ids,
    ) );

    if( 
      ! empty( $sitepress ) &&
      ! empty( $filter_exists ) &&
      $taxonomy == 'product_cat'
    ){
      add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 3 );
    }

	// user didn't set terms, so get all
	}else{
		$terms = get_terms( array(
			'taxonomy' => $taxonomy,
			'hide_empty' => $hide_empty,
		) );
  }

	return $terms;
}

function wcpt_include_taxonomy_walker(){
  if( ! class_exists( 'WCPT_Taxonomy_Walker' ) ){
    class WCPT_Taxonomy_Walker extends Walker {

      var $db_fields = array('parent' => 'parent', 'id' => 'term_id');
			var $args;

	    function __construct($args) {
        if( empty( $args ) ){
          $args = array();
        }

        if( empty( $args['taxonomy'] ) ){
          $args['taxonomy'] = 'product_cat';
        }

        if( ! $args['taxonomy_obj'] = get_taxonomy( $args['taxonomy'] ) ){
          return false;
        }

        if( ! empty( $args['exclude'] ) ){
          $term_taxonomy_ids = array();
          foreach( $args['exclude'] as $term_name ){
            $term = get_term_by( 'name', $term_name, $args['taxonomy'] );
            $term_taxonomy_ids[] = $term->term_taxonomy_id;
          }
          $args['exclude'] = $term_taxonomy_ids;

        }else{
          $args['exclude'] = array();

        }

        if( empty( $args['single'] ) ){
          $args['single'] = false;
        }

        if( empty( $args['hide_empty'] ) ){
          $args['hide_empty'] = false;
        }

        if( ! isset( $args['pre_open_depth'] ) ){
          $args['pre_open_depth'] = 1;
        }

        if( ! isset( $args['option_class'] ) ){
          $args['option_class'] = 'wcpt-dropdown-option';
        }

        $this->args = $args;
	    }

      function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ){
        $category = (object) $category;
        $children = get_terms( $this->args['taxonomy'], array(
          'parent' => $category->term_id, 
          'hide_empty' => 0,
          'exclude' => $this->args['exclude'],
          'fields' => 'ids',
        ) );

				$has_children = false;
				$child_checked = false;
				if( ! is_wp_error( $children ) && count( $children ) ){
					$has_children = true;
					$child_checked = false;
					if(
            ! empty( $_GET[$this->args['field_name']] ) &&
            count( array_intersect( $children, $_GET[$this->args['field_name']] ) )
					){
						$child_checked = true;
					}
				}

				$checked = false;
				if(
          ! empty( $_GET[$this->args['field_name']] ) &&
          in_array( $category->term_taxonomy_id, $_GET[$this->args['field_name']] )
				){
					$checked = true;
					// use filter in query
					$filter_info = array(
						'filter'        => ( $this->args['taxonomy'] == 'product_cat' ) ? 'category' : 'taxonomy',
						'taxonomy'      => $this->args['taxonomy'],
						'values'        => array( $category->term_taxonomy_id ),
						'operator'      => 'IN',
						'clear_label'   => $this->args['taxonomy_obj']->labels->singular_name,
					);

          if( ! empty( $category->clear_label ) ){
						$filter_info['clear_labels_2'] = array(
							$category->value => str_replace(
                array( '[option]', '[filter]' ),
                array( $category->name, $this->args['taxonomy_obj']->labels->singular_name ),
                $category->clear_label
              ),
						);
					}else{
						$filter_info['clear_labels_2'] = array(
							$category->value => $category->name, $this->args['taxonomy_obj']->labels->singular_name . ' : ' . $category->name,
						);
					}

					wcpt_update_user_filters( $filter_info, $this->args['single'] );
				}

        ob_start();
        ?>
        <div
					class="<?php echo $this->args['option_class'] ?> <?php echo $has_children ? 'wcpt-accordion' : ''; ?> <?php echo ($checked || $child_checked) ? 'wcpt-ac-open' : ''; ?> <?php echo $this->args['pre_open_depth'] > $depth ? 'wcpt-ac-open' : ''; ?>"
          data-wcpt-value="<?php echo $category->term_taxonomy_id; ?>"
          data-wcpt-open="<?php echo $this->args['pre_open_depth']; ?>"
          data-wcpt-depth="<?php echo $depth; ?>"
				>
          <label 
            class="<?php echo $checked ? 'wcpt-active' : ''; ?>" 
            data-wcpt-value="<?php echo $category->term_taxonomy_id; ?>"
            data-wcpt-slug="<?php echo $category->slug; ?>"
          >
            <input
              class="<?php echo ( is_wp_error( $children ) || ! count( $children ) ) ? '' : 'wcpt-hr-parent-term'; ?>"
              type="<?php echo $this->args['single'] ? 'radio' : 'checkbox'; ?>"
  						name="<?php echo $this->args['field_name'] ?>[]"
              value="<?php echo $category->term_taxonomy_id; ?>"
							<?php echo $checked ? ' checked="checked" ' : ''; ?>
          /><?php echo $category->label; ?>
						<?php echo $has_children ? wcpt_icon('chevron-down', 'wcpt-ac-icon') : ''; ?>
          </label>
        <?php
        $output .= ob_get_clean();
      }

      function end_el( &$output, $object, $depth = 0, $args = array() ){
        $output .= '</div>';
      }

      function start_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= '<div class="wcpt-hr-child-terms-wrapper wcpt-dropdown-sub-menu wcpt-ac-content">';
      }

      function end_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= '</div>';
      }
    }
  }
}

// condition
require_once( WCPT_PLUGIN_PATH . 'condition.php' );

// condition
require_once( WCPT_PLUGIN_PATH . 'search.php' );

// WCPT PRO buttons, covers and markers
function wcpt_elm_type_list( $element_types ){
  ?>
  <div class="wcpt-block-editor-element-type-list">
    <?php
      ob_start();
      wcpt_pro_badge();
      $pro_badge = ob_get_clean();

      foreach( $element_types as $element_type ){

        if(
          $element_type == 'Availability Filter [pro]' &&
          get_option('woocommerce_hide_out_of_stock_items', 'no') == 'yes'
        ){
          continue;
        }

        if( ! $element_type ) {
          echo '<div class="wcpt-clear"></div>';

        }else{

          $slug = strtolower( str_replace(' ', '_', str_replace( ' [pro]', '', $element_type) ) );
          $lock = $pro_badge && ( false !== strpos( $element_type, '[pro]' ) ) ? ' wcpt-pro-lock wcpt-disabled ' : '';
          $label = str_replace(' [pro]', $pro_badge, $element_type);
          if( false !== strpos($label, "__") ){
            $label = substr($label, 0, strpos($label, "__"));
          }

          ?>
          <span class="wcpt-block-editor-element-type <?php echo $lock; ?>" data-elm="<?php echo $slug; ?>"><?php echo $label; ?></span>
          <?php
        }

      }
    ?>
  </div>
  <?php
}

function wcpt_how_to_use_link($link){
  ?>
  <a href="<?php echo $link; ?>" target="_blank" class="wcpt-how-to-use">
    <?php wcpt_icon('file-text'); ?>
    <span>How to use</span>
  </a>
  <?php
}

// add the import export markup
add_action('admin_footer', 'wcpt_insert_import_export_markup');
function wcpt_insert_import_export_markup(){
  $arr = explode( '/', $_SERVER['PHP_SELF'] );
  $page = end( $arr );

  if(
    $page !== 'edit.php' ||
    ! empty( $_GET['page'] ) ||
    (
      empty( $_GET['post_type'] ) ||
      $_GET['post_type'] !== 'wc_product_table'
    )
  ){
    return;
  }
  $wcpt_import_export_button_label_append = 'tables';
  $wcpt_import_export_button_context = 'tables';
  require_once('editor/settings-partials/import-export.php');
  ?>
  <style>
    .wcpt-import-export-wrapper {
      display: none;
    }
  </style>

  <script>
    (function($){
      $('.wcpt-import-export-wrapper').appendTo('#wpbody-content').show();
    })(jQuery)
  </script>
  <?php
}

// checks if template is empty
function wcpt_is_template_empty($tpl){
  if( empty( $tpl ) ){
    return true;
  }

  if( in_array( gettype( $tpl ), array( 'string', 'number' ) ) ){
    return false;
  }

  $has_content = false;
  foreach( $tpl as $row ){
    if( 
      ! empty( $row['elements'] ) &&
      count( $row['elements'] )
    ){
      $has_content = true;
    }
  }

  return ! $has_content;
}

// list image sizes
function wcpt_get_all_image_sizes() {
  global $_wp_additional_image_sizes;

  $default_image_sizes = get_intermediate_image_sizes();

  $image_sizes = array();

  foreach ( $default_image_sizes as $size ) {
    $image_sizes[ $size ] = array(
      'width' => intval( get_option( "{$size}_size_w" ) ),
      'height' => intval( get_option( "{$size}_size_h" ) ),
      'crop' => get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false,
    );
  }

  if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
    $image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
  }

  return $image_sizes;
}

/* PRO */

// add a small badge next to pro features
function wcpt_pro_badge(){
  if( ! defined( 'WCPT_PRO' ) ){
    ?>
    <span class="wcpt-pro-badge">PRO</span>
    <?php
  }
}

// a disable cover over set of PRO features
function wcpt_pro_cover(){
  if( ! defined( 'WCPT_PRO' ) ){
    echo 'wcpt-pro-cover';
  }
}

// disable and print a PRO select option with badge
function wcpt_pro_option($val, $label){
  if( ! defined( 'WCPT_PRO' ) ){
    $label = $label . ' (PRO only)';
    $disabled = 'disabled';
  }else{
    $disabled = '';
  }
  ?>
  <option value="<?php echo $val; ?>" <?php echo $disabled; ?> ><?php echo $label; ?></option>
  <?php
}

// disable and print a PRO radio option with badge
function wcpt_pro_radio($val, $label, $mkey){
  ?>
  <label>
  <input type="radio" value="<?php echo $val; ?>" wcpt-model-key="<?php echo $mkey; ?>" <?php echo defined( 'WCPT_PRO' ) ? '' : 'disabled'; ?>>
  <?php echo $label; wcpt_pro_badge(); ?>
  </label>
  <?php
}

// disable and print a PRO checkbox option with badge
function wcpt_pro_checkbox($val, $label, $mkey){
  ?>
  <label>
    <input type="checkbox" value="<?php echo $val; ?>" wcpt-model-key="<?php echo $mkey; ?>" <?php echo defined( 'WCPT_PRO' ) ? '' : 'disabled'; ?>>
    <?php echo $label; wcpt_pro_badge(); ?>
  </label>
  <?php
}

// include PRO materials
if( file_exists( WCPT_PLUGIN_PATH . 'pro/' ) ){
  require( WCPT_PLUGIN_PATH . 'pro/functions.php' );
}

// manage WCPT All Product Tables page columns
add_filter( 'manage_wc_product_table_posts_columns', 'wcpt_set_shortcode_column' );
function wcpt_set_shortcode_column($columns) {
  $new_columns = array();
  foreach( $columns as $name => $label ){
    $new_columns[$name] = $label;
    if( $name == 'title' ){
      $new_columns['shortcode'] = __( 'Shortcode', 'wc-product-table' );
    }
  }
  return $new_columns;
}

// add shortcode column in WCPT All Product Tables page
add_action( 'manage_wc_product_table_posts_custom_column' , 'wcpt_shortcode_column', 10, 2 );
function wcpt_shortcode_column( $column, $post_id ) {
  switch ( $column ) {
    case 'shortcode' :
      ?>
      <input style="width: 230px; border: 1px solid #e2e2e2; padding: 10px; background: #f7f7f7;" value="<?php esc_html_e( '[product_table id="'. $post_id .'"]' ); ?>" onClick="this.setSelectionRange(0, this.value.length)" readonly />
      <?php
      break;
  }
}

// make shortcode column sortable in WCPT All Product Tables page
add_filter( 'manage_edit-wc_product_table_sortable_columns', 'wcpt_shortcode_column_sortable' );
function wcpt_shortcode_column_sortable( $columns ) {
    $columns['shortcode'] = 'id';
    return $columns;
}

// terms for variation
add_action( 'wp_ajax_wcpt_get_attribute_terms', 'wcpt_get_attribute_terms_ajax' );
function wcpt_get_attribute_terms_ajax(){
  if( empty( $_POST['taxonomy'] ) ){
    return false;
  }

  $terms = get_terms( array(
    'taxonomy' => (string) $_POST['taxonomy'],
    'hide_empty' => false,
    'orderby' => 'menu_id',
  ) );

  if( is_wp_error( $terms ) ){
    return false;
    die();
  }

  foreach( $terms as &$term ){
    $term_obj = get_term( $term->term_id, (string) $_POST['taxonomy'] );
    $term->name = esc_html( $term_obj->name );
  }

  wp_send_json($terms);
}

// get matching variation from attribute_terms
function wcpt_find_matching_product_variation( $product, $attributes ) {
  foreach( $attributes as $key => $value ) {
    if( strpos( $key, 'attribute_' ) === 0 ) {
        continue;
    }

    unset( $attributes[ $key ] );
    $attributes[ sprintf( 'attribute_%s', $key ) ] = $value;
  }

  if( class_exists('WC_Data_Store') ) {
    $data_store = WC_Data_Store::load( 'product' );
    return $data_store->find_matching_product_variation( $product, $attributes );

  } else {
    return $product->get_matching_variation( $attributes );

  }
}

function wcpt_find_closests_matching_product_variation( $product, $attributes ){
  // iterate the variations
  $partial_match = false; // variation has some extra attributes
  $matched_variation = false;
  $variation_attributes = array(); // attributes of the complete / partial variation (used for pre-set in form)
  $last_attributes_diff = 100000; // extra attributes in the last partial match variation
  $total_attributes = count( array_keys( $attributes ) );

  // wmpl
  global $sitepress;
  if(
    ! empty( $sitepress ) &&
    $sitepress->get_default_language() !==  $sitepress->get_current_language()
  ){
    $_attributes = array();
    foreach( $attributes as $attr => $term_slug ){
      $term = get_term_by('slug', $term_slug, substr($attr, 10 ) );
      $_attributes[$attr] = $term->slug;
    }

    $attributes = $_attributes;
  }

  $variations = wcpt_get_variations($product);
  foreach( $variations as $variation ){
    // skip if variation has too few attributes
    $total_variation_attributes = count( array_keys( $variation['attributes'] ) );
    if( $total_variation_attributes < $total_attributes ){
      continue;
    }

    // all the desired attributes must be in the variation
    $match = true;
    foreach( $attributes as $attribute => $term ){
      // skip variation if it does not have a desired attribute / match
      if(
        empty( $variation['attributes'][$attribute] ) ||
        $variation['attributes'][$attribute] !== $term
      ){
        $match = false;
        break;
      }
    }

    if( ! $match ){
      continue;

    }else{

      // complete match
      $attributes_diff = $total_variation_attributes - $total_attributes;
      if( ! $attributes_diff ){
        return array(
          'type' => 'complete_match',
          'variation' => $variation,
          'variation_id' => $variation['variation_id'],
          'variation_attributes' => $variation['attributes']
        );

      // partial match
      }else if( $attributes_diff < $last_attributes_diff ){
        $partial_match = $variation['variation_id'];
        $variation_attributes = $variation['attributes'];
        $last_attributes_diff = $attributes_diff;
        $matched_variation = $variation;

      }

    }

  }

  if( $partial_match ){
    return array(
      'type' => 'partial_match',
      'variation' => $matched_variation,
      'variation_id' => $partial_match,
      'variation_attributes' => $variation_attributes,
    );

  } else {
    return false;

  }

}

// get variations array for the product
$wcpt_variations_cache = array();
function wcpt_get_variations($product){

  global $wcpt_variations_cache;

  $id = $product->get_id();

  if( ! empty( $wcpt_variations_cache[$id] ) ){
    return $wcpt_variations_cache[$id];

  }else{
  	$wcpt_variations_cache[$id] = $product->get_available_variations();
    return $wcpt_variations_cache[$id];
  }

  //  // daily cache conflicts with user role plugins
  // if( ! empty( $wcpt_variations_cache[$id] ) ){
  //   return $wcpt_variations_cache[$id];

  // }else{
  //   $transient_name = 'wcpt_variations_' . $product->get_id();
  //   $variations = get_transient( $transient_name );
  //   if( ! $variations ){
  //   	$variations = $product->get_available_variations();
  //   	set_transient( $transient_name, $variations, 60 * 60 * 24 );
  //   }
  //   $wcpt_variations_cache[$id] = $variations;
  //   return $variations;

  // }
}

// get default variation for current product
function wcpt_get_default_variation( $product ){
  if( ! $default_attributes = $product->get_default_attributes() ){
    return false;
  }

  $_default_attributes = array();
	foreach( $default_attributes as $key => $value ) {
		$_default_attributes['attribute_' . $key] = $value;
	}

  return wcpt_find_closests_matching_product_variation($product, $_default_attributes);
}

// check if current variation is incomplete
function wcpt_is_incomplete_variation($product, $variation) {

  foreach( $product->get_variation_attributes() as $attribute => $terms ){
    if( substr( $attribute, 0, 3 ) !== 'pa_'  ){ // custom attribute
      $attribute = sanitize_title( $attribute );
    }

    if( empty( $variation['attributes']['attribute_' . $attribute] ) ){
      return true;
    }
  }

  return false;
}

/* clear product transients */
add_action( 'before_delete_post', 'wcpt_clear_product_transients' );
add_action( 'save_post', 'wcpt_clear_product_transients' );
function wcpt_clear_product_transients($post_id){
  if( get_post_type( $post_id ) == 'product' ){
    delete_transient( 'wcpt_variations_' . $post_id );
  }
}

// duplicate post
add_filter( 'post_row_actions', 'wcpt_duplicate_post_link', 10, 2 );
function wcpt_duplicate_post_link( $actions, $post ) {
	if (
    current_user_can('edit_posts') &&
    $post->post_type=='wc_product_table'
  ){
    if( defined('WCPT_PRO') ){
		  $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=wcpt_duplicate_post_as_draft&post=' . $post->ID, WCPT_PLUGIN_PATH, 'duplicate_nonce' ) . '" title="Duplicate this table" rel="permalink">Duplicate</a>';
    }else{
		  $actions['duplicate'] = '<span style="color: #999">Duplicate (PRO)</span>';
    }

	}
	return $actions;
}

// gets the required filter from nav -- recursive
function wcpt_check_if_nav_has_filter ( $arr= null , $type, $second ){
  if( null === $arr ){
    $arr = wcpt_get_table_data();
  }
  foreach( $arr as $key => &$val ){
    if( 
      $key === 'type' &&
      $val === $type &&
      (
        $type === 'taxonomy_filter' && $second === $arr['taxonomy'] ||
        $type === 'attribute_filter' && $second === $arr['attribute_name']
      )
    ){
      return true;      
    }else if( 
      gettype($val) == 'array' &&
      TRUE === wcpt_check_if_nav_has_filter( $val, $type, $second )
    ){
      return true;
    }
  }
}

add_filter('wcpt_settings', 'wcpt_settings__search', 2, 10);

function wcpt_settings__search( $data, $ctx ){
  if( $ctx == 'view' ){
    return $data;
  }

  if( empty( $data['search'] ) ){
    $data['search'] = array(

      'stopwords' => implode(", ", array( "i", "me", "my", "myself", "we", "our", "ours", "ourselves", "you", "your", "yours", "yourself", "yourselves", "he", "him", "his", "himself", "she", "her", "hers", "herself", "it", "its", "itself", "they", "them", "their", "theirs", "themselves", "what", "which", "who", "whom", "this", "that", "these", "those", "am", "is", "are", "was", "were", "be", "been", "being", "have", "has", "had", "having", "do", "does", "did", "doing", "a", "an", "the", "and", "but", "if", "or", "because", "as", "until", "while", "of", "at", "by", "for", "with", "about", "against", "between", "into", "through", "during", "before", "after", "above", "below", "to", "from", "up", "down", "in", "out", "on", "off", "over", "under", "again", "further", "then", "once", "here", "there", "when", "where", "why", "how", "all", "any", "both", "each", "few", "more", "most", "other", "some", "such", "no", "nor", "not", "only", "own", "same", "so", "than", "too", "very", "s", "t", "can", "will", "just", "don", "should", "now" )),
      'replacements' => '',

      'title' => array(
        'enabled' => true,
        'rules' => array(
          'phrase_exact_enabled' => true,
          'phrase_exact_score' => 100,

          'phrase_like_enabled' => true,
          'phrase_like_score' => 60,

          'keyword_exact_enabled' => true,
          'keyword_exact_score' => 40,

          'keyword_like_enabled' => true,
          'keyword_like_score' => 20,
        )
      ),
      'sku' => array(
        'enabled' => true,
        'rules' => array(
          'phrase_exact_enabled' => true,
          'phrase_exact_score' => 100,

          'phrase_like_enabled' => true,
          'phrase_like_score' => 60,

          'keyword_exact_enabled' => true,
          'keyword_exact_score' => 40,

          'keyword_like_enabled' => true,
          'keyword_like_score' => 20,
        )
      ),
      'category' => array(
        'enabled' => true,
        'rules' => array(
          'phrase_exact_enabled' => true,
          'phrase_exact_score' => 100,

          'phrase_like_enabled' => true,
          'phrase_like_score' => 60,

          'keyword_exact_enabled' => true,
          'keyword_exact_score' => 40,

          'keyword_like_enabled' => true,
          'keyword_like_score' => 20,
        )
      ),
      'attribute' => array(
        'enabled' => true,
        'rules' => array(
          'phrase_exact_enabled' => true,
          'phrase_exact_score' => 100,

          'phrase_like_enabled' => true,
          'phrase_like_score' => 60,

          'keyword_exact_enabled' => true,
          'keyword_exact_score' => 40,

          'keyword_like_enabled' => true,
          'keyword_like_score' => 20,
        ),
        'items' => array()
      ),
      'tag' => array(
        'enabled' => true,
        'rules' => array(
          'phrase_exact_enabled' => true,
          'phrase_exact_score' => 100,

          'phrase_like_enabled' => true,
          'phrase_like_score' => 60,

          'keyword_exact_enabled' => true,
          'keyword_exact_score' => 40,

          'keyword_like_enabled' => true,
          'keyword_like_score' => 20,
        )
      ),
      'content' => array(
        'enabled' => true,
        'rules' => array(
          'phrase_exact_enabled' => true,
          'phrase_exact_score' => 100,

          'phrase_like_enabled' => true,
          'phrase_like_score' => 60,

          'keyword_exact_enabled' => true,
          'keyword_exact_score' => 40,

          'keyword_like_enabled' => true,
          'keyword_like_score' => 20,
        )
      ),
      'excerpt' => array(
        'enabled' => true,
        'rules' => array(
          'phrase_exact_enabled' => true,
          'phrase_exact_score' => 100,

          'phrase_like_enabled' => true,
          'phrase_like_score' => 60,

          'keyword_exact_enabled' => true,
          'keyword_exact_score' => 40,

          'keyword_like_enabled' => true,
          'keyword_like_score' => 20,
        )
      ),
      'custom_field' => array(
        'enabled' => true,
        'rules' => array(
          'phrase_exact_enabled' => true,
          'phrase_exact_score' => 100,

          'phrase_like_enabled' => true,
          'phrase_like_score' => 60,

          'keyword_exact_enabled' => true,
          'keyword_exact_score' => 40,

          'keyword_like_enabled' => true,
          'keyword_like_score' => 20,
        ),
        'items' => array()
      ),        
    );

  }

  // attribute integrity
  $attributes = array();
  foreach( wc_get_attribute_taxonomies() as $attribute ){
    $match = false;
    foreach( $data['search']['attribute']['items'] as $item ){
      if( $item['item'] === $attribute->attribute_name ){
        $attributes[] = $item;
        $match = true;
        break;
      }
    }

    if( ! $match ){
      $attributes[] = array(
        'item' => $attribute->attribute_name,
        'label' => $attribute->attribute_label,
        'enabled' => true,
        'custom_rules_enabled' => false,
        'rules' => array(
          'phrase_exact_enabled' => true,
          'phrase_exact_score' => 100,

          'phrase_like_enabled' => true,
          'phrase_like_score' => 60,

          'keyword_exact_enabled' => true,
          'keyword_exact_score' => 40,

          'keyword_like_enabled' => true,
          'keyword_like_score' => 20,
        )
      );
    }
  }

  $data['search']['attribute']['items'] = $attributes;

  // custom field integrity
  $custom_fields = array();
  foreach( wcpt_get_product_custom_fields() as $meta_name ){
    $match = false;

    // get previous settings
    foreach( $data['search']['custom_field']['items'] as $item ){
      if( $item['item'] == $meta_name ){
        $custom_fields[] = $item;
        $match = true;
        break;
      }
    }

    // generate fresh settings
    if( ! $match ){
      $custom_fields[] = array(
        'item' => $meta_name,
        'label' => $meta_name,
        'enabled' => true,
        'custom_rules_enabled' => false,
        'rules' => array(
          'phrase_exact_enabled' => true,
          'phrase_exact_score' => 80,

          'phrase_like_enabled' => true,
          'phrase_like_score' => 60,

          'keyword_exact_enabled' => true,
          'keyword_exact_score' => 40,

          'keyword_like_enabled' => true,
          'keyword_like_score' => 20,
        )
      );
    }
  }

  $data['search']['custom_field']['items'] = $custom_fields;

  return $data;
}


function wcpt_get_product_custom_fields(){
  global $wpdb;
  $query = "SELECT DISTINCT meta_key FROM $wpdb->postmeta meta LEFT JOIN $wpdb->posts posts ON meta.post_id = posts.ID WHERE posts.post_type='product'";
  $custom_fields = array();
  foreach( $wpdb->get_col($query) as $meta_name ){
    if(
      '_' == substr( $meta_name, 0, 1 ) ||
      'total_sales' == $meta_name
    ){
      continue;
    }else{
      $custom_fields[] = $meta_name;
    }
  }
  return $custom_fields;
}