<div class="wcpt-editor-row-option" wcpt-model-key="style">

  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id] .single_add_to_cart_button">

    <span class="wcpt-toggle-label">
      Style for Button
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

    <!-- font color on hover -->
    <div class="wcpt-editor-row-option">
      <label>Font color on hover</label>
      <input type="text" wcpt-model-key="color:hover" placeholder="#000" class="wcpt-color-picker">
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

    <!-- letter-spacing -->
    <div class="wcpt-editor-row-option">
      <label>Letter spacing</label>
      <input type="text" wcpt-model-key="letter-spacing" placeholder="0px" >
    </div>

    <!-- background color -->
    <div class="wcpt-editor-row-option">
      <label>Background color</label>
      <input type="text" wcpt-model-key="background-color" class="wcpt-color-picker">
    </div>

    <!-- background color on hover -->
    <div class="wcpt-editor-row-option">
      <label>Background color on hover</label>
      <input type="text" wcpt-model-key="background-color:hover" class="wcpt-color-picker">
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

    <!-- border-color on hover -->
    <div class="wcpt-editor-row-option">
      <label>Border color on hover</label>
      <input type="text" wcpt-model-key="border-color:hover" class="wcpt-color-picker" placeholder="color">
    </div>

    <!-- border-radius -->
    <div class="wcpt-editor-row-option">
      <label>Border radius</label>
      <input type="text" wcpt-model-key="border-radius" >
    </div>

    <!-- width -->
    <div class="wcpt-editor-row-option">
      <label>Force width</label>
      <input type="text" wcpt-model-key="width" />
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

<div class="wcpt-editor-row-option">
  <label>Additional CSS Class</label>
  <input type="text" wcpt-model-key="html_class" />
</div>

<?php
  include( 'condition/outer.php' );
?>
