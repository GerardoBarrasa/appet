<div class="login-box">
    <?php
    if( !empty($mensajeError) )
    {
        ?>
        <div class="alert alert-danger bg-danger text-white" role="alert">
            <?=$mensajeError;?>
        </div>
        <?php
    }
    ?>
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="<?=_DOMINIO_._ADMIN_?>" class="h1"><b>Ap</b>Pet</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Identifícate por favor</p>

            <form action="<?=_DOMINIO_._ADMIN_?>" method="post">
                <div class="input-group mb-3">
                    <input type="email" name="usuario" class="form-control" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Contraseña">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                Recuérdame
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button name="btn-login" type="submit" class="btn btn-primary btn-block">Entrar</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <?php //include(_INCLUDES_._ADMIN_.'login_social.php'); ?>
            <!-- /.social-auth-links -->

            <p class="mb-1">
                <a href="<?=_DOMINIO_._ADMIN_?>forgot-password/">Olvidé mi contraseña</a>
            </p>
            <p class="mb-0">
                <a href="<?=_DOMINIO_._ADMIN_?>registro/" class="text-center">Registrarme</a>
            </p>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!-- /.login-box -->