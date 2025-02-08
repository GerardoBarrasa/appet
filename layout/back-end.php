<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content=" user-scalable=no, width=device-width, initial-scale=1, minimum-scale=1">
    <link rel="icon" type="image/x-icon" href="<?=_RESOURCES_._COMMON_?>img/appet_icon.png">
    <?php Metas::getMetas();?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php include(_INCLUDES_._ADMIN_.'stylesheets.php'); ?>
    <script type="text/javascript">
        const dominio = "<?=_DOMINIO_;?>";
        const static_token = "<?=!empty($_SESSION['token']) ? $_SESSION['token'] : '';?>";
    </script>
    <?php include(_INCLUDES_._ADMIN_.'javascript_top.php'); ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed outfit-appet">
<!-- Site wrapper -->
<div class="wrapper">
    <!-- Navbar -->
    <?php include(_INCLUDES_._ADMIN_.'navbar.php'); ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include(_INCLUDES_._ADMIN_.'sidebar.php'); ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <?php include(_INCLUDES_._ADMIN_.'header.php'); ?>
        <?php Render::getAdminPage();?>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <?php include(_INCLUDES_._ADMIN_.'footer.php'); ?>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<?php include(_INCLUDES_._ADMIN_.'javascript_bottom.php'); ?>
</body>
</html>
