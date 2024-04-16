<body class="hold-transition login-page dark-mode">
<div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="<?=_ASSETS_._ADMIN_;?>index2.html" class="h1"><b>ApPet</b></a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>
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
            <form method="post" action="<?=_DOMINIO_._ADMIN_;?>">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" id="usuario" name="usuario" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="checkbox" name="checkbox">
                            <label for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" name="btn-login" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <div class="social-auth-links text-center mt-2 mb-3" hidden>
                <a href="#" class="btn btn-block btn-primary">
                    <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
                </a>
                <a href="#" class="btn btn-block btn-danger">
                    <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
                </a>
            </div>
            <!-- /.social-auth-links -->
<br>
            <p class="mb-1 text-center">
                <a href="<?=_DOMINIO_.$_SESSION['lang']?>/forgot-password/">I forgot my password</a>
            </p>
            <p class="mb-0 text-center">
                <a href="<?=_DOMINIO_.$_SESSION['lang']?>/register/" class="text-center">Register a new membership</a>
            </p>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?=_ASSETS_._ADMIN_;?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=_ASSETS_._ADMIN_;?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=_ASSETS_._ADMIN_;?>dist/js/adminlte.min.js"></script>
</body>



