<?php
if($total > 0)
{?>
	<table id="tablaPerfiles" class="footable table table-striped bg-default table-primary">
		<thead>
			<tr>
				<th class="text-center"><?=l('admin-perfiles-campo-id');?></th>
				<th><?=l('admin-perfiles-campo-nombre');?></th>
				<th class="text-right"><?=l('admin-listado-acciones');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($perfiles as $perfil)
			{
				?>
				<tr>
					<td class="text-center"><?=$perfil->id_perfil?></td>
					<td><?=$perfil->nombre?></td>
					<td class="text-right">
						<a href="<?=_DOMINIO_._ADMIN_?>permiso/<?=$perfil->id_perfil?>/" type="button" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="<?=l('admin-listado-editar');?>"> <i class="fas fa-pencil-alt text-light"></i></a>
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
			<?=l('admin-listado-perfiles-cantidad', array($total))?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="dataTables_paginate paging_bootstrap">
			<?php Tools::getPaginador($pagina, $limite, 'Admin', 'getPerfiles', 'ajax_get_perfiles', '', '', 'end'); ?>
		</div>
	</div>
</div>

<script>
	$(function()
	{
		$('.footable').footable();
	});
</script>