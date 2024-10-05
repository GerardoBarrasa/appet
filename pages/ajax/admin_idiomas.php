<?php
if($total > 0)
{?>
	<table id="tablaIdiomas" class="footable table table-striped bg-default table-primary">
		<thead>
			<tr>
				<th class="text-center"><?=l('admin-idiomas-campo-id');?></th>
				<th><?=l('admin-idiomas-campo-nombre');?></th>
                <th><?=l('admin-idiomas-campo-abreviatura');?></th>
                <th><?=l('admin-idiomas-campo-icono');?></th>
                <th><?=l('admin-idiomas-campo-color');?></th>
				<th class="text-center"><?=l('admin-idiomas-campo-activo');?></th>
                <th class="text-right"><?=l('admin-listado-acciones');?></th>
			</tr>
		</thead>
		<tbody>
            <?php
                if(count($idiomas) > 0){
                    foreach($idiomas as $key => $idioma){
                        ?>
                        <tr>
                            <td class="text-center"><?=$idioma->id?></td>
                            <td><?=$idioma->nombre?></td>
                            <td><?=$idioma->slug?></td>
                            <td><img src="<?=_DOMINIO_.$idioma->icon?>" width="30" style="border-radius: 0px;" /></td>
                            <td><?=$idioma->colour?></td>
                            <td class="text-center">
                                <?php
                                    //Verificamos si existe traduccion o no para este idioma
                                    if(isset($idioma->visible) && $idioma->visible == '1')
                                        echo "<i class='mdi mdi-checkbox-marked text-success' style='font-size: 20px;'></i>";
                                    else
                                        echo "<i class='mdi mdi-close-box text-danger' style='font-size: 20px;'></i>";
                                ?>
                            </td>
                            <td class="text-right">
                                <a href="<?=_DOMINIO_._ADMIN_?>administrar-idioma/<?=$idioma->id?>/" type="button" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="<?=l('admin-listado-editar');?>"> <i class="fas fa-pencil-alt text-light"></i></a>
                            </td>
                        </tr>
                        <?php
                    }
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
			<?=l('admin-listado-idiomas-cantidad', array($total))?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="dataTables_paginate paging_bootstrap">
			<?php Tools::getPaginador($pagina, $limite, 'Idiomas', 'getIdiomasWithFiltros', 'ajax_get_idiomas_admin', '', '', 'end'); ?>
		</div>
	</div>
</div>

<script>
	$(function()
	{
		$('.footable').footable();
	});
</script>