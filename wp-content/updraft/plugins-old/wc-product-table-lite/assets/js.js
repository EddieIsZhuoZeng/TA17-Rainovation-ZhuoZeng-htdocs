jQuery(function($){

  // local cache
  window.wcpt_cache = {
    data: {},
    remove: function (url) {
        delete window.wcpt_cache.data[url];
    },
    exist: function (url) {
        return window.wcpt_cache.data.hasOwnProperty(url) && window.wcpt_cache.data[url] !== null;
    },
    get: function (url) {
        return window.wcpt_cache.data[url];
    },
    set: function (url, cachedData, callback) {
        window.wcpt_cache.remove(url);
        window.wcpt_cache.data[url] = cachedData;
        if ($.isFunction(callback)) callback(cachedData);
    }
  };

  // variation forms
  window.wcpt_product_form = {};

  function get_device($wcpt){
    // device
    var device = 'laptop'; // default

    if( $(window).width() < 701 ){
      device = 'phone';
    }else if( $(window).width() < 1201 ){
      device = 'tablet';
    }

    return device;
  }

  function get_device_table($wcpt){
    var device = get_device($wcpt),
        table_selector = '.wcpt-table-scroll-wrapper-outer.wcpt-device-laptop:visible > .wcpt-table-scroll-wrapper > .wcpt-table, .wcpt-table-scroll-wrapper-outer.wcpt-device-laptop:visible .frzTbl-table';

    // if device not available, get next larger
    if( device == 'phone' && ! $wcpt.find( table_selector.replace('laptop', 'phone') ).length ){
      device = 'tablet';
    }

    if( device == 'tablet' && ! $wcpt.find( table_selector.replace('laptop', 'tablet') ).length ){
      device = 'laptop';
    }

    var $table = $wcpt.find(table_selector.replace('laptop', device));
    
    return $table;
  }

  // html entity encode
  function htmlentity( string ){
    return string.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
      return '&#'+i.charCodeAt(0)+';';
    });  
  }

  // layout handler
  $('body').on('wcpt_layout', '.wcpt', function layout(){
    var $wcpt = $(this),
        $wrap = $wcpt.find('.wcpt-table-scroll-wrapper:visible'),
        $table = $wrap.find('.wcpt-table');

    // load device view ###
    if( $('>.wcpt-device-view-loading-icon', $wrap).length ){
      attempt_ajax( $wcpt, '', false, 'device_view' );
      return;  // layout on AJAX response
    }

    // add pointer on sortable headings
    $wrap.find('.wcpt-heading').each(function(){
      var $this = $(this);
      if( $this.find( '.wcpt-sorting-icons' ).length ){
        $this.addClass('wcpt-sortable');
      }
    })

    // freeze table
    var sc_attrs_string = $wcpt.attr( 'data-wcpt-sc-attrs' ),
        sc_attrs = ( sc_attrs_string && sc_attrs_string !== '{}' ) ? JSON.parse( sc_attrs_string ) : {},
        options = {
          left: ! sc_attrs.laptop_freeze_left ? 0 : parseInt( sc_attrs.laptop_freeze_left ),
          right: ! sc_attrs.laptop_freeze_right ? 0 : parseInt( sc_attrs.laptop_freeze_right ),
          heading: !! sc_attrs.laptop_freeze_heading && sc_attrs.laptop_freeze_heading !== 'false',

          wrapperWidth: ! sc_attrs.laptop_freeze_wrapper_width ? 0 : parseInt( sc_attrs.laptop_freeze_wrapper_width ),
          wrapperHeight: ! sc_attrs.laptop_freeze_wrapper_height ? 0 : parseInt( sc_attrs.laptop_freeze_wrapper_height ),
          
          tableWidth: ! sc_attrs.laptop_freeze_table_width ? 0 : parseInt( sc_attrs.laptop_freeze_table_width ),
          
          offset: ! sc_attrs.laptop_scroll_offset ? 0 : parseInt( sc_attrs.laptop_scroll_offset ),

          breakpoint: {
            1200: {
              left: ! sc_attrs.tablet_freeze_left ? 0 : parseInt( sc_attrs.tablet_freeze_left ),
              right: ! sc_attrs.tablet_freeze_right ? 0 : parseInt( sc_attrs.tablet_freeze_right ),
              heading: !! sc_attrs.tablet_freeze_heading && sc_attrs.tablet_freeze_heading !== 'false',

              wrapperWidth: ! sc_attrs.tablet_freeze_wrapper_width ? 0 : parseInt( sc_attrs.tablet_freeze_wrapper_width ),
              wrapperHeight: ! sc_attrs.tablet_freeze_wrapper_height ? 0 : parseInt( sc_attrs.tablet_freeze_wrapper_height ),
              
              tableWidth: ! sc_attrs.tablet_freeze_table_width ? 0 : parseInt( sc_attrs.tablet_freeze_table_width ),
              
              offset: ! sc_attrs.tablet_scroll_offset ? 0 : parseInt( sc_attrs.tablet_scroll_offset ),
            },
            800: {
              left: ! sc_attrs.phone_freeze_left ? 0 : parseInt( sc_attrs.phone_freeze_left ),
              right: ! sc_attrs.phone_freeze_right ? 0 : parseInt( sc_attrs.phone_freeze_right ),
              heading: !! sc_attrs.phone_freeze_heading && sc_attrs.phone_freeze_heading !== 'false',

              wrapperWidth: ! sc_attrs.phone_freeze_wrapper_width ? 0 : parseInt( sc_attrs.phone_freeze_wrapper_width ),
              wrapperHeight: ! sc_attrs.phone_freeze_wrapper_height ? 0 : parseInt( sc_attrs.phone_freeze_wrapper_height ),
              
              tableWidth: ! sc_attrs.phone_freeze_table_width ? 0 : parseInt( sc_attrs.phone_freeze_table_width ),
              
              offset: ! sc_attrs.phone_scroll_offset ? 0 : parseInt( sc_attrs.phone_scroll_offset ),
            },
          },
        },
        $table = get_device_table($wcpt);
    
    // freeze least tables
    if( 
      $table.length && 
      ! $table.data('freezeTable') 
    ){
      $table.freezeTable( options );
    }
  })

  // resize
  var resize_timer,
      throttle = 250,
      window_width;

  // throttled window resize event listener
  $( window ).on( 'resize', window_resize );

  function window_resize(e){
    clearTimeout( resize_timer );
    var new_window_width = window.innerWidth;
    if( new_window_width != window_width ){
      window_width = new_window_width;
      resize_timer = setTimeout( trigger_layout, throttle);
    }
  }

  // orientation change event listener
  $( window ).on( 'orientationchange', function( e ) {
    trigger_layout();
  } );

  // trigger the layout event
  function trigger_layout(){
    $('.wcpt').trigger('wcpt_layout');
  }

  // layout upon page load
  trigger_layout();


  // every load - ajax or page load, needs this
  function after_every_load($container){
    $('.cart', $container).each(function(){
      // action -> same page
      $(this).attr( 'action', window.location.href );
    })
    var $qty_wrapper = $('.quantity', $container);
    
    maybe_disable_qty_controllers($('.quantity', $container));
    prep_variation_options( $container );
  }

  // lazy load
  function lazy_load_start(){
    if( ! window.wcpt_lazy_loaded ){
      $('.wcpt-lazy-load').each(function(){
        var $this = $(this);
        attempt_ajax( $( this ), false, false, 'lazy_load' );
      })
      window.wcpt_lazy_loaded = true;
    }
  }

  if( ! $('body').offset().top ){
    $(window).one('mousemove scroll touchstart', lazy_load_start);
    setTimeout(lazy_load_start, 4000);
  }else{
    lazy_load_start();
  }

  // get rows including freeze
  function get_product_rows( $elm ){
    var $row = $elm.closest('.wcpt-row'),
        product_id = $row.attr('data-wcpt-product-id'),
        variation_id = $row.attr('data-wcpt-variation-id'),
        $scroll_wrapper = $elm.closest('.wcpt-table-scroll-wrapper'),
        row_selector;

    if( variation_id ){
      row_selector = '.wcpt-row[data-wcpt-variation-id="'+ variation_id +'"]';
    }else{
      row_selector = '.wcpt-row[data-wcpt-product-id="'+ product_id +'"]';
    }

    return $(row_selector, $scroll_wrapper);
  }

  // event listeners
  // -- adding to cart
  $('body').on('adding_to_cart', function(e, $button, data){
    $('.wcpt-cw-loading-icon').removeClass('wcpt-hide');
    var $row = get_product_rows( $button );
    if( $row.length ){
      // return row quantity input to default
      var $input = $row.find('input.qty, select.wcpt-qty-select'),
          min = $input.attr('min') ? $input.attr('min') : 1;
      $input.val(min);
    }
  })

  // -- added to / remove from cart
  $('body').on(' removed_from_cart', function(e, fragments, cart_hash, buttons){
    // update button count
    update_cart_items_ajax();
  })

  // button click listener
  $('body').on('click.wcpt', '.wcpt-button', button_click);

  function button_click(e){
    var $button = $(this),
        link_code = $button.attr('data-wcpt-link-code'),
        $product_rows = get_product_rows( $button ),
        product_id = $product_rows.attr('data-wcpt-product-id'),
        is_variable = $product_rows.hasClass('wcpt-product-type-variable'),
        has_addons = $product_rows.hasClass('wcpt-product-has-addons');

    if( -1 !== $.inArray( link_code, ['product_link', 'external_link', 'custom_field', 'custom_field_media_id'] ) ){
      return;
    }

    e.preventDefault();

    // prepare AJAX data
    var ajax_data = {
      'action'      : 'wcpt_add_to_cart',
      'add-to-cart' : $product_rows.attr('data-wcpt-product-id'),
      'product_id'  : product_id,
      'quantity'    : 1,
    };

    // quantity input
    var $wcpt_qty =  $('.wcpt-quantity-wrapper input.qty, .wcpt-quantity-wrapper > select.wcpt-qty-select', $product_rows),
        $wc_qty = $('.cart .qty', $product_rows);

    if( $wc_qty.length ){ // from wc form qty
      ajax_data.quantity = $wc_qty.val();
    }

    if( $wcpt_qty.length ){ // from wcpt qty
      ajax_data.quantity = $wcpt_qty.val();
    }

    if( ! ajax_data.quantity ){
      ajax_data.quantity = 1;
    }

    // variation attributes 
    var variation_attributes = $button.closest('.wcpt-row').attr('data-wcpt-variation-attributes');
    if( variation_attributes ){
      $.extend( ajax_data, JSON.parse(variation_attributes) );      
    }

    // receive notices from server?
    ajax_data.return_notice = ( link_code == "cart_ajax" );

    // variable product
    if( is_variable || has_addons ){

      if( is_variable ){

        var variation_id = $product_rows.data('wcpt_variation_id'),
            complete_match = $product_rows.data('wcpt_complete_match'),
            attributes = $product_rows.data('wcpt_attributes'),
            variation_found = $product_rows.data('wcpt_variation_found'),
            variation_selected = $product_rows.data('wcpt_variation_selected'),
            variation_available = $product_rows.data('wcpt_variation_available');
            variation_ops = $product_rows.data('wcpt_variation_ops');

        // if row has variation selection options,
        // block with message if there is an issue.
        // no need to load modal
        if( variation_ops ){
          if( ! variation_found ){
            alert( wcpt_i18n.i18n_no_matching_variations_text );
            return;
          }

          if( ! variation_selected ){
            alert( wcpt_i18n.i18n_make_a_selection_text );
            return;
          }

          if( ! variation_available ){
            alert( wcpt_i18n.i18n_unavailable_text );
            return;
          }
        }

        if( attributes ){
          $.extend( ajax_data, attributes );
        }

        // a complete variation is selected, add to cart
        // not if there are addons
        if( 
          variation_id && 
          complete_match &&
          ! has_addons
        ){
          ajax_data.variation_id = variation_id;
          wcpt_ajax_add_to_cart( $button, ajax_data )
          return false;

        // otherwise we need to open the variation form in modal
        }

      }

      // deploy modal if cached
      if( typeof window.wcpt_product_form[product_id] !== 'undefined' ){
        var $modal = $( window.wcpt_product_form[product_id] );
        $modal.appendTo('body');
        $('body').addClass('wcpt-modal-on');
        $modal.show();
        prep_product_form( $modal, $button, ajax_data );

      //-- else fetch modal from server
      }else{
        ajax_data.action = 'wcpt_get_product_form_modal';
        delete ajax_data['add-to-cart'];
        var $loading_modal = $( $('#tmpl-wcpt-product-form-loading-modal').html() );

        // AJAX call
        $.ajax({
          url: wcpt_i18n.ajax_url,
          method: 'POST',
          beforeSend: function(){

            $('body').append($loading_modal);

            // in case loading modal is closed,
            // set a flag to not display the form when it arrives
            $loading_modal.on('wcpt_close', function(){
              window.wcpt_cancel_product_form = true;
            })
            // give permission for now since this AJAX call is made
            window.wcpt_cancel_product_form = false;
          },
          data: ajax_data
        })
        .done(function( response ){

          window.wcpt_product_form[product_id] = response;

          if( ! window.wcpt_cancel_product_form ){
            var $modal = $(response);
            $modal.appendTo('body');
            $loading_modal.remove();
            $('body').addClass('wcpt-modal-on');

            prep_product_form( $modal, $button, ajax_data );
          }

        })
      }

      return false;
    }

    if( $button.hasClass('wcpt-disabled') ){ // TODO: send above
      return;
    }

    // non-variable product & without any add-ons
    if( link_code == "cart_ajax" ){
      // add the items to cart via AJAX
      wcpt_ajax_add_to_cart($button, ajax_data);

    }else{
      // redirect by form
      var $form = $('<form method="POST" action="' + $button.attr('href') + '" style="display: none;"></form>');
      $.each( ajax_data, function( key, val ){
        if( key == 'action' ) return; // continue
        var $input = $('<input type="hidden" name="'+ key +'" value="" />');
        $input.val( val );
        $form.append($input);
      } )
      $form.appendTo($('body')).submit();

    }

  }

  // add items to cart via AJAX
  function wcpt_ajax_add_to_cart( $button, ajax_data ){
    var $product_row = get_product_rows( $button ),
        url = wcpt_i18n.ajax_url;

    if( $button.hasClass('wcpt-button-cart_ajax') ){
      $button = $button.add( $product_row.find( '.wcpt-button-cart_ajax' ).not( $button ) );
    }

    if( 
      typeof wc_add_to_cart_params !== 'undefined' && 
      typeof wc_add_to_cart_params.wc_ajax_url !== 'undefined' 
    ){
      url = wc_add_to_cart_params.wc_ajax_url.replace( '%%endpoint%%', 'wcpt_add_to_cart' );
    }

    $.ajax({
      url: url,
      method: 'POST',
      beforeSend: function(){
        disable_button($button);
        loading_badge_on_button($button);

        $('body').trigger('adding_to_cart', [ $button, ajax_data ]);
      },
      data: ajax_data
    })
    .done(function( response ) {

      if( $button.attr('data-wcpt-link-code') === 'cart_refresh' ){
        window.location.reload();
        return;
      }

      enable_button($button);
      $('.wcpt-cw-loading-icon').removeClass('wcpt-hide');
      $button.find( '.wcpt-cart-badge-refresh' ).remove();

      var in_cart = $product_row.attr( 'data-wcpt-in-cart' );
      if( ! in_cart ){
        in_cart = 0;
      }

      if ( response.error ) {
      // error
        var $notice = $(response.notice);
        $notice.find('a').remove();
        alert($notice.text().trim());
        // hide cart widget loading icon
        $('.wcpt-cw-loading-icon').addClass('wcpt-hide');
        in_cart = parseInt( in_cart, 10 );

      }else if( response.success ){
      // success
        in_cart = parseInt( ajax_data.quantity, 10 ) + parseInt( in_cart, 10 );
        $( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $button ] );
      }

      if( 0 !== in_cart ){
        add_count_badge_to_button( in_cart, $button );
      }

      $product_row.attr( 'data-wcpt-in-cart', in_cart );

    })
  }

  function prep_product_form( $modal, $button, pre_select ){
    var link_code = $button.attr('data-wcpt-link-code'),
        href = link_code == 'cart_ajax' ? '' : $button.attr('href');

    $( '.cart', $modal ).each( function() {
      var $form = $(this);

      if( $form.hasClass('variations_form') ){
        $form.wc_variation_form();

      }else { // simple product (probably with addon)
        $form.append('<input name="add-to-cart" type="hidden" value="'+ pre_select['product_id'] +'">');

      }

      if( $.fn.init_addon_totals ){
        $form.init_addon_totals();
      }

      if( typeof wcPaoInitAddonTotals === 'object' ){
        wcPaoInitAddonTotals.init( $form );
      }

      $form.attr('action', href);

      $('.qty', $form).attr('autocomplete', 'off');

      if( pre_select ){
        $.each( pre_select, function( key, val ){
          var $control = $form.find('[name='+ key +']');
          if( $control.is('input.qty') ){
            // working on input
            val = parseInt( val );
            var min = $control.attr('min') ? parseInt( $control.attr('min') ) : 0;
            var max = $control.attr('max') ? parseInt( $control.attr('max') ) : 1000000000;

            // respect min
            if( val < min ){
              val = min;
            }
            // respect max
            if( val > max ){
              val = max;
            }
          }
          $control.val(val);
        } )
      }

      if( link_code == 'cart_ajax' ){
        $form.on('submit', function(e){
          e.preventDefault();

          ajax_data = {
            action        : 'wcpt_add_to_cart',
            return_notice : true,
          };

          $.each($form.serializeArray(), function(i, field) {
              ajax_data[field.name] = field.value;
          });

          wcpt_ajax_add_to_cart($button, ajax_data);

          $modal.remove();
          $('body').removeClass('wcpt-modal-on');
        })
      }

    });
  }

  function disable_button($button){
    $button.addClass("wcpt-disabled");
  }

  function enable_button($button){
    $button.removeClass("wcpt-disabled");
  }

  function loading_badge_on_button($button){
    if( ! $button.find( '.wcpt-cart-badge-refresh' ).length ){
      var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader" color="#384047"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>';
      $button.append( '<i class="wcpt-cart-badge-refresh">'+ svg +'</i>' );
    }
  }

  function add_count_badge_to_button(in_cart, $button){
    if( ! $button.find( '.wcpt-cart-badge-number' ).length ){
      $button.append( '<i class="wcpt-cart-badge-number">' + in_cart + '</i>' );
    }else{
      $button.find( '.wcpt-cart-badge-number' ).html( in_cart );
    }

    $button.closest('.wcpt-row').find('.wcpt-remove').removeClass('wcpt-disabled');
  }

  // search
  // -- submit
  $('body').on('click', '.wcpt-search-submit', search_submit);
  $('body').on('keydown', '.wcpt-search-input', search_submit);
  function search_submit(e){
    var $this       = $(this),
        $search     = $this.closest('.wcpt-search'),
        $input      = $search.find('.wcpt-search-input'),
        table_id    = $search.attr('data-wcpt-table-id'),
        $container  = $('#wcpt-' + table_id),
        $nav_modal  = $this.closest('.wcpt-nav-modal'),
        keyword     = $input.val();
        query       = $input.attr('name') + '=' + keyword,
        append      = true;

    if(
      ( $(e.target).closest('.wcpt-search-submit').length && e.type == 'click' ) ||
      ( $(e.target).is('.wcpt-search-input') && e.type == 'keydown' && ( e.keyCode == 13 || e.which == 13 ) )
    ){
      if( $nav_modal.length ){
        $nav_modal.trigger('wcpt_close');
      }

      attempt_ajax( $container, query, append, 'filter' );
    }
  }
  // -- clear
  $('body').on('click', '.wcpt-search-clear', function(e){
    var $this       = $(this),
        $search     = $this.closest('.wcpt-search'),
        $input      = $search.find('.wcpt-search-input'),
        table_id    = $search.attr('data-wcpt-table-id'),
        $container  = $('#wcpt-' + table_id),
        $nav_modal  = $this.closest('.wcpt-nav-modal'),
        query       = '&' + $input.attr('name') + '=',
        append      = true;
    $input.val('');
    attempt_ajax( $container, query, append, 'filter' );

    if( $nav_modal.length ){
      $nav_modal.trigger('wcpt_close');
    }
  })

  // var log_arr = [];
  //
  // function log(text){
  //   var log = '_wcpt-log';
  //   if( ! $('.' + log).length ){
  //     $('body').append( '<div class="'+ log +'">' );
  //   }
  //
  //   log_arr.push(text);
  //   if( log_arr.length > 3 ){
  //     log_arr.pop(text);
  //   }
  //
  //   var str = '';
  //   $.each(log_arr, function(key, val){
  //     str += val;
  //     if( log_arr[key + 1] ){
  //       str += ' · ';
  //     }
  //   })
  //
  //   $('.' + log).text(str);
  //   console.log(text);
  // }

  // dropdown / tooltip
  // behave smoothly b/w mouse / touch events

  function dropdown_switch_to_mouse_events (){
    var target = '.wcpt-dropdown, .wcpt-tooltip',
        $body = $('body');

    $body.off( 'touchstart.wcpt', target, dropdown_touch_toggle );

    // hover
    $body.on( 'mouseenter.wcpt',  target, dropdown_mouse_open );
    $body.on( 'mouseleave.wcpt',  target, dropdown_mouse_close );

    // click
    $body.on( 'click.wcpt',  target, dropdown_mouse_open );
    $body.on( 'mouseleave.wcpt',  target, dropdown_mouse_close );

    // prefer touch
    $body.one('touchstart', function(){
      dropdown_switch_to_touch_events();
    })
  }

  function dropdown_switch_to_touch_events (){
    var target = '.wcpt-dropdown, .wcpt-tooltip',
        $body = $('body');

    $body.off( 'mouseenter.wcpt', target, dropdown_mouse_open );
    $body.off( 'mouseleave.wcpt', target, dropdown_mouse_close );

    $body.on( 'touchstart.wcpt', dropdown_touch_toggle );

    // default to mouse
    var width = $(window).width();
    $(window).one('resize', function(){
      if( width != $(window).width() ){
        dropdown_switch_to_mouse_events();
      }
    })
  }

  // assume mouse at first
  dropdown_switch_to_mouse_events();

  function dropdown_mouse_open( e ){
    var $this = $(this);
    $this.addClass('wcpt-open');

    fix_tooltip_position($this);
  }

  function dropdown_mouse_close( e ){
    var $this = $(this);
    $this.removeClass('wcpt-open');
  }

  function dropdown_touch_toggle( e ){
    var $target = $(e.target),
        container = '.wcpt-dropdown, .wcpt-tooltip',
        $dropdown = $target.closest(container);

    if(
      ! $dropdown.length ||
      $dropdown.hasClass('wcpt-open') && $target.closest('.wcpt-dropdown-label, .wcpt-tooltip-label').length // re-click label
    ){
      $('body').find(container).removeClass('wcpt-open');
      return;
    }

    $dropdown.addClass('wcpt-open');

    $('body').find(container).not($dropdown).removeClass('wcpt-open');

    fix_tooltip_position($dropdown);
  }

  function fix_tooltip_position($tooltip){
    // correct position
    var $content      = $tooltip.find(' > .wcpt-dropdown-menu, > .wcpt-tooltip-content-wrapper > .wcpt-tooltip-content'),
        content_width = $content.outerWidth(false),
        offset_left   = $content.offset().left,
        page_width    = $(window).width();

    if( content_width + 30 > page_width ){
      $content.outerWidth( page_width - 30 );
      var content_width = $content.outerWidth(false);
    }

    if( $content.offset().left + content_width > page_width ){ // offscreen right
      var offset_required = $content.offset().left + content_width - page_width;
      $content.css('left', '-=' + ( offset_required + 15 ) );

    }else if( $content.offset().left < 0 ){ // offscreen left
      $content.css('left', '-=' + ( $content.offset().left - 15 ) );

    }

    // tooltip arrow
    if( $tooltip.hasClass('wcpt-tooltip') ){
      var $label      = $tooltip.find('> .wcpt-tooltip-label'),
          offset_left = $label.offset().left,
          width       = $label.outerWidth(),
          $arrow      = $tooltip.find('> .wcpt-tooltip-content > .wcpt-tooltip-arrow');
      $arrow.css('left', (offset_left - $content.offset().left + (width / 2)) + 'px');
    }
  }

  // filters
  $('body').on('change', '.wcpt-navigation', function(e){

    var $target = $(e.target);

    // taxonomy hierarchy
    if( $target.closest('.wcpt-hierarchy').length ){
      var checked = $target.prop('checked');

      // effect on child terms
      if ($target.hasClass('wcpt-hr-parent-term')) {
        var ct_selector 	= 'input[type=checkbox], input[type=radio]',
            $child_terms 	= $target.closest('label').siblings('.wcpt-hr-child-terms-wrapper').find(ct_selector);
        $child_terms.prop('checked', false);
      }

      // effect on parent terms
      var $ancestors = $target.parents('.wcpt-hr-child-terms-wrapper');
      if ($ancestors.length) {
        $ancestors.each(function() {
          var $parent_term = $(this).siblings('label').find('.wcpt-hr-parent-term');
          $parent_term.prop('checked', false);
        })
      }
    }

    // range filter
    if( $target.closest('.wcpt-range-filter') ){

      // -- input boxes shouldn't propagate
      if( $target.hasClass('wcpt-range-input-min') || $target.hasClass('wcpt-range-input-max') ){
        return;
      }

      var min = $target.attr('data-wcpt-range-min') || '',
          max = $target.attr('data-wcpt-range-max') || '',
          $range_filter = $target.closest('.wcpt-range-filter'),
          $min = $range_filter.find('.wcpt-range-input-min'),
          $max = $range_filter.find('.wcpt-range-input-max');

      $min.val(min);
      $max.val(max);
    }

    // search
    if( $target.closest('.wcpt-search').length ){
      return;
    }

    // modal
    if( $target.closest( '.wcpt-nav-modal' ).length ){
      return;
    }

    var $this           = $(this),
        $nav            = $this.add($this.siblings('.wcpt-navigation')), // combine query from all navs
        $filter         = $target.closest('.wcpt-filter'),
        $container      = $nav.closest('.wcpt'),
        table_id        = $container.attr('id').substring(5),
        $nav_clone      = $nav.clone(),
        $reverse_check  = $();

    $nav_clone.find('[data-wcpt-reverse-value]:not(:checked)').each(function(){
      var $this = $(this);
      $this.attr('value', $this.attr('data-wcpt-reverse-value'));
      $this.prop('checked', 'checked');
      $reverse_check = $reverse_check.add($this.clone());
    })
    $nav_clone = $nav_clone.add( $reverse_check );

    // build query
    var query = $('<form>').append($nav_clone).serialize();

    // include column sort
    if( ! $(e.target).closest('[data-wcpt-filter="sort_by"]').length ){
      var $sortable_headings = $('.wcpt-heading.wcpt-sortable:visible', $container),
          $current_sort_col = $sortable_headings.filter(function(){
            return $(this).find('.wcpt-sorting-icons.wcpt-sorting-asc, .wcpt-sorting-icons.wcpt-sorting-desc').length;
          });
      if( $current_sort_col.length ){
        var col_index = $current_sort_col.index(),
            order = $current_sort_col.find( '.wcpt-sorting-icons.wcpt-sorting-asc' ).length ? 'ASC' : 'DESC';
        query += '&' + table_id +'_orderby=column_' + col_index + '&'+ table_id +'_order=' + order;
      }
    }

    attempt_ajax( $container, query, false, 'filter' );
  })

  // submit range by enter
  $('body').on('keyup', '.wcpt-range-input-min, .wcpt-range-input-max', function(e){
    var $this = $(this),
        $filters  = $this.closest('.wcpt-navigation'),
        code = (e.keyCode ? e.keyCode : e.which);

    if( code == 13 ){
      $filters.trigger('change');
    }
  })

  // submit range
  $('body').on('click', '.wcpt-range-submit-button', function(e){
    var $this     = $(this),
        $filters  = $this.closest('.wcpt-navigation');

    $filters.trigger('change');
  })

  // clear filter
  $('body').on('click', '.wcpt-clear-filter', function(e){

    var $clear_filter = $(this),
        $target       = $(e.target);

    if( $target.closest('.wcpt-dropdown-menu') ){
      var $sub_option = $target.closest('.wcpt-dropdown-option');
    }else{
      $sub_option = false;
    }

    var $container  = $clear_filter.closest('.wcpt'),
        filter      = $clear_filter.attr('data-wcpt-filter'),
        $navs    = $('> .wcpt-navigation', $container),
        $inputs     = $();

    if( filter == 'attribute' || filter == 'category' || filter == 'taxonomy' ){

      var taxonomy  = $clear_filter.attr('data-wcpt-taxonomy'),
          term = $clear_filter.attr('data-wcpt-value'),
          $inputs = $navs.find('.wcpt-filter[data-wcpt-filter="'+ filter +'"][data-wcpt-taxonomy="'+ taxonomy +'"]').find('input[value="'+ term +'"]');

    }else if( filter == 'custom_field' ){

      var meta_key = $clear_filter.attr('data-wcpt-meta-key'),
          value = $clear_filter.attr('data-wcpt-value'),
          $filter = $navs.find('.wcpt-filter[data-wcpt-filter="'+ filter +'"][data-wcpt-meta-key="'+ meta_key +'"]');

      if( $filter.hasClass('wcpt-range-filter') ){
        $inputs = $filter.find('input');
      }else{
        $inputs  = $navs.find('.wcpt-filter[data-wcpt-filter="'+ filter +'"][data-wcpt-meta-key="'+ meta_key +'"]').find('input[value="'+ value +'"]');
      }

    }else if( filter == 'price_range' ){
      var $inputs = $navs.find('.wcpt-filter[data-wcpt-filter="'+ filter +'"]').find('input');

    }else if( filter == 'search' ){
      $inputs = $navs.find('input[type=search][data-wcpt-value="'+ htmlentity( $clear_filter.attr("data-wcpt-value") ) +'"]');

    }else if( filter == 'rating' ){
      $inputs = $navs.find('.wcpt-filter[data-wcpt-filter="rating"]').find('input');

    }

    $inputs.filter(':input[type=checkbox], :input[type=radio]').prop('checked', false);
    $inputs.filter(':input[type=text], :input[type=number], :input[type=search]').val(''); // search and range input

    $navs.first().trigger('change'); // triggering one will trigger all. Do not multi-trigger
  })

  // clear all filters
  $('body').on('click', '.wcpt-clear-filters, .wcpt-clear-all-filters', function(e){
    e.preventDefault();
    var $this       = $(this),
        $container  = $this.closest('.wcpt'),
        query       = '';
    attempt_ajax( $container, query, false, 'filter' );
  })

  // sort by column heading
  $('body').on('click', '.wcpt-heading', function(){
    var $this = $(this),
        $sorting = $this.find('.wcpt-sorting-icons');

    if( ! $sorting.length ){
      return;
    }

    var order = $sorting.hasClass('wcpt-sorting-asc') ? 'desc' : 'asc',
        col_index = $this.index(),
        $container = $this.closest( '.wcpt' ),
        table_id = $container.attr('id').substring(5),
        device = 'laptop';

    if( $( '.wcpt-sorting-'+ order +'-icon', $sorting ).hasClass('wcpt-hide') ){
      if( $( '.wcpt-sorting-'+ order +'-icon', $sorting ).siblings().hasClass('wcpt-active') ){
        return;
      }else{
        order = order == 'asc' ? 'desc' : 'asc';
      }
    }

    var query = table_id +'_orderby=column_' + col_index + '&'+ table_id +'_order=' + order + '&'+ table_id +'_device=' + device;

    attempt_ajax( $container, query, true, false );
  })

  // pagination
  $('body').on('click', '.wcpt-pagination .page-numbers:not(.dots):not(.current)', function(e){
    e.preventDefault( );
    var $this       = $( this ),
        $container  = $this.closest('.wcpt'),
        table_id    = $container.attr('id').slice(5),
        query       = table_id + '_paged=' + $this.text();
        append      = true;
    attempt_ajax( $container, query, append, 'paginate' );
  })

  // ajax
  function attempt_ajax( $container, new_query, append, purpose ){

    if( typeof purpose == 'undefined' ){
      throw 'WCPT: Define AJAX purpose';
      debugger;
    }

    // combine earlier query
    var query = '',
        earlier_query = $container.attr('data-wcpt-query-string');
    if( append ){
      query = earlier_query ? earlier_query + '&' + new_query : '?' + new_query;
    }else{
      query = '?' + new_query;
    }

    // persist params
    var parsed_params = parse_query_string( window.location.search.substring(1) );

    if( typeof window.wcpt_persist_params !== 'undefined' ){
      $.each(wcpt_persist_params, function(index, val){
        if( parsed_params[val] ){
          query += '&' + val + '=' + parsed_params[val];
        }
      })
    }

    // device
    var device = 'laptop',
        $scroll_outer = $container.find( '.wcpt-table-scroll-wrapper-outer:visible' ),
        table_id = $container.attr('data-wcpt-table-id');

    if( $scroll_outer.length ){
      if( $scroll_outer.hasClass('wcpt-device-phone') ){
        device = 'phone';
      }else if( $scroll_outer.hasClass('wcpt-device-tablet') ){
        device = 'tablet';
      }
    }else if( $('body').hasClass('wcpt-nav-modal-on') ){
      $('.wcpt-nav-modal').attr('data-wcpt-device');
    }

    query += '&' + table_id + '_device=' + device;

    // search - orderby relevance
    var new_query_p = new_query ? parse_query_string( new_query ) : {},
        earlier_query_p = earlier_query ? parse_query_string( earlier_query.substring(1) ) : {};
    
    $.each(new_query_p, function(key, val){
      if( 
        key.indexOf('search') !== -1 &&
        val &&
        (
          earlier_query_p[key] !== val.replace(/\+/g, ' ')
        )
      ){
        query += '&'+ table_id +'_orderby=relevance';
        return false;
      }
    });

    // sc attrs
    var sc_attrs = $container.attr( 'data-wcpt-sc-attrs' ),
        disable_ajax = false;
    if( sc_attrs && sc_attrs !== '{}' ){
      sc_attrs = htmlentity(sc_attrs);
      query += '&' + table_id + '_sc_attrs=' + sc_attrs;

      if( sc_attrs.indexOf("disable_ajax") !== -1 ){
        disable_ajax = true;
      }
    }

    // scroll after ajax
    var scroll = true;
    if( -1 !== $.inArray( purpose, ['device_view', 'lazy_load'] ) ){
      scroll = false;
    }

    // table already been filtered?
    if( purpose == 'filter' ){
      query += '&' + table_id + '_filtered=true';
    }

    // skip ajax, redirect
    if( disable_ajax ){
      window.location = query;
      console.log('disable ajax');
      return;
    }

    $.ajax({
      url: wcpt_i18n.ajax_url + query,
      method: 'GET',
      beforeSend: function(){
        $container.addClass('wcpt-loading');

        if (window.wcpt_cache.exist(query)) {
          ajax_success (window.wcpt_cache.get(query), $container, scroll, device);
          return false;
        }
        return true;
      },
      data: {
        'action'   : 'wcpt_ajax',
        'id'       : table_id,
      },
    })
    .done(function( response ) {
      // success
      if( response && response.indexOf('wcpt-table') !== -1 ){
        window.wcpt_cache.set(query, response);
        ajax_success (window.wcpt_cache.get(query), $container, scroll, device);

      // fail
      }else{
        console.log('wcpt notice: query fail');
        window.location = query;

      }

    });

  }

  // helper fn.
  function parse_query_string(query) {
    var vars = query.split("&");
    var query_string = {};
    for (var i = 0; i < vars.length; i++) {
      var pair = vars[i].split("=");
      var key = decodeURIComponent(pair[0]);
      var value = decodeURIComponent(pair[1]);
      // If first entry with this name
      if (typeof query_string[key] === "undefined") {
        query_string[key] = decodeURIComponent(value);
        // If second entry with this name
      } else if (typeof query_string[key] === "string") {
        var arr = [query_string[key], decodeURIComponent(value)];
        query_string[key] = arr;
        // If third or later entry with this name
      } else {
        query_string[key].push(decodeURIComponent(value));
      }
    }
    return query_string;
  }

  // ajax successful
  function ajax_success(response, $container, scroll, device ){
    var $new_container = $(response);
    $container.replaceWith( $new_container );
    $new_container.trigger('wcpt_layout');

    // audio/video player
    if( typeof window.wp.mediaelement !== 'undefined' ){
      window.wp.mediaelement.initialize();
    }

    // variation forms need to be force init after ajax
    $( '.cart', $new_container ).each(function(){
      var $form = $(this);
      if( $form.hasClass('variations_form') ){
        $(this).wc_variation_form();
      }
      if( $.fn.init_addon_totals ){
        $form.init_addon_totals();
      }
      if( typeof wcPaoInitAddonTotals === 'object' ){
        wcPaoInitAddonTotals.init( $form );
      }
    })

    // needs to run on page load as well as ajax load
    after_every_load( $new_container );

    var sc_attrs_string = $new_container.attr( 'data-wcpt-sc-attrs' ),
        sc_attrs = ( sc_attrs_string && sc_attrs_string !== '{}' ) ? JSON.parse( sc_attrs_string ) : {},
        offset = {
          'laptop': ( typeof sc_attrs.laptop_scroll_offset == 'undefined' || sc_attrs.laptop_scroll_offset == ''  ) ? 20 : parseInt( sc_attrs.laptop_scroll_offset ),
          'tablet': ( typeof sc_attrs.tablet_scroll_offset == 'undefined' || sc_attrs.tablet_scroll_offset == ''  ) ? 20 : parseInt( sc_attrs.tablet_scroll_offset ),
          'phone' : ( typeof sc_attrs.phone_scroll_offset  == 'undefined' || sc_attrs.phone_scroll_offset ==  ''  ) ? 20 : parseInt( sc_attrs.phone_scroll_offset  ),
        };

    // scroll to top & set history
    if( scroll ){
      $('html, body').animate({
          scrollTop: $new_container.offset().top - offset[device],
      }, 200);

      var query = $new_container.attr('data-wcpt-query-string');
      if( query && typeof window.history !== 'undefined' ){
        history.replaceState({}, $('title').text(), query);
      }
    }
  }

  // variable product modal form
  //-- close modal
  $('body').on('click', '.wcpt-modal, .wcpt-close-modal', function( e ){
    var $target = $(e.target),
        $modal = $(this).closest('.wcpt-modal');
    if( $target.hasClass( 'wcpt-modal' ) || $target.closest( '.wcpt-close-modal' ).length ){
      $modal.trigger('wcpt_close');
      $modal.remove();
      $('body').removeClass('wcpt-modal-on');
    }
  })

  // update cart items
  function update_cart_items_ajax(){
    if( ! $('.wcpt, .wcpt-lazy-load').length ){
      return;
    }

    // fetch cart details
    $.post( wcpt_i18n.ajax_url, { action: 'wcpt_get_cart' }, window.wcpt_update_cart_items );

    // fetch cart widget
    $.post( wcpt_i18n.ajax_url, { action: 'wcpt_cart_widget' }, function(cart_widget){
      var $old = $('.wcpt-cart-widget'),
          $new = $(cart_widget);

      $('body').append($new);
      $old.fadeOut(800, function(){
        $old.remove();
      });
    })
  }

  window.wcpt_update_cart_items = function ( cart ){

    var cart_products = {},
        total = 0;
    $.each( cart, function( key, item ){
      if( ! cart_products[item.product_id] ){
        cart_products[item.product_id] = 0;
      }

      if( item.variation_id && ! cart_products[item.variation_id] ){
        cart_products[item.variation_id] = 0;
      }

      cart_products[item.product_id] += item.quantity;

      if( item.variation_id ){
        cart_products[item.variation_id] += item.quantity;
      }

      total += item.quantity;
    } )

    // -- update each product row
    $('.wcpt-row').each(function(){
      var $this = $(this),
          id = $this.attr('data-wcpt-variation-id') ? $this.attr('data-wcpt-variation-id') : $this.attr('data-wcpt-product-id'),
          qty = cart_products[id] ? cart_products[id] : 0,
          $badge = $this.find('.wcpt-cart-badge-number'),
          $remove = $this.find('.wcpt-remove');

      $this.attr('data-wcpt-in-cart', qty);

      if( qty ){
        $badge.text(qty);
        $remove.removeClass('wcpt-disabled')
      }else{
        $badge.text('');
        $remove.addClass('wcpt-disabled')
      }
      $remove.removeClass('wcpt-removing');
    })
  }

  update_cart_items_ajax();

  // anchor tag
  $('body').on('click touchstart', '[data-wcpt-href]', function(){
    window.location = $(this).attr('data-wcpt-href');
  })

  // accordion
  //-- filters
  $('body').on('click', '.wcpt-left-sidebar .wcpt-filter > .wcpt-filter-heading', function(){
    var $this = $(this),
        $filter = $this.closest('.wcpt-filter');
    $filter.toggleClass('wcpt-filter-open');
  })
  //-- taxonomy parent
  $('body').on('click', '.wcpt-ac-icon', function(e){
    var $this = $(this);
    $this.closest('.wcpt-accordion').toggleClass('wcpt-ac-open');
    e.stopPropagation();
    return false;
  })

  // nav modal
  function nav_modal(e){
    var $button = $(e.target).closest('.wcpt-rn-button'),
        modal_type = $button.attr('data-wcpt-modal'),
        $wcpt = $button.closest('.wcpt'),
        wcpt_id = $wcpt.attr('id'),
        $nav_modal = $( $wcpt.find('.wcpt-nav-modal-tpl').html() ),
        $filters = $wcpt.find('.wcpt-filter').not('[data-wcpt-filter="sort_by"]'),
        $search = $wcpt.find('.wcpt-search-wrapper'),
        $sort = $wcpt.find('[data-wcpt-filter="sort_by"].wcpt-filter'),
        radios = {};

    $('.wcpt-nm-sort-placeholder', $nav_modal).replaceWith( $sort.clone() );
    $('.wcpt-nm-filters-placeholder', $nav_modal).replaceWith( $search.clone().add($filters.clone()) );

    if( modal_type == 'sort' ){
      $nav_modal.addClass('wcpt-show-sort').removeClass('wcpt-show-filters');
    }else{ // filter
      $nav_modal.addClass('wcpt-show-filters').removeClass('wcpt-show-sort');
    }

    // record radios
    $wcpt.find('input[type=radio]:checked').each(function(){
      var $this = $(this);
      radios[$this.attr('name')] = $this.val();
    })
    $nav_modal.data('wcpt-radios', radios);

    // append
    $('body')
      .addClass('wcpt-nav-modal-on')
      .append( $nav_modal );

    // apply
    //-- filter
    $nav_modal.find('.wcpt-nm-apply').on('click', function(){
      var query = $('<form>').append($nav_modal.clone()).serialize(),
          $container  = $('#' + wcpt_id);

      $nav_modal.remove();
      $('body').removeClass('wcpt-nav-modal-on');
      $container[0].scrollIntoView();

      attempt_ajax( $container, query, false, 'filter' );
    })
    //-- sort
    $nav_modal.filter('.wcpt-show-sort').on('change', function(){
      var query = $('<form>').append($nav_modal.clone()).serialize(),
          $container  = $('#' + wcpt_id);

      $nav_modal.trigger('wcpt_close');

      attempt_ajax( $container, query, false, 'filter' );
    })

    // clear
    $nav_modal.find('.wcpt-nm-reset').on('click', function(){
      var query = $('<form>').append($nav_modal.clone()).serialize(),
          $container  = $('#' + wcpt_id),
          query = '';

      $nav_modal.trigger('wcpt_close');

      attempt_ajax( $container, query, false, 'filter' );
    })

    // close
    $nav_modal.find('a.wcpt-nm-close').on('click', function(e){
      e.preventDefault();

      var $container  = $('#' + wcpt_id),
          radios = $.extend( {}, $nav_modal.data('wcpt-radios') );

      $nav_modal.trigger('wcpt_close');

      $.each(radios, function(name, val){
        $wcpt.find('input[type=radio][name="'+ name +'"][value="'+ val +'"]').each(function(){
          $(this).prop('checked', 'checked');
        })
      })


    })

  }

  $('body').on('wcpt_close', '.wcpt-nav-modal', function(){
    var $this = $(this),
        table_id = $this.attr('data-wcpt-table-id'),
        $container = $('#wcpt-' + table_id);

    $this.remove();
    $('body').removeClass('wcpt-nav-modal-on');
    $container[0].scrollIntoView();
  })

  // toggle
  $('body').on('click', '.wcpt-tg-trigger', function(){
    var $this = $(this),
        $toggle = $this.closest('.wcpt-toggle'),
        $table = $this.closest('.wcpt-table'),
        ft = $table.data('freezeTable');
    $toggle.toggleClass(' wcpt-tg-on wcpt-tg-off ');
    if(
      ft &&
      ! ft.disabled
    ){
      $table.freezeTable('cell_resize');
    }

  })

  $('body').on('click', '.wcpt-rn-filter, .wcpt-rn-sort', nav_modal);

  $('body').on('click', '.wcpt-accordion-heading', function(){
    $(this).closest('.wcpt-accordion').toggleClass('wcpt-open');
  });

  // image lightbox
  $('body').on('click', '.wcpt-lightbox-enabled', function(){
    var $this = $(this),
        url = $this.attr('data-wcpt-lightbox'),
        $el = $('<div class="wcpt-lightbox-screen"><img class="wcpt-lightbox-image" src="'+ url +'"></div>');
    $('body').append($el);
    $el.on('click ', function(){
      $el.remove();
    })

  })

  // image zoom
  //-- image hover
  $('body').on('mouseenter', '.wcpt-zoom-enabled[data-wcpt-zoom-trigger="image_hover"]', function(){
    var $this = $(this),
        level = $this.attr('data-wcpt-zoom-level');
    if( ! level ){
      level = '1.5';
    }

    if( $this.closest('.wcpt-device-tablet, .wcpt-device-phone').length ){
      return;
    }


    $this.css({
      'transform': 'scale('+ level +')',
      'z-index' : '2',
    })

    $this.one('mouseleave', function(){
      $this.css({
        'transform': '',
        'z-index' : '',
      })
    })
  })
  //-- row hover
  $('body').on('mouseenter', '.wcpt-row', function(){
    var $row = $(this);
    $row.find( '.wcpt-zoom-enabled[data-wcpt-zoom-trigger="row_hover"]' ).each(function(){
      var $zoom_me = $(this),
          level = $zoom_me.attr('data-wcpt-zoom-level');
      if( ! level ){
        level = '1.5';
      }

      if( $zoom_me.closest('.wcpt-device-tablet, .wcpt-device-phone').length ){
        return;
      }

      $zoom_me.css({
        'transform': 'scale('+ level +')',
        'z-index' : '2',
      })

      $row.one('mouseleave', function(){
        $zoom_me.css({
          'transform': '',
          'z-index' : '',
        })
      })
    })
  })

  // uncheck variation radio
  $('body').on('click', '.wcpt-variation-radio', function(e){
    var $this = $(this),
        $variation = $this.closest('.wcpt-select-variation'),
        $row = $this.closest('.wcpt-row');

    if( $variation.hasClass('wcpt-selected') ){
      $this.prop('checked', false);
      $this.change();

      $row.trigger( 'select_variation', {
        variation_id:  false,
        complete_match:  false,
        attributes: false,
        variation: false,
        variation_found: false,
        variation_selected: false,
        variation_available: false,
      } );
    }
  })

  // variation selected class toggle
  $('body').on('change', '.wcpt-variation-radio', function(){
    var $this = $(this),
        $others = $('.wcpt-variation-radio[name="'+ $(this).attr('name') +'"]').not($(this)),
        $variation = $this.closest('.wcpt-select-variation');

    if( $this.is(':checked') ){
      $variation.addClass('wcpt-selected');
    }else{
      $variation.removeClass('wcpt-selected');
    }

    $others.not(':checked').closest('.wcpt-select-variation').removeClass('wcpt-selected');
  })

  // sync qty between sibling tables
  $('body').on('change', '.wcpt-table input.qty', function(e, syncing){
    var $input = $(this),
        $product_rows = get_product_rows( $input ),
        $siblings = $product_rows.find('input.qty, select.wcpt-qty-select').not(this);

    $siblings.not(this).val($input.val());

    if( ! syncing ){
      $siblings.trigger('change', true);
    }

    var $wc_qty = $('.add_to_cart_button', $product_rows);
    $wc_qty.data('quantity', $input.val());

  })

  // select variations
  //-- sync
  $('body').on('select_variation', '.wcpt-product-type-variable', function(e, data){
    var $row = get_product_rows($(this));

    // update dropdown
    $row.find('.wcpt-select-variation-dropdown').val(data.variation_id);

    // update radio
    $row.find('.wcpt-variation-radio[value="'+ data.variation_id +'"]').prop('checked', true);

    // update row
    $row.data('wcpt_variation',           data.variation);
    $row.data('wcpt_variation_id',        data.variation_id);
    $row.data('wcpt_complete_match',      data.complete_match);
    $row.data('wcpt_attributes',          data.attributes);
    $row.data('wcpt_variation_found',     data.variation_found);
    $row.data('wcpt_variation_selected',  data.variation_selected);
    $row.data('wcpt_variation_available', data.variation_available);

    // update button
    if( ! data.variation_found || ! data.variation_selected || ! data.variation_available ){
      $('.wcpt-button-cart_ajax', $row).addClass('wcpt-disabled');

    }else{
      $('.wcpt-button-cart_ajax', $row).removeClass('wcpt-disabled');

    }

    // TODO: update form

    // update column elements
    if( data.variation ){
      //-- qty input
      var $input = $row.find('.wcpt-quantity-wrapper input[type=number].qty');

      if( $input.length ){
        $input.attr({
          'min' : parseInt( data.variation.min_qty ),
          'max' : parseInt( data.variation.max_qty ),
          // TODO: step
        })

        if( typeof data.variation.min_qty == 'number' && $input.val() < data.variation.min_qty ){
          $input.val( data.variation.min_qty );

        }else if( typeof data.variation.max_qty == 'number' && $input.val() > data.variation.max_qty ){
          $input.val( data.variation.max_qty );

        }

        $input.change();
      }

      //-- qty select
      var $select = $row.find('.wcpt-quantity-wrapper > select.wcpt-qty-select');
      if( $select.length ){

        // re-create select
        var qty_label = $select.attr('data-wcpt-qty-label'),
            max_qty = parseInt($select.attr('data-wcpt-max-qty')),
            val = data.variation.min_qty,
            options = '<option value="'+ data.variation.min_qty +'" selected="selected">'+ qty_label + data.variation.min_qty +'</option>';
        if( data.variation.max_qty ){
          max_qty = data.variation.max_qty;
        }

        while(val < max_qty){
          val += data.variation.step || 1;
          options += '<option>'+ val +'</option>';
        }
        $select.html(options);
        $select.attr('min', data.variation.min_qty)
      }

      //-- product image
      var $product_image_wrapper = $('.wcpt-product-image-wrapper', $row),
          $product_image = $('.wcpt-product-image-wrapper > img', $row);

      if( 
        $product_image.length && 
        data.variation.image.src
      ){        
        $product_image.attr({
          'src': data.variation.image.src,
          'srcset': data.variation.image.srcset
        });

        if( $product_image_wrapper.hasClass('wcpt-lightbox-enabled') ){
          $product_image_wrapper.attr('data-wcpt-lightbox', data.variation.image.full_src);
        }
      }
    }

  })

  //-- -- update from dropdown
  $('body').on('change', '.wcpt-select-variation-dropdown', function(e){
    var $this = $(this),
        $selected = $this.find('option:selected'),
        $row = $this.closest('.wcpt-row');

    $row.trigger( 'select_variation', {
      variation_id: $this.val(),
      complete_match: $selected.hasClass('wcpt-complete_match'),
      attributes: JSON.parse( $selected.attr('data-wcpt-attributes') ),
      variation: JSON.parse( $selected.attr('data-wcpt-variation') ),
      variation_found: true,
      variation_selected: true,
      variation_available: ! $selected.is(':disabled'),
    } );
  })

  //-- -- update from radio
  $('body').on('change', '.wcpt-variation-radio', function(e){
    var $this = $(this),
        $wrapper = $this.closest('.wcpt-select-variation'),
        $row = $this.closest('.wcpt-row');

    if( $this.is(':checked') ){
      $row.trigger( 'select_variation', {
        variation_id: $this.val(),
        complete_match: $wrapper.hasClass('wcpt-complete_match'),
        attributes: JSON.parse( $wrapper.attr('data-wcpt-attributes') ),
        variation: JSON.parse( $wrapper.attr('data-wcpt-variation') ),
        variation_found: true,
        variation_selected: true,
        variation_available: ! $this.is(':disabled'),
      } );
    }
  })

  //-- -- update from form
  $('body').on('woocommerce_variation_has_changed', '.wcpt-row .variations_form', function(e){

    var $form = $(this),
        variations = JSON.parse( $form.attr('data-product_variations') ),
        $row = $form.closest('.wcpt-row'),
        $variation_id = $('.variation_id', $form),
        variation = {},
        attributes = {},
        selected_variation = $variation_id.val();

    $.each( variations, function( index, value ){
      if( parseInt( value['variation_id'] ) == selected_variation ){
        attributes = value.attributes;
        variation = value;
        return false;
      }
    } )
    
    $row.trigger( 'select_variation', {
      variation: variation,
      variation_id: selected_variation,
      complete_match: true,
      attributes: attributes,
      variation_found: true, // ###
      variation_selected: ! $variation_id.siblings('.wc-variation-selection-needed').length,
      variation_available: ! $variation_id.siblings('.wc-variation-is-unavailable').length,
    } );

  })

  // prepare variation options
  // (gets called by 'after_every_load()')
  function prep_variation_options($container){
    $('.wcpt-product-type-variable', $container).each(function(){
      var $row = $(this),
          $dropdown = $('.wcpt-select-variation-dropdown', $row),
          $radio = $('.wcpt-variation-radio', $row),
          $form = $('.variations_form', $row),
          $options = $dropdown.add($radio).add($form);

      // flag availability of options in row
      if( $options.length ){
        $row.data('wcpt_variation_ops', true);
      }

      // init form in this row
      if( $form.length ){
        $form.each(function(){
          var $form = $(this);

          setTimeout(function(){ // need to match setTimeout in add-to-cart-variation.js that delays form init
            // this init syncs the WC form with WCPT native controls including add-to-cart button
            $form.find('select').first().change();
          }, 200);
        })

      // or else init dropdown in this row
      }else if( $dropdown.length ){
        if( $dropdown.val() ){
          $dropdown.trigger('change');
        }

      // or else init radio in this row
      }else if( $radio.length ){
        $radio.filter(':checked').trigger('change');

      }

    })
  }

  // quantity controller

  if ('ontouchstart' in document.documentElement) {
    var mousedown = 'touchstart',
        mouseup = 'touchend';
  }else{
    var mousedown = 'mousedown',
        mouseup = 'mouseup';
  }

  //-- controller mouseDown
  $('body').on(mousedown, '.wcpt-qty-controller', function qty_controller_onMousedown(){
    var $this = $(this),
        $parent = $this.parent(),
        $qty = $this.siblings('.qty'),
        min = $qty.attr('min') ? parseInt($qty.attr('min')) : 0,
        max = $qty.attr('max') ? parseInt($qty.attr('max')) : false,
        step = $qty.attr('step') ? parseInt($qty.attr('step')) : 1,
        val = $qty.val() ? parseInt($qty.val()) : min;

    if( $this.hasClass('wcpt-plus') ){
      $qty.val(val + step).change();
    }else{
      $qty.val(val - step).change();
    }

    var count = 0,
        clear = setInterval(
          function(){
            count++;
            if( count % 5 || count < 50 ){
              return;
            }
            var val = $qty.val() ? parseInt($qty.val()) : min;
            if( $this.hasClass('wcpt-plus') ){
              $qty.val(val + step).change();
            }else{
              $qty.val(val - step).change();
            }
          }, 10
        );

    $this.data('wcpt_clear', clear);

    // stop counter
    $this.on(mouseup, function(){
      var $this = $(this),
          clear = $this.data('wcpt_clear');
      if( clear ){
        clearInterval(clear);
        $this.data('wcpt_clear', false);
      }
    })

    maybe_disable_qty_controllers($parent);
  })

  //-- validator
  $('body').on('change', '.wcpt-quantity-wrapper .qty', function qty_controller_validate(e, syncing){
    var $this = $(this),
        min = $this.attr('min') ? parseInt($this.attr('min')) : 0,
        max = $this.attr('max') ? parseInt($this.attr('max')) : false,
        step = $this.attr('step') ? parseInt($this.attr('step')) : 1,
        val = $this.val() ? parseInt($this.val()) : min;
    
    if( val < min ){
      $this.val(min);
    }

    if( false !== max && val > max ){
      $this.val(max);
    }

    if( ! syncing ){
      $this.trigger('change', true);
    }

    maybe_disable_qty_controllers($(this).parent());
  })

  $('body').on('keypress', '.wcpt-quantity-wrapper .qty', function(e){
    if (e.keyCode == 13) {
      var $rows = get_product_rows($(this));
      $rows.find('.wcpt-button[data-wcpt-link-code^="cart"]').eq(0).click();
    }
 });

  //-- toggle controllers
  function maybe_disable_qty_controllers($qty_wrapper){
    $qty_wrapper.each(function(){
      var $this = $(this),
          $minus = $this.children('.wcpt-minus'),
          $plus = $this.children('.wcpt-plus'),
          $qty = $this.find('.qty'),
          min = $qty.attr('min') ? parseInt($qty.attr('min')) : 0,
          max = $qty.attr('max') ? parseInt($qty.attr('max')) : false,
          step = $qty.attr('step') ? parseInt($qty.attr('step')) : 1,
          val = $qty.val() ? parseInt($qty.val()) : min;
    
      $minus.removeClass('wcpt-disabled');
      if( val - step < min ){
        $minus.addClass('wcpt-disabled');
      }

      $plus.removeClass('wcpt-disabled');
      if( false !== max && val + step > max ){
        $plus.addClass('wcpt-disabled');
      }
    })    
  }

  $('body').on('contextmenu', '.wcpt-noselect', function(){
    return false;
  })

  // player
	$('body').on('click', '.wcpt-player__button', function(){
		var $button = $(this),
        $container = $button.closest('.wcpt-player'),
        src = $container.attr('data-wcpt-src'),
				$el = $container.data('wcpt-media-el');
		
		if( ! $el ){
			$el = $('<audio class="wcpt-audio-elm" src="'+ src +'" loop></audio>');
			$container.append($el);
			$container.data('wcpt-media-el', $el);
		}

		if( $button.hasClass('wcpt-player__play-button') ){
		  $el[0].play();
			
			if( ! $container.hasClass('wcpt-media-loaded') ){
			$el.on('canplay', function(){
				$container.addClass('wcpt-media-loaded');
			})
			}

			$('audio.wcpt-audio-elm').not($el).each(function(){
        this.currentTime = 0;
        this.pause();
			})
			$('.wcpt-player.wcpt-player--playing').not($container).find('.wcpt-player__pause-button').click();
		}else{
		  $el[0].pause();
		}
		
		$container.toggleClass('wcpt-player--playing');
		
  })

  // filter link terms
  $('body').on('click', '.wcpt-filter-link-terms > [data-wcpt-slug]', function(){
    var $this = $(this),
        slug = $this.attr('data-wcpt-slug'),
        taxonomy = $this.parent().attr('data-wcpt-taxonomy'),
        $container = $this.closest('.wcpt'),
        $nav = $container.find('.wcpt-navigation:visible'),
        $option = $nav.find('[data-wcpt-taxonomy="'+ taxonomy +'"] [data-wcpt-slug="'+ slug +'"]'); 

    if( ! $option.length ){      
      return;
    }

    $nav.addClass('wcpt-force-hide-dropdown-menus');
    $option.click();
  })

  // remove
  $('body').on('click', '.wcpt-remove:not(.wcpt-disabled):not(.wcpt-removing)', function(){
    var $this = $(this),
        $row = $product_row = get_product_rows( $this ),
        product_id = $row.attr('data-wcpt-product-id'),
        variation_id = $row.attr('data-wcpt-variation-id'),
        $remove = $row.find('.wcpt-remove'),
        $button = $row.find('.wcpt-button-cart_ajax'),
        button_control = $button.length && ! $button.hasClass('wcpt-disabled');

    $remove.addClass('wcpt-removing');

    $.ajax({
      url: wcpt_i18n.ajax_url,
      method: 'POST',
      beforeSend: function(){
        if( button_control ){
          disable_button($button);
        } 
        loading_badge_on_button($button);
      },
      data: {
        action: 'wcpt_remove_product',
        product_id: product_id,
        variation_id: variation_id
      }
    })
    .done(function( response ){
      $( document.body ).trigger( 'removed_from_cart', [ response.fragments, response.cart_hash, $button ] );

      if( button_control ){
        enable_button($button);
      }
      $button.find( '.wcpt-cart-badge-refresh, .wcpt-cart-badge-number' ).remove();      
    })



  })

  // init
  $('.wcpt').each(function(){
    after_every_load($(this));
  })

})
