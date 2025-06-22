<?php
/**
 * @var $total
 * @var $mascotas
 * @var $idtutor
 */
if ($total > 0) {
    foreach ($mascotas as $key => $mascota) {
        $image = file_exists(_RESOURCES_PATH_.'private/mascotas/'.$mascota->id.'/profile.jpg') ? _RESOURCES_.'private/mascotas/'.$mascota->id.'/profile.jpg' : _RESOURCES_ . _COMMON_ .'img/petType_'.$mascota->tipo.'_default.png';
        ?>

        <div class="col-12 m-0 p-0 d-flex align-items-stretch flex-column">
            <div class="card bg-white d-flex flex-fill mb-1">
                <div class="card-body pl-1">
                    <div class="d-flex flex-row align-items-center justify-content-between">
                        <div class="col-4 col-sm-1 p-0 text-center text-sm-left">
                            <div class="text-center btn btn-secondary p-4 p-sm-2" data-idmascota="<?= $mascota->id ?>" data-idtutor="<?= $idtutor ?>" onclick="asignarMascota(this)">
                                <i class="fa fa-plus text-white"></i>
                            </div>
                        </div>
                        <div class="col-8 col-sm-11 d-flex flex-wrap align-items-center justify-content-between">
                            <div class="col-sm-2 text-center">
                                <img src="<?= $image?>" alt="dog-avatar" class="img-circle img-fluid">
                            </div>
                            <div class="col-sm--5 col-sm-3 h4 text-center text-sm-left">
                                <?=$mascota->nombre?>
                            </div>
                            <div class="col-sm-7 col-sm-7 text-center text-sm-left">
                                <?=$mascota->GENERO?> <?=$mascota->raza?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php
    }
} else {
    ?>
    <div class="alert alert-dark text-center">
        <p class="mb-0">No se han encontrado mascotas</p>
    </div>
    <?php
}
?>
