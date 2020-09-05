jQuery(document).ready(function($) {

	$('div#gl-admin-meta-box h2.hndle.ui-sortable-handle').remove();

	$('#gl-admin-meta-box').appendTo('body');
	$('button.close_graph_modal').appendTo('div#gl-admin-meta-box h2.hndle');
	$('#gl-admin-meta-box').hide();

	$('div#gl-admin-meta-box button.handlediv').remove();
	$('div#gl-admin-meta-box').removeClass('postbox');

	$('.close_graph_modal').on('click', function () {
		$('#gl-admin-meta-box').fadeOut();
	});

	// Nav Menu
	$('.gl_nav nav').click(function(e){

		$('.gl_nav nav').css('background-color', '#3e8e41');

		$(this).css('background-color', '#3473aa');

	});

	// $('.gl_old_graph').hide();

	$('nav#gl_new_chart').click(function(){
		$('.gl_old_graph').hide();
		$('.gl_new_chart').fadeIn();
	});

	$('nav#gl_old_chart').click(function(){
		$('.gl_new_chart').hide();
		$('.gl_old_graph').fadeIn();
	});

	// Replace string to boolean
	// var card_data = JSON.parse(gl.chart_data);
	// function gl_findAndReplace(object, value, replacevalue){
	//   for(var x in object){
	//     if(typeof object[x] == typeof {}){
	//       gl_findAndReplace(object[x], value, replacevalue);
	//     }
	//     if(object[x] == value){
	//       object[x] = replacevalue;
	//       // break;
	//     }
	//   }
	// }

	// gl_findAndReplace(card_data, 'true', true);
	// gl_findAndReplace(card_data, 'false', false);

	// var ctx = document.getElementById("Chart");
	// new Chart(ctx, card_data);
});