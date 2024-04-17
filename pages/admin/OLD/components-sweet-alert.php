<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">Sweet-Alert</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0);">Agroxa</a></li>
				<li class="breadcrumb-item"><a href="javascript:void(0);">Components</a></li>
				<li class="breadcrumb-item active">Sweet-Alert</li>
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

					<h4 class="mt-0 header-title">Examples</h4>
					<p class="text-muted m-b-30 font-14">A beautiful, responsive, customizable
						and accessible (WAI-ARIA) replacement for JavaScript's popup boxes. Zero
						dependencies.</p>

					<div class="row text-center">
						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">A basic message</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="sa-basic">Click me</button>
						</div>
						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">A title with a text under</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="sa-title">Click me</button>
						</div>
						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">A success message!</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="sa-success">Click me</button>
						</div>
						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">A warning message, with a function attached to the "Confirm"-button...</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="sa-warning">Click me</button>
						</div>

						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">By passing a parameter, you can execute something else for "Cancel".</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="sa-params">Click me</button>
						</div>
						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">A message with custom Image Header</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="sa-image">Click me</button>
						</div>
						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">A message with auto close timer</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="sa-close">Click me</button>
						</div>
						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">Custom HTML description and buttons</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="custom-html-alert">Click me</button>
						</div>
						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">A message with custom width, padding and background</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="custom-padding-width-alert">Click me</button>
						</div>
						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">Ajax request example</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="ajax-alert">Click me</button>
						</div>
						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">Chaining modals (queue) example</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="chaining-alert">Click me</button>
						</div>
						<div class="col-lg-3 col-md-6 m-b-30">
							<p class="text-muted">Dynamic queue example</p>
							<button type="button" class="btn btn-primary waves-effect waves-light" id="dynamic-alert">Click me</button>
						</div>

					</div> <!-- end row -->

				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->

</div>
<!-- end page content-->

<script>
	!function ($) {
		"use strict";

		var SweetAlert = function () {
		};

		//examples
		SweetAlert.prototype.init = function () {

			//Basic
			$('#sa-basic').on('click', function () {
				swal('Any fool can use a computer').catch(swal.noop)
			});

			//A title with a text under
			$('#sa-title').click(function () {
				swal(
					'The Internet?',
					'That thing is still around?',
					'question'
				)
			});

			//Success Message
			$('#sa-success').click(function () {
				swal(
					{
						title: 'Good job!',
						text: 'You clicked the button!',
						type: 'success',
						showCancelButton: true,
						confirmButtonClass: 'btn btn-success',
						cancelButtonClass: 'btn btn-danger m-l-10'
					}
				)
			});

			//Warning Message
			$('#sa-warning').click(function () {
				swal({
					title: 'Are you sure?',
					text: "You won't be able to revert this!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger m-l-10',
					confirmButtonText: 'Yes, delete it!'
				}).then(function () {
					swal(
						'Deleted!',
						'Your file has been deleted.',
						'success'
					)
				})
			});

			//Parameter
			$('#sa-params').click(function () {
				swal({
					title: 'Are you sure?',
					text: "You won't be able to revert this!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes, delete it!',
					cancelButtonText: 'No, cancel!',
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger m-l-10',
					buttonsStyling: false
				}).then(function () {
					swal(
						'Deleted!',
						'Your file has been deleted.',
						'success'
					)
				}, function (dismiss) {
					// dismiss can be 'cancel', 'overlay',
					// 'close', and 'timer'
					if (dismiss === 'cancel') {
						swal(
							'Cancelled',
							'Your imaginary file is safe :)',
							'error'
						)
					}
				})
			});

			//Custom Image
			$('#sa-image').click(function () {
				swal({
					title: 'Sweet!',
					text: 'Modal with a custom image.',
					imageUrl: 'assets/images/logo.png',
					imageHeight: 50,
					animation: false
				})
			});

			//Auto Close Timer
			$('#sa-close').click(function () {
				swal({
					title: 'Auto close alert!',
					text: 'I will close in 2 seconds.',
					timer: 2000
				}).then(
					function () {
					},
					// handling the promise rejection
					function (dismiss) {
						if (dismiss === 'timer') {
							console.log('I was closed by the timer')
						}
					}
				)
			});

			//custom html alert
			$('#custom-html-alert').click(function () {
				swal({
					title: '<i>HTML</i> <u>example</u>',
					type: 'info',
					html: 'You can use <b>bold text</b>, ' +
					'<a href="//themesdesign.in/">links</a> ' +
					'and other HTML tags',
					showCloseButton: true,
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger m-l-10',
					confirmButtonText: '<i class="fa fa-thumbs-up"></i> Great!',
					cancelButtonText: '<i class="fa fa-thumbs-down"></i>'
				})
			});

			//Custom width padding
			$('#custom-padding-width-alert').click(function () {
				swal({
					title: 'Custom width, padding, background.',
					width: 600,
					padding: 100,
					background: '#fff url(//subtlepatterns2015.subtlepatterns.netdna-cdn.com/patterns/geometry.png)'
				})
			});

			//Ajax
			$('#ajax-alert').click(function () {
				swal({
					title: 'Submit email to run ajax request',
					input: 'email',
					showCancelButton: true,
					confirmButtonText: 'Submit',
					showLoaderOnConfirm: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger m-l-10',
					preConfirm: function (email) {
						return new Promise(function (resolve, reject) {
							setTimeout(function () {
								if (email === 'taken@example.com') {
									reject('This email is already taken.')
								} else {
									resolve()
								}
							}, 2000)
						})
					},
					allowOutsideClick: false
				}).then(function (email) {
					swal({
						type: 'success',
						title: 'Ajax request finished!',
						html: 'Submitted email: ' + email
					})
				})
			});

			//chaining modal alert
			$('#chaining-alert').click(function () {
				swal.setDefaults({
					input: 'text',
					confirmButtonText: 'Next &rarr;',
					showCancelButton: true,
					animation: false,
					progressSteps: ['1', '2', '3']
				})

				var steps = [
					{
						title: 'Question 1',
						text: 'Chaining swal2 modals is easy'
					},
					'Question 2',
					'Question 3'
				]

				swal.queue(steps).then(function (result) {
					swal.resetDefaults()
					swal({
						title: 'All done!',
						html: 'Your answers: <pre>' +
						JSON.stringify(result) +
						'</pre>',
						confirmButtonText: 'Lovely!',
						showCancelButton: false
					})
				}, function () {
					swal.resetDefaults()
				})
			});

			//Danger
			$('#dynamic-alert').click(function () {
				swal.queue([{
					title: 'Your public IP',
					confirmButtonText: 'Show my public IP',
					text: 'Your public IP will be received ' +
					'via AJAX request',
					showLoaderOnConfirm: true,
					preConfirm: function () {
						return new Promise(function (resolve) {
							$.get('https://api.ipify.org?format=json')
								.done(function (data) {
									swal.insertQueueStep(data.ip)
									resolve()
								})
						})
					}
				}])
			});


		},
			//init
			$.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert
	}(window.jQuery),

	//initializing
	function ($) {
		"use strict";
		$.SweetAlert.init()
	}(window.jQuery);
</script>