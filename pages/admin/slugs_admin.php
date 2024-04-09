<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<div class="row">
				<div class="col-md-6">
					<h4 class="page-title">Páginas meta</h4>
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
						<div class="row justify-content-end">
							<div class="col-12 col-lg-2">
								<div class="form-group">
									<label for="busqueda">Búsqueda</label>
									<input type="text" class="form-control" id="busqueda" name="busqueda" onkeyup="ajax_get_metas_admin('<?=$comienzo?>', '<?=$limite?>', '<?=$pagina?>');">
								</div>
							</div>

							<div class="col-12 col-lg-2">
								<div class="form-group">
									<label for="page">Todas las páginas</label>
									<select name="page" id="page">
										<option value="">--</option>
										<?php
										foreach($slugsPages as $key => $page)
										{
											$name = str_replace("-", " ", $page->mod_id);
											$name = ucfirst($name);
											?>
											<option value="<?=$page->mod_id?>"><?=$name?></option>
											<?php
										}
										?>
									</select>
								</div>
							</div>

							<div class="col-12 col-lg-2">
								<div class="form-group">
									<label for="id_idioma">Idiomas</label>
									<select name="id_idioma" id="id_idioma">
										<option value="">--</option>
										<?php
										foreach($languages as $key => $lang)
										{
											?>
											<option value="<?=$lang->id?>"><?=$lang->nombre?></option>
											<?php
										}
										?>
									</select>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<p>Administra los diferentes slugs (urls disponibles) de <?=_TITULO_;?>. <strong>Por defecto</strong> sólo salen los slugs en el idioma <img src="<?=_DOMINIO_.$languageDefault->icon?>" width="20px" />. Pero usando los filtros se pueden ver los slugs en los diversos idiomas para su edición.</p>
							</div>
						</div>
					</form>
					
					<div id="page-content"></div>
					<script>
						$(document).ready(function() {
							$("#page").select2();
							$("#id_idioma").select2();

							$("#page, #id_idioma").change( function(){
								ajax_get_metas_admin(<?=$comienzo;?>,<?=$limite;?>,<?=$pagina;?>)
							});
	        			});  
					</script>
					<script>ajax_get_metas_admin(<?=$comienzo;?>,<?=$limite;?>,<?=$pagina;?>); </script>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>
