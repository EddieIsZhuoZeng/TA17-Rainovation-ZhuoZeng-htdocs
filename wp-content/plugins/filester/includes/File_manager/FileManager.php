<?php
namespace NinjaFileManager\File_manager;

defined('ABSPATH') || exit;

/**
 * Settings Page
 */

class FileManager
{
    protected static $instance = null;
    
    /**
     *
     * @var object $options The object of the options class
     *
     * */
    public $options;
    public $fmCapability = '';
    public $userRole = '';
    private $hook_suffix = array();
    
    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __construct()
    {
        //get user role
        $user = wp_get_current_user();
        $this->userRole = $user && $user->roles && $user->roles[0] ? $user->roles[0] : '';

        // Loading Options
        // Options
		$this->options = get_option('njt_fs_settings');
        if(empty($this->options)) {
            $this->options = array( // Setting up default values
                'njt_fs_file_manager_settings' => array(
                    'root_folder_path' =>  ABSPATH,
                    'root_folder_url' => site_url()
                ),
            );
        }
        register_shutdown_function(array($this, 'saveOptions'));

        add_action('init', array($this, 'isAlowUserAccess'));
        if ($this->isAlowUserAccess()) {
            add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
            add_action('admin_menu', array($this, 'FileManager'));
            add_action('wp_ajax_fs_connector', array($this, 'fsConnector'));
            add_action('wp_ajax_selector_themes', array($this, 'selectorThemes'));
            add_action('wp_ajax_get_role_restrictions', array($this, 'getArrRoleRestrictions'));
       }
    }

    public function isAlowUserAccess()
    {
        if($this->userRole) {
            $allowed_roles = !empty($this->options['njt_fs_file_manager_settings']['list_user_alow_access']) ? $this->options['njt_fs_file_manager_settings']['list_user_alow_access'] : array();
            if( in_array($this->userRole,$allowed_roles) || $this->userRole == 'administrator') {
                $this->fmCapability = $this->userRole;
                return true;
            }
        }
        if (is_super_admin()) {
            $this->fmCapability = 'administrator';
            return true;
        }
        $this->fmCapability = 'read';
        return false;
    }

    public function FileManager()
    {

        $display_suffix = add_menu_page(
            __('Custom Menu Title', 'textdomain'),
            'File Manager',
            $this->fmCapability,
            'njt-fs-filemanager',
            array($this, 'fsViewFileCallback'),
            NJT_FS_BN_PLUGIN_URL . 'assets/img/fs_icon.svg',
            9
        );
        
        $settings_suffix = add_submenu_page (
          'njt-fs-filemanager',
          'Settings',
          'Settings', 
          'manage_options', 
          'njt-fs-filemanager-settings',
          array($this, 'fsSettingsPage') );

        $this->hook_suffix = array($display_suffix, $settings_suffix);
    }

    public function fsViewFileCallback()
    {
        $viewPath = NJT_FS_BN_PLUGIN_PATH . 'views/pages/html-filemanager.php';
        include_once $viewPath;
    }

    public function fsSettingsPage()
    {
        $viewPath = NJT_FS_BN_PLUGIN_PATH . 'views/pages/html-filemanager-settings.php';
        include_once $viewPath;
    }

    public function enqueueAdminScripts($suffix)
    {
        wp_register_style('file_manager_icon_css',NJT_FS_BN_PLUGIN_URL . 'assets/css/style-icon.css');
        wp_enqueue_style('file_manager_icon_css');

        if (in_array($suffix, $this->hook_suffix)) {
            $selectorThemes = get_option('njt_fs_selector_themes');
            if (empty($selectorThemes[$this->userRole])) {
                $selectorThemes[$this->userRole]['themesValue'] = 'Default';
                update_option('njt_fs_selector_themes', $selectorThemes);
            }
        
            $selectedTheme = $selectorThemes[$this->userRole]['themesValue'];

            //elfinder css
            wp_enqueue_style('elfinder.jq.css', plugins_url('/lib/jquery/jquery-ui.css', __FILE__));
            wp_enqueue_style('elfinder.full.css', plugins_url('/lib/css/elfinder.full.css', __FILE__));
            wp_enqueue_style('themes', plugins_url('/lib/css/theme.css', __FILE__));
            wp_enqueue_style('themes-selector', plugins_url('/lib/themes/' . $selectedTheme . '/css/theme.css', __FILE__));
        
            //elfinder core
            wp_enqueue_script('jquery_min', plugins_url('/lib/jquery/jquery-ui.min.js', __FILE__));
            
            //elfinder js, css custom
            wp_register_style('file_manager_admin_css',NJT_FS_BN_PLUGIN_URL . 'assets/css/file_manager_admin.css');
            wp_enqueue_style('file_manager_admin_css');
            wp_enqueue_script('file_manager_admin', NJT_FS_BN_PLUGIN_URL . 'assets/js/file_manager_admin.js', array('jquery'), NJT_FS_BN_VERSION);
            wp_localize_script('file_manager_admin', 'wpData', array(
                'admin_ajax' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce("njt-fs-file-manager-admin"),
                'PLUGIN_URL' => NJT_FS_BN_PLUGIN_URL .'includes/File_manager/lib/',
                'PLUGIN_PATH' => NJT_FS_BN_PLUGIN_PATH.'includes/File_manager/lib/',
                'PLUGIN_DIR'=> NJT_FS_BN_PLUGIN_DIR,
                'ABSPATH'=> str_replace("\\", "/", ABSPATH)

            ));

            //js load elFinder
            wp_enqueue_script('elFinder', plugins_url('/lib/js/elfinder.full.js', __FILE__));
            wp_enqueue_script('elfinder_editor', plugins_url('/lib/js/extras/editors.default.js', __FILE__));
            //js load fm_locale
            if(isset($this->options['njt_fs_file_manager_settings']['fm_locale'])) {
                $locale = $this->options['njt_fs_file_manager_settings']['fm_locale'];
                if($locale != 'en') {
                    wp_enqueue_script( 'fma_lang', plugins_url('lib/js/i18n/elfinder.'.$locale.'.js', __FILE__));
                }
            }
        }
    }

