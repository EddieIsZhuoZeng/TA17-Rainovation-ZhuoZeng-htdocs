<?php
/*
Plugin name: WP Clone by WP Academy
Plugin URI: http://wpacademy.com/software/
Description: Move or copy a WordPress site to another server or to another domain name, move to/from local server hosting, and backup sites.
Author: WP Academy
Version: 2.2.10
Author URI: http://wpacademy.com/
*/
require_once 'analyst/main.php';

analyst_init(array(
	'client-id' => '9zdex5mar85kmgya',
	'client-secret' => 'd5702a59d32c01c211316717493096485d5156e8',
	'base-dir' => __FILE__
));


/**
 *
 * @URI https://wordpress.org/plugins/wp-clone-by-wp-academy
 *
 * @developed by Shaharia Azam <mail@shaharia.com>
 */

include_once 'lib/functions.php';
include_once 'lib/class.wpc-wpdb.php';

$upload_dir = wp_upload_dir();

define('WPBACKUP_FILE_PERMISSION', 0755);
define('WPCLONE_ROOT',  rtrim(str_replace("\\", "/", ABSPATH), "/\\") . '/');
define('WPCLONE_BACKUP_FOLDER',  'wp-clone');
define('WPCLONE_DIR_UPLOADS',  str_replace('\\', '/', $upload_dir['basedir']));
define('WPCLONE_DIR_PLUGIN', str_replace('\\', '/', plugin_dir_path(__FILE__)));
define('WPCLONE_URL_PLUGIN', plugin_dir_url(__FILE__));
define('WPCLONE_DIR_BACKUP',  WPCLONE_DIR_UPLOADS . '/' . WPCLONE_BACKUP_FOLDER . '/');
define('WPCLONE_INSTALLER_PATH', WPCLONE_DIR_PLUGIN);
define('WPCLONE_WP_CONTENT' , str_replace('\\', '/', WP_CONTENT_DIR));
define('WPCLONE_ROOT_FILE_PATH' , __FILE__);


/* Init options & tables during activation & deregister init option */

register_activation_hook((__FILE__), 'wpa_wpclone_activate');
register_deactivation_hook(__FILE__ , 'wpa_wpclone_deactivate');
register_uninstall_hook(__FILE__ , 'wpa_wpclone_uninstall');
add_action('admin_menu', 'wpclone_plugin_menu');
add_action( 'wp_ajax_wpclone-ajax-size', 'wpa_wpc_ajax_size' );
add_action( 'wp_ajax_wpclone-ajax-dir', 'wpa_wpc_ajax_dir' );
add_action( 'wp_ajax_wpclone-ajax-delete', 'wpa_wpc_ajax_delete' );
add_action( 'wp_ajax_wpclone-ajax-uninstall', 'wpa_wpc_ajax_uninstall' );
add_action( 'wp_ajax_wpclone-search-n-replace', 'wpa_wpc_ajax_search_n_replace' );
add_action( 'wp_ajax_wpclone-ajax-banner1-close', 'wpclone_ajax_banner1_close' );
add_action( 'wp_ajax_wpclone-ajax-banner1-removed', 'wpclone_ajax_banner1_removed' );
add_action( 'wp_ajax_wpclone-ajax-banner1-getstatus', 'wpclone_ajax_banner1_getstatus' );
add_action('admin_init', 'wpa_wpc_plugin_redirect');
add_action('admin_head', 'wpclone_admin_head_scripts');
add_action('admin_footer', 'wpclone_admin_footer_scripts');
add_action( 'admin_notices', 'wpclone_admin_notice__success' );

function wpclone_plugin_menu() {
    add_menu_page (
        'WP Clone Plugin Options',
        'WP Clone',
        'manage_options',
        'wp-clone',
        'wpclone_plugin_options'
    );
}

function wpa_wpc_ajax_size() {

    check_ajax_referer( 'wpclone-ajax-submit', 'nonce' );

    $cached = get_option( 'wpclone_directory_scan' );
    $interval = 600; /* 10 minutes */

    if( false !== $cached && time() - $cached['time'] < $interval ) {
        $size = $cached;
        $size['time'] = date( 'i', time() - $size['time'] );
    } else {
        $size = wpa_wpc_dir_size( WP_CONTENT_DIR );
    }

    echo json_encode( $size );
    wp_die();

}

function wpa_wpc_ajax_dir() {

    check_ajax_referer( 'wpclone-ajax-submit', 'nonce' );
    if( ! file_exists( WPCLONE_DIR_BACKUP ) ) wpa_create_directory();
    wpa_wpc_scan_dir();
    wp_die();

}

