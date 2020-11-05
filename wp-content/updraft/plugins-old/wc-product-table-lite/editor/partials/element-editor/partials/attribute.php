<!-- attribute -->
<div class="wcpt-editor-row-option">
  <label>
    Attribute
    <small>Only <a href="https://docs.woocommerce.com/document/managing-product-taxonomies/#set-global-attributes" target="_blank">global attributes</a> are supported.</small>
  </label>

  <?php
    $attributes = wc_get_attribute_taxonomies();
    if( empty( $attributes ) ){
      echo '<div class="wcpt-notice">There are no WooCommerce attributes on this site!</div>';
      $hide_class = 'wcpt-hide';
    }
  ?>
  <select class="<?php echo empty( $attributes ) ? 'wcpt-hide' : '';  ?>" wcpt-model-key="attribute_name">
    <option value=""></option>
    <?php
      foreach( $attributes as $attribute ){
        ?>
        <option value="<?php echo $attribute->attribute_name; ?>">
          <?php echo $attribute->attribute_label; ?>
        </option>
        <?php
      }
    ?>
  </select>
</div>

<div
  class="wcpt-editor-row-option"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="attribute_name"
  wcpt-condition-val="true"
>

  <!-- term separator -->
  <div class="wcpt-editor-row-option">
    <label>Separator between multiple terms</label>
    <div
      wcpt-model-key="separator"
      class="wcpt-separator-editor"
      wcpt-block-editor=""
      wcpt-be-add-row="0"
    ></div>
  </div>

  <!-- empty value relabel -->
  <div class="wcpt-editor-row-option">
    <label>Output when no terms</label>
    <div
      wcpt-model-key="empty_relabel"
      wcpt-block-editor=""
      wcpt-be-add-row="0"
    ></div>
  </div>

  <!-- exclude terms -->
  <div class="wcpt-editor-row-option">
    <label>
      Exclude terms by name
      <small>Enter one term name <u>per line</u></small>
    </label>
    <textarea wcpt-model-key="exclude_terms"></textarea>
  </div>

  <!-- link term to filter -->
  <div class="wcpt-editor-row-option">
    <?php 
      $label = "Link term to filter";
      wcpt_pro_checkbox(true, $label, "filter_link_term"); 
    ?>
    <label><small>Clicking on a term will add that term to the filter, but only if the attribute filter is present in navigation section.</small></label>
  </div>

  <!-- relabel -->
  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion">

    <span class="wcpt-toggle-label">
      Custom term labels <?php wcpt_pro_badge(); ?>
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <div class="wcpt-editor-loading" data-loading="terms" style="display: none;">
      <?php wcpt_icon('loader', 'wcpt-rotate'); ?> Loading ...
    </div>

    <div
      class="
        wcpt-editor-row-option
        <?php wcpt_pro_cover(); ?>
      "
      wcpt-model-key="relabels"
    >
      <div
        class="wcpt-editor-custom-label-setup"
        wcpt-controller="relabels"
        wcpt-model-key="[]"
        wcpt-model-key-index="0"
        wcpt-row-template="relabel_rule_term_column_element_2"
      >


        <div class="wcpt-tabs">

          <!-- triggers -->
          <div class="wcpt-tab-triggers">
            <div class="wcpt-tab-trigger" wcpt-content-template="term">
              Term name
            </div>
            <div class="wcpt-tab-trigger">
              Style
            </div>
          </div>

          <!-- content: term label -->
          <div class="wcpt-tab-content">
            <div class="wcpt-editor-row-option">
              <div
                wcpt-model-key="label"
                class="wcpt-term-relabel-editor"
                wcpt-block-editor=""
                wcpt-be-add-row="0"
                wcpt-be-add-element-partial="add-term-element"
              ></div>
            </div>
          </div>

          <!-- content: term style -->
          <div class="wcpt-tab-content">

            <div class="wcpt-editor-row-option" wcpt-model-key="style">
              <div class="wcpt-editor-row-option" wcpt-model-key="[id]">

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

                <!-- border color -->
                <div class="wcpt-editor-row-option">
                  <label>Border color</label>
                  <input type="text" wcpt-model-key="border-color" class="wcpt-color-picker">
                </div>

              </div>
            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

  <?php include( 'style/parent-child.php' ); ?>

</div>
