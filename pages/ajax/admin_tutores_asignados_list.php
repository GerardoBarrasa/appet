<?php
/**
 * @var $idmascota
 */
if(empty($tutoresAsignados)){
    echo "No hay tutores asignados";
} else{
    foreach ($tutoresAsignados as $tutor){?>
        <div class="row mb-1">
            <a href="<?= _DOMINIO_ . $_SESSION['admin_vars']['entorno'] ?>tutor/<?=$tutor->slug?>-<?=$tutor->id?>/" class="col-10 col-sm-11 text-black">
                <div class="d-flex flex-row flex-wrap align-items-center justify-content-between">
                    <div class="col-12 col-md-4">
                        <span class="fs-5 font-weight-bold"><?=$tutor->nombre?></span>
                    </div>
                    <div class="col-9 col-md-auto">
                        <i class="fa fa-phone"></i> <?=$tutor->telefono_1?>
                    </div>

                    <div class="col-3 col-md-auto">
                        <?php if(!empty($tutor->email)){
                            ?>
                            <a href="mailto:<?=$tutor->email?>" class="text-secondary">
                                <i class="fa fa-envelope"></i>
                            </a>
                        <?php }?>
                    </div>

                </div>
            </a>
            <div class="col-2 col-sm-1 m-0 p-0 btn btn-secondary d-flex align-items-center justify-content-center" data-idmascota="<?=$idmascota?>" data-idtutor="<?=$tutor->id?>" data-action="remove" onclick="asignarMascota(this)">
                <i class="fa fa-minus"></i>
            </div>
        </div>

    <?php }
}
?>