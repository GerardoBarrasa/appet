<?php
/**
 * @var $body
 * @var $mascota
 * @var $generos
 */
?>
<form name="mascota_editar_nombre" id="<?=$body?>" method="POST" action="">
    <input type="hidden" name="action" value="<?=$body?>">
    <input type="hidden" name="tipo" value="mascota">
    <input type="hidden" name="id" value="<?=$mascota->id?>">
    <div class="col">
        <div class="form-group row">
            <div class="col-12 col-sm-4">
                <label for="genero">Género<i class="text-danger">*</i></label>
            </div>
            <div class="col-12 col-sm-8">
                <!-- insertamos un select para el género de la mascota a partir de la variable $generos -->
                <select class="form-control" id="genero" name="genero" required>
                    <?php foreach ($generos as $genero): ?>
                        <option value="<?=$genero->id?>" <?=($mascota->genero == $genero->id) ? 'selected' : ''?>><?=$genero->nombre?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-12 col-sm-4">
                <label for="raza">Raza</label>
            </div>
            <div class="col-12 col-sm-8">
                <input type="text" class="form-control" id="raza" name="raza" value="<?=$mascota->raza?>" required>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-12 text-center">
                <div class="btn btn-outline-secondary" onclick="saveData('<?=$body?>')">Guardar datos</div>
            </div>
        </div>
    </div>
</form>