<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?=_RESOURCES_._COMMON_?>img/appet_icon.png">
    <?php Metas::getMetas();?>
    <?php include(_INCLUDES_._ADMIN_.'stylesheets.php'); ?>
    <?php include(_INCLUDES_._ADMIN_.'javascript_top.php'); ?>
</head>
<body class="hold-transition login-page">
<?php Render::getAdminPage();?>

<?php include(_INCLUDES_._ADMIN_.'javascript_bottom.php'); ?>
</body>
</html>
