<?php
/**
 * @var $total
 * @var $mascotas
 */
if ($total > 0) {
    foreach ($mascotas as $key => $mascota) {
        $tutores = Tutores::getTutoresByMascota($mascota->id);
        $reservas = [];
        $image = file_exists(_RESOURCES_PATH_.'private/mascotas/'.$mascota->id.'/profile.jpg') ? _RESOURCES_.'private/mascotas/'.$mascota->id.'/profile.jpg' : _RESOURCES_ . _COMMON_ .'img/petType_'.$mascota->tipo.'_default.png';
        ?>

        <div class="col-12 col-md-6 col-xl-4 d-flex align-items-stretch flex-column">
            <div class="card bg-light d-flex flex-fill">
                <div class="card-header h2 border-bottom-0">
                    <?=$mascota->nombre?>
                    <?=$mascota->alias != '' ? '<span class="text-muted fs-6">('.$mascota->alias.')</span>' : ''?>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-12 col-sm-7">
                            <?php if(!empty($tutores)){
                                foreach($tutores as $tutor){?>
                                        <div class="d-flex flex-row align-items-center justify-content-between">
                                            <div class="fs-5"><?=$tutor->nombre?></div>
                                            <a href="tel:<?=$tutor->telefono_1?>" class="text-secondary">
                                                <i class="fa fa-phone"></i>
                                                <?=$tutor->telefono_1?>
                                            </a>
                                        </div>
                            <?php }
                            } else{?>
                                <div class="fs-6 text-muted">Sin tutores asignados</div>
                            <?php }?>
                            <?php if(!empty($reservas)){
                                foreach($reservas as $k=>$reserva){
                                    if($k == 2){// Mostramos las 2 próximas reservas
                                        break;
                                    }?>
                                        <div class="d-flex flex-row align-items-center justify-content-between">
                                            <div class="fs-5">Del <?=$reserva->fecha_inicio?> <?=$reserva->hora_inicio?></div>
                                            <div class="fs-5">al <?=$reserva->fecha_fin?> <?=$reserva->hora_fin?></div>
                                            <a href="#" class="text-secondary">
                                                <i class="fa fa-calendar-days"></i>
                                            </a>
                                        </div>
                            <?php }
                            } else{?>
                                <div class="fs-6 text-muted">
                                    <i class="fa fa-calendar-days mr-2"></i>
                                    No tiene reservas próximas
                                </div>
                            <?php }?>
                            <ul class="ml-4 mb-0 fa-ul">
                                <li class="small mb-2"><span class="fa-li"><i class="fas fa-lg fa-weight-hanging"></i></span>
                                    Peso: <?=$mascota->peso?>Kg
                                </li>
                                <li class="small mb-2"><span class="fa-li"><i class="fas fa-lg fa-syringe"></i></span>
                                    Castrado: <?=$mascota->esterilizado==1?'Sí':'No'?>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-sm-5 text-center">
                            <img src="<?= $image?>" alt="dog-avatar" class="img-circle img-fluid">
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
                                <i class="fas fa-magnifying-glass-plus"></i>
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
