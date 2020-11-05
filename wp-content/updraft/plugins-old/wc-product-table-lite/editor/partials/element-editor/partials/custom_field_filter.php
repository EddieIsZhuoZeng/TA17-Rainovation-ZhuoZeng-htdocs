<!-- custom field -->
<div class="wcpt-editor-row-option">
  <label>Custom field name</label>
  <input type="text" wcpt-model-key="field_name">
</div>

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

<!-- compare -->
<div class="wcpt-editor-row-option">
  <label>Comparison type</label>
  <select wcpt-model-key="compare">
    <option value="IN">Exact match</option>
    <option value="BETWEEN">Within range</option>
  </select>
</div>

<!-- type -->
<div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="compare"
  wcpt-condition-val="BETWEEN"
>
  <label>Field value type</label>
  <select wcpt-model-key="field_type">
    <option value="NUMERIC">Numeric</option>
    <option value="DECIMAL">Decimal</option>
    <option value="DATE">Date</option>
    <option value="TIME">Time</option>
    <option value="DATETIME">Datetime</option>
    <option value="CHAR">Char</option>
  </select>
</div>

<!-- multiple selections permission -->
<div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="compare"
  wcpt-condition-val="IN"
>
  <label>
    <input type="checkbox" wcpt-model-key="single" />
    Only allow one option to be selected
  </label>
</div>

<!-- "Show all" label -->
<div class="wcpt-editor-row-option"
  wcpt-panel-condition="cf_show_all_label"
  wcpt-condition-prop="single"
  wcpt-condition-val="true"
>
  <label>
    "Show all" option label
  </label>
  <div
    wcpt-model-key="show_all_label"
    wcpt-block-editor
  >
  </div>
</div>

<!-- options -->
<div class="wcpt-editor-row-option">
  <label class="wcpt-editor-options-heading">
    Filter options
    <span
      wcpt-panel-condition="prop"
      wcpt-condition-prop="compare"
      wcpt-condition-val="IN"
    >
      (exact match type)
    </span>
    <span
      wcpt-panel-condition="prop"
      wcpt-condition-prop="compare"
      wcpt-condition-val="BETWEEN"
    >
      (range type)
    </span>
  </label>
</div>

<!-- exact options -->
<div class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="compare"
  wcpt-condition-val="IN"
>

  <div
    class="wcpt-label-options-rows-wrapper wcpt-sortable"
    wcpt-model-key="manual_options"
  >
    <div
      class="wcpt-editor-row wcpt-editor-custom-label-setup"
      wcpt-controller="manual_options"
      wcpt-model-key="[]"
      wcpt-model-key-index="0"
      wcpt-row-template="cf_manual_option_2"
      wcpt-initial-data="custom_field_filter_manual_option"
    >

      <!-- value -->
      <div class="wcpt-editor-row-option">
        <label>Custom field value</label>
        <input type="text" wcpt-model-key="value" />
      </div>

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
            <div
              wcpt-model-key="label"
              class="wcpt-term-relabel-editor"
              wcpt-block-editor=""
              wcpt-be-add-row="1"
            ></div>
          </div>
        </div>

        <!-- content: clear fitler label -->
        <div class="wcpt-tab-content">
          <div class="wcpt-editor-row-option">
            <input type="text" wcpt-model-key="clear_label" placeholder="[filter] : [option]">
          </div>
        </div>

      </div>


      <!-- corner options -->
      <?php wcpt_corner_options(); ?>

    </div>

    <button
      class="wcpt-button"
      wcpt-add-row-template="cf_manual_option_2"
    >
      Add an Option
    </button>

  </div>

</div>

<!-- range options -->
<div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="compare"
  wcpt-condition-val="BETWEEN"
>
  <div
    class="wcpt-label-options-rows-wrapper wcpt-sortable"
    wcpt-model-key="range_options"
  >
    <div
      class="wcpt-editor-row wcpt-editor-custom-label-setup"
      wcpt-controller="range_options"
      wcpt-model-key="[]"
      wcpt-model-key-index="0"
      wcpt-row-template="cf_range_option_2"
      wcpt-initial-data="custom_field_filter_range_option"
    >

      <!-- min value -->
      <div class="wcpt-editor-row-option">
        <label>Min value</label>
        <input type="number" wcpt-model-key="min_value" />
      </div>

      <!-- max value -->
      <div class="wcpt-editor-row-option">
        <label>Max value</label>
        <input type="number" wcpt-model-key="max_value" />
      </div>

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
            <div
              wcpt-model-key="label"
              class="wcpt-term-relabel-editor"
              wcpt-block-editor=""
              wcpt-be-add-row="1"
            ></div>
          </div>
        </div>

        <!-- content: clear fitler label -->
        <div class="wcpt-tab-content">
          <div class="wcpt-editor-row-option">
            <input type="text" wcpt-model-key="clear_label" placeholder="[filter] : [option]">
          </div>
        </div>

      </div>

      <!-- corner options -->
      <?php wcpt_corner_options(); ?>

    </div>

    <button
      class="wcpt-button"
      wcpt-add-row-template="cf_range_option_2"
    >
      Add an Option
    </button>

  </div>

  <!-- Custom 'Min-Max' enabled -->
  <div class="wcpt-editor-row-option">
    <label>
      <input type="checkbox" wcpt-model-key="custom_min_max_enabled">
      Enable the custom 'Min - Max' input option
    </label>
  </div>

  <div
    class="wcpt-editor-row-option"
    wcpt-panel-condition="prop"
    wcpt-condition-prop="custom_min_max_enabled"
    wcpt-condition-val="true"
  >

    <!-- 'Min' -->
    <div class="wcpt-editor-row-option"  >
      <label>Label for 'Min' (text inside min input box)</label>
      <input type="text" wcpt-model-key="min_label">
    </div>

    <!-- 'Max' -->
    <div class="wcpt-editor-row-option">
      <label>Label for 'Max' (text inside max input box)</label>
      <input type="text" wcpt-model-key="max_label">
    </div>

    <!-- 'to' -->
    <div class="wcpt-editor-row-option">
      <label>Label for 'to' (text between min & max input boxes)</label>
      <input type="text" wcpt-model-key="to_label">
    </div>


    <!-- 'Go' -->
    <div class="wcpt-editor-row-option">
      <label>Label for 'Go' (submit button after min & max input boxes)</label>
      <input type="text" wcpt-model-key="go_label">
    </div>

    <!-- min_max_clear_label -->
    <div class="wcpt-editor-row-option">
      <label>Custom clear label</label>
      <input type="text" wcpt-model-key="min_max_clear_label" placeholder="[filter] : [min] - [max]">
    </div>

    <!-- no_min_clear_label -->
    <div class="wcpt-editor-row-option">
      <label>Custom clear label - no minimum value</label>
      <input type="text" wcpt-model-key="no_min_clear_label" placeholder="[filter] : Upto [max]">
    </div>

    <!-- no_max_clear_label -->
    <div class="wcpt-editor-row-option">
      <label>Custom clear label - no maximum value</label>
      <input type="text" wcpt-model-key="no_max_clear_label" placeholder="[filter] : [min]+">
    </div>

  </div>

</div>

<!-- accordion always open -->
<div class="wcpt-editor-row-option">
  <label>
    <input type="checkbox" wcpt-model-key="accordion_always_open"> Keep sidebar / modal accordion always open
  </label>
</div>

<?php include('style/filter.php'); ?>
