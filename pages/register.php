<body class="register-page dark-mode" style="min-height: 570.781px;">
<div class="register-box">
    <div class="register-logo">
        <a href="../../index2.html"><b>Admin</b>LTE</a>
    </div>
    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Register a new membership</p>
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
            <?php
            if( !empty($mensajeSuccess) )
            {
                ?>
                <div class="success alert-success bg-success text-dark" role="alert">
                    <?=$mensajeSuccess;?>
                </div>
                <?php
            }
            ?>
            <form method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre completo">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" id="rpassword" name="rpassword" placeholder="Reescribir contraseña">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Direccion">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-location-arrow"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="localidad" name="localidad" placeholder="Localidad">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-city"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="provincia" name="provincia" placeholder="Provincia">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-address-card"></span>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="checkbox" name="checkbox" value="agree">
                            <label for="agreeTerms">
                                I agree to the <a href="#">terms</a>
                            </label>
                        </div>
                    </div>

                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block" name="btn-register">Register</button>
                    </div>

                </div>
            </form>

            <div class="social-auth-links text-center" hidden>
                <p>- OR -</p>
                <a href="#" class="btn btn-block btn-primary">
                    <i class="fab fa-facebook mr-2"></i>
                    Sign up using Facebook
                </a>
                <a href="#" class="btn btn-block btn-danger">
                    <i class="fab fa-google-plus mr-2"></i>
                    Sign up using Google+
                </a>
            </div>
<br>
            <div class="text-center">
                <a href="<?=_DOMINIO_._ADMIN_;?>login/" class="text-center">I already have a membership</a>
            </div>
        </div>

    </div>
</div>


<script src="../../plugins/jquery/jquery.min.js"></script>

<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="../../dist/js/adminlte.min.js?v=3.2.0"></script>


</body>