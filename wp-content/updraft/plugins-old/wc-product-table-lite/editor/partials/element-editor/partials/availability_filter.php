<?php
if( get_option('woocommerce_hide_out_of_stock_items', 'no') == 'yes' ){
  ?>
  <span class="wcpt-notice" style="margin: 30px 0 0;">
    To enable this filter, please uncheck 'Out of stock visibility'
    <a href="<?php echo get_admin_url(); ?>admin.php?page=wc-settings&tab=products&section=inventory" target="_blank">here</a>. Otherwise out of stock items will remain hidden regardless of filter.
  </span>
  <?php
  return;
}
?>
<!-- heading -->
<div class="wcpt-editor-row-option">
  <label>Heading</label>
  <div
    wcpt-block-editor
    wcpt-model-key="heading"
  ></div>
</div>

<!-- display type -->
<div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="position"
  wcpt-condition-val="header"
>
  <label>Display type</label>
  <select wcpt-model-key="display_type">
    <option value="dropdown">Dropdown</option>
    <option value="row">Row</option>
  </select>
</div>

<!-- hide label -->
<div class="wcpt-editor-row-option">
  <label>"Hide out of stock items" label</label>
  <div
    wcpt-model-key="hide_label"
    wcpt-block-editor
    wcpt-be-add-row="0"
  ></div>
</div>

<!-- accordion always open -->
<div class="wcpt-editor-row-option">
  <label>
    <input type="checkbox" wcpt-model-key="accordion_always_open"> Keep sidebar / modal accordion always open
  </label>
</div>

<?php include('style/filter.php'); ?>
