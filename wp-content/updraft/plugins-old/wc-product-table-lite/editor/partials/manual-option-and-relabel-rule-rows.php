<?php
  if( empty( $type ) ){
    $type = 'manual_options';
  }

  if( $type == 'manual_options' ){
    $model_key = 'manual_options';
    $controller = 'manual_options';
    $row_template = 'manual_option';
    $initial_data = 'manual_option';
    $label = 'Label';
    $compare_label = 'This option will select products with';

  }else if( $type == 'relabel_rules' ){
    $model_key = 'relabel_rules';
    $controller = 'relabel_rules';
    $row_template = 'relabel_rule';
    $initial_data = 'relabel_rule';
    $label = 'Relabel';
    $compare_label = 'Relabel for';
  }
?>

<div
  class="wcpt-label-options-rows-wrapper wcpt-sortable wcpt-editor-row-option"
  wcpt-model-key="<?php echo $model_key; ?>"
>
  <div
    class="wcpt-editor-row wcpt-editor-custom-label-setup"
    wcpt-controller="<?php echo $controller; ?>"
    wcpt-model-key="[]"
    wcpt-model-key-index="0"
    wcpt-row-template="<?php echo $row_template; ?>"
    wcpt-initial-data="<?php echo $initial_data; ?>"
  >

    <!-- compare -->
    <label><?php echo $compare_label; ?></label>
    <select wcpt-model-key="compare">
      <option value="=">A specific custom field value</option>
      <option value="BETWEEN">Custom field values within a range</option>
    </select>

    <!-- value -->
    <div class="wcpt-editor-row-option"
      wcpt-panel-condition="prop"
      wcpt-condition-prop="compare"
      wcpt-condition-val="="
    >
      <label>Custom field value</label>
      <input type="text" wcpt-model-key="value" />
    </div>

    <!-- min value -->
    <div class="wcpt-editor-row-option"
      wcpt-panel-condition="prop"
      wcpt-condition-prop="compare"
      wcpt-condition-val="BETWEEN"
    >
      <label>Custom field min value</label>
      <input type="number" wcpt-model-key="min_value" />
    </div>

    <!-- max value -->
    <div class="wcpt-editor-row-option"
      wcpt-panel-condition="prop"
      wcpt-condition-prop="compare"
      wcpt-condition-val="BETWEEN"
    >
      <label>Custom field max value</label>
      <input type="number" wcpt-model-key="max_value" />
    </div>

    <!-- label -->
    <div class="wcpt-editor-row-option">
      <label><?php echo $label; ?></label>
      <textarea type="text" wcpt-model-key="label"></textarea>
    </div>

    <?php
      if( $type == 'relabel_rules' ){
    ?>
    <!-- style -->
    <div class="wcpt-editor-row-option" wcpt-model-key="style">

      <!-- color -->
      <div class="wcpt-editor-row-option">
        <label>Font color</label>
        <input type="text" wcpt-model-key="color" class="wcpt-color-picker" />
      </div>

      <!-- background color -->
      <div class="wcpt-editor-row-option">
        <label>Background color</label>
        <input type="text" wcpt-model-key="background-color" class="wcpt-color-picker" />
      </div>

    </div>
    <?php
      }
    ?>

    <!-- corner options -->
    <?php wcpt_corner_options(); ?>

  </div>

  <button
    class="wcpt-button"
    wcpt-add-row-template="<?php echo $row_template; ?>"
  >
    Add an Option
  </button>

</div>
