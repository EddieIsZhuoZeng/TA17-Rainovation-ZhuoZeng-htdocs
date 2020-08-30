<?php
namespace WPDM\libs;


use WPDM\Template;

class ShortCodes
{

    function __construct()
    {


        // Total Package Count
        add_shortcode('wpdm_package_count', array($this, 'TotalPackages'));

        // Total Package Count
        add_shortcode('wpdm_download_count', array($this, 'TotalDownloads'));

        // Login/Register Form
        add_shortcode('wpdm_login_form', array($this, 'loginForm'));

        // Login Modal Form
        add_shortcode('wpdm_modal_login_form', array($this, 'modalLoginFormBtn'));

         // Register form
        add_shortcode('wpdm_reg_form', array($this, 'registerForm'));

        // Edit Profile
        add_shortcode('wpdm_edit_profile', array($this, 'EditProfile'));

        // Show all packages
        add_shortcode('wpdm_packages', array($this, 'packages'));

        //Fetch packages from the given categories
        add_shortcode("wpdm_category", array($this, 'category'));

        // Show a package by id
        add_shortcode('wpdm_package', array($this, 'Package'));

        // Generate direct download link
        add_shortcode('wpdm_direct_link', array($this, 'directLink'));

        // Show all packages in a responsive table
        add_shortcode('wpdm_all_packages', array($this, 'allPackages'));
        add_shortcode('wpdm-all-packages', array($this, 'allPackages'));

        //Search Result
        add_shortcode('wpdm_search_result', array($this, 'searchResult'));

        //Packages by tag
        add_shortcode("wpdm_tag", array($this, 'packagesByTag'));

    }

    /**
     * @usage Short-code function for total download count
     * @param array $params
     * @return mixed
     */
    function TotalDownloads($params = array()){
        global $wpdb;
        $download_count = $wpdb->get_var("select sum(meta_value) from {$wpdb->prefix}postmeta where meta_key='__wpdm_download_count'");
        return $download_count;
    }

    /**
     * @usage Short-code function for total package count
     * @param array $params
     * @return mixed
     */
    function TotalPackages($params = array()){
        $count_posts = wp_count_posts('wpdmpro');
        $status = isset($params['status'])?$params['status']:'publish';
        if($status=='draft') return $count_posts->draft;
        if($status=='pending') return $count_posts->pending;
        return $count_posts->publish;
    }

    /**
     * @usage Short-code callback function for login form
     * @return string
     */
    function loginForm($params = array())
    {

        global $current_user;

        if (!isset($params) || !is_array($params)) $params = array();

        if (isset($params) && is_array($params))
            extract($params);
        if (!isset($redirect)) $redirect = get_permalink(get_option('__wpdm_user_dashboard'));

        if (!isset($regurl)) {
            $regurl = get_option('__wpdm_register_url');
            if ($regurl > 0)
                $regurl = get_permalink($regurl);
        }
        $log_redirect = $_SERVER['REQUEST_URI'];
        if (isset($params['redirect'])) $log_redirect = esc_url($params['redirect']);
        if (isset($_GET['redirect_to'])) $log_redirect = esc_url($_GET['redirect_to']);

        $up = parse_url($log_redirect);
        if (isset($up['host']) && $up['host'] != $_SERVER['SERVER_NAME']) $log_redirect = $_SERVER['REQUEST_URI'];

        $log_redirect = strip_tags($log_redirect);

        if (!isset($params['logo'])) $params['logo'] = get_site_icon_url();

        $__wpdm_social_login = get_option('__wpdm_social_login');
        $__wpdm_social_login = is_array($__wpdm_social_login) ? $__wpdm_social_login : array();

        ob_start();

        if (is_user_logged_in())
            $template = wpdm_tpl_path("already-logged-in.php", WPDM_TPL_DIR, WPDM_TPL_FALLBACK);
        else {
            if (wpdm_query_var('action') === 'lostpassword')
                $template = wpdm_tpl_path('lost-password-form.php');
            else if (wpdm_query_var('action') === 'rp')
                $template = wpdm_tpl_path('reset-password-form.php');
            else
                $template = wpdm_tpl_path('wpdm-login-form.php');
        }

        include($template);

        $content = ob_get_clean();
        $content = apply_filters("wpdm_login_form_html", $content);

        return $content;
    }

