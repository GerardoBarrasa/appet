<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">C3 Chart</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0);">Agroxa</a></li>
				<li class="breadcrumb-item"><a href="javascript:void(0);">Charts</a></li>
				<li class="breadcrumb-item active">C3 Chart</li>
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

					<h4 class="mt-0 header-title">Bar Chart</h4>

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

					<div id="chart"></div>
				</div>
			</div>
		</div> <!-- end col -->

		<div class="col-xl-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Stacked Area Chart</h4>

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

					<div id="chart-stacked"></div>

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->


	<div class="row">
		<div class="col-xl-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Roated Chart</h4>

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

					<div id="roated-chart"></div>
				</div>
			</div>
		</div> <!-- end col -->

		<div class="col-xl-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Combine Chart</h4>

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

					<div id="combine-chart"></div>

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->


	<div class="row">
		<div class="col-xl-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Donut Chart</h4>

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

					<div id="donut-chart"></div>

				</div>
			</div>
		</div> <!-- end col -->

		<div class="col-xl-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Pie Chart</h4>

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

					<div id="pie-chart"></div>

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

			var ChartC3 = function() {};

			ChartC3.prototype.init = function () {
				//generating chart 
				c3.generate({
					bindto: '#chart',
					data: {
						columns: [
							['Desktop', 150, 80, 70, 152, 250, 95],
							['Mobile', 200, 130, 90, 240, 130, 220],
							['Tablet', 300, 200, 160, 400, 250, 250]
						],
						type: 'bar',
						colors: {
							Desktop: '#f0f1f4',
							Mobile: '#f5b225',
							Tablet: '#1b82ec'
						}
					}
				});

				//combined chart
				c3.generate({
					bindto: '#combine-chart',
					data: {
						columns: [
							['SonyVaio', 30, 20, 50, 40, 60, 50],
							['iMacs', 200, 130, 90, 240, 130, 220],
							['Tablets', 300, 200, 160, 400, 250, 250],
							['iPhones', 200, 130, 90, 240, 130, 220],
							['Macbooks', 130, 120, 150, 140, 160, 150]
						],
						types: {
							SonyVaio: 'bar',
							iMacs: 'bar',
							Tablets: 'spline',
							iPhones: 'line',
							Macbooks: 'bar'
						},
						colors: {
							SonyVaio: '#f0f1f4',
							iMacs: '#1b82ec',
							Tablets: '#35a989',
							iPhones: '#f16c69',
							Macbooks: '#f5b225'
						},
						groups: [
							['SonyVaio','iMacs']
						]
					},
					axis: {
						x: {
							type: 'categorized'
						}
					}
				});
				
				//roated chart
				c3.generate({
					bindto: '#roated-chart',
					data: {
						columns: [
						['Revenue', 30, 200, 100, 400, 150, 250],
						['Pageview', 50, 20, 10, 40, 15, 25]
						],
						types: {
							Revenue: 'bar'
						},
						colors: {
							Revenue: '#f0f1f4',
							Pageview: '#1b82ec'
						}
					},
					axis: {
						rotated: true,
						x: {
						type: 'categorized'
						}
					}
				});

				//stacked chart
				c3.generate({
					bindto: '#chart-stacked',
					data: {
						columns: [
							['Revenue', 130, 120, 150, 140, 160, 150, 130, 120, 150, 140, 160, 150],
							['Pageview', 200, 130, 90, 240, 130, 220, 200, 130, 90, 240, 130, 220]
						],
						types: {
							Revenue: 'area-spline',
							Pageview: 'area-spline'
							// 'line', 'spline', 'step', 'area', 'area-step' are also available to stack
						},
						colors: {
							Revenue: '#f0f1f4',
							Pageview: '#f5b225'
						}
					}
				});
				
				//Donut Chart
				c3.generate({
					 bindto: '#donut-chart',
					data: {
						columns: [
							['Desktops', 78],
							['Smart Phones', 55],
							['Mobiles', 40],
							['Tablets', 25]
						],
						type : 'donut'
					},
					donut: {
						title: "Candidates",
						width: 30,
						label: { 
							show:false
						}
					},
					color: {
						pattern: ['#f0f1f4', '#1b82ec', '#f16c69', '#f5b225']
					}
				});
				
				//Pie Chart
				c3.generate({
					 bindto: '#pie-chart',
					data: {
						columns: [
							['Desktops', 78],
							['Smart Phones', 55],
							['Mobiles', 40],
							['Tablets', 25]
						],
						type : 'pie'
					},
					color: {
						pattern: ['#f0f1f4', '#1b82ec', '#f16c69', '#f5b225']
					},
					pie: {
						label: {
						  show: false
						}
					}
				});

			},
			$.ChartC3 = new ChartC3, $.ChartC3.Constructor = ChartC3

		}(window.jQuery),

		//initializing 
		function($) {
			"use strict";
			$.ChartC3.init()
		}(window.jQuery);
	});
</script>