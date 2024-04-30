<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php Metas::getMetas();?>

    <?php include(_INCLUDES_._ADMIN_.'stylesheets.php'); ?>
    <script type="text/javascript">
        const dominio = "<?=_DOMINIO_;?>";
        const static_token = "<?=!empty($_SESSION['token']) ? $_SESSION['token'] : '';?>";
    </script>
    <?php include(_INCLUDES_._ADMIN_.'javascript_top.php'); ?>

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
<?php include(_INCLUDES_._ADMIN_.'javascript_bottom.php'); ?>

</body>
</html>
