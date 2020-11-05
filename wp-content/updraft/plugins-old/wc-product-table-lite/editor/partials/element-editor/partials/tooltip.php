<div class="wcpt-editor-row-option">
  <label>
    Tooltip label
  </label>
  <div
    wcpt-block-editor=""
    wcpt-be-add-element-partial="add-tooltip-element"
    wcpt-model-key="label"
  ></div>
</div>

<div class="wcpt-editor-row-option">
  <label>
    Tooltip content
  </label>
  <div
    wcpt-block-editor=""
    wcpt-be-add-element-partial="add-tooltip-element"
    wcpt-be-add-row="1"
    wcpt-model-key="content"
  ></div>
</div>

<div class="wcpt-editor-row-style-options wcpt-editor-row-option" wcpt-model-key="style">

  <div class="wcpt-wrapper wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id] > .wcpt-tooltip-label">

    <span class="wcpt-toggle-label">
      Style for ToolTip Label
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <!-- font-size -->
    <div class="wcpt-editor-row-option">
      <label>Font size</label>
      <input type="text" wcpt-model-key="font-size">
    </div>

    <!-- font color -->
    <div class="wcpt-editor-row-option">
      <label>Font color</label>
      <input type="text" wcpt-model-key="color" placeholder="#000" class="wcpt-color-picker">
    </div>

    <!-- background color -->
    <div class="wcpt-editor-row-option">
      <label>Background color</label>
      <input type="text" wcpt-model-key="background-color" class="wcpt-color-picker">
    </div>

    <!-- background color on hover -->
    <!-- <div class="wcpt-editor-row-option">
      <label>Background color on hover</label>
      <input type="text" wcpt-model-key="background-color:hover" class="wcpt-color-picker">
    </div> -->

    <!-- border -->
    <div class="wcpt-editor-row-option wcpt-borders-style">
      <label>Border</label>
      <input type="text" wcpt-model-key="border-width" placeholder="width">
      <select wcpt-model-key="border-style">
        <option value="solid">Solid</option>
        <option value="dashed">Dashed</option>
        <option value="dotted">Dotted</option>
        <option value="none">None</option>
      </select>
      <input type="text" wcpt-model-key="border-color" class="wcpt-color-picker" placeholder="color">
    </div>

    <!-- border-color on hover -->
    <!-- <div class="wcpt-editor-row-option">
      <label>Border color on hover</label>
      <input type="text" wcpt-model-key="border-color:hover" class="wcpt-color-picker" placeholder="color">
    </div> -->

    <!-- border-radius -->
    <div class="wcpt-editor-row-option">
      <label>Border radius</label>
      <input type="text" wcpt-model-key="border-radius" >
    </div>

    <!-- padding -->
    <div class="wcpt-editor-row-option">
      <label>Padding</label>
      <input type="text" wcpt-model-key="padding-top" placeholder="top">
      <input type="text" wcpt-model-key="padding-right" placeholder="right">
      <input type="text" wcpt-model-key="padding-bottom" placeholder="bottom">
      <input type="text" wcpt-model-key="padding-left" placeholder="left">
    </div>

    <!-- vertical align -->
    <div class="wcpt-editor-row-option">
      <label>Vertical align</label>
      <select wcpt-model-key="vertical-align">
        <option value="">Select:</option>
        <option value="middle">Middle</option>
        <option value="baseline">Baseline</option>
        <option value="top">Top</option>
      </select>
    </div>

  </div>

</div>

<div class="wcpt-editor-row-style-options wcpt-editor-row-option" wcpt-model-key="style">

  <div class="wcpt-wrapper wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id] > .wcpt-tooltip-content-wrapper > .wcpt-tooltip-content">

    <span class="wcpt-toggle-label">
      Style for ToolTip Content
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <!-- width -->
    <div class="wcpt-editor-row-option">
      <label>Width (px)</label>
      <input type="text" wcpt-model-key="width"/>
    </div>

    <!-- font-size -->
    <div class="wcpt-editor-row-option">
      <label>Font size</label>
      <input type="text" wcpt-model-key="font-size">
    </div>

    <!-- font color -->
    <div class="wcpt-editor-row-option">
      <label>Font color</label>
      <input type="text" wcpt-model-key="color" placeholder="#000" class="wcpt-color-picker">
    </div>

    <!-- line-height -->
    <div class="wcpt-editor-row-option">
      <label>Line height</label>
      <input type="text" wcpt-model-key="line-height">
    </div>

    <!-- text-align -->
    <div class="wcpt-editor-row-option">
      <label>Text align</label>
      <select wcpt-model-key="text-align">
        <option value="">Auto</option>
        <option value="center">Center</option>
        <option value="left">Left</option>
        <option value="right">Right</option>
      </select>
    </div>

    <!-- background-color -->
    <div class="wcpt-editor-row-option">
      <label>Background color</label>
      <input type="text" wcpt-model-key="background-color" class="wcpt-color-picker" >
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

</div>

<?php
  include( 'condition/outer.php' );
?>
