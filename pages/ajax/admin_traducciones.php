<?php
if($total > 0)
{?>
	<table id="tablaTraducciones" class="footable table table-striped bg-default table-primary">
		<thead>
			<tr>
                <th>Shortcode</th>
                <th>Texto</th>
                <th class="text-center">Idioma</th>
                <th class="text-right">Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php
                foreach($traducciones as $key => $traduccion)
                {
                    ?>
                    <tr>
                        <td><?=$traduccion->shortcode;?></td>
                        <td><?=$traduccion->texto;?></td>
                        <td class="text-center"><img src="<?=_DOMINIO_.$traduccion->icon?>" width="20" /></td>
                        <td class="text-right">
                            <a href="<?=_DOMINIO_._ADMIN_?>traduccion/<?=$traduccion->id_traduccion;?>/" target="_blank" type="button" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="Editar traducción"> <i class="fas fa-pencil-alt text-light"></i></a>
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
        <p class="mb-0">No se han encontrado traducciones</p>
    </div>
    <?php
}
?>
<div class="row">
	<div class="col-sm-6">
		<div class="dataTables_info">
			<?=($total > 1 || $total == '0') ? $total.' traducciones encontradas' : '1 traducción encontrada'?>
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
