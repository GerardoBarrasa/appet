<?php

class Metas
{
	public static $title = _TITULO_;
	public static $description = _TITULO_;

	public static function getMetas()
	{
		?>
		<title><?=self::$title?></title>
		<meta name="description" http-equiv="description" content="<?=self::$description?>" />
		<?php
	}
}
