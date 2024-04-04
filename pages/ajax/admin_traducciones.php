<?php
if($totalTraducciones > 0)
{?>
	<table id="tablaTraducciones" class="footable table table-striped bg-default table-primary">
		<thead>
			<tr>
				<th>Zona de traducción</th>
                <th>Shortcode</th>
                <th>Contenido</th>
                <?php
                    //Vamos a cargar los diferentes idiomas dinamicamente
                    foreach($datos_idiomas as $key => $idioma)
                    {
                        if( empty($slug_idioma) || $slug_idioma == $idioma->slug )
                        {
                        ?>
                            <th class="text-center" style="padding: .875rem 0.9375rem;"><img src="<?=_DOMINIO_.$idioma->icon?>" width="20" /></th>
                        <?php
                        }
                    }
                ?>
                <th class="text-right">Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php
                if(count($datos_traducciones) > 0)
                {
                    $contador =0;
                    foreach($datos_traducciones as $key => $traduction)
                    {
                        ?>
                        <tr>
                            <td>
                                <?php
                                    //Vamos a cargar los diferentes idiomas dinamicamente
                                        if((isset($traduction[(!empty($slug_idioma) ? $slug_idioma : _DEFAULT_LANGUAGE_)]->traduction_for) && $traduction[(!empty($slug_idioma) ? $slug_idioma : _DEFAULT_LANGUAGE_)]->traduction_for != "") || (isset($traduction[(!empty($slug_idioma) ? $slug_idioma : $idioma_busqueda[$contador][0]) ]->traduction_for) && $traduction[(!empty($slug_idioma) ? $slug_idioma : $idioma_busqueda[$contador][0]) ]->traduction_for !=""))
                                        {
                                            if(isset($idioma_busqueda[$contador]) && (count($idioma_busqueda[$contador]) == $totalIdiomas ||  count($idioma_busqueda[$contador])==0))
                                            {
                                                echo $traduction[(!empty($slug_idioma) ? $slug_idioma : _DEFAULT_LANGUAGE_)]->traduction_for;
                                            }
                                            else
                                            {
                                                echo $traduction[(!empty($slug_idioma) ? $slug_idioma : $idioma_busqueda[$contador][0]) ]->traduction_for;
                                            }
                                        }
                                        else
                                            echo "home";
                                ?>
                            </td>

                            <?php
                                if(isset($idioma_busqueda[$contador]) && (count($idioma_busqueda[$contador]) == $totalIdiomas ||  count($idioma_busqueda[$contador])==0))
                                {
                                ?>
                                    <td><?=$traduction[(!empty($slug_idioma) ? $slug_idioma : _DEFAULT_LANGUAGE_)]->shortcode?></td>
                                <?php 
                                }
                                else
                                {
                                ?>
                                    <td><?=$traduction[(!empty($slug_idioma) ? $slug_idioma : $idioma_busqueda[$contador][0] )]->shortcode?></td>
                                <?php 
                                }
                            ?>
                            <td>
                                <?php
                                if(isset($idioma_busqueda[$contador]) && (count($idioma_busqueda[$contador]) == $totalIdiomas ||  count($idioma_busqueda[$contador])==0))
                                {
                                ?>
                                    <?=$traduction[(!empty($slug_idioma) ? $slug_idioma : _DEFAULT_LANGUAGE_)]->contenido?>
                                <?php 
                                }
                                else
                                {
                                ?>
                                    <?=$traduction[(!empty($slug_idioma) ? $slug_idioma : $idioma_busqueda[$contador][0] )]->contenido?>
                                <?php 
                                }
                                ?>
                            </td>
                            <?php
                                foreach($datos_idiomas as $key => $idioma)
                                {
                                    if( empty($slug_idioma) || $slug_idioma == $idioma->slug )
                                    {
                                    ?>
                                        <td class="text-center">
                                            <?php
                                                //Verificamos si existe traduccion o no para este idioma
                                                if(isset($traduction[$idioma->slug]->contenido) && !empty($traduction[$idioma->slug]->contenido))
                                                    echo "<i class='mdi mdi-checkbox-marked text-success' style='font-size: 20px;'></i>";
                                                else
                                                    echo "<i class='mdi mdi-close-box text-danger' style='font-size: 20px;'></i>";
                                            ?>
                                        </td>
                                    <?php
                                    }
                                }
                            ?>
                            <td class="text-right">
                            <?php
                                if(isset($idioma_busqueda[$contador]) && (count($idioma_busqueda[$contador]) == $totalIdiomas ||  count($idioma_busqueda[$contador])==0))
                                {
                                ?>
                                    <a href="<?=_DOMINIO_._ADMIN_?>administrar-traduccion/<?=$traduction[(!empty($slug_idioma) ? $slug_idioma : _DEFAULT_LANGUAGE_)]->id?>/" target="_blank" type="button" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="Editar traducción"> <i class="fas fa-pencil-alt text-light"></i></a>
                                <?php 
                                }
                                else
                                {
                                ?>
                                <a href="<?=_DOMINIO_._ADMIN_?>administrar-traduccion/<?=$traduction[(!empty($slug_idioma) ? $slug_idioma : $idioma_busqueda[$contador][0])]->id?>/" target="_blank" type="button" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="Editar traducción"> <i class="fas fa-pencil-alt text-light"></i></a>
                                <?php 
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        $contador++;
                    }
                }
            ?>
		</tbody>
	</table>
<?php
}
else
{
    if( $filtroBloqueado )
    {
    ?>
    	<div class="alert alert-dark text-center">
    		<p class="mb-0">Debes seleccionar un idioma para buscar traducciones pendientes</p>
    	</div>
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
}
?>
<div class="row">
	<div class="col-sm-6">
		<div class="dataTables_info">
			<?=($totalTraducciones > 1 || $totalTraducciones == '0') ? $totalTraducciones.' traducciones encontradas' : '1 traducción encontrada'?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="dataTables_paginate paging_bootstrap">
			<?php Tools::->getPaginador($pagina, $limite, 'lang', 'getAllTraductionsGroupedFiltered', 'ajax_get_traductions_filtered', '', '', 'end', true); ?>
		</div>
	</div>
</div>

<script>
	$(function()
	{
		$('.footable').footable();
	});
</script>