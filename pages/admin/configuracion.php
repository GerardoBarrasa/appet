<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">
                <span class="text-capitalize"><?=l('admin-configuracion-title');?></span>
            </h4>
		</div>
	</div>
</div>

<div class="page-content-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<div class="card m-b-20 px-3">
				<div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <form method="POST">
                                <div class="row">

                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-group mb-0">
                                            <label for="modo_mantenimiento"><?=l('admin-configuracion-modo-mantenimiento');?></label>
                                        </div>
                                        <div class="form-group">
                                            <input type="checkbox" id="modo_mantenimiento" name="modo_mantenimiento" switch="danger" <?=(!empty($modo_mantenimiento) ? 'checked' : '');?> value="1"/>
                                            <label for="modo_mantenimiento" data-on-label="<?=l('admin-si');?>" data-off-label="<?=l('admin-no');?>"></label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-group">
                                            <label for="default_language"><?=l('admin-configuracion-idioma-predeterminado');?></label>
                                            <select name="default_language" id="default_language" class="form-control">
                                                <?php
                                                foreach( $idiomas as $idioma )
                                                {
                                                    ?>
                                                    <option value="<?=$idioma->id;?>" <?=($default_language == $idioma->id ? 'selected' : '');?>><?=$idioma->nombre;?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end align-items-center">
                                        <div class="justify-self-end">
                                            <button type="submit" name="submitUpdateConfiguracionAdmin" class="btn btn-primary  waves-effect waves-light"><?=l('admin-guardar');?></button>
                                        </div>
                                    </div>   
                                </div>
                            </form>
                        </div>
                    </div>
				</div>
			</div>
		</div> <!-- end col -->

	</div> <!-- end row -->
</div>
