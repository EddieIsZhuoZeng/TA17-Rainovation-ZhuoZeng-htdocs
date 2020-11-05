<?php
  if ( ! defined( 'ABSPATH' ) ) {
  	exit; // Exit if accessed directly
  }
?>

<div class="wcpt-editor-clear"></div>

<h1 class="wcpt-page-title dashicons-before dashicons-editor-justify">
  <?php _e( "WooCommerce Product Table", "wc-product-table" ); ?>
</h1>

<div class="wcpt-title-resources">
  <a href="https://wcproducttable.com/documentation/" target="_blank">Docs</a>
  /
  <a href="https://wcproducttable.com/tutorials/" target="_blank">Tuts</a>
  /
  <a href="https://wcproducttable.com/support/" target="_blank">Help</a>
</div>

<div class="wcpt-editor-clear"></div>

<!-- top options -->
<span class="wcpt-table-title-label"><?php _e( "Table name", "wc-product-table" ); ?>:</span>
<input type="text" class="wcpt-table-title" name="" value="<?php echo (isset($_GET['post_id']) ? get_the_title( (int) $_GET['post_id'] ) : ''); ?>" placeholder="Enter name here..." />
<br>
<span class="wcpt-sc-display-label"><?php _e( "Shortcode", "wc-product-table" ); ?>:</span>
<input class="wcpt-sc-display" value="<?php esc_html_e( '[product_table id="'. $post_id .'"]' ); ?>" onClick="this.setSelectionRange(0, this.value.length)" readonly />
<span class="wcpt-shortcode-info wcpt-toggle wcpt-toggle-off">
  <span class="wcpt-toggle-trigger wcpt-noselect">
    <?php echo wcpt_icon('chevron-down', 'wcpt-toggle-is-off'); ?>
    <?php echo wcpt_icon('chevron-up', 'wcpt-toggle-is-on'); ?>
    Shortcode Attributes
  </span>
  <span class="wcpt-toggle-tray">

    <?php echo wcpt_icon('x', 'wcpt-toggle-x'); ?>

    <table>
       <thead>
          <tr>
             <td><strong>Attribute</strong></td>
             <td><strong>Description</strong></td>
          </tr>
       </thead>
       <tbody>
          <tr>
             <td>name</td>
             <td>[product_table name="test table"] <br>Can be used to replace id attribute.</td>
          </tr>
          <tr>
             <td>offset</td>
             <td>[product_table id="1234" offset="6"] <br>Number of initial products to skip over. In this example the shortcode will skip the first 6 products.</td>
          </tr>
          <tr>
             <td>limit</td>
             <td>[product_table id="1234" limit="8"] <br>Limits the number of products per page.</td>
          </tr>
          <tr>
             <td>category</td>
             <td>[product_table id="1234" category="Clothes, Shoes"] <br>Comma separated category names.</td>
          </tr>
          <tr>
             <td>category_relation <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="1234" category_relation="AND"] <br>
               The default relation between categories is 'OR', which means, a product will be included in the filtering results if it is associated with any one of the categories. You can use 'AND' to only include products included in all the categories.
             </td>
          </tr>
          <tr>
             <td>nav_category <br><?php wcpt_pro_badge(); ?></td>
             <td>[product_table id="1234" nav_category="Clothes, Shoes"] <br>You can use this attribute to narrow down the category options that will appear in the table navigation. For example, if the table settings allowed the options Clothes, Shoes, Accessories, Handbags you can use nav_category to reduce it down to just Clothes and Shoes using the above shortcode.</td>
          </tr>
          <!-- <tr>
             <td>exclude_category</td>
             <td>[product_table id="1234" exclude_category="Clothes, Shoes"] <br>Comma separated category names to exclude.</td>
          </tr> -->
          <tr>
             <td>ids</td>
             <td>[product_table id="1234" ids="100, 101, 102"] <br>Comma separated product IDs.</td>
          </tr>
          <tr>
             <td>skus</td>
             <td>[product_table id="1234" skus="sku1, sku2"] <br>Comma separated product Skus.</td>
          </tr>
          <tr>
             <td>include_hidden <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="1234" include_hidden="true"] <br>
               Include products that are hidden from shop / search page as per their product settings.
             </td>
          </tr>
          <tr>
             <td>attribute <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="1234" attribute="Attribute 1: Term 1, Term 2 | Attribute 2: Term 3, Term 4"] <br>
               Pre-selects attributes for the table. Pattern: attribute name/slug, followed by ':', then one or more permitted attribute terms. Then a bang '|' followed by next attribute and so on. Requires global level attributes.
             </td>
          </tr>
          <tr>
             <td>attribute_relation <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="1234" attribute_relation="OR"] <br>
               The default relation between attributes for filtering results is 'AND', which means, a product will be included in the filtering results only if it satisfies all the attribute filters. You can use 'OR' to include products that satisfy atleast one of the filters. 
             </td>
          </tr>
          <tr>
             <td>custom_field <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="1234" custom_field="CF 1: Val 1, Val 2 | CF 2: Val 3, Val 4"] <br>
               Pre-selects meta for the table. Pattern: custom field key, followed by ':', then one or more permitted values. Then a bang '|' followed by next custom field key and so on.
             </td>
          </tr>
          <tr>
             <td>min_price / max_price <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="1234" min_price="10" max_price="100"] <br>
               Pre-selects the price range for products in the table. You can use either or both the attributes in the shortcode.
             </td>
          </tr>
          <tr>
             <td>taxonomy <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="1234" taxonomy="tax-1: Term 1, Term 2 | tax-2: Term 3, Term 4"] <br>
               Pre-selects taxonomy for the table. Pattern: taxonomy slug, followed by ':', then one or more permitted terms. Then a bang '|' followed by next taxonomy and so on.
             </td>
          </tr>
          <tr>
             <td>laptop_scroll_offset</td>
             <td>[product_table id="1234" laptop_scroll_offset="50"] <br>Scroll offset after filtering and pagination on laptop (and larger devices). This can help you prevent the table from hiding behind a floating menu upon auto scroll.</td>
          </tr>
          <tr>
             <td>tablet_scroll_offset</td>
             <td>[product_table id="1234" tablet_scroll_offset="50"]</td>
          </tr>
          <tr>
             <td>phone_scroll_offset</td>
             <td>[product_table id="1234" phone_scroll_offset="50"]</td>
          </tr>
          <tr>
             <td>
               laptop_freeze_left <br>
               laptop_freeze_right <br>
               <?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="1234" laptop_freeze_left="1" laptop_freeze_left="1"] <br>Freeze columns on either side of the table so they remain at a fixed position while the table is scrolled horizonatally.</td>
          </tr>
          <tr>
            <td>
              tablet_freeze_left <br>
              tablet_freeze_right <br>
              <?php wcpt_pro_badge(); ?>
            </td>
             <td>[product_table id="1234" tablet_freeze_left="1" tablet_freeze_left="1"]</td>
          </tr>
          <tr>
            <td>
              phone_freeze_left <br>
              phone_freeze_right <br>
              <?php wcpt_pro_badge(); ?>
            </td>
             <td>[product_table id="1234" phone_freeze_left="1" phone_freeze_left="1"]</td>
          </tr>
          <tr>
             <td>json_ld <br><?php wcpt_pro_badge(); ?></td>
             <td>
               [product_table id="1234" json_ld="true"] <br>
               Prints JSON-LD for the product table shortcode. On archive pages the JSON-LD is printed automatically.
             </td>
          </tr>
          <tr>
             <td>
              lazy_load
              <?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="1234" lazy_load="true"] <br>Speed up page load by lazy loading the table.</td>
          </tr>
          <tr>
             <td>
              product_variations <br><?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="1234" product_variations="true"] <br>This attribute splits product variations into separate rows. It is useful if you wish to show the variations of a product in a table. Please note, any products that are not variations will be hidden from the results. Combine this attribute with 'ids' to reuse the same shortcode to display variation tables of different products, for example: [product_table id="1234" product_variations="true" ids="10"] [product_table id="1234" product_variations="true" ids="11, 12"]</td>
          </tr>
          <tr>
             <td>
              disable_ajax <br><?php wcpt_pro_badge(); ?>
             </td>
             <td>[product_table id="1234" disable_ajax="true"] <br>This can be useful when 3rd party plugin elements are being displayed inside WCPT and require full page reload upon filtering and pagination to show up correctly.</td>
          </tr>
       </tbody>
    </table>

  </span>
