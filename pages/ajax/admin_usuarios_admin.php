<?php
if($total > 0)
{?>
	<table id="tablaUsuarios" class="footable table table-striped bg-default table-primary">
		<thead>
			<tr>
				<th>Nombre</th>
				<th>Email</th>
				<th data-breakpoints="xs" class="text-center">Desde</th>
				<th data-breakpoints="xs sm md" class="text-right">Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($total > 0){
				foreach( $usuarios as $key => $usuario ){
					?>
					
					<tr class="gradeX" <?= $key == 0 ? "data-expanded='true'" : '' ?> >
						<td><?=$usuario->nombre?></td>
						<td><?=$usuario->email?></td>
						<td class="text-center"><?=!empty($usuario) ? Tools::fechaConHora($usuario->date_created) : '';?></td>
						<td align="right">
							<a href="<?= _DOMINIO_ . _ADMIN_ . 'usuario-admin/' . $usuario->id_usuario_admin . '/' ?>" type="button" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="Editar usuario">
								<i class="fas fa-pencil-alt text-light"></i>
							</a>
							<button type="button" class="btn btn-danger waves-effect waves-light" onClick="confirmarEliminacion( <?= $usuario->id_usuario_admin ?>, 'Admin', () => ajax_get_usuarios_admin(<?= $comienzo ?>, <?= $limite ?>, <?= $pagina ?>) )"   data-toggle="tooltip" title="Eliminar usuario">
								<i class="far fa-trash-alt"></i>
							</button>
						</td>
					</tr>

					<?php
				}
			}
			else{
				?>
				<tr class="gradeX">
					<td class="text-center" colspan="6"><strong><i class="fa fa-warning"></i> &nbsp; No se han encontrado usuarios</strong></td>
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
		<p class="mb-0">No se han encontrado usuarios</p>
	</div>
	<?php
}
?>
<div class="row">
	<div class="col-sm-6">
		<div class="dataTables_info">
			<?=($total > 1 || $total == '0') ? $total.' usuarios encontrados' : '1 usuario encontrado'?>
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