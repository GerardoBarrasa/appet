<body class="login-page dark-mode" style="min-height: 332.781px;">
<div class="login-box">
    <div class="login-logo">
        <a href="../../index2.html"><b>ApPet</b></a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
            <form action="recover-password.html" method="post">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Request new password</button>
                    </div>

                </div>
            </form>
            <div class="text-center">
            <p class="mt-3 mb-1">
                <a href="<?=_DOMINIO_._ADMIN_;?>login/">Login</a>
            </p>
            <p class="mb-0">
                <a href="<?=_DOMINIO_._ADMIN_;?>register/" class="text-center">Register a new membership</a>
            </p>
            </div>
        </div>

    </div>
</div>


<script src="<?=_ASSETS_._ADMIN_;?>plugins/jquery/jquery.min.js"></script>

<script src="<?=_ASSETS_._ADMIN_;?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="<?=_ASSETS_._ADMIN_;?>dist/js/adminlte.min.js?v=3.2.0"></script>


</body>