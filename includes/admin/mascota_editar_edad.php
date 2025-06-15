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
    <span>Puedes indicar su</span>
    <div class="form-group row">
        <div class="col-12 col-sm-4">
            <label for="nacimiento_fecha">Fecha de nacimiento<i class="text-danger">*</i></label>
        </div>
        <div class="col-12 col-sm-8">
            <input type="date" class="form-control" id="nacimiento_fecha" name="nacimiento_fecha" value="<?=$mascota->nacimiento_fecha?>" required>
        </div>
    </div>
    <span>O puedes decir la edad que tenía en una fecha determinada (por defecto a día de hoy)</span>
    <div class="form-group row">
        <div class="col-12 col-sm-6">
            <div class="col-12 col-sm-4">
                <label for="edad">Edad</label>
            </div>
            <div class="col-12 col-sm-8 d-flex align-items-center">
                <input type="number" step="1" min="1" max="50" class="form-control" id="edad" name="edad" value="<?=$mascota->edad ?? 0?>"> años
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="col-12 col-sm-4">
                <label for="edad_fecha">el</label>
            </div>
            <div class="col-12 col-sm-8 d-flex align-items-center">
                <input type="date" class="form-control" id="edad_fecha" name="edad_fecha" value="<?=$mascota->edad_fecha?>">
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