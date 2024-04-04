<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">Morris Chart</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0);">Agroxa</a></li>
				<li class="breadcrumb-item"><a href="javascript:void(0);">Charts</a></li>
				<li class="breadcrumb-item active">Morris Chart</li>
			</ol>
		</div>
	</div>
</div>
<!-- end row -->

<div class="page-content-wrapper">
	<div class="row">
		<div class="col-xl-6">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">Line Chart</h4>

					<ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
						<li class="list-inline-item">
							<h5 class="mb-0">25610</h5>
							<p class="text-muted">Activated</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">56210</h5>
							<p class="text-muted">Pending</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">12485</h5>
							<p class="text-muted">Deactivated</p>
						</li>
					</ul>

					<div id="morris-line-example" class="morris-chart-height morris-charts"></div>

				</div>
			</div>
		</div> <!-- end col -->

		<div class="col-xl-6">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">Bar Chart</h4>

					<ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
						<li class="list-inline-item">
							<h5 class="mb-0">6,95,412</h5>
							<p class="text-muted">Activated</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">1,63,542</h5>
							<p class="text-muted">Pending</p>
						</li>
					</ul>

					<div id="morris-bar-example" class="morris-chart-height morris-charts"></div>

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->


	<div class="row">
		<div class="col-xl-6">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">Area Chart</h4>

					<ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
						<li class="list-inline-item">
							<h5 class="mb-0">86541</h5>
							<p class="text-muted">Activated</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">2541</h5>
							<p class="text-muted">Pending</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">102030</h5>
							<p class="text-muted">Deactivated</p>
						</li>
					</ul>

					<div id="morris-area-example" class="morris-chart-height morris-charts"></div>

				</div>
			</div>
		</div> <!-- end col -->

		<div class="col-xl-6">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">Donut Chart</h4>

					<ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
						<li class="list-inline-item">
							<h5 class="mb-0">3201</h5>
							<p class="text-muted">Activated</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">85120</h5>
							<p class="text-muted">Pending</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">65214</h5>
							<p class="text-muted">Deactivated</p>
						</li>
					</ul>

					<div id="morris-donut-example" class="morris-chart-height morris-charts"></div>

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->


	<div class="row">
		<div class="col-12">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">Area Chart</h4>

					<ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
						<li class="list-inline-item">
							<h5 class="mb-0">86541</h5>
							<p class="text-muted">Activated</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">2541</h5>
							<p class="text-muted">Pending</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">102030</h5>
							<p class="text-muted">Deactivated</p>
						</li>
					</ul>

					<div id="morris-bar-stacked" class="morris-chart-height morris-charts"></div>

				</div>
			</div>
		</div> <!-- end col -->

	</div> <!-- end row -->

</div>
<!-- end page content-->

