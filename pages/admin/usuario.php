<?php
/**
 * @var $comienzo
 * @var $limite
 * @var $pagina
 */
?>
<div class="card card-solid">
    <div class="card-body pb-0" id="page-content">
        <div class="formEditar">
            <br>
            <form>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="<?=$usuario->email?>" placeholder="Correo electrónico">
                </div>
                <div class="form-group">
                    <label for="credencial">Credencial:</label>
                    <input type="text" class="form-control" id="credencial" name="credencial" placeholder="Credencial">
                </div>
                <div class="form-group">
                    <label for="cuentaAsociada">Cuenta Asociada:</label>
                    <input type="text" class="form-control" id="cuentaAsociada" name="cuentaAsociada" placeholder="Cuenta Asociada">
                </div>
                <button type="submit" name="submitUpdateUsuarioAdmin" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>