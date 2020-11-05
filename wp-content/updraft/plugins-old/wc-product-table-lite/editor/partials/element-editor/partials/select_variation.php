<a href="https://wcproducttable.com/documentation/select-variation/" target="_blank" class="wcpt-how-to-use">
  <?php wcpt_icon('file-text'); ?>
  <span>How to use</span>
</a>

<div class="wcpt-editor-row-option">
  <label>
    Display type
  </label>
  <select class="" wcpt-model-key="display_type">
    <option value="dropdown">Dropdown with all variations</option>
    <option value="radio_multiple">Radio buttons for all variations</option>
    <option value="radio_single">Single radio button &mdash; for 1 variation</option>
  </select>
</div>

<!-- single radio options -->
<div class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="display_type"
  wcpt-condition-val="dropdown||radio_multiple"
>

  <div class="wcpt-editor-row-option">
    <label>
      <input type="checkbox" wcpt-model-key="hide_attributes">
      Hide attributes from variation name
      <small>
        Eg: "Size: Large, Gluten: Gluten free" becomes "Large, Gluten free"
      </small>
    </label>
  </div>

  <div class="wcpt-editor-row-option">
    <label>
      Separator between each attribute and term
      <small>
        A character to show separating between the attribute and the term. Eg: : - &mdash;
      </small>
    </label>
    <input type="text" wcpt-model-key="attribute_term_separator">
  </div>

  <div class="wcpt-editor-row-option">
    <label>
      Separator between attributes
      <small>
        A character to show separating between the attributes. Eg: , | & ::
      </small>
    </label>
    <input type="text" wcpt-model-key="attribute_separator">
  </div>

</div>

<!-- dropdown options -->
<div class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="display_type"
  wcpt-condition-val="dropdown"
>
  <div class="wcpt-editor-row-option">
    <label>
      Label for 'Select'
      <small>
        Appears when no default variation is selected
      </small>
    </label>
    <input type="text" wcpt-model-key="select_label">
  </div>
</div>

<!-- radio options -->
<div class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="display_type"
  wcpt-condition-val="radio_multiple"
>
  <div class="wcpt-editor-row-option">
    <label>
      <input type="checkbox" wcpt-model-key="separate_lines" />
      Show options in separate lines
    </label>
  </div>
</div>

<!-- radio & dropdown options -->
<div class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="display_type"
  wcpt-condition-val="radio_multiple||dropdown"
>

  <!-- hide stock info -->
  <div class="wcpt-editor-row-option">
    <label>
      <input type="checkbox" wcpt-model-key="hide_stock" />
      Hide the stock information
    </label>
  </div>

  <!-- template for non-variable -->
  <div class="wcpt-editor-row-option">
    <label>
      Output template when product is not variable
    </label>
    <div 
      wcpt-model-key="non_variable_template"
      wcpt-block-editor=""
      wcpt-be-add-row="1"
      wcpt-be-add-element-partial="add-column-cell-element"
    ></div>
  </div>

</div>

<!-- single radio options -->
<div class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="display_type"
  wcpt-condition-val="radio_single"
>

  <div class="wcpt-editor-row-option">
    <label>
      Variation name
      <small>Give this variation a name</small>
    </label>
    <input type="text" wcpt-model-key="variation_name" />
  </div>

  <div class="wcpt-editor-row-option" wcpt-model-key="attribute_terms">

    <label>
      Specify all attribute-terms of this variation
      <small>This list will help WCPT identify the variation</small>
    </label>

    <div
      class="wcpt-editor-row wcpt-editor-select-variation-attribute-term"
      wcpt-controller="taxonomy_terms"
      wcpt-model-key="[]"
      wcpt-model-key-index="0"
      wcpt-row-template="identify_variation"
    >
      <select wcpt-model-key="taxonomy">
        <option value="">Attribute</option>
        <?php
          foreach( $attributes as $attribute ){
            echo '<option value="pa_'. $attribute->attribute_name .'">' . $attribute->attribute_label . '</option>';
          }
        ?>
      </select>
      <select wcpt-model-key="term">
        <option value="">Term</option>
      </select>
      <span class="wcpt-loading-term" style="display: none;"><?php wcpt_icon('loader', 'wcpt-rotate'); ?> Loading...</span>
      <span class="wcpt-remove-item" wcpt-remove-row title="Delete row"><?php wcpt_icon('x') ?></span>
    </div>

    <button
      class="wcpt-button"
      wcpt-add-row-template="identify_variation"
    >
      Add another
    </button>

  </div>

  <div class="wcpt-editor-row-option">
    <label>
      Template
      <small>Placeholder: [variation_name]</small>
    </label>
    <div
      wcpt-block-editor=""
      wcpt-be-add-element-partial="add-variation-element"
      wcpt-be-add-row="1"
      wcpt-model-key="template"
    ></div>
  </div>

  <div class="wcpt-editor-row-option">
    <label>
      Output if this variation does not exist for the product
      <small>Leave empty for no output</small>
    </label>
    <div
      wcpt-block-editor=""
      wcpt-be-add-element-partial="add-common-element"
      wcpt-be-add-row="1"
      wcpt-model-key="not_exist_template"
    ></div>
  </div>

  <!-- radio single style -->
  <div class="wcpt-editor-row-option" wcpt-model-key="style">

    <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id]">

      <span class="wcpt-toggle-label">
        Style for Container
        <?php echo wcpt_icon('chevron-down'); ?>
      </span>

      <?php require( 'style/common-props.php' ); ?>

    </div>

    <!-- style: out of stock -->
    <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id].wcpt-variation-out-of-stock">

      <span class="wcpt-toggle-label">
        Style when variation is 'Out of Stock'
        <?php echo wcpt_icon('chevron-down'); ?>
      </span>

      <?php require( 'style/common-props.php' ); ?>

    </div>

    <!-- style: checked -->
    <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id].wcpt-selected">

      <span class="wcpt-toggle-label">
        Style when variation is 'Selected'
        <?php echo wcpt_icon('chevron-down'); ?>
      </span>

      <?php require( 'style/common-props.php' ); ?>

    </div>

  </div>

