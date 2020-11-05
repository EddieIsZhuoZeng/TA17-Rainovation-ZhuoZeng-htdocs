<!-- display type -->
<div class="wcpt-editor-row-option">
  <label>
    Display type
  </label>
  <label><input type="radio" value="input" wcpt-model-key="display_type" /> Input box (numeric input field)</label>
  <?php wcpt_pro_radio('select', 'Dropdown (select field)', 'display_type'); ?>
</div>

<div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="display_type"
  wcpt-condition-val="select"
>
  <!-- max quantity -->
  <div class="wcpt-editor-row-option">
    <label>
      Max quantity (default)
    </label>
    <input type="number" wcpt-model-key="max_qty" />
  </div>

  <!-- quantity label -->
  <div class="wcpt-editor-row-option">
    <label>
      Quantity label
    </label>
    <input type="text" wcpt-model-key="qty_label" />
  </div>

</div>

<!-- controls -->
<div 
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="display_type"
  wcpt-condition-val="input"
>
  <label>
    Controls +/-
  </label>
  <label><input type="radio" value="none" wcpt-model-key="controls" /> None</label>
  <label><input type="radio" value="browser" wcpt-model-key="controls" /> Browser default</label>
  <?php wcpt_pro_radio('left_edge', 'Left edge', 'controls'); ?>
  <?php wcpt_pro_radio('right_edge', 'Right edge', 'controls'); ?>
  <?php wcpt_pro_radio('edges', 'Edges', 'controls'); ?>
</div>

<!-- hide if 1 limit order -->
<div class="wcpt-editor-row-option">
  <?php wcpt_pro_checkbox('true', 'Hide if only 1 allowed per order', 'hide_if_sold_individually'); ?>
</div>

<div 
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="display_type"
  wcpt-condition-val="input"
>
  <div
    wcpt-model-key="style"
  >
    <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id].wcpt-display-type-input">

      <span class="wcpt-toggle-label">
        Style for Element
        <?php echo wcpt_icon('chevron-down'); ?>
      </span>

      <!-- font-size -->
      <div class="wcpt-editor-row-option">
        <label>Font size</label>
        <input type="text" wcpt-model-key="font-size" placeholder="16px">
      </div>

      <!-- font-color -->
      <div class="wcpt-editor-row-option">
        <label>Font color</label>
        <input type="text" wcpt-model-key="color" placeholder="#000" class="wcpt-color-picker" >
      </div>

      <!-- background-color -->
      <div class="wcpt-editor-row-option">
        <label>Background color</label>
        <input type="text" wcpt-model-key="background-color" class="wcpt-color-picker" >
      </div>

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

      <!-- margin -->
      <div class="wcpt-editor-row-option">
        <label>Margin</label>
        <input type="text" wcpt-model-key="margin-top" placeholder="top">
        <input type="text" wcpt-model-key="margin-right" placeholder="right">
        <input type="text" wcpt-model-key="margin-bottom" placeholder="bottom">
        <input type="text" wcpt-model-key="margin-left" placeholder="left">
      </div>

      <!-- padding -->
      <div class="wcpt-editor-option-row">
        <label>Padding</label>
        <input type="text" wcpt-model-key="padding-top" placeholder="top">
        <input type="text" wcpt-model-key="padding-right" placeholder="right">
        <input type="text" wcpt-model-key="padding-bottom" placeholder="bottom">
        <input type="text" wcpt-model-key="padding-left" placeholder="left">
      </div>

    </div>
  </div>
</div>

<div 
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="display_type"
  wcpt-condition-val="select"
>
  <div
    wcpt-model-key="style"
  >
    <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id] > .wcpt-qty-select">

      <span class="wcpt-toggle-label">
        Style for Select element
        <?php echo wcpt_icon('chevron-down'); ?>
      </span>

      <!-- font-size -->
      <div class="wcpt-editor-row-option">
        <label>Font size</label>
        <input type="text" wcpt-model-key="font-size" placeholder="16px">
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

<div class="wcpt-editor-row-option">
  <label>HTML class for container</label>
  <input type="text" wcpt-model-key="html_class" />
</div>
