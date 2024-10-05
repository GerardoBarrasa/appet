<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">Google Map</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0);">Agroxa</a></li>
				<li class="breadcrumb-item"><a href="javascript:void(0);">Maps</a></li>
				<li class="breadcrumb-item active">Google Map</li>
			</ol>
		</div>
	</div>
</div>
<!-- end row -->

<div class="page-content-wrapper">
	<div class="row">
		<div class="col-lg-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Markers</h4>
					<p class="text-muted m-b-30">Example of google maps.</p>

					<div id="gmaps-markers" class="gmaps"></div>
				</div>
			</div>
		</div> <!-- end col -->

		<div class="col-lg-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Overlays</h4>
					<p class="text-muted m-b-30">Example of google maps.</p>

					<div id="gmaps-overlay" class="gmaps"></div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->


	<div class="row">
		<div class="col-lg-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Street View Panoramas</h4>
					<p class="text-muted m-b-30">Example of google maps.</p>

					<div id="panorama" class="gmaps-panaroma"></div>
				</div>
			</div>
		</div> <!-- end col -->

		<div class="col-lg-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Map Types</h4>
					<p class="text-muted m-b-30">Example of google maps.</p>

					<div id="gmaps-types" class="gmaps"></div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->  
</div>
<!-- end page content-->
		
<script>
	var map;
	$(document).ready(function(){
	  // Markers
	  map = new GMaps({
		div: '#gmaps-markers',
		lat: -12.043333,
		lng: -77.028333
	  });
	  map.addMarker({
		lat: -12.043333,
		lng: -77.03,
		title: 'Lima',
		details: {
		  database_id: 42,
		  author: 'HPNeo'
		},
		click: function(e){
		  if(console.log)
			console.log(e);
		  alert('You clicked in this marker');
		}
	  });

	  // Overlays
	  map = new GMaps({
		div: '#gmaps-overlay',
		lat: -12.043333,
		lng: -77.028333
	  });
	  map.drawOverlay({
		lat: map.getCenter().lat(),
		lng: map.getCenter().lng(),
		content: '<div class="gmaps-overlay">Lima<div class="gmaps-overlay_arrow above"></div></div>',
		verticalAlign: 'top',
		horizontalAlign: 'center'
	  });

	  //panorama
	  map = GMaps.createPanorama({
		el: '#panorama',
		lat : 42.3455,
		lng : -71.0983
	  });

	  //Map type
	  map = new GMaps({
		div: '#gmaps-types',
		lat: -12.043333,
		lng: -77.028333,
		mapTypeControlOptions: {
		  mapTypeIds : ["hybrid", "roadmap", "satellite", "terrain", "osm"]
		}
	  });
	  map.addMapType("osm", {
		getTileUrl: function(coord, zoom) {
		  return "https://a.tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
		tileSize: new google.maps.Size(256, 256),
		name: "OpenStreetMap",
		maxZoom: 18
	  });
	  map.setMapTypeId("osm");
	});
</script>