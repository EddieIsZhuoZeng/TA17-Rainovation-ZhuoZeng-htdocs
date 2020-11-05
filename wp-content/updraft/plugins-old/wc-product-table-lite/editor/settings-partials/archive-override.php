<?php
  $wcpt_post_query = new WP_Query(array(
    'post_type' => 'wc_product_table',
    'posts_per_page' => -1,
  ));

  ob_start();
  echo '<option value="">*None* &mdash; use default WC grid</option>';
  echo '<option value="custom">*Custom* &mdash; manually enter table shortcode </option>';
  if( $wcpt_post_query->have_posts() ){
    while( $wcpt_post_query->have_posts() ){
      $wcpt_post_query->the_post();
      echo '<option value="'. get_the_ID() .'">'. get_the_title() .'</option>';
    }
  }
  $wcpt_table_options = ob_get_clean();


  function wcpt_custom_shortcode_textarea($condition_prop, $condition_val, $model_key) {
    ?>
      <div
        wcpt-panel-condition="prop"
        wcpt-condition-prop="<?php echo $condition_prop; ?>"
        wcpt-condition-val="<?php echo $condition_val; ?>"
      >
        <textarea class='wcpt-editor-custom-table-shortcode' wcpt-model-key='<?php echo $model_key; ?>' placeholder="Enter your [product_table] shortcode here..."></textarea>
      </div>
    <?php
  }
