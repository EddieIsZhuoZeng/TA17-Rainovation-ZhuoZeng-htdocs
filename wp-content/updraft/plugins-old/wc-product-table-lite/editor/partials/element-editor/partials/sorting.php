<?php wcpt_how_to_use_link( "https://wcproducttable.com/documentation/sorting-by-column-heading" ); ?>

<!-- sorting options -->
<div class="wcpt-editor-row-option">
  <label>Sort by</label>
  <select class="" wcpt-model-key="orderby" wcpt-initial-data="title">
    <option value="title"          >Title</option>
    <option value="date"           >Date</option>
    <option value="rating"         >Rating</option>
    <option value="price"          >Price</option>
    <option value="popularity"     >Popularity (sales)</option>
    <option value="sku"            >SKU</option>
    <option value="meta_value_num" >Custom field as number</option>
    <option value="meta_value"     >Custom field as text</option>
  </select>
</div>

<div class="wcpt-editor-row-option" wcpt-panel-condition="prop" wcpt-condition-prop="orderby" wcpt-condition-val="meta_value_num||meta_value">
  <label for="">Sort by custom field key</label>
  <input type="text" wcpt-model-key="meta_key">
</div>

<div class="wcpt-editor-row-option" wcpt-model-key="style">

  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion">

    <span class="wcpt-toggle-label">
      Style for Element
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <div class="wcpt-wrapper wcpt-editor-row-option" wcpt-model-key="[id]">

      <!-- font-size -->
      <div class="wcpt-editor-row-option">
        <label>Size</label>
        <input type="text" wcpt-model-key="font-size" style="margin-bottom: 0 !important;">
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

    <div class="wcpt-wrapper wcpt-editor-row-option" wcpt-model-key="[id] > .wcpt-inactive">

      <!-- font-color -->
      <div class="wcpt-editor-row-option">
        <label>Color - inactive</label>
        <input type="text" wcpt-model-key="color" class="wcpt-color-picker" >
      </div>

    </div>

    <div class="wcpt-wrapper wcpt-editor-row-option" wcpt-model-key="[id] > .wcpt-active">

      <!-- font-color -->
      <div class="wcpt-editor-row-option">
        <label>Color - active</label>
        <input type="text" wcpt-model-key="color" class="wcpt-color-picker" >
      </div>

    </div>

  </div>

</div>

<div class="wcpt-editor-row-option">
  <label>HTML Class</label>
  <input type="text" wcpt-model-key="html_class" />
</div>
