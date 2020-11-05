<div class="wcpt-editor-row-option" wcpt-model-key="style">

  <!-- Container -->
  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id]">

    <span class="wcpt-toggle-label">
      Style for Container
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <?php require( 'common-props.php' ); ?>

  </div>

  <!-- Terms -->
  <div class="wcpt-editor-row-option wcpt-toggle-options wcpt-row-accordion" wcpt-model-key="[id] > div:not(.wcpt-term-separator)">

    <span class="wcpt-toggle-label">
      Style for Terms
      <?php echo wcpt_icon('chevron-down'); ?>
    </span>

    <?php require( 'common-props.php' ); ?>

  </div>

</div>

<div class="wcpt-editor-row-option">
  <label>HTML Class</label>
  <input type="text" wcpt-model-key="html_class" />
</div>
