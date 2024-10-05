<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<div class="row">
				<div class="col-md-6">
					<h4 class="page-title"><?=l('admin-usuarios-admin-title');?></h4>
				</div>
				<div class="col-md-6 text-right">
					<a href="<?= _DOMINIO_ . _ADMIN_ . 'usuario-admin/new/' ?>" type="button" class="btn btn-primary waves-effect waves-light">
						<i class="fas fa-plus text-light"></i> <?=l('admin-usuarios-admin-nuevo');?>
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
								<label for="busqueda"><?=l('admin-search-field');?></label>
								<input type="text" class="form-control" name="busqueda" aria-describedby="buscarHelpId" placeholder="<?=l('admin-search-field-placeholder');?>" onKeyUp="ajax_get_usuarios_admin(0, 10, 1);">
							</div>
						</div>
					</div>
				</form>
				
				<div id="page-content"></div>
				<script> ajax_get_usuarios_admin(<?=$comienzo;?>,<?=$limite;?>,<?= $pagina;?>); </script>
				
			</div>
		</div>
	</div> <!-- end col -->
</div> <!-- end row -->
