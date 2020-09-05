<template>
	<div class="gl_chart_template" id="line">
		<div class="gl_graphOptions">
			<table class="form-table">
				<tr>
					<th scope="row" class="gl_backButotnTh"><button class="gl_backButton" type="button" @click="goBacktoAllGraphPage">Go Back</button></th>
					<td></td>
				</tr>
				<tr>
					<th scope="row"><label for="labels">Labels*</label></th>
					<td>
						<input class="regular-text" :class="{'gl_fieldRequired': ifLabelsEmpty}" type="text" id="labels" placeholder="Comma separated list of labels" v-model="chartlabelsString" @keyup="addLabels">
						<p class="gl_fieldRequiredError" v-if="ifLabelsEmpty">*required</p>
					</td>
				</tr>
			</table>

			<fieldset v-for="(data, index) in datasets" :key="data">
				<legend>Dataset {{index+1}}</legend>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="label">Label</label></th>
						<td><input class="regular-text" type="text" id="label" placeholder="Dataset label" v-model="data.label" @keyup="addDatasetLabel(index)"></td>
					</tr>
					<tr>
						<th scope="row"><label for="datasets">Data*</label></th>
						<td>
							<input class="regular-text" :class="{'gl_fieldRequired': data.ifDataEmpty}" type="text" id="datasets" placeholder="Numeric data value for each label. Eg. 1,2,3 etc" v-model="data.chartDatasetDataString" @keyup="addDatasetData(index)">
							<p class="gl_fieldRequiredError" v-if="data.ifDataEmpty">*required</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="colors">Fill Color*</label></th>
						<td class="gl_colorPickerTd" v-on-clickaway="() => clickedAwayFromBg(index)">
							<input class="regular-text" :class="{'gl_fieldRequired': data.ifFillColorEmpty}" type="text" id="colors" v-model="data.backgroundColor" @keyup="addDatasetBgColor(index)" @focus="showBackgroundColorPickerField(index)">
							<div class="gl_colorPickerDiv">
								<chrome-picker v-model="setBackgroundColor" v-if="data.backgroundColorFieldFocused" />
								<div class="gl_pickOrCloseColorPickerDiv">
									<button class="gl_colorPickerButton" type="button" @click="pickBackgroundColor(index)" v-if="data.backgroundColorFieldFocused">Pick</button>
									<button class="gl_colorPickerButton" type="button" @click="hideBackgroundColorPickerField(index)" v-if="data.backgroundColorFieldFocused">Close</button>
									<div style="clear: both;"></div>
								</div>
							</div>
							<p class="gl_fieldRequiredError" v-if="data.ifFillColorEmpty">*required</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="line_color">Line Color*</label></th>
						<td class="gl_colorPickerTd" v-on-clickaway="() => clickedAwayFromBd(index)">
							<input class="regular-text" :class="{'gl_fieldRequired': data.ifLineColorEmpty}" type="text" id="line_color" v-model="data.borderColor" @keyup="addDatasetborderColor(index)" @focus="showBorderColorPickerField(index)">
							<div class="gl_colorPickerDiv">
								<chrome-picker v-model="setBorderColor" v-if="data.borderColorFieldFocused" />
								<div class="gl_pickOrCloseColorPickerDiv">
									<button class="gl_colorPickerButton" type="button" @click="pickBorderColor(index)" v-if="data.borderColorFieldFocused">Pick</button>
									<button class="gl_colorPickerButton" type="button" @click="hideBorderColorPickerField(index)" v-if="data.borderColorFieldFocused">Close</button>
									<div style="clear: both;"></div>
								</div>
							</div>
							<p class="gl_fieldRequiredError" v-if="data.ifLineColorEmpty">*required</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="fill">Fill Color Under the line</label></th>
						<td><input type="checkbox" id="fill" v-model="data.fill" @change="fillColor(index)"></td>
					</tr>
					<tr v-if="index != 0">
						<th scope="row" class="gl_deleteButtonTh"><label></label></th>
						<td class="gl_deleteButtonTd"><input type="button" class="button button-danger gl_delete_dataset" value="Delete Dataset" @click="deleteDataset(index)"></td>
					</tr>
				</table>
			</fieldset>

			<table class="form-table">
				<tr>
					<th scope="row" style="padding-top: 5px;"><input type="button" id="add_dataset" class="button button-primary" value="Add Dataset" @click="addDataset"></th>
					<td></td>
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
				<canvas id="radarChart"></canvas>
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
				chartType: 'radar',
				chartlabelsString: '',
				titleText: '',
				legendPosition: 'top',
				setBackgroundColor: '',
				setBorderColor: '',
				labels: [],
				showTitle: false,
				showLegend: true,
				ifLabelsEmpty: false,
				showTutorial: true,
				datasets: [
					{
						label: '',
						chartDatasetDataString: '',
						data: [],
						backgroundColor: '',
						borderColor: '',
						fill: false,
						ifDataEmpty: false,
						ifFillColorEmpty: false,
						ifLineColorEmpty: false,
						backgroundColorFieldFocused: false,
						borderColorFieldFocused: false
					}
				]
			};
		},
		components: {
			'chrome-picker': Chrome
		},
		methods: {
			addDataset() {
				this.datasets.push({
					label: '',
					chartDatasetDataString: '',
					data: [],
					backgroundColor: '',
					borderColor: '',
					fill: false,
					ifDataEmpty: false,
					ifFillColorEmpty: false,
					ifLineColorEmpty: false,
					backgroundColorFieldFocused: false,
					borderColorFieldFocused: false
				});
				this.theChart.data.datasets.push({
					label: '',
					data: [],
					backgroundColor: '',
					borderColor: '',
					fill: false
				});
				this.theChart.update();
			},
			addLabels() {
				if(this.ifLabelsEmpty) {
					this.ifLabelsEmpty = false;
				}
				this.showTutorial=false;
				this.labels = this.chartlabelsString.split(',');
				this.theChart.data.labels = this.labels;
				this.theChart.update();
			},
			addDatasetLabel(index) {
				this.showTutorial=false;
				this.theChart.data.datasets[index].label = this.datasets[index].label;
				this.theChart.update();
			},
			addDatasetData(index) {
				if(this.datasets[index].ifDataEmpty) {
					this.datasets[index].ifDataEmpty = false;
				}
				this.showTutorial=false;
				this.datasets[index].data = this.datasets[index].chartDatasetDataString.split(',');
				this.theChart.data.datasets[index].data = this.datasets[index].data;
				this.theChart.update();
			},
			showBackgroundColorPickerField(index) {
				this.datasets[index].backgroundColorFieldFocused = true;
			},
			hideBackgroundColorPickerField(index) {
				this.datasets[index].backgroundColorFieldFocused = false;
			},
			clickedAwayFromBg(index) {
				this.datasets[index].backgroundColorFieldFocused = false;
			},
			pickBackgroundColor(index) {
				if(this.datasets[index].ifFillColorEmpty) {
					this.datasets[index].ifFillColorEmpty = false;
				}
				this.showTutorial=false;
				this.theChart.data.datasets[index].backgroundColor = this.datasets[index].backgroundColor = this.setBackgroundColor.hex;
				this.theChart.update();
				this.datasets[index].backgroundColorFieldFocused = false;
			},
			addDatasetBgColor(index) {
				if(this.datasets[index].ifFillColorEmpty) {
					this.datasets[index].ifFillColorEmpty = false;
				}
				this.showTutorial=false;
				this.theChart.data.datasets[index].backgroundColor = this.datasets[index].backgroundColor;
				this.theChart.update();
			},
			showBorderColorPickerField(index) {
				this.datasets[index].borderColorFieldFocused = true;
			},
			hideBorderColorPickerField(index) {
				this.datasets[index].borderColorFieldFocused = false;
			},
			clickedAwayFromBd(index) {
				this.datasets[index].borderColorFieldFocused = false;
			},
			pickBorderColor(index) {
				if(this.datasets[index].ifLineColorEmpty) {
					this.datasets[index].ifLineColorEmpty = false;
				}
				this.showTutorial=false;
				this.theChart.data.datasets[index].borderColor = this.datasets[index].borderColor = this.setBorderColor.hex;
				this.theChart.update();
				this.datasets[index].borderColorFieldFocused = false;
			},
			addDatasetborderColor(index) {
				if(this.datasets[index].ifLineColorEmpty) {
					this.datasets[index].ifLineColorEmpty = false;
				}
				this.showTutorial=false;
				this.theChart.data.datasets[index].borderColor = this.datasets[index].borderColor;
				this.theChart.update();
			},
			fillColor(index) {
				this.showTutorial=false;
				this.theChart.data.datasets[index].fill = this.datasets[index].fill;
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
			deleteDataset(index) {
				this.datasets.splice(index, 1);
				this.theChart.data.datasets.splice(index, 1);
				this.theChart.update();
			},
			saveGraphData() {
				let outerThis = this;
				let DatasetHasEmptyValue = true

				this.datasets.forEach(function(value) {
					if(value.chartDatasetDataString === '') {
						value.ifDataEmpty = true;
						DatasetHasEmptyValue = false;
					}
					if(value.backgroundColor === '') {
						value.ifFillColorEmpty = true;
						DatasetHasEmptyValue = false;
					}
					if(value.borderColor === '') {
						value.ifLineColorEmpty = true;
						DatasetHasEmptyValue = false;
					}
				});

				if(this.chartlabelsString === '') {
					this.ifLabelsEmpty = true;
				}

				if(this.chartlabelsString !== '' && DatasetHasEmptyValue) {
					let chartDatas = {
						type: this.chartType,
						data: {
							labels: this.labels,
							datasets: this.datasets
						},
						options: {
							maintainAspectRatio: false,
							scale: {
								ticks: {
									beginAtZero: true
								}
							},
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
				let DatasetHasEmptyValue = true

				this.datasets.forEach(function(value) {
					if(value.chartDatasetDataString === '') {
						value.ifDataEmpty = true;
						DatasetHasEmptyValue = false;
					}
					if(value.backgroundColor === '') {
						value.ifFillColorEmpty = true;
						DatasetHasEmptyValue = false;
					}
					if(value.borderColor === '') {
						value.ifLineColorEmpty = true;
						DatasetHasEmptyValue = false;
					}
				});

				if(this.chartlabelsString === '') {
					this.ifLabelsEmpty = true;
				}

				if(this.chartlabelsString !== '' && DatasetHasEmptyValue) {
					let chartDatas = {
						type: this.chartType,
						data: {
							labels: this.labels,
							datasets: []
						},
						options: {
							maintainAspectRatio: false,
							scale: {
								ticks: {
									beginAtZero: true
								}
							},
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

					this.datasets.forEach(function(value) {
						chartDatas.data.datasets.push({ label: value.label, data: value.data, chartDatasetDataString: value.chartDatasetDataString, backgroundColor: value.backgroundColor, borderColor: value.borderColor, fill: value.fill });
					});

					let payload = {'chartDetails': chartDatas, 'graphIndex': this.graphIndex, 'graph_id': this.graphData.graph_id};

					this.$store.dispatch('updateGraph', payload).then(function() {
						setTimeout(function() {
							outerThis.$emit("updated");
						}, 2000);
					});
				}
			},
			onLoad() {
				let ctx = document.getElementById("radarChart").getContext('2d');
				this.theChart = new Chart(ctx, {
					type: this.chartType,
					data: {
						labels: [],
						datasets: [
							{
								label: '',
								data: [],
								backgroundColor: '',
								borderColor: '',
								fill: false
							}
						]
					},
					options: {
						maintainAspectRatio: false,
						scale: {
							ticks: {
								beginAtZero: true
							}
						},
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
				let outerThis = this;
				this.chartlabelsString = this.graphData.data.labels.join(", ");
				this.theChart.data.labels = this.labels = this.graphData.data.labels;
				this.graphData.data.datasets.forEach(function(value, key) {
					if(key) {
						outerThis.datasets.push({ label: '', chartDatasetDataString: '', data: [], backgroundColor:'', ifDataEmpty: false, ifFillColorEmpty: false, ifLineColorEmpty: false, backgroundColorFieldFocused: false, borderColorFieldFocused: false });
						outerThis.theChart.data.datasets.push({ label: '', data: [], backgroundColor:'' });
					}
					outerThis.theChart.data.datasets[key].label = outerThis.datasets[key].label = outerThis.graphData.data.datasets[key].label;

					outerThis.datasets[key].chartDatasetDataString = outerThis.graphData.data.datasets[key].chartDatasetDataString;

					outerThis.theChart.data.datasets[key].data = outerThis.datasets[key].data = outerThis.graphData.data.datasets[key].data;

					outerThis.theChart.data.datasets[key].backgroundColor = outerThis.datasets[key].backgroundColor = outerThis.graphData.data.datasets[key].backgroundColor;

					outerThis.theChart.data.datasets[key].borderColor = outerThis.datasets[key].borderColor = outerThis.graphData.data.datasets[key].borderColor;

					outerThis.theChart.data.datasets[key].fill = outerThis.datasets[key].fill = outerThis.graphData.data.datasets[key].fill;
				});

				this.theChart.options.title.display = this.showTitle = this.graphData.options.title.display;
				this.theChart.options.title.text = this.titleText = this.graphData.options.title.text;
				this.theChart.options.legend.display = this.showLegend = this.graphData.options.legend.display;
				this.theChart.options.legend.position = this.legendPosition = this.graphData.options.legend.position;
				this.theChart.options.scale.ticks.beginAtZero = this.beginAtZero = this.graphData.options.scale.ticks.beginAtZero;
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
		height: 65%;
	}
</style>