    /**
     * @param array $params
     * @return false|string
     */
    function modalLoginFormBtn($params = array())
    {
        if ((int)get_option('__wpdm_modal_login', 0) !== 1) return "";
        $defaults = array('class' => '', 'redirect' => '', 'label' => __('Login', 'download-manager'), 'id' => 'wpdmmodalloginbtn');
        $params = shortcode_atts($defaults, $params, 'wpdm_modal_login_form');
        $redirect = isset($params['redirect']) && $params['redirect'] !== '' ? "data-redirect='{$params['redirect']}'" : '';
        ob_start();
        ?>
        <div class="w3eden d-inline-block"><a href="#" <?php echo $redirect; ?> type="button"
                                              id="<?php echo $params['id']; ?>" class="<?php echo $params['class']; ?>"
                                              data-toggle="modal"
                                              data-target="#wpdmloginmodal"><?php echo $params['label']; ?></a></div>
        <?php
        $btncode = ob_get_clean();
        return $btncode;
    }



    /**
     * @usage Edit profile
     * @return string
     */
    public function EditProfile()
    {
        global $wpdb, $current_user, $wp_query;
        wp_reset_query();
        $currentAccess = maybe_unserialize(get_option('__wpdm_front_end_access', array()));

        if (!array_intersect($currentAccess, $current_user->roles) && is_user_logged_in())
            return WPDM_Messages::Error(wpautop(stripslashes(get_option('__wpdm_front_end_access_blocked'))), -1);


        $id = wpdm_query_var('ID');

        ob_start();

        if (is_user_logged_in()) {
            include(wpdm_tpl_path('wpdm-edit-user-profile.php'));
        } else {
            $this->loginForm();
        }

        $data = ob_get_clean();
        return $data;
    }

    function registerForm($params = array())
    {

        if (!get_option('users_can_register')) return \WPDM_Messages::warning(__("User registration is disabled", "download-manager"), -1);

        if (!isset($params) || !is_array($params)) $params = array();

        ob_start();
        $regparams = \WPDM\libs\Crypt::Encrypt($params);

        $_social_only = isset($params['social_only']) && ($params['social_only'] === 'true' || (int)$params['social_only'] === 1) ? true : false;
        $_verify_email = isset($params['verifyemail']) && ($params['verifyemail'] === 'true' || (int)$params['verifyemail'] === 1) ? true : false;
        $_show_captcha = !isset($params['captcha']) || ($params['captcha'] === 'true' || (int)$params['captcha'] === 1) ? true : false;
        $_auto_login = isset($params['autologin']) && ($params['autologin'] === 'true' || (int)$params['autologin'] === 1) ? true : false;


        $loginurl = wpdm_login_url();
        $reg_redirect = $loginurl;
        if (isset($params['autologin']) && (int)$params['autologin'] === 1) $reg_redirect = wpdm_user_dashboard_url();
        if (isset($params['redirect'])) $reg_redirect = esc_url($params['redirect']);
        if (isset($_GET['redirect_to'])) $reg_redirect = esc_url($_GET['redirect_to']);

        $force = uniqid();

        $up = parse_url($reg_redirect);
        if (isset($up['host']) && $up['host'] != $_SERVER['SERVER_NAME']) $reg_redirect = home_url('/');

        $reg_redirect = esc_attr(esc_url($reg_redirect));

        if (!isset($params['logo'])) $params['logo'] = get_site_icon_url();

        \WPDM\Session::set('__wpdm_reg_params', $params);

        $tmp_reg_info = \WPDM\Session::get('tmp_reg_info');

        $__wpdm_social_login = get_option('__wpdm_social_login');
        $__wpdm_social_login = is_array($__wpdm_social_login) ? $__wpdm_social_login : array();

        //Template
        include(wpdm_tpl_path('wpdm-reg-form.php'));

        $data = ob_get_clean();
        return $data;
    }

