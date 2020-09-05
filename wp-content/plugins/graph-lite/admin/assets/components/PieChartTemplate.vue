<template>
	<div class="gl_chart_template" id="pie">
		<div class="gl_graphOptions">
			<table class="form-table">
				<tr>
					<th scope="row" class="gl_backButotnTh"><button class="gl_backButton" type="button" @click="goBacktoAllGraphPage">Go Back</button></th>
					<td></td>
				</tr>
				<tr>
					<th scope="row"><label for="labels">Labels*</label></th>
					<td>
						<input class="regular-text" :class="{'gl_fieldRequired': ifLabelsEmpty}" type="text" id="labels" placeholder="Comma separated list of labels" v-model="chartlabelString" @keyup="addLabels">
						<p class="gl_fieldRequiredError" v-if="ifLabelsEmpty">*required</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="datasets">Data*</label></th>
					<td>
						<input class="regular-text" :class="{'gl_fieldRequired': ifDataEmpty}" type="text" id="datasets" placeholder="Numeric data value for each label. Eg. 1,2,3 etc" v-model="chartDatasetDataString" @keyup="addDatasetData">
						<p class="gl_fieldRequiredError" v-if="ifDataEmpty">*required</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="colors">Color*</label></th>
					<td class="gl_colorPickerTd" v-on-clickaway="clickedAway">
						<input class="regular-text" :class="{'gl_fieldRequired': ifBackgroundEmpty}" type="text" id="colors" placeholder="Color value for each label. Eg. red, green, blue" v-model="chartDatasetBgColorString" @keyup="addDatasetBgColor" @focus="showBackgroundColorPickerField">
						<div class="gl_colorPickerDiv">
							<chrome-picker v-model="setBackgroundColor" v-if="backgroundColorFieldFocused" />
							<div class="gl_pickOrCloseColorPickerDiv">
								<button class="gl_colorPickerButton" type="button" @click="pickBackgroundColor" v-if="backgroundColorFieldFocused">Pick</button>
								<button class="gl_colorPickerButton" type="button" @click="hideBackgroundColorPickerField" v-if="backgroundColorFieldFocused">Close</button>
								<div style="clear: both;"></div>
							</div>
						</div>
						<p class="gl_fieldRequiredError" v-if="ifBackgroundEmpty">*required</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="titleText">Chart Title</label></th>
					<td><input class="regular-text" type="text" id="titleText" placeholder="Title for the chart" v-model="titleText" @keyup="addTitleText"></td>
				</tr>
				<tr>
					<th scope="row"><label for="legend">Show Label</label></th>
					<td><input type="checkbox" id="legend" v-model="showLegend" @change="showingGraphLegend"></td>
				</tr>
				<tr>
					<th scope="row"><label for="legend_position">Label Position</label></th>
					<td>
						<select id="legend_position" v-model="legendPosition" @change="changeLegendPosition">
							<option selected="selected" value="top">Top</option>
							<option value="bottom">Bottom</option>
							<option value="left">Left</option>
							<option value="right">Right</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label></label></th>
					<td v-if="graphData == ''"><button type="button" class="gl_saveGraphData" @click="saveGraphData">Save</button></td>
					<td v-else><button type="button" class="gl_saveGraphData" @click="updateGraphData">Update</button></td>
				</tr>
			</table>
		</div>
		<div class="gl_graphDiv">
			<!-- <iframe class="gl_tutorialFrame" v-if="showTutorial" width="560" height="315" src="https://www.youtube.com/embed/Hwn4UKc5Bew?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe> -->
			<div class="gl_dummyMessages" v-if="showTutorial">
				<h2>Start typing to see live preview</h2>
				<p>Live preview will appear here after you enter some data</p>
			</div>
			<div class="gl_graphChildDiv" v-show="!showTutorial">
				<canvas id="pieChart"></canvas>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	import { Chrome } from 'vue-color';
	import { mixin as clickaway } from 'vue-clickaway2';

	export default {
		mixins: [ clickaway ],
		props: ['graphData', 'graphIndex'],
		data() {
			return {
				chartType: 'pie',
				chartlabelString: '',
				chartDatasetDataString: '',
				chartDatasetBgColorString: '',
				titleText: '',
				editedGraphIdNo: '',
				legendPosition: 'top',
				setBackgroundColor: '',
				backgroundConcatCount: 0,
				labels: [],
				datasets: [
					{
						data: [],
						backgroundColor: []
					}
				],
				showTitle: false,
				showLegend: true,
				ifLabelsEmpty: false,
				ifDataEmpty: false,
				ifBackgroundEmpty: false,
				showTutorial: true,
				backgroundColorFieldFocused: false
			};
		},
		components: {
			'chrome-picker': Chrome
		},
		methods: {
			addLabels() {
				if(this.ifLabelsEmpty) {
					this.ifLabelsEmpty = false;
				}
				this.showTutorial=false;
				this.theChart.data.labels = this.labels = this.chartlabelString.split(',');
				this.theChart.update();
			},
			addDatasetData() {
				if(this.ifDataEmpty) {
					this.ifDataEmpty = false;
				}
				this.showTutorial=false;
				this.theChart.data.datasets[0].data = this.datasets[0].data = this.chartDatasetDataString.split(',');
				this.theChart.update();
			},
			showBackgroundColorPickerField() {
				this.backgroundColorFieldFocused = true;
			},
			hideBackgroundColorPickerField() {
				this.backgroundColorFieldFocused = false;
			},
			clickedAway() {
				this.backgroundColorFieldFocused = false;
			},
			pickBackgroundColor() {
				if(this.ifBackgroundEmpty) {
					this.ifBackgroundEmpty = false;
				}
				this.showTutorial=false;
				if(this.backgroundConcatCount > 0) {
					this.chartDatasetBgColorString = this.chartDatasetBgColorString + ',' + this.setBackgroundColor.hex;
				} else {
					this.chartDatasetBgColorString = this.setBackgroundColor.hex;
				}
				this.theChart.data.datasets[0].backgroundColor = this.datasets[0].backgroundColor = this.chartDatasetBgColorString.split(',');
				this.theChart.update();
				this.backgroundColorFieldFocused = false;
				this.backgroundConcatCount = this.datasets[0].backgroundColor.length;
			},
			addDatasetBgColor() {
				if(this.ifBackgroundEmpty) {
					this.ifBackgroundEmpty = false;
				}
				this.showTutorial=false;
				this.theChart.data.datasets[0].backgroundColor = this.datasets[0].backgroundColor = this.chartDatasetBgColorString.split(',');
				if(this.chartDatasetBgColorString === '') {
					this.datasets[0].backgroundColor.length = 0;
				}
				this.backgroundConcatCount = this.datasets[0].backgroundColor.length;
				this.theChart.update();
			},
			addTitleText() {
				this.titleText !== '' ? this.showTitle = true : this.showTitle = false;
				this.showTutorial=false;
				this.theChart.options.title.display = this.showTitle;
				this.theChart.options.title.text = this.titleText;
				this.theChart.update();
			},
			showingGraphLegend() {
				this.showTutorial=false;
				this.theChart.options.legend.display = this.showLegend;
				this.theChart.update();
			},
			changeLegendPosition() {
				this.showTutorial=false;
				this.theChart.options.legend.position = this.legendPosition;
				this.theChart.update();
			},
			saveGraphData() {
				let outerThis = this;

				if(this.chartlabelString === '') {
					this.ifLabelsEmpty = true;
				}
				if(this.chartDatasetDataString === '') {
					this.ifDataEmpty = true;
				}
				if(this.chartDatasetBgColorString === '') {
					this.ifBackgroundEmpty = true;
				}

				if(this.chartlabelString !== '' && this.chartDatasetDataString !== '' && this.chartDatasetBgColorString !== '') {
					let chartDatas = {
						type: this.chartType,
						data: {
							labels: this.labels,
							datasets: this.datasets
						},
						options: {
							maintainAspectRatio: false,
							title: {
								display: this.showTitle,
								text: this.titleText
							},
							legend: {
								display: this.showLegend,
								position: this.legendPosition
							}
						}
					};

					this.$store.dispatch('addNewGraph', chartDatas).then(function() {
						setTimeout(function() {
							outerThis.$emit("saved");
						}, 1500);
					});
				}
			},
			updateGraphData() {
				let outerThis = this;

				if(this.chartlabelString === '') {
					this.ifLabelsEmpty = true;
				}
				if(this.chartDatasetDataString === '') {
					this.ifDataEmpty = true;
				}
				if(this.chartDatasetBgColorString === '') {
					this.ifBackgroundEmpty = true;
				}

				if(this.chartlabelString !== '' && this.chartDatasetDataString !== '' && this.chartDatasetBgColorString !== '') {
					let chartDatas = {
						type: this.chartType,
						data: {
							labels: this.labels,
							datasets: [
								{
									data: this.datasets[0].data,
									backgroundColor: this.datasets[0].backgroundColor
								}
							]
						},
						options: {
							maintainAspectRatio: false,
							title: {
								display: this.showTitle,
								text: this.titleText
							},
							legend: {
								display: this.showLegend,
								position: this.legendPosition
							}
						}
					};

					let payload = {'chartDetails': chartDatas, 'graphIndex': this.graphIndex, 'graph_id': this.graphData.graph_id};

					this.$store.dispatch('updateGraph', payload).then(function() {
						setTimeout(function() {
							outerThis.$emit("updated");
						}, 2000);
					});
				}
			},
			onLoad() {
				let ctx = document.getElementById("pieChart").getContext('2d');
				this.theChart = new Chart(ctx, {
					type: this.chartType,
					data: {
						labels: [],
						datasets: [
							{
								data: [],
								backgroundColor: []
							}
						]
					},
					options: {
						maintainAspectRatio: false,
						title: {
							display: false,
							text: ''
						},
						legend: {
							display: true,
							position: 'top'
						}
					}
				});
			},
			forEdit() {
				this.showTutorial=false;
				this.chartlabelString = this.graphData.data.labels.join(", ");
				this.theChart.data.labels = this.labels = this.graphData.data.labels;

				this.chartDatasetBgColorString = this.graphData.data.datasets[0].backgroundColor.join(", ");
				this.theChart.data.datasets[0].backgroundColor = this.datasets[0].backgroundColor = this.graphData.data.datasets[0].backgroundColor;
				this.backgroundConcatCount = this.datasets[0].backgroundColor.length;

				this.chartDatasetDataString = this.graphData.data.datasets[0].data.join(", ");
				this.theChart.data.datasets[0].data = this.datasets[0].data = this.graphData.data.datasets[0].data;

				this.theChart.options.title.display = this.showTitle = this.graphData.options.title.display;
				this.theChart.options.title.text = this.titleText = this.graphData.options.title.text;

				this.theChart.options.legend.display = this.showLegend = this.graphData.options.legend.display;
				this.theChart.options.legend.position = this.legendPosition = this.graphData.options.legend.position;
				this.theChart.update();
			},
			goBacktoAllGraphPage() {
				this.$emit("backed");
			}
		},
		mounted() {
			this.onLoad();
			if(this.graphData != '') {
				this.forEdit();
			}
		}
	}
</script>

<style type="text/css" scoped="scoped">
	.gl_graphChildDiv {
		width: 40%;
		height: 60%;
		right: 5%;
	}
</style>