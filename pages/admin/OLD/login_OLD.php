<div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="../../index2.html" class="h1"><b>Admin</b>LTE</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form action="../../index3.html" method="post">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password">
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
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <div class="social-auth-links text-center mt-2 mb-3">
                <a href="#" class="btn btn-block btn-primary">
                    <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
                </a>
                <a href="#" class="btn btn-block btn-danger">
                    <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
                </a>
            </div>
            <!-- /.social-auth-links -->

            <p class="mb-1">
                <a href="forgot-password.html">I forgot my password</a>
            </p>
            <p class="mb-0">
                <a href="register.html" class="text-center">Register a new membership</a>
            </p>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!-- /.login-box -->


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<?php Metas::getMetas();?>
	<?php Tools::loadBootstrap('css');?>
	<link rel="stylesheet" type="text/css" href="<?=_ASSETS_._ADMIN_;?>metismenu.min.css">
	<link rel="stylesheet" type="text/css" href="<?=_ASSETS_._ADMIN_;?>icons.css">
	<link rel="stylesheet" type="text/css" href="<?=_ASSETS_._ADMIN_;?>style.css">
	<link rel="stylesheet" type="text/css" href="<?=_ASSETS_._ADMIN_;?>custom.css">
	<link rel="stylesheet" type="text/css" href="<?=_ASSETS_._ADMIN_;?>sweetalert2.min.css">
	<script type="text/javascript" src="<?=_ASSETS_;?>jquery/jquery.min.js"></script>
</head>

	<body>

		<!-- Background -->
		<div class="account-pages"></div>
		<!-- Begin page -->
		<div class="wrapper-page">

			<div class="card">
				<div class="card-body">

					<h3 class="text-center m-0">
						<img src="<?=_ASSETS_._ADMIN_;?>images/demo/logo.png" alt="logo">
					</h3>

					<div class="p-3">
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
						<form class="form-horizontal m-t-30" method="POST">

							<div class="form-group">
								<label for="email">E-mail</label>
								<input type="text" class="form-control" id="email" placeholder="Introduce tu e-mail" name="usuario">
							</div>

							<div class="form-group">
								<label for="password">Contraseña</label>
								<input type="password" class="form-control" id="password" placeholder="Introduce tu contraseña" name="password">
							</div>

							<div class="form-group row m-t-20">
								<div class="col-12 text-center">
									<button class="btn btn-primary w-md waves-effect waves-light login-btn" name="btn-login" type="submit">Entrar</button>
								</div>
							</div>

							<!--div class="form-group m-t-10 mb-0 row">
								<div class="col-12 m-t-20">
									<a href="pages-recoverpw.html" class="text-muted"><i class="mdi mdi-lock"></i> Forgot your password?</a>
								</div>
							</div-->
						</form>
					</div>

				</div>
			</div>

			<div class="m-t-40 text-center">
				<p class="text-muted">© <?=date('Y');?> Anelis Network</p>
			</div>

		</div>

		<!-- END wrapper -->


		<script type="text/javascript" src="<?=_ASSETS_._ADMIN_;?>bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="<?=_ASSETS_._ADMIN_;?>metismenu.min.js"></script>
		<script type="text/javascript" src="<?=_ASSETS_._ADMIN_;?>jquery.slimscroll.js"></script>
		<script type="text/javascript" src="<?=_ASSETS_._ADMIN_;?>waves.min.js"></script>
		<script type="text/javascript" src="<?=_ASSETS_._ADMIN_;?>sweetalert2.all.min.js"></script>

		<script type="text/javascript" src="<?=_ASSETS_._ADMIN_;?>app.js"></script>
	</body>

</html>
