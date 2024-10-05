<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">
                <span class="text-capitalize"><?=l('admin-slug-title', array((!empty($datos) ? $datos->slug : '--')));?></span>
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
                                                    <label for="slug"><?=l('admin-slugs-campo-slug');?></label>
                                                    <input <?= !empty($datos) && $datos->slug == 'home' ? 'readonly' : '' ?> type="text" name="slug" id="slug" class="form-control" placeholder="<?=l('admin-slugs-campo-slug');?>" value="<?= !empty($datos) ? $datos->slug : '' ?>">
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <label for="page"><?=l('admin-slugs-campo-pagina');?></label>
                                                    <select name="page" id="page" class="form-control"> 
                                                        <option value=""><?=l('admin-slugs-elige-pagina');?></option>
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
                                                    <label for="id_language"><?=l('admin-slugs-campo-idioma');?></label>
                                                    <select name="id_language" id="id_language" class="form-control"> 
                                                        <option value=""><?=l('admin-slugs-elige-idioma');?></option>
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
                                                    <label for="title"><?=l('admin-slugs-campo-title');?></label>
                                                    <input type="text" name="title" id="title" class="form-control" placeholder="<?=l('admin-slugs-campo-title');?>" value="<?= !empty($datos) ? $datos->title : '' ?>">
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <label for="description"><?=l('admin-slugs-campo-description');?></label>
                                                    <input type="text" name="description" id="description" class="form-control" placeholder="<?=l('admin-slugs-campo-description');?>" value="<?= !empty($datos) ? $datos->description : '' ?>">
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-4 d-none">
                                                <div class="form-group">
                                                    <label for="keywords">Keywords</label>
                                                    <input type="text" name="keywords" id="keywords" class="form-control" placeholder="Keywords" value="">
                                                </div>
                                            </div>

                                            <div class="col-12 d-flex justify-content-end align-items-center">
                                                <div class="justify-self-end">
                                                    <?php if( !empty($datos) && isset($datos->id) ) : ?>
                                                        <button type="submit" name="submitUpdateSlug" class="btn btn-primary  waves-effect waves-light"><?=l('admin-actualizar');?></button>
                                                    <?php else: ?>
                                                        <button type="submit" name="submitCrearSlug" class="btn btn-primary  waves-effect waves-light"><?=l('admin-crear');?></button>
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

<script>
    $(document).ready(function() { 
        $("#configuracion").addClass("active");
        $("#configuracion").parent().addClass("active");
        $("#configuracion").attr("aria-expanded","true");
        $("#configuracion").parent().children('ul').addClass("in");
        $("#configuracion").parent().children('ul').attr("aria-expanded","true");
    });
</script>
