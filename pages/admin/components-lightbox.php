<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">Lightbox</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0);">Agroxa</a></li>
				<li class="breadcrumb-item"><a href="javascript:void(0);">Components</a></li>
				<li class="breadcrumb-item active">Lightbox</li>
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

					<h4 class="mt-0 header-title">Single image lightbox</h4>
					<p class="text-muted m-b-30">Three simple popups with different scaling settings.</p>

					<div class="row">
						<div class="col-6">
							<h5 class="mt-0 font-14 m-b-15 text-muted">Fits (Horz/Vert)</h5>
							<a class="image-popup-vertical-fit" href="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" title="Caption. Can be aligned it to any side and contain any HTML.">
								<img class="img-fluid" alt="" src="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg"  width="145">
							</a>
						</div>
						<div class="col-6">
							<h5 class="mt-0 font-14 m-b-15 text-muted">Effects</h5>
							<a class="image-popup-no-margins" href="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg">
								<img class="img-fluid" alt="" src="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" width="75">
							</a>
							<p class="mt-2 mb-0 text-muted">No gaps, zoom animation, close icon in top-right corner.</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">Lightbox gallery</h4>
					<p class="text-muted m-b-30">In this example lazy-loading of images is enabled for the next image based on move direction. </p>

					<div class="popup-gallery">
						<a class="float-left" href="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" title="Project 1">
							<div class="img-responsive">
								<img src="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" alt="" width="120">
							</div>
						</a>
						<a class="float-left" href="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" title="Project 2">
							<div class="img-responsive">
								<img src="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" alt="" width="120">
							</div>
						</a>
						<a class="float-left" href="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" title="Project 3">
							<div class="img-responsive">
								<img src="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" alt="" width="120">
							</div>
						</a>
						<a class="float-left" href="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" title="Project 4">
							<div class="img-responsive">
								<img src="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" alt="" width="120">
							</div>
						</a>
						<a class="float-left" href="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" title="Project 5">
							<div class="img-responsive">
								<img src="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" alt="" width="120">
							</div>
						</a>
						<a class="float-left" href="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" title="Project 6">
							<div class="img-responsive">
								<img src="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" alt="" width="120">
							</div>
						</a>
					</div>

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->


	<div class="row">
		<div class="col-lg-6">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">Zoom Gallery</h4>
					<p class="text-muted m-b-30">Zoom effect works only with images.</p>

					<div class="zoom-gallery">
						<a class="float-left" href="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" title="Project 1"><img src="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" alt="" width="275"></a>
						<a class="float-left" href="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" title="Project 2"><img src="<?=_ASSETS_._ADMIN_;?>images/demo/attach.jpg" alt="" width="275"></a>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="card m-b-30">
				<div class="card-body">

					<h4 class="mt-0 header-title">Popup with video or map</h4>
					<p class="text-muted m-b-30">In this example lazy-loading of images is enabled for the next image based on move direction. </p>

					<div class="row">
						<div class="col-12">
							<a class="popup-youtube btn btn-secondary mo-mb-2" href="http://www.youtube.com/watch?v=0O2aH4XLbto">Open YouTube Video</a>
							<a class="popup-vimeo btn btn-secondary mo-mb-2" href="https://vimeo.com/45830194">Open Vimeo Video</a>
							<a class="popup-gmaps btn btn-secondary mo-mb-2" href="https://maps.google.com/maps?q=221B+Baker+Street,+London,+United+Kingdom&amp;hl=en&amp;t=v&amp;hnear=221B+Baker+St,+London+NW1+6XE,+United+Kingdom">Open Google Map</a>
						</div>
					</div>

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->

</div>
<!-- end page content-->

<script>
	$(document).ready(function(){
		/*
		Single Image
		*/

		$('.image-popup-vertical-fit').magnificPopup({
			type: 'image',
			closeOnContentClick: true,
			mainClass: 'mfp-img-mobile',
			image: {
				verticalFit: true
			}

		});

		$('.image-popup-no-margins').magnificPopup({
			type: 'image',
			closeOnContentClick: true,
			closeBtnInside: false,
			fixedContentPos: true,
			mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
			image: {
				verticalFit: true
			},
			zoom: {
				enabled: true,
				duration: 300 // don't foget to change the duration also in CSS
			}
		});

		/*
		Gallery
		*/
		$('.popup-gallery').magnificPopup({
			delegate: 'a',
			type: 'image',
			tLoading: 'Loading image #%curr%...',
			mainClass: 'mfp-img-mobile',
			gallery: {
				enabled: true,
				navigateByImgClick: true,
				preload: [0,1] // Will preload 0 - before current, and 1 after the current image
			},
			image: {
				tError: '<a href="%url%">The image #%curr%</a> could not be loaded.'
			}
		});

		/*
		Zoom Gallery
		*/
		$('.zoom-gallery').magnificPopup({
			delegate: 'a',
			type: 'image',
			closeOnContentClick: false,
			closeBtnInside: false,
			mainClass: 'mfp-with-zoom mfp-img-mobile',
			image: {
				verticalFit: true,
				titleSrc: function(item) {
					return item.el.attr('title') + ' &middot; <a href="'+item.el.attr('data-source')+'" target="_blank">image source</a>';
				}
			},
			gallery: {
				enabled: true
			},
			zoom: {
				enabled: true,
				duration: 300, // don't foget to change the duration also in CSS
				opener: function(element) {
					return element.find('img');
				}
			}
		});

		/*
		Popup with video or map
		*/
		$('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
			disableOn: 700,
			type: 'iframe',
			mainClass: 'mfp-fade',
			removalDelay: 160,
			preloader: false,

			fixedContentPos: false
		});

		/*
		Dialog with CSS animation
		*/
		$('.popup-with-zoom-anim').magnificPopup({
			type: 'inline',

			fixedContentPos: false,
			fixedBgPos: true,

			overflowY: 'auto',

			closeBtnInside: true,
			preloader: false,

			midClick: true,
			removalDelay: 300,
			mainClass: 'my-mfp-zoom-in'
		});

		$('.popup-with-move-anim').magnificPopup({
			type: 'inline',

			fixedContentPos: false,
			fixedBgPos: true,

			overflowY: 'auto',

			closeBtnInside: true,
			preloader: false,

			midClick: true,
			removalDelay: 300,
			mainClass: 'my-mfp-slide-bottom'
		});
	});
</script>