<?php
/**
 * @var $mascota
 */
?>
<form name="mascota_editar_nombre" id="mascota_editar_nombre" method="POST" action="">
    <input type="hidden" name="action" value="mascota_editar_nombre">
    <input type="hidden" name="tipo" value="mascota">
    <input type="hidden" name="id" value="<?=$mascota->id?>">
<div class="col">
    <div class="form-group row">
        <div class="col-12 col-sm-4">
            <label for="nombre">Nombre<i class="text-danger">*</i></label>
        </div>
        <div class="col-12 col-sm-8">
            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Introduce el nombre de la mascota" value="<?=$mascota->nombre?>" required>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12 col-sm-4">
            <label for="alias">Alias</label>
        </div>
        <div class="col-12 col-sm-8">
            <input type="text" class="form-control" id="alias" name="alias" placeholder="Introduce un nombre alternativo para la mascota" value="<?=$mascota->alias?>">
            <span class="small">Es para diferenciarlo de otras con el mismo nombre, por ejemplo "Nala (Pepe)"</span>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12 text-center">
            <div class="btn btn-outline-secondary" onclick="saveData('mascota_editar_nombre')">Guardar datos</div>
        </div>
    </div>
</div>
</form>