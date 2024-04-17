<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">Chartjs</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0);">Agroxa</a></li>
				<li class="breadcrumb-item"><a href="javascript:void(0);">Charts</a></li>
				<li class="breadcrumb-item active">Chartjs</li>
			</ol>
		</div>
	</div>
</div>
<!-- end row -->

<div class="page-content-wrapper">
	<div class="row">
		<div class="col-xl-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Line Chart</h4>

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

					<canvas id="lineChart" height="300"></canvas>

				</div>
			</div>
		</div> <!-- end col -->

		<div class="col-xl-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Bar Chart</h4>

					<ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
						<li class="list-inline-item">
							<h5 class="mb-0">2541</h5>
							<p class="text-muted">Activated</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">84845</h5>
							<p class="text-muted">Pending</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">12001</h5>
							<p class="text-muted">Deactivated</p>
						</li>
					</ul>

					<canvas id="bar" height="300"></canvas>

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->


	<div class="row">
		<div class="col-xl-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Pie Chart</h4>

					<ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
						<li class="list-inline-item">
							<h5 class="mb-0">2536</h5>
							<p class="text-muted">Activated</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">69421</h5>
							<p class="text-muted">Pending</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">89854</h5>
							<p class="text-muted">Deactivated</p>
						</li>
					</ul>

					<canvas id="pie" height="260"></canvas>

				</div>
			</div>
		</div> <!-- end col -->

		<div class="col-xl-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Donut Chart</h4>

					<ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
						<li class="list-inline-item">
							<h5 class="mb-0">9595</h5>
							<p class="text-muted">Activated</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">36524</h5>
							<p class="text-muted">Pending</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">62541</h5>
							<p class="text-muted">Deactivated</p>
						</li>
					</ul>

					<canvas id="doughnut" height="260"></canvas>

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->

	<div class="row">
		<div class="col-xl-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Polar Chart</h4>

					<ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
						<li class="list-inline-item">
							<h5 class="mb-0">4852</h5>
							<p class="text-muted">Activated</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">3652</h5>
							<p class="text-muted">Pending</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">85412</h5>
							<p class="text-muted">Deactivated</p>
						</li>
					</ul>

					<canvas id="polarArea" height="300"> </canvas>

				</div>
			</div>
		</div> <!-- end col -->

		<div class="col-xl-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Radar Chart</h4>

					<ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
						<li class="list-inline-item">
							<h5 class="mb-0">694</h5>
							<p class="text-muted">Activated</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">55210</h5>
							<p class="text-muted">Pending</p>
						</li>
						<li class="list-inline-item">
							<h5 class="mb-0">489498</h5>
							<p class="text-muted">Deactivated</p>
						</li>
					</ul>

					<canvas id="radar" height="300"></canvas>

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->	
</div>
<!-- end page content-->

