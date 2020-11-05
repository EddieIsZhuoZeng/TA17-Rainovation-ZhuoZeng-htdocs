<!-- <?php echo $field; ?> -->
<div
  class="wcpt-editor-row-option wcpt-toggle-options wcpt-search__field"
  wcpt-model-key="<?php echo $field; ?>"
  wcpt-controller="search_rules"
>

  <div class="wcpt-editor-light-heading wcpt-toggle-label">
    <?php echo $heading; ?> rules <?php wcpt_icon('chevron-down'); ?>
  </div>

  <!-- <div class="wcpt-search__field-state wcpt-toggle-escape">
    <span class="wcpt-search__enabled"><?php wcpt_icon('check'); ?> Enabled</span>
    <span class="wcpt-search__disabled"><?php wcpt_icon('x'); ?>Disabled</span>
  </div> -->

  <!-- <div class="wcpt-editor-row-option">
    <label>
      <input type="checkbox" wcpt-model-key="enabled"> Enable search
    </label>
  </div> -->


  <!-- <div 
    class="wcpt-editor-row-option"
    wcpt-panel-condition="prop"
    wcpt-condition-prop="enabled"
    wcpt-condition-val="true"
  >   -->
    <?php include( 'search__rules.php' );  ?>
  <!-- </div> -->

</div>