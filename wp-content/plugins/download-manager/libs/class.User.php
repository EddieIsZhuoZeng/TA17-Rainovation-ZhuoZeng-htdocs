<?php
/**
 * Created by PhpStorm.
 * User: shahnuralam
 * Date: 6/24/18
 * Time: 10:39 PM
 */

namespace WPDM\libs;


use WPDM\Form;

if( ! class_exists( 'User' ) ):

    class User{

        public static $instance;

        public static function instance(){
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        function __construct()
        {

            //add_action('wp_login', array($this, 'onUserLogin'), 10, 2);

        }

        static function signupForm($params = []){
            $reg_data_fields['__phash'] =[ 'type'  => 'hidden', 'attrs' => ['name' => '__phash', 'id' => '__phash', 'value' => Crypt::encrypt($params)] ];
            $reg_data_fields['__reg_nonce'] =[ 'type'  => 'hidden', 'attrs' => ['name' => '__reg_nonce', 'id' => '__reg_nonce', 'value' => ''] ];
            $reg_data_fields['loginurl'] =[ 'type'  => 'hidden', 'attrs' => ['name' => 'loginurl', 'id' => 'loginurl', 'value' => $params['loginurl']] ];
            $reg_data_fields['permalink'] =[ 'type'  => 'hidden', 'attrs' => ['name' => 'permalink', 'id' => 'permalink', 'value' => $params['permalink']] ];
            $reg_data_fields['reg_redirect'] =[ 'type'  => 'hidden', 'attrs' => ['name' => 'reg_redirect', 'id' => 'reg_redirect', 'value' => $params['reg_redirect']] ];
            $reg_data_fields['name'] = [
                'cols' => [
                    [ 'label' => __( "First name", "download-manager" ), 'type' => 'text', 'grid_class' => 'col-md-6 col-sm-12', 'attrs' => [ 'name' => 'wpdm_reg[first_name]', 'id' => 'first_name', 'placeholder' => __('Your First Name', 'download-manager') , 'required' => 'required'] ],
                    [ 'label' => __( "Last name", "download-manager" ), 'type' => 'text', 'grid_class' => 'col-md-6 col-sm-12', 'attrs' => [ 'name' => 'wpdm_reg[last_name]', 'id' => 'last_name', 'placeholder' => __('Your Last Name', 'download-manager') , 'required' => 'required'] ]
                ],
                'note' => 'Your full name'
            ];
            $reg_data_fields['username'] = array(
                'label' => __( "Username", "download-manager" ),
                'type'  => 'text',
                'attrs' => array( 'name' => 'wpdm_reg[user_login]', 'id' => 'user_login' , 'placeholder' => __('User Login ID', 'download-manager') , 'required' => 'required'),
                'note'  => 'Enter a valid email address'
            );
            $reg_data_fields['email'] = [
                'label' => __( "Email", "download-manager" ),
                'type'  => 'email',
                'attrs' => [ 'name' => 'wpdm_reg[user_email]', 'id' => 'user_email' , 'placeholder' => __( "Your Email Address", "download-manager" ) , 'required' => 'required' ],
                'note'  => 'Enter a valid email address'
            ];

            if ((int)get_option('__wpdm_signup_email_verify', 0) === 0) {
                $reg_data_fields['password'] = array(
                    'cols' => [
                        ['label' => __("Password", "download-manager"), 'type' => 'password', 'grid_class' => 'col-md-6 col-sm-12', 'attrs' => ['name' => 'wpdm_reg[user_pass]', 'id' => 'reg_password', 'placeholder' => __( "Be Secure", "download-manager" ), 'required' => 'required']],
                        ['label' => __("Confirm Password", "download-manager"), 'type' => 'password', 'grid_class' => 'col-md-6 col-sm-12', 'attrs' => ['name' => 'confirm_user_pass', 'data-match' => '#reg_password', 'id' => 'reg_confirm_pass', 'placeholder' => __( "Do Not Forget", "download-manager" ), 'required' => 'required']]
                    ]
                );
            }
            if(!isset($params['captcha']) ||  $params['captcha'] ===  true) {
                $show_captcha = (int)get_option('__wpdm_recaptcha_regform', 0) === 1 && get_option('_wpdm_recaptcha_secret_key', '') != '';
                $show_captcha = apply_filters("signup_form_captcha", $show_captcha);
                if ($show_captcha) {
                    $reg_data_fields['__recap'] = array(
                        'type' => 'reCaptcha',
                        'attrs' => array('name' => '__recap', 'id' => '__recap'),
                    );
                }
            }
            $form = new Form($reg_data_fields, ['name' => 'wpdm_reg_form', 'id' => 'wpdm_reg_form', 'method' => 'POST', 'action' => '', 'submit_button' => [ ], 'noForm' => true]);
            return $form->render();

        }

        static function signinForm($params = []){
            $login_data_fields['__phash'] =[ 'type'  => 'hidden', 'attrs' => ['name' => '__phash', 'id' => '__phash', 'value' => Crypt::encrypt($params)] ];
            $login_data_fields['log'] = array(
                'label' => __( "Login ID", "download-manager" ),
                'type'  => 'text',
                'attrs' => array( 'name' => 'wpdm_login[log]', 'id' => 'user_login', 'required' => 'required', 'placeholder' => __( 'Username or Email', "download-manager" ) ),
            );
            $login_data_fields['password'] = array(
                'label' => __( "Password", "download-manager" ),
                'type'  => 'password',
                'attrs' => array( 'name' => 'wpdm_login[pwd]', 'id' => 'password', 'required' => 'required', 'placeholder' => __( "Enter Password", "download-manager" ) ),
            );
            if(!isset($params['captcha']) ||  $params['captcha'] ===  true) {
                $show_captcha = (int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_secret_key', '') != '';
                $show_captcha = apply_filters("signin_form_captcha", $show_captcha);
                if ($show_captcha) {
                    $login_data_fields['__recap'] = array(
                        'type' => 'reCaptcha',
                        'attrs' => array('name' => '__recap', 'id' => '__recap'),
                    );
                }
            }
            $form = new Form($login_data_fields, ['name' => 'wpdm_login_form', 'id' => 'wpdm_login_form', 'method' => 'POST', 'action' => '', 'submit_button' => [ ], 'noForm' => true]);
            return $form->render();

        }

        function modalLoginForm($params = array()){

            global $current_user;

            if(!isset($params) || !is_array($params)) $params = array();

            if(isset($params) && is_array($params))
                extract($params);
            if(!isset($redirect)) $redirect = get_permalink(get_option('__wpdm_user_dashboard'));

            if(!isset($regurl)) {
                $regurl = get_option('__wpdm_register_url');
                if ($regurl > 0)
                    $regurl = get_permalink($regurl);
            }
            $log_redirect =  $_SERVER['REQUEST_URI'];
            if(isset($params['redirect'])) $log_redirect = esc_url($params['redirect']);
            if(isset($_GET['redirect_to'])) $log_redirect = esc_url($_GET['redirect_to']);

            $up = parse_url($log_redirect);
            if(isset($up['host']) && $up['host'] != $_SERVER['SERVER_NAME']) $log_redirect = $_SERVER['REQUEST_URI'];

            $log_redirect = strip_tags($log_redirect);

            if(!isset($params['logo']) || $params['logo'] == '') $params['logo'] = get_site_icon_url();

            $__wpdm_social_login = get_option('__wpdm_social_login');
            $__wpdm_social_login = is_array($__wpdm_social_login)?$__wpdm_social_login:array();

            ob_start();
            //get_option('__wpdm_modal_login', 0)

            include( wpdm_tpl_path('wpdm-login-modal-form.php') );

            $content =  ob_get_clean();
            $content = apply_filters("wpdm_login_modal_form_html", $content);

            return $content;
        }

        function onUserLogin($username, $user){

            //Register last login time
            update_user_meta(get_current_user_id(), '__wpdm_last_login_time', time());

        }

    }

//echo User::signupForm();

endif;