function wpa_wpc_ajax_delete() {

    check_ajax_referer( 'wpclone-ajax-submit', 'nonce' );

    if( isset( $_REQUEST['fileid'] ) && ! empty( $_REQUEST['fileid'] ) ) {

        echo json_encode( DeleteWPBackupZip( $_REQUEST['fileid'] ) );


    }

    wp_die();

}

function wpa_wpc_ajax_uninstall() {

    check_ajax_referer( 'wpclone-ajax-submit', 'nonce' );
    if( file_exists( WPCLONE_DIR_BACKUP ) ) {
        wpa_delete_dir( WPCLONE_DIR_BACKUP );

    }

    if( file_exists( WPCLONE_WP_CONTENT . 'wpclone-temp' ) ) {
        wpa_delete_dir( WPCLONE_WP_CONTENT . 'wpclone-temp' );

    }

    delete_option( 'wpclone_backups' );
    wpa_wpc_remove_table();
    wp_die();

}

function wpa_wpc_ajax_search_n_replace() {
    check_ajax_referer( 'wpclone-ajax-submit', 'nonce' );
    global $wpdb;
    $search  = isset( $_POST['search'] ) ? $_POST['search'] : '';
    $replace = isset( $_POST['replace'] ) ? $_POST['replace'] : '';

    if( empty( $search ) || empty( $replace ) ) {
        echo '<p class="error">Search and Replace values cannot be empty.</p>';
        wp_die();
    }

    wpa_bump_limits();
    $report = wpa_safe_replace_wrapper( $search, $replace, $wpdb->prefix );
    echo wpa_wpc_search_n_replace_report( $report );

    wp_die();
}

function wpclone_plugin_options() {
    include_once 'lib/view.php';
}

function wpa_enqueue_scripts(){
    wp_register_script('clipboard', plugin_dir_url(__FILE__) . '/lib/js/clipboard.min.js', array('jquery'));
    wp_register_script('wpclone', plugin_dir_url(__FILE__) . '/lib/js/backupmanager.js', array('jquery'));
    wp_register_style('wpclone', plugin_dir_url(__FILE__) . '/lib/css/style.css');
    wp_localize_script('wpclone', 'wpclone', array( 'nonce' => wp_create_nonce( 'wpclone-ajax-submit' ), 'spinner' => esc_url( admin_url( 'images/spinner.gif' ) ) ) );
    wp_enqueue_script('clipboard');
    wp_enqueue_script('wpclone');
    wp_enqueue_style('wpclone');
    wp_deregister_script('heartbeat');
    add_thickbox();
}
if( isset($_GET['page']) && 'wp-clone' == $_GET['page'] ) add_action('admin_enqueue_scripts', 'wpa_enqueue_scripts');

function wpa_wpclone_activate() {

	//Control after activating redirect to settings page
	add_option('wpa_wpc_plugin_do_activation_redirect', true);

	wpa_create_directory();
}

function wpa_wpclone_deactivate() {

	//Control after activating redirect to settings page
	delete_option("wpa_activation_redirect_required");

    if( file_exists( WPCLONE_DIR_BACKUP ) ) {
        $data = "<Files>\r\n\tOrder allow,deny\r\n\tDeny from all\r\n\tSatisfy all\r\n</Files>";
        $file = WPCLONE_DIR_BACKUP . '.htaccess';
        file_put_contents($file, $data);
    }

}

function wpa_wpclone_uninstall() {
	//Control after activating redirect to settings page
	delete_option("wpa_activation_redirect_required");
    delete_option("wpclone_ajax_banner1_close");
    delete_option("wpclone_ajax_banner1_removed");
}

function wpa_wpc_remove_table() {
    global $wpdb;
    $wp_backup = $wpdb->prefix . 'wpclone';
    $wpdb->query ("DROP TABLE IF EXISTS $wp_backup");
}

function wpa_create_directory() {
    $indexFile = (WPCLONE_DIR_BACKUP.'index.html');
    $htacc = WPCLONE_DIR_BACKUP . '.htaccess';
    $htacc_data = "Options All -Indexes";
    if (!file_exists($indexFile)) {
        if(!file_exists(WPCLONE_DIR_BACKUP)) {
            if(!mkdir(WPCLONE_DIR_BACKUP, WPBACKUP_FILE_PERMISSION)) {
                die("Unable to create directory '" . rtrim(WPCLONE_DIR_BACKUP, "/\\"). "'. Please set 0755 permission to wp-content.");
            }
        }
        $handle = fopen($indexFile, "w");
        fclose($handle);
    }
    if( file_exists( $htacc ) ) {
        @unlink ( $htacc );
    }
    file_put_contents($htacc, $htacc_data);
}

