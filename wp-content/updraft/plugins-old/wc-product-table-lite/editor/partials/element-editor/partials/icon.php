<div class="wcpt-editor-row-option">
  <label>Icon name</label>
  <select class="wcpt-select-icon" wcpt-model-key="name" style="width: 100%;">
    <?php
      $path = WCPT_PLUGIN_PATH . 'assets/feather';
      $icons = array_diff( scandir( $path ), array( '..', '.', '.DS_Store' ) );
      foreach( $icons as $icon ){
        if( $icon ){
          $icon_name = substr( $icon, 0, -4 );
          echo '<option value="'. $icon_name .'">'. str_replace( '-', ' ', ucfirst( $icon_name ) ) .'</option>';
        }
      }
    ?>
  </select>
</div>

<div class="wcpt-editor-row-option">
  <label>
    Title
    <small>Text that shows up on mouse hover</small>
  </label>
  <input type="text" wcpt-model-key="title" />
</div>

<div class="wcpt-editor-row-style-options" wcpt-model-key="style" style="margin-top: 25px;">

  <div class="wcpt-wrapper" wcpt-model-key="[id]">

    <!-- font-size -->
    <div class="wcpt-editor-row-option">
      <label>Size</label>
      <input type="text" wcpt-model-key="font-size">
    </div>

    <!-- font-color -->
    <div class="wcpt-editor-row-option">
      <label>Stroke color</label>
      <input type="text" wcpt-model-key="color" placeholder="#000" class="wcpt-color-picker" >
    </div>

    <!-- fill -->
    <div class="wcpt-editor-row-option">
      <label>Fill color</label>
      <input type="text" wcpt-model-key="fill" class="wcpt-color-picker" >
    </div>

    <!-- stroke-width -->
    <div class="wcpt-editor-row-option">
      <label>Thickness</label>
      <input type="text" wcpt-model-key="stroke-width">
    </div>

    <!-- background-color -->
    <!-- <div class="wcpt-editor-row-option">
      <label>Background color</label>
      <input type="text" wcpt-model-key="background-color" class="wcpt-color-picker" >
    </div> -->

    <!-- margin -->
    <div class="wcpt-editor-row-option">
      <label>Margin</label>
      <input type="text" wcpt-model-key="margin-top" placeholder="top">
      <input type="text" wcpt-model-key="margin-right" placeholder="right">
      <input type="text" wcpt-model-key="margin-bottom" placeholder="bottom">
      <input type="text" wcpt-model-key="margin-left" placeholder="left">
    </div>

  </div>

</div>
