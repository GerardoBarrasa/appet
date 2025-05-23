<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">Form Advanced</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0);">Agroxa</a></li>
				<li class="breadcrumb-item"><a href="javascript:void(0);">Forms</a></li>
				<li class="breadcrumb-item active">Form Advanced</li>
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

					<h4 class="mt-0 header-title">Colorpicker</h4>
					<p class="text-muted m-b-30">Fancy and customizable colorpicker
						plugin for Twitter Bootstrap.</p>

					<div class="color-picker-inputs">
						<form action="#">
							<div class="form-group">
								<label>Simple input field</label>
								<input type="text" class="colorpicker-default form-control" value="#8fff00">
							</div>
							<div class="form-group">
								<label>With custom options - RGBA</label>
								<input type="text" class="colorpicker-rgba form-control" value="rgb(0,194,255,0.78)" data-color-format="rgba">
							</div>
							<div class="form-group m-b-0">
								<label>As a component</label>
								<div data-color-format="rgb" data-color="rgb(255, 146, 180)" class="colorpicker-default input-group">
									<input type="text" readonly="readonly" value="" class="form-control">
									<span class="input-group-append add-on">
										<button class="btn btn-white" type="button">
											<i style="background-color: rgb(124, 66, 84);margin-top: 2px;"></i>
										</button>
									</span>
								</div>
							</div>
							<div class="form-group">
								<label>Horizontal mode</label>
								<input type="text" class="form-control" id="colorpicker-horizontal">
							</div>

							<div class="form-group">
								<label>Aliased color palette</label>
								<div id="colorpicker-color-pattern" data-format="alias" class="input-group colorpicker-component">
									<input type="text" value="primary" class="form-control" />
									<span class="input-group-append add-on">
										<button class="btn btn-white" type="button">
											<i style="background-color: #337ab7;margin-top: 2px;"></i>
										</button>
									</span>
								</div>
							</div>

							<div class="form-group">
								<label>Customized widget size</label>
								<input type="text" class="colorpicker-large form-control" value="pink">
							</div>

						</form>
					</div>

				</div>
			</div>

			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Bootstrap MaxLength</h4>
					<p class="text-muted m-b-30">This plugin integrates by default with
						Twitter bootstrap using badges to display the maximum lenght of the
						field where the user is inserting text. </p>

					<label class="text-muted">Default usage</label>
					<p class="text-muted m-b-15">
						The badge will show up by default when the remaining chars are 10 or less:
					</p>
					<input type="text" class="form-control" maxlength="25" name="defaultconfig" id="defaultconfig" />

					<div class="m-t-20">
						<label class="text-muted">Threshold value</label>
						<p class="text-muted m-b-15">
							Do you want the badge to show up when there are 20 chars or less? Use the <code>threshold</code> option:
						</p>
						<input type="text" maxlength="25" name="thresholdconfig" class="form-control" id="thresholdconfig" />
					</div>

					<div class="m-t-20">
						<label class="text-muted">All the options</label>
						<p class="text-muted m-b-15">
							Please note: if the <code>alwaysShow</code> option is enabled, the <code>threshold</code> option is ignored.
						</p>
						<input type="text" class="form-control" maxlength="25" name="alloptions" id="alloptions" />
					</div>

					<div class="m-t-20">
						<label class="text-muted">Position</label>
						<p class="text-muted m-b-15">
							All you need to do is specify the <code>placement</code> option, with one of those strings. If none
							is specified, the positioning will be defauted to 'bottom'.
						</p>
						<input type="text" class="form-control" maxlength="25" name="placement" id="placement" />
					</div>

					<div class="m-t-20">
						<label class="text-muted">textareas</label>
						<p class="text-muted m-b-15">
							Bootstrap maxlength supports textarea as well as inputs. Even on old IE.
						</p>
						<textarea id="textarea" class="form-control" maxlength="225" rows="3" placeholder="This textarea has a limit of 225 chars."></textarea>
					</div>

				</div>
			</div>

			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Select2</h4>
					<p class="text-muted m-b-30">A mobile and touch friendly input spinner component for Bootstrap</p>

					<form>
						<div class="form-group">
							<label class="control-label">Single Select</label>
							<select class="form-control select2">
								<option>Select</option>
								<optgroup label="Alaskan/Hawaiian Time Zone">
									<option value="AK">Alaska</option>
									<option value="HI">Hawaii</option>
								</optgroup>
								<optgroup label="Pacific Time Zone">
									<option value="CA">California</option>
									<option value="NV">Nevada</option>
									<option value="OR">Oregon</option>
									<option value="WA">Washington</option>
								</optgroup>
								<optgroup label="Mountain Time Zone">
									<option value="AZ">Arizona</option>
									<option value="CO">Colorado</option>
									<option value="ID">Idaho</option>
									<option value="MT">Montana</option>
									<option value="NE">Nebraska</option>
									<option value="NM">New Mexico</option>
									<option value="ND">North Dakota</option>
									<option value="UT">Utah</option>
									<option value="WY">Wyoming</option>
								</optgroup>
								<optgroup label="Central Time Zone">
									<option value="AL">Alabama</option>
									<option value="AR">Arkansas</option>
									<option value="IL">Illinois</option>
									<option value="IA">Iowa</option>
									<option value="KS">Kansas</option>
									<option value="KY">Kentucky</option>
									<option value="LA">Louisiana</option>
									<option value="MN">Minnesota</option>
									<option value="MS">Mississippi</option>
									<option value="MO">Missouri</option>
									<option value="OK">Oklahoma</option>
									<option value="SD">South Dakota</option>
									<option value="TX">Texas</option>
									<option value="TN">Tennessee</option>
									<option value="WI">Wisconsin</option>
								</optgroup>
								<optgroup label="Eastern Time Zone">
									<option value="CT">Connecticut</option>
									<option value="DE">Delaware</option>
									<option value="FL">Florida</option>
									<option value="GA">Georgia</option>
									<option value="IN">Indiana</option>
									<option value="ME">Maine</option>
									<option value="MD">Maryland</option>
									<option value="MA">Massachusetts</option>
									<option value="MI">Michigan</option>
									<option value="NH">New Hampshire</option>
									<option value="NJ">New Jersey</option>
									<option value="NY">New York</option>
									<option value="NC">North Carolina</option>
									<option value="OH">Ohio</option>
									<option value="PA">Pennsylvania</option>
									<option value="RI">Rhode Island</option>
									<option value="SC">South Carolina</option>
									<option value="VT">Vermont</option>
									<option value="VA">Virginia</option>
									<option value="WV">West Virginia</option>
								</optgroup>
							</select>
						</div>
						<div class="form-group">
							<label class="control-label">Multiple Select</label>

							<select class="select2 form-control select2-multiple" multiple="multiple" data-placeholder="Choose ...">
								<optgroup label="Alaskan/Hawaiian Time Zone">
									<option value="AK">Alaska</option>
									<option value="HI">Hawaii</option>
								</optgroup>
								<optgroup label="Pacific Time Zone">
									<option value="CA">California</option>
									<option value="NV">Nevada</option>
									<option value="OR">Oregon</option>
									<option value="WA">Washington</option>
								</optgroup>
								<optgroup label="Mountain Time Zone">
									<option value="AZ">Arizona</option>
									<option value="CO">Colorado</option>
									<option value="ID">Idaho</option>
									<option value="MT">Montana</option>
									<option value="NE">Nebraska</option>
									<option value="NM">New Mexico</option>
									<option value="ND">North Dakota</option>
									<option value="UT">Utah</option>
									<option value="WY">Wyoming</option>
								</optgroup>
								<optgroup label="Central Time Zone">
									<option value="AL">Alabama</option>
									<option value="AR">Arkansas</option>
									<option value="IL">Illinois</option>
									<option value="IA">Iowa</option>
									<option value="KS">Kansas</option>
									<option value="KY">Kentucky</option>
									<option value="LA">Louisiana</option>
									<option value="MN">Minnesota</option>
									<option value="MS">Mississippi</option>
									<option value="MO">Missouri</option>
									<option value="OK">Oklahoma</option>
									<option value="SD">South Dakota</option>
									<option value="TX">Texas</option>
									<option value="TN">Tennessee</option>
									<option value="WI">Wisconsin</option>
								</optgroup>
								<optgroup label="Eastern Time Zone">
									<option value="CT">Connecticut</option>
									<option value="DE">Delaware</option>
									<option value="FL">Florida</option>
									<option value="GA">Georgia</option>
									<option value="IN">Indiana</option>
									<option value="ME">Maine</option>
									<option value="MD">Maryland</option>
									<option value="MA">Massachusetts</option>
									<option value="MI">Michigan</option>
									<option value="NH">New Hampshire</option>
									<option value="NJ">New Jersey</option>
									<option value="NY">New York</option>
									<option value="NC">North Carolina</option>
									<option value="OH">Ohio</option>
									<option value="PA">Pennsylvania</option>
									<option value="RI">Rhode Island</option>
									<option value="SC">South Carolina</option>
									<option value="VT">Vermont</option>
									<option value="VA">Virginia</option>
									<option value="WV">West Virginia</option>
								</optgroup>
							</select>

						</div>

					</form>

				</div>
			</div>

		</div> <!-- end col -->

		<div class="col-lg-6">
			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Material DatePicker</h4>
					<p class="text-muted m-b-30">Examples of twitter bootstrap datepicker.</p>

					<form action="#">
						<div class="form-group">
							<label>DatePicker</label>
							<div>
								<input type="text" class="form-control floating-label" placeholder="Date" id="date">
							</div>
							<!-- input-group -->
						</div>

						<div class="form-group">
							<label>Time Picker</label>
							<div>
								<input type="text" id="time" class="form-control floating-label" placeholder="Time">
							</div>
						</div>

						<div class="form-group">
							<label>Date Time Picker</label>
							<div>
								<input type="text" id="date-format" class="form-control floating-label" placeholder="Begin Date Time">
							</div>
						</div>

						<div class="form-group">
							<label>French Locales (Week starts at Monday)</label>
							<div>
								<input type="text" id="date-fr" class="form-control floating-label" value="18/03/2018 08:00" placeholder="Date de début">
							</div>
						</div>

						<div class="form-group">
							<label>Min Date set</label>
							<div>
								<input type="text" id="min-date" class="form-control floating-label" placeholder="Start Date">
							</div>
						</div>

						<div class="form-group">
							<label>Events</label>
							<div>
								<div class="input-group">
									<input type="text" id="date-start" class="form-control floating-label" placeholder="Start Date">
									<input type="text" id="date-end" class="form-control floating-label" placeholder="End Date">

								</div>
							</div>
						</div>
					</form>
				</div>
			</div>


			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Bootstrap TouchSpin</h4>
					<p class="text-muted m-b-30">A mobile and touch friendly input spinner component for Bootstrap</p>

					<form>
						<div class="form-group">
							<label class="control-label">Using data attributes</label>
							<input id="demo0" type="text" value="55" name="demo0" data-bts-min="0" data-bts-max="100" data-bts-init-val="" data-bts-step="1" data-bts-decimal="0" data-bts-step-interval="100" data-bts-force-step-divisibility="round" data-bts-step-interval-delay="500" data-bts-prefix="" data-bts-postfix="" data-bts-prefix-extra-class="" data-bts-postfix-extra-class="" data-bts-booster="true" data-bts-boostat="10" data-bts-max-boosted-step="false" data-bts-mousewheel="true" data-bts-button-down-class="btn btn-default" data-bts-button-up-class="btn btn-default"/>
						</div>
						<div class="form-group">
							<label class="control-label">Example with postfix (large)</label>
							<input id="demo1" type="text" value="55" name="demo1">
						</div>
						<div class="form-group">
							<label class="control-label">With prefix </label>
							<input id="demo2" type="text" value="0" name="demo2" class=" form-control">
						</div>

						<div class="form-group">
							<label class="control-label">Init with empty value:</label>
							<input id="demo3" type="text" value="" name="demo3">
						</div>
						<div class="form-group">
							<label class="control-label">Value attribute is not set (applying settings.initval)</label>
							<input id="demo3_21" type="text" value="" name="demo3_21">
						</div>
						<div class="form-group">
							<label class="control-label">Value is set explicitly to 33 (skipping settings.initval) </label>
							<input id="demo3_22" type="text" value="33" name="demo3_22">
						</div>

					</form>

				</div>
			</div>

			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Css Switch</h4>
					<p class="text-muted m-b-30">Here are a few types of switches. </p>

					<div>
						<input type="checkbox" id="switch1" switch="none" checked/>
						<label for="switch1" data-on-label="On"
								data-off-label="Off"></label>

						<input type="checkbox" id="switch2" switch="default" checked/>
						<label for="switch2" data-on-label=""
								data-off-label=""></label>

						<input type="checkbox" id="switch3" switch="bool" checked/>
						<label for="switch3" data-on-label="Yes"
								data-off-label="No"></label>

						<input type="checkbox" id="switch6" switch="primary" checked/>
						<label for="switch6" data-on-label="Yes"
								data-off-label="No"></label>

						<input type="checkbox" id="switch4" switch="success" checked/>
						<label for="switch4" data-on-label="Yes"
								data-off-label="No"></label>

						<input type="checkbox" id="switch7" switch="info" checked/>
						<label for="switch7" data-on-label="Yes"
								data-off-label="No"></label>

						<input type="checkbox" id="switch5" switch="warning" checked/>
						<label for="switch5" data-on-label="Yes"
								data-off-label="No"></label>

						<input type="checkbox" id="switch8" switch="danger" checked/>
						<label for="switch8" data-on-label="Yes"
								data-off-label="No"></label>

						<input type="checkbox" id="switch9" switch="dark" checked/>
						<label for="switch9" data-on-label="Yes"
								data-off-label="No"></label>

					</div>

				</div>
			</div>
			


			<div class="card m-b-20">
				<div class="card-body">

					<h4 class="mt-0 header-title">Bootstrap FileStyle</h4>
					<p class="text-muted m-b-30">Examples of bootstrap fileStyle.</p>

					<form action="#">
						<div class="form-group">
							<label>Default file input</label>
							<input type="file" class="filestyle" data-buttonname="btn-secondary">
						</div>

						<div class="form-group">
							<label>File style without input</label>
							<input type="file" class="filestyle" data-input="false" data-buttonname="btn-secondary">
						</div>

					</form>
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

			var AdvancedForm = function() {};
			
			AdvancedForm.prototype.init = function() {
				//creating various controls

				//colorpicker start
				$('.colorpicker-default').colorpicker({
					format: 'hex'
				});
				$('.colorpicker-rgba').colorpicker();

				$('#colorpicker-horizontal').colorpicker({
					color: "#88cc33",
					horizontal: true
				});
				$('#colorpicker-color-pattern').colorpicker({
					colorSelectors: {
						'black': '#000000',
						'white': '#ffffff',
						'red': '#FF0000',
						'default': '#777777',
						'primary': '#337ab7',
						'success': '#5cb85c',
						'info': '#5bc0de',
						'warning': '#f0ad4e',
						'danger': '#d9534f'
					}
				});

				$('.colorpicker-large').colorpicker({
					customClass: 'colorpicker-2x',
					sliders: {
						saturation: {
							maxLeft: 200,
							maxTop: 200
						},
						hue: {
							maxTop: 200
						},
						alpha: {
							maxTop: 200
						}
					}
				});

				// Date Picker
				$('#date').bootstrapMaterialDatePicker({ weekStart : 0, time: false });

				$('#time').bootstrapMaterialDatePicker({ date: false });

				$('#date-format').bootstrapMaterialDatePicker({ format : 'dddd DD MMMM YYYY - HH:mm' });

				$('#date-fr').bootstrapMaterialDatePicker({ format : 'DD/MM/YYYY HH:mm', lang : 'fr', weekStart : 1, cancelText : 'ANNULER' });

				$('#min-date').bootstrapMaterialDatePicker({ format : 'DD/MM/YYYY HH:mm', minDate : new Date() });

				$('#date-end').bootstrapMaterialDatePicker({ weekStart : 0 });
				$('#date-start').bootstrapMaterialDatePicker({ weekStart : 0 }).on('change', function(e, date)
				{
				$('#date-end').bootstrapMaterialDatePicker('setMinDate', date);
				});


				//Bootstrap-MaxLength
				$('input#defaultconfig').maxlength({
					warningClass: "badge badge-info",
					limitReachedClass: "badge badge-warning"
				});

				$('input#thresholdconfig').maxlength({
					threshold: 20,
					warningClass: "badge badge-info",
					limitReachedClass: "badge badge-warning"
				});

				$('input#moreoptions').maxlength({
					alwaysShow: true,
					warningClass: "badge badge-success",
					limitReachedClass: "badge badge-danger"
				});

				$('input#alloptions').maxlength({
					alwaysShow: true,
					warningClass: "badge badge-success",
					limitReachedClass: "badge badge-danger",
					separator: ' out of ',
					preText: 'You typed ',
					postText: ' chars available.',
					validate: true
				});

				$('textarea#textarea').maxlength({
					alwaysShow: true,
					warningClass: "badge badge-info",
					limitReachedClass: "badge badge-warning"
				});

				$('input#placement').maxlength({
					alwaysShow: true,
					placement: 'top-left',
					warningClass: "badge badge-info",
					limitReachedClass: "badge badge-warning"
				});

				//Bootstrap-TouchSpin
				$(".vertical-spin").TouchSpin({
					verticalbuttons: true,
					verticalupclass: 'ion-plus-round',
					verticaldownclass: 'ion-minus-round',
					buttondown_class: 'btn btn-primary',
					buttonup_class: 'btn btn-primary'
				});

				$("input[name='demo1']").TouchSpin({
					min: 0,
					max: 100,
					step: 0.1,
					decimals: 2,
					boostat: 5,
					maxboostedstep: 10,
					postfix: '%',
					buttondown_class: 'btn btn-primary',
					buttonup_class: 'btn btn-primary'
				});
				$("input[name='demo2']").TouchSpin({
					min: -1000000000,
					max: 1000000000,
					stepinterval: 50,
					maxboostedstep: 10000000,
					prefix: '$',
					buttondown_class: 'btn btn-primary',
					buttonup_class: 'btn btn-primary'
				});
				$("input[name='demo3']").TouchSpin({
					buttondown_class: 'btn btn-primary',
					buttonup_class: 'btn btn-primary'
				});
				$("input[name='demo3_21']").TouchSpin({
					initval: 40,
					buttondown_class: 'btn btn-primary',
					buttonup_class: 'btn btn-primary'
				});
				$("input[name='demo3_22']").TouchSpin({
					initval: 40,
					buttondown_class: 'btn btn-primary',
					buttonup_class: 'btn btn-primary'
				});

				$("input[name='demo5']").TouchSpin({
					prefix: "pre",
					postfix: "post",
					buttondown_class: 'btn btn-primary',
					buttonup_class: 'btn btn-primary'
				});
				$("input[name='demo0']").TouchSpin({
					buttondown_class: 'btn btn-primary',
					buttonup_class: 'btn btn-primary'
				});

				// Select2
				$(".select2").select2();

				$(".select2-limiting").select2({
					maximumSelectionLength: 2
				});
			},
			//init
			$.AdvancedForm = new AdvancedForm, $.AdvancedForm.Constructor = AdvancedForm
		}(window.jQuery),

		//initializing
		function ($) {
			"use strict";
			$.AdvancedForm.init();
		}(window.jQuery);
	});
</script>