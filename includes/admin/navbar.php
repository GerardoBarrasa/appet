<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?=_DOMINIO_.$_SESSION['admin_vars']['entorno']?>contacto/" class="nav-link">Contacto</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <?php //include(_INCLUDES_._ADMIN_.'navbar_search.php'); ?>

        <!-- Messages Dropdown Menu -->
        <?php //include(_INCLUDES_._ADMIN_.'navbar_messages.php'); ?>

        <!-- Notifications Dropdown Menu -->
        <?php //include(_INCLUDES_._ADMIN_.'navbar_notifications.php'); ?>

        <!-- Profile Dropdown Menu -->
        <?php include(_INCLUDES_._ADMIN_.'navbar_profile.php'); ?>
    </ul>
</nav>