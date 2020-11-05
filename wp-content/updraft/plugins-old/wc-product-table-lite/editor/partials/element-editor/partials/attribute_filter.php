<!-- attribute -->
<div class="wcpt-editor-row-option">

  <!-- the attribute -->
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

  <!-- heading -->
  <div class="wcpt-editor-row-option">
    <label>
      Heading
      <small>You can use [attribute] placeholder</small>
    </label>
    <div
      wcpt-block-editor
      wcpt-model-key="heading"
      wcpt-be-add-row="0"
    ></div>
  </div>

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

  <!-- operator -->
  <div class="wcpt-editor-row-option">
    <label>Operator</label>
    <select wcpt-model-key="operator">
      <option value="IN">"IN" - each result must have at least one selected term</option>
      <option value="AND">"AND" - each result must have all the selected terms</option>
      <option value="NOT IN">"NOT IN" - each result must have none of the selected terms</option>
    </select>
  </div>

  <!-- multiple selections permission -->
  <div class="wcpt-editor-row-option">
    <label>
      <input type="checkbox" wcpt-model-key="single" />
      Only allow one option to be selected
    </label>
  </div>

  <!-- "Show all" label -->
  <div class="wcpt-editor-row-option"
    wcpt-panel-condition="prop"
    wcpt-condition-prop="single"
    wcpt-condition-val="true"
  >
    <label>
      "Show All" option label
    </label>
    <div
      wcpt-model-key="show_all_label"
      wcpt-block-editor
      wcpt-be-add-row="0"
    ></div>
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
        class="wcpt-editor-row wcpt-editor-custom-label-setup"
        wcpt-controller="relabels"
        wcpt-model-key="[]"
        wcpt-model-key-index="0"
        wcpt-row-template="relabel_rule_term_filter_element_2"
      >
        <div class="wcpt-tabs">

          <!-- triggers -->
          <div class="wcpt-tab-triggers">
            <div class="wcpt-tab-trigger" wcpt-content-template="term">
              Term name
            </div>
            <div class="wcpt-tab-trigger" wcpt-can-disable>
              Clear label
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
              ></div>
            </div>
          </div>

          <!-- content: clear fitler label -->
          <div class="wcpt-tab-content">
            <div class="wcpt-editor-row-option">
              <input type="text" wcpt-model-key="clear_label" placeholder="[filter] : [option]">
            </div>
          </div>

        </div>

      </div>

    </div>

  </div>

  <!-- exclude terms -->
  <div class="wcpt-editor-row-option">
    <label>
      Exclude terms by name
      <small>Enter one term name <u>per line</u></small>
    </label>
    <textarea wcpt-model-key="exclude_terms"></textarea>
  </div>

  <!-- accordion always open -->
  <div class="wcpt-editor-row-option">
    <label>
      <input type="checkbox" wcpt-model-key="accordion_always_open"> Keep sidebar / modal accordion always open
    </label>
  </div>

  <?php include('style/filter.php'); ?>

</div>
