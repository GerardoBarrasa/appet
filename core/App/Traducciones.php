<?php

class Traducciones
{
	public static $traducciones = array();

	public static function checkShortcodeExists($shortcode, $ignore_id = false)
	{
		$where_not_id = "";
		if( !empty($ignore_id) )
			$where_not_id = " AND id_traduccion != ".(int)$ignore_id;
		$result = Bd::getInstance()->countRows("SELECT id_traduccion FROM traducciones WHERE shortcode = '".$shortcode."'".$where_not_id);
		return empty($result) ? false : true;
	}

	public static function crearTraduccion($shortcode, $id_lang, $texto)
	{
		$bdInstance = Bd::getInstance();
		$addTraduccion = array(
			'shortcode' => $shortcode
		);

		$bdInstance->insert('traducciones', $addTraduccion);
		$id_traduccion = $bdInstance->lastId();

		$idiomas = Idiomas::getLanguages();
		foreach( $idiomas as $idioma )
		{
			$addTraduccionIdioma = array(
				'id_traduccion' => $id_traduccion,
				'id_lang' => $idioma->id,
				'texto' => ''
			);
			if( $idioma->id == $id_lang )
				$addTraduccionIdioma['texto'] = $texto;

			$bdInstance->insert('idiomas_traducciones', $addTraduccionIdioma);
		}

		return $id_traduccion;
	}

	public static function actualizarTraduccion($id_traduccion, $shortcode, $textos)
	{
		Bd::getInstance()->update('traducciones', array('shortcode' => $shortcode), 'id_traduccion = '.(int)$id_traduccion);

		foreach( $textos as $id_lang => $texto )
			Bd::getInstance()->update('idiomas_traducciones', array('texto' => $texto), 'id_traduccion = '.(int)$id_traduccion.' AND id_lang = '.(int)$id_lang);
	}

	public static function getTraduccionById($id_traduccion)
	{
		$result = Bd::getInstance()->fetchObject("SELECT t.*, it.* FROM traducciones t LEFT JOIN idiomas_traducciones it ON it.id_traduccion = t.id_traduccion WHERE t.id_traduccion = ".(int)$id_traduccion);
		$finalResult = new stdClass();
		if( !empty($result) )
		{
			foreach( $result as $res )
			{
				$finalResult->id_traduccion = $res->id_traduccion;
				$finalResult->shortcode = $res->shortcode;
				$finalResult->traducciones[$res->id_lang] = $res->texto;
			}
		}
		return $finalResult;
	}

	public static function getStatsTraduccionesByIdioma($id_lang)
	{
		$traduccionesTotales = Bd::getInstance()->countRows("SELECT id_traduccion FROM traducciones");
		$traduccionesRealizadasIdioma = Bd::getInstance()->countRows("SELECT id_traduccion FROM idiomas_traducciones WHERE texto != '' AND id_lang = ".(int)$id_lang);
		return round(($traduccionesRealizadasIdioma * 100) / $traduccionesTotales);
	}

	public static function getTraduccionesByIdioma($id_lang)
	{
		return Bd::getInstance()->fetchObject("SELECT t.shortcode, it.texto FROM traducciones t LEFT JOIN idiomas_traducciones it ON it.id_traduccion = t.id_traduccion AND it.id_lang = ".(int)$id_lang);
	}

	public static function getTraduccionesWithFiltros($comienzo, $limite, $applyLimit=true)
	{
		$busqueda = Tools::getValue('busqueda', '');
		$translation_status	= Tools::getValue('translation_status');
		$id_lang = Tools::getValue('id_lang');
		$search = "";
		$limit = "";

		if( $busqueda != '' )
			$search .= " AND (t.shortcode LIKE '%".$busqueda."%' OR it.texto LIKE '%".$busqueda."%')";

		if( !empty($translation_status) )
			$search .= " AND it.texto = ''";

		if( !empty($id_lang) )
			$search .= " AND it.id_lang = ".(int)$id_lang;

		if($applyLimit)
			$limit = "LIMIT $comienzo, $limite";

		$listado = Bd::getInstance()->fetchObject("SELECT t.*, it.*, i.icon FROM traducciones t LEFT JOIN idiomas_traducciones it ON it.id_traduccion = t.id_traduccion LEFT JOIN idiomas i ON i.id = it.id_lang WHERE 1=1 $search ORDER BY t.id_traduccion ASC, it.id_lang ASC $limit");

		$total = Bd::getInstance()->countRows("SELECT t.*, it.*, i.icon FROM traducciones t LEFT JOIN idiomas_traducciones it ON it.id_traduccion = t.id_traduccion LEFT JOIN idiomas i ON i.id = it.id_lang WHERE 1=1 $search ORDER BY t.id_traduccion ASC, it.id_lang ASC");

		return array(
			'listado' => $listado,
			'total' => $total
		);
	}

	public static function getShortcodes()
	{
		return Bd::getInstance()->fetchObject("SELECT * FROM traducciones WHERE 1=1 ORDER BY id_traduccion");
	}

	public static function regenerarCacheTraduccionesByIdioma($id_lang, $file_path)
	{
		$traducciones = self::getTraduccionesByIdioma($id_lang);

		$traduccionesFinales = array();
		foreach( $traducciones as $traduccion )
			$traduccionesFinales[$traduccion->shortcode] = $traduccion->texto;

		file_put_contents($file_path, gzcompress(serialize($traduccionesFinales)));
	}

	public static function loadTraducciones($id_lang)
	{
		$slug = Bd::getInstance()->fetchValue("SELECT slug FROM idiomas WHERE id = ".(int)$id_lang);
		if( file_exists(_PATH_.'translations/'.$slug.'.php') )
		{
			$file_contents = file_get_contents(_PATH_.'translations/'.$slug.'.php');
			$traducciones = unserialize(gzuncompress($file_contents));
			self::$traducciones = $traducciones;
		}
		else
			die('Traducciones::loadTraducciones() idioma incorrecto: '.$slug);
	}

	public static function getTextoByShortcodeIdioma($shortcode, $id_lang)
	{
		return Bd::getInstance()->fetchValue("SELECT it.texto FROM idiomas_traducciones it LEFT JOIN traducciones t ON t.id_traduccion = it.id_traduccion AND t.shortcode = '".$shortcode."' WHERE it.id_lang = ".(int)$id_lang);
	}
}
