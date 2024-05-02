<?php include 'Account.php';?>

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
                            <th>Correo</th>
                            <th>Credencial</th>
                            <th>Cuenta asociada</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
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
                            <td><button type="button" class="btn-primary" name="btn-disableAccount">Desactivar</button> <button type="button" class="btn-primary" onclick="cargarFormularioEdicion()">Editar</button></td>

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

                    <div class="formEditar" style="display: none;">
                        <br>
                        <form>
                            <div class="form-group">
                                <label for="correo">Correo:</label>
                                <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo electrónico">
                            </div>
                            <div class="form-group">
                                <label for="credencial">Credencial:</label>
                                <input type="text" class="form-control" id="credencial" name="credencial" placeholder="Credencial">
                            </div>
                            <div class="form-group">
                                <label for="cuentaAsociada">Cuenta Asociada:</label>
                                <input type="text" class="form-control" id="cuentaAsociada" name="cuentaAsociada" placeholder="Cuenta Asociada">
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                    </div>
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
