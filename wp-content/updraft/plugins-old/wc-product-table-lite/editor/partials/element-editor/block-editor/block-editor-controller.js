(function($, ctrl){

  ctrl.get_parent = function(elm){
    return $(elm).closest('.wcpt-block-editor').data('wcpt_block_editor');
  };

  ctrl.add_element = function(element, row_index, elm_index){
    var model = WCPT_Block_Editor.Ctrl.get_parent(this).model;
    model.add_element(element, row_index, elm_index);
    model.parent.$elm.children('.wcpt-block-editor-row').eq(row_index).children('.wcpt-element-block').eq(elm_index).addClass('editing');
  }

  ctrl.remove_element = function(element, row_index, elm_index){
    var model = WCPT_Block_Editor.Ctrl.get_parent(this).model;
    model.remove_element(element, row_index, elm_index);
  }

  ctrl.add_row = function(e){
    e.preventDefault();

    var model = WCPT_Block_Editor.Ctrl.get_parent(this).model;
    model.add_row();
  }

  ctrl.edit_row = function(e){

    var $this = $(this),
        $row = $(e.target).closest('.wcpt-block-editor-row'),
        row = $row.data('wcpt-data'),
        row_index = $row.index(),
        parent = WCPT_Block_Editor.Ctrl.get_parent(this);

    // row is not editable
    if( ! parent.config.edit_row_partial ){
      return;
    }

    var $lightbox = parent.view.lightbox({
          partial: parent.config.edit_row_partial,
          attr: {
            'data-row-index': row_index,
          },
          $row: $row,
          duplicate_remove: true,
        });

    // transfer data to block from lightbox
  	dominator_ui.init( $lightbox, row );
  	$lightbox.on('change', function(){
      parent.model.update_row($lightbox.data('wcpt-data'), row_index);
  	});

    var $tray = $( '> .wcpt-block-editor-lightbox-content > .wcpt-block-editor-lightbox-tray', $lightbox );
    // remove
    $( '> .wcpt-block-editor-lightbox-remove', $tray ).on('click', function(){
      $lightbox.trigger('destroy');
      parent.model.remove_row(row_index);
    })

    // duplicate
    $( '> .wcpt-block-editor-lightbox-duplicate', $tray ).on('click', function(){
      parent.model.duplicate_row(row_index);
      $lightbox.trigger('destroy');
    })

  },

  ctrl.delete_row = function(e){

    var $this = $(this),
        $row = $(e.target).closest('.wcpt-block-editor-row'),
        row = $row.data('wcpt-data'),
        row_index = $row.index(),
        parent = WCPT_Block_Editor.Ctrl.get_parent(this);

    // row is not editable
    if( ! parent.config.delete_row ){
      return;
    }

    parent.model.remove_row(row_index);
  },

  ctrl.sort_update = function(e){
    var $editor = $(e.target).closest('.wcpt-block-editor'),
        new_data = [],
        model = WCPT_Block_Editor.Ctrl.get_parent($editor).model;

    // iterate rows
    $editor.children('.wcpt-block-editor-row').each(function(){
      var $row = $(this),
          id = $row.attr('data-id'),
          row = model.get_row(id);
      new_data.push({
        id: row.id,
        style: $.extend({}, row.style),
        elements: [],
      });

      // iterate elements
      $row.children('.wcpt-element-block').each(function(){
        var $element = $(this),
            id = $element.attr('data-id'),
            element = $element.data('wcpt-data');

        new_data[new_data.length - 1].elements.push($.extend({}, element));
      });

    });

    model.set_data(new_data);
  }

  ctrl.edit_element = function(e){

    var $this = $(this),
        $row = $(e.target).closest('.wcpt-block-editor-row'),
        row_index = $row.index(),
        $element = $(e.target).closest('.wcpt-element-block'),
        element = $element.data('wcpt-data'),
        elm_index = $element.length ? $element.index() : $row.children('.wcpt-element-block').length,
        parent = WCPT_Block_Editor.Ctrl.get_parent(this),
        $lightbox = parent.view.lightbox({
          partial: element.type,
          attr: {
            'data-row-index': row_index,
            'data-elm-index': elm_index,
            'data-partial': element.type,
            'wcpt-initial-data': 'element_' + element.type,
          },
          $element: $element,
          duplicate_remove: true,
        });

    // transfer data to block from lightbox
  	dominator_ui.init( $lightbox, $.extend( true, {}, element ) );
  	$lightbox.on('change', function(){
      parent.model.update_element($.extend( true, {}, $lightbox.data('wcpt-data') ), row_index, elm_index);
      parent.view.mark_elm( row_index, elm_index );
  	});

    // focus no first input
    $lightbox.find('input, textarea').first().filter(function(){
      return 'text' == $(this).prop('type');
    }).focus();

    var $tray = $( '> .wcpt-block-editor-lightbox-content > .wcpt-block-editor-lightbox-tray', $lightbox );
    // remove
    $( '> .wcpt-block-editor-lightbox-remove', $tray ).on('click', function(){
      $lightbox.trigger('destroy');
      parent.model.remove_element(row_index, elm_index);
    })

    // duplicate
    $( '> .wcpt-block-editor-lightbox-duplicate', $tray ).on('click', function(){
      parent.model.duplicate_element(row_index, elm_index);
      $lightbox.trigger('destroy');
    })

  },

  ctrl.add_element = function(e){
    e.preventDefault();

    var $this = $(this),
        $row = $(e.target).closest('.wcpt-block-editor-row'),
        row_index = $row.index(),
        $element = $(e.target).closest('.wcpt-element-block'),
        elm_index = $element.length ? $element.index() : $row.children('.wcpt-element-block').length,
        parent = WCPT_Block_Editor.Ctrl.get_parent(this),
        $lightbox = parent.view.lightbox({
          partial: parent.config.add_element_partial,
          attr: {
            'data-row-index': row_index,
            'data-elm-index': elm_index,
          },
          duplicate_remove: false,
        });

    // add element
    $lightbox.on('click', '.wcpt-block-editor-element-type:not(.wcpt-disabled)', function(e){
      var element = {
          id: Date.now(),
          type: $(e.target).attr('data-elm'),
          style: {},
        };
      parent.model.add_element(element, row_index, elm_index);
      // close this lightbox
      $lightbox.trigger('destroy');

      // trigger new element edit
      parent.$elm.children('.wcpt-block-editor-row').eq(row_index).children('.wcpt-element-block').eq(elm_index).click();
    })

  }

})(jQuery, WCPT_Block_Editor.Ctrl);
