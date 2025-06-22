<?php
/**
 * @var $tutor
 */
?>
<section class="content">
    <div class="container-fluid">
        <form method="post">
            <div class="row">
                <div class="col-12">
                    <div class="row">

                        <!-- Form principal -->
                        <div class="col-12">
                            <div class="row">
                                <input type="hidden" name="id" id="id"
                                       value="<?= !empty($tutor) ? $tutor->id : '' ?>">

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="nombre">Nombre<span class="text-danger ml-2">*</span></label>
                                        <input type="text" name="nombre" id="nombre" class="form-control"
                                               placeholder="Nombre" aria-describedby="nombreHelpId"
                                               value="<?= !empty($tutor) ? $tutor->nombre : '' ?>" required>
                                        <small id="nombreHelpId" class="text-muted">Nombre del tutor</small>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="telefono_1">Teléfono 1<span class="text-danger ml-2">*</span></label>
                                        <input type="tel" name="telefono_1" id="telefono_1" class="form-control"
                                               value="<?= !empty($tutor) ? $tutor->telefono_1 : '' ?>" required>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="telefono_2">Teléfono 2</label>
                                        <input type="tel" name="telefono_2" id="telefono_2" class="form-control"
                                               value="<?= !empty($tutor) ? $tutor->telefono_2 : '' ?>">
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                               placeholder="Email" aria-describedby="emailHelpId"
                                               value="<?= !empty($tutor) ? $tutor->email : '' ?>">
                                        <small id="emailHelpId" class="text-muted">Email del tutor</small>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="notas">Notas</label>
                                        <textarea name="notas" id="notas" class="form-control"><?= !empty($tutor) ? $tutor->notas : '' ?></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end align-items-center">
                            <div class="justify-self-end">
                                <?php if (!empty($tutor) && isset($tutor->id)) : ?>
                                    <button type="submit" name="submitUpdateTutor"
                                            class="btn btn-primary  waves-effect waves-light">Actualizar
                                    </button>
                                    <button type="button" class="btn btn-danger  waves-effect waves-light"
                                            onClick="confirmarEliminacion( <?= $tutor->id ?>, 'Admin', () => window.history.back() )">
                                        Eliminar
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="submitCreateTutor"
                                            class="btn btn-primary  waves-effect waves-light">Crear
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
        <?php if($tutor){?>
            <div class="row">
                <div class="h4">Mascotas asignadas</div>
            </div>
            <div class="row">
                <div class="col-12 col-md 6" id="mascotasAsignadas">

                </div>
                <div class="col-12 col-md 6">
                    <input type="text" class="form-control debouncefunc mb-1" name="busqueda" id="busqueda" placeholder="Busca una mascota para asignarla al tutor" value="" data-function="ajax_get_mascotas_admin" data-idtutor="<?=$tutor ? $tutor->id : ''?>" data-listado="admin_mascotas_list_tutor" data-ifempty="empty">
                    <div id="page-content">

                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
<script> ajax_get_mascotas_asignadas('<?=$tutor ? $tutor->id : ''?>'); </script>