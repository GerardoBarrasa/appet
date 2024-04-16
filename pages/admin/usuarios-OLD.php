<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<div class="row">
				<div class="col-md-6">
					<h4 class="page-title">Usuarios admin</h4>
				</div>
				<div class="col-md-6 text-right">
					<a href="<?= _DOMINIO_ . _ADMIN_ . 'usuario-admin/new/' ?>" type="button" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="Crear usuario">
						<i class="fas fa-plus text-light"></i> Nuevo
					</a>
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

				<form id="formFiltrosAdmin">
					<div class="row">
						<div class="col-12 col-sm-6 col-md-3 offset-sm-6 offset-md-9">
							<div class="form-group">
								<label for="busqueda">Búsqueda</label>
								<input type="text" class="form-control" name="busqueda" aria-describedby="buscarHelpId" placeholder="Buscar" onKeyUp="ajax_get_usuarios(0, 10, 1);">
							</div>
						</div>
					</div>
				</form>
				
				<div id="page-content"></div>
				<script> ajax_get_usuarios(<?=$comienzo;?>,<?=$limite;?>,<?= $pagina;?>); </script>
				
			</div>
		</div>
	</div> <!-- end col -->
</div> <!-- end row -->
