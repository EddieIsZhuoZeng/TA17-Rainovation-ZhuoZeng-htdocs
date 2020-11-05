<h2>Edit Cell Row</h2>

<?php
  include( 'condition/outer.php' );
?>

<div class="wcpt-editor-row-option" wcpt-model-key="style">

  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id]">

    <span class="wcpt-toggle-label">
      Style for Row
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <?php require( 'style/common-props.php' ); ?>

  </div>

</div>

<div class="wcpt-editor-row-option">
  <label>Additional CSS Class</label>
  <input type="text" wcpt-model-key="html_class" />
</div>
