<template>
	<div>
		<div class="gl_single_graph gl_single_graph_create" v-if="docState === 'add'">
			<div class="gl_graph_box">
				<div class="gl_graph_box_cca">
					<p class="plusIcon">+</p>
					<button class="button button-primary button-large create_new_graph" type="button" @click="docState = 'create'">Add New Graph</button>
				</div>
			</div>
		</div>
		<div class="gl_single_graph gl_single_graph_create" v-if="docState === 'create'">
			<div class="gl_graph_box gl_single_graph_create_content">
				<div class="gl_single_body_content">
					<div class="gl_chart_dropdown_area">
			            <select v-model="selectedChartIndex" @change="changeTabChart">
							<option value="">Select a chart</option>
							<option  v-for="(chartTab, index) in chartTabs" v-bind:key="chartTab.tabFileName" :value="index">{{ chartTab.tabName }}</option>
						</select>
			        </div>
					<button type="button" class="button imgedit-cancel-btn" @click="docState = 'add'">Cancel</button>
				</div>
			</div>
		</div>
		<div class="gl_single_graph" v-for="(graph, index) in allGraph" :key="graph.graph_id">
			<div class="gl_graph_box">
				<canvas :id="index"></canvas>
			</div>
			<div class="gl_control_area">
				<button type="button" class="button button-primary" @click="useGraph(graph.graph_id)">Insert</button>
				<button type="button" class="button button-primary" @click="editGraphDetails(index)">Edit</button>
				<button type="button" class="button imgedit-cancel-btn" @click="deleteGraph(index)">Delete</button>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import { mapGetters } from 'vuex';

	export default {
		data() {
			return {
				currentComponent: '',
				selectedChartIndex: '',
				theChart: [],
				docState: 'add',
				chartTabs: [
                    { tabFileName: 'pieChart', tabName: 'Pie Chart' },
                    { tabFileName: 'doughnutChart', tabName: 'Doughnut Chart' },
                    { tabFileName: 'polarAreaChart', tabName: 'Polar Area Chart' },
                    { tabFileName: 'barChart', tabName: 'Bar Chart' },
                    { tabFileName: 'lineChart', tabName: 'Line Chart' },
                    { tabFileName: 'radarChart', tabName: 'Radar Chart' },
                    { tabFileName: 'bubbleChart', tabName: 'Bubble Chart' },
                    { tabFileName: 'scatterChart', tabName: 'Scatter Chart' }
                ]
			}
		},
		computed: {
			...mapGetters(['allGraph'])
		},
		methods: {
			onLoad() {
				let outerThis = this;
				this.allGraph.forEach(function(value, key) {
					var ctx = document.getElementById(key).getContext('2d');
					outerThis.theChart[key] = new Chart(ctx, {
						type: value.type,
						data: value.data,
						options: value.options
					});
					ctx.height = 295;
				});
			},
			useGraph(id){
				var content = '[graph_lite id="'+id+'"]';
				tinymce.activeEditor.execCommand('mceInsertContent', false, content);
				$('#gl-admin-meta-box').fadeOut();
			},
			changeTabChart() {
				this.currentPageName = this.chartTabs[this.selectedChartIndex].tabName;
				this.currentComponent = this.chartTabs[this.selectedChartIndex].tabFileName;
				this.selectedChartIndex = '';
				this.docState = 'add';

				let withData = { graphIndex: 0, graphData: '', pageName: this.currentPageName, currentComponent: this.currentComponent };
				this.$emit('graphPage', withData);
			},
			editGraphDetails(index) {
				let chartType = this.allGraph[index].type+"Chart";
				let result = this.chartTabs.find(chart => chart.tabFileName === chartType);
				this.currentPageName = result.tabName;
				this.currentComponent = chartType;

				let withData = { graphIndex: index, graphData: this.allGraph[index], pageName: this.currentPageName, currentComponent: this.currentComponent };
				this.$emit('graphPage', withData);
			},
			whenGraphUpdated() {
				let index = this.$store.state.editedGraphIndex;
				if(index != '') {
					this.theChart[index].data.datasets = this.allGraph[index].data.datasets;
					this.theChart[index].options.legend.display = this.allGraph[index].options.legend.display;
					this.theChart[index].options.legend.position = this.allGraph[index].options.legend.position;
					this.theChart[index].options.title.display = this.allGraph[index].options.title.display;
						this.theChart[index].options.title.text = this.allGraph[index].options.title.text;

					if( this.allGraph[index].type == "pie" || this.allGraph[index].type == "doughnut" || this.allGraph[index].type == "polarArea" || this.allGraph[index].type == "bar" || this.allGraph[index].type == "line" || this.allGraph[index].type == "radar" ) {
						this.theChart[index].data.labels = this.allGraph[index].data.labels;
					}
					if( this.allGraph[index].type == "bar" || this.allGraph[index].type == "line" ) {
						this.theChart[index].options.scales.yAxes[0].ticks.beginAtZero = this.allGraph[index].options.scales.yAxes[0].ticks.beginAtZero;
					}
					if( this.allGraph[index].type == "radar") {
						this.theChart[index].options.scale.ticks.beginAtZero = this.allGraph[index].options.scale.ticks.beginAtZero;
					}

					this.theChart[index].update();
					this.$store.commit('emptyEditGraph');
				}
			},
			deleteGraph(index) {
				let deletedGraphId = this.allGraph[index].graph_id;
				const outerThis = this;

				$.sweetModal.confirm('Do you really want to delete the chart?', function() {
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'delete_chart',
							graph_id: deletedGraphId,
						},
						success: function( response ) {
							outerThis.$store.dispatch('deleteGraph', index);
						},
						error: function( error ) {
							alert('Something went wront please try again');
						}
					});
				});
			}
		},
		mounted() {
			this.onLoad();
			this.whenGraphUpdated();
		}
	}
</script>

<style type="text/css" scoped="scoped">
	.gl_chart_dropdown_area {
		margin-bottom: 10px;
	}
	.plusIcon {
		font-size: 50px;
		margin: 0px;
		font-weight: bold;
		margin-top: -65px;
	}
</style>