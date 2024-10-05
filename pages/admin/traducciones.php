<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="page-title"><?=l('admin-traducciones-title');?></h4>
                </div>
                <div class="col-md-6 text-right">
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#addNewTranslation" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i> <?=l('admin-traducciones-nuevo');?></a>
                    <a href="<?=_DOMINIO_._ADMIN_;?>regenerar-cache-traducciones/" class="btn btn-danger waves-effect waves-light"> <i class="fas fa-redo-alt"></i> <?=l('admin-traducciones-regenerar-cache');?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="row">
        <!-- ESTADISTICAS DE TRADUCCION -->
        <div class="col-12">
            <div class="row">
                <?php
                    foreach($idiomas as $idioma)
                    {
                        ?>
                        <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title"><img src="<?=_DOMINIO_.$idioma->icon?>" width="20" />&nbsp;&nbsp;<?=$idioma->nombre?></h4>
                                    <div class="d-flex justify-content-between">
                                        <p class="text-muted"><?=l('admin-traducciones-porcentaje', array($porcentajeTraduccionesPorIdioma[$idioma->id]));?>%</p>
                                    </div>
                                    <div class="progress progress-md">
                                        <div class="progress-bar" style="background: <?=$idioma->colour?>; width: <?=$porcentajeTraduccionesPorIdioma[$idioma->id];?>%;" role="progressbar" aria-valuenow="<?=$porcentajeTraduccionesPorIdioma[$idioma->id]?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>

        <div class="col-12">
            <div class="card m-b-20">
                <div class="card-body">

                    <form id="formFiltrosAdmin">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label for="busqueda"><?=l('admin-search-field');?></label>
                                    <input type="text" name="busqueda" id="busqueda" value="" onkeyup="ajax_get_traductions_filtered('<?=$comienzo?>', '<?=$limite?>', '<?=$pagina?>');" placeholder="<?=l('admin-search-field-placeholder');?>" class="form-control" />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label for="zona"><?=l('admin-traducciones-campo-zona');?></label>
                                <select name="zona" id="zona" class="form-control" onchange="ajax_get_traductions_filtered('<?=$comienzo?>', '<?=$limite?>', '<?=$pagina?>');">
                                    <option value="0">--</option>
                                    <?php
                                    foreach( $zonas as $zona )
                                    {
                                        ?>
                                        <option value="<?=$zona->zona;?>"><?=ucwords($zona->zona);?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label for="translation_status"><?=l('admin-traducciones-campo-estado');?></label>
                                <select name="translation_status" id="translation_status" class="form-control" onchange="ajax_get_traductions_filtered('<?=$comienzo?>', '<?=$limite?>', '<?=$pagina?>');">
                                    <option value="0"><?=l('admin-traducciones-campo-estado-todas');?></option>
                                    <option value="1"><?=l('admin-traducciones-campo-estado-pendientes');?></option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label for="id_lang"><?=l('admin-traducciones-campo-idioma');?></label>
                                <select name="id_lang" id="id_lang" class="form-control" onchange="ajax_get_traductions_filtered('<?=$comienzo?>', '<?=$limite?>', '<?=$pagina?>');">
                                    <option value=""><?=l('admin-traducciones-campo-idioma-todos');?></option>
                                    <?php
                                        foreach($idiomas as $key => $idioma){
                                            ?><option value="<?=$idioma->id?>"><?=$idioma->nombre?></option><?php
                                        }
                                    ?>
                                    </select>
                            </div>
                        </div>
                    </form>
                    
                    <div id="page-content"></div>
                    <script> ajax_get_traductions_filtered(<?=$comienzo;?>,<?=$limite;?>,<?= $pagina;?>); </script>
                    
                </div>
            </div>
        </div>
        

        <!-- MODAL PARA AÑADIR UNA TRADUCCION -->
        <div class="modal fade" id="addNewTranslation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><?=l('admin-traducciones-nuevo');?></h5>
                        <button type="button" id="closeModal" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form method="post" action="<?=_DOMINIO_._ADMIN_.'traduccion/new/';?>" name="form_new_traduction" id="form_new_traduction">
                            <input type="hidden" name="id" value="0" />
                            <input type="hidden" name="action" value="create" />
                            <input type="hidden" name="comienzo" value="<?=$comienzo?>" />
                            <input type="hidden" name="limite" value="<?=$limite?>" />
                            <input type="hidden" name="pagina" value="<?=$pagina?>" />

                            <div class="form-group">
                                <label class="mb-0"><?=l('admin-traducciones-campo-idioma');?></label>
                                <select class="form-control" name="id_idioma" style="height: 44.44px; border: 1px solid #ccc; pointer-events: none;" readonly>
                                    <?php
                                        foreach($idiomas as $key => $idioma){
                                            ?><option value="<?=$idioma->id?>" <?=$idioma->id == Configuracion::get('default_language') ? 'selected' : '';?>><?=$idioma->nombre?></option><?php
                                        }
                                    ?>
                                </select>
                                <small><?=l('admin-traducciones-campo-idioma-extra');?></small>
                            </div>

                            <div class="form-group">
                                <label class="mb-0" for="input_zona"><?=l('admin-traducciones-campo-zona');?></label>
                                <input type="text" name="zona" style="border: 1px solid #ccc;" value="" id="input_zona" class="form-control" placeholder="<?=l('admin-traducciones-campo-zona-placeholder');?>">
                            </div>

                            <div class="form-group">
                                <label class="mb-0" for="input_shortcode"><?=l('admin-traducciones-campo-shortcode');?></label>
                                <input type="text" name="shortcode" style="border: 1px solid #ccc;" value="" id="input_shortcode" class="form-control" placeholder="<?=l('admin-traducciones-campo-shortcode-placeholder');?>">
                            </div>

                            <div class="form-group">
                                <label class="mb-0" for="input_texto"><?=l('admin-traducciones-campo-texto');?></label>
                                <textarea name="texto" style="border: 1px solid #ccc;" id="input_texto" rows="5" class="form-control" placeholder="<?=l('admin-traducciones-campo-texto-placeholder');?>"></textarea>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-success" name="submitCrearTraduccion"><?=l('admin-crear');?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
