<div class="wcpt-toggle-options" wcpt-model-key="cart_widget">
  <div class="wcpt-editor-light-heading wcpt-toggle-label">Cart widget <?php echo wcpt_icon('chevron-down'); ?></div>

  <div class="wcpt-editor-row-option">
    <label>Toggle</label>
    <label><input type="radio" wcpt-model-key="toggle" value="enabled"> Enabled</label>
    <label><input type="radio" wcpt-model-key="toggle" value="disabled"> Disabled</label>
  </div>

  <div class="wcpt-editor-row-option">
    <label>Responsive toggle</label>
    <label><input type="radio" wcpt-model-key="r_toggle" value="enabled"> Enabled</label>
    <label><input type="radio" wcpt-model-key="r_toggle" value="disabled"> Disabled</label>
  </div>

  <div class="wcpt-editor-row-option" wcpt-model-key="style">
    <label style="font-weight: bold;">Style</label>

    <div class="wcpt-editor-row-option">
      <label>
        Bottom offset (px)
      </label>
      <input type="number" wcpt-model-key="bottom" />
    </div>

    <div class="wcpt-editor-row-option">
      <label>
        Background color
      </label>
      <input type="text" wcpt-model-key="background-color" />
    </div>

    <div class="wcpt-editor-row-option">
      <label>
        Border color
      </label>
      <input type="text" wcpt-model-key="border-color" />
    </div>

    <div class="wcpt-editor-row-option">
      <label>
        Font color
      </label>
      <input type="text" wcpt-model-key="color" />
    </div>

    <div class="wcpt-editor-row-option">
      <label>
        Font size
      </label>
      <input type="text" wcpt-model-key="font-size" />
    </div>

    <div class="wcpt-editor-row-option">
      <label>
        Width
      </label>
      <input type="text" wcpt-model-key="width" />
    </div>

  </div>

  <div class="wcpt-editor-row-option" wcpt-model-key="labels">
    <label style="font-weight: bold;">
      Labels
      <small>For multiple translations enter one per line like this:</small>
      <small style="line-height: 1.5;">
        en_US: Item <br>
        fr_FR: Article <br>
      </small>
    </label>

    <div class="wcpt-editor-row-option">
      <label>
        'Item' (singular)
      </label>
      <textarea wcpt-model-key="item"></textarea>
    </div>

    <div class="wcpt-editor-row-option">
      <label>
        'Items' (plural)
      </label>
      <textarea wcpt-model-key="items"></textarea>
    </div>

    <div class="wcpt-editor-row-option">
      <label>
        'View Cart'
      </label>
      <textarea wcpt-model-key="view_cart"></textarea>
    </div>

    <div class="wcpt-editor-row-option">
      <label>
        'Extra charges may apply'
      </label>
      <textarea wcpt-model-key="extra_charges"></textarea>
    </div>
  </div>

</div>
