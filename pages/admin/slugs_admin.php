<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<div class="row">
				<div class="col-md-6">
					<h4 class="page-title"><?=l('admin-slugs-title');?></h4>
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
									<label for="busqueda"><?=l('admin-search-field');?></label>
									<input type="text" class="form-control" id="busqueda" name="busqueda" onkeyup="ajax_get_metas_admin('<?=$comienzo?>', '<?=$limite?>', '<?=$pagina?>');" placeholder="<?=l('admin-search-field-placeholder');?>">
								</div>
							</div>

							<div class="col-12 col-lg-2">
								<div class="form-group">
									<label for="page"><?=l('admin-slugs-filtro-paginas');?></label>
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
									<label for="id_idioma"><?=l('admin-slugs-filtro-idioma');?></label>
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
								<p><?=l('admin-slugs-explicacion', array(_TITULO_, '<img src="'._DOMINIO_.$languageDefault->icon.'" width="20px" />'));?></p>
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
