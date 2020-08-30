<?php
defined('ABSPATH') || exit;
$viewListOperations = NJT_FS_BN_PLUGIN_PATH . 'views/pages/html-filemanager-list-operations.php';
$listUserApproved = !empty($this->options['njt_fs_file_manager_settings']['list_user_alow_access']) ? $this->options['njt_fs_file_manager_settings']['list_user_alow_access'] : array();

if (isset($_POST) && !empty($_POST) && !empty($_POST['njt-form-user-role-restrictionst'])) {
  if (!wp_verify_nonce($_POST['njt-fs-user-restrictions-security-token'], 'njt-fs-user-restrictions-security-token')) {
      wp_die();
  }
  if(!empty($_POST['njt-fs-list-user-restrictions'])) {

    $userRoleRestrictedSubmited = filter_var($_POST['njt-fs-list-user-restrictions'], FILTER_SANITIZE_STRING) ? sanitize_text_field($_POST['njt-fs-list-user-restrictions']) : '';
    
    if (empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'])) {
      $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'] = array();
    }

    //Save data list User Restrictions alow access
    $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$_POST['njt-fs-list-user-restrictions']]['list_user_restrictions_alow_access'] = 
      filter_var($_POST['list_user_restrictions_alow_access'], FILTER_SANITIZE_STRING) ?
      explode(',', $_POST['list_user_restrictions_alow_access']) : array();
    //Seperate or private folder access
    $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$_POST['njt-fs-list-user-restrictions']]['private_folder_access'] =
      filter_var($_POST['private_folder_access'], FILTER_SANITIZE_STRING) ?
      str_replace("\\\\", "/", trim($_POST['private_folder_access'])) : '';
    //Save data Enter Folder or File Paths That You want to Hide
    $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$_POST['njt-fs-list-user-restrictions']]['hide_paths'] = 
      filter_var($_POST['hide_paths'], FILTER_SANITIZE_STRING) ?
      explode('|', preg_replace('/\s+/', '', $_POST['hide_paths'])) : array();
    //Save data Enter file extensions which you want to Lock
    $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$_POST['njt-fs-list-user-restrictions']]['lock_files'] =
      filter_var($_POST['lock_files'], FILTER_SANITIZE_STRING) ?
      explode('|', preg_replace('/\s+/', '', $_POST['lock_files'])) : array();
    //Enter file extensions which can be uploaded
    $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'][$_POST['njt-fs-list-user-restrictions']]['can_upload_mime'] =
      filter_var($_POST['can_upload_mime'], FILTER_SANITIZE_STRING) ?
      explode(',', preg_replace('/\s+/', '', $_POST['can_upload_mime'])) : array();
  }
}

$arrRestrictions = !empty($this->options['njt_fs_file_manager_settings']['list_user_role_restrictions']) ? $this->options['njt_fs_file_manager_settings']['list_user_role_restrictions'] : array();
if (count($arrRestrictions) > 0) {
  $firstKeyRestrictions = !empty($userRoleRestrictedSubmited) ? $userRoleRestrictedSubmited : array_keys($arrRestrictions)[0];
} else {
  $firstKeyRestrictions = '';
}
?>

