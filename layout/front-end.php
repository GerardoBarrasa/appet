<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link rel="icon" type="image/x-icon" href="<?=_ASSETS_;?>img/favicon.ico">
	<?php Metas::getMetas();?>
	<?php include(_INCLUDES_.'stylesheets.php'); ?>
	<script type="text/javascript">
		const dominio = "<?=_DOMINIO_;?>";
		const static_token = "<?=!empty($_SESSION['token']) ? $_SESSION['token'] : '';?>";
	</script>
	<?php include(_INCLUDES_.'javascript_top.php'); ?>
</head>
<body id="<?=$page_name;?>">
	<main>
		<?php Render::getPage();?>
	</main>
	<?php include(_INCLUDES_.'javascript_bottom.php'); ?>
</body>  
</html>
