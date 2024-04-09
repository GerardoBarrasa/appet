<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">
                <span class="text-capitalize">Actualizando slug</span>
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
                            <?php if(!empty($msg_success)){ ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>
                                    <?= $msg_success;?>
                                </strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php  } ?>
                            <form method="post" id="formSlug">
                                <div class="row mt-3">
                                    <!-- Form principal -->
                                    <div class="col-12">

                                        <div class="row">
                                            <input type="hidden" name="id" id="id" value="<?= !empty($datos) ? $datos->id : '' ?>">
                                            <input type="hidden" name="status" id="status" value="<?= !empty($datos) ? $datos->status : 'active' ?>">
                                            <input type="hidden" name="visible" id="visible" value="<?= !empty($datos) ? $datos->visible : '1' ?>">

                                            <div class="col-12 col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <label for="slug">Slug</label>
                                                    <input <?= !empty($datos) && $datos->slug == 'home' ? 'readonly' : '' ?> type="text" name="slug" id="slug" class="form-control" placeholder="Slug" value="<?= !empty($datos) ? $datos->slug : '' ?>">
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <label for="page">Página a la que pertenece el slug</label>
                                                    <select name="page" id="page" class="form-control"> 
                                                        <option value="">Selecciona página</option>
                                                        <?php
                                                            foreach($slugsPages as $key => $page){

                                                                $name = str_replace("-", " ", $page->mod_id);
                                                                $name = ucfirst($name);

                                                                ?><option value="<?=$page->mod_id?>" <?=(isset($datos->mod_id) && $datos->mod_id == $page->mod_id) ? 'selected': ''?>><?=$name?></option><?php
                                                            }
                                                        ?>
                                                    </select>                                                    
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <label for="id_language">Idioma del slug</label>
                                                    <select name="id_language" id="id_language" class="form-control"> 
                                                        <option value="">Selecciona idioma</option>
                                                        <?php
                                                            foreach($languages as $key => $lang){
                                                                ?><option value="<?=$lang->id?>" <?=(isset($datos->id_language) && $datos->id_language == $lang->id) ? 'selected': ''?>><?=$lang->nombre?></option><?php
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <label for="title">Metatitle</label>
                                                    <input type="text" name="title" id="title" class="form-control" placeholder="Metatitle" value="<?= !empty($datos) ? $datos->title : '' ?>">
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <label for="description">Metadescription</label>
                                                    <input type="text" name="description" id="description" class="form-control" placeholder="Metadescription" value="<?= !empty($datos) ? $datos->description : '' ?>">
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <label for="keywords">Keywords</label>
                                                    <input type="text" name="keywords" id="keywords" class="form-control" placeholder="Keywords" value="<?= !empty($datos) ? $datos->keywords : '' ?>">
                                                </div>
                                            </div>

                                            <div class="col-12 d-flex justify-content-end align-items-center">
                                                <div class="justify-self-end">
                                                    <?php if( !empty($datos) && isset($datos->id) ) : ?>
                                                        <button type="submit" name="submitUpdateSlug" class="btn btn-primary  waves-effect waves-light">Actualizar</button>
                                                    <?php else: ?>
                                                        <button type="submit" name="submitCrearSlug" class="btn btn-primary  waves-effect waves-light">Crear</button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
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
