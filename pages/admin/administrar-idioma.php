<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">
                <span class="text-capitalize">Idioma</span>
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
                            <h1 class="h2 mt-0 mb-3">
                                Idioma <span class="text-primary"><small><?=!empty($datos_idioma) ? $datos_idioma->id : ''?></small></span> 
                            </h1>

                            <form method="post" action="<?=_DOMINIO_._ADMIN_?>administrar-idioma/<?=$id?>/" id="form_update_idioma" name="form_update_idioma" enctype="multipart/form-data">
                                <div class="row">

                                    <!-- Form principal -->
                                    <div class="col-12">
                                        <div class="row">
                                            <input type="hidden" name="id" value="<?=(isset($datos_idioma->id)) ? $datos_idioma->id : '0'?>" />
                                            <input type="hidden" name="action" value="<?=(isset($id) &&  $id != '0') ? 'update' : 'create'?>" />

                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <!-- NOMBRE -->
                                                        <div class="form-group">
                                                            <label>Nombre interno</label>
                                                            <input type="text" name="nombre" value="<?=(isset($datos_idioma->nombre)) ? $datos_idioma->nombre : ''?>" class="form-control" placeholder="Nombre interno del idioma" />
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-sm-6">
                                                        <!-- SLUG -->
                                                        <div class="form-group">
                                                            <label>Abreviatura</label>
                                                            <input type="text" name="slug" value="<?=(isset($datos_idioma->slug)) ? $datos_idioma->slug : ''?>" class="form-control" placeholder="Ejemplo en inglés: en" />
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-sm-6">
                                                        <!-- COLOUR -->
                                                        <div class="form-group">
                                                            <label>Color</label>
                                                            <input type='text' class="form-control color-picker" name="colour" value="<?=(isset($datos_idioma->colour)) ? $datos_idioma->colour : ''?>" placeholder="A priori esto es para uso interno, usa alguno de los arriba indicados." />
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-sm-6">
                                                        <!-- VISIBLE -->
                                                        <div class="form-group">
                                                            <label>&nbsp;</label>
                                                            <div class="d-flex justify-content-start align-items-center">
                                                                <span class="order-1 order-sm-0">&nbsp;&nbsp;Visible en web</span>
                                                                <input type="checkbox" name="visible" id="switchVisible" switch="bool" <?=(isset($datos_idioma->visible) && $datos_idioma->visible == '1') ? 'checked' : ''?> class="order-0 order-sm-1"/>
                                                                <label class="mb-0 ml-3" for="switchVisible" data-on-label="Si" data-off-label="No"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-5">
                                                <div class="row">
                                                    <div class="col-12 d-flex flex-column justify-content-center align-items-center">
                                                        <?php
                                                        if(isset($datos_idioma->icon) && $datos_idioma->icon != ''){
                                                        ?>
                                                            <div class="d-flex justify-content-center align-items-center">
                                                                <div class="ficha-wrapper  d-flex justify-content-center align-items-center">
                                                                    <img src="<?=_DOMINIO_.$datos_idioma->icon?>" class="img-fluid">
                                                                </div>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                        <div class="form-group mt-2 w-100">
                                                            <label for="icon">Icono / Bandera (128x128). Búscala <a href="https://www.iconfinder.com/icons/2634423/ensign_flag_nation_spain_icon" target="_blank">aquí</a>, abajo aparecerán banderas relacionadas con el mismo diseño, descárgala a 128px.</label>
                                                            <input type="file" name="icon" id="icon" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-3 d-flex justify-content-end align-items-center">
                                        <div class="justify-self-end">
                                            <button type="submit" name="btn-update" class="btn btn-primary  waves-effect waves-light"><?=(isset($id) && $id != '0') ? 'Actualizar' : 'Crear'?></button>
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