    //File manager connector function

    public function fsConnector()
    {
        if( isset( $_POST ) && !empty( $_POST ) && ! wp_verify_nonce( $_POST['nonce'] ,'file-manager-security-token') ) wp_die();
        $uploadMaxSize = isset($this->options['njt_fs_file_manager_settings']['upload_max_size']) && !empty($this->options['njt_fs_file_manager_settings']['upload_max_size']) ? $this->options['njt_fs_file_manager_settings']['upload_max_size'] : 0;

        $opts = array(
            'bind' => array(
                'put.pre' => array(new \FileManagerHelper, 'madeStripcslashesFile'), // Check endcode when save file.
            ),
            'roots' => array(
                array(
                    'driver' => 'LocalFileSystem',
                    'path' => isset($this->options['njt_fs_file_manager_settings']['root_folder_path']) && !empty($this->options['njt_fs_file_manager_settings']['root_folder_path']) ? $this->options['njt_fs_file_manager_settings']['root_folder_path'] : ABSPATH,
                    'URL' => isset($this->options['njt_fs_file_manager_settings']['root_folder_url']) && !empty($this->options['njt_fs_file_manager_settings']['root_folder_url']) ? $this->options['njt_fs_file_manager_settings']['root_folder_url'] :site_url(),
                    'trashHash'     => '', // default is empty, when not enable trash
                    'uploadMaxSize' =>  $uploadMaxSize .'M',
                    'winHashFix'    => DIRECTORY_SEPARATOR !== '/', 
                    'uploadDeny'    => array(), 
                    'uploadAllow'   => array('all'),
                    'uploadOrder'   => array('deny', 'allow'),
                    'disabled' => array(''),
                    'acceptedName' => 'validName',
                    'attributes' => array() // default is empty
                ),
            ),
        );
        // .htaccess
        if(isset($this->options['njt_fs_file_manager_settings']['enable_htaccess']) && ($this->options['njt_fs_file_manager_settings']['enable_htaccess'] == '1')) {
            $attributes = array(
                'pattern' => '/.htaccess/',
                'read' => false,
                'write' => false,
                'hidden' => true,
                'locked' => false
            );
            array_push($opts['roots'][0]['attributes'], $attributes);
        }

        //Enable Trash
        if(isset($this->options['njt_fs_file_manager_settings']['enable_trash']) && ($this->options['njt_fs_file_manager_settings']['enable_trash'] == '1')) {
            $trash = array(
                'id'            => '1',
                'driver'        => 'Trash',
                'path'          => NJT_FS_BN_PLUGIN_PATH.'includes/File_manager/lib/files/.trash/',
                'tmbURL'        => site_url() . '/includes/File_manager/lib/files/.trash/.tmb',
                'winHashFix'    => DIRECTORY_SEPARATOR !== '/', 
                'uploadDeny'    => array(), 
                'uploadAllow'   => array('all'),
                'uploadOrder'   => array('deny', 'allow'),
                'acceptedName' => 'validName',
                'attributes' => array(
                    array(
                        'pattern' => '/.tmb/',
                        'read' => false,
                        'write' => false,
                        'hidden' => true,
                        'locked' => false
                    ),
                    array(
                        'pattern' => '/.gitkeep/',
                        'read' => false,
                        'write' => false,
                        'hidden' => true,
                        'locked' => false
                    )
                )
            );
            $opts['roots'][0]['trashHash'] = 't1_Lw';
            $opts['roots'][1] = $trash;
        }

        //Start --setting User Role Restrictions
        $user = wp_get_current_user();
        $userRoles =  $user && $user->roles && $user->roles[0] ? $user->roles[0] : '';
        
        //Disable Operations
        if(!empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['list_user_restrictions_alow_access'])){
            $opts['roots'][0]['disabled'] = $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['list_user_restrictions_alow_access'];
        }
        //Creat root path for user
        if(!empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['private_folder_access'])){
            $opts['roots'][0]['path'] = $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['private_folder_access'] .'/';
        }

        //Folder or File Paths That You want to Hide
        if(!empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['hide_paths'])){
            foreach ($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['hide_paths'] as $key => $value){
                $arrItemHidePath =  array( 
                     'pattern' => '~/'.$value.'~',
                     'read' => false,
                     'write' => false,
                     'hidden' => true,
                     'locked' => false
                   );
                   array_push($opts['roots'][0]['attributes'], $arrItemHidePath);
               };
        }

        //File extensions which you want to Lock
        if(!empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['lock_files'])){
            foreach ($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['lock_files'] as $key => $value){
                $arrItemLockFile =  array( 
                     'pattern' => '/'.$value.'/',
                     'read' => false,
                     'write' => false,
                     'hidden' => false,
                     'locked' => true
                   );
                   array_push($opts['roots'][0]['attributes'], $arrItemLockFile);
               };
        }

        //Enter file extensions which can be uploaded
        if($this->userRole !== 'administrator' && empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['can_upload_mime'])) {
            $opts['roots'][0]['uploadDeny'] = array('all');
            $opts['roots'][0]['uploadAllow'] = array('');
        } else if ( $this->userRole !== 'administrator' && !empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['can_upload_mime'])) {
            $opts['roots'][0]['uploadDeny'] = array('all');
            $opts['roots'][0]['uploadAllow'] = array();
            $arrCanUploadMime = $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$this->userRole]['can_upload_mime'];
            $mimeTypes = new \FileManagerHelper();
            $arrMimeTypes = $mimeTypes->getArrMimeTypes();
            foreach ($arrMimeTypes as $key => $value){
                if(in_array($key,$arrCanUploadMime)) {
                    array_push($opts['roots'][0]['uploadAllow'], $value);
                }
            };
        } else {
            $opts['roots'][0]['uploadDeny'] = array();
            $opts['roots'][0]['uploadAllow'] = array('all');
        }
        //End --setting User Role Restrictions

        $connector = new \elFinderConnector(new \elFinder($opts));
        $connector->run();
        wp_die();
    }
    
    public function selectorThemes()
    {
        if( ! wp_verify_nonce( $_POST['nonce'] ,'njt-fs-file-manager-admin')) wp_die();
        check_ajax_referer('njt-fs-file-manager-admin', 'nonce', true);
        
        $themesValue = sanitize_text_field ($_POST['themesValue']);
        $selectorThemes = get_option('njt_fs_selector_themes');
        if (empty($selectorThemes[$this->userRole])) {
            $selectorThemes[$this->userRole]['themesValue'] = 'Default';
            update_option('njt_fs_selector_themes', $selectorThemes);
        }
       
        if ($selectorThemes[$this->userRole]['themesValue'] != $themesValue) {
            $selectorThemes[$this->userRole]['themesValue'] = $themesValue;
            update_option('njt_fs_selector_themes', $selectorThemes);
        }
        $selected_themes = get_option('njt_fs_selector_themes');
        $linkThemes = plugins_url('/lib/themes/' . $selected_themes[$this->userRole]['themesValue'] . '/css/theme.css', __FILE__);
        wp_send_json_success($linkThemes);
        wp_die();
    }

    public function saveOptions()
    {
		update_option('njt_fs_settings', $this->options);
    }
    
    public function getArrRoleRestrictions()
    {
        if(!wp_verify_nonce( $_POST['nonce'] ,'njt-fs-file-manager-admin')) wp_die();
        check_ajax_referer('njt-fs-file-manager-admin', 'nonce', true);
        $valueUserRole = filter_var($_POST['valueUserRole']) ? sanitize_text_field ($_POST['valueUserRole']) : '';
        $arrRestrictions = !empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions']) ? $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'] : array();
        $dataArrRoleRestrictions = array (
            'disable_operations' => implode(",", !empty($arrRestrictions[$valueUserRole]['list_user_restrictions_alow_access']) ? $arrRestrictions[$valueUserRole]['list_user_restrictions_alow_access'] : array()),
            'private_folder_access' => !empty($arrRestrictions[$valueUserRole]['private_folder_access']) ? str_replace("\\\\", "/", trim($arrRestrictions[$valueUserRole]['private_folder_access'])) : '',
            'hide_paths' => implode(',', !empty($arrRestrictions[$valueUserRole]['hide_paths']) ? $arrRestrictions[$valueUserRole]['hide_paths'] : array()),
            'lock_files' => implode(',', !empty($arrRestrictions[$valueUserRole]['lock_files']) ? $arrRestrictions[$valueUserRole]['lock_files'] : array()),
            'can_upload_mime' => implode(',', !empty($arrRestrictions[$valueUserRole]['can_upload_mime']) ? $arrRestrictions[$valueUserRole]['can_upload_mime'] : array())
        );
        wp_send_json_success($dataArrRoleRestrictions);
        wp_die();
    }

}