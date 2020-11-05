var WCPT_Tabs = {};

(function($){

  WCPT_Tabs = {

    $elm: $(),

    init: function(elm){

      // instantiate sub objects
      this.view = Object.create(WCPT_Tabs.View);
      this.ctrl = Object.create(WCPT_Tabs.Ctrl);

      // parent refrence
      this.view.parent = this;
      this.ctrl.parent = this;

      // relate $elm and this
      this.$elm = $(elm);
      this.$elm.data('wcpt_tabs', this);

      // inital view
      this.view.render();

      // attach controller
      $( '> .wcpt-tab-triggers', this.$elm ).on('click', '.wcpt-tab-trigger', this.ctrl.trigger_tab);
      $( '> .wcpt-tab-triggers', this.$elm ).on('click', '.wcpt-tab-disable', this.ctrl.disable_tab);
      $( '> .wcpt-tab-triggers', this.$elm ).on('click', '.wcpt-tab-enable', this.ctrl.enable_tab);
    },

    Ctrl: {
      get_parent: function(elm){
        return $(elm).closest('.wcpt-tabs').data('wcpt_tabs');
      },

      trigger_tab: function(e){
        var $trigger = $(e.target).closest('.wcpt-tab-trigger'),
            tabs_instance = WCPT_Tabs.Ctrl.get_parent($trigger),
            tab_index = $trigger.index();
        tabs_instance.view.open(tab_index);
      },

      disable_tab: function(e){
        var $trigger = $(e.target).closest('.wcpt-tab-trigger'),
            tabs_instance = WCPT_Tabs.Ctrl.get_parent($trigger),
            tab_index = $trigger.attr('wcpt-index'),
            $enable_trigger = $trigger.siblings('.wcpt-disabled-tabs').find('[wcpt-index="'+ tab_index +'"]'),
            $tabs = $trigger.closest('.wcpt-tabs');

        if( $trigger.hasClass('wcpt-selected-tab') ){
          $trigger.hide();
          $enable_trigger.show();

          tabs_instance.view.open(0);
          $tabs.trigger('tab_disabled', tab_index);
          e.stopPropagation();
        }
      },

      enable_tab: function(e){
        var $en_trigger = $(e.target).closest('.wcpt-tab-enable'),
            tab_index = $en_trigger.attr('wcpt-index'),
            $trigger = $en_trigger.closest('.wcpt-disabled-tabs').siblings('[wcpt-index="'+ tab_index +'"]'),
            tabs_instance = WCPT_Tabs.Ctrl.get_parent($trigger),
            $tabs = $trigger.closest('.wcpt-tabs');
        $trigger.show();
        $en_trigger.hide();

        tabs_instance.view.open(tab_index);
        $tabs.trigger('tab_enabled', tab_index);
      },

      enable_tab_index: function(tab_index){
        var $tabs   = this.parent.$elm,
            $tab    = $('> .wcpt-tab-triggers > [wcpt-index="'+ tab_index +'"]', $tabs),
            $enable = $('> .wcpt-tab-triggers > .wcpt-disabled-tabs > [wcpt-index="'+ tab_index +'"]', $tabs);
        $tab.show();
        $enable.hide();
      }
    },

    View: {

      parent,

      render: function(){
        var $tabs = this.parent.$elm,
            $triggers_wrapper = $tabs.children('.wcpt-tab-triggers'),
            $enable_wrapper = $triggers_wrapper.children('.wcpt-disabled-tabs');
        if( ! $enable_wrapper.length ){
          $enable_wrapper = $('<div class="wcpt-disabled-tabs">');
          $triggers_wrapper.append($enable_wrapper);
        }

        $tabs.find('.wcpt-tab-trigger').each(function(){
          var $trigger = $(this),
              index = $trigger.index();
          $trigger.attr('wcpt-index', index);
          $trigger.siblings('')
          if( $trigger.is('[wcpt-can-disable]') ){
            $enable_wrapper.append('<span class="wcpt-tab-enable" wcpt-index="'+ index +'">+ '+ $trigger.text() +'</span>');
            if( ! $trigger.children('.wcpt-tab-disable').length ){
              var x = $('#wcpt-icon-x').length ? $('#wcpt-icon-x').html() : 'x';
              $trigger.append(' <i class="wcpt-tab-disable">'+ x +'</i>');
            }
            $trigger.hide();
          }
        })

        this.open(0);
      },

      open: function(index){
        var $tabs = this.parent.$elm;
        $tabs.children('.wcpt-tab-content').eq(index).show().siblings('.wcpt-tab-content').hide();

        $tabs.children('.wcpt-tab-triggers').children('.wcpt-tab-trigger')
          .eq(index).addClass('wcpt-selected-tab')
          .siblings().removeClass('wcpt-selected-tab');

        $tabs.children('.wcpt-tab-content').eq(index).addClass('wcpt-selected-tab');

      }
    }
  };

  $.fn.wcpt_tabs = function() {
    return this.each(function() {
      var tabs = $(this).data('wcpt_tabs');
      if( ! tabs ){
        Object.create(WCPT_Tabs).init(this);
      }
    });
  };

})(jQuery)
