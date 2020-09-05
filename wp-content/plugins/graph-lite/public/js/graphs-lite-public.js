(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	function gl_findAndReplace(object, value, replacevalue){
	  for(var x in object){
	    if(typeof object[x] == typeof {}){
	      gl_findAndReplace(object[x], value, replacevalue);
	    }
	    if(object[x] == value){
	      object[x] = replacevalue;
	      // break;
	    }
	  }
	};

	$('.gnc-plugin-charts').each(function(index, data){
		var chart_id = $(this).attr('data-attr');
		var data_var_name = "gnc_plugin_data_" + chart_id;

		var card_data = JSON.parse(window[data_var_name].chart_data);

		gl_findAndReplace(card_data, 'true', true);
		gl_findAndReplace(card_data, 'false', false);

		var ctx = document.getElementById("gnc-plugin-chart-" + chart_id);
		new Chart(ctx, card_data);
	});
})( jQuery );
