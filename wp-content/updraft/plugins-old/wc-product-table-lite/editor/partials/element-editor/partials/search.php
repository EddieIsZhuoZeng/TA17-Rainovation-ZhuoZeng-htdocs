<!-- placeholder -->
<div class="wcpt-editor-row-option">
  <label>Placeholder</label>
  <input type="text" wcpt-model-key="placeholder">
</div>

<!-- override global settings -->
<div class="wcpt-editor-row-option">
  <label>Select target fields to search through:</label>
  <!-- target -->
  <?php foreach( array('Title', 'Content', 'Excerpt', 'SKU', 'Custom field', 'Category', 'Attribute', 'Tag') as $field ): ?>
  <?php $model_val = strtolower( str_replace(' ', '_', $field) ); ?>
  <?php 
    if( in_array( $field, array( 'Title', 'Content' ) ) ){
      ?>
      <label>
        <input 
          type="checkbox" 
          value="<?php echo $model_val; ?>" 
          wcpt-model-key="target[]" 
        />
        <?php echo $field; ?>
      </label>
      <?php
    }else{
      wcpt_pro_checkbox($model_val, $field, "target[]");
    }
  ?>

    <?php if( $model_val === 'custom_field' ): ?>
      <div
        class="wcpt-checkbox-selection-group"
        wcpt-panel-condition="prop"
        wcpt-condition-prop="target"
        wcpt-condition-val="custom_field"
      >
        <label>
          <small>Select custom fields to search through:</small>
        </label>
        <?php foreach( wcpt_get_product_custom_fields() as $meta_name ): ?>
          <label class="wcpt-editor-checkbox-label">
            <input 
              type="checkbox" 
              wcpt-model-key="custom_fields[]" 
              value="<?php echo esc_attr( $meta_name ); ?>"
            />
            <?php echo esc_attr( $meta_name ); ?>
          </label>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if( $model_val === 'attribute' ): ?>
      <div
        class="wcpt-checkbox-selection-group"
        wcpt-panel-condition="prop"
        wcpt-condition-prop="target"
        wcpt-condition-val="attribute"
      >
        <label>
          <small>Select attributes to search through:</small>
        </label>
        <?php foreach( $attributes as $attribute ): ?>
          <label class="wcpt-editor-checkbox-label">
            <input 
              type="checkbox" 
              wcpt-model-key="attributes[]" 
              value="<?php echo esc_attr( $attribute->attribute_name ); ?>"
            />
            <?php echo esc_attr( $attribute->attribute_label ); ?>
          </label>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <?php endforeach; ?>
</div>

<!-- clear label -->
<div class="wcpt-editor-row-option">
  <label>
    Text in 'clear search' option
    <small>use [kw] as placeholder for the search keyword</small>
  </label>
  <input type="text" wcpt-model-key="clear_label" placeholder="Search: [kw]">
</div>

<div class="wcpt-editor-row-option" wcpt-model-key="style">

  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id]">

    <span class="wcpt-toggle-label">
      Style for Element
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <!-- font-size -->
    <div class="wcpt-editor-row-option">
      <label>Font size</label>
      <input type="text" wcpt-model-key="font-size" placeholder="16px" wcpt-initial-data="">
    </div>

    <!-- font-color -->
    <div class="wcpt-editor-row-option">
      <label>Font color</label>
      <input type="text" wcpt-model-key="color" placeholder="#000" class="wcpt-color-picker" >
    </div>

    <!-- width -->
    <div class="wcpt-editor-row-option">
      <label>Force width</label>
      <input type="text" wcpt-model-key="width" />
    </div>

  </div>

</div>


<div class="wcpt-editor-row-option" wcpt-model-key="style">

  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id] .wcpt-search-submit">

    <span class="wcpt-toggle-label">
      Style for Submit Button
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <!-- background-color -->
    <div class="wcpt-editor-row-option">
      <label>Background color</label>
      <input type="text" wcpt-model-key="background-color" class="wcpt-color-picker" >
    </div>

    <!-- color -->
    <div class="wcpt-editor-row-option">
      <label>Icon color</label>
      <input type="text" wcpt-model-key="color" class="wcpt-color-picker" >
    </div>

  </div>

</div>


<div class="wcpt-editor-row-option">
  <label>HTML Class</label>
  <input type="text" wcpt-model-key="html_class" />
</div>
