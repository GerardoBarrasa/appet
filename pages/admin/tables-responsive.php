<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">Responsive Table</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0);">Agroxa</a></li>
				<li class="breadcrumb-item"><a href="javascript:void(0);">Tables</a></li>
				<li class="breadcrumb-item active">Responsive Table</li>
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

					<h4 class="mt-0 header-title">Example</h4>
					<p class="text-muted m-b-30">This is an experimental awesome solution for responsive tables with complex data.</p>

					<table class="footable table table-striped bg-default" >
						<thead>
							<tr>
								<th data-class="expand" class="text-center">ID</th>
								<th data-class="expand">Name</th>
								<th data-hide="phone,tablet" class="text-center">Created date</th>
								<th data-hide="phone,tablet" class="text-center">Modified date</th>
							</tr>
						</thead>
						<tbody>
							<tr class="gradeX">
								<td class="text-center">1</td>
								<td><strong>Test Name</strong></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
							</tr>
							<tr class="gradeX">
								<td class="text-center">1</td>
								<td><strong>Test Name</strong></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
							</tr>
							<tr class="gradeX">
								<td class="text-center">1</td>
								<td><strong>Test Name</strong></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
							</tr>
							<tr class="gradeX">
								<td class="text-center">1</td>
								<td><strong>Test Name</strong></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
							</tr>
							<tr class="gradeX">
								<td class="text-center">1</td>
								<td><strong>Test Name</strong></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
							</tr>
							<tr class="gradeX">
								<td class="text-center">1</td>
								<td><strong>Test Name</strong></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
							</tr>
							<tr class="gradeX">
								<td class="text-center">1</td>
								<td><strong>Test Name</strong></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
							</tr>
							<tr class="gradeX">
								<td class="text-center">1</td>
								<td><strong>Test Name</strong></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
								<td class="text-center"><?=Tools::->fecha(date('Y-m-d H:i:s'))?></td>
							</tr>
						</tbody>
					</table>

					<script>
						$(function()
						{
							$('.footable').footable();
						});
					</script>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->   
</div>
<!-- end page content-->
