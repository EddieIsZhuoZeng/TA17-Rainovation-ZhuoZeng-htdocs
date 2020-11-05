<div class="wcpt-editor-row-option">
  <label class="wcpt-editor-options-heading">Property List Rows</label>
</div>

<!-- rows -->
<div
  class="wcpt-sortable wcpt-editor-row-option"
  wcpt-model-key="rows"
>
  <div
    class="wcpt-editor-row wcpt-editor-custom-label-setup"
    wcpt-controller="property_list_row"
    wcpt-model-key="[]"
    wcpt-model-key-index="0"
    wcpt-row-template="property_list_row"
    wcpt-initial-data="property_list_row"
  >

    <div class="wcpt-tabs">

      <!-- triggers -->
      <div class="wcpt-tab-triggers">
        <div class="wcpt-tab-trigger">
          Template
        </div>
        <div class="wcpt-tab-trigger">
          Condition
        </div>
      </div>

      <!-- content: template -->
      <div class="wcpt-tab-content">

        <div class="wcpt-editor-row-option">
          <label>Property name</label>
          <div
            wcpt-model-key="property_name"
            wcpt-block-editor=""
            wcpt-be-add-row="0"
          ></div>
        </div>

        <div class="wcpt-editor-row-option">
          <label>Property value</label>
          <div
            wcpt-model-key="property_value"
            wcpt-block-editor=""
            wcpt-be-add-row="0"
            wcpt-be-add-element-partial="add-property-value-element"
          ></div>
        </div>

      </div>

      <!-- content: condition -->
      <div class="wcpt-tab-content">
        <?php include( 'condition/inner.php' ); ?>
      </div>

    </div>

    <!-- corner options -->
    <?php wcpt_corner_options(); ?>

  </div>

  <button
    class="wcpt-button"
    wcpt-add-row-template="property_list_row"
  >
    Add a Row
  </button>

</div>

<!-- toggle enabled -->
<!-- <div class="wcpt-editor-row-option">
  <label>
    <input type="checkbox" wcpt-model-key="toggle_enabled">
    Enable Toggle (Show more / Show less)
  </label>
</div> -->

<!-- <div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="toggle_enabled"
  wcpt-condition-val="true"
> -->

  <!-- initial reveal -->
  <div class="wcpt-editor-row-option">
    <label>
      Number of rows to initially reveal
    </label>
    <input type="number" wcpt-model-key="initial_reveal" min="0">
  </div>

  <!-- show more label -->
  <div class="wcpt-editor-row-option">
    <label>Toggle 'Show more' label</label>
    <input type="text" wcpt-model-key="show_more_label" />
    <!-- <div
      wcpt-model-key="show_more_label"
      wcpt-block-editor
      wcpt-be-add-new-row="0"
    ></div> -->
  </div>

  <!-- show less label -->
  <div class="wcpt-editor-row-option">
    <label>Toggle 'Show less' label</label>
    <input type="text" wcpt-model-key="show_less_label" />
    <!-- <div
      wcpt-model-key="show_less_label"
      wcpt-block-editor
      wcpt-be-add-new-row="0"
    ></div> -->
  </div>

<!-- </div> -->


