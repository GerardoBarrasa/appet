<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?=_DOMINIO_._ADMIN_?>" class="brand-link">
        <img src="<?=Admin::getEntornoLogo()?>" alt="<?=$_SESSION['admin_panel']->cuidador_nombre?>" class="brand-image img-circle elevation-3 bg-light p-1" style="background-color: #FFFFFF !important;">
        <span class="brand-text font-weight-light"><?=$_SESSION['admin_panel']->cuidador_nombre?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item<?=Render::$page == 'mascotas'? ' active' : ''?>">
                    <a href="<?=_DOMINIO_.$_SESSION['admin_vars']['entorno']?>mascotas/" class="nav-link">
                        <i class="fa fa-dog nav-icon"></i>
                        <p>Mascotas</p>
                    </a>
                </li>
                <li class="nav-item <?=Render::$page == 'propietarios'? ' active' : ''?>">
                    <a href="<?=_DOMINIO_.$_SESSION['admin_vars']['entorno']?>propietarios/" class="nav-link">
                        <i class="fa fa-user-alt nav-icon"></i>
                        <p>Tutores</p>
                    </a>
                </li>
                <?php
                // Ejemplo de uso de permisos en las vistas
                // Solo mostrar enlaces si el usuario tiene los permisos correspondientes

                if (Permisos::tienePermiso('ACCESS_USUARIOS_ADMIN')): ?>
                    <li class="nav-item">
                        <a href="<?= _DOMINIO_ . $_SESSION['admin_vars']['entorno'] ?>usuarios-admin/" class="nav-link">
                            <i class="fas fa-users nav-icon"></i>
                            <p>Usuarios Admin</p>
                        </a>
                    </li>
                <?php endif;

                if (Permisos::tienePermiso('ACCESS_PERMISOS')): ?>
                    <li class="nav-item">
                        <a href="<?= _DOMINIO_ . $_SESSION['admin_vars']['entorno'] ?>permisos/" class="nav-link">
                            <i class="fas fa-shield-alt nav-icon"></i>
                            <p>Permisos</p>
                        </a>
                    </li>
                <?php endif;

                if (Permisos::tienePermiso('ACCESS_IDIOMAS')): ?>
                    <li class="nav-item">
                        <a href="<?= _DOMINIO_ . $_SESSION['admin_vars']['entorno'] ?>idiomas/" class="nav-link">
                            <i class="fas fa-language nav-icon"></i>
                            <p>Idiomas</p>
                        </a>
                    </li>
                <?php endif;

                // Ejemplo de verificación de perfil
                if (Permisos::esSuperAdmin()): ?>
                    <li class="nav-item">
                        <a href="<?= _DOMINIO_ . $_SESSION['admin_vars']['entorno'] ?>configuracion/" class="nav-link">
                            <i class="fas fa-cog nav-icon"></i>
                            <p>Configuración</p>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
