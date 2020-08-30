const njtFileManager = {
  sunriseCreateCookie(name, value, days) {
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      var expires = "; expires=" + date.toGMTString();
    } else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
  },

  sunriseReadCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  },

  //Setting tab
  activeTabSetting() {
    var pagenow = "njt-fs-filemanager-settings-tab";
    jQuery("#njt-plugin-tabs a").click(function (event) {
      jQuery("#njt-plugin-tabs a").removeClass("nav-tab-active");
      jQuery(".njt-plugin-setting").hide();
      jQuery(this).addClass("nav-tab-active");
      // Show current pane
      jQuery(".njt-plugin-setting:eq(" + jQuery(this).index() + ")").show();
      njtFileManager.sunriseCreateCookie(pagenow + "_last_tab", jQuery(this).index(), 365);
    });

    //Auto-open tab by cookies
    if (njtFileManager.sunriseReadCookie(pagenow + "_last_tab") != null)
      jQuery("#njt-plugin-tabs a:eq(" + njtFileManager.sunriseReadCookie(pagenow + "_last_tab") + ")").trigger("click");
    // Open first tab by default
    else jQuery("#njt-plugin-tabs a:eq(0)").trigger("click");
  },

  themeSelector() {
    if (jQuery('input[name = "selected-theme"]')) {
      const selectedTheme = jQuery('input[name = "selected-theme"]').val()
      jQuery('#selector-themes').val(selectedTheme);
    }

    jQuery('select#selector-themes').on('change', function () {
      const themesValue = jQuery(this).val()
      const dataThemes = {
        'action': 'selector_themes',
        'themesValue': themesValue,
        'nonce': wpData.nonce,
      }
      jQuery.post(
        wpData.admin_ajax,
        dataThemes,
        function (response) {
          jQuery('link#themes-selector-css').attr('href', response.data)
        });
    });
  },

  actionSettingFormSubmit() {
    jQuery('.njt-settings-form-submit').on('click', function () {
      const arraylistUserAccess = [];
      jQuery('.fm-list-user-item').each(function () {
        if (jQuery(this).is(":checked")) {
          arraylistUserAccess.push(jQuery(this).val());
        }
      });
      arraylistUserAccess.push('administrator')
      jQuery("#list_user_alow_access").val(arraylistUserAccess)
    })
  },

  userHasApproved() {
    const arrayUserHasApproved = jQuery('#list_user_has_approved').val() ? jQuery('#list_user_has_approved').val().split(",") : []
    for (itemUserHasApproved of arrayUserHasApproved) {
      if (itemUserHasApproved != 'administrator') {
        jQuery('input[name = ' + itemUserHasApproved + ']').prop('checked', true);
      }
    }
  },

  actionSubmitRoleRestrictionst() {
    jQuery('#njt-form-user-role-restrictionst').on('click', function () {
      const arrayUserRestrictionsAccess = [];
      if (!jQuery('.njt-fs-list-user-restrictions').val()) {
        alert('Please select a User Role at Setings tab to use this option.')
        return false;
      }
      jQuery('.fm-list-user-restrictions-item').each(function () {
        if (jQuery(this).is(":checked")) {
          arrayUserRestrictionsAccess.push(jQuery(this).val());
        }
      });
      jQuery("#list_user_restrictions_alow_access").val(arrayUserRestrictionsAccess)

      if (jQuery("#hide_paths").val().trim().length > 0) {
        const valueHidePaths = jQuery("#hide_paths").val().trim().split("|")
        const newValueHidePaths = []
        for (const itemHidePath of valueHidePaths) {
          if (itemHidePath.trim().length > 0) {
            newValueHidePaths.push(itemHidePath.trim())
          }
        }
        jQuery("#hide_paths").val(newValueHidePaths.join("|"))
      }

      if (jQuery("#lock_files").val().trim().length > 0) {
        const valueLockFiles = jQuery("#lock_files").val().trim().split("|")
        const newValueLockFiles = []
        for (const itemLockFile of valueLockFiles) {
          if (itemLockFile.trim().length > 0) {
            newValueLockFiles.push(itemLockFile.trim())
          }
        }
        jQuery("#lock_files").val(newValueLockFiles.join("|"))
      }

    })
  },

  restrictionsHasApproved() {
    const arrayRestrictionsHasApproved = jQuery('#list_restrictions_has_approved').val() ? jQuery('#list_restrictions_has_approved').val().split(",") : []
    for (itemRestrictionsHasApproved of arrayRestrictionsHasApproved) {
      jQuery('input[name = ' + itemRestrictionsHasApproved + ']').prop('checked', true);
    }
  },

  ajaxRoleRestrictions() {
    jQuery('select.njt-fs-list-user-restrictions').on('change', function () {
      const valueUserRole = jQuery(this).val()
      const dataUserRole = {
        'action': 'get_role_restrictions',
        'valueUserRole': valueUserRole,
        'nonce': wpData.nonce,
      }
      jQuery.post(
        wpData.admin_ajax,
        dataUserRole,
        function (response) {
          const resRestrictionsHasApproved = response.data.disable_operations ? response.data.disable_operations.split(",") : []
          const resPrivateFolderAccess = response.data.private_folder_access ? response.data.private_folder_access : ''
          const resHidePaths = response.data.hide_paths ? response.data.hide_paths.replace(/[,]+/g, ' | ') : '';
          const resLockFiles = response.data.lock_files ? response.data.lock_files.replace(/[,]+/g, ' | ') : '';
          const resCanUploadMime = response.data.can_upload_mime ? response.data.can_upload_mime : '';
          jQuery('input.fm-list-user-restrictions-item').prop('checked', false);
          for (itemRestrictionsHasApproved of resRestrictionsHasApproved) {
            jQuery('input[name = ' + itemRestrictionsHasApproved + ']').prop('checked', true);
          }
          // Set value for textarea[name='private_folder_access']
          jQuery('textarea#private_folder_access').val(resPrivateFolderAccess)
          // Set value for textarea[name='hide_paths']
          jQuery('textarea#hide_paths').val(resHidePaths)
          // Set value for textarea[name='lock_files']
          jQuery('textarea#lock_files').val(resLockFiles)
          // Set value for textarea[name='can_upload_mime']
          jQuery('textarea#can_upload_mime').val(resCanUploadMime)
        });
    });
  },

  setValueUploadMime() {
    jQuery('.njt-mime-type').on('click', function () {
      let arrCanUploadMime = []
      if (jQuery('textarea#can_upload_mime').val().trim().length > 0) {
        arrCanUploadMime = jQuery('textarea#can_upload_mime').val().split(",")
      }
      const objMimeTypes = {
        text: ['txt', 'htm', 'html', 'php', 'css', 'js', 'json', 'xml'],
        images: ['png', 'jpe', 'jpeg', 'jpg', 'gif', 'bmp', 'ico', 'tiff', 'tif', 'svg', 'svgz'],
        archives: ['zip', 'rar', 'exe', 'msi', 'cab', 'tar', 'gz', 'bz2', '7z'],
        audio: ['mp3', 'mp4a', 'mpega', 'mpga', 'aac', 'm3u', 'mpa', 'wav', 'wma'],
        video: ['flv', 'qt', 'mov', 'avi', 'mp4v', 'mpegv', 'mpg', 'swf', 'wmv', 'mpav'],
        adobe: ['pdf', 'psd', 'ai', 'eps', 'ps'],
        office: ['doc', 'rtf', 'xls', 'ppt', 'odt', 'ods', 'pptx', 'docx', 'xlsx', 'dotx', 'xltx', 'potx', 'ppsx', 'sldx']
      }
      if (jQuery(this).val() == 'clearall') {
        arrCanUploadMime.length = 0
      } else {
        const mineChose = jQuery(this).val()
        const valMimeTypes = objMimeTypes[mineChose]
        arrMimeTypesConcat = arrCanUploadMime.concat(valMimeTypes)
        arrCanUploadMime = arrMimeTypesConcat.filter((item, index) => arrMimeTypesConcat.indexOf(item) == index)
      }

      jQuery('textarea#can_upload_mime').val(arrCanUploadMime.toString())
    })
  },
  clickedCreatRootPath() {
    jQuery('.js-creat-root-path').on('click', function () {
      const valueRootPath = wpData.ABSPATH
      jQuery('textarea#private_folder_access').val(valueRootPath)
    })
  }
}

jQuery(document).ready(function () {
  if (jQuery("div").hasClass("njt-fs-file-manager")) {
    //set select value
    njtFileManager.themeSelector();
    // Start- Setting for `Select User Roles to access`
    njtFileManager.actionSettingFormSubmit();
    // Get value to prop checked for input checkbox
    njtFileManager.userHasApproved();
    //Setting tab
    njtFileManager.activeTabSetting();

    njtFileManager.actionSubmitRoleRestrictionst();
    // Get value to prop checked for input checkbox
    njtFileManager.restrictionsHasApproved();
    //Ajax change value
    njtFileManager.ajaxRoleRestrictions();
    //Set value for textarea[name="can_upload_mime"]
    njtFileManager.setValueUploadMime();
    //Creat root path default
    njtFileManager.clickedCreatRootPath();
    // End- Setting for `Select User Roles Restrictions to access`
  }
});
