<?php
/**
 * @var $mascotasAsignadas
 * @var $idtutor
 */
if(!empty($mascotasAsignadas)){
    foreach($mascotasAsignadas as $mascota){
        $image = file_exists(_RESOURCES_PATH_.'private/mascotas/'.$mascota->id.'/profile.jpg') ? _RESOURCES_.'private/mascotas/'.$mascota->id.'/profile.jpg' : _RESOURCES_ . _COMMON_ .'img/petType_'.$mascota->tipo.'_default.png';?>
        <div class="col-12 m-0 p-0 d-flex align-items-stretch flex-column">
            <div class="bg-white d-flex flex-row mb-1">
                <div class="d-flex flex-wrap align-items-center justify-content-between p-2">
                    <div class="col-12 col-sm-3 d-sm-none h4 mb-0 text-center text-sm-left">
                        <?=$mascota->nombre?>
                    </div>
                    <div class="col-12 col-sm-2 d-flex align-items-center justify-content-center">
                        <div class="col-6 col-sm-12 text-center">
                            <img src="<?= $image?>" alt="dog-avatar" class="img-circle img-fluid">
                        </div>
                    </div>
                    <div class="col-12 col-sm-3 d-none d-sm-flex h4 mb-0 text-center text-sm-left">
                        <?=$mascota->nombre?>
                    </div>
                    <div class="col-12 col-sm-7 text-center text-sm-left">
                        <?=Tools::getGeneroNombre($mascota->genero)?> <?=$mascota->raza?>
                    </div>
                </div>
                <div class="btn btn-secondary col-2 col-xl-1 d-flex flex-row align-items-center justify-content-center" data-idmascota="<?= $mascota->id ?>" data-idtutor="<?= $idtutor ?>" data-action="remove" onclick="asignarMascota(this)">
                    <i class="fa fa-minus text-white"></i>
                </div>
            </div>
        </div>
    <?php }
} else{?>
    <div class="alert alert-warning" role="alert">
        Este tutor todavía no tiene mascotas asignadas.
    </div>
<?php }?>