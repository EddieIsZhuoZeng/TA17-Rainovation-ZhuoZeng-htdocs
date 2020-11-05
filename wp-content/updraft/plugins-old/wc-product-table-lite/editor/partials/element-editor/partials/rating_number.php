<!-- single decimal -->
<div class="wcpt-editor-row-option">
  <label>
    <input type="checkbox" wcpt-model-key="decimals" value="1">
    Force single decimal place
  </label>
</div>

<!-- decimal point -->
<div class="wcpt-editor-row-option">
  <label>Decimal point symbol</label>
  <input type="text" wcpt-model-key="dec_point">
</div>

<!-- style -->
<div class="wcpt-editor-row-style-options wcpt-editor-row-option" wcpt-model-key="style">

  <div wcpt-model-key="[id]">

    <!-- margin -->
    <div class="wcpt-editor-row-option">
      <label>Margin</label>
      <input type="text" wcpt-model-key="margin-top" placeholder="top">
      <input type="text" wcpt-model-key="margin-right" placeholder="right">
      <input type="text" wcpt-model-key="margin-bottom" placeholder="bottom">
      <input type="text" wcpt-model-key="margin-left" placeholder="left">
    </div>

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

    <!-- font-weight -->
    <div class="wcpt-editor-row-option">
      <label>Font weight</label>
      <select wcpt-model-key="font-weight">
        <option value="normal">Normal</option>
        <option value="bold">Bold</option>
        <option value="lighter">Light</option>
      </select>
    </div>

    <!-- width -->
    <div class="wcpt-editor-row-option">
      <label>Force width</label>
      <input type="text" wcpt-model-key="width" />
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

  </div>

</div>
