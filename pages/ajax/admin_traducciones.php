<?php
if($total > 0)
{?>
	<table id="tablaTraducciones" class="footable table table-striped bg-default table-primary">
		<thead>
			<tr>
				<th><?=l('admin-traducciones-campo-zona');?></th>
                <th><?=l('admin-traducciones-campo-shortcode');?></th>
                <th><?=l('admin-traducciones-campo-texto');?></th>
                <th class="text-center"><?=l('admin-traducciones-campo-shortcode');?></th>
                <th class="text-right"><?=l('admin-listado-acciones');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
                foreach($traducciones as $key => $traduccion)
                {
                    ?>
                    <tr>
                    	<td><?=ucwords($traduccion->zona);?></td>
                        <td><?=$traduccion->shortcode;?></td>
                        <td><?=$traduccion->texto;?></td>
                        <td class="text-center"><img src="<?=_DOMINIO_.$traduccion->icon?>" width="20" /></td>
                        <td class="text-right">
                            <a href="<?=_DOMINIO_._ADMIN_?>traduccion/<?=$traduccion->id_traduccion;?>/" target="_blank" type="button" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="<?=l('admin-listado-editar');?>"> <i class="fas fa-pencil-alt text-light"></i></a>
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
{
    ?>
    <div class="alert alert-dark text-center">
        <p class="mb-0"><?=l('admin-listado-vacio');?></p>
    </div>
    <?php
}
?>
<div class="row">
	<div class="col-sm-6">
		<div class="dataTables_info">
			<?=l('admin-listado-traducciones-cantidad', array($total))?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="dataTables_paginate paging_bootstrap">
			<?php
            Tools::getPaginador($pagina, $limite, 'Traducciones', 'getTraduccionesWithFiltros', 'ajax_get_traductions_filtered', '', '', 'end', true); ?>
		</div>
	</div>
</div>

<script>
	$(function()
	{
		$('.footable').footable();
	});
</script>