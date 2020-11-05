<?php
/**
 * Code for Admin Options
 *
 * @since      2.4
 * @package    Secure File Manager
 * @author     Themexa
 */

add_action( 'admin_menu', 'sfm_setup_option_pages' );
function sfm_setup_option_pages() {
    $page_title = __( 'Secure File Manager', 'secure-file-manager' );
    $menu_title = __( 'Secure File Manager', 'secure-file-manager' );
    $capability = 'read';
    $menu_slug = 'sfm_file_manager';
    $function = 'sfm_file_manager_display';
    $icon_url = '';
    $position = 24;

    global $sfm_file_manager;
    $sfm_file_manager = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );

    global $sfm_settings;
    $sfm_settings = add_submenu_page(
        $menu_slug,
        __( 'Settings', 'secure-file-manager' ),
        __( 'Settings', 'secure-file-manager' ),
        'administrator',
        'sfm_settings',
        'sfm_settings_display'
    );
}

function sfm_file_manager_display() {

    if ( ! get_option( 'sfm_auth_user' ) ) {
        update_option( 'sfm_auth_user', (array)'' );
    }
    $currentUser = get_current_user_id();
    $currentUserRole = wp_get_current_user();
    $roles = ( array ) $currentUserRole->roles;

    if ( ! (current_user_can('update_core') || in_array( $currentUser, get_option( 'sfm_auth_user' ) ) || array_intersect( get_option( 'sfm_auth_roles' ), $roles ) ) ) {
        wp_die( '<h1>Unauthorized Access. Please contact Site Administrator.</h1>' );
    }

?>
    <div class="tx_wrap sfm_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="jumbotron" style="background-image: url( <?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>assets/images/tinypixi_pluginman_head.png ); ">
                        <h1 class="display-3"><?php _e( 'Secure File Manager', 'secure-file-manager' ); ?></h1>
                        <p class="lead"><?php _e( 'WordPress file editing made easy (and secure)', 'secure-file-manager' ); ?></p>
                    </div>
                </div>
            </div>
            <div class="sfm-wrapper container" style="padding: 20px 0;">
                <div id="elfinder"></div>
            </div>
        </div>
    </div>

<?php }

function sfm_settings_display() {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( '<h1>Unauthorized Access. Please contact Site Administrator.</h1>' );
    }

?>

    <div class="tx_wrap sfm_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="jumbotron" style="background-image: url( <?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>assets/images/tinypixi_pluginman_head.png ); ">
                        <h1 class="display-3"><?php _e( 'Secure File Manager', 'secure-file-manager' ); ?></h1>
                        <p class="lead"><?php _e( 'Settings', 'bwpse' ); ?></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <form method="post">
                        <h1><?php _e( 'Limit with User ID(s)', 'secure-file-manager' ); ?></h1>
                        <fieldset id="sfm_auth_user">
                            <div class="form-group">
                                <?php
                                    if ( isset( $_POST[ 'sfm_auth_user' ] ) ) {
                                        update_option( 'sfm_auth_user', preg_replace( array( '/[^\d,]/', '/(?<=,),+/', '/^,+/', '/,+$/' ), '', explode( ',', $_POST[ 'sfm_auth_user' ] ) ) );
                                    }
                                ?>
                                <strong><label><?php _e( 'Which user should have access to the File Manager?', 'secure-file-manager' ); ?></label></strong>
                                <input type="text" name="sfm_auth_user" id="sfm_auth_user" placeholder="e.g. 1, 2, 3" class="form-control" value="<?php echo implode(', ', (array) get_option( 'sfm_auth_user' )); ?>">
                                <small class="form-text text-muted"><?php _e( 'Enter specific user ID. Enter comma ( , ) between IDs if there are more than one.', 'secure-file-manager' ); ?></small>
                            </div>
                        </fieldset>
                        <fieldset>
                            <input type="hidden" name="action" value="update" />
                            <?php wp_nonce_field('sfm_update_action'); ?>
                            <button type="submit" class="btn btn-primary"><?php _e( 'Save Changes', 'secure-file-manager' ); ?></button>
                        </fieldset>
                    </form>
                </div>
                <div class="col-md-5 offset-md-1">
                    <form method="post">
                        <fieldset id="sfm_auth_roles">
                            <?php
                                if( isset( $_POST[ 'sfm_auth_roles' ] ) ) {
                                    update_option ( 'sfm_auth_roles', $_POST[ 'sfm_auth_roles' ]);
                                }
                            ?>
                            <h1><?php _e( 'Specify User Role(s)', 'secure-file-manager' ); ?></h1>
                            <strong><label><?php _e( 'Which user role(s) should have access to the File Manager?', 'secure-file-manager' ); ?></label></strong>
                            <p></p>
                            <div class="row">
                                <?php foreach ( get_editable_roles() as $role_name => $role_info ): ?>
                                    <div class="form-group col-md-6">
                                        <input class="form-check-input" type="checkbox" value="<?php echo $role_name; ?>" id="<?php echo $role_name; ?>" name="sfm_auth_roles[]" <?php echo in_array( $role_name, get_option( 'sfm_auth_roles' ) ) ? 'checked' : ''; ?> >
                                        <label class="form-check-label" for="<?php echo $role_name; ?>"><span class="checkbox_role"><?php echo $role_info['name']; ?></span></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>
                        <fieldset>
                            <input type="hidden" name="action" value="update" />
                            <?php wp_nonce_field( 'sfm_update_action' ); ?>
                            <button type="submit" class="btn btn-primary"><?php _e( 'Save Changes', 'secure-file-manager' ); ?></button>
                        </fieldset>
                    </form>
                </div>
            </div>
            <div>

            </div>
        </div>
    </div>
<?php }
