jQuery(function($){

  $.fn.freezeTable = function (options) {
    return this.each(function () {
      
      var $this = $(this);

      if( $this.hasClass('frzTbl-clone-table') ){
        return;
      }

      // destroy
      if( options == 'destroy' ){
        if( $this.data('freezeTable') ) {
          $this.data('freezeTable').destroy();
        }
        return true;
      }

      // create
      if( ! $this.data('freezeTable') ){
        $this.data('freezeTable', new $.FreezeTable(this, options));
        return true;
      }

      // resize
      if( options == 'resize' ){
        $this.data('freezeTable').resize();
        return true;
      }

      // cell resize
      if( options == 'cell_resize' ){
        $this.data('freezeTable').cell_resize();
        return true;
      }
      
      // reload
      $this.data('freezeTable').reload(options);

    });
    
  };

  $.FreezeTable = FreezeTable;

  function FreezeTable (table, options) {
    this.el = {
      $table: $(table)
    }

    this.ev = {
      touchstart: false
    }    

    this.options = $.extend(true, {}, this.default_options, typeof options === 'object' ? options : {} );

    this.namespace = Math.floor((Math.random() * 100000) + 1);

    if( this.options.height && ! this.options.force_sticky_outer_heading ){
      this.options._sticky_outer_heading = false;

    }else if( this.options.force_sticky_outer_heading ){
      this.options._sticky_outer_heading = true;
    }

    this.build();
  };

  FreezeTable.prototype.default_options = {
    left: 0,
    right: 0,
    heading: 0,
    offset: 0,

    wrapperWidth: 0,
    wrapperHeight: 0,

    tableWidth: 0,

    captureScroll: false,
    force_sticky_outer_heading: false,
    _sticky_outer_heading: true
  };

  // unwrap if FT not required at this window size 
  FreezeTable.prototype.maybe_disable = function() {
    var settings = this.get_breakpoint_options(),
        $table = this.el.$table,
        $container = $table.closest('.frzTbl').length ? $table.closest('.frzTbl') : $table.parent(),
        container_width = settings.wrapperWidth ? settings.wrapperWidth : $container.width(),
        table_original_width = $table[0].style.width,
        table_compressed_width = $table.outerWidth(container_width).outerWidth();

    $table[0].style.width = table_original_width;

    $(window).off('resize.ft' + this.namespace);

    if(
      ! settings.tableWidth &&
      table_compressed_width <= container_width &&
      ! settings.left &&
      ! settings.right && 
      ! settings.heading
    ){
      this.unwrap();
      this.disabled = true;

      // register event handler to check if FT required upon future resize
      $(window).on('resize.ft' + this.namespace, $.proxy(this, 'try_enable'));
      return true;
    }

  }

  // throttles event handler, attempts 'build' every 200 ms
  FreezeTable.prototype.try_enable = function() {
    var _build = $.proxy(this, 'build');
    clearTimeout(this.try_enable_clear);
    this.try_enable_clear = setTimeout(_build, 200);

  };

  FreezeTable.prototype.build = function() {
    if( this.maybe_disable() ){
      // $(window).off('resize.ft' + this.namespace, $.proxy(this, 'try_enable'));
      this.disable = false;
      return;
    }

    var settings = this.get_breakpoint_options(),

        $table = this.el.$table, 
        $temp_wrapper = $('<div class="frzTbl frzTbl--temp-wrapper">').insertBefore($table),
        
        tpl_master = $('#frzTbl-tpl').html();
    
    if( settings.tableWidth ){
      $table.width(settings.tableWidth);
    }else{
      $table.width('');
    }

    var table_height = $table.height(),
        table_width = $table.width();

    // unrestricted table
    if(
      settings.left || 
      settings.right &&
      ! settings.tableWidth
    ){
      $temp_wrapper[0].innerHTML = '<div>'+ $table[0].outerHTML +'</div>';
      table_width = Math.max( $('>div>table', $temp_wrapper).outerWidth(), $temp_wrapper.outerWidth() );
      table_height = $('>div>table', $temp_wrapper).outerHeight();
      $table.width(table_width);
    }

    var wrapper_width = this.options.wrapperWidth ? this.options.wrapperWidth : '',
        wrapper_height = this.options.wrapperHeight ? this.options.wrapperHeight : table_height;

    $temp_wrapper.remove();

    this.tpl = tpl_master
      .replace(/{{wrapper_height}}/g,'height:' +  wrapper_height + 'px; ')
      .replace(/{{wrapper_width}}/g, wrapper_width ? 'width:' + wrapper_width + 'px; ' : '')
      .replace(/{{table_height}}/g, 'height:' + table_height + 'px; ')
      .replace(/{{table_width}}/g, 'width:' + table_width + 'px; ');

    this.build_heading();
    this.build_left();
    this.build_right();

    $table.addClass('frzTbl-table'); 

    var $wrapper = this.el.$wrapper = $(this.tpl).insertBefore($table);
    $wrapper.find('.frzTbl-table-placeholder').replaceWith($table);

    var $html = $('html'),
        $window = $(window);

    // record components
    this.el.$firstCell = this.el.$table.find('.wcpt-cell:first');

    this.el.$scrollOverlay = this.el.$wrapper.children('.frzTbl-scroll-overlay');
    this.el.$scrollOverlayInner = this.el.$scrollOverlay.children('.frzTbl-scroll-overlay__inner');

    this.el.$contentWrapper = this.el.$wrapper.children('.frzTbl-content-wrapper');
    this.el.$frozenColumnsWrapper = this.el.$contentWrapper.children('.frzTbl-frozen-columns-wrapper');
    this.el.$frozenColumnsInner = this.el.$frozenColumnsWrapper.children('.frzTbl-frozen-columns-wrapper__inner');
    this.el.$frozenColumnsLeft = this.el.$frozenColumnsInner.children('.frzTbl-frozen-columns-wrapper__columns--left');
    this.el.$frozenColumnsLeftSticky = this.el.$frozenColumnsLeft.children('.frzTbl-top-sticky');
    this.el.$frozenColumnsRight = this.el.$frozenColumnsInner.children('.frzTbl-frozen-columns-wrapper__columns--right');
    this.el.$frozenColumnsRightSticky = this.el.$frozenColumnsRight.children('.frzTbl-top-sticky');

    this.el.$fixedHeadingWrapperOuter = this.el.$contentWrapper.children('.frzTbl-fixed-heading-wrapper-outer');
    this.el.$fixedHeadingWrapper = this.el.$fixedHeadingWrapperOuter.children('.frzTbl-fixed-heading-wrapper');
    this.el.$fixedHeadingLeftColumn = this.el.$fixedHeadingWrapperOuter.children('.frzTbl-fixed-heading-wrapper__columns--left');
    this.el.$fixedHeadingRightColumn = this.el.$fixedHeadingWrapperOuter.children('.frzTbl-fixed-heading-wrapper__columns--right');
    this.el.$fixedHeadingInner = this.el.$fixedHeadingWrapper.children('.frzTbl-fixed-heading-wrapper__inner');

    this.el.$tableWrapper = this.el.$contentWrapper.children('.frzTbl-table-wrapper');
    this.el.$tableInner = this.el.$tableWrapper.children('.frzTbl-table-wrapper__inner');
    this.el.$tableWrapperSticky = this.el.$tableInner.children('.frzTbl-top-sticky');

    this.sticky_heading();

    this.resize_cells();

    this.antiscroll();

    // wheel/scroll
    this.el.$wrapper.on('wheel', $.proxy(this, 'wrapper_wheel'));
    this.el.$wrapper.on('touchstart touchmove touchend', $.proxy(this, 'wrapper_touch'));
    this.el.$scrollOverlay.on('wheel scroll', $.proxy(this, 'scrollOverlay_wheel'));

    var affected = [
      this.el.$scrollOverlay,
      this.el.$tableWrapper,
      this.el.$fixedHeadingWrapper,
      this.el.$frozenColumnsWrapper,
    ];

    $.each(affected, function(i, $elm){
      $elm[0].scrollTop = 0;
      $elm[0].scrollLeft = 0;
    })

    $window.on('resize.ft' + this.namespace , $.proxy(this, 'resize'));

    var cell_resize = $.proxy(this, 'cell_resize');
    this.el.$table[0].addEventListener(
      'load',
      function(event){
        event.target.tagName == 'IMG' && cell_resize(event.target)
      },
      true
    );

    this.recordEnv();
  };

  FreezeTable.prototype.build_heading = function() {
    if( this.get_breakpoint_options().heading ){
      var $heading = this.clone_table();
      $heading.find('tbody').remove();  
      this.tpl = this.tpl.replace(/{{heading}}/g, $heading[0].outerHTML);
    } else {
      this.tpl = this.tpl.replace(/{{heading}}/g, '');
    }
  };

  FreezeTable.prototype.sticky_heading = function() {
    var settings = this.get_breakpoint_options(),
        offset = settings.offset ? settings.offset : 0,
        $heading = this.el.$table.children('thead'),
        border_height = parseInt( $heading.children('tr').css('border-bottom-width') ),
        heading_height = $heading[0].getBoundingClientRect().height + ( border_height / 2 );

    this.el.$fixedHeadingWrapperOuter.css({
      height: heading_height,
      top: offset + 'px',
    });

    if( ! settings._sticky_outer_heading ){
      this.el.$fixedHeadingWrapperOuter.hide();
    }else{
      this.el.$fixedHeadingWrapperOuter.show();
    }

    $('> div > table', this.el.$frozenColumnsInner).each(function(){
      var $this = $(this);
      $('> thead', $this).remove();
      $this.css('margin-top', (heading_height) + 'px');
    })

    if( ! settings.heading ){
      this.el.$fixedHeadingWrapperOuter.css({position: 'relative'});
    }

    this.el.$wrapper.parents().each(function(){      
      var $this = $(this),
          overflow = $this.css('overflow');
      if(
        ! $this.is('html') && 
        ! $this.is('body') &&
        overflow && 
        overflow !== 'visible' 
      ){
        $this.addClass('frzTbl-force-parent-overlow-visible');
      }
    });

  };

  FreezeTable.prototype.build_left = function() {
    var $left = this.clone_table(),
        settings = this.get_breakpoint_options();
    $left.find('td, th').each(function(){
      var $this = $(this);
      if( $this.index() >= settings.left ){
        $this.remove();
      }
    })
    this.tpl = this.tpl.replace(/{{left-columns}}/g, ($left[0].outerHTML || '') );

    $left_heading = $left.clone();
    $left_heading.find('tbody').remove();
    this.tpl = this.tpl.replace(/{{left-columns-heading}}/g, ($left_heading[0].outerHTML || '') );
  };

  FreezeTable.prototype.build_right = function() {
    var $right = this.clone_table(),
        settings = this.get_breakpoint_options();
    $right.find('td, th').each(function(){
      var $this = $(this);
      if( $this.siblings().length - $this.index() >= settings.right ){
        $this.remove();
      }
    })
    this.tpl = this.tpl.replace(/{{right-columns}}/g, ($right[0].outerHTML || '') );

    $right_heading = $right.clone();
    $right_heading.find('tbody').remove();
    this.tpl = this.tpl.replace(/{{right-columns-heading}}/g, ($right_heading[0].outerHTML || '') );
  };

  FreezeTable.prototype.clone_table = function() {
    var $table = this.el.$table,
        $cloneTable = this.el.$table.clone();
    
    $cloneTable
      .width('')
      .addClass('frzTbl-clone-table');

    $( '> tbody > tr > td, > thead > tr > th', $cloneTable ).each(function(){
      var $this = $(this);
      $this.attr({
        'data-index': $this.index(),
      });
    });

    return $cloneTable;
  };

  FreezeTable.prototype.resize_cells = function() {
    requestAnimationFrame($.proxy(this, '_resize_cells' ));
  };

  FreezeTable.prototype._resize_cells = function() {
    var $table = this.el.$table,
        $cloneTables = this.get_clone_tables();
    
    var $cloneCells = $( '> tbody > tr > td, > thead > tr > th', $cloneTables ),
        dimensions = [];

    // read styles
    $cloneCells.each(function(){
      var $this = $(this),
          $row = $this.parent(),
          wrapper = $row.parent().is('thead') ? 'thead' : 'tbody',
          selector = '> '+ wrapper +' > tr:nth-child('+ ($row.index() + 1) +') > *:nth-child('+ ( parseInt($this.attr('data-index')) + 1) +')',
          $original = $(selector, $table);

      dimensions.push({
        width: $original[0].getBoundingClientRect().width,
        rowOuterHeight: $original.parent()[0].getBoundingClientRect().height
      });
    });

    // write styles
    $cloneCells.each(function(i){
      var $this = $(this);

      $this.css({
        width: dimensions[i].width,
        minWidth: dimensions[i].width,
      });

      $this.parent().outerHeight(dimensions[i].rowOuterHeight);
    });    
  };

  FreezeTable.prototype.get_clone_tables = function() {
    var $table = this.el.$table,
        $cloneTables = $();
    $.each(this.el, function(name, $el){
      var $childTables = $el.children().filter(function(){ 
        return $(this).is('table') && this !== $table[0]
      });
      $cloneTables = $cloneTables.add($childTables);
    });

    return $cloneTables;
  };

  FreezeTable.prototype.wrapper_wheel = function(e) {

    if(
      "webkitLineBreak" in document.documentElement.style &&
      ! e.originalEvent.deltaX &&
      ! this.get_breakpoint_options().wrapperHeight
    ){
      return true;
    }

    var $wrapper = this.el.$wrapper,
        scrolling = 'frzTbl--scrolling';

    $wrapper.addClass(scrolling);
    clearTimeout(this.scroll_clear);
    this.scroll_clear = setTimeout(
      function(){        
        $wrapper.removeClass(scrolling);
      }, 300
    );

    e.preventDefault();
     
    if( ! this.options.captureScroll || ! this.options.wrapperHeight ){
      if(
        // no scroll
        this.el.$scrollOverlay[0].scrollHeight == this.el.$scrollOverlay.height() ||
        // scroll down
        (
          e.originalEvent.deltaY > 0 &&
          this.el.$scrollOverlay[0].scrollTop + this.el.$scrollOverlay.height() == this.el.$scrollOverlayInner.height()
        ) ||
        // scroll up
        (
          e.originalEvent.deltaY < 0 &&
          ! this.el.$scrollOverlay[0].scrollTop
        )
      ){
        $('html')[0].scrollTop += e.originalEvent.deltaY;      
        $('body')[0].scrollTop += e.originalEvent.deltaY;
      }  
    }

  };

  FreezeTable.prototype.wrapper_touch = function(e) {

    if( e.type == 'touchstart' ){
      this.el.$scrollOverlay.stop(true);
    }

    if(
      e.type == 'touchmove' && 
      this.ev.prevClientX !== false
    ){

      var diffX = this.ev.prevClientX - e.originalEvent.touches[0].clientX,
          diffY = this.ev.prevClientY - e.originalEvent.touches[0].clientY;

      var e2 = {
        originalEvent: { 
          deltaX: diffX, 
          deltaY: diffY 
        }
      };

      this.scrollOverlay_wheel(e2);

      // prep animate scroll      
      if( Math.abs(diffX) > 5 ){     
        this.ev.animScroll = 20 * diffX + this.el.$scrollOverlay[0].scrollLeft;
      }else{
        this.ev.animScroll = false;
      }
    }

    if( e.type == 'touchend' ){

      if( this.ev.animScroll ){        
        this.el.$scrollOverlay.animate({scrollLeft: this.ev.animScroll}, {
          specialEasing: {
            scrollLeft : 'easeOutQuad',
          }
        });
        this.ev.animScroll = false;
      }

      this.ev.prevClientX = false;
      this.ev.prevClientY = false;

    }else{
      this.ev.prevClientX = e.originalEvent.touches[0].clientX,
      this.ev.prevClientY = e.originalEvent.touches[0].clientY;

    }
  };

  FreezeTable.prototype.scrollOverlay_wheel = function(e) {
        
    var $scrollOverlay = this.el.$scrollOverlay,
        scrollTop = $scrollOverlay[0].scrollTop,
        scrollLeft = $scrollOverlay[0].scrollLeft,
        deltaX = e.originalEvent.deltaX || 0,
        deltaY = e.originalEvent.deltaY || 0;

    scrollLeft += deltaX;
    scrollTop += deltaY;

    $scrollOverlay[0].scrollTop = scrollTop;
    $scrollOverlay[0].scrollLeft = scrollLeft;

    // scroll
    this.el.$tableWrapper[0].scrollTop = scrollTop;
    this.el.$tableWrapper[0].scrollLeft = scrollLeft;
    this.el.$fixedHeadingWrapper[0].scrollLeft = scrollLeft;
    this.el.$frozenColumnsWrapper[0].scrollTop = scrollTop;

    // transform
    // this.el.$tableInner[0].style.transform = 'translate3d(-' + scrollLeft + 'px, -' + scrollTop + 'px, 0)';
    // this.el.$fixedHeadingInner[0].style.transform = 'translate3d(-' + scrollLeft + 'px, 0, 0)';
    // this.el.$frozenColumnsInner[0].style.transform = 'translate3d(0, -' + scrollTop + 'px, 0)';

    this.el.$wrapper.removeClass('frzTbl--scrolled-to-left-edge frzTbl--scrolled-to-right-edge');
    if( ! $scrollOverlay[0].scrollLeft ){
      this.el.$wrapper.addClass('frzTbl--scrolled-to-left-edge');
    }
    if( $scrollOverlay[0].scrollLeft + $scrollOverlay.width() >= this.el.$scrollOverlayInner.width() ){
      this.el.$wrapper.addClass('frzTbl--scrolled-to-right-edge');
    }    

  };

  FreezeTable.prototype.get_breakpoint_options = function() {
    var settings = this.get_options(),
    current_bp = this.current_breakpoint();
    
    if( current_bp ){
      var ops = $.extend(true, {}, this.default_options, settings.breakpoint[current_bp]);
      return ops;
    }

    return settings;
  };

  FreezeTable.prototype.get_options = function() {
    return $.extend(true, {}, this.options);
  };  

  FreezeTable.prototype.resize = function() {
    var _resize = $.proxy(this, '_resize');
    clearTimeout(this.resize_clear);
    this.resize_clear = setTimeout(_resize, 200);
  };

  FreezeTable.prototype._resize = function() {
    var wrapperWidth = this.el.$wrapper.width();

    if( this.env.wrapperWidth !== wrapperWidth ){
      if( this.crossed_breakpoint() ){
        this.reload(this.get_options());
        return;
      }

      this.antiscroll();
      this.recordEnv();
    }
  };

  FreezeTable.prototype.cell_resize = function(cell) {    
    // unrestricted table
    if( ! this.get_breakpoint_options().tableWidth ){
      this.el.$tableInner.addClass('frzTbl-table-wrapper__inner--unrestrict-table-wrapper');
      var table_width = Math.ceil(this.el.$table[0].getBoundingClientRect().width),
          table_height = this.el.$table.height();
      this.el.$tableInner.removeClass('frzTbl-table-wrapper__inner--unrestrict-table-wrapper');
    // restricted table
    }else{
      var table_width = this.el.$table.width(),
          table_height = this.el.$table.height();
    }

    var $affected = [
      this.el.$scrollOverlayInner,
      this.el.$tableInner
    ];

    if( ! this.get_breakpoint_options.wrapperHeight ){
      $affected.push( this.el.$wrapper );
    }

    $.each($affected, function(key, $elm){
      $elm.css({
        height: table_height,
        width: table_width,
      });
    });

    this.resize_cells(cell);
    this.antiscroll();

  };

  FreezeTable.prototype.antiscroll = function() {
    var $table = this.el.$table;
    this.el.$wrapper.antiscroll().find('> .frzTbl-antiscroll-wrap').remove();

    $('> .antiscroll-scrollbar-horizontal', this.el.$wrapper).wrap('<div class="frzTbl-antiscroll-wrap">')
  };

  FreezeTable.prototype.crossed_breakpoint = function() {
    return this.current_breakpoint() !== this.env.breakpoint
  };

  FreezeTable.prototype.recordEnv = function() {
    var _ = this; 
    _.env = {
      // window
      windowWidth: $(window).width(),
      windowHeight: $(window).height(),

      // wrapper
      wrapperWidth: this.el.$wrapper.width(),
      wrapperHeight: this.el.$wrapper.height(),

      // table
      tableWidth: _.el.$table.width(),
      tableHeight: _.el.$table.height(),

      // first cell
      firstCellWidth: _.el.$firstCell.width(),
      firstCellHeight: _.el.$firstCell.height(),

      // breakpoint
      breakpoint: _.current_breakpoint(),
    }
  };

  FreezeTable.prototype.current_breakpoint = function() {
    var settings = this.get_options(),
        breakpoint = false,
        windowWidth = $(window).width();

    if( ! settings.breakpoint ){
      return false;
    }

    $.each(settings.breakpoint, function(bp, bp_settings){
      var bp = parseInt( bp );
      if( bp < windowWidth ){
        return true;
      }

      if( ! breakpoint || bp < breakpoint ){
        breakpoint = bp;
      }
    })

    return breakpoint;
  };

  FreezeTable.prototype.destroy = function() {
    this.unwrap();

    $(window).off('resize.ft' + this.namespace);
    this.el.$table.removeData('freezeTable');
  };

  FreezeTable.prototype.unwrap = function() {
    var $table = this.el.$table,
        $wrapper = this.el.$wrapper;

    if( ! $wrapper || ! $wrapper.length ){
      return;
    }

    $table
      .insertBefore($wrapper)
      .removeClass('frzTbl-table')
      .css('width', '');
    $wrapper.remove();
  };

  FreezeTable.prototype.reload = function(options) {
    var $table = this.el.$table;
    this.destroy();
     
    $table.data('freezeTable', new $.FreezeTable($table[0], options));
  };

  $.extend($.easing,
    {
      easeOutQuad: function (x, t, b, c, d) {
        return -c *(t/=d)*(t-2) + b;
      },
    }
  );

});