</span>

<!-- table creation checklist -->
<?php require_once('partials/checklist.php') ?>

<!-- editor begins -->
<div class="wcpt-editor" wcpt-model-key="data">
  <!-- tab triggers -->
  <div class="wcpt-tab-label wcpt-products-tab active" data-wcpt-tab="products">
    <span class="wcpt-tab-label-text">
      <?php wcpt_icon('box', 'wcpt-tab-label-icon'); ?>
      <?php _e( "Query", "wc-product-table" ); ?>
    </span>
  </div>
  <div class="wcpt-tab-label wcpt-columns-tab" data-wcpt-tab="columns">
    <span class="wcpt-tab-label-text">
      <?php wcpt_icon('menu', 'wcpt-tab-label-icon wcpt-rotate-90'); ?>
      <?php _e( "Columns", "wc-product-table" ); ?>
    </span>
  </div>
  <div class="wcpt-tab-label wcpt-navigation-tab" data-wcpt-tab="navigation">
    <span class="wcpt-tab-label-text">
      <?php wcpt_icon('filter', 'wcpt-tab-label-icon'); ?>
      <?php _e( "Navigation", "wc-product-table" ); ?>
    </span>
  </div>
  <div class="wcpt-tab-label wcpt-style-tab " data-wcpt-tab="style">
    <span class="wcpt-tab-label-text">
      <?php wcpt_icon('type', 'wcpt-tab-label-icon'); ?>
      <?php _e( "Style", "wc-product-table" ); ?>
    </span>
  </div>

  <!-- products tab -->
  <div class="wcpt-editor-tab-products  wcpt-tab-content active" data-wcpt-tab="products" wcpt-model-key="query">
    <?php require_once('partials/query.php') ?>
  </div>

  <!-- columns tab -->
  <div class="wcpt-editor-tab-columns wcpt-tab-content" data-wcpt-tab="columns" wcpt-model-key="columns">

    <?php
      // create the 3 device columns ui
      foreach( array('laptop', 'tablet', 'phone') as $device ){
        ?>
        <!-- <?php echo $device ?> -->
        <div
          class="wcpt-editor-columns-container wcpt-sortable"
          data-wcpt-device="<?php echo $device; ?>"
          wcpt-model-key="<?php echo $device; ?>"
          wcpt-connect-with="[wcpt-controller='device_columns']"
          wcpt-controller="device_columns"
        >
          <h2 class="wcpt-editor-light-heading">
            <span><?php echo ucfirst( $device ); ?> Columns</span>
            <div class="wcpt-column-links"></div>
          </h2><br>
          <?php require('partials/columns.php'); ?>
        </div>
        <hr class="wcpt-editor-columns-device-divider">
        <?php
      }
    ?>

  </div><!-- /columns tab -->

  <!-- style tab -->
  <div class="wcpt-editor-tab-style wcpt-tab-content" data-wcpt-tab="style" wcpt-model-key="style">
    <?php require_once('partials/style.php') ?>
  </div>

  <!-- navigation tab -->
  <div class="wcpt-editor-tab-navigation wcpt-tab-content" data-wcpt-tab="navigation" wcpt-model-key="navigation" wcpt-initial-data="navigation">
    <?php require_once('partials/navigation.php') ?>
  </div>

