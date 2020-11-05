<div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="use_external_source"
  wcpt-condition-val="false"
>
  <!-- select image -->
  <div class="wcpt-editor-row-option">
    <button type="button" class="wcpt-select-media-button wcpt-button" style="padding: 10px 20px;">Select image</button>
    <div class="wcpt-selected-media-display"></div>
    <input class="wcpt-hide" type="text" wcpt-model-key="media_id" />
    <input class="wcpt-hide" type="text" wcpt-model-key="url" />
  </div>

  <!-- size -->
  <div class="wcpt-editor-row-option">
    <label>Select image size</label>
    <select wcpt-model-key="size">
      <?php
        foreach( get_intermediate_image_sizes() as $image_size ){
          echo "<option value='" . $image_size . "'>". ucfirst( str_replace( '_', ' ', $image_size ) ) ."</option>";
        }
      ?>
    </select>
  </div>

</div>

<!-- use external source -->
<div class="wcpt-editor-row-option">
  <label>
    <input type="checkbox" wcpt-model-key="use_external_source" /> Use an external image source instead
  </label>
</div>

<!-- external image source -->
<div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="use_external_source"
  wcpt-condition-val="true"
>
  <label>
    Enter image url
  </label>
  <input type="text" wcpt-model-key="external_source" />
</div>

<!-- label -->
<div class="wcpt-editor-row-option">
  <label>Image label (optional)</label>
  <input type="text" wcpt-model-key="label" />
</div>

<div class="wcpt-editor-row-option" wcpt-model-key="style">

  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion wcpt-open" wcpt-model-key="[id]">

    <span class="wcpt-toggle-label">
      Style for Element
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

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

    <!-- max-width -->
    <div class="wcpt-editor-row-option">
      <label>Max width</label>
      <input type="text" wcpt-model-key="max-width" />
    </div>

    <!-- max-height -->
    <div class="wcpt-editor-row-option">
      <label>Max height</label>
      <input type="text" wcpt-model-key="max-height" />
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

    <!-- border-radius -->
    <div class="wcpt-editor-row-option">
      <label>Border radius</label>
      <input type="text" wcpt-model-key="border-radius" >
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
  <label>HTML Class</label>
  <input type="text" wcpt-model-key="html_class" />
</div>
