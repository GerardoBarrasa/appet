<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">
                <span class="text-capitalize">Traducción</span>
            </h4>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-20 px-3">
                <div class="card-body">
                    <form method="post" action="">
                        <div class="row">
                            <input type="hidden" name="id_traduccion" value="<?=(isset($traduccion->id_traduccion)) ? $traduccion->id_traduccion : '0'?>" />
                            <div class="col-4">
                                <!-- SHORTCODE -->
                                <div class="form-group">
                                    <label>Shortcode</label>
                                    <input type="text" name="shortcode" value="<?=(isset($traduccion->shortcode)) ? $traduccion->shortcode : ''?>" class="form-control" placeholder="Ejemplo: header-usuario-logout" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                            foreach( $idiomas as $idioma )
                            {
                                ?>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label><?=$idioma->nombre;?> <img src="<?=_DOMINIO_.$idioma->icon?>" width="25" /></label>
                                        <input type="text" name="texto[<?=$idioma->id;?>]" value="<?=(isset($traduccion->traducciones[$idioma->id])) ? $traduccion->traducciones[$idioma->id] : ''?>" class="form-control" placeholder="Especifica el texto para este idioma" />
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="col-12 mt-3 d-flex justify-content-end align-items-center">
                                <div class="justify-self-end">
                                    <button type="submit" name="submitUpdateTraduccion" class="btn btn-primary  waves-effect waves-light">Actualizar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
</div>
