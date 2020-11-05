<!-- content -->
<div class="wcpt-editor-row-option">
  <label>
    Content
    <small>Placeholders: [title] [url]</small>
  </label>
  <div
    wcpt-block-editor
    wcpt-be-add-element-partial="add-product-link-element"
    wcpt-model-key="template"
  ></div>
</div>

<!-- target -->
<div class="wcpt-editor-row-option">
  <label>Open on</label>
  <select wcpt-model-key="target">
    <option value="_self">Same page</option>
    <option value="_blank">New page</option>
  </select>
</div>

<!-- suffix -->
<div class="wcpt-editor-row-option">
  <label>
    Link Suffix
  </label>
  <input type="text" wcpt-model-key="suffix" />
</div>

<?php
  include( 'style/common.php' );
  include( 'condition/outer.php' );
?>
