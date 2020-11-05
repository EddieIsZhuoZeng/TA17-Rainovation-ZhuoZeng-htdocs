<div class="wcpt-editor-row-style-options wcpt-toggle-options" wcpt-model-key="style">

  <div class="wcpt-editor-row-option wcpt-toggle-label">
    Style Options
    <?php wcpt_icon('chevron-down', 'wcpt-toggle-icon'); ?>
  </div>

  <div class="wcpt-wrapper" wcpt-model-key="[id]">

    <!-- font-size -->
    <div class="wcpt-editor-row-option">
      <label>Font size</label>
      <input type="text" wcpt-model-key="font-size" placeholder="16px">
    </div>

    <!-- line-height -->
    <div class="wcpt-editor-row-option">
      <label>Line height</label>
      <input type="text" wcpt-model-key="line-height" placeholder="1.2em">
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

    <!-- font-color -->
    <div class="wcpt-editor-row-option">
      <label>Font color</label>
      <input type="text" wcpt-model-key="color" placeholder="#000" class="wcpt-color-picker" >
    </div>

    <!-- text-transform -->
    <div class="wcpt-editor-row-option">
      <label>Text transform</label>
      <select wcpt-model-key="text-transform">
        <option value="none">None</option>
        <option value="uppercase">Upper case</option>
        <option value="capitalize">Capitalize</option>
        <option value="lowercase">Lower case</option>
      </select>
    </div>

    <!-- letter-spacing -->
    <div class="wcpt-editor-row-option">
      <label>Letter spacing</label>
      <input type="text" wcpt-model-key="letter-spacing" placeholder="0px" >
    </div>

    <!-- background-color -->
    <div class="wcpt-editor-row-option">
      <label>Background color</label>
      <input type="text" wcpt-model-key="background-color" class="wcpt-color-picker" >
    </div>

    <!-- border -->
    <div class="wcpt-editor-row-option">
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

    <!-- border-radius -->
    <div class="wcpt-editor-row-option">
      <label>Border radius</label>
      <input type="text" wcpt-model-key="border-radius" >
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
    <div class="wcpt-editor-row-option">
      <label>Padding</label>
      <input type="text" wcpt-model-key="padding-top" placeholder="top">
      <input type="text" wcpt-model-key="padding-right" placeholder="right">
      <input type="text" wcpt-model-key="padding-bottom" placeholder="bottom">
      <input type="text" wcpt-model-key="padding-left" placeholder="left">
    </div>

    <!-- display -->
    <div class="wcpt-editor-row-option">
      <label>Display</label>
      <select wcpt-model-key="display">
        <option value="inline-block">Inline-block</option>
        <option value="inline">Inline</option>
        <option value="block">Block</option>
      </select>
    </div>

  </div>

</div>
