<!-- style -->
<div class="wcpt-editor-row-style-options wcpt-editor-row-option" wcpt-model-key="style">

  <div wcpt-model-key="[id]">

    <!-- font-size -->
    <div class="wcpt-editor-row-option">
      <label>Size</label>
      <input type="text" wcpt-model-key="font-size" placeholder="16px">
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
