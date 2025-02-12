<?php
if ($total > 0) {
    foreach ($mascotas as $key => $mascota) {
        $image = file_exists(_RESOURCES_.'private/mascotas/'.$mascota->id.'/'.$mascota->slug.'_'.$mascota->id.'.jpg') ? _RESOURCES_.'private/mascotas/'.$mascota->id.'/'.$mascota->slug.'_'.$mascota->id.'jpg' : _RESOURCES_ . _COMMON_ .'img/petType_'.$mascota->tipo.'_default.png';
        ?>

        <div class="col-12 col-md-6 col-xl-4 d-flex align-items-stretch flex-column">
            <div class="card bg-light d-flex flex-fill">
                <div class="card-header text-muted border-bottom-0">
                    <?=$mascota->TIPO?> <?=$mascota->GENERO?>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-12 col-sm-7">
                            <h2 class="lead text-center text-sm-left"><b><?=$mascota->nombre?></b></h2>
                            <p class="text-muted text-sm"><b>Raza: </b> <?=$mascota->raza?> </p>
                            <ul class="ml-4 mb-0 fa-ul text-muted">
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-weight-hanging"></i></span>
                                    Peso: <?=$mascota->peso?>Kg
                                </li>
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-syringe"></i></span>
                                    Castrado: <?=$mascota->esterilizado==1?'Sí':'No'?>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-sm-5 text-center">
                            <img src="<?= _RESOURCES_.'private/mascotas/'.$mascota->id.'/'.$mascota->slug.'_'.$mascota->id.'.jpg'?>" width="150" alt="dog-avatar" class="img-circle img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="text-center text-sm-left col-12 col-sm-6 mb-2">
                            <a href="#" class="btn btn-sm bg-secondary" title="Nueva reserva">
                                <i class="fas fa-calendar-days"></i> Nueva reserva
                            </a>
                        </div>
                        <div class="text-center text-sm-right col-12 col-sm-6 mb-2">
                            <a href="#" class="btn btn-sm bg-secondary" title="Dejar un comentario">
                                <i class="fas fa-comments"></i>
                            </a>
                            <a href="<?=_DOMINIO_.$_SESSION['admin_vars']['entorno']?>mascota/<?=$mascota->slug.'-'.$mascota->id?>/" class="btn btn-sm btn-primary">
                                <i class="fas fa-square-up-right"></i> Ver la ficha
                            </a>
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