?>
<div class="wcpt-toggle-options" wcpt-model-key="archive_override">

  <div class="wcpt-editor-light-heading wcpt-toggle-label">
    Archive override
    <?php wcpt_pro_badge(); ?>
    <?php echo wcpt_icon('chevron-down'); ?>
  </div>

  <div class="<?php wcpt_pro_cover(); ?>">

    <div class="wcpt-notice">Please <a target="_blank" href="https://wcproducttable.com/documentation/enable-archive-override/">read here</a> how to enable this feature for your theme.</div>

    <!-- default -->
    <div class="wcpt-editor-row-option">
      <label>Default override table</label>
      <select wcpt-model-key='default'>
        <?php echo $wcpt_table_options; ?>
      </select>
      <?php wcpt_custom_shortcode_textarea('default', 'custom', 'default_custom') ?>
    </div>

    <?php
      // add the default option to table options
      $wcpt_table_options = '<option value="default">Default override table</option>' . $wcpt_table_options;
    ?>

    <!-- shop -->
    <div class="wcpt-editor-row-option">
      <label>Shop override table</label>
      <select wcpt-model-key='shop'>
        <?php echo $wcpt_table_options; ?>
      </select>
      <?php wcpt_custom_shortcode_textarea('shop', 'custom', 'shop_custom') ?>
    </div>

    <!-- search -->
    <div class="wcpt-editor-row-option">
      <label>Search override table</label>
      <select wcpt-model-key='search'>
        <?php echo $wcpt_table_options; ?>
      </select>
      <?php wcpt_custom_shortcode_textarea('search', 'custom', 'search_custom') ?>
    </div>

    <!-- category -->
    <div class="wcpt-toggle-options wcpt-editor-row-option" wcpt-model-key='category' style="padding-left: 40px;">
      <div class="wcpt-editor-light-heading wcpt-toggle-label">Category override <?php echo wcpt_icon('chevron-down'); ?></div>

      <div class="wcpt-editor-row-option">
        <label>Default category override table</label>
        <select wcpt-model-key='default'>
          <?php echo $wcpt_table_options; ?>
        </select>
        <?php wcpt_custom_shortcode_textarea('default', 'custom', 'custom') ?>
      </div>

      <!-- additional category overides -->
      <?php
        $wcpt_category_select = wp_dropdown_categories( array(
          'echo'          => 0,
          'value_field'	  => 'slug',
          'taxonomy'      => 'product_cat',
          'hierarchical'  => 1,
        ) );
        $wcpt_category_select = str_replace( '<select ', '<select multiple wcpt-model-key="category" ', $wcpt_category_select );
      ?>

      <div
        class="wcpt-editor-row-option"
        wcpt-controller="archive_override_rows"
        wcpt-model-key="other_rules"
      >
        <!-- row -->
        <div
          class="wcpt-editor-row-option wcpt-archive-overrider-rule"
          wcpt-controller="archive_override_row"
          wcpt-model-key="[]"
          wcpt-model-key-index="0"
          wcpt-row-template="category-archive-override-rules"
          wcpt-initial-data=""
        >
          <?php wcpt_corner_options(); ?>

          <div class="wcpt-editor-row-option">
            <label>Category</label>
            <?php echo $wcpt_category_select; ?>
          </div>

          <div class="wcpt-editor-row-option">
            <label>Override table</label>
            <select wcpt-model-key='table_id'>
              <?php echo $wcpt_table_options; ?>
            </select>
            <?php wcpt_custom_shortcode_textarea('table_id', 'custom', 'custom') ?>
          </div>

        </div>
        <!-- /row -->

        <button class="wcpt-button" wcpt-add-row-template="category-archive-override-rules">
          Add a rule
        </button>
      </div>

    </div>

    <!-- attribute -->
    <div class="wcpt-toggle-options wcpt-editor-row-option" wcpt-model-key='attribute' style="padding-left: 40px;">
      <div class="wcpt-editor-light-heading wcpt-toggle-label">Attribute override <?php echo wcpt_icon('chevron-down'); ?></div>

      <div class="wcpt-editor-row-option">
        <label>Default attribute override table</label>
        <select wcpt-model-key='default'>
          <?php echo $wcpt_table_options; ?>
        </select>
        <?php wcpt_custom_shortcode_textarea('default', 'custom', 'custom') ?>
      </div>


      <!-- additional attribute overides -->
      <?php
        $wcpt_attributes = wc_get_attribute_taxonomies();
        $wcpt_attribute_select = '<select multiple wcpt-model-key="attribute">';
        foreach( $wcpt_attributes as $attribute ){
          $wcpt_attribute_select .= '<option value="'. $attribute->attribute_name .'">'. $attribute->attribute_label .'</option>';
        }
        $wcpt_attribute_select .= '</select>';

        // echo $wcpt_attribute_select;
      ?>

      <div
        class="wcpt-editor-row-option"
        wcpt-controller="archive_override_rows"
        wcpt-model-key="other_rules"
      >
        <!-- row -->
        <div
          class="wcpt-editor-row-option wcpt-archive-overrider-rule"
          wcpt-controller="archive_override_row"
          wcpt-model-key="[]"
          wcpt-model-key-index="0"
          wcpt-row-template="attribute-archive-override-rules"
          wcpt-initial-data="archive_override_rule"
        >
          <?php wcpt_corner_options(); ?>

          <div class="wcpt-editor-row-option">
            <label>Attribute</label>
            <?php echo $wcpt_attribute_select; ?>
          </div>

          <div class="wcpt-editor-row-option">
            <label>Override table</label>
            <select wcpt-model-key='table_id'>
              <?php echo $wcpt_table_options; ?>
            </select>
            <?php wcpt_custom_shortcode_textarea('table_id', 'custom', 'custom') ?>
          </div>

        </div>
        <!-- /row -->

        <button class="wcpt-button" wcpt-add-row-template="attribute-archive-override-rules">
          Add a rule
        </button>
      </div>

    </div>

    <!-- tag -->
    <div class="wcpt-toggle-options wcpt-editor-row-option" wcpt-model-key='tag' style="padding-left: 40px;">
      <div class="wcpt-editor-light-heading wcpt-toggle-label">Tag override <?php echo wcpt_icon('chevron-down'); ?></div>

      <div class="wcpt-editor-row-option">
        <label>Default tag override table</label>
        <select wcpt-model-key='default'>
          <?php echo $wcpt_table_options; ?>
        </select>
        <?php wcpt_custom_shortcode_textarea('default', 'custom', 'custom') ?>
      </div>


      <!-- additional tag overides -->
      <?php
        $wcpt_tag_select = wp_dropdown_categories( array(
          'echo'        => 0,
          'value_field'	=> 'slug',
          'taxonomy'    => 'product_tag',
        ) );
        $wcpt_tag_select = str_replace( '<select ', '<select multiple wcpt-model-key="tag" ', $wcpt_tag_select );
      ?>

      <div
        class="wcpt-editor-row-option"
        wcpt-controller="archive_override_rows"
        wcpt-model-key="other_rules"
      >
        <!-- row -->
        <div
          class="wcpt-editor-row-option wcpt-archive-overrider-rule"
          wcpt-controller="archive_override_row"
          wcpt-model-key="[]"
          wcpt-model-key-index="0"
          wcpt-row-template="tag-archive-override-rules"
          wcpt-initial-data="archive_override_rule"
        >
          <?php wcpt_corner_options(); ?>

          <div class="wcpt-editor-row-option">
            <label>Tag</label>
            <?php echo $wcpt_tag_select; ?>
          </div>

          <div class="wcpt-editor-row-option">
            <label>Override table</label>
            <select wcpt-model-key='table_id'>
              <?php echo $wcpt_table_options; ?>
            </select>
            <?php wcpt_custom_shortcode_textarea('table_id', 'custom', 'custom') ?>
          </div>

        </div>
        <!-- /row -->

        <button class="wcpt-button" wcpt-add-row-template="tag-archive-override-rules">
          Add a rule
        </button>
      </div>

    </div>

  </div>

</div>
