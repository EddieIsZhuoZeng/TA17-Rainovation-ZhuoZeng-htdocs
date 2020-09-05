<template>
	<div class="gl_chart_template" id="scatter">
		<div class="gl_graphOptions">
			<table class="form-table">
				<tr>
					<th scope="row" class="gl_backButotnTh"><button class="gl_backButton" type="button" @click="goBacktoAllGraphPage">Go Back</button></th>
					<td></td>
				</tr>
			</table>

			<fieldset v-for="(dataset, index) in datasets" :key="dataset">
				<legend>Dataset {{index+1}}</legend>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="label">Label</label></th>
						<td><input class="regular-text" type="text" id="label" placeholder="Dataset label" v-model="dataset.label" @keyup="addDatasetLabel(index)"></td>
					</tr>
					<tr>
						<th scope="row"><label for="datasets">Data*</label></th>
							<td>
								<template v-for="(data, PIndex) in dataset.data">
									<div class="gl_bb_point">
										<div class="gl_bubble_input_fields">
											<div class="gl_bb_xp">
												<label for="xPoint">x-point</label>
												<input :class="{'gl_fieldRequired': data.ifxPointEmpty}" type="number" id="xPoint" v-model="dataset.data[PIndex].x" @keyup="addDatasetDataPoints(index, PIndex, 'x')" @mouseup="addDatasetDataPoints(index, PIndex, 'x')">
											</div>
											<div class="gl_bb_yp">
												<label for="yPoint">y-point</label>
												<input :class="{'gl_fieldRequired': data.ifyPointEmpty}" type="number" id="yPoint" v-model="dataset.data[PIndex].y" @keyup="addDatasetDataPoints(index, PIndex, 'y')" @mouseup="addDatasetDataPoints(index, PIndex, 'y')">
											</div>
											<p class="gl_fieldRequiredError" v-if="data.ifxPointEmpty || data.ifyPointEmpty">*required</p>
										</div>
										<div v-if="PIndex != 0">
											<a href="javascript:void(0)" class="gl_deleteBublePoint" @click="deleteButtonPoint(index, PIndex)">X</a>
										</div>
									</div>
								</template>
								<button type="button" @click="addBubblePoint(index)">Add Bubble Point</button>
							</td>
					</tr>
					<tr>
						<th scope="row" style="padding-top: 5px; padding-bottom: 5px"><label for="colors">Circle Background Color*</label></th>
						<td class="gl_colorPickerTd" v-on-clickaway="() => clickedAwayFromBg(index)">
							<input class="regular-text" :class="{'gl_fieldRequired': dataset.ifCircleBackgroundEmpty}" type="text" id="colors" v-model="dataset.backgroundColor" @keyup="addDatasetBgColor(index)" @focus="showBackgroundColorPickerField(index)">
							<div class="gl_colorPickerDiv">
								<chrome-picker v-model="setBackgroundColor" v-if="dataset.backgroundColorFieldFocused" />
								<div class="gl_pickOrCloseColorPickerDiv">
									<button class="gl_colorPickerButton" type="button" @click="pickBackgroundColor(index)" v-if="dataset.backgroundColorFieldFocused">Pick</button>
									<button class="gl_colorPickerButton" type="button" @click="hideBackgroundColorPickerField(index)" v-if="dataset.backgroundColorFieldFocused">Close</button>
									<div style="clear: both;"></div>
								</div>
							</div>
							<p class="gl_fieldRequiredError" v-if="dataset.ifCircleBackgroundEmpty">*required</p>
						</td>
					</tr>
					<tr>
						<th scope="row" style="padding-top: 5px; padding-bottom: 5px"><label for="line_color">Circle Border Color*</label></th>
						<td class="gl_colorPickerTd" v-on-clickaway="() => clickedAwayFromBd(index)">
							<input class="regular-text" :class="{'gl_fieldRequired': dataset.ifCicleBorderColorEmpty}" type="text" id="line_color" v-model="dataset.borderColor" @keyup="addDatasetborderColor(index)" @focus="showBorderColorPickerField(index)">
							<div class="gl_colorPickerDiv">
								<chrome-picker v-model="setBorderColor" v-if="dataset.borderColorFieldFocused" />
								<div class="gl_pickOrCloseColorPickerDiv">
									<button class="gl_colorPickerButton" type="button" @click="pickBorderColor(index)" v-if="dataset.borderColorFieldFocused">Pick</button>
									<button class="gl_colorPickerButton" type="button" @click="hideBorderColorPickerField(index)" v-if="dataset.borderColorFieldFocused">Close</button>
									<div style="clear: both;"></div>
								</div>
							</div>
							<p class="gl_fieldRequiredError" v-if="dataset.ifCicleBorderColorEmpty">*required</p>
						</td>
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
			<!-- <iframe class="gl_tutorialFrame" v-if="showTutorial" width="560" height="315" src="https://www.youtube.com/embed/Hwn4UKc5Bew?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen style="margin-top: 67px !important"></iframe> -->
			<div class="gl_dummyMessages" v-if="showTutorial">
				<h2>Start typing to see live preview</h2>
				<p>Live preview will appear here after you enter some data</p>
			</div>
			<div class="gl_graphChildDiv" v-show="!showTutorial">
				<canvas id="scatterChart"></canvas>
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
				chartType: 'scatter',
				titleText: '',
				legendPosition: 'top',
				setBackgroundColor: '',
				setBorderColor: '',
				showTitle: false,
				showLegend: true,
				showTutorial: true,
				datasets: [
					{
						label: '',
						data: [
							{
								x: '',
								y: '',
								ifxPointEmpty: false,
								ifyPointEmpty: false
							}
						],
						backgroundColor: '',
						borderColor: '',
						fill: false,
						showLine: false,
						ifCircleBackgroundEmpty: false,
						ifCicleBorderColorEmpty: false,
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
					data: [
						{
							x: '',
							y: '',
							fxPointEmpty: false,
							ifyPointEmpty: false
						}
					],
					backgroundColor: '',
					borderColor: '',
					fill: false,
					showLine: false,
					ifCircleBackgroundEmpty: false,
					ifCicleBorderColorEmpty: false,
					backgroundColorFieldFocused: false,
					borderColorFieldFocused: false
				});
				this.theChart.data.datasets.push({
					label: '',
					data: [
						{x: '', y: ''}
					],
					backgroundColor: '',
					borderColor: '',
					fill: false,
					showLine: false
				});
				this.theChart.update();
			},
			addBubblePoint(index) {
				this.showTutorial=false;
				this.datasets[index].data.push({ x: '', y: '', ifxPointEmpty: false, ifyPointEmpty: false });
				this.theChart.data.datasets[index].data.push({x: '', y: ''});
				this.theChart.update();
			},
			addDatasetLabel(index) {
				this.showTutorial=false;
				this.theChart.data.datasets[index].label = this.datasets[index].label;
				this.theChart.update();
			},
			addDatasetDataPoints(index, pIndex, point) {
				let gettingErrorPoint = 'if'+point+'PointEmpty';
				if(this.datasets[index].data[pIndex][gettingErrorPoint]) {
					this.datasets[index].data[pIndex][gettingErrorPoint] = false;
				}
				this.showTutorial=false;
				this.theChart.data.datasets[index].data[pIndex][point] = this.datasets[index].data[pIndex][point];
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
				if(this.datasets[index].ifCircleBackgroundEmpty) {
					this.datasets[index].ifCircleBackgroundEmpty = false;
				}
				this.showTutorial=false;
				this.theChart.data.datasets[index].backgroundColor = this.datasets[index].backgroundColor = this.setBackgroundColor.hex;
				this.theChart.update();
				this.datasets[index].backgroundColorFieldFocused = false;
			},
			addDatasetBgColor(index) {
				if(this.datasets[index].ifCircleBackgroundEmpty) {
					this.datasets[index].ifCircleBackgroundEmpty = false;
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
				if(this.datasets[index].ifCicleBorderColorEmpty) {
					this.datasets[index].ifCicleBorderColorEmpty = false;
				}
				this.showTutorial=false;
				this.theChart.data.datasets[index].borderColor = this.datasets[index].borderColor = this.setBorderColor.hex;
				this.theChart.update();
				this.datasets[index].borderColorFieldFocused = false;
			},
			addDatasetborderColor(index) {
				if(this.datasets[index].ifCicleBorderColorEmpty) {
					this.datasets[index].ifCicleBorderColorEmpty = false;
				}
				this.showTutorial=false;
				this.theChart.data.datasets[index].borderColor = this.datasets[index].borderColor;
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
			deleteButtonPoint(datasetIndex, bubblePointIndex) {
				this.datasets[datasetIndex].data.splice(bubblePointIndex, 1);
				this.theChart.data.datasets[datasetIndex].data.splice(bubblePointIndex, 1);
				this.theChart.update();
			},
			saveGraphData() {
				let outerThis = this;
				let DatasetHasEmptyValue = true

				this.datasets.forEach(function(value) {
					if(value.backgroundColor === '') {
						value.ifCircleBackgroundEmpty = true;
						DatasetHasEmptyValue = false;
					}
					if(value.borderColor === '') {
						value.ifCicleBorderColorEmpty = true;
						DatasetHasEmptyValue = false;
					}

					value.data.forEach(function(data) {
						if(data.x === '') {
							data.ifxPointEmpty = true;
							DatasetHasEmptyValue = false;
						}
						if(data.y === '') {
							data.ifyPointEmpty = true;
							DatasetHasEmptyValue = false;
						}
					})
				});

				if(DatasetHasEmptyValue) {
					let chartDatas = {
						type: this.chartType,
						data: {
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
				let DatasetHasEmptyValue = true

				this.datasets.forEach(function(value) {
					if(value.backgroundColor === '') {
						value.ifCircleBackgroundEmpty = true;
						DatasetHasEmptyValue = false;
					}
					if(value.borderColor === '') {
						value.ifCicleBorderColorEmpty = true;
						DatasetHasEmptyValue = false;
					}

					value.data.forEach(function(data) {
						if(data.x === '') {
							data.ifxPointEmpty = true;
							DatasetHasEmptyValue = false;
						}
						if(data.y === '') {
							data.ifyPointEmpty = true;
							DatasetHasEmptyValue = false;
						}
					})
				});

				if(DatasetHasEmptyValue) {
					let chartDatas = {
						type: this.chartType,
						data: {
							datasets: []
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

					this.datasets.forEach(function(value, key) {
						chartDatas.data.datasets.push({
							label: value.label,
							data: value.data,
							backgroundColor: value.backgroundColor,
							borderColor: value.borderColor,
							fill: value.fill,
							showLine: value.showLine
						});
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
				let ctx = document.getElementById("scatterChart").getContext('2d');
				this.theChart = new Chart(ctx, {
					type: this.chartType,
					data: {
						datasets: [
							{
								label: '',
								data: [
									{x: '', y: ''}
								],
								backgroundColor: '',
								borderColor: '',
								fill: false,
								showLine: false
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
				let outerThis = this;
				this.graphData.data.datasets.forEach(function(value, key) {
					if(key) {
						outerThis.datasets.push({ label: '', data: [{ x: '', y: '', ifxPointEmpty: false, ifyPointEmpty: false }], backgroundColor:'', fill: false, showLine: false, ifCircleBackgroundEmpty: false, ifCicleBorderColorEmpty: false, backgroundColorFieldFocused: false, borderColorFieldFocused: false });
						outerThis.theChart.data.datasets.push({ label: '', data: [{ x: '', y: '' }], backgroundColor:'', fill: false, showLine: false });
					}
					outerThis.theChart.data.datasets[key].label = outerThis.datasets[key].label = value.label;
					value.data.forEach(function(innerValue, innerKey) {
						if(innerKey) {
							outerThis.datasets[key].data.push({ x: '', y: '' });
							outerThis.theChart.data.datasets[key].data.push({ x: '', y: '' });
						}
						outerThis.theChart.data.datasets[key].data[innerKey].x = outerThis.datasets[key].data[innerKey].x = innerValue.x;
						outerThis.theChart.data.datasets[key].data[innerKey].y = outerThis.datasets[key].data[innerKey].y = innerValue.y;
					});
					outerThis.theChart.data.datasets[key].backgroundColor = outerThis.datasets[key].backgroundColor = value.backgroundColor;
					outerThis.theChart.data.datasets[key].borderColor = outerThis.datasets[key].borderColor = value.borderColor;
				});

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
</style>