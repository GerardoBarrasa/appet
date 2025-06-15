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
            <label for="nombre">Peso<i class="text-danger">*</i></label>
        </div>
        <div class="col-12 col-sm-8 d-flex align-items-center justify-content-between">
            <input type="number" step="1" min="0" max="200" class="form-control text-right" id="peso" name="peso" value="<?=$mascota->peso?>" required>Kg
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12 text-center">
            <div class="btn btn-outline-secondary" onclick="saveData('<?=$body?>')">Guardar datos</div>
        </div>
    </div>
</div>
</form>