<script>
	$(document).ready(function(){
		!function($) {
			"use strict";

			var ChartJs = function() {};

			ChartJs.prototype.respChart = function(selector,type,data, options) {
				// get selector by context
				var ctx = selector.get(0).getContext("2d");
				// pointing parent container to make chart js inherit its width
				var container = $(selector).parent();

				// enable resizing matter
				$(window).resize( generateChart );

				// this function produce the responsive Chart JS
				function generateChart(){
					// make chart width fit with its container
					var ww = selector.attr('width', $(container).width() );
					switch(type){
						case 'Line':
							new Chart(ctx, {type: 'line', data: data, options: options});
							break;
						case 'Doughnut':
							new Chart(ctx, {type: 'doughnut', data: data, options: options});
							break;
						case 'Pie':
							new Chart(ctx, {type: 'pie', data: data, options: options});
							break;
						case 'Bar':
							new Chart(ctx, {type: 'bar', data: data, options: options});
							break;
						case 'Radar':
							new Chart(ctx, {type: 'radar', data: data, options: options});
							break;
						case 'PolarArea':
							new Chart(ctx, {data: data, type: 'polarArea', options: options});
							break;
					}
					// Initiate new chart or Redraw

				};
				// run function - render chart at first load
				generateChart();
			},
			//init
			ChartJs.prototype.init = function() {
				//creating lineChart
				var lineChart = {
					labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September","October"],
					datasets: [
						{
							label: "Sales Analytics",
							fill: true,
							lineTension: 0.5,
							backgroundColor: "rgba(245, 178, 37, 0.2)",
							borderColor: "#f5b225",
							borderCapStyle: 'butt',
							borderDash: [],
							borderDashOffset: 0.0,
							borderJoinStyle: 'miter',
							pointBorderColor: "#f5b225",
							pointBackgroundColor: "#fff",
							pointBorderWidth: 1,
							pointHoverRadius: 5,
							pointHoverBackgroundColor: "#f5b225",
							pointHoverBorderColor: "#fff",
							pointHoverBorderWidth: 2,
							pointRadius: 1,
							pointHitRadius: 10,
							data: [65, 59, 80, 81, 56, 55, 40, 55, 30, 80]
						},
						{
							label: "Monthly Earnings",
							fill: true,
							lineTension: 0.5,
							backgroundColor: "rgba(235, 239, 242, 0.2)",
							borderColor: "#ebeff2",
							borderCapStyle: 'butt',
							borderDash: [],
							borderDashOffset: 0.0,
							borderJoinStyle: 'miter',
							pointBorderColor: "#ebeff2",
							pointBackgroundColor: "#fff",
							pointBorderWidth: 1,
							pointHoverRadius: 5,
							pointHoverBackgroundColor: "#ebeff2",
							pointHoverBorderColor: "#eef0f2",
							pointHoverBorderWidth: 2,
							pointRadius: 1,
							pointHitRadius: 10,
							data: [80, 23, 56, 65, 23, 35, 85, 25, 92, 36]
						}
					]
				};

				var lineOpts = {
					scales: {
						yAxes: [{
							ticks: {
								max: 100,
								min: 20,
								stepSize: 10
							}
						}]
					}
				};

				this.respChart($("#lineChart"),'Line',lineChart, lineOpts);

				//donut chart
				var donutChart = {
					labels: [
						"Desktops",
						"Tablets"
					],
					datasets: [
						{
							data: [300, 210],
							backgroundColor: [
								"#f5b225",
								"#ebeff2"
							],
							hoverBackgroundColor: [
								"#f5b225",
								"#ebeff2"
							],
							hoverBorderColor: "#fff"
						}]
				};
				this.respChart($("#doughnut"),'Doughnut',donutChart);


				//Pie chart
				var pieChart = {
					labels: [
						"Desktops",
						"Tablets"
					],
					datasets: [
						{
							data: [300, 180],
							backgroundColor: [
								"#1b82ec",
								"#ebeff2"
							],
							hoverBackgroundColor: [
								"#1b82ec",
								"#ebeff2"
							],
							hoverBorderColor: "#fff"
						}]
				};
				this.respChart($("#pie"),'Pie',pieChart);


				//barchart
				var barChart = {
					labels: ["January", "February", "March", "April", "May", "June", "July"],
					datasets: [
						{
							label: "Sales Analytics",
							backgroundColor: "#1b82ec",
							borderColor: "#1b82ec",
							borderWidth: 1,
							hoverBackgroundColor: "#1b82ec",
							hoverBorderColor: "#1b82ec",
							data: [65, 59, 81, 45, 56, 80, 50,20]
						}
					]
				};
				this.respChart($("#bar"),'Bar',barChart);


				//radar chart
				var radarChart = {
					labels: ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"],
					datasets: [
						{
							label: "Desktops",
							backgroundColor: "rgba(245, 178, 37, 0.2)",
							borderColor: "#f5b225",
							pointBackgroundColor: "#f5b225",
							pointBorderColor: "#fff",
							pointHoverBackgroundColor: "#fff",
							pointHoverBorderColor: "#f5b225",
							data: [65, 59, 90, 81, 56, 55, 40]
						},
						{
							label: "Tablets",
							backgroundColor: "rgba(27, 130, 236, 0.2)",
							borderColor: "#1b82ec",
							pointBackgroundColor: "#1b82ec",
							pointBorderColor: "#fff",
							pointHoverBackgroundColor: "#fff",
							pointHoverBorderColor: "#1b82ec",
							data: [28, 48, 40, 19, 96, 27, 100]
						}
					]
				};
				this.respChart($("#radar"),'Radar',radarChart);

				//Polar area  chart
				var polarChart = {
					datasets: [{
						data: [
							11,
							16,
							7,
							18
						],
						backgroundColor: [
							"#f16c69",
							"#1b82ec",
							"#ebeff2",
							"#f5b225"
						],
						label: 'My dataset', // for legend
						hoverBorderColor: "#fff"
					}],
					labels: [
						"Series 1",
						"Series 2",
						"Series 3",
						"Series 4"
					]
				};
				this.respChart($("#polarArea"),'PolarArea',polarChart);
			},
			$.ChartJs = new ChartJs, $.ChartJs.Constructor = ChartJs

		}(window.jQuery),

		//initializing
		function($) {
			"use strict";
			$.ChartJs.init()
		}(window.jQuery);
	});
</script>