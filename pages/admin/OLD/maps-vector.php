<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">Vector Map</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0);">Agroxa</a></li>
				<li class="breadcrumb-item"><a href="javascript:void(0);">Maps</a></li>
				<li class="breadcrumb-item active">Vector Map</li>
			</ol>
		</div>
	</div>
</div>
<!-- end row -->

<div class="page-content-wrapper">
	<div class="row">
		<div class="col-lg-6">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">World Map</h4>
					<p class="text-muted m-b-30">Example of vector map.</p>

					<div id="world-map-markers" class="vector-map-height"></div>

				</div>
			</div>
		</div> <!-- end col -->
		<div class="col-lg-6">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">USA Map</h4>
					<p class="text-muted m-b-30">Example of vector map.</p>

					<div id="usa" class="vector-map-height"></div>

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->


	<div class="row">
		<div class="col-lg-6">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">UK Map</h4>
					<p class="text-muted m-b-30">Example of vector map.</p>

					<div id="uk" class="vector-map-height"></div>

				</div>
			</div>
		</div> <!-- end col -->

		<div class="col-lg-6">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">Chicago Map</h4>
					<p class="text-muted m-b-30">Example of vector map.</p>

					<div id="chicago" class="vector-map-height"></div>

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

			var VectorMap = function() {};

			VectorMap.prototype.init = function() {
				//various examples
				$('#world-map-markers').vectorMap({
					map : 'world_mill_en',
					scaleColors : ['#f5b225', '#f5b225'],
					normalizeFunction : 'polynomial',
					hoverOpacity : 0.7,
					hoverColor : false,
					regionStyle : {
						initial : {
							fill : '#1b82ec'
						}
					},
					 markerStyle: {
						initial: {
							r: 9,
							'fill': '#f5b225',
							'fill-opacity': 0.9,
							'stroke': '#fff',
							'stroke-width' : 7,
							'stroke-opacity': 0.4
						},

						hover: {
							'stroke': '#fff',
							'fill-opacity': 1,
							'stroke-width': 1.5
						}
					},
					backgroundColor : 'transparent',
					markers : [{
						latLng : [41.90, 12.45],
						name : 'Vatican City'
					}, {
						latLng : [43.73, 7.41],
						name : 'Monaco'
					}, {
						latLng : [-0.52, 166.93],
						name : 'Nauru'
					}, {
						latLng : [-8.51, 179.21],
						name : 'Tuvalu'
					}, {
						latLng : [43.93, 12.46],
						name : 'San Marino'
					}, {
						latLng : [47.14, 9.52],
						name : 'Liechtenstein'
					}, {
						latLng : [7.11, 171.06],
						name : 'Marshall Islands'
					}, {
						latLng : [17.3, -62.73],
						name : 'Saint Kitts and Nevis'
					}, {
						latLng : [3.2, 73.22],
						name : 'Maldives'
					}, {
						latLng : [35.88, 14.5],
						name : 'Malta'
					}, {
						latLng : [12.05, -61.75],
						name : 'Grenada'
					}, {
						latLng : [13.16, -61.23],
						name : 'Saint Vincent and the Grenadines'
					}, {
						latLng : [13.16, -59.55],
						name : 'Barbados'
					}, {
						latLng : [17.11, -61.85],
						name : 'Antigua and Barbuda'
					}, {
						latLng : [-4.61, 55.45],
						name : 'Seychelles'
					}, {
						latLng : [7.35, 134.46],
						name : 'Palau'
					}, {
						latLng : [42.5, 1.51],
						name : 'Andorra'
					}, {
						latLng : [14.01, -60.98],
						name : 'Saint Lucia'
					}, {
						latLng : [6.91, 158.18],
						name : 'Federated States of Micronesia'
					}, {
						latLng : [1.3, 103.8],
						name : 'Singapore'
					}, {
						latLng : [1.46, 173.03],
						name : 'Kiribati'
					}, {
						latLng : [-21.13, -175.2],
						name : 'Tonga'
					}, {
						latLng : [15.3, -61.38],
						name : 'Dominica'
					}, {
						latLng : [-20.2, 57.5],
						name : 'Mauritius'
					}, {
						latLng : [26.02, 50.55],
						name : 'Bahrain'
					}, {
						latLng : [0.33, 6.73],
						name : 'SÃ£o TomÃ© and PrÃ­ncipe'
					}]
				});

				$('#usa').vectorMap({map: 'us_aea_en',backgroundColor: 'transparent',
						  regionStyle: {
							initial: {
							  fill: '#1b82ec'
							}
						  }});
				$('#uk').vectorMap({map: 'uk_mill_en',backgroundColor: 'transparent',
						  regionStyle: {
							initial: {
							  fill: '#1b82ec'
							}
						  }});
				$('#chicago').vectorMap({map: 'us-il-chicago_mill_en',backgroundColor: 'transparent',
						  regionStyle: {
							initial: {
							  fill: '#1b82ec'
							}
						  }});

		  },
			//init
			$.VectorMap = new VectorMap, $.VectorMap.Constructor = VectorMap
		}(window.jQuery),

		//initializing 
		function($) {
			"use strict";
			$.VectorMap.init()
		}(window.jQuery);
	});
</script>