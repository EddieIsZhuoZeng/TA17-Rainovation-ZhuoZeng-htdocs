var dominator_ui = {};

jQuery(function($){
  dominator_ui = {

    init: function($elm, data){

      if( typeof $elm == 'string' ){
        $elm = $($elm);
      }

      var _                   = this,
          $controllers        = $elm.find('[wcpt-controller]').addBack(),
          $data_nodes         = $elm.find('[wcpt-model-key]').addBack(),
          $value_forwards     = $elm.find('[wcpt-value-forward]'),
          $content_templates  = $elm.find('[wcpt-content-template]'),
          $row_wrapper        = $elm.find('[wcpt-row-template]').parent(),
          $add_row            = $elm.find('[wcpt-add-row-template]'),
          $duplicate_row      = $elm.find('[wcpt-duplicate-row]'),
          $move_row_up        = $elm.find('[wcpt-move-up]'),
          $move_row_down      = $elm.find('[wcpt-move-down]'),
          $remove_row         = $elm.find('[wcpt-remove-row]'),
          $panel_conditions   = $elm.find('[wcpt-panel-condition]');

      // initial data
      if( $elm.attr('wcpt-initial-data') ){
        var initial_data_copy = {};
        $.extend( true, initial_data_copy, _.initial_data[ $elm.attr('wcpt-initial-data') ] );
        var data = $.extend( {}, initial_data_copy, data );
      }

      // register relations
      $controllers.add($data_nodes).each(function(){
        var $child      = $(this),
            $parent     = $child.parent().closest('[wcpt-model-key]'),
            $children   = $parent.data('wcpt-children');
        if( ! $children ){
          $children = $();
        }
        $children = $children.add($child);
        $parent.data('wcpt-children', $children);
        $child.data('wcpt-parent', $parent);
      })

      $value_forwards.each(function(){
        var $this       = $(this),
            selector    = $this.attr('wcpt-value-forward'),
            $data_node  = $this.closest(selector);
        $this.data('wcpt-data-node', $data_node);
        $data_node.data('wcpt-value-forward', $this);
      })

      $content_templates.each(function(){
        var $this       = $(this),
            $data_node  = $this.closest('[wcpt-model-key]');

        $this.data('wcpt-data-node', $data_node);

        var  $content_templates  = $data_node.data('wcpt-content-templates');
        if( ! $content_templates ){
          $content_templates = $();
        }
        $content_templates = $content_templates.add($this);
        $data_node.data('wcpt-content-templates', $content_templates);
      })

      $panel_conditions.each(function(){
        var $this       = $(this),
            $data_node  = $this.closest('[wcpt-model-key]');

        $this.data('wcpt-parent', $data_node);

        var  $panel_conditions  = $data_node.data('wcpt-panel-conditions');
        if( ! $panel_conditions ){
          $panel_conditions = $();
        }
        $panel_conditions = $panel_conditions.add($this);
        $data_node.data('wcpt-panel-conditions', $panel_conditions);
      })

      // row sorting
      $row_wrapper.each(function(){
        var $wrapper      = $(this),
            connect_with  = $wrapper.attr('wcpt-connect-with');

        if( $wrapper.hasClass('wcpt-sortable') ){
          $wrapper.sortable({
            handle: function(){ return $wrapper.find('.wcpt-sortable-handle').first(); },
            connectWith: connect_with,
            items: '>[wcpt-model-key="[]"]',
            update: function(e, ui){
              // ensure 'add row' button remains beneath the rows
              var $button = ui.item.siblings('.wcpt-button[wcpt-add-row-template]'),
                  $last_row = ui.item.siblings().addBack().filter('[wcpt-row-template].wcpt-last-row')
              if(
                $button.length && $last_row.length && ( $button.index() < $last_row.index() )
              ){
                $button.detach().insertAfter($last_row);
              }

            }
          })
        }
      })


      // row controllers
      $add_row
        .add($duplicate_row)
        .add($remove_row)
        .add($move_row_up)
        .add($move_row_down)
          .each(function(){
            var $this         = $(this),
                $row          = $this.closest('[wcpt-model-key]'),
                $rows_wrapper = $row.data('wcpt-parent'),
                rows_data     = $rows_wrapper.data('wcpt-data');
            $this.data('wcpt-parent', $row);
          })

      $remove_row.each(function(){
        var $this         = $(this),
            $row          = $this.data('wcpt-parent'),
            min_rows      = $row.attr('wcpt-min-rows') ? parseInt( $row.attr('wcpt-min-rows') ) : 0,
            $rows_wrapper = $row.data('wcpt-parent'),
            rows_data     = $rows_wrapper.data('wcpt-data');

        if( min_rows && typeof rows_data != 'undefined' && Object.prototype.toString.call(rows_data) == '[object Array]' ){
          if( rows_data.length > min_rows ){
            $this.removeAttr('wcpt-disabled');
          }else{
            $this.attr('wcpt-disabled', '');
          }
        }
      })

      // register row templates
      $elm.find('[wcpt-row-template]').each(function(){
        var $template     = $(this),
            template_name = $template.attr('wcpt-row-template');
        if( ! _.row_templates[template_name] ){
          _.row_templates[template_name] =  $template[0].outerHTML;
          $template.attr('wcpt-disabled', '');
        }
      })

      // radio buttons 'name' attribute
      $controllers.add($data_nodes).each(function(){
        var $this       = $(this),
            $children   = $this.data('wcpt-children'),
            fixed       = [];

        if( ! $children || ! $children.length ){
          return;
        }

        $children.filter('[type="radio"]').each(function(){
          var $radio = $(this),
              model_key = $radio.attr('wcpt-model-key');

          if( -1 !== $.inArray(model_key, fixed) ){
            return;
          }

          fixed.push(model_key);

          var name = 'wcpt-' + model_key + '-' + new Date().getTime();

          $children.filter('[type="radio"][wcpt-model-key="'+ model_key +'"]').each(function(){
            var $sibling = $(this);
            $sibling.attr('name', name);
          })
        })

      })

      // bind change handler
      $controllers.add($data_nodes).off('change keyup').on( 'change keyup', function(e){

        var $this    = $(this),
            key      = $this.attr('wcpt-model-key'),
            index    = $this.attr('wcpt-model-key-index'),
            $parent  = $this.data('wcpt-parent'),
            val;

        if( -1 !== $.inArray( $this[0].tagName, ['SELECT', 'INPUT', 'TEXTAREA'] ) ){
          if( -1 !== $.inArray( $this[0].type, ['checkbox'] ) ){
            val = $this[0].checked;
          }else{
            val = $this.val();
          }
        }else{
          val = $this.data('wcpt-data');
        }

        if( $parent && $parent.length ){
          var parent_data  = $parent.data('wcpt-data');
          // feed data to parent
          switch(key) {
              // array
              case '[]':
                  if( ! parent_data ){
                    parent_data = [];
                  }
                  parent_data[index] = val;
                  break;
              // object key
              default:

                if( key.length > 2 && key.slice(-2) == '[]' ){
                // input:checkbox[]

                  var true_key = key.slice(0, -2),
                      val = $this.val();
                  if( typeof parent_data[true_key] == 'undefined' || ! Array.isArray( parent_data[true_key] ) ){
                    parent_data[true_key] = [];
                  }

                  if( $this.prop('checked') ){
                    parent_data[true_key].push(val);

                  }else{
                    var index = parent_data[true_key].indexOf(val);
                    if (index > -1) {
                      parent_data[true_key].splice(index, 1);
                    }

                  }

                }else{
                // other input

                  if( ! parent_data || Array.isArray(parent_data) ){
                    parent_data = {};

                  }
                  parent_data[key] = val;

                }
          }
          $parent.data('wcpt-data', parent_data);
        }

        var $content_templates = $this.data('wcpt-content-templates');
        if( $content_templates && typeof val == 'object' ){
          $content_templates.each(function(){
            var $content_template  = $(this),
                _key               = $content_template.attr('wcpt-content-template'),
                _val               = val.hasOwnProperty(_key) ? val[_key] : '',
                value_modifier     = $content_template.attr('wcpt-value-modifier');
            if( typeof _.value_modifiers[value_modifier] == 'function' ){
              _val = _.value_modifiers[value_modifier](_val);
            }
            $content_template.text(_val);
          })
        }

        // panel conditions
        var $panel_conditions = $this.data('wcpt-panel-conditions');
        if( $panel_conditions ){
          var condition_context = $this.data('wcpt-data');
          $panel_conditions.each(function(){
            var $this         = $(this),
                condition_key = $this.attr('wcpt-panel-condition');

            if( _.panel_conditions[condition_key]($this, condition_context) ){
              $this.removeAttr('wcpt-disabled');
              var enable_handler = $this.attr('wcpt-panel-enable');
              if( enable_handler ){
                _.panel_enable[enable_handler]( $this );
              }

            }else{
              $this.attr('wcpt-disabled', '');
              var disable_handler = $this.attr('wcpt-panel-disable');
              if( disable_handler ){
                _.panel_disable[disable_handler]( $this );
              }

            }
          })
        }

        // [wcpt-model-key] siblings must match value
        var $parent = $this.data('wcpt-parent');
        if( $parent && $parent.length && key && key !== '[]' && key.slice(-2) !== '[]' ){ // not template nor checkbox/radio
          var parent_data = $parent.data('wcpt-data'),
              $siblings   = $parent.data('wcpt-children');

          // var $twins      = $siblings.filter('[wcpt-model-key="'+ key +'"]').not($this);
          // if( $twins.length ){
          //   _.set_data($parent, parent_data);
          // }
        }

        if( $this.attr('wcpt-controller') ){
          var controller_name = $this.attr('wcpt-controller');
          if( typeof _.controllers[controller_name] !== 'undefined' ){
            _.controllers[controller_name]( $this, val, e );
          }
        }

      })

      // bind sortupdate handler
      $controllers.add($data_nodes).off('sortupdate').on('sortupdate', function(e){
        var $wrapper = $(this),
            $target  = $(e.target);
        if( $wrapper.is($target) && typeof $wrapper.data('wcpt-children') !== 'undefined' ){
          _.reindex_rows($wrapper);
        }
      })

      // bind sortreceive handler
      $controllers.add($data_nodes).off('sortreceive').on('sortreceive', function(e, ui){
        var $wrapper = $(this),
            $target  = $(e.target);

        if( $wrapper.is($target) && typeof $wrapper.data('wcpt-children') !== 'undefined' ){
          var $item         = ui.item,
              wrapper_data  = $wrapper.data('wcpt-data'),
              item_data     = $item.data('wcpt-data'),
              index         = $item.index();

          _.init( $item, item_data );
          wrapper_data.splice(index, 0, item_data);
          _.reindex_rows($wrapper);
        }
      })

      // forward values to a data node above
      $value_forwards.off('change').on('change', function(){
        var $this       = $(this),
            val         = $this.val();
            $data_node  = $this.data('wcpt-data-node');
        $data_node.data('wcpt-data', val);
      })

      // add another row
      $add_row.off('click').on('click', function(){
        var $button       = $(this),
            template_name = $button.attr('wcpt-add-row-template'),
            direction     = $button.attr('wcpt-direction'),
            $template     = $(_.row_templates[template_name]),
            $last_row     = $button.prev('[wcpt-row-template]'),
            index         = 0;

        if( $last_row.length && $last_row.attr('wcpt-row-template') == template_name && $last_row.attr('wcpt-model-key-index') ){
          var index = parseInt( $last_row.attr('wcpt-model-key-index') ) + 1;
        }

        if( ! direction || direction == 'before' ){
          $template.insertBefore($button);
        }else{
          $template.insertAfter($button);
        }

        _.init($template, {});

        var $rows_wrapper = $button.data('wcpt-parent');
        _.reindex_rows($rows_wrapper);

        window.wcpt_feedback_anim( 'add_new_row', $template );

      })

      // duplicate row
      $duplicate_row.off('click').on('click', function(){
        var $this         = $(this),
            $row          = $this.data('wcpt-parent'),
            row_data      = $row.data('wcpt-data'),
            template_name = $row.attr('wcpt-row-template'),
            $template     = $(_.row_templates[template_name]),
            index         = parseInt($row.attr('wcpt-model-key-index')) + 1;

        $template.insertAfter($row);
        var copy_row_data = {};
        $.extend( true, copy_row_data, row_data );
        _.init( $template, _.refresh_ids(copy_row_data) );

        var $rows_wrapper = $row.data('wcpt-parent');
        _.reindex_rows($rows_wrapper);

        window.wcpt_feedback_anim( 'duplicate_row', $template );
      })

      // remove row
      $remove_row.off('click').on('click', function(){
        var $this         = $(this),
            $row          = $this.data('wcpt-parent'),
            $sibling_rows = $row.siblings('[wcpt-row-template]'),
            index         = $row.attr('wcpt-model-key-index');

        var $rows_wrapper = $row.data('wcpt-parent');
        window.wcpt_feedback_anim('delete_row', $row);
        $row.remove();
        _.reindex_rows($rows_wrapper);

      })

      // move row up
      $move_row_up.off('click').on('click', function(){
        var $this         = $(this),
            $row          = $this.data('wcpt-parent'),
            $prev         = $row.prev('[wcpt-row-template]'),
            $rows_wrapper = $row.data('wcpt-parent');

        if( $prev.length ){
          $row.insertBefore( $prev )
          _.reindex_rows($rows_wrapper);
          wcpt_feedback_anim( 'move_row_up', $row );
        }
      })

      // move row down
      $move_row_down.off('click').on('click', function(){
        var $this         = $(this),
            $row          = $this.data('wcpt-parent'),
            $next         = $row.next('[wcpt-row-template]'),
            $rows_wrapper = $row.data('wcpt-parent');

        if( $next.length ){
          $row.insertAfter( $next )
          _.reindex_rows($rows_wrapper);
          wcpt_feedback_anim( 'move_row_down', $row );
        }
      })

      // set values for children
      if( data ){
        _.set_data($elm, data);
      }

    },

    set_data : function($elm, data){
      $elm.data('wcpt-data', data);

      if( typeof $elm.attr('wcpt-block-editor') !== 'undefined' ){
        $elm.wcpt_block_editor();
        return;
      }

      var _           = this,
          $children   = $elm.data('wcpt-children');

      if( $children ){
        $children.each(function(){
          var $this = $(this),
              key   = $this.attr('wcpt-model-key');

          switch(key) {
              // rows array
              case '[]':
                  var template_name = $this.attr('wcpt-row-template'),
                      $template     = $(_.row_templates[template_name]);

                  $this.siblings('[wcpt-row-template="'+ template_name +'"]').remove();
                  $this.replaceWith($template);
                  $this = $template;

                  if( typeof data[0] == 'undefined' ){
                    $this.remove();
                    break;
                  }

                  _.init($this, data[0]);
                  $this.addClass('wcpt-first-row wcpt-last-row');

                  if( data.length > 1 ){
                    $this.removeClass('wcpt-last-row');

                    var $current = $template,
                        i        = 1;

                    while( i < data.length ){
                      var $new = $(_.row_templates[template_name]);
                      $new.attr('wcpt-model-key-index', i);
                      $current.after($new);
                      $current = $new;
                      _.init($current, data[i]);
                      ++i;
                    }
                    $current.addClass( 'wcpt-last-row' )
                  }
                  break;

              // object key
              default:

                if( key.length > 2 && key.slice(-2) == '[]' ){
                // input:checkbox[] / radio[]

                  var true_key = key.slice(0, -2),
                      val = $this.val();
                  if( typeof data[true_key] == 'undefined' || ! Array.isArray( data[true_key] ) ){
                    data[true_key] = [];
                  }

                  $this.prop( 'checked', ( data[true_key].indexOf( val ) > -1 ) );
                  if( $this.attr('wcpt-controller') ){
                    var controller_name = $this.attr('wcpt-controller');
                    if( typeof _.controllers[controller_name] !== 'undefined' ){
                      _.controllers[controller_name]( $this );
                    }
                  }

                }else{
                // other input

                  if( typeof data[key] !== 'undefined' ){
                    if( -1 !== $.inArray($this[0].tagName, ['INPUT', 'SELECT', 'TEXTAREA']) ){
                      if( $this[0].type == 'checkbox' ){
                        data[key] ? $this.prop('checked', true) : $this.prop('checked', false);
                      }else if( $this[0].type == 'radio' ){
                        data[key] == $this.attr('value') ? $this.prop('checked', true) : $this.prop('checked', false);
                      }else{
                        $this.val(data[key]);
                      }
                    }
                    _.set_data( $this, data[key] );
                  }

                }

          }

        })
      }

      // value forward
      var $value_forward = $elm.data('wcpt-value-forward');
      if( $value_forward ){
        $value_forward.val(data);
      }

      // content templates
      var $content_templates = $elm.data('wcpt-content-templates');
      if( $content_templates ){
        $content_templates.each(function(){
          var $content_template = $(this),
              key       = $content_template.attr('wcpt-content-template'),
              val       = $elm.data('wcpt-data')[key],
              modifier  = $content_template.attr('wcpt-value-modifier');
          if( modifier ){
            val = _.value_modifiers[modifier](val);
          }
          val = String(val).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
          $content_template.text(val);
        })
      }

      // panel conditions
      var $panel_conditions = $elm.data('wcpt-panel-conditions');
      if( $panel_conditions ){
        $panel_conditions.each(function(){
          var $this         = $(this),
              condition_key = $this.attr('wcpt-panel-condition');

          if( _.panel_conditions[condition_key]($this, data) ){
            $this.removeAttr('wcpt-disabled');
          }else{
            $this.attr('wcpt-disabled', '');
          }
        })
      }

      // call controller
      if( $elm.attr('wcpt-controller') ){
        var controller_name = $elm.attr('wcpt-controller');
        if( typeof _.controllers[controller_name] !== 'undefined' ){
          _.controllers[controller_name]( $elm, data );
        }
      }

    },

    reindex_rows: function($wrapper){
      var $children     = $wrapper.data('wcpt-children'),
          $removed      = $(),
          wrapper_data  = [];
      $children.each(function(){
        var $child      = $(this),
            child_index = $wrapper.children('[wcpt-model-key="[]"]').index($child);
            child_data  = $child.data('wcpt-data');
        // does the node exist?
        if( child_index !== -1 ){
          wrapper_data[child_index] = child_data;
          $child.attr('wcpt-model-key-index', child_index);
        }else{
          $removed = $removed.add($child);
        }
      })
      $wrapper.data('wcpt-data', wrapper_data);
      $wrapper.data('wcpt-children', $children.not($removed));
      // html class
      $wrapper.data('wcpt-children').removeClass('wcpt-first-row wcpt-last-row')
      //-- first row
      $wrapper.data('wcpt-children').filter(function(){
        return $(this).attr('wcpt-model-key-index') == 0;
      }).addClass('wcpt-first-row');
      //-- last row
      $wrapper.data('wcpt-children').filter(function(){
        return $(this).attr('wcpt-model-key-index') == $wrapper.data('wcpt-children').length - 1;
      }).addClass('wcpt-last-row');

      $wrapper.change();
    },

    /* controllers */

    controllers: {

      /*  controller: column row */

      column_row: function($elm, data, e){

        // property
        if( data.type == 'property' ){
          var prop = data.property ? data.property : $elm.data('wcpt-children').filter('[wcpt-model-key="property"]').val();

          // build attributes
          var attrs = '';
          // use prop like image-size as size in sc attrs
          $.each( data, function(key, val){
            if( key.substring(0, prop.length) == prop ){
              attrs += ' ' + key.substring( prop.length + 1 ) + '="' + val + '" ';
            }
          })

          data.content = '['+ prop + attrs +']';
          $elm.data('wcpt-children').filter("[wcpt-model-key='template']").val(data.content);

        // custom field
        }else if( data.type == 'custom_field' ){
          var key = data.custom_field ? data.custom_field : '';
          data.content = '[custom_field key="'+ key +'"]';
          $elm.data('wcpt-children').filter("[wcpt-model-key='template']").val(data.content);

        // attribute
        }else if( data.type == 'attribute' ){
          var name = data.attribute ? data.attribute : '';
          data.content = '[attribute name="'+ name +'"]';
          $elm.data('wcpt-children').filter("[wcpt-model-key='template']").val(data.content);

        //buttons
        }else if( data.type == 'buttons' ){
          data.content = '';
          if( data.buttons instanceof Array ){
            $.each( data.buttons, function( index, button ){
              var button_mkp = '[button ';
              $.each( button, function(button_key, button_val){
                button_mkp += button_key + '="' + button_val + '" ';
              } )
              button_mkp += ']';
              data.content += button_mkp;
            } )
          }
          $elm.data('wcpt-children').filter("[wcpt-model-key='template']").val(data.content);
        }

      },

      /* controller: navgiation: header row */
      header_row: function( $elm, data, e ){
        $elm.removeClass('wcpt-editor-columns-enabled-left wcpt-editor-columns-enabled-left-right wcpt-editor-columns-enabled-left-center-right');
        $elm.addClass('wcpt-editor-columns-enabled-' + data.columns_enabled);
      },

      /* controller: filters */

      filters: function( $elm, data, e ){
        if( ! data || ! data.length ){
          $elm.prev('.wcpt-filter-headings').hide();
        }else{
          $elm.prev('.wcpt-filter-headings').show();
        }
      },

    },

    row_templates: {},

    refresh_ids: function(item){
      var _ = this

      $.each( item, function( key, val ){
        if( typeof val == 'object' ){
          item[key] = _.refresh_ids(val);
        }

        if( key == 'id' ){
          item[key] = Math.round( Math.random() * 1000000000 );
        }
      } )
      return item;
    },

    value_modifiers: {

      /* columns */
      escape_html: function(val){
        if( ! val ) return false;
        return val.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {return '&#'+i.charCodeAt(0)+';';});
      },

      /* filters */
      unlsug_filter_options_source: function(val){
        var options = {
          "product_cat"   : "Categories",
          "product_tag"   : "Product tags",
          "product_name"  : "Product names",
          "attribute"     : "Attribute",
          "price"         : "Price",
          "rating"        : "Rating",
          "custom_field"  : "Custom field",
          "in_stock"      : "In stock",
          "on_sale"       : "On sale",
        }

        return options[val];
      },

      unlsug_filter_template: function(val){
        var options = {
          "dropdown"              : "Dropdown",
          "checkbox"              : "Checkbox",
          "radio"                 : "Radio",
          "search"                : "Search",
          "min_max"               : "Min-max",
        }

        return options[val];
      }

    },

    initial_data: {

      /* columns */

      column_row: {
        heading   : '',
        template  : '',
        orderby   : 'title',
      },

      /* navigation: header row */

      header_row: {
        columns_enabled : 'left-right',
        columns         : {
          left  : { template: '' },
          center: { template: '' },
          right : { template: '' },
        },
      },

    },

    panel_conditions: {

      prop: function($elm, data){

        var prop = $elm.attr('wcpt-condition-prop'),
            val = $elm.attr('wcpt-condition-val');

        if( val === "true" ){
          return !! data[prop];
        }

        if( val === "false" ){
          return ! data[prop];
        }

        if( typeof data[prop] != 'undefined' && data[prop].constructor === Array ){ // array          
          return -1 !== $.inArray( val, data[prop] );
        }

        vals_arr = val.split('||');

        return -1 !== $.inArray(data[prop], vals_arr);
      },

    },

    panel_enable: {
      custom_content: function($elm){
        $elm.find('.CodeMirror').each(function(){
          this.CodeMirror.refresh();
        })
      }
    },

    panel_disable: {

    },

  }
  $(window).trigger('dominator_ui_ready');

})
