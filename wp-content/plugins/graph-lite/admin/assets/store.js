export default {
	state: {
		allGraph: [],
		editedGraphIndex: ''
	},
	getters: {
		allGraph: state => {
			return state.allGraph;
		}
	},
	mutations: {
		onLoad(state) {
			state.allGraph = global_chart_data;
		},
		addNewGraph(state, newGraph) {
			state.allGraph.push(newGraph);
		},
		updateGraph(state, details) {
			let dataIndex = details.graphIndex;
			let graphDetails = details.chartDetails;
			state.allGraph[dataIndex].data = graphDetails.data;
			state.allGraph[dataIndex].options = graphDetails.options;
			state.editedGraphIndex = dataIndex;
		},
		deleteGraph(state, index) {
			state.allGraph.splice(index, 1);
		},
		emptyEditGraph(state) {
			state.editedGraphIndex =  '';
		}
	},
	actions: {
		onLoad: context => {
			context.commit('onLoad');
		},
		addNewGraph: (context, newGraph) => {
			$.ajax({
				url: gl.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'save_chart',
					graph_data: newGraph,
				},
				success: function( response ) {
					newGraph.graph_id = response;
					context.commit('addNewGraph', newGraph);
					var content = '[graph_lite id="'+response+'"]';
					tinymce.activeEditor.execCommand('mceInsertContent', false, content);
					$('#gl-admin-meta-box').fadeOut();
					$('div#gl-admin-meta-box').find('input:text').val('');
				},
				error: function( error ) {
					alert('Something went wront please try again');
				}
			});
		},
		updateGraph(context, editedGraphDetails) {
			let graphDetails = editedGraphDetails.chartDetails;
			let graphId = editedGraphDetails.graph_id;

			$.ajax({
				url: gl.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'update_chart',
					graph_id: graphId,
					updated_graph_data: graphDetails
				},
				success: function( response ) {
					context.commit('updateGraph', editedGraphDetails);
					$.sweetModal({
						content: 'Updated',
						icon: $.sweetModal.ICON_SUCCESS,
						timeout: 1300,
						showCloseButton: false
					});
				},
				error: function( error ) {
					alert('Something went wront please try again');
				}
			});
		},
		deleteGraph: (context, index) => {
			context.commit('deleteGraph', index);
		}
	}
}