function wpa_wpc_import_db(){

    global $wpdb;
    $table_name = $wpdb->prefix . 'wpclone';

    if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'") === $table_name ) {

        $old_backups = array();
        $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpclone ORDER BY id DESC", ARRAY_A);

        foreach( $result as $row ) {

            $time = strtotime( $row['data_time'] );
            $old_backups[$time] = array(
                    'name' => $row['backup_name'],
                    'creator' => $row['creator'],
                    'size' => $row['backup_size']

            );

        }

        if( false !== get_option( 'wpclone_backups' ) ) {
            $old_backups = get_option( 'wpclone_backups' ) + $old_backups;
        }

        update_option( 'wpclone_backups', $old_backups );

        wpa_wpc_remove_table();

    }


}

function wpa_wpc_msnotice() {
    echo '<div class="error">';
    echo '<h4>WP Clone Notice.</h4>';
    echo '<p>WP Clone is not compatible with multisite installations.</p></div>';
}

if ( is_multisite() )
    add_action( 'admin_notices', 'wpa_wpc_msnotice');

function wpa_wpc_phpnotice() {
    echo '<div class="error">';
    echo '<h4>WP Clone Notice.</h4>';
    printf( '<p>WP Clone is not compatible with PHP %s, please upgrade to PHP 5.3 or newer.</p></div>', phpversion() );
}

if( version_compare( phpversion(), '5.3', '<' ) ){
    add_action( 'admin_notices', 'wpa_wpc_phpnotice');    
}

function wpa_wpc_plugin_redirect()

{

	//Control after activating redirect to settings page
	if (get_option('wpa_wpc_plugin_do_activation_redirect', false)) {

		delete_option('wpa_wpc_plugin_do_activation_redirect');

		wp_redirect(admin_url('admin.php?page=wp-clone'));
	}
}


//Banner functionality
function wpclone_ajax_banner1_close(){
    update_option("wpclone_ajax_banner1_close", true);
    update_option("wpclone_ajax_banner1_removed", false);
    wp_send_json_success(["success" => true]);
    exit();
}

function wpclone_ajax_banner1_removed(){
    update_option("wpclone_ajax_banner1_close", true);
    update_option("wpclone_ajax_banner1_removed", true);
    wp_send_json_success(["success" => true]);
    exit();
}

function wpclone_ajax_banner1_getstatus(){
    wp_send_json_success([
        'wpclone_ajax_banner1_close' => get_option("wpclone_ajax_banner1_close", "0"),
        'wpclone_ajax_banner1_removed' => get_option("wpclone_ajax_banner1_removed", "0")
    ]);
    exit();
}

function wpclone_admin_head_scripts(){
    echo '<style rel="stylesheet">
/** Banner CSS **/
.banner-1{
    min-height: 744px;
    width: auto;
    background-size: cover;
    padding-top: 48px;
    padding-left: 61px;
    padding-right: 85px;
    font-family: \'Montserrat\', sans-serif;
    margin-right: 30px;
    margin-top: 20px;
}

.banner-1 .heading{
    color: #0f9087;
    font-size: 26px;
}

.banner-1 .nutshell-list{
    color: #3A3A3A;
    font-size: 18px;
    line-height: 22px;
}

.banner-1 .nutshell-list li{
    list-style-position: inside;
    text-indent: -1em;
    padding-left: 20px;
}


.banner-1 .banner-footer {
    margin-top: 25px;
    font-size: 18px;
    line-height: 29px;
}

.button1 span.sc-button {
    -webkit-font-smoothing: antialiased;
    background-color: #0f9087;
    border: none;
    color: #fff;
    display: inline-block;
    text-decoration: none;
    user-select: none;
    letter-spacing: 1px;
    padding-left: 25px;
    padding-right: 25px;
    padding-top: 12px;
    padding-bottom: 12px;
    transition: all 0.1s ease-out;
    border-radius: 15px;
}

.banner-1 .button1{
    

}
.banner-1 .button2{
    -webkit-font-smoothing: antialiased;
    background-color: #0f9087;
    border: none;
    color: #fff;
    display: inline-block;
    text-decoration: none;
    user-select: none;
    letter-spacing: 1px;
    padding: 12px 35px;
    text-transform: uppercase;
    transition: all 0.1s ease-out;
    border-radius: 10px;
}

.banner-1 .close-icon {
    float: right;
    margin-top: -30px;
    margin-right: -65px;
    cursor: pointer;
}

.plugin-large-notice .banner-1-collapsed{
    min-height: 63px;
    width: auto;
    /*padding-top: 48px;
    padding-left: 61px;
    padding-right: 85px;*/
    font-family: \'Montserrat\', sans-serif;
    margin-right: 30px;
    margin-top: 20px;
}

.plugin-large-notice .banner-1-collapsed p.left-text {
    font-size: 20px;
    line-height: 25px;
    color: #0f9087;
    font-family: \'Montserrat\', sans-serif;
    padding-left: 15px;
    float: left;
}

.plugin-large-notice .banner-1-collapsed p.left-text a {
    font-size: 15px;
    color: #0f9087;
    text-decoration: underline;
}

.plugin-large-notice .banner-1-collapsed p.remove-for-good {
    float: right;
    font-size: 16px;
    color: #0f9087;
    margin-right: 30px;
    line-height: 35px;
    cursor: pointer;
}
.nutshell-list a {
    color: #0f9087;
    text-decoration: underline;
}
</style>';
}

