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
            <div class="col-12">
                <input class="form-control w-100 debouncefuncAlt" name="busqueda" id="busqueda" type="text" autocomplete="off" placeholder="Busca por nombre, e-mail o mascota" data-function="ajax_get_tutores" data-listado="admin_tutores_list_mascota" data-ifempty="empty" data-idmascota="<?=$mascota->id?>">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-12 text-center">
                <div class="btn btn-outline-secondary" onclick="saveData('<?=$body?>')">Guardar datos</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div id="page-content">

        </div>
    </div>
</form>
<script>
    $(".debouncefuncAlt").on('keyup', $.debounce(750, function(e) {
        var functionName = $(this).data('function');

        if (typeof window[functionName] === 'function') {
            window[functionName](); // Ejecuta la función
        }
    }));
</script>