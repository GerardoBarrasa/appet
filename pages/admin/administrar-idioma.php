<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">
                <span class="text-capitalize"><?=(!empty($datos_idioma) ? l('admin-idioma-title', array($datos_idioma->nombre)) : l('admin-idioma-title-nuevo'));?></span>
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
                            <form method="post" id="form_update_idioma" name="form_update_idioma" enctype="multipart/form-data">
                                <div class="row">

                                    <!-- Form principal -->
                                    <div class="col-12">
                                        <div class="row">
                                            <input type="hidden" name="id" value="<?=(isset($datos_idioma->id)) ? $datos_idioma->id : '0'?>" />

                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <!-- NOMBRE -->
                                                        <div class="form-group">
                                                            <label><?=l('admin-idiomas-campo-nombre');?></label>
                                                            <input type="text" name="nombre" value="<?=(isset($datos_idioma->nombre)) ? $datos_idioma->nombre : ''?>" class="form-control" placeholder="<?=l('admin-idiomas-campo-nombre-placeholder');?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-sm-6">
                                                        <!-- SLUG -->
                                                        <div class="form-group">
                                                            <label><?=l('admin-idiomas-campo-abreviatura');?></label>
                                                            <input type="text" name="slug" value="<?=(isset($datos_idioma->slug)) ? $datos_idioma->slug : ''?>" class="form-control" placeholder="<?=l('admin-idiomas-campo-abreviatura-placeholder');?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-sm-6">
                                                        <!-- COLOUR -->
                                                        <div class="form-group">
                                                            <label><?=l('admin-idiomas-campo-color');?></label>
                                                            <input type='text' class="form-control color-picker" name="colour" value="<?=(isset($datos_idioma->colour)) ? $datos_idioma->colour : ''?>" placeholder="<?=l('admin-idiomas-campo-color-placeholder');?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-sm-6">
                                                        <!-- VISIBLE -->
                                                        <div class="form-group">
                                                            <label>&nbsp;</label>
                                                            <div class="d-flex justify-content-start align-items-center">
                                                                <span class="order-1 order-sm-0"><?=l('admin-idiomas-campo-activo');?></span>
                                                                <input type="checkbox" name="visible" id="switchVisible" switch="bool" <?=(isset($datos_idioma->visible) && $datos_idioma->visible == '1') ? 'checked' : ''?> class="order-0 order-sm-1"/>
                                                                <label class="mb-0 ml-3" for="switchVisible" data-on-label="<?=l('admin-si');?>" data-off-label="<?=l('admin-no');?>"></label>
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
                                                            <label for="icon"><?=l('admin-idiomas-campo-icono');?> (128x128). <?=l('admin-idiomas-campo-icono-extra', array('<a href="https://www.iconfinder.com/icons/2634423/ensign_flag_nation_spain_icon" target="_blank">', '</a>'));?></label>
                                                            <input type="file" name="icon" id="icon" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-3 d-flex justify-content-end align-items-center">
                                        <div class="justify-self-end">
                                            <?php if( !empty($datos_idioma) && isset($datos_idioma->id) ) : ?>
                                                <button type="submit" name="submitUpdateIdioma" class="btn btn-primary  waves-effect waves-light"><?=l('admin-actualizar')?></button>
                                            <?php else: ?>
                                                <button type="submit" name="submitCrearIdioma" class="btn btn-primary  waves-effect waves-light"><?=l('admin-crear')?></button>
                                            <?php endif; ?>
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

<script>
    $(document).ready(function() { 
        $("#idiomas").addClass("active");
        $("#idiomas").parent().addClass("active");
        $("#idiomas").attr("aria-expanded","true");
        $("#idiomas").parent().children('ul').addClass("in");
        $("#idiomas").parent().children('ul').attr("aria-expanded","true");
    });
</script>
