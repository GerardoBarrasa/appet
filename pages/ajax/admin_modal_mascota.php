<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><?=$titulo?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <?php if(empty($body)){
        echo "Aquí puedes editar los datos de la mascota.";
    }else{
        include(_INCLUDES_._ADMIN_.$body.'.php');
    } ?>
</div>
<div class="modal-footer">
    <div class="d-flex w-100 align-items-center justify-content-between">
        <a class="font-size-14" href="#">
            <i class="fa fa-envelope mr-1"></i>
            Para cualquier duda que te surja, contáctanos.
        </a>
    </div>

</div>