<form action="" class="njt-plugin-setting form-user-role-restrictions" method="POST">
  <!-- creat token -->
  <input type='hidden' name='njt-fs-user-restrictions-security-token'
    value='<?php echo wp_create_nonce('njt-fs-user-restrictions-security-token'); ?>'>
  <table class="form-table">
    <tr>
      <th><?php _e("If User Role is", NJT_FS_BN_DOMAIN); ?></th>
      <td>
        <div>
          <select class="njt-fs-list-user-restrictions njt-settting-width-select" name="njt-fs-list-user-restrictions">
            <?php
              if ($listUserApproved && count($listUserApproved) != 1 && $listUserApproved[0] != 'administrator') {
              foreach ( $wp_roles->roles as $key=>$value ):
                if ($key != 'administrator' && in_array($key,$listUserApproved) ) {?>
            <option value="<?php echo $key; ?>"
              <?php echo(!empty($firstKeyRestrictions) && $firstKeyRestrictions == $key ) ? 'selected="selected"' : '';?>>
              <?php echo $value['name']; ?>
            </option>
            <?php 
                }
              endforeach;}
              else {
             ?>
            <option selected disabled hidden><?php _e("Nothing to choose", NJT_FS_BN_DOMAIN); ?></option>
            <?php }?>
          </select>
          <?php 
            if(empty($listUserApproved) || $listUserApproved && count($listUserApproved) == 1 && $listUserApproved[0] == 'administrator') {
              ?>
          <p class="description njt-text-error njt-settting-width">
            <?php _e("Please select a User Role at Setings tab to use this option.", NJT_FS_BN_DOMAIN); ?>
          </p>
          <?php
            }
          ?>
        </div>
      </td>
    </tr>
    <tr>
      <th><?php _e("Disable command", NJT_FS_BN_DOMAIN); ?></th>
      <td>
        <div style="line-height: 2" class="njt-settting-width njt-fs-list-col4">
          <?php include_once $viewListOperations; ?>
          <!-- Value to submit data -->
          <input type="hidden" name="list_user_restrictions_alow_access" id="list_user_restrictions_alow_access">
          <!-- Data saved after submit -->
          <input type="hidden" name="list_restrictions_has_approved" id="list_restrictions_has_approved"
            value="<?php echo implode(",", !empty($arrRestrictions[$firstKeyRestrictions]['list_user_restrictions_alow_access']) ? $arrRestrictions[$firstKeyRestrictions]['list_user_restrictions_alow_access'] : array());?>">
        </div>
      </td>
    </tr>
    <tr>
      <th><?php _e("Root Path for this User Role", NJT_FS_BN_DOMAIN); ?></th>
      <td>
        <div>
          <div class="njt-settting-width">
            <button type="button"
              class="njt-fs-button js-creat-root-path"><?php _e("Insert Root Path", NJT_FS_BN_DOMAIN); ?></button>
          </div>
          <textarea name="private_folder_access" id="private_folder_access" placeholder="ex: <?php echo (str_replace("\\", "/", ABSPATH)."wp-content");?>"
            class="njt-settting-width"><?php echo (!empty($arrRestrictions[$firstKeyRestrictions]['private_folder_access']) ? $arrRestrictions[$firstKeyRestrictions]['private_folder_access'] : '');?></textarea>
          <div>
            <p class="description njt-settting-width">
              <?php _e("Default path is: "."<code>". str_replace("\\", "/", ABSPATH)."</code>", NJT_FS_BN_DOMAIN); ?>
            </p>
            <p class="description njt-settting-width">
            <?php _e("Eg: If you want to set root path access is ". "<strong>wp-content</strong>". " folder. Just enter ", NJT_FS_BN_DOMAIN); ?>
              <?php echo (str_replace("\\", "/", ABSPATH));?>wp-content
            </p>
          </div>
        </div>
      </td>
    </tr>
    <tr>
      <th> <?php _e("Enter folder or file paths that you want to Hide", NJT_FS_BN_DOMAIN); ?></th>
      <td>
        <div>
          <textarea name="hide_paths" id="hide_paths"
            class="njt-settting-width"><?php echo implode(" | ", !empty($arrRestrictions[$firstKeyRestrictions]['hide_paths']) ? $arrRestrictions[$firstKeyRestrictions]['hide_paths'] : array());?></textarea>
          <p class="description njt-settting-width">
            <?php _e("Multiple separated by vertical bar (|). Eg: themes/twentytwenty | themes/avada.", NJT_FS_BN_DOMAIN); ?>
          </p>
        </div>
      </td>
    </tr>
    <tr>
      <th><?php _e("Enter file extensions which you want to Lock", NJT_FS_BN_DOMAIN); ?></th>
      <td>
        <div>
          <textarea name="lock_files" id="lock_files"
            class="njt-settting-width"><?php echo implode(" | ", !empty($arrRestrictions[$firstKeyRestrictions]['lock_files']) ? $arrRestrictions[$firstKeyRestrictions]['lock_files'] : array());?></textarea>
          <p class="description njt-settting-width">
            <?php _e("Multiple separated by vertical bar (|). Eg: .php | .png | .css", NJT_FS_BN_DOMAIN); ?>
          </p>
        </div>
      </td>
    </tr>
    <tr>
      <th><?php _e("Enter file extensions which user can be Uploaded", NJT_FS_BN_DOMAIN); ?></th>
      <td>
        <div>
          <div>
            <div class="njt-btn-group njt-settting-width">
              <button type="button" class="njt-mime-type njt-fs-button"
                value="text"><?php _e("text", NJT_FS_BN_DOMAIN); ?></button>
              <button type="button" class="njt-mime-type njt-fs-button"
                value="office"><?php _e("office", NJT_FS_BN_DOMAIN); ?></button>
              <button type="button" class="njt-mime-type njt-fs-button"
                value="images"><?php _e("images", NJT_FS_BN_DOMAIN); ?></button>
              <button type="button" class="njt-mime-type njt-fs-button"
                value="video"><?php _e("video", NJT_FS_BN_DOMAIN); ?></button>
              <button type="button" class="njt-mime-type njt-fs-button"
                value="audio"><?php _e("audio", NJT_FS_BN_DOMAIN); ?></button>
              <button type="button" class="njt-mime-type njt-fs-button"
                value="archives"><?php _e("archives", NJT_FS_BN_DOMAIN); ?></button>
              <button type="button" class="njt-mime-type njt-fs-button"
                value="adobe"><?php _e("adobe", NJT_FS_BN_DOMAIN); ?></button>
              <button type="button" class="njt-mime-type njt-fs-button"
                value="clearall"><?php _e("clear all", NJT_FS_BN_DOMAIN); ?></button>
            </div>
          </div>
          <textarea name="can_upload_mime" id="can_upload_mime"
            class="njt-settting-width"><?php echo implode(",", !empty($arrRestrictions[$firstKeyRestrictions]['can_upload_mime']) ? $arrRestrictions[$firstKeyRestrictions]['can_upload_mime'] : array());?></textarea>
          <p class="description njt-settting-width">
            <?php _e("Multiple separated by comma. If left empty, this means user can't upload any files.", NJT_FS_BN_DOMAIN); ?>
          </p>
        </div>
      </td>
    </tr>

    <!-- button submit -->
    <tr>
      <td></td>
      <td>
        <p class="submit">
          <input type="submit" name="njt-form-user-role-restrictionst" id="njt-form-user-role-restrictionst"
            class="button button-primary" value="Save Changes">
        </p>
      </td>
    </tr>
  </table>
</form>