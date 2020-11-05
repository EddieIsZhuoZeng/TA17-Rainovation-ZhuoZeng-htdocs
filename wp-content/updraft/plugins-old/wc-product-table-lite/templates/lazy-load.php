<?php
  $_sc_attrs = $data['query']['sc_attrs'];
  unset( $_sc_attrs['lazy_load'] );
  if( count( $_sc_attrs ) ){
    $_sc_attrs = ' data-wcpt-sc-attrs="'. esc_attr( json_encode( $_sc_attrs ) ) .'" ';
  }else{
    $_sc_attrs = '';
  }
?>
<div class="wcpt-lazy-load" data-wcpt-table-id="<?php echo $table_id; ?>" <?php echo $_sc_attrs; ?>>
  <div class="wcpt-ll-anim"></div>
</div>