</div><!-- /.wcpt-editor -->

<!-- save data -->
<div class="wcpt-editor-save-table">
  <form class="wcpt-save-data" action="wcpt_save_table_settings" method="post">
    <!-- hidden fields -->
    <input name="post_id" type="hidden" value="<?php echo $post_id; ?>" />
    <input name="nonce" type="hidden" value="<?php echo wp_create_nonce( "wcpt" ); ?>">
    <input name="title" type="hidden" value="<?php echo ( isset($post_id) ? get_the_title( $post_id ) : __( "Untitled table", "wc-product-table" ) ); ?>" />
    <button type="submit" class="wcpt-editor-save-button button button-primary button-large"><?php _e( "Save settings", "wcpt" ); ?></button>
    <i class="wcpt-saving-icon">
      <?php wcpt_icon('loader', 'wcpt-rotate'); ?>
    </i>
    <br/>
    <div class="wcpt-save-keys">
      (Ctr/Cmd + s)
    </div>
  </form>
</div>

<div class="wcpt-footer">
  <div class="wcpt-support wcpt-footer-note">
    <?php wcpt_icon('alert-circle'); ?>
    <span><?php _e( "Found a bug? Got questions / suggestions? Please reach me for support here: ", "wc-product-table" ); ?><a href="mailto:wcproducttable@gmail.com" target="_blank">wcproducttable@gmail.com</a> | <a href="https://wcproducttable.com/tutorials/" target="_blank">Tutorials</a></span>
  </div>
  <?php if( ! defined( 'WCPT_PRO' ) ): ?>
  <div class="wcpt-support wcpt-footer-note">
    <?php wcpt_icon('zap'); ?>
    <span>
      <?php _e( "WCPT PRO is ready for your shop! Build better tables today!", "wc-product-table" ); ?>
      <a href="https://wcproducttable.com/get-pro/" target="_blank"><?php _e( "View enhancements", "wc-product-table" ); ?></a>
    </span>
  </div>
  <?php endif; ?>

  <!-- <div class="wcpt-support wcpt-footer-note">
    <?php wcpt_icon('heart'); ?>
    <span><?php _e( "Do you like WCPT? Please consider supporting with a 5 star review here: ", "wc-product-table" ); ?><a href="https://wordpress.org/support/plugin/wc-product-table-lite/reviews/" target="_blank">WCPT Reviews</a></span>
  </div> -->
</div>

<!-- icon templates -->
<?php
  $icons = array( 'trash', 'sliders', 'copy', 'x', 'check' );
  foreach( $icons as $icon_name ){
    ?>
    <script type="text/template" id="wcpt-icon-<?php echo $icon_name; ?>">
      <?php echo wcpt_icon( $icon_name ); ?>
    </script>
    <?php
  }
?>

<!-- element partials -->
<?php require_once('partials/element-editor/element-partials.php'); ?>

<!-- required js vars -->
<?php
  $attributes = wc_get_attribute_taxonomies();
?>
<script>wcpt_attributes = <?php echo json_encode( $attributes ) ?>;</script>
<script>var wcpt_icons_url = "<?php echo WCPT_PLUGIN_URL . '/assets/feather'; ?>";</script>

<!-- embedded style -->
<?php
  $svg_cross_path =  plugin_dir_url( __FILE__ ) . 'assets/css/cross.svg';
?>
<style media="screen">
  .wcpt-block-editor-lightbox-screen {
    cursor: url('<?php echo $svg_cross_path; ?>'), auto;
  }
</style>
