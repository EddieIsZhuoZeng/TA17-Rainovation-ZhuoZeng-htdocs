<?php
namespace WPDM;

use WPDM\libs\Crypt;

class UserProfile
{

    public $profile_menu;
    public $user;

    function __construct(){
        add_action("init", array($this, 'profileMenuInit'));
        add_action("wp_ajax_wpdm_get_profile_menu_content", array($this, 'menuContent'));
        add_action("wp_ajax_nopriv_wpdm_get_profile_menu_content", array($this, 'menuContent'));
        add_shortcode("wpdm_user_profile", array($this, 'profile'));
    }

    function profileMenuInit(){
        $this->profile_menu['downloads'] = array('icon' => 'fas fa-arrow-alt-circle-down', 'name'=> __( "Downloads" , "download-manager" ), 'content' => array($this, 'downloads'));
        $this->profile_menu['favourites'] = array('icon' => 'fas fa-heart', 'name'=> __( "Favourites" , "download-manager" ), 'content' => array($this, 'favourites'));
        $this->profile_menu = apply_filters("wpdm_user_profile_menu", $this->profile_menu);
    }

    function menuContent(){
        call_user_func($this->profile_menu[wpdm_query_var('__pmenu')]['content']);
        die();
    }

    function profile($params = array()){
        global $wp_query;


        if(!isset($params) || !is_array($params)) $params = array();

        ob_start();
        global $current_user;
        $username = get_query_var('profile');
        if(is_author())
            $username = get_query_var('author_name');
        if($username)
            $user = get_user_by('login', $username);
        else
            $user = $current_user;


        $cols = isset($params['cols'])?$params['cols']:3;
        $items_per_page = isset($params['items_per_page'])?$params['items_per_page']:$cols*3;
        $cols = 12/$cols;
        $template = isset($params['template'])?$params['template']:'link-template-panel.php';

        $user_ID = $user->ID;
        $store = get_user_meta($user_ID, '__wpdm_public_profile', true);
        if(!is_array($store)) $store = array();
        $store['logo'] = isset($store['logo'])?$store['logo']:get_avatar_url($user_ID);
        $store['title'] = isset($store['title']) && $store['title'] != '' ? $store['title'] : $user->display_name;
        $store['intro'] = isset($store['intro']) && $store['intro'] != '' ? $store['intro'] : '';
        $store['description'] = isset($store['description']) && $store['description'] != '' ? $store['description'] : '';
        $store['banner'] = isset($store['banner']) && $store['banner'] != '' ? $store['banner'] : '';
        $store['bgcolor'] = isset($store['bgcolor']) && $store['bgcolor'] != '' ? $store['bgcolor'] : '#eeeeee';
        $store['txtcolor'] = isset($store['txtcolor']) && $store['txtcolor'] != '' ? $store['txtcolor'] : '#333333';
        $mydownloads = count_user_posts($user->ID, 'wpdmpro');
        $rgb = wpdm_hex2rgb($store['bgcolor']);
        $first_menu = array_keys($this->profile_menu);
        $first_menu = $first_menu[0];

        include_once wpdm_tpl_path('user-profile/profile.php');
        return ob_get_clean();
    }


    function downloads(){
        $params = Crypt::decrypt(wpdm_query_var('__scp'));
        $params['author'] = wpdm_query_var('__pu', 'int');
        echo WPDM()->shortCode->packages($params);
    }

    function favourites(){
        $params = Crypt::decrypt(wpdm_query_var('__scp'));
        $myfavs = maybe_unserialize(get_user_meta(wpdm_query_var('__pu', 'int'), '__wpdm_favs', true));
        $params['post__in'] = implode(",", $myfavs);
        echo WPDM()->shortCode->packages($params);

    }


}

