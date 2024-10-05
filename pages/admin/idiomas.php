<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="page-title"><?=l('admin-idiomas-title');?></h4>
                </div>
                <div class="col-md-6 text-right">
                    <a href="<?=_DOMINIO_._ADMIN_?>administrar-idioma/new/" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i> <?=l('admin-idiomas-nuevo');?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card m-b-20">
            <div class="card-body">

                <form id="formFiltrosAdmin">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3 offset-sm-6 offset-md-9">
                            <div class="form-group">
                                
                            </div>
                        </div>
                    </div>
                </form>
                
                <div id="page-content"></div>
                <script> ajax_get_idiomas_admin(<?=$comienzo;?>,<?=$limite;?>,<?= $pagina;?>); </script>
                
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
