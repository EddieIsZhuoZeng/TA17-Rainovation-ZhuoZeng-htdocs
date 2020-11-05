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

<!-- heading -->
<div class="wcpt-editor-row-option">
  <label>Heading</label>
  <div
    wcpt-block-editor
    wcpt-model-key="heading"
  ></div>
</div>

<div class="wcpt-editor-row-option">
  <label>"Show any" option label</label>
  <div
    wcpt-model-key="show_all_label"
    wcpt-block-editor
  >
</div>

<!-- range options -->
<div class="wcpt-editor-row-option">
  <label class="wcpt-editor-options-heading">Filter options</label>
</div>

<div
  class="wcpt-editor-row-option wcpt-range-options-rows-wrapper"
  wcpt-model-key="rating_options"
>
  <div
    class="wcpt-editor-row wcpt-editor-range-options-row"
    wcpt-controller="rating_options"
    wcpt-model-key="[]"
    wcpt-model-key-index="0"
    wcpt-row-template="rating_filter_row"
    wcpt-initial-data="rating_filter_row"
  >
    <input type="hidden" wcpt-model-key="value">
    <label>Rating: <span wcpt-content-template="value"></span> star</label>

    <!-- label -->
    <div class="wcpt-tabs">

      <!-- triggers -->
      <div class="wcpt-tab-triggers">
        <div class="wcpt-tab-trigger">
          Label
        </div>
        <div class="wcpt-tab-trigger" wcpt-can-disable>
          Custom clear label
        </div>
      </div>

      <!-- content: label -->
      <div class="wcpt-tab-content">
        <div class="wcpt-editor-row-option">
          <input type="text" wcpt-model-key="label" placeholder="Label">
        </div>
      </div>

      <!-- content: clear fitler label -->
      <div class="wcpt-tab-content">
        <div class="wcpt-editor-row-option">
          <input type="text" wcpt-model-key="clear_label" placeholder="[filter] : [option]">
        </div>
      </div>

    </div>

    <label>
      <input type="checkbox" wcpt-model-key="enabled" /> Enable this options
    </label>

  </div>

</div>

<!-- accordion always open -->
<div class="wcpt-editor-row-option">
  <label>
    <input type="checkbox" wcpt-model-key="accordion_always_open"> Keep sidebar / modal accordion always open
  </label>
</div>

<div class="wcpt-editor-row-option" wcpt-model-key="style">

  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion">

    <span class="wcpt-toggle-label">
      Style for star icons
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <div class="wcpt-editor-row-option">

      <div class="wcpt-editor-row-option" wcpt-model-key="[id] .wcpt-star:not(.wcpt-star-empty) > svg:first-child">
        <!-- font-color -->
        <div class="wcpt-editor-row-option">
          <label>Highlight color</label>
          <input type="text" wcpt-model-key="color" class="wcpt-color-picker" >
        </div>
      </div>

      <div class="wcpt-editor-row-option" wcpt-model-key="[id] .wcpt-star:not(.wcpt-star-full) > svg:last-child">
        <!-- font-color -->
        <div class="wcpt-editor-row-option">
          <label>Background color</label>
          <input type="text" wcpt-model-key="color" class="wcpt-color-picker" >
        </div>
      </div>

    </div>

  </div>

</div>

<?php include('style/filter.php'); ?>
