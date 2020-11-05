<div class="wcpt-toggle-options" wcpt-model-key="pro_license">
  <div class="wcpt-editor-light-heading wcpt-toggle-label">
    PRO License <?php echo wcpt_icon('chevron-down'); ?>

    <span class="wcpt-license-feedback wcpt-in-heading">
      <span class="wcpt-verifying-license-message wcpt-hide">
        <?php wcpt_icon('loader', 'wcpt-rotate'); ?>
        Verifying...
      </span>
      <span class="wcpt-response-active-elsewhere wcpt-hide">
        <?php wcpt_icon('x'); ?>
        Error!
      </span>
      <span class="wcpt-response-invalid-key wcpt-hide">
        <?php wcpt_icon('x'); ?>
        Error!
      </span>
      <span class="wcpt-response-invalid-response wcpt-hide">
        <?php wcpt_icon('x'); ?>
        Error!
      </span>
      <span class="wcpt-response-activated <?php echo wcpt_get_license_status() == 'active' ? '' : 'wcpt-hide'; ?>">
        <?php wcpt_icon('check'); ?>
        Activated
      </span>
      <span class="wcpt-response-deactivated <?php echo wcpt_get_license_status() == 'inactive' ? '' : 'wcpt-hide'; ?>">
        <?php wcpt_icon('check'); ?>
        Deactivated
      </span>
    </span>
  </div>

  <div class="wcpt-editor-row-option">
    <label>
      License key
    </label>
    <input type="text" wcpt-model-key="key" class="wcpt-license-key" />

    <label class="wcpt-license-feedback">
      <span class="wcpt-verifying-license-message wcpt-hide">
        <?php wcpt_icon('loader', 'wcpt-rotate'); ?>
        Connecting to WCPT server, please wait for response...
      </span>
      <span class="wcpt-response-active-elsewhere wcpt-hide">
        <?php wcpt_icon('x'); ?>
        Sorry! This license key could not be activated here as it is still active on another site.
      </span>
      <span class="wcpt-response-invalid-key wcpt-hide">
        <?php wcpt_icon('x'); ?>
        Error! This license key is not valid. Please check your purchase email.
      </span>
      <span class="wcpt-response-invalid-response wcpt-hide">
        <?php wcpt_icon('x'); ?>
        Error! No valid response received. Try later or contact plugin support.
      </span>
      <span class="wcpt-response-activated wcpt-hide">
        <?php wcpt_icon('check'); ?>
        Congrats! This license is now activated on your site.
      </span>
      <span class="wcpt-response-deactivated wcpt-hide">
        <?php wcpt_icon('check'); ?>
        Done! This license has been deactivated on your site.
      </span>
    </label>

    <label for="" style="cursor: default;">
      <button
        class="wcpt-button wcpt-activate-license"
        data-wcpt-purpose="activate"
        data-wcpt-nonce="<?php echo wp_create_nonce( "wcpt" ); ?>"
        <?php echo wcpt_get_license_status() == 'active' ? 'disabled' : ''; ?>
      >
        Activate
      </button>
      <button
        class="wcpt-button wcpt-deactivate-license"
        data-wcpt-purpose="deactivate"
        data-wcpt-nonce="<?php echo wp_create_nonce( "wcpt" ); ?>"
        <?php echo wcpt_get_license_status() == 'inactive' ? 'disabled' : ''; ?>
      >
        Deactivate
      </button>
    </label>
  </div>
</div>
