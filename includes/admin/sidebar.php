<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?=_DOMINIO_._ADMIN_?>" class="brand-link">
        <img src="<?=_RESOURCES_._COMMON_?>img/appet_logotipo.png" alt="ApPet Logo" class="brand-image img-circle elevation-3 bg-light p-1" style="background-color: #FFFFFF !important;">
        <span class="brand-text font-weight-light">Appet</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?=_RESOURCES_._ADMIN_?>img/default-150x150.png" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="<?=_DOMINIO_._ADMIN_?>mi-cuenta/" class="d-block">Alexander Pierce</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item <?=Render::$page == 'home' || Render::$page == 'propietarios' || Render::$page == 'mascotas' ? ' menu-is-opening menu-open' : ''?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Clientes
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?=_DOMINIO_._ADMIN_?>propietarios/" class="nav-link">
                                <i class="fa fa-user-alt nav-icon"></i>
                                <p>Propietarios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../../index2.html" class="nav-link">
                                <i class="fa fa-dog nav-icon"></i>
                                <p>Mascotas</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>