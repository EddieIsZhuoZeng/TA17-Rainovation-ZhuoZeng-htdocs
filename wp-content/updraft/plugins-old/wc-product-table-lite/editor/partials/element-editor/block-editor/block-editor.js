var WCPT_Block_Editor = {};

(function($){

  WCPT_Block_Editor = {

    $elm: $(),

    Config: {
      add_element_partial: 'add-common-element',
      add_row: true,
      connect_with: '.wcpt-block-editor-row',
      delete_row: true,
    },

    Ctrl: {}, View: {}, Model: {},

    init: function(elm, options){

      // instantiate sub objects
      this.view = Object.create(WCPT_Block_Editor.View);
      this.model = Object.create(WCPT_Block_Editor.Model);
      this.ctrl = Object.create(WCPT_Block_Editor.Ctrl);
      this.config = Object.create(WCPT_Block_Editor.Config);

      // parent refrence
      this.view.parent = this;
      this.model.parent = this;
      this.ctrl.parent = this;

      // relate $elm and this
      this.$elm = $(elm);
      this.$elm.data('wcpt_block_editor', this);

      this.$elm.addClass('wcpt-block-editor');

      // update config
      if( options ){
        // from jQ init params
        $.extend(true, this.config, options);
      }else{
        // from $elm attrs
        //-- add elm
        if( this.$elm.attr('wcpt-be-add-element-partial') ){
          this.config.add_element_partial = this.$elm.attr('wcpt-be-add-element-partial');
        }
        //-- add row
        this.config.add_row = !! parseInt( this.$elm.attr('wcpt-be-add-row') ); // bool
        //-- delete row
        if( this.$elm.attr('wcpt-be-delete-row') == 'string' === '0' ){
          this.config.delete_row = false;
        }
        //-- connect
        if( this.$elm.attr('wcpt-be-connect-with') ){
          this.config.connect_with = this.$elm.attr('wcpt-be-connect-with');
        }
      }

      // attach controller
      // -- add row
      this.$elm.on('click', '.wcpt-block-editor-add-row', this.ctrl.add_row);
      // -- sort update
      this.$elm.on('sortupdate', this.ctrl.sort_update);
      // lightbox
      // -- open
      // -- -- to edit element
      this.$elm.on('click', '.wcpt-element-block', this.ctrl.edit_element);
      // -- -- to add element
      this.$elm.on('click', '.wcpt-block-editor-add-element', this.ctrl.add_element);
      // -- -- row settings
      this.$elm.on('click', '.wcpt-block-editor-edit-row', this.ctrl.edit_row);
      // -- -- delete settings
      this.$elm.on('click', '.wcpt-block-editor-delete-row', this.ctrl.delete_row);

      // set data and render
      var data = false;

      // -- from $elm
      if( this.$elm.data('wcpt-data') ){
        data = this.$elm.data('wcpt-data');
      }

      // -- from config
      if( this.config.data ){
        data = $.extend( true, {}, this.config.data );
      }

      // -- convert text to elm
      if( typeof data == 'string' ){
        data = [{
          id: Date.now(),
          style: {},
          elements: [{
            type: 'text',
            text: data,
          }],
        }];
      }
      if( ! data ){
        data = [{
          id: Date.now(),
          style: {},
          condition: {},
          elements: [],
        }];
      }

      this.model.set_data(data); // also renders

    },
  };

  $.fn.wcpt_block_editor = function(options) {
    return this.each(function() {
      if( ! $(this).data('wcpt_block_editor') ){
        // init
        var block_editor = Object.create(WCPT_Block_Editor);
        block_editor.init(this, options);
      }
    });
  };

})(jQuery)
