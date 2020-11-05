<div class="wcpt-option-row">
  <div class="wcpt-option-label" style="width: 100%">
    <?php _e( "Select products by category", 'wc-product-table' ); ?>:
  </div>

  <div class="wcpt-category-options wcpt-hierarchy">
    <?php
    function wcpt_product_cat_options_walker( $category ){
      $child_cats = get_terms( 'product_cat', array('parent' => $category->term_id, 'hide_empty' => 0) );
      ?>
      <div class="wcpt-category">
        <label>
          <input
            type="checkbox"
            class="<?php echo $child_cats ? 'wcpt-hr-parent-term' : ''; ?>"
            wcpt-model-key="category[]"
            wcpt-controller="category"
            value="<?php echo $category->term_taxonomy_id; ?>"
          />
          <?php echo $category->name; ?>
        </label>
        <?php
          if( $child_cats ){
            ?>
            <i class="wcpt-toggle-sub-categories"><?php wcpt_icon('chevron-down'); ?></i>
            <div class="wcpt-sub-categories wcpt-hr-child-terms-wrapper">
              <?php
              foreach ($child_cats as $child_cat) {
                wcpt_product_cat_options_walker( $child_cat );
              }
              ?>
            </div>
            <?php
          }
        ?>
      </div>
      <?php
    };

    $product_cats = get_terms( 'product_cat', array( 'hide_empty' => 0 ) );
    foreach( $product_cats as $category){
      if( $category->parent ){
        continue;
      }
      wcpt_product_cat_options_walker( $category );
    }
    ?>
  </div>
</div>

<!-- limit -->
<div class="wcpt-option-row">
  <div class="wcpt-option-label"><?php _e( "Max products per page", 'wc-product-table' ); ?>:</div>
  <div class="wcpt-input">
    <input class="wcpt-limit" wcpt-model-key="limit" type="number" min="-1" value=""/>
  </div>
</div>

<!-- pagination -->
<div class="wcpt-option-row">
  <div class="wcpt-option-label"><?php _e( "Pagination", 'wc-product-table' ); ?>:</div>
  <div class="wcpt-input">
    <label>
      <input class="wcpt-paginate" wcpt-model-key="paginate" type="checkbox" value="on" />
      Enable
    </label>
  </div>
</div>

<!-- orderby -->
<div class="wcpt-option-row">
  <div class="wcpt-option-label"><?php _e( "Initial orderby", 'wc-product-table' ); ?>:</div>
  <div class="wcpt-input">
    <select class="wcpt-orderby" wcpt-model-key="orderby">
    <?php
      $orderby_options  = array(
        'title'           => __( 'Title', 'wcpt' ),
        'date'            => __( 'Newness', 'wcpt' ),
        'menu_order'      => __( 'Menu order', 'wcpt' ),
        'rating'          => __( 'Average rating', 'wcpt' ),
        'price'           => __( 'Price: low to high ', 'wcpt' ),
        'price-desc'      => __( 'Price: high to low', 'wcpt' ),
        'popularity'      => __( 'Popularity (sales)', 'wcpt' ),
        'rand'           => __( 'Random', 'wcpt' ),
        // 'ID'             => __( 'Product ID', 'wcpt' ),
      );
      foreach( $orderby_options as $option => $label ){
      ?>
        <option value="<?php echo $option ?>"><?php echo $label; ?></option>
      <?php
      }
    ?>
    <?php wcpt_pro_option('meta_value_num', 'Custom field as number'); ?>
    <?php wcpt_pro_option('meta_value', 'Custom field as text'); ?>
    <?php wcpt_pro_option('sku', 'SKU as text'); ?>
    <?php wcpt_pro_option('sku_num', 'SKU as integer'); ?>
    </select>
  </div>
</div>

<!-- meta key -->
<div
  class="wcpt-option-row"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="orderby"
  wcpt-condition-val="meta_value_num||meta_value"
>
  <div class="wcpt-option-label"><?php _e( "Custom field to orderby", 'wc-product-table' ); ?>:</div>
  <div class="wcpt-input">
    <input class="wcpt-course-ids" wcpt-model-key="meta_key" type="text">
  </div>
</div>

<!-- order -->
<div
  class="wcpt-option-row"
  wcpt-panel-condition="prop"
  wcpt-condition-prop="orderby"
  wcpt-condition-val="meta_value_num||meta_value||title||menu_order||sku||sku_num"
>
  <div class="wcpt-option-label"><?php _e( "Initial order", 'wc-product-table' ); ?>:</div>
  <div class="wcpt-input">
    <select class="wcpt-order" wcpt-model-key="order">
    <?php
      $order_options  = array(
        'ASC'   => __( 'Ascending', 'wcpt' ),
        'DESC'  => __( 'Descending', 'wcpt' ),
      );
      foreach( $order_options as $option => $label ){
      ?>
        <option value="<?php echo $option ?>"><?php echo $label; ?></option>
      <?php
      }
    ?>
    </select>
  </div>
</div>

<!-- hide out of stock items -->
<div class="wcpt-option-row">
  <div class="wcpt-option-label"><?php _e( "Hide out of stock items", 'wc-product-table' ); ?>:</div>
  <div class="wcpt-input">
    <label>
      <?php
        $disabled = get_option('woocommerce_hide_out_of_stock_items', 'no') == 'yes' ? 'disabled="disabled" checked="checked"' : '';
      ?>
      <input wcpt-model-key="hide_out_of_stock_items" type="checkbox" value="on" <?php echo $disabled; ?> />
      Enable
      <?php if( $disabled ): ?>
        <span class="wcpt-hide-out-of-stock-option-note">
          To enable this option, please uncheck 'Out of stock visibility'
          <a href="<?php echo get_admin_url(); ?>admin.php?page=wc-settings&tab=products&section=inventory" target="_blank">here</a>.
        </span>
      <?php endif; ?>
    </label>
  </div>
</div>

<!-- ids -->
<div class="wcpt-option-row">
  <div class="wcpt-option-label"><?php _e( "Select products by ID", 'wc-product-table' ); ?>:</div>
  <div class="wcpt-input">
    <textarea class="wcpt-course-ids" wcpt-model-key="ids" placeholder="<?php _e( "enter comma separated product IDs", 'wc-product-table' ); ?>"></textarea>
    </select>
  </div>
</div>

<!-- skus -->
<div class="wcpt-option-row">
  <div class="wcpt-option-label"><?php _e( "Select products by SKU", 'wc-product-table' ); ?>:</div>
  <div class="wcpt-input">
    <textarea class="wcpt-course-skus" wcpt-model-key="skus" placeholder="<?php _e( "enter comma separated product SKUs", 'wc-product-table' ); ?>"></textarea>
    </select>
  </div>
</div>

<!-- additional query args -->
<div class="wcpt-option-row">
  <div class="wcpt-option-label"><?php _e( "Additional query args", 'wc-product-table' ); ?>:</div>
  <div class="wcpt-input">
    <input class="wcpt-query-args-additional" wcpt-model-key="additional_query_args" type="text" />
  </div>
</div>
