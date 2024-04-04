<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="page-title">Traducciones</h4>
                </div>
                <div class="col-md-6 text-right">
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#addNewTranslation" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i> Crear traducción</a>
                    <a href="<?=_DOMINIO_._ADMIN_?>idiomas/" class="btn btn-primary waves-effect waves-light"> <i class="ion-ios7-world-outline"></i> Gestionar idiomas</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ESTADISTICAS DE TRADUCCION -->
<div class="page-content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <?php
                    if(count($datos_idiomas) > 0){
                        foreach($datos_idiomas as $key => $idioma){

                            if( $idioma->totalTraductionsDone > '0' )
                                $traductionPercent = round(($idioma->totalTraductionsDone * 100) / $idioma->totalTraductions);
                            else
                                $traductionPercent = '0';

                            ?>
                            <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title"><img src="<?=_DOMINIO_.$idioma->icon?>" width="20" />&nbsp;&nbsp;<?=$idioma->nombre?></h4>
                                        <div class="d-flex justify-content-between">
                                            <p class="text-muted">Traducción al <?=$traductionPercent?>%</p>
                                            <p class="text-muted">100%</p>
                                        </div>
                                        <div class="progress progress-md">
                                            <div class="progress-bar" style="background: <?=$idioma->colour?>; width: <?=$traductionPercent;?>%;" role="progressbar" aria-valuenow="<?=$traductionPercent?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
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
                                <label for="busqueda">Búsqueda</label>
                                <input type="text" name="busqueda" value="" onkeyup="ajax_get_traductions_filtered('<?=$comienzo?>', '<?=$limite?>', '<?=$pagina?>');" placeholder="Busca un contenido..." class="form-control" />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="form-group">
                                <label for="busqueda">Zona</label>
                                <select name="traduction_for" class="form-control" onchange="ajax_get_traductions_filtered('<?=$comienzo?>', '<?=$limite?>', '<?=$pagina?>');">
                                    <option value="all">Todas las zonas</option>
                                    <?php
                                        foreach($datos_traductionFor as $key => $traduction){

                                            if($traduction->traduction_for == '')
                                                $name = "Home";
                                            else
                                                $name = ucwords(str_replace(array("-", "_"), " " ,$traduction->traduction_for));

                                            ?><option value="<?=$traduction->traduction_for?>"><?=$name?></option><?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                       <div class="col-12 col-sm-6 col-md-3">
                            <label for="busqueda">Estado</label>
                            <select name="translation_status" class="form-control" onchange="ajax_get_traductions_filtered('<?=$comienzo?>', '<?=$limite?>', '<?=$pagina?>');">
                                <option value="0">Todas las traducciones</option>
                                <option value="1">Traducciones pendientes</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="busqueda">Idioma</label>
                            <select name="slug_idioma" class="form-control" onchange="ajax_get_traductions_filtered('<?=$comienzo?>', '<?=$limite?>', '<?=$pagina?>');">
                                <option value="">Todos</option>
                                <?php
                                    foreach($datos_idiomas as $key => $idioma){
                                        ?><option value="<?=$idioma->slug?>" <?=(!empty($slug_idioma) && $slug_idioma == $idioma->id) ? 'selected' : '';?>><?=$idioma->nombre?></option><?php
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
                    <h5 class="modal-title" id="exampleModalLongTitle">Nueva traducción</h5>
                    <button type="button" id="closeModal" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form method="post" action="" name="form_new_traduction" id="form_new_traduction">
                        <input type="hidden" name="id" value="0" />
                        <input type="hidden" name="action" value="create" />
                        <input type="hidden" name="comienzo" value="<?=$comienzo?>" />
                        <input type="hidden" name="limite" value="<?=$limite?>" />
                        <input type="hidden" name="pagina" value="<?=$pagina?>" />

                        <div class="form-group">
                            <label class="mb-0">Idioma de la traducción</label>
                            <select class="form-control" name="id_idioma" style="height: 44.44px; border: 1px solid #ccc; pointer-events: none;" readonly>
                                <?php
                                    foreach($datos_idiomas as $key => $idioma){
                                        ?><option value="<?=$idioma->id?>" <?=$idioma->slug == _DEFAULT_LANGUAGE_ ? 'selected' : '';?>><?=$idioma->nombre?></option><?php
                                    }
                                ?>
                            </select>
                            <small>Creación de la traducción en el idioma por defecto, podrás añadir el resto de traducciones de esta cadena desde el listado</small>
                        </div>

                        <div class="form-group">
                            <label class="mb-0">Traducción para</label>
                            <input type="text" name="traduction_for" style="border: 1px solid #ccc;" id="input_traduction_for" value="" class="form-control" placeholder="Ejemplo: traducciones">
                        </div>

                        <div class="form-group">
                            <label class="mb-0">Shortcode</label>
                            <input type="text" name="shortcode" style="border: 1px solid #ccc;" value="" id="input_shortcode" class="form-control" placeholder="Ejemplo: btn-crear-traduccion">
                        </div>

                        <div class="form-group">
                            <label class="mb-0">Traducción</label>
                            <textarea name="contenido" style="border: 1px solid #ccc;" id="input_contenido" rows="5" class="form-control" placeholder="Especifica la traducción para este idioma"></textarea>
                        </div>

                    </form>

                    <div class="text-right">
                        <button type="button" class="btn btn-success" onclick="ajax_update_traduction('form_new_traduction', '', true);">Crear traducción</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
