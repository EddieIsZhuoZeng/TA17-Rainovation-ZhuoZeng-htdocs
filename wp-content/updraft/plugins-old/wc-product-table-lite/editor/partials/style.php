<!-- CSS -->
<div class="wcpt-editor-option-row">
  <label>
    CSS
    <span class="wcpt-selectors wcpt-toggle wcpt-toggle-off">
      <span class="wcpt-toggle-trigger wcpt-noselect">
        <?php echo wcpt_icon('chevron-down', 'wcpt-toggle-is-off'); ?>
        <?php echo wcpt_icon('chevron-up', 'wcpt-toggle-is-on'); ?>
        Show selectors
      </span>
      <span class="wcpt-toggle-tray">

        <?php echo wcpt_icon('x', 'wcpt-toggle-x'); ?>

        <table>
           <thead>
              <tr>
                 <td><strong>Selector</strong></td>
                 <td><strong>Description</strong></td>
              </tr>
           </thead>
           <tbody>
              <tr>
                 <td>[container]</td>
                 <td>Target the entire container with table and navigation elements (filters, pagination)</td>
              </tr>
              <tr>
                 <td>[table]</td>
                 <td>Target the table</td>
              </tr>
              <tr>
                 <td>[heading_row]</td>
                 <td>Target the heading row</td>
              </tr>
              <tr>
                 <td>[heading_cell]</td>
                 <td>Target the heading cells</td>
              </tr>
              <tr>
                 <td>[heading_cell_even]</td>
                 <td>Target even heading cells</td>
              </tr>
              <tr>
                 <td>[heading_cell_odd]</td>
                 <td>Target odd heading cells</td>
              </tr>
              <tr>
                 <td>[row]</td>
                 <td>Target the table row element</td>
              </tr>
              <tr>
                 <td>[row_even]</td>
                 <td>Target even rows</td>
              </tr>
              <tr>
                 <td>[row_odd]</td>
                 <td>Target odd rows</td>
              </tr>
              <tr>
                 <td>[cell]</td>
                 <td>Target all the table cells</td>
              </tr>
              <tr>
                 <td>[cell_even]</td>
                 <td>Target even column cells</td>
              </tr>
              <tr>
                 <td>[cell_odd]</td>
                 <td>Target odd column cells</td>
              </tr>
              <tr>
                 <td>[tablet] ... [/tablet]</td>
                 <td>Replace ... with the css code meant only for tablet size devices</td>
              </tr>
              <tr>
                 <td>[phone] ... [/phone]</td>
                 <td>Replace ... with the css code meant only for phone size devices</td>
              </tr>
           </tbody>
        </table>

      </span>
    </span>
  </label>
  <textarea class="wcpt-style" id="wcpt-css" wcpt-model-key="css" placeholder="<?php _e( "Enter custom CSS here...", "wc-product-table" ); ?>"></textarea>
</div>

<?php
  foreach( array( 'laptop', 'tablet', 'phone' ) as $device ){
    ?>

    <!-- <?php echo $device . ' style'; ?> -->
    <div class="wcpt-device-style" data-wcpt-device="<?php echo $device; ?>" wcpt-model-key="<?php echo $device; ?>">
      <h2 class="wcpt-editor-light-heading">
        <?php
          echo ucfirst($device) . ' Style';
          // inheritance option
          if( in_array( $device, array( 'phone', 'tablet' ) ) ){
            $label = "Inherit " . ( $device == 'tablet' ? 'Laptop' : 'Tablet' ) . " Style";
            $model_key =  str_replace( ' ', '_', strtolower($label) );
            ?>
            <div class="wcpt-inheritance-option">
              <label>
                <input type="checkbox" wcpt-model-key="<?php echo $model_key; ?>">
                <?php echo $label; ?>
              </label>
            </div>
            <?php
          }
        ?>
      </h2>
      <?php
        foreach(
          array(
            'container'  => '',
            'headings'  => '',
            'cells'     => '[container] .wcpt-cell',
            'odd_rows'  => '[container] tr.wcpt-odd',
            'even_rows' => '[container] tr.wcpt-even',
            'borders'   => '',
          ) as $elm => $selector
        ){
          ?>
          <!-- <?php echo $elm; ?> -->
          <div class="wcpt-editor-option-row wcpt-toggle-options wcpt-<?php echo $elm; ?>-style" <?php if( $selector ) echo 'wcpt-model-key="'. $selector .'"' ?>>
            <span class="wcpt-toggle-label">
              <?php echo str_replace( '_' , ' ', ucfirst( $elm ) ); ?>
              <?php wcpt_icon( 'chevron-down' ) ?>
            </span>

            <?php require( __DIR__ . '/style/'. $elm .'.php' ); ?>
          </div>
          <?php
        }
      ?>
    </div>

    <?php

  }
?>
