<!-- output format -->
<div class="wcpt-editor-row-option">
  <label>
    Template <?php wcpt_pro_badge(); ?>
  </label>
  <div
    class="<?php wcpt_pro_cover(); ?>"
    wcpt-block-editor=""
    wcpt-be-add-element-partial="add-rating-element"
    wcpt-model-key="template"
  ></div>
</div>

<!-- unrated content -->
<div class="wcpt-editor-row-option">
  <label>
    Content when product is not rated
  </label>
  <div
    wcpt-block-editor=""
    wcpt-be-add-element-partial="add-common-element"
    wcpt-model-key="not_rated"
  ></div>
</div>

<!-- style -->
<?php include( 'style/common.php' ); ?>