<!-- style -->
<div class="wcpt-editor-row-option" wcpt-model-key="style">

  <!-- prop name -->
  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id] .wcpt-property-name">

    <span class="wcpt-toggle-label">
      Style for Property Names
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <!-- width -->
    <div class="wcpt-editor-row-option">
      <label>Width</label>
      <input type="text" wcpt-model-key="width" />
    </div>

    <!-- font-size -->
    <div class="wcpt-editor-row-option">
      <label>Font size</label>
      <input type="text" wcpt-model-key="font-size" />
    </div>

    <!-- font color -->
    <div class="wcpt-editor-row-option">
      <label>Font color</label>
      <input type="text" wcpt-model-key="color" placeholder="#000" class="wcpt-color-picker">
    </div>

    <!-- font-weight -->
    <div class="wcpt-editor-row-option">
      <label>Font weight</label>
      <select wcpt-model-key="font-weight">
        <option value="normal">Normal</option>
        <option value="bold">Bold</option>
        <option value="200">Light</option>
      </select>
    </div>

  </div>

  <!-- value -->
  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id] .wcpt-property-value">

    <span class="wcpt-toggle-label">
      Style for Property Values
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <!-- width -->
    <div class="wcpt-editor-row-option">
      <label>Width</label>
      <input type="text" wcpt-model-key="width" />
    </div>

    <!-- font-size -->
    <div class="wcpt-editor-row-option">
      <label>Font size</label>
      <input type="text" wcpt-model-key="font-size" />
    </div>

    <!-- font color -->
    <div class="wcpt-editor-row-option">
      <label>Font color</label>
      <input type="text" wcpt-model-key="color" placeholder="#000" class="wcpt-color-picker">
    </div>

    <!-- font-weight -->
    <div class="wcpt-editor-row-option">
      <label>Font weight</label>
      <select wcpt-model-key="font-weight">
        <option value="normal">Normal</option>
        <option value="bold">Bold</option>
        <option value="200">Light</option>
      </select>
    </div>

    <!-- vertical-align -->
    <div class="wcpt-editor-option-row">
      <label>Vertical align</label>
      <select wcpt-model-key="vertical-align">
        <option value="middle">Middle</option>
        <option value="top">Top</option>
        <option value="baseline">Baseline</option>
        <option value="bottom">Bottom</option>
      </select>
    </div>

  </div>

  <!-- trigger text -->
  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id] .wcpt-tg-trigger">

    <span class="wcpt-toggle-label">
      Style for trigger (Show more / less)
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <!-- font-size -->
    <div class="wcpt-editor-row-option">
      <label>Font size</label>
      <input type="text" wcpt-model-key="font-size" />
    </div>

    <!-- font color -->
    <div class="wcpt-editor-row-option">
      <label>Font color</label>
      <input type="text" wcpt-model-key="color" placeholder="#000" class="wcpt-color-picker">
    </div>

    <!-- font-weight -->
    <div class="wcpt-editor-row-option">
      <label>Font weight</label>
      <select wcpt-model-key="font-weight">
        <option value="normal">Normal</option>
        <option value="bold">Bold</option>
        <option value="200">Light</option>
      </select>
    </div>

    <!-- background -->
    <div class="wcpt-editor-row-option">
      <label>Background color</label>
      <input type="text" wcpt-model-key="background-color" class="wcpt-color-picker">
    </div>

    <!-- padding -->
    <div class="wcpt-editor-row-option">
      <label>Padding</label>
      <input type="text" wcpt-model-key="padding-top" placeholder="top">
      <input type="text" wcpt-model-key="padding-right" placeholder="right">
      <input type="text" wcpt-model-key="padding-bottom" placeholder="bottom">
      <input type="text" wcpt-model-key="padding-left" placeholder="left">
    </div>

  </div>

  <!-- trigger icon -->
  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id] .wcpt-tg-trigger .wcpt-icon">

    <span class="wcpt-toggle-label">
      Style for trigger icon
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <!-- font-size -->
    <div class="wcpt-editor-row-option">
      <label>Size</label>
      <input type="text" wcpt-model-key="font-size">
    </div>

    <!-- font-color -->
    <div class="wcpt-editor-row-option">
      <label>Stroke color</label>
      <input type="text" wcpt-model-key="color" placeholder="#000" class="wcpt-color-picker" >
    </div>

    <!-- fill -->
    <div class="wcpt-editor-row-option">
      <label>Fill color</label>
      <input type="text" wcpt-model-key="fill" class="wcpt-color-picker" >
    </div>

    <!-- stroke-width -->
    <div class="wcpt-editor-row-option">
      <label>Thickness</label>
      <input type="text" wcpt-model-key="stroke-width">
    </div>

  </div>

</div>

<div class="wcpt-editor-row-option">
  <label>Additional CSS Class</label>
  <input type="text" wcpt-model-key="html_class" />
</div>
