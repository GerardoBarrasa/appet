<?php
/**
 * @var $total
 * @var $tutores
 */
if($total > 0)
{?>

	<table id="tablaTutores" class="table table-responsive-lg table-striped bg-default table-bordered table-hover">
		<thead>
			<tr>
				<th>Nombre</th>
				<th>Teléfono 1</th>
				<th>Teléfono 2</th>
				<th>Email</th>
				<th data-breakpoints="xs sm md" class="text-right">Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach( $tutores as $key => $tutor ){
					?>
					
					<tr class="gradeX" <?= $key == 0 ? "data-expanded='true'" : '' ?> >
						<td><?=$tutor->nombre?></td>
						<td><?=$tutor->telefono_1?></td>
						<td><?=$tutor->telefono_2?></td>
						<td><?=$tutor->email?></td>
						<td align="right">
							<a href="<?= _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . 'tutor/'. $tutor->slug .'-'. $tutor->id . '/' ?>" type="button" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="Editar tutor">
								<i class="fas fa-pencil-alt text-light"></i>
							</a>
							<button type="button" class="btn btn-danger waves-effect waves-light" onClick="confirmarEliminacion( <?= $tutor->id ?>, 'Admin', () => ajax_get_tutores_admin() )"   data-toggle="tooltip" title="Eliminar tutor">
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
		<p class="mb-0">No se han encontrado tutores</p>
	</div>
	<?php
}
?>