function wpclone_admin_footer_scripts(){
    echo '<script>
jQuery(function($) {
//Banner notice
$("document").ready(function (e) {
        $.ajax({
            url: ajaxurl,
            type: \'get\',
            data: {
                \'action\': \'wpclone-ajax-banner1-getstatus\'
            },
            success: function(data){
                var urlParams = new URLSearchParams(window.location.search);
                var currentPage = urlParams.get("page");
                
                if(data.data.wpclone_ajax_banner1_close === "1" && data.data.wpclone_ajax_banner1_removed === "1"){
                    $(".banner-1-collapsed").hide().remove();
                    $(".banner-1").hide().remove();
                }else if(data.data.wpclone_ajax_banner1_close === "1" && data.data.wpclone_ajax_banner1_removed != "1"){
                    if(currentPage === "wp-clone"){
                        $(".banner-1-collapsed").show();
                        $(".banner-1").hide();
                    }else{
                        $(".banner-1-collapsed").show();
                        $(".banner-1").hide();
                    }
                }else if(data.data.wpclone_ajax_banner1_close === "0" && data.data.wpclone_ajax_banner1_removed === "0"){
                    if(currentPage === "wp-clone"){
                        $(".banner-1-collapsed").hide();
                        $(".banner-1").show();
                    }else{
                        $(".banner-1-collapsed").show();
                        $(".banner-1").hide();
                    }
                }
            },
            error: function(e){
            }
        });
    });
    $("a#show-large-banner-1").on("click", function(){
        $(".banner-1-collapsed").hide();
        $(".banner-1").show(100);
    });
    $("#please-first-read-it").on("click", function(){
        $(".banner-1-collapsed").hide();
        $(".banner-1").show(100);
    });
    $(".banner-1 .close-icon").on("click", function (e) {
        $(".banner-1-collapsed").show(100);
        $(".banner-1").hide(100);

        $.ajax({
            url: ajaxurl,
            type: \'get\',
            data: {
                \'action\': \'wpclone-ajax-banner1-close\'
            },
            success: function(data){
                console.log(data);
            },
            error: function(e){
            }
        });
    })

    $(".banner-1-collapsed #remove-for-good-text").on("click", function (e) {
        $(".banner-1-collapsed").hide();

        $.ajax({
            url: ajaxurl,
            type: \'get\',
            data: {
                \'action\': \'wpclone-ajax-banner1-removed\'
            },
            success: function(data){
                console.log(data);
            },
            error: function(e){
            }
        });
    })
    });
</script>';
}