<script>
	$(document).ready(function(){
		!function ($) {
			"use strict";

			var MorrisCharts = function () {
			};

				//creates line chart
				MorrisCharts.prototype.createLineChart = function (element, data, xkey, ykeys, labels, lineColors) {
					Morris.Line({
						element: element,
						data: data,
						xkey: xkey,
						ykeys: ykeys,
						labels: labels,
						hideHover: 'auto',
						gridLineColor: '#eef0f2',
						resize: true, //defaulted to true
						lineColors: lineColors,
						lineWidth: 2
					});
				},

				//creates Bar chart
				MorrisCharts.prototype.createBarChart = function (element, data, xkey, ykeys, labels, lineColors) {
					Morris.Bar({
						element: element,
						data: data,
						xkey: xkey,
						ykeys: ykeys,
						labels: labels,
						gridLineColor: '#eef0f2',
						barSizeRatio: 0.4,
						resize: true,
						hideHover: 'auto',
						barColors: lineColors
					});
				},

				//creates area chart
				MorrisCharts.prototype.createAreaChart = function (element, pointSize, lineWidth, data, xkey, ykeys, labels, lineColors) {
					Morris.Area({
						element: element,
						pointSize: 0,
						lineWidth: 0,
						data: data,
						xkey: xkey,
						ykeys: ykeys,
						labels: labels,
						resize: true,
						gridLineColor: '#eee',
						hideHover: 'auto',
						lineColors: lineColors,
						fillOpacity: .6,
						behaveLikeLine: true
					});
				},

				//creates Donut chart
				MorrisCharts.prototype.createDonutChart = function (element, data, colors) {
					Morris.Donut({
						element: element,
						data: data,
						resize: true,
						colors: colors
					});
				},
				//creates Stacked chart
				MorrisCharts.prototype.createStackedChart = function (element, data, xkey, ykeys, labels, lineColors) {
					Morris.Bar({
						element: element,
						data: data,
						xkey: xkey,
						ykeys: ykeys,
						stacked: true,
						labels: labels,
						hideHover: 'auto',
						barSizeRatio: 0.4,
						resize: true, //defaulted to true
						gridLineColor: '#eeeeee',
						barColors: lineColors
					});
				},
				MorrisCharts.prototype.init = function () {

					//create line chart
					var $data = [
						{y: '2009', a: 50, b: 80, c: 20},
						{y: '2010', a: 130, b: 100, c: 80},
						{y: '2011', a: 80, b: 60, c: 70},
						{y: '2012', a: 70, b: 200, c: 140},
						{y: '2013', a: 180, b: 140, c: 150},
						{y: '2014', a: 105, b: 100, c: 80},
						{y: '2015', a: 250, b: 150, c: 200}
					];
					this.createLineChart('morris-line-example', $data, 'y', ['a', 'b', 'c'], ['Activated', 'Pending', 'Deactivated'], ['#ccc', '#1b82ec', '#f5b225']);

					//creating bar chart
					var $barData = [
						{y: '2009', a: 100, b: 90},
						{y: '2010', a: 75, b: 65},
						{y: '2011', a: 50, b: 40},
						{y: '2012', a: 75, b: 65},
						{y: '2013', a: 50, b: 40},
						{y: '2014', a: 75, b: 65},
						{y: '2015', a: 100, b: 90},
						{y: '2016', a: 90, b: 75}
					];
					this.createBarChart('morris-bar-example', $barData, 'y', ['a', 'b'], ['Series A', 'Series B'], ['#1b82ec','#f5b225']);

					//creating area chart
					var $areaData = [
						{y: '2007', a: 0, b: 0, c:0},
						{y: '2008', a: 150, b: 45, c:15},
						{y: '2009', a: 60, b: 150, c:195},
						{y: '2010', a: 180, b: 36, c:21},
						{y: '2011', a: 90, b: 60, c:360},
						{y: '2012', a: 75, b: 240, c:120},
						{y: '2013', a: 30, b: 30, c:30}
					];
					this.createAreaChart('morris-area-example', 0, 0, $areaData, 'y', ['a', 'b', 'c'], ['Series A', 'Series B', 'Series C'], ['#ccc', '#f5b225', '#1b82ec']);

					//creating donut chart
					var $donutData = [
						{label: "Download Sales", value: 12},
						{label: "In-Store Sales", value: 30},
						{label: "Mail-Order Sales", value: 20}
					];
					this.createDonutChart('morris-donut-example', $donutData, ['#f0f1f4', '#1b82ec', '#f5b225']);

					//creating Stacked chart
					var $stckedData = [
						{y: '2005', a: 45, b: 180},
						{y: '2006', a: 75, b: 65},
						{y: '2007', a: 100, b: 90},
						{y: '2008', a: 75, b: 65},
						{y: '2009', a: 100, b: 90},
						{y: '2010', a: 75, b: 65},
						{y: '2011', a: 50, b: 40},
						{y: '2012', a: 75, b: 65},
						{y: '2013', a: 50, b: 40},
						{y: '2014', a: 75, b: 65},
						{y: '2015', a: 100, b: 90},
						{y: '2016', a: 80, b: 65}
					];
					this.createStackedChart('morris-bar-stacked', $stckedData, 'y', ['a', 'b'], ['Series A', 'Series B'], ['#1b82ec', '#f0f1f4']);

				},
				//init
				$.MorrisCharts = new MorrisCharts, $.MorrisCharts.Constructor = MorrisCharts
		}(window.jQuery),

		//initializing 
			function ($) {
				"use strict";
				$.MorrisCharts.init();
			}(window.jQuery);
	});
</script>