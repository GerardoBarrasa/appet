<div class="row">


        <!--USUARIO-->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Usuarios</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th class="text-center">Correo</th>
                            <th class="text-center">Credencial</th>
                            <th class="text-center">Cuenta asociada</th>
                            <th class="text-center">Fecha Creación</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if($total > 0)
                        {
                        foreach($usuarios as $usuario){//vd($usuario);?>
                        <tr >
                            <td><?=$usuario->email?></td>
                            <td><?=$usuario->CREDENCIAL?></td>
                            <td><?=$usuario->ACNAME?></td>
                            <td><?=$usuario->DATE_CREATED?></td>
                            <td class="text-center">
                                <a class="btn btn-<?=$usuario->estado=='1'?'success':'danger'?><?= $_SESSION['admin_panel']->id != $usuario->id ?'' : ' disabled'?>" title="<?=$usuario->estado=='0'?'Desactivar':'Activar'?>" <?= $_SESSION['admin_panel']->id != $usuario->id ?' onclick="ajax_updateUserField(\''.$usuario->id.'\',\'estado\',\''.$usuario->estado.'\')"' : ''?>>
                                    <i class="fa fa-<?=$usuario->estado=='1'?'check':'times'?>"></i>
                                </a>
                                <a href="<?=_DOMINIO_ . _ADMIN_?>usuario/<?=$usuario->id?>/" class="btn btn-primary" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>

                        </tr>

                        <?php }
                        }
                        else
                        {?>
                            <tr>
                                <td colspan="3">
                                    No se han encontrado usuarios
                                </td>
                            </tr>
                            <?php
                        }
                        ?>

                        </tbody>
                    </table>

            <!-- /.card -->
        </div>




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
