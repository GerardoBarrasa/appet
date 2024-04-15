<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Dashboard 2</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?=_ASSETS_._ADMIN_;?>plugins/fontawesome-free/css/all.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?=_ASSETS_._ADMIN_;?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=_ASSETS_._ADMIN_;?>dist/css/adminlte.min.css">
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__wobble" src="<?=_ASSETS_._ADMIN_;?>dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
    </div>

    <!-- Navbar -->
    <?php include(_INCLUDES_._ADMIN_.'header.php'); ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include(_INCLUDES_._ADMIN_.'left_column.php'); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <?php include(_INCLUDES_._ADMIN_.'breadcrumb.php'); ?>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
                <?php Render::getAdminPage();?>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <?php include(_INCLUDES_._ADMIN_.'footer.php'); ?>

    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="<?=_ASSETS_._ADMIN_;?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="<?=_ASSETS_._ADMIN_;?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?=_ASSETS_._ADMIN_;?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=_ASSETS_._ADMIN_;?>dist/js/adminlte.js"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="<?=_ASSETS_._ADMIN_;?>plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
<script src="<?=_ASSETS_._ADMIN_;?>plugins/raphael/raphael.min.js"></script>
<script src="<?=_ASSETS_._ADMIN_;?>plugins/jquery-mapael/jquery.mapael.min.js"></script>
<script src="<?=_ASSETS_._ADMIN_;?>plugins/jquery-mapael/maps/usa_states.min.js"></script>
<!-- ChartJS -->
<script src="<?=_ASSETS_._ADMIN_;?>plugins/chart.js/Chart.min.js"></script>

<!-- AdminLTE for demo purposes -->
<script src="<?=_ASSETS_._ADMIN_;?>dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=_ASSETS_._ADMIN_;?>dist/js/pages/dashboard2.js"></script>
</body>
</html>
