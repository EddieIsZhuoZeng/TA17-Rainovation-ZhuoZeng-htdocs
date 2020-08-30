<?php
defined('ABSPATH') || exit;
$lang = !empty( $this->options['njt_fs_file_manager_settings']['fm_locale']) ? $this->options['njt_fs_file_manager_settings']['fm_locale'] : ''; 
?>
<div class="njt-fs-file-manager">
  <div class="njt-fs-select-theme">
    <div class="njt-fs-wrap njt-fs-mr0">
      <h1 class="wp-heading-inline njt-fs-pd0"><?php _e("Filester - WordPress File Manager Pro", NJT_FS_BN_DOMAIN); ?></h1>
    </div>
    <div class="select-theme-content">
      <?php    
      $selectedTheme = get_option('njt_fs_selector_themes') && get_option('njt_fs_selector_themes')[$this->userRole]['themesValue'] ? get_option('njt_fs_selector_themes')[$this->userRole]['themesValue'] : null;
    ?>
      <div class="njt-fs-wrap njt-fs-mr0">
        <h3 class="wp-heading-inline select-theme-title"><?php _e("Select theme:", NJT_FS_BN_DOMAIN); ?></h3>
      </div>
      <select name="selector-themes" id="selector-themes">
        <option value="Default"><?php _e("Default Elfinder", NJT_FS_BN_DOMAIN); ?></option>
        <option value="dark-slim"><?php _e("Dark Slim", NJT_FS_BN_DOMAIN); ?></option>
        <option value="Material"><?php _e("Material", NJT_FS_BN_DOMAIN); ?></option>
        <option value="Material-Gray"><?php _e("Material Gray", NJT_FS_BN_DOMAIN); ?></option>
        <option value="Material-Light"><?php _e("Material Light", NJT_FS_BN_DOMAIN); ?></option>
        <option value="windows-10"><?php _e("Windows 10", NJT_FS_BN_DOMAIN); ?></option>
      </select>
      <input type="hidden" name="selected-theme" value="<?php echo ($selectedTheme) ?>">

    </div>
  </div>

  <div class="clear"></div>
  <div id="njt-fs-file-manager">
  </div>
</div>


<script>
jQuery(document).ready(function() {
  jQuery('#njt-fs-file-manager').elfinder({
    url: ajaxurl,
    handlers : {
      dblclick : function(event, elfinderInstance) {
        event.preventDefault();
        elfinderInstance.exec('getfile')
        .done(function() { 
          try {
            elfinderInstance.exec('edit'); 
          } catch (e) {
            elfinderInstance.exec('quicklook'); 
          }
        })
        .fail(function() { elfinderInstance.exec('open');});
      }
    },
    getFileCallback : function(files, fm) {
      return false;
    }, 
    contextmenu: {
      // current directory file menu
      files: ['getfile', '|', 'open', 'opennew', 'download', 'opendir', 'quicklook', 'email', '|', 'upload',
        'mkdir', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', 'empty', 'hide', '|', 'rename', 'edit',
        'resize', '|', 'archive', 'extract', '|', 'selectall', 'selectinvert', '|', 'places', 'info', 'chmod',
        'netunmount'
      ],
      // navbarfolder menu
      navbar: ['open', 'opennew', 'download', '|', 'upload', 'mkdir', '|', 'copy', 'cut', 'paste', 'duplicate',
        '|', 'rm', 'empty', 'hide', '|', 'rename', '|', 'archive', '|', 'places', 'info', 'chmod',
        'netunmount'
      ],
      // current directory menu
      cwd: ['undo', 'redo', '|', 'back', 'up', 'reload', '|', 'upload', 'mkdir', 'mkfile', 'paste', '|',
        'empty', 'hide', '|', 'view', 'sort', 'selectall', 'colwidth', '|', 'places', 'info', 'chmod',
        'netunmount',
        '|', 'fullscreen', '|'
      ],
    },
    uiOptions: {
      // toolbar configuration
      toolbar: [
        ['home', 'back', 'forward', 'up', 'reload'],
        ['netmount'],
        ['mkdir', 'mkfile', 'upload'],
        ['open', 'download', 'getfile'],
        ['undo', 'redo'],
        ['copy', 'cut', 'paste', 'rm', 'empty'],
        ['duplicate', 'rename', 'edit', 'resize', 'chmod'],
        ['selectall', 'selectnone', 'selectinvert'],
        ['quicklook', 'info'],
        ['extract', 'archive'],
        ['search'],
        ['view', 'sort'],
        ['help'],
        ['fullscreen']
      ],
      toolbarExtra: {
        // show Preference button into contextmenu of the toolbar (true / false)
        preferenceInContextmenu: false
      }
    },
    ui: ['toolbar', 'tree', 'path', 'stat'],
    customData: {
      action: 'fs_connector',
      nonce: '<?php echo wp_create_nonce( "file-manager-security-token" ); ?>'
    },
    lang: '<?php echo ($lang)?>',
    requestType: 'post',
    width: 'auto',
    height: '600',
  });
});
</script>