</div> <!-- /single radio options -->

<!-- dropdown style -->
<div
  wcpt-panel-condition="prop"
  wcpt-condition-prop="display_type"
  wcpt-condition-val="dropdown"
>
  <div wcpt-model-key="style">
    <div 
      class="wcpt-toggle-options wcpt-row-accordion"
      wcpt-model-key="[id] > .wcpt-select-variation-dropdown"
    >
      <span class="wcpt-toggle-label">
        Style for Dropdown
        <?php echo wcpt_icon('chevron-down'); ?>
      </span>

      <!-- font-size -->
      <div class="wcpt-editor-row-option">
        <label>Font size</label>
        <input type="text" wcpt-model-key="font-size" />
      </div>

      <!-- line-height -->
      <div class="wcpt-editor-row-option">
        <label>Line height</label>
        <input type="text" wcpt-model-key="line-height" placeholder="1.2em">
      </div>

      <!-- width -->
      <div class="wcpt-editor-row-option">
        <label>Width</label>
        <input type="text" wcpt-model-key="width" />
      </div>

      <!-- height -->
      <div class="wcpt-editor-row-option">
        <label>Height</label>
        <input type="text" wcpt-model-key="height" />
      </div>

      <!-- padding -->
      <div class="wcpt-editor-row-option">
        <label>Padding</label>
        <input type="text" wcpt-model-key="padding-top" placeholder="top">
        <input type="text" wcpt-model-key="padding-right" placeholder="right">
        <input type="text" wcpt-model-key="padding-bottom" placeholder="bottom">
        <input type="text" wcpt-model-key="padding-left" placeholder="left">
      </div>

      <!-- margin -->
      <div class="wcpt-editor-row-option">
        <label>Margin</label>
        <input type="text" wcpt-model-key="margin-top" placeholder="top">
        <input type="text" wcpt-model-key="margin-right" placeholder="right">
        <input type="text" wcpt-model-key="margin-bottom" placeholder="bottom">
        <input type="text" wcpt-model-key="margin-left" placeholder="left">
      </div>
    </div>
  </div>
</div>


<!-- radio multiple style -->
<div 
  wcpt-panel-condition="prop"
  wcpt-condition-prop="display_type"
  wcpt-condition-val="radio_multiple"
> 
  <div wcpt-model-key="style">
    <div 
      class="wcpt-toggle-options wcpt-row-accordion"     
      wcpt-model-key="[id].wcpt-select-varaition-radio-multiple-wrapper"
    >
      <span class="wcpt-toggle-label">
        Style for Container
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

      <!-- line-height -->
      <div class="wcpt-editor-row-option">
        <label>Line height</label>
        <input type="text" wcpt-model-key="line-height" placeholder="1.2em">
      </div>

      <!-- padding -->
      <div class="wcpt-editor-row-option">
        <label>Padding</label>
        <input type="text" wcpt-model-key="padding-top" placeholder="top">
        <input type="text" wcpt-model-key="padding-right" placeholder="right">
        <input type="text" wcpt-model-key="padding-bottom" placeholder="bottom">
        <input type="text" wcpt-model-key="padding-left" placeholder="left">
      </div>

      <!-- margin -->
      <div class="wcpt-editor-row-option">
        <label>Margin</label>
        <input type="text" wcpt-model-key="margin-top" placeholder="top">
        <input type="text" wcpt-model-key="margin-right" placeholder="right">
        <input type="text" wcpt-model-key="margin-bottom" placeholder="bottom">
        <input type="text" wcpt-model-key="margin-left" placeholder="left">
      </div>
    </div>
  </div>
</div>
