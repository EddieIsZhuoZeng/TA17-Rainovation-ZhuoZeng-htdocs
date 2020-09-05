<template>
    <div class="mainAppDiv">
        <div class="gl_heading_area">
            <div class="gl_heading">
                <p>{{currentPageName}}</p>
                <button type="button" @click="resetComponent" class="media-modal-close close_graph_modal">
                    <span class="media-modal-icon">
                        <span class="screen-reader-text">Close media panel</span>
                    </span>
                </button>
            </div>
        </div>

        <component class="gl_graphComponentDiv" @graphPage="whenPageChange" @saved="whenGraphSaved" @updated="whenGraphUpdated" @backed="whenBackButtonPressed" v-bind:is="currentChartTabComponent" :graph-data="editedGraphData" :graph-index="editedGraphIndex"></component>
    </div>
</template>

<script type="text/javascript">
    import allGraphs from './components/allSavedChartsTemplate';
    import barChart from './components/BarChartTemplate';
    import lineChart from './components/LineChartTemplate';
    import pieChart from './components/PieChartTemplate';
    import doughnutChart from './components/DoughnutChartTemplate';
    import radarChart from './components/RadarChartTemplate';
    import polarAreaChart from './components/PolarAreaChartTemplate';
    import bubbleChart from './components/BubbleChartTemplate';
    import scatterChart from './components/ScatterChartTemplate';

    export default {
        data() {
            return {
                currentComponent: 'allGraphs',
                editedGraphData: [],
                editedGraphIndex: '',
                currentPageName: 'All Graphs'
            }
        },
        computed: {
            currentChartTabComponent() {
                return this.currentComponent;
            },
        },
        components: {
            allGraphs, pieChart, doughnutChart, polarAreaChart, barChart, lineChart, radarChart, bubbleChart, scatterChart
        },
        methods: {
            whenPageChange(data) {
                this.editedGraphIndex = data.graphIndex;
                this.editedGraphData = data.graphData;
                this.currentPageName = data.pageName;
                this.currentComponent = data.currentComponent;
            },
            whenGraphSaved() {
                this.currentPageName = 'All Graphs';
                this.currentComponent = 'allGraphs';
            },
            whenGraphUpdated() {
                this.currentPageName = 'All Graphs';
                this.currentComponent = 'allGraphs';
            },
            whenBackButtonPressed() {
                this.currentPageName = 'All Graphs';
                this.currentComponent = 'allGraphs';
            },
            resetComponent() {
                let outerThis = this;
                this.currentPageName = 'All Graphs';
                setTimeout(function() {
                    outerThis.currentComponent = 'allGraphs';
                }, 500);
            }
        }
    }
</script>

<style type="text/css" scoped="scoped">
    .gl_heading p {
        display: inline-block;
    }
    .gl_heading div {
        text-align: right;
        font-weight: bold;
        font-size: 18px;
        display: inline-block;
        float: right;
        margin-top: 13px;
    }
    .gl_heading div a {
        text-decoration: none;
        color: #000;
        box-shadow: none;
    }
</style>