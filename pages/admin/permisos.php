<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<div class="row">
				<div class="col-md-6">
					<h4 class="page-title"><?=l('admin-permisos-title');?></h4>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- end row -->

<div class="page-content-wrapper">
	<div class="row">
		<div class="col-12">
			<div class="card m-b-20">
			<div class="card-body">
				<div id="page-content"></div>
				<script>ajax_get_perfiles(<?=$comienzo;?>,<?=$limite;?>,<?= $pagina;?>);</script>
			</div>
		</div>
	</div> <!-- end col -->
</div> <!-- end row -->
