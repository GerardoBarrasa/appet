<?php
/**
 * @var $body
 * @var $mascota
 */
?>
<form name="mascota_editar_nombre" id="<?=$body?>" method="POST" action="">
    <input type="hidden" name="action" value="<?=$body?>">
    <input type="hidden" name="tipo" value="mascota">
    <input type="hidden" name="id" value="<?=$mascota->id?>">
<div class="col">
    <div class="form-group row">
        <div class="col-12 col-sm-4">
            <label for="nombre">¿Está esterilizada/o?<i class="text-danger">*</i></label>
        </div>
        <div class="col-12 col-sm-8 d-flex align-items-center justify-content-between">
            <!-- vamos a habilitar dos botones de radio para los posibles valores sí y no -->
            <div class="col-6 d-flex align-items-center justify-content-center">
                <input class="m-0 p-0 mr-2 clickable" type="radio" name="esterilizado" id="esterilizado_si" value="1" <?=($mascota->esterilizado == 1) ? 'checked' : ''?>>
                <label class="m-0 p-0 clickable" for="esterilizado_si">Sí</label>
            </div>

            <div class="col-6 d-flex align-items-center justify-content-center">
                <input class="m-0 p-0 mr-2 clickable" type="radio" name="esterilizado" id="esterilizado_no" value="0" <?=($mascota->esterilizado == 0) ? 'checked' : ''?>>
                <label class="m-0 p-0 clickable" for="esterilizado_no">No</label>
            </div>


        </div>
    </div>
    <div class="form-group row">
        <div class="col-12 text-center">
            <div class="btn btn-outline-secondary" onclick="saveData('<?=$body?>')">Guardar datos</div>
        </div>
    </div>
</div>
</form>