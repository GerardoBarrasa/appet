<?php
if(count($datos) > 0)
{?>
    <div class="table-responsive">
    	<table id="tablaSlugs" class="footable table table-striped bg-default table-primary">
    		<thead>
    			<tr>
    				<th><?=l('admin-slugs-campo-slug');?></th>
                    <th><?=l('admin-slugs-campo-title');?></th>
                    <th><?=l('admin-slugs-campo-description');?></th>
                    <?php
                        //Vamos a cargar los diferentes idiomas dinamicamente
                        foreach($languages as $key => $idioma)
                        {
                            if( empty($slug_idioma) || $slug_idioma == $idioma->slug )
                            {
                            ?>
                                <th class="text-center" style="padding: .875rem 0.9375rem;"><img src="<?=_DOMINIO_.$idioma->icon?>" width="20" /></th>
                            <?php
                            }
                        }
                    ?>
                    <th class="text-right"><?=l('admin-listado-acciones');?></th>
    			</tr>
    		</thead>
    		<tbody>
                <?php
                    foreach($datos as $key => $slug){
                        ?>
                        <tr>
                            <td><?=$slug->slug?></td>
                            <td><?=$slug->title?></td>
                            <td><?=Tools::cortarString(30, $slug->description)?></td>
                            <?php
                                if(count($languages) > 0){
                                    foreach($languages as $key => $lang){
                                        if(isset($slug->langs[$lang->slug]) && $slug->langs[$lang->slug])
                                            $iconTranslated = "<i class='fas fa-check text-success'></i>";
                                        else
                                            $iconTranslated = "<i class='fas fa-times text-danger'></i>";

                                        ?><td class="text-center"><?=$iconTranslated?></td><?php
                                    }
                                }
                            ?>
                            <td class="text-right">
                                <a href="<?=_DOMINIO_._ADMIN_?>administrar-slug/<?=$slug->id;?>/" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="<?=l('admin-listado-editar');?>"> <i class="fas fa-pencil-alt text-light"></i></a>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
    		</tbody>
    	</table>
    </div>
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
	</div>
	<div class="col-sm-6">
		<div class="dataTables_paginate paging_bootstrap">
			<?php Tools::getPaginador($pagina, $limite, 'Slugs', 'getSlugsFiltered', 'ajax_get_metas_admin', '', '', 'end'); ?>
		</div>
	</div>
</div>

<script>
	$(function()
	{
		$('.footable').footable();
	});
</script>