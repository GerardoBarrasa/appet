<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="card card-solid">
        <div class="card-header">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-12 col-md-8 mb-2 mb-md-0">
                    <form id="formFiltrosAdmin">
                        <input class="form-control w-100 debouncefunc" name="busqueda" id="busqueda" type="text" placeholder="Busca por nombre, raza o por su tutor" data-function="ajax_get_mascotas_admin">
                    </form>
                </div>
                <div class="col-12 col-md-4 d-flex align-items-center justify-content-around justify-content-md-end mb-2 mb-md-0">
                    <a href="<?=_DOMINIO_.$_SESSION['admin_vars']['entorno']?>nueva-mascota/" class="btn btn-primary">Nueva mascota</a>
                </div>
            </div>
        </div>
        <div class="card-body pb-0">
            <div class="totalfound fs-5 text-right text-secondary w-100" ></div>
            <div class="row pt-2" id="page-content">

            </div>
            <script> ajax_get_mascotas_admin(); </script>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <nav class="paginador">

            </nav>
        </div>
        <!-- /.card-footer -->
    </div>
    <!-- /.card -->

</section>
<!-- /.content -->
