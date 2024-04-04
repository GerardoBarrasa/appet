<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">Range Slider</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0);">Agroxa</a></li>
				<li class="breadcrumb-item"><a href="javascript:void(0);">Components</a></li>
				<li class="breadcrumb-item active">Range Slider</li>
			</ol>
		</div>
	</div>
</div>
<!-- end row -->

<div class="page-content-wrapper">
	<div class="row">
		<div class="col-12">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">ION Range slider</h4>
					<p class="text-muted m-b-30">Cool, comfortable, responsive and easily customizable range slider</p>

					<div class="row">
						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Default</h5>
								<input type="text" id="range_01">
							</div>
						</div>

						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Min-Max</h5>
								<input type="text" id="range_02">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Prefix</h5>
								<input type="text" id="range_03">
							</div>
						</div>

						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Range</h5>
								<input type="text" id="range_04">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Step</h5>
								<input type="text" id="range_05">
							</div>
						</div>

						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Custom Values</h5>
								<input type="text" id="range_06">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Prettify Numbers</h5>
								<input type="text" id="range_07">
							</div>
						</div>

						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Disabled</h5>
								<input type="text" id="range_08">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Extra Example</h5>
								<input type="text" id="range_09">
							</div>
						</div>

						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Use decorate_both option</h5>
								<input type="text" id="range_10">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Postfixes</h5>
								<input type="text" id="range_11">
							</div>
						</div>

						<div class="col-lg-6">
							<div class="p-3">
								<h5 class="font-14 m-b-20 mt-0">Hide</h5>
								<input type="text" id="range_12">
							</div>
						</div>
					</div>

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->

</div>
<!-- end page content-->

<script>
	$(document).ready(function () {
	    $("#range_01").ionRangeSlider();
	    
	    $("#range_02").ionRangeSlider({
	        min: 100,
	        max: 1000,
	        from: 550
	    });
	    
	    $("#range_03").ionRangeSlider({
	        type: "double",
	        grid: true,
	        min: 0,
	        max: 1000,
	        from: 200,
	        to: 800,
	        prefix: "$"
	    });
	   
	    $("#range_04").ionRangeSlider({
	        type: "double",
	        grid: true,
	        min: -1000,
	        max: 1000,
	        from: -500,
	        to: 500
	    });
	    
	    $("#range_05").ionRangeSlider({
	        type: "double",
	        grid: true,
	        min: -1000,
	        max: 1000,
	        from: -500,
	        to: 500,
	        step: 250
	    });
	    
	    $("#range_06").ionRangeSlider({
	        grid: true,
	        from: 3,
	        values: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
	    });
	    
	    $("#range_07").ionRangeSlider({
	        grid: true,
	        min: 1000,
	        max: 1000000,
	        from: 200000,
	        step: 1000,
	        prettify_enabled: true
	    });
	    
	    $("#range_08").ionRangeSlider({
	        min: 100,
	        max: 1000,
	        from: 550,
	        disable: true
	    });
	    $("#range_09").ionRangeSlider({
	        grid: true,
	        min: 18,
	        max: 70,
	        from: 30,
	        prefix: "Age ",
	        max_postfix: "+"
	    });
	    $("#range_10").ionRangeSlider({
	        type: "double",
	        min: 100,
	        max: 200,
	        from: 145,
	        to: 155,
	        prefix: "Weight: ",
	        postfix: " million pounds",
	        decorate_both: true
	    });
	    $("#range_11").ionRangeSlider({
	        type: "single",
	        grid: true,
	        min: -90,
	        max: 90,
	        from: 0,
	        postfix: "Â°"
	    });
	    $("#range_12").ionRangeSlider({
	        type: "double",
	        min: 1000,
	        max: 2000,
	        from: 1200,
	        to: 1800,
	        hide_min_max: true,
	        hide_from_to: true,
	        grid: true
	    });
	});
</script>