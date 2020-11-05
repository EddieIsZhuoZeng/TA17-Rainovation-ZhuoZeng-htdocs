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
  <label>
    Heading
    <small>Optional. You may use [limit] for current setting's max posts per page.</small>
  </label>
  <input type="text" wcpt-model-key="heading">
</div>

<div class="wcpt-editor-row-option">
  <label class="wcpt-editor-options-heading">Options</label>
</div>

<!-- options -->
<div class="wcpt-editor-row-option">

  <!-- option rows -->
  <div
    class="wcpt-label-options-rows-wrapper"
    wcpt-model-key="dropdown_options"
  >
    <div
      class="wcpt-editor-row wcpt-editor-custom-label-setup"
      wcpt-controller="results_per_page_options"
      wcpt-model-key="[]"
      wcpt-model-key-index="0"
      wcpt-initial-data="results_per_page_option"
      wcpt-row-template="results_per_page_option"
    >

      <!-- label -->
      <div class="wcpt-editor-row-option">
        <label>Label</label>
        <input type="text" wcpt-model-key="label">
      </div>

      <!-- results -->
      <div class="wcpt-editor-row-option">
        <label>results</label>
        <input type="number" wcpt-model-key="results" />
      </div>

      <!-- corner options -->
      <?php wcpt_corner_options(); ?>

    </div>

    <button
      class="wcpt-button"
      wcpt-add-row-template="results_per_page_option"
    >
      Add an Option
    </button>

  </div>
</div>

<!-- accordion always open -->
<div class="wcpt-editor-row-option">
  <label>
    <input type="checkbox" wcpt-model-key="accordion_always_open"> Keep sidebar / modal accordion always open
  </label>
</div>

<?php include('style/filter.php'); ?>