    /**
     * @param array $params
     * @return string
     */
    function packages($params = array('items_per_page' => 10, 'title' => false, 'desc' => false, 'order_by' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => '', 'cols' => 3, 'colspad' => 2, 'colsphone' => 1, 'tags' => '', 'categories' => '', 'year' => '', 'month' => '', 's' => '', 'css_class' => 'wpdm_packages', 'scid' => '', 'async' => 1))
    {
        global $current_user, $post;

        static $wpdm_packages = 0;

        if (isset($params['login']) && $params['login'] == 1 && !is_user_logged_in())
            return $this->loginForm($params);

        $wpdm_packages++;

        //$params['order_by']  = isset($params['order_field']) && $params['order_field'] != '' && !isset($params['order_by'])?$params['order_field']:$params['order_by'];
        $scparams = $params;
        $defaults = array('author' => '', 'author_name' => '', 'items_per_page' => 10, 'title' => false, 'desc' => false, 'order_by' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => 'link-template-panel', 'cols' => 3, 'colspad' => 2, 'colsphone' => 1, 'css_class' => 'wpdm_packages', 'scid' => 'wpdm_packages_' . $wpdm_packages, 'async' => 1);
        $params = shortcode_atts($defaults, $params, 'wpdm_packages');

        if (is_array($params))
            extract($params);

        if (!isset($items_per_page) || $items_per_page < 1) $items_per_page = 10;

        $cwd_class = "col-lg-" . (int)(12 / $cols);
        $cwdsm_class = "col-md-" . (int)(12 / $colspad);
        $cwdxs_class = "col-sm-" . (int)(12 / $colsphone);

        if (isset($order_by) && !isset($order_field)) $order_field = $order_by;
        $order_field = isset($order_field) ? $order_field : 'date';
        $order_field = isset($_GET['orderby']) ? esc_attr($_GET['orderby']) : $order_field;
        $order = isset($order) ? $order : 'desc';
        $order = isset($_GET['order']) ? esc_attr($_GET['order']) : $order;
        $cp = wpdm_query_var('cp', 'num');
        if (!$cp) $cp = 1;

        $params = array(
            'post_type' => 'wpdmpro',
            'paged' => $cp,
            'posts_per_page' => $items_per_page,
        );

        if (isset($scparams['s']) && $scparams['s'] != '') $params['s'] = $scparams['s'];
        if (isset($_GET['skw']) && $_GET['skw'] != '') $params['s'] = wpdm_query_var('skw', 'txt');
        if (isset($scparams['author']) && $scparams['author'] != '') $params['author'] = $scparams['author'];
        if (isset($scparams['author_name']) && $scparams['author_name'] != '') $params['author_name'] = $scparams['author_name'];
        if (isset($scparams['author__not_in']) && $scparams['author__not_in'] != '') $params['author__not_in'] = explode(",", $scparams['author__not_in']);
        if (isset($scparams['search']) && $scparams['search'] != '') $params['s'] = $scparams['search'];
        if (isset($scparams['tag']) && $scparams['tag'] != '') $params['tag'] = $scparams['tag'];
        if (isset($scparams['tag_id']) && $scparams['tag_id'] != '') $params['tag_id'] = $scparams['tag_id'];
        if (isset($scparams['tag__and']) && $scparams['tag__and'] != '') $params['tag__and'] = explode(",", $scparams['tag__and']);
        if (isset($scparams['tag__in']) && $scparams['tag__in'] != '') $params['tag__in'] = explode(",", $scparams['tag__in']);
        if (isset($scparams['tag__not_in']) && $scparams['tag__not_in'] != '') {
            $params['tag__not_in'] = explode(",", $scparams['tag__not_in']);
            foreach ($params['tag__not_in'] as &$tg) {
                if (!is_numeric($tg)) {
                    $tgg = get_term_by('slug', $tg, 'post_tag');
                    $tg = $tgg->term_id;
                }
            }
        }

        if (isset($scparams['post__in']) && $scparams['post__in'] != '') $params['post__in'] = explode(",", $scparams['post__in']);
        if (isset($scparams['post__not_in']) && $scparams['post__not_in'] != '') $params['post__not_in'] = explode(",", $scparams['post__not_in']);

        if (isset($scparams['tag_slug__and']) && $scparams['tag_slug__and'] != '') $params['tag_slug__and'] = explode(",", $scparams['tag_slug__and']);
        if (isset($scparams['tag_slug__in']) && $scparams['tag_slug__in'] != '') $params['tag_slug__in'] = explode(",", $scparams['tag_slug__in']);
        if (isset($scparams['categories']) && $scparams['categories'] != '') {
            $operator = isset($scparams['operator']) ? $scparams['operator'] : 'IN';
            $scparams['categories'] = trim($scparams['categories'], ",");
            $params['tax_query'] = array(array(
                'taxonomy' => 'wpdmcategory',
                'field' => 'slug',
                'terms' => explode(",", $scparams['categories']),
                'include_children' => (isset($scparams['include_children']) && $scparams['include_children'] != '') ? $scparams['include_children'] : false,
                'operator' => $operator
            ));
        }

        if (isset($scparams['xcats']) && $scparams['xcats'] != '') {
            $xcats = explode(",", $scparams['xcats']);
            foreach ($xcats as &$xcat) {
                if (!is_numeric($xcat)) {
                    $xct = get_term_by('slug', $xcat, 'wpdmcategory');
                    $xcat = $xct->term_id;
                }
            }
            $params['tax_query'][] = array(
                'taxonomy' => 'wpdmcategory',
                'field' => 'term_id',
                'terms' => $xcats,
                'operator' => 'NOT IN',
            );
        }

        if (isset($params['tax_query']) && count($params['tax_query']) > 1)
            $params['tax_query']['relation'] = 'AND';


        if (get_option('_wpdm_hide_all', 0) == 1) {
            $params['meta_query'] = array(
                array(
                    'key' => '__wpdm_access',
                    'value' => '"guest"',
                    'compare' => 'LIKE'
                )
            );
            if (is_user_logged_in()) {
                $params['meta_query'][] = array(
                    'key' => '__wpdm_access',
                    'value' => $current_user->roles[0],
                    'compare' => 'LIKE'
                );
                $params['meta_query']['relation'] = 'OR';
            }
        }

        if (isset($scparams['year']) || isset($scparams['month']) || isset($scparams['day'])) {
            $date_query = array();

            if (isset($scparams['day']) && $scparams['day'] == 'today') $scparams['day'] = date('d');
            if (isset($scparams['year']) && $scparams['year'] == 'this') $scparams['year'] = date('Y');
            if (isset($scparams['month']) && $scparams['month'] == 'this') $scparams['month'] = date('m');
            if (isset($scparams['week']) && $scparams['week'] == 'this') $scparams['week'] = date('W');

            if (isset($scparams['year'])) $date_query['year'] = $scparams['year'];
            if (isset($scparams['month'])) $date_query['month'] = $scparams['month'];
            if (isset($scparams['week'])) $date_query['week'] = $scparams['week'];
            if (isset($scparams['day'])) $date_query['day'] = $scparams['day'];
            $params['date_query'][] = $date_query;
        }

        $order_fields = array('__wpdm_download_count', '__wpdm_view_count', '__wpdm_package_size_b');
        if (!in_array("__wpdm_" . $order_field, $order_fields)) {
            $params['orderby'] = $order_field;
            $params['order'] = $order;
        } else {
            $params['orderby'] = 'meta_value_num';
            $params['meta_key'] = "__wpdm_" . $order_field;
            $params['order'] = $order;
        }

        $params = apply_filters("wpdm_packages_query_params", $params);

        $packs = new \WP_Query($params);

        $total = $packs->found_posts;

        $pages = ceil($total / $items_per_page);
        $page = isset($_GET['cp']) ? (int)$_GET['cp'] : 1;
        $start = ($page - 1) * $items_per_page;


        $html = '';
        $templates = maybe_unserialize(get_option("_fm_link_templates", true));

        if (isset($template) && isset($templates[$template])) $template = $templates[$template]['content'];

        //global $post;
        while ($packs->have_posts()) {
            $packs->the_post();

            $pack = (array)$post;
            $repeater = "<div class='{$cwd_class} {$cwdsm_class} {$cwdxs_class}'>" . \WPDM\Package::fetchTemplate($template, $pack) . "</div>";
            $html .= $repeater;

        }
        wp_reset_postdata();

        $html = "<div class='row'>{$html}</div>";


        if (!isset($paging) || intval($paging) == 1) {
            $pag_links = wpdm_paginate_links($total, $items_per_page, $page, 'cp', array('container' => '#content_' . $scid, 'async' => (isset($async) && $async == 1 ? 1 : 0), 'next_text' => ' <i style="display: inline-block;width: 8px;height: 8px;border-right: 2px solid;border-top: 2px solid;transform: rotate(45deg);margin-left: -2px;margin-top: -2px;"></i> ', 'prev_text' => ' <i style="display: inline-block;width: 8px;height: 8px;border-right: 2px solid;border-bottom: 2px solid;transform: rotate(135deg);margin-left: 2px;margin-top: -2px;"></i> '));
            $pagination = "<div style='clear:both'></div>" . $pag_links . "<div style='clear:both'></div>";
        } else
            $pagination = "";

        global $post;

        $burl = get_permalink();
        $sap = get_option('permalink_structure') ? '?' : '&';
        $burl = $burl . $sap;
        if (isset($_GET['p']) && $_GET['p'] != '') $burl .= 'p=' . esc_attr($_GET['p']) . '&';
        if (isset($_GET['src']) && $_GET['src'] != '') $burl .= 'src=' . esc_attr($_GET['src']) . '&';
        $orderby = isset($_GET['orderby']) ? esc_attr($_GET['orderby']) : 'date';
        $order = ucfirst($order);

        $order_field = " " . __(ucwords(str_replace("_", " ", $order_field)), "wpdmpro");
        $ttitle = __("Title", "download-manager");
        $tdls = __("Downloads", "download-manager");
        $tcdate = __("Publish Date", "download-manager");
        $tudate = __("Update Date", "download-manager");
        $tasc = __("Asc", "download-manager");
        $tdsc = __("Desc", "download-manager");
        $tsrc = __("Search", "download-manager");
        $ord = __("Order", "download-manager");
        $order_by_label = __("Order By", "download-manager");

        $css_class = isset($scparams['css_class']) ? sanitize_text_field($scparams['css_class']) : '';
        $desc = isset($scparams['desc']) ? sanitize_text_field($scparams['desc']) : '';

        $title = isset($title) && $title != '' && $total > 0 ? "<h3>$title</h3>" : "";


        $toolbar = isset($toolbar) ? $toolbar : 0;

        ob_start();
        include Template::locate("shortcodes/packages.php", WPDM_TPL_FALLBACK);
        $content = ob_get_clean();
        return $content;
    }

