(function($) {
	tinymce.PluginManager.add('graphs_lite_mce_btn', function( editor, url ) {
		editor.addButton( 'graphs_lite_mce_btn', {
			title: 'Insert Graph',
			icon: true,
			// text: 'Graph',
			image: gl.admin_dir_url+'images/16x16.png',
			type: 'button',
			onclick: function() {
				$('#gl-admin-meta-box').fadeIn();
			},
		});
	});
})(jQuery);