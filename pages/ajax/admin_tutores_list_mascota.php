<?php
/**
 * @var $total
 * @var $tutores
 * @var $idmascota
 */
$tutoresDisponibles = [];
foreach ($tutores as $key => $tutor) {
    if (isset($tutoresAsignados[$tutor->id])) {
        continue;
    }
    $tutoresDisponibles[] = $tutor;
}
$total = count($tutoresDisponibles);
if($total > 0)
{?>
        <div class="col-12">
            <?php
            foreach( $tutoresDisponibles as $key => $tutor ){
                ?>
                <div class="row border-bottom border-1 border-grey">
                    <div class="col-10 col-lg-11">
                        <div class="row">
                            <div class="col-12 col-sm-4">
                                <?=$tutor->nombre?>
                            </div>
                            <div class="col-12 col-sm-3">
                                <?=$tutor->telefono_1?>
                            </div>
                            <div class="col-12 col-sm-5">
                                <?=$tutor->email?>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 col-lg-1 btn btn-success d-flex align-items-center justify-content-center" data-idmascota="<?=$idmascota?>" data-idtutor="<?=$tutor->id?>" onclick="asignarMascota(this)">
                        <i class="fa fa-plus"></i>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

<?php
}
else
{?>
	<div class="alert alert-dark text-center">
		<p class="mb-0">No se han encontrado tutores</p>
	</div>
	<?php
}
?>