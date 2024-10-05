<?php

class Idiomas
{
	protected static $_initialized = false;
	protected static $_cache_ids = null;
	protected static $_cache_slugs = null;

	public static function loadIdiomas()
	{
		$idiomas = Bd::getInstance()->fetchObject('SELECT * FROM idiomas ORDER BY id ASC');
		foreach( $idiomas as $idioma )
		{
			self::$_cache_ids[$idioma->id] = $idioma;
			self::$_cache_slugs[$idioma->slug] = $idioma;
		}
		self::$_initialized = true;
	}

	public static function setLanguage()
	{
		if( empty($_SESSION['lang']) )
		{
			$defaultLanguage = self::getLanguages(Configuracion::get('default_language'));
			if( !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
			{
				$langNavegador = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
				$idiomasDisponibles = self::getLanguagesVisibles();
				foreach( $idiomasDisponibles as $id )
				{
					if( $id->slug == $langNavegador )
					{
						$_SESSION['lang'] = $id;
						return;
					}
				}
			}
			$_SESSION['lang'] = $defaultLanguage;
		}
	}

	//Funcion que devuelve el total de idiomas
	public static function getLanguages($id="")
	{
		if( !self::$_initialized )
			self::loadIdiomas();

		if( $id == "" )
			return self::$_cache_ids;
		else
			return self::$_cache_ids[$id];
	}

	public static function getLanguagesAdminForm()
	{
		return Bd::getInstance()->fetchObjectWithKey('SELECT * FROM idiomas ORDER BY id ASC', 'id');
	}

	//Funcion que devuelve el total de idiomas
	public static function getLanguagesVisibles()
	{
		if( !self::$_initialized )
			self::loadIdiomas();

		$idiomas = array();
		foreach( self::$_cache_ids as $idioma )
			if( !empty($idioma->visible) )
				$idiomas[] = $idioma;
		return $idiomas;
	}

	//Funcion que devuelve el total de idiomas
	public static function getLanguagesVisiblesArray()
	{
		$languages = self::getLanguagesVisibles();
		$result = array();
		foreach( $languages as $lang )
			$result[] = $lang->slug;
		return $result;
	}

	//Funcion que devuelve idioma
	public static function getLangBySlug($slug)
	{
		if( !self::$_initialized )
			self::loadIdiomas();

		return isset(self::$_cache_slugs[$slug]) ? self::$_cache_slugs[$slug] : false;
	}

	public static function getIdiomasWithFiltros($comienzo, $limite, $applyLimit=true)
	{
		$search = "";
		$limit = "";

		if($applyLimit)
			$limit = "LIMIT $comienzo, $limite";

		return Bd::getInstance()->fetchObject("SELECT * FROM idiomas WHERE 1=1 ORDER BY visible DESC, nombre ASC $limit");
	}

	public static function crearIdioma()
	{
		$ruta_img = Tools::uploadImage('assets/img/flags/', 'icon', Tools::getValue('slug').'-flag-'.time());

		if( $ruta_img['type'] == 'success' )
		{
			$addIdioma = array(
				'nombre' => Tools::getValue('nombre'),
				'slug' => Tools::getValue('slug'),
				'colour' => Tools::getValue('colour'),
				'visible' => isset($_REQUEST['visible']) ? '1' : '0',
				'icon' => $ruta_img['data']
			);

			if( Bd::getInstance()->insert('idiomas', $addIdioma) )
				return Bd::getInstance()->lastId();
		}
		elseif( $ruta_img['type'] == 'error' )
			return false;
		return false;
	}

	public static function actualizarIdioma()
	{
		$datos = self::getLanguages(Tools::getValue('id'));

		$updIdioma = array(
			'nombre' => Tools::getValue('nombre'),
			'slug' => Tools::getValue('slug'),
			'colour' => Tools::getValue('colour'),
			'visible' => isset($_REQUEST['visible']) ? '1' : '0'
		);

		if( isset($_FILES['icon']) && $_FILES['icon']['size'] > '0' )
		{
			$ruta_img = Tools::uploadImage('assets/img/flags/', 'icon', $updIdioma['slug'].'-flag-'.time());

			if( $ruta_img['type'] == 'success' )
			{
				$updIdioma['icon'] = $ruta_img['data'];

				if( isset($datos->icon) && $datos->icon != "" && file_exists(_PATH_.$datos->icon) )
					unlink(_PATH_.$datos->icon);
			}
		}

		if( Bd::getInstance()->update('idiomas', $updIdioma, 'id = '.(int)$datos->id) )
			return true;
		return false;
	}

	public static function getSlugById($id_lang)
	{
		if( !self::$_initialized )
			self::loadIdiomas();
		return self::$_cache_ids[$id_lang]->slug;
	}

	public static function getIDs()
	{
		$idiomas = self::getLanguages();
		return array_keys(self::$_cache_ids);
	}
}
