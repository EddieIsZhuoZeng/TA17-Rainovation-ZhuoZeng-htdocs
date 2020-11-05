<!-- use default template -->
<div class="wcpt-editor-row-option">
  <label>
    <input type="checkbox" wcpt-model-key="use_default_template">
    Use default WooCommerce button from product page
  </label>
</div>

<div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="use_default_template"
  wcpt-condition-val="false"
>
  <!-- label -->
  <div class="wcpt-editor-row-option">
    <label>Label</label>
    <div
      wcpt-block-editor
      wcpt-model-key="label"
      wcpt-be-add-row="0"
    ></div>
  </div>

  <!-- link -->
  <div class="wcpt-editor-row-option">
    <label>Link</label>
    <?php
      $options = array(
        'product_link'  => 'Open product page',
        'cart_ajax'     => 'Add to cart via AJAX',
        'cart_refresh'  => 'Add to cart and refresh page',
        'cart_redirect' => 'Add to cart and redirect to cart page',
        'cart_checkout' => 'Add to cart and redirect to checkout page',
        'external_link' => 'Open external/affiliate link',
      );

      foreach( $options as $val => $label ){
          echo "<label><input type='radio' wcpt-model-key='link' value='$val' >$label</label>";
      }
      ?>
      <?php wcpt_pro_radio('custom_field', 'Custom field containing URL', 'link'); ?>
      <?php wcpt_pro_radio('custom_field_media_id', 'Custom field containing Media ID', 'link'); ?>
      <?php
    ?>
  </div>

  <!-- cart 302 issue -->
  <?php if( 'yes' === get_option('woocommerce_cart_redirect_after_add', 'no') ): ?>
  <div class="wcpt-editor-row-option " wcpt-panel-condition="prop" wcpt-condition-prop="link" wcpt-condition-val="cart_ajax||cart_refresh||cart_redirect||cart_checkout">
    <div class="wcpt-notice">
      Disable the 'Redirect to the cart page after successful addition' option <a href="<?php echo get_admin_url(); ?>admin.php?page=wc-settings&tab=products" target="_blank">here</a> to make the ajax cart option work.
    </div>
  </div>
  <?php endif; ?>

  <!-- custom field -->
  <div class="wcpt-editor-row-option" wcpt-panel-condition="prop" wcpt-condition-prop="link" wcpt-condition-val="custom_field||custom_field_media_id">
    <label>Custom field name / meta key</label>
    <input wcpt-model-key="custom_field" type="text" >
  </div>

  <!-- target -->
  <div class="wcpt-editor-row-option" wcpt-panel-condition="prop" wcpt-condition-prop="link" wcpt-condition-val="product_link||external_link">
    <label>Action on click</label>
    <label>
      <input type="radio" wcpt-model-key="target" value="_self" >
      Open the link on the same page
    </label>
    <label>
      <input type="radio" wcpt-model-key="target" value="_blank" >
      Open the link on a new page
    </label>
  </div>

  <!-- target for custom field -->
  <div class="wcpt-editor-row-option" wcpt-panel-condition="prop" wcpt-condition-prop="link" wcpt-condition-val="custom_field||custom_field_media_id">
    <label>Action on click</label>
    <label>
      <input type="radio" wcpt-model-key="target" value="_self" >
      Open the link on the same page
    </label>
    <label>
      <input type="radio" wcpt-model-key="target" value="_blank" >
      Open the link on a new page
    </label>
    <label>
      <input type="radio" wcpt-model-key="target" value="download" >
      Download the file
    </label>
  </div>

  <!-- empty value relabel -->
  <div class="wcpt-editor-row-option" wcpt-panel-condition="prop" wcpt-condition-prop="link" wcpt-condition-val="custom_field||custom_field_media_id">
    <label>Output when no custom field value exists</label>
    <div
      wcpt-model-key="custom_field_empty_relabel"
      wcpt-block-editor
      wcpt-be-add-row="0"
    ></div>
  </div>

  <div class="wcpt-editor-row-option" wcpt-model-key="style">

    <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id]">

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

  <div
    class="wcpt-editor-row-option"
    wcpt-panel-condition="prop"
    wcpt-condition-prop="link"
    wcpt-condition-val="cart_ajax||cart_refresh||cart_redirect||cart_checkout||external_link"
  >
    <div class="wcpt-editor-row-option" wcpt-model-key="style">
      <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id].wcpt-disabled">

        <span class="wcpt-toggle-label">
          Style when out of stock
          <?php echo wcpt_icon('chevron-down'); ?>
        </span>

        <!-- opacity -->
        <div class="wcpt-editor-row-option">
          <label>Opacity</label>
          <?php
            $arr = array(
              '.1' => '.1',
              '.2' => '.2',
              '.3' => '.3',
              '.4' => '.4',
              '.5' => '.5',
              '.6' => '.6',
              '.7' => '.7',
              '.8' => '.8',
              '.9' => '.9',
              '1' => '1',
            );

            foreach( $arr as $key=> $val ){
              ?>
              <label class="wcpt-radio-wrapper">
                <input type="radio" name="wcpt-opacity" wcpt-model-key="opacity" value="<?php echo $val; ?>" />
                <?php echo $key; ?>
              </label>
              <?php
            }
          ?>
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

        <!-- border-color -->
        <div class="wcpt-editor-row-option">
          <label>Border color</label>
          <input type="text" wcpt-model-key="border-color" class="wcpt-color-picker" placeholder="color">
        </div>

      </div>

    </div>
  </div>

  <div class="wcpt-editor-row-option">
    <label>Additional CSS Class</label>
    <input type="text" wcpt-model-key="html_class" />
  </div>
</div>

<?php
  include( 'condition/outer.php' );
?>
