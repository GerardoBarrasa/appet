<div class="row">
<?php
if($total > 0)
{
    foreach($usuarios as $usuario){//vd($usuario);?>

        <!--USUARIO-->
        <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
            <div class="card bg-light d-flex flex-fill">
                <div class="card-header text-muted border-bottom-0">
                    <?=$usuario->TYPE?>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-7">
                            <h2 class="lead"><b><?=$usuario->nombre?></b></h2>
                            <p class="text-muted text-sm"><b>Cuenta: </b> <?=$usuario->ACNAME?> </p>
                            <ul class="ml-4 mb-0 fa-ul text-muted">
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> <?=$usuario->email?></li>
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Address: Demo Street 123, Demo City 04312, NJ</li>
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone #: + 800 - 12 12 23 52</li>
                            </ul>
                        </div>
                        <div class="col-5 text-center">
                            <img src="<?=_ASSETS_._ADMIN_;?>dist/img/user2-160x160.jpg" alt="user-avatar" class="img-circle img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-right">
                        <a href="#" class="btn btn-sm bg-teal">
                            <i class="fas fa-comments"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-primary">
                            <i class="fas fa-user"></i> View Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

<?php }
}
else
{?>
	<div class="alert alert-dark text-center">
		<p class="mb-0">No se han encontrado usuarios</p>
	</div>
	<?php
}
?>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="dataTables_info">
			<?=($total > 1 || $total == '0') ? $total.' usuarios encontrados' : '1 usuario encontrado'?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="dataTables_paginate paging_bootstrap">
			<?php Tools::getPaginador($pagina, $limite, 'Admin', 'getUsuariosWithFiltros', 'ajax_get_usuarios', '', '', 'end'); ?>
		</div>
	</div>
</div>