    function category($params = [])
    {
        return WPDM()->categories->embed($params);
    }

    /**
     * @param array $params
     * @return array|null|WP_Post
     * @usage Shortcode callback function for [wpdm_simple_search]
     */
    function searchResult($params = array())
    {
        global $wpdb;

        if (is_array($params))
            @extract($params);
        $template = isset($template) && $template != '' ? $template : 'link-template-calltoaction3';
        $async = isset($async) ? $async : 0;
        $items_per_page = isset($items_per_page) ? $items_per_page : 0;
        update_post_meta(get_the_ID(), "__wpdm_link_template", $template);
        update_post_meta(get_the_ID(), "__wpdm_items_per_page", $items_per_page);
        $strm = wpdm_query_var('search', 'txt');
        if ($strm === '') $strm = wpdm_query_var('s', 'txt');
        $html = '';
        $cols = isset($cols) ? $cols : 1;
        $colspad = isset($colspad) ? $colspad : 1;
        $colsphone = isset($colsphone) ? $colsphone : 1;
        if (($strm == '' && isset($init) && $init == 1) || $strm != '')
            $html = $this->packages(array('items_per_page' => $items_per_page, 'template' => $template, 's' => $strm, 'paging' => true, 'toolbar' => 0, 'cols' => $cols, 'colsphone' => $colsphone, 'colspad' => $colspad, 'async' => $async));
        $html = "<div class='w3eden'><form id='wpdm-search-form' style='margin-bottom: 20px'><div class='input-group input-group-lg'><div class='input-group-addon input-group-prepend'><span class=\"input-group-text\"><i class='fas fa-search'></i></span></div><input type='text' name='search' value='" . $strm . "' class='form-control input-lg' /></div></form>{$html}</div>";
        return str_replace(array("\r", "\n"), "", $html);
    }


