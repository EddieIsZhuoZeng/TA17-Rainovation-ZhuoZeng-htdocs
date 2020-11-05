<!-- shortcode -->
<div class="wcpt-editor-row-option">
  <label>
    Shortcode
    <small>placeholders: %product_id% %variation_id% </small>
  </label>
  <textarea wcpt-model-key="shortcode"></textarea>
  <small><strong>Note:</strong> Not every shortcode is going to work inside a WCPT table due to compatibility limitations. Please contact plugin author for support if a required shortcode is incompatible.</small>

  <ul style="font-size: 14px; color: #999">
    <li>
      <strong>[wcpt_wc_quick_view]</strong><br>
      Create a quick view button. Requires the plugin <a href="https://wordpress.org/plugins/woo-quick-view/" target="_blank">WC Quick View</a> to work.
    </li>

    <li>
      <strong>[wcpt_player src="custom field"]</strong><br>
      Creates an audio player button to play / pause. The 'src' shortcode attribute needs to contain a custom field where you enter the audio source.
    </li>    
    <li>
      <strong>[wcpt_remove]</strong><br>
      Creates an 'X' remove button that can remove the row item from cart. The X button only appears after the item has been placed in cart by customer.
    </li>    
    <li>
      <strong>[wcpt_translate default="text" en_US="text" fr_FR="text"]</strong><br>
      Use for translating text. Enter any locale code and text for that locale. If no locale matches the 'default' text will be used.
    </li>    
  </ul>


</div>

<!-- style -->
<?php include( 'style/common.php' ); ?>

<!-- condition -->
<?php include( 'condition/outer.php' ); ?>
