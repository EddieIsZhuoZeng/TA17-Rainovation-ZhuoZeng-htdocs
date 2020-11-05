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

<!-- label -->
<div class="wcpt-editor-row-option">
  <label>"Only on sale items" label</label>
  <div
    wcpt-model-key="on_sale_label"
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
