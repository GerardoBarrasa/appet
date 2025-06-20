<?php
/**
 * @var $total
 * @var $tutores
 */
if($total > 0)
{?>
	<table id="tablaTutores" class="table table-striped bg-default table-bordered table-hover">
		<thead>
			<tr>
				<th>Nombre</th>
				<th>Email</th>
				<th data-breakpoints="xs" class="text-center">Alta</th>
				<th data-breakpoints="xs sm md" class="text-right">Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach( $tutores as $key => $tutor ){
					?>
					
					<tr class="gradeX" <?= $key == 0 ? "data-expanded='true'" : '' ?> >
						<td><?=$tutor->nombre?></td>
						<td><?=$tutor->email?></td>
						<td class="text-center"><?=!empty($tutor) ? Tools::fechaConHora($tutor->date_created) : '';?></td>
						<td align="right">
							<a href="<?= _DOMINIO_ . _ADMIN_ . 'tutor-admin/' . $tutor->id_tutor_admin . '/' ?>" type="button" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" title="Editar tutor">
								<i class="fas fa-pencil-alt text-light"></i>
							</a>
							<button type="button" class="btn btn-danger waves-effect waves-light" onClick="confirmarEliminacion( <?= $tutor->id_tutor_admin ?>, 'Admin', () => ajax_get_tutores_admin() )"   data-toggle="tooltip" title="Eliminar tutor">
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