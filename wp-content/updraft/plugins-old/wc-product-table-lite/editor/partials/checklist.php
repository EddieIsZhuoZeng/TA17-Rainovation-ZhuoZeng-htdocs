<?php
$data = wcpt_get_table_data();
$checklist = array(
  'query_selected' => ! ( empty( $data['query']['category'] ) && empty( $data['query']['skus'] ) && empty( $data['query']['ids'] ) ),
  'column_element_created' => ! ( empty( $data['columns']['laptop'] ) || wcpt_device_columns_empty( $data['columns']['laptop'] ) ),
  'saved' => ( ! empty( $data['id'] ) && get_post_status($data['id']) == 'publish' ),
);

if( count( array_filter( array_values( $checklist ) ) ) == 3 ){
  return;
}

?>
<div class="wcpt-notice-checklist">
  <span class="wcpt-notice-ck-label">
    <?php wcpt_icon('chevron-right') ?>
    Checklist:
  </span>
  <span class="wcpt-notice-ck-item <?php echo $checklist['query_selected'] ? 'wcpt-done' : '' ?>" data-wcpt-ck="query_selected">
    <?php wcpt_icon('check') ?>
    <?php wcpt_icon('info') ?>
    Create a query
    <span class="wcpt-tltp-content">
      Go to the 'Query' tab below and select some criteria for which products to display. If you leave these settings empty all products in your shop will be displayed. Remember, you can also use shortcode attributes to modify a table's query on the fly.
    </span>
  </span>
  <span class="wcpt-notice-ck-item <?php echo $checklist['column_element_created'] ? 'wcpt-done' : '' ?>" data-wcpt-ck="column_element_created">
    <?php wcpt_icon('check') ?>
    <?php wcpt_icon('info') ?>
    Add columns & elements
    <span class="wcpt-tltp-content">
      Go to the 'Columns' tab below and create at least 1 column in the Laptop Columns section. Then create at least 1 element in this column. Without any settings for column and elements, the table has nothing to display.
    </span>
  </span>
  <span class="wcpt-notice-ck-item <?php echo $checklist['saved'] ? 'wcpt-done' : '' ?>" data-wcpt-ck="saved">
    <?php wcpt_icon('check') ?>
    <?php wcpt_icon('info') ?>
    Save the table settings
    <span class="wcpt-tltp-content">
      You will find the 'Save settings' button at the bottom of the editor. Press it now to save this table, and move it from 'draft' to 'publish' state. Please remember to save your table settings whenever you make changes. You can use Ctrl + s on windows and Cmd + s to conveniently save your settings at any time.
    </span>
  </span>
</div>
