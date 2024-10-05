<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="page-title"><span class="text-capitalize"><?=l('admin-textos-emails-title');?></span></h4>
                </div>
                <div class="col-md-6 text-right">
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#addNewEmailText" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i> <?=l('admin-textos-emails-nuevo');?></a>
                </div>
            </div>
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
                                <div class="row" >
                                <?php
                                    foreach( $idiomas as $idioma )
                                    {
                                        ?>
                                        <div class="col-12 mb-5">
                                            <h3><?=$idioma->nombre;?></h3>
                                            <hr/>
                                            <div class="row">
                                                <?php
                                                foreach( $textos_emails[$idioma->id] as $texto_email )
                                                {
                                                    ?>
                                                    <div class="col-12 col-md-6">
                                                        <h4>
                                                            <?=l('admin-textos-emails-'.$texto_email->tipo_texto_email)?>
                                                            <br/>
                                                            <small><?=$texto_email->tipo_texto_email;?></small>
                                                        </h4>
                                                        <label><?=l('admin-textos-emails-asunto');?></label>
                                                        <input type="text" class="form-control" name="textos-emails[<?=$idioma->id?>][<?=$texto_email->tipo_texto_email;?>][asunto]" value="<?=$texto_email->asunto;?>" max-length="250" />
                                                        <br/>
                                                        <label><?=l('admin-textos-emails-contenido');?></label>
                                                        <textarea class="form-control" name="textos-emails[<?=$idioma->id?>][<?=$texto_email->tipo_texto_email;?>][contenido]"><?=$texto_email->contenido;?></textarea>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                ?>
                                </div>
                                
                                <div class="col-12 d-flex justify-content-end align-items-center">
                                    <div class="justify-self-end ml-2">
                                        <button type="submit" name="submitUpdateTextosEmails" class="btn btn-primary  waves-effect waves-light">
                                            <?=l('admin-actualizar');?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
				</div>
			</div>
		</div> <!-- end col -->

        <!-- MODAL PARA AÑADIR NUEVA PLANTILLA EMAIL -->
        <div class="modal fade" id="addNewEmailText" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><?=l('admin-textos-emails-nuevo');?></h5>
                        <button type="button" id="closeModal" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form method="post" action="<?=_DOMINIO_._ADMIN_.'textos-emails/';?>" name="form_new_texto_email" id="form_new_texto_email">
                            <div class="form-group">
                                <label class="mb-0" for="nombre"><?=l('admin-textos-emails-nombre-interno');?></label>
                                <input type="text" name="nombre_interno" style="border: 1px solid #ccc;" value="" id="nombre" class="form-control" placeholder="<?=l('admin-textos-emails-nombre-interno-placeholder');?>">
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-success" name="submitAddTextosEmails"><?=l('admin-crear');?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
	</div> <!-- end row -->
</div>

<script>
    $(document).ready(function() {
        tinymce.init({
            selector: 'textarea',
            mode : "textareas",
            plugins : "paste",
            theme_advanced_buttons3_add : "pastetext,pasteword,selectall",
            paste_auto_cleanup_on_paste : true
        });
    });
</script>