    /**
     * @usage Callback function for shortcode [wpdm_package id=PID]
     * @param mixed $params
     * @return mixed
     */
    function Package($params)
    {
        extract($params);
        if(!isset($id)) return '';
        $id = (int)$id;
        $postlink = site_url('/');
        if (isset($pagetemplate) && $pagetemplate == 1) {
            $template = get_post_meta($id,'__wpdm_page_template', true);
            $wpdm_package['page_template'] = stripcslashes($template);
            $data = FetchTemplate($template, $id, 'page');
            $siteurl = site_url('/');
            return  "<div class='w3eden'>{$data}</div>";
        }

        $template = isset($params['template'])?$params['template']:get_post_meta($id,'__wpdm_template', true);
        if($template == '') $template = 'link-template-default.php';
        return "<div class='w3eden'>" . \WPDM\Package::fetchTemplate($template, $id, 'link') . "</div>";
    }

    /**
     * @usage Generate direct link to download
     * @param $params
     * @param string $content
     * @return string
     */
    function directLink($params, $content = "")
    {
        extract($params);

        global $wpdb;
        $ID = (int)$params['id'];

        if(\WPDM\Package::isLocked($ID))
            $linkURL = get_permalink($ID);
        else
            $linkURL = home_url("/?wpdmdl=".$ID);

        $extras = isset($params['extras'])?wpdm_sanitize_var($params['extras'], 'txt'):"";
        $target = isset($params['target'])?"target='".wpdm_sanitize_var($params['target'], 'txt')."'":"";
        $class = isset($params['class'])?"class='".wpdm_sanitize_var($params['class'], 'txt')."'":"";
        $style = isset($params['style'])?"style='".wpdm_sanitize_var($params['style'], 'txt')."'":"";
        $rel = isset($params['rel'])?"rel='".wpdm_sanitize_var($params['rel'], 'txt')."'":"";
        $eid = isset($params['eid'])?"id='".wpdm_sanitize_var($params['eid'], 'txt')."'":"";
        $linkLabel = isset($params['label']) && !empty($params['label'])?$params['label']:get_post_meta($ID, '__wpdm_link_label', true);
        $linkLabel = empty($linkLabel)?get_the_title($ID):$linkLabel;
        $linkLabel = wpdm_sanitize_var($linkLabel, 'kses');
        return  "<a {$target} {$class} {$eid} {$style} {$rel} {$extras} href='$linkURL'>$linkLabel</a>";

    }


