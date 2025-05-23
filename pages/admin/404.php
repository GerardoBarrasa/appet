<div class="error-page mt-0 pt-5">
    <h2 class="headline text-secondary"> 404</h2>

    <div class="error-content">
        <h3><i class="fas fa-exclamation-triangle text-secondary"></i> Oops! No hemos encontrado la página.</h3>

        <p>
            No hemos podido encontrar la página que estás buscando.
            De todas maneras, puedes <a href="<?=_DOMINIO_?>">volver al inicio</a> o <a href="<?=_DOMINIO_.'logout/'?>">volver a acceder</a>.
        </p>

        <form class="search-form">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search">

                <div class="input-group-append">
                    <button type="submit" name="submit" class="btn btn-secondary"><i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <!-- /.input-group -->
        </form>
    </div>
    <!-- /.error-content -->
</div>