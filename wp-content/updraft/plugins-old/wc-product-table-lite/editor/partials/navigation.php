<div
  class="wcpt-nav-device"
  wcpt-controller="laptop_navigation"
  wcpt-model-key="laptop"
>

  <div class="wcpt-editor-light-heading">
      Laptop navigation
  </div>
  <div class="wcpt-clear"></div>

  <div class="wcpt-navigation-errors" style="display: none;">
    <strong class="wcpt-navigation-errors__heading">Warning(s)</strong>
    <ul class="wcpt-navigation-errors__warnings"></ul>
  </div>

  <!-- left sidebar -->
  <div class="wcpt-left-sidebar-settings">

    <div class="wcpt-editor-light-heading wcpt-sub">
      Left sidebar
    </div>

    <div
      wcpt-model-key="left_sidebar"
      wcpt-block-editor=""
      wcpt-be-add-element-partial="add-navgiation-header-element"
			wcpt-be-connect-with: ".wcpt-editor-tab-navigation [wcpt-model-key='laptop'] .wcpt-block-editor-row",
      wcpt-initial-data="nav_left_sidebar"
    ></div>

  </div>

  <!-- header -->
  <div class="wcpt-header-settings" wcpt-model-key="header">

    <div class="wcpt-editor-light-heading wcpt-sub">
      Header
    </div>

    <!-- navigation: header rows wrapper -->
    <div
      class="wcpt-sortable wcpt-header-rows-wrapper"
      wcpt-controller="header_rows"
      wcpt-model-key="rows"
    >
      <!-- header row -->
      <div
        class="wcpt-nav-editor-row"
        wcpt-controller="nav_header_row"
        wcpt-model-key="[]"
        wcpt-model-key-index="0"
        wcpt-row-template="nav_header_row"
        wcpt-initial-data="nav_header_row"
      >

        <!-- columns enabled -->
        <select wcpt-model-key="ratio">
          <option value="100-0">Only Left column</option>
          <option value="70-30">Left: 70% | Right: 30%</option>
          <option value="50-50">Left: 50% | Right: 50%</option>
          <option value="30-70">Left: 30% | Right: 70%</option>
          <option value="0-100">Only Right column</option>
        </select>

        <?php wcpt_corner_options(); ?>


        <!-- textarea options -->
        <div class="wcpt-editor-row-option wcpt-editor-filter-row-options wcpt-editor-header-textareas-container" wcpt-model-key="columns">

          <!-- left column -->
          <div class="wcpt-editor-left-column-input-wrapper wcpt-editor-header-textarea-wrapper" wcpt-model-key="left">
            <div
              wcpt-model-key="template"
              wcpt-block-editor
              wcpt-be-add-element-partial="add-navgiation-header-element"
              wcpt-be-connect-with=".wcpt-editor-tab-navigation [wcpt-model-key='laptop'] .wcpt-block-editor-row"
              wcpt-be-add-row="0"
            ></div>
          </div>

          <!-- center column -->
          <div class="wcpt-editor-center-column-input-wrapper wcpt-editor-header-textarea-wrapper" wcpt-model-key="center">
            <div
              wcpt-model-key="template"
              wcpt-block-editor
              wcpt-be-add-element-partial="add-navgiation-header-element"
              wcpt-be-connect-with=".wcpt-editor-tab-navigation [wcpt-model-key='laptop'] .wcpt-block-editor-row"
              wcpt-be-add-row="0"
            ></div>
          </div>

          <!-- right column -->
          <div class="wcpt-editor-right-column-input-wrapper wcpt-editor-header-textarea-wrapper" wcpt-model-key="right">
            <div
              wcpt-model-key="template"
              wcpt-block-editor
              wcpt-be-add-element-partial="add-navgiation-header-element"
              wcpt-be-connect-with=".wcpt-editor-tab-navigation [wcpt-model-key='laptop'] .wcpt-block-editor-row"
              wcpt-be-add-row="0"
            ></div>
          </div>

        </div>

      </div>
      <!-- /header row -->

      <button class="wcpt-button wcpt-add-header_row" wcpt-add-row-template="nav_header_row">
        Add Header Row
      </button>

    </div>
    <!-- /navigation: header rows wrapper -->

  </div>
</div>

<div class="wcpt-nav-device" style="display: none;">
  <div class="wcpt-editor-light-heading">
    Tablet navigation
  </div>

  <div class="wcpt-nav-inherit-notice">Leave empty to inherit from above device settings</div>

  <div
    wcpt-model-key="tablet"
    wcpt-block-editor
    wcpt-be-add-row = '1'
    wcpt-be-add-element-partial="add-responsive-navigation-element"
    wcpt-be-connect-with=".wcpt-editor-tab-navigation [wcpt-model-key='tablet'] .wcpt-block-editor-row"
  ></div>
</div>

<div class="wcpt-nav-device">
  <div class="wcpt-editor-light-heading">
    Responsive navigation
  </div>

  <div class="wcpt-nav-inherit-notice">Leave empty to inherit Laptop navigation</div>

  <div
    wcpt-model-key="phone"
    wcpt-block-editor
    wcpt-be-add-row = '1'
    wcpt-be-add-element-partial="add-responsive-navigation-element"
    wcpt-be-connect-with=".wcpt-editor-tab-navigation [wcpt-model-key='phone'] .wcpt-block-editor-row"
  ></div>
</div>