function wpclone_admin_notice__success() {
    ?>
    <script type="text/javascript" src="https://sellcodes.com/quick_purchase/q1OGuSox/embed.js" async="async"></script>
    <div  style="clear: both; margin-top: 2px;"></div>
    <div class="plugin-large-notice">
        <div class="banner-1-collapsed" style="display:none; background-image: url('<?php echo plugins_url( 'lib/img/banner_bg_fold_2.jpg', __FILE__ )?>')">
            <p class="left-text"><strong>BIG NEWS:</strong> We want WP Clone to arise from the dead. <a href="#" id="show-large-banner-1">Read more</a></p>
            <p class="remove-for-good"><span id="remove-for-good-text" style="text-decoration: underline">Remove for good</span> <span style="font-size: 14px; cursor: pointer;">(please first <span id="please-first-read-it" style="text-decoration: underline">read it</span>!)</span></p>
        </div>
        <div class="banner-1" style="display:none;background-image: url('<?php echo plugins_url( 'lib/img/banner_bg.jpg', __FILE__ )?>')">
            <div class="close-icon"><img src='<?php echo plugins_url( 'lib/img/banner_close_icon.png', __FILE__ )?>'> </div>
            <div class="heading">BIG NEWS: <strong>We want WP Clone to arise from the dead.</strong> Please help us!</div>
            <div style="margin-top: 27px; font-size: 20px; color: #3a3a3a">The key points in a nutshell:</div>
            <div class="nutshell-list">
                <ul>
                    <li>1.	New contributors have been added to the plugin, and with it comes new motivation to make it a kick-ass product!</li>
                    <li>2. 	Some fixes have been applied, the plugin now works in 90% of cases (and a further 9% if you follow the process as
                        outlined on the <a href="https://wordpress.org/plugins/wp-clone-by-wp-academy/" target="_blank">plugin page</a>)</li>
                    <li>
                        3.	We want to revive the plugin, make it work in 100% of cases, and add many more features. As we’re short on cash,
                        we’re crowdfunding it, and need your help:
                        <ul style="margin-left: 30px;margin-top: 15px;">
                            <li>
                                a.	<span class="sellcodes-quick-purchase" style="float: none;"><span style="text-decoration: underline; font-family: 'Montserrat', sans-serif;" class="sc-button" data-product-id="q1OGuSox" data-option-id="FgUPGiaV">Contribution of 5 or 10 USD:</span></span> You get the warm fuzzy feeling from giving a sincere “Thank you” for a plugin which <br>
                                probably made your life easier in the past, and helping to further develop it. Plus: a free backlink to your site!
                            </li>
                            <li>
                                b.	<span class="sellcodes-quick-purchase" style="float: none;"><span style="text-decoration: underline; font-family: 'Montserrat', sans-serif;" class="sc-button" data-product-id="q1OGuSox" data-option-id="HtNSwPAK">Contribution of 15 USD:</span></span> As in a), plus you will be rewarded with a <strong>free plugin license</strong> <br>
                                (for the premium product which we will create). A contribution of 30 USD gets you 2 licenses.
                            </li>
                            <li>
                                c.	<span class="sellcodes-quick-purchase" style="float: none;"><span style="text-decoration: underline; font-family: 'Montserrat', sans-serif;" class="sc-button" data-product-id="q1OGuSox" data-option-id="3DV66HIl">Contribution of 50 USD:</span></span>  As in a), plus an <strong>unlimited websites premium license.</strong> <br>
                                This a fantastic, one-time deal. The plugin will provide many more features <br>
                                - such as backup scheduling, backup to external servers etc. - while still <br>being super-easy to use! It will be the best on the market – <strong style="text-decoration: underline">guaranteed</strong>.
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="banner-footer">
                <span>All licenses are <strong>lifetime licenses</strong> and valid on both commercial and non-commercial <br>websites. The crowdfunding target is USD 3,000. If we don’t reach it you’ll be refunded*.</span> <br> <br>
                Thank you for your support - we <span style="text-decoration: underline;">really</span> depend on it!
            </div>
            <div style="margin-top: 33px;">
                <a href="#" class="button1"><span class="sellcodes-quick-purchase" style="float: none;"><span style="letter-spacing: 1.2px; color: #ffffff; text-decoration: none; font-family: 'Montserrat', sans-serif;" class="sc-button" data-product-id="q1OGuSox" data-option-id="FgUPGiaV">Contribute</span></span></a>
                <a href="#" class="button1"><span class="sellcodes-quick-purchase" style="float: none;"><span style="letter-spacing: 1.2px; color: #ffffff; text-decoration: none; font-family: 'Montserrat', sans-serif;" class="sc-button" data-product-id="q1OGuSox" data-option-id="3DV66HIl">Contribute & get free license(s)</span></span></a>
            </div>
            <p style="margin-top: 33px;">
                Also check out the <a href="https://wordpress.org/plugins/wp-clone-by-wp-academy/" target="_blank" style="color: #0f9087">updated plugin description.</a> To follow our funding progress please go <a href="https://sellcodes.com/q1OGuSox" target="_blank" style="color: #0f9087">here</a>.
            </p>
            <p style="margin-top: 33px; color: #0f9087">
                *With the exception of the 5 or 10 USD amounts. We want you to have that warm fuzzy feeling forever ;)
            </p>
        </div>
    </div>
    <?php
}