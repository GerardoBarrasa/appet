<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">
                <span class="text-capitalize"><?=l('admin-textos-legales-title');?></span>
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
                                                foreach( $textos_legales[$idioma->id] as $texto_legal )
                                                {
                                                    ?>
                                                    <div class="col-12 col-md-6">
                                                        <h4><?=l('admin-textos-legales-'.$texto_legal->tipo_texto_legal)?></h4>
                                                        <textarea class="form-control" name="textos-legales[<?=$idioma->id?>][<?=$texto_legal->tipo_texto_legal;?>]"><?=$texto_legal->contenido;?></textarea>
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
                                        <button type="submit" name="submitUpdateTextosLegales" class="btn btn-primary  waves-effect waves-light">
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
