<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<h4 class="page-title">
                <span class="text-capitalize"><?=(!empty($usuario) ? l('admin-usuario-admin-title', array($usuario->nombre)) : l('admin-usuario-admin-title-nuevo'));?></span>
            </h4>
		</div>
	</div>
</div>

<div class="page-content-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<div class="card m-b-20 px-3">
				<div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <p class="mb-3"><span class="bold text-primary"><?=l('admin-usuario-admin-fecha-registro');?>&nbsp;</span><?=!empty($usuario) ? Tools::fechaConHora($usuario->date_created) : '';?></p>

                            <form method="post">
                                <div class="row">

                                    <!-- Form principal -->
                                    <div class="col-12">
                                        <div class="row">
                                        <input type="hidden" name="id_usuario_admin" id="id_usuario_admin" value="<?=!empty($usuario) ? $usuario->id_usuario_admin : ''?>">

                                            <div class="col-12 col-sm-6 col-md-3">
                                                <div class="form-group">
                                                    <label for="nombre"><?=l('admin-usuarios-admin-campo-nombre');?></label>
                                                    <input type="text" name="nombre" id="nombre" class="form-control" placeholder="<?=l('admin-usuarios-admin-campo-nombre');?>" aria-describedby="nombreHelpId" value="<?=!empty($usuario) ? $usuario->nombre : ''?>">
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-3">
                                                <div class="form-group">
                                                    <label for="email"><?=l('admin-usuarios-admin-campo-email');?></label>
                                                    <input type="email" name="email" id="email" class="form-control" placeholder="<?=l('admin-usuarios-admin-campo-email');?>" aria-describedby="emailHelpId"  value="<?=!empty($usuario) ? $usuario->email : ''?>">
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-3">
                                                <div class="form-group">
                                                    <label for="id_perfil"><?=l('admin-usuarios-admin-campo-perfil');?></label>
                                                    <select class="form-control" name="id_perfil" id="id_perfil">
                                                        <?php
                                                        foreach( $perfiles as $perfil )
                                                        {
                                                            if( $perfil->id_perfil == 1 && $_SESSION['admin_panel']->id_perfil != 1 )
                                                                continue;
                                                            ?>
                                                            <option value="<?=$perfil->id_perfil;?>" <?=(!empty($usuario) && $perfil->id_perfil == $usuario->id_perfil ? 'selected' : '');?>><?=$perfil->nombre;?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 col-md-3">
                                                <div class="form-group">
                                                    <label for="password"><?=l('admin-usuarios-admin-campo-password');?></label>
                                                    <input type="password" name="password" id="password" class="form-control" placeholder="<?=l('admin-usuarios-admin-campo-password');?>" aria-describedby="passwordHelpId">
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end align-items-center">
                                        <div class="justify-self-end">
                                            <?php if( !empty($usuario) && isset($usuario->id_usuario_admin) ) : ?>
                                                <button type="submit" name="submitUpdateUsuarioAdmin" class="btn btn-primary  waves-effect waves-light"><?=l('admin-actualizar');?></button>
                                                <button type="button" class="btn btn-danger  waves-effect waves-light" onClick="confirmarEliminacion( <?= $usuario->id_usuario_admin ?>, 'Admin', () => window.history.back() )"><?=l('admin-eliminar');?></button>
                                            <?php else: ?>
                                                <button type="submit" name="submitCrearUsuarioAdmin" class="btn btn-primary  waves-effect waves-light"><?=l('admin-crear');?></button>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
				</div>
			</div>
		</div> <!-- end col -->

	</div> <!-- end row -->

</div>