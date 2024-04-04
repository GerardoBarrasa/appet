<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">
                <span class="text-capitalize">Administra traducción</span>
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
                            <ul class="nav nav-tabs" role="tablist">
                                <?php
                                    $cont = 0;
                                    foreach($datos_idiomas as $key => $idioma){
                                        ?>
                                        <li class="nav-item">
                                            <a class="nav-link <?=($cont == 0) ? 'active' : ''?>" data-toggle="tab" href="#lang-<?=$idioma->slug?>" role="tab">
                                                <img src="<?=_DOMINIO_.$idioma->icon?>" width="22" />&nbsp;&nbsp;<?=$idioma->nombre?>
                                            </a>
                                        </li>
                                        <?php
                                        $cont++;
                                    }
                                ?>
                            </ul>
                            <div class="tab-content mt-3">
                                <?php
                                    $contLang = 0;
                                    foreach($datos_idiomas as $key => $idioma){
                                        ?>
                                        <div class="tab-pane <?=($contLang == 0) ? 'show active' : ''?>" id="lang-<?=$idioma->slug?>" role="tabpanel">
                                            
                                            <form method="post" action="" id="form_<?=$idioma->slug?>" name="form_<?=$idioma->slug?>">

                                                <input type="hidden" name="id" value="<?=(isset($datos_traducciones[$idioma->slug]->id)) ? $datos_traducciones[$idioma->slug]->id : '0'?>" />
                                                <input type="hidden" name="id_idioma" value="<?=$idioma->id?>" />
                                                <input type="hidden" name="traduction_for" value="<?=(isset($datos_traducciones['es']->traduction_for)) ? $datos_traducciones['es']->traduction_for : ''?>" />
                                                <input type="hidden" name="shortcode" value="<?=(isset($datos_traducciones['es']->shortcode)) ? $datos_traducciones['es']->shortcode : ''?>" />
                                                <input type="hidden" name="action" value="<?=(isset($datos_traducciones[$idioma->slug]->id)) ? 'update' : 'create'?>" />

                                                <div class="form-group">
                                                    <textarea name="contenido" id="contenido_<?=$idioma->slug?>" class="form-control tinyMce"><?=(isset($datos_traducciones[$idioma->slug]->contenido)) ? $datos_traducciones[$idioma->slug]->contenido : ''?></textarea>
                                                </div>

                                                <div class="clearfix"></div>

                                                <div class="row">
                                                    <div class="col-12 text-right">
                                                        <a href="javascript:void(0);" onclick="ajax_update_traduction('form_<?=$idioma->slug?>', '<?=$idioma->slug?>');" class="btn btn-<?=(isset($datos_traducciones[$idioma->slug]->id)) ? 'info' : 'success'?> btn-fw btn-icon-text"><?=(isset($datos_traducciones[$idioma->slug]->id)) ? 'Actualizar' : 'Crear'?></a>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>
                                        <?php
                                        $contLang++;
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
</div>
