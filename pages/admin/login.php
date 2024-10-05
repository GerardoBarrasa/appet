<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<?php Metas::getMetas();?>
	<?php include(_INCLUDES_._ADMIN_.'stylesheets.php'); ?>
	<?php include(_INCLUDES_._ADMIN_.'javascript_top.php'); ?>
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

						if( !empty($mensajeSuccess) )
						{
							?>
							<div class="alert alert-success bg-success text-white" role="alert">
								<?=$mensajeSuccess;?>
							</div>
							<?php
						}
						?>
						<form class="form-horizontal m-t-30" method="POST">

							<div class="form-group">
								<label for="email"><?=l('admin-login-email');?></label>
								<input type="text" class="form-control" id="email" placeholder="<?=l('admin-login-email-placeholder');?>" name="usuario">
							</div>

							<div class="form-group">
								<label for="password"><?=l('admin-login-password');?></label>
								<input type="password" class="form-control" id="password" placeholder="<?=l('admin-login-password-placeholder');?>" name="password">
							</div>

							<div class="form-group row m-t-20">
								<div class="col-12 text-center">
									<button class="btn btn-primary w-md waves-effect waves-light login-btn" name="btn-login" type="submit"><?=l('admin-login-button');?></button>
								</div>
							</div>

							<div class="form-group m-t-10 mb-0 row">
								<div class="col-12 m-t-20">
									<a href="javascript:void(0)" data-toggle="modal" data-target="#askForNewPassword" class="text-muted"> <i class="mdi mdi-lock"></i> <?=l('admin-login-ask-for-password-title');?></a>
								</div>
							</div>
						</form>
					</div>

				</div>
			</div>

			<div class="m-t-40 text-center">
				<p class="text-muted">© <?=date('Y');?> Anelis Network</p>
			</div>

		</div>

		<!-- MODAL PARA SOLICITAR UNA NUEVA CONTRASEÑA -->
        <div class="modal fade" id="askForNewPassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><?=l('admin-login-ask-for-password-title');?></h5>
                        <button type="button" id="closeModal" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form method="post" action="<?=_DOMINIO_._ADMIN_;?>">
                            <div class="form-group">
                                <label class="mb-0" for="password_email"><?=l('admin-login-ask-for-password-email');?></label>
                                <input type="text" name="password_email" style="border: 1px solid #ccc;" value="" id="password_email" class="form-control">
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-success" name="submitAskForNewPassword"><?=l('admin-solicitar');?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

		<!-- END wrapper -->
			
		<?php include(_INCLUDES_._ADMIN_.'javascript_bottom.php'); ?>
		<script>
			$(document).ready(function()
			{
				$('#email').focus();
			});
		</script>
	</body>

</html>
