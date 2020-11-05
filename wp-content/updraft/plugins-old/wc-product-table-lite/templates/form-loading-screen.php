<script type="text/template" id="tmpl-wcpt-product-form-loading-modal">
  <div class="wcpt-modal wcpt-product-form-loading-modal" data-wcpt-product-id="{{{ data.product_id }}}">
    <div class="wcpt-modal-content">
      <div class="wcpt-close-modal">
        <!-- close 'x' icon svg -->
        <span class="wcpt-icon wcpt-icon-x wcpt-close-modal-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </span>
      </div>
      <span class="wcpt-product-form-loading-text">
        <i class="wcpt-ajax-badge">
          <!-- ajax loading icon svg -->
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader" color="#384047">
            <line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
          </svg>
        </i>

        <?php _e( 'Loading' ); ?>…
      </span>
    </div>
  </div>
</script>