    /**
     * @usage Short-code [wpdm_all_packages] to list all packages in tabular format
     * @param array $params
     * @return string
     */
    function AllPackages($params = array())
    {
        global $wpdb, $current_user, $wp_query;
        $items = isset($params['items_per_page']) && $params['items_per_page'] > 0 ? $params['items_per_page'] : 20;
        if(isset($params['jstable']) && $params['jstable']==1) $items = 2000;
        $cp = isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] > 0 ? $wp_query->query_vars['paged'] : 1;
        $terms = isset($params['categories']) ? explode(",", $params['categories']) : array();
        if (isset($_GET['wpdmc'])) $terms = array(esc_attr($_GET['wpdmc']));
        $offset = ($cp - 1) * $items;
        $total_files = wp_count_posts('wpdmpro')->publish;
        if (count($terms) > 0) {
            $tax_query = array(array(
                'taxonomy' => 'wpdmcategory',
                'field' => 'slug',
                'terms' => $terms,
                'operator' => 'IN',
                'include_children' => false
            ));
        }

        ob_start();
        include(wpdm_tpl_path("wpdm-all-downloads.php"));
        $data = ob_get_clean();
        return $data;
    }

    /**
     * @usage Show packages by tag
     * @param $params
     * @return string
     */
    function packagesByTag($params)
    {
        $params['order_field'] = isset($params['order_by'])?$params['order_by']:'publish_date';
        $params['tag'] = 1;
        unset($params['order_by']);
        if (isset($params['item_per_page']) && !isset($params['items_per_page'])) $params['items_per_page'] = $params['item_per_page'];
        unset($params['item_per_page']);
        return wpdm_embed_category($params);

    }





}
