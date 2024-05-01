<?php
/**
 * @var $comienzo
 * @var $limite
 * @var $pagina
 */
?>
<div class="card card-solid">
    <div class="card-body pb-0" id="page-content">

    </div>

    <!--Paginación-->
    <div class="card-footer">
        <nav aria-label="Contacts Page Navigation">
            <ul class="pagination justify-content-center m-0">
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">4</a></li>
                <li class="page-item"><a class="page-link" href="#">5</a></li>
                <li class="page-item"><a class="page-link" href="#">6</a></li>
                <li class="page-item"><a class="page-link" href="#">7</a></li>
                <li class="page-item"><a class="page-link" href="#">8</a></li>
            </ul>
        </nav>
    </div>

</div>

<script>
    $(document).ready(function(){
        ajax_get_usuarios(<?=$comienzo;?>,<?=$limite;?>,<?= $pagina;?>);

    });


    function cargarFormularioEdicion(id) {
        $.ajax({
            url: 'formEditar.php',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                $('formEditar').html(response);
                $('#formEditar').show();
            }
        });
    }

 </script>