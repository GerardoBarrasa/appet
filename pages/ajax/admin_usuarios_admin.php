<?php
if($total > 0)
{?>
	<table id="tablaUsuarios" class="footable table table-striped bg-default table-primary">
		<thead>
			<tr>
				<th><?=l('admin-usuarios-admin-campo-nombre');?></th>
				<th><?=l('admin-usuarios-admin-campo-email');?></th>
				<th><?=l('admin-usuarios-admin-campo-perfil');?></th>
				<th data-breakpoints="xs" class="text-center"><?=l('admin-usuarios-admin-campo-desde');?></th>
				<th data-breakpoints="xs sm md" class="text-right"><?=l('admin-listado-acciones');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach( $usuarios as $key => $usuario ){
					?>
					
					<tr class="gradeX" <?= $key == 0 ? "data-expanded='true'" : '' ?> >
						<td><?=$usuario->nombre?></td>
						<td><?=$usuario->email?></td>
						<td><?=$usuario->perfil?></td>
						<td class="text-center"><?=!empty($usuario) ? Tools::fechaConHora($usuario->date_created) : '';?></td>
						<td align="right">
							<a href="<?= _DOMINIO_ . _ADMIN_ . 'usuario-admin/' . $usuario->id_usuario_admin . '/' ?>" type="button" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="<?=l('admin-listado-editar');?>">
								<i class="fas fa-pencil-alt text-light"></i>
							</a>
							<button type="button" class="btn btn-danger waves-effect waves-light" onClick="confirmarEliminacion( <?= $usuario->id_usuario_admin ?>, 'Admin', () => ajax_get_usuarios_admin(<?= $comienzo ?>, <?= $limite ?>, <?= $pagina ?>) )"   data-toggle="tooltip" title="<?=l('admin-eliminar');?>">
								<i class="far fa-trash-alt"></i>
							</button>
						</td>
					</tr>

					<?php
				}
			?>
		</tbody>
	</table>
<?php
}
else
{?>
	<div class="alert alert-dark text-center">
		<p class="mb-0"><?=l('admin-listado-vacio');?></p>
	</div>
	<?php
}
?>
<div class="row">
	<div class="col-sm-6">
		<div class="dataTables_info">
			<?=l('admin-listado-usuarios-admin-cantidad', array($total))?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="dataTables_paginate paging_bootstrap">
			<?php Tools::getPaginador($pagina, $limite, 'Admin', 'getUsuariosWithFiltros', 'ajax_get_usuarios_admin', '', '', 'end'); ?>
		</div>
	</div>
</div>

<script>
	$(function()
	{
		$('.footable').footable();
	});
</script>