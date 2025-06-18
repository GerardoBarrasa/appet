<?php
/**
 * @var $usuario
 */
?>
<section class="content">
    <div class="container-fluid">
        <form method="post">
            <div class="row">
                <div class="col-12">
                    <div class="row d-flex align-items-start">
                        <div class="col-12">
                            <?php
                            if (!$usuario && !empty($perfiles)) {
                                ?>
                                <select name="perfil" id="idperfil" class="form-select mb-3 w-auto">
                                    <option value="">Selecciona el tipo de usuario</option>
                                    <?php foreach ($perfiles as $perfil) { ?>
                                        <option value="<?= $perfil->id_perfil ?>" <?= !empty($usuario) && $usuario->id_perfil == $perfil->id_perfil ? 'selected' : '' ?>><?= $perfil->nombre ?></option>
                                    <?php } ?>
                                </select>
                            <?php }
                            else{?>
                            <h1 class="h2 my-0">
                                <span class="text-secondary"><small><?= !empty($usuario) ? $usuario->nombre : '' ?></small></span>
                            </h1>

                            <?php if (!empty($usuario) && isset($usuario->date_created)) : ?>
                                <p class="mb-3"><span class="bold text-primary">Miembro desde:&nbsp;</span><?= !empty($usuario) ? Tools::fechaConHora($usuario->date_created) : ''; ?>
                                </p>
                            <?php endif;
                            }?>
                        </div>
                    </div>


                    <div class="row">

                        <!-- Form principal -->
                        <div class="col-12">
                            <div class="row">
                                <input type="hidden" name="id_usuario_admin" id="id_usuario_admin"
                                       value="<?= !empty($usuario) ? $usuario->id_usuario_admin : '' ?>">

                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label for="nombre">Nombre</label>
                                        <input type="text" name="nombre" id="nombre" class="form-control"
                                               placeholder="Nombre" aria-describedby="nombreHelpId"
                                               value="<?= !empty($usuario) ? $usuario->nombre : '' ?>" required>
                                        <small id="nombreHelpId" class="text-muted">Nombre del usuario</small>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label for="apellidos">Apellidos</label>
                                        <input type="text" name="apellidos" id="apellidos" class="form-control"
                                               placeholder="Apellidos" aria-describedby="nombreHelpId"
                                               value="<?= !empty($usuario) ? $usuario->apellidos : '' ?>">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                               placeholder="Email" aria-describedby="emailHelpId"
                                               value="<?= !empty($usuario) ? $usuario->email : '' ?>" required>
                                        <small id="emailHelpId" class="text-muted">Email del usuario</small>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label for="password">Contraseña</label>
                                        <input type="password" name="password" id="password" class="form-control"
                                               placeholder="Contraseña" aria-describedby="passwordHelpId">
                                        <small id="passwordHelpId" class="text-muted">Contraseña del usuario</small>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end align-items-center">
                            <div class="justify-self-end">
                                <?php if (!empty($usuario) && isset($usuario->id_usuario_admin)) : ?>
                                    <button type="submit" name="submitUpdateUsuarioAdmin"
                                            class="btn btn-primary  waves-effect waves-light">Actualizar
                                    </button>
                                    <button type="button" class="btn btn-danger  waves-effect waves-light"
                                            onClick="confirmarEliminacion( <?= $usuario->id_usuario_admin ?>, 'Admin', () => window.history.back() )">
                                        Eliminar
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="submitCrearUsuarioAdmin"
                                            class="btn btn-primary  waves-effect waves-light">Crear
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
</section>