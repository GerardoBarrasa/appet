<li class="nav-item dropdown">
    <a class="nav-link" data-bs-toggle="dropdown" href="#">
        <i class="btn btn-sm btn-outline-primary rounded-circle fa fa-user mr-2"></i>
        <?=$_SESSION['admin_panel']->nombre?> <?=$_SESSION['admin_panel']->apellidos?>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
            <i class="fas fa-user-gear mr-2"></i>
            <span class="float-right text-muted text-sm">Mi cuenta</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="<?=_DOMINIO_.$_SESSION['admin_vars']['entorno']?>logout/" class="dropdown-item">
            <i class="fas fa-person-walking-dashed-line-arrow-right mr-2"></i>
            <span class="float-right text-muted text-sm">Salir</span>
        </a>
    </div>
</li>
