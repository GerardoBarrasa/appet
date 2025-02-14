<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="card card-solid">
        <div class="card-header">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-12 col-md-3 mb-2 mb-md-0 card-title">Gestiona las mascotas</div>
                <div class="col-12 col-md-7 mb-2 mb-md-0">
                    <form id="formFiltrosAdmin">
                        <input class="form-control w-100" name="busqueda" id="busqueda" type="text" placeholder="Busca por nombre, raza o por su tutor" data-function="ajax_get_mascotas_admin">
                    </form>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-center justify-content-around justify-content-md-end mb-2 mb-md-0">
                    <a href="<?=_DOMINIO_.$_SESSION['admin_vars']['entorno']?>nueva-mascota/" class="btn btn-primary">Nueva mascota</a>
                </div>
            </div>
        </div>
        <div class="card-body pb-0">
            <div class="row" id="page-content">

            </div>
            <script> ajax_get_mascotas_admin('<?=$comienzo;?>','<?=$limite;?>','<?= $pagina;?>'); </script>
        </div>
        <!-- /.card-body -->
        <!--<div class="card-footer">
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
        </div>-->
        <!-- /.card-footer -->
    </div>
    <!-- /.card -->

</section>
<!-- /.content -->