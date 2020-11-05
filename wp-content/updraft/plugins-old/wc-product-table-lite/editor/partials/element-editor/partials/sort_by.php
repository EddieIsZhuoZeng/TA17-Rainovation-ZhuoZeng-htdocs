<!-- display type -->
<div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="position"
  wcpt-condition-val="header"
>
  <label>Display type</label>
  <select wcpt-model-key="display_type">
    <option value="dropdown">Dropdown</option>
    <option value="row">Row</option>
  </select>
</div>

<!-- heading -->
<div class="wcpt-editor-row-option" wcpt-panel-condition="prop" wcpt-condition-prop="display_type" wcpt-condition-val="row">
  <label>Heading</label>
  <input type="text" wcpt-model-key="heading">
</div>

<div class="wcpt-editor-row-option">
  <label class="wcpt-editor-options-heading">Sort Options</label>
</div>

<!-- options -->
<div class="wcpt-editor-row-option">

  <!-- option rows -->
  <div
    class="wcpt-label-options-rows-wrapper wcpt-sortable"
    wcpt-model-key="dropdown_options"
  >
    <div
      class="wcpt-editor-row wcpt-editor-custom-label-setup"
      wcpt-controller="custom_labels"
      wcpt-model-key="[]"
      wcpt-model-key-index="0"
      wcpt-initial-data="sortby_option"
      wcpt-row-template="sortby_option"
    >

      <!-- label -->
      <div class="wcpt-editor-row-option">
        <label>Label</label>
        <input type="text" wcpt-model-key="label">
      </div>

      <!-- Orderby -->
      <div class="wcpt-editor-row-option">
        <label>Order by</label>
        <select wcpt-model-key="orderby">
          <option value="popularity">Popularity (sales)</option>
          <option value="rating">Average rating</option>
          <option value="price">Price low to high</option>
          <option value="price-desc">Price high to low</option>
          <option value="title">Title</option>
          <option value="rand">Random</option>
          <option value="date">Date</option>
          <option value="menu_order">Menu order</option>
          <?php wcpt_pro_option('sku', 'SKU as text'); ?>
          <?php wcpt_pro_option('sku_num', 'SKU as integer'); ?>
          <?php wcpt_pro_option('meta_value_num', 'Custom field as number'); ?>
          <?php wcpt_pro_option('meta_value', 'Custom field as text'); ?>
        </select>
      </div>

      <!-- meta key -->
      <div
        class="wcpt-editor-row-option"
        wcpt-panel-condition="prop"
        wcpt-condition-prop="orderby"
        wcpt-condition-val="meta_value_num||meta_value"
      >
        <label>Custom field name</label>
        <input type="text" wcpt-model-key="meta_key">
      </div>

      <!-- Order -->
      <div
        class="wcpt-editor-row-option"
        wcpt-panel-condition="prop"
        wcpt-condition-prop="orderby"
        wcpt-condition-val="meta_value_num||meta_value||title||menu_order||sku||sku_num"
      >
        <label>Order</label>
        <select wcpt-model-key="order">
          <option value="ASC">Low to high</option>
          <option value="DESC">High to low</option>
        </select>
      </div>

      <!-- corner options -->
      <?php wcpt_corner_options(); ?>

    </div>

    <button
      class="wcpt-button"
      wcpt-add-row-template="sortby_option"
    >
      Add an Option
    </button>

  </div>
</div>

<!-- accordion always open -->
<div class="wcpt-editor-row-option">
  <label>
    <input type="checkbox" wcpt-model-key="accordion_always_open"> Keep sidebar / modal accordion always open
  </label>
</div>

<?php include('style/filter.php'); ?>
