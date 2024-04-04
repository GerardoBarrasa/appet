<?php

class Idiomas
{
	public static function setLanguage()
	{
		if( !isset($_SESSION['lang']) || empty($_SESSION['lang']) )
		{
			if( _MULTI_LANGUAGE_ && isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
			{
			    $langNavegador = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
			    $idiomasDisponibles = self::getLanguagesVisiblesArray();
			    $_SESSION['lang'] = in_array($langNavegador, $idiomasDisponibles) ? $langNavegador : _DEFAULT_LANGUAGE_;
			}
			else
				$_SESSION['lang'] = _DEFAULT_LANGUAGE_;
		}
	}

	public static function getDefaultLanguage()
	{
		$datos = Bd::getInstance()->fetchRow('SELECT * FROM idiomas WHERE isDefault = "1"');

		if(!empty($datos))
			return $datos;
		else
			return false;
	}

	//Funcion que devuelve el total de idiomas
	public static function getLanguages($id="")
	{
		if( $id == "" )
			return Bd::getInstance()->fetchObject('SELECT * FROM idiomas ORDER BY id ASC');
		else
		{
			$datos = Bd::getInstance()->fetchObject('SELECT * FROM idiomas WHERE id = "'.$id.'"');

			if( count($datos) == 1 )
				return $datos[0];
			else
				return false;
		}
	}

	public static function getLanguagesAdminForm()
	{
		return Bd::getInstance()->fetchObjectWithKey('SELECT * FROM idiomas ORDER BY id ASC', 'id');
	}

	//Funcion que devuelve el total de idiomas
	public static function getLanguagesVisibles()
	{
		return Bd::getInstance()->fetchObject('SELECT * FROM idiomas WHERE visible = "1" ORDER BY id ASC');
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
	public static function getLangBySlug($slug, $withName=false)
	{
		if(!$withName)
			$datos = Bd::getInstance()->fetchObject('SELECT * FROM idiomas WHERE slug = "'.$slug.'"');
		else
		{
			$datos = Bd::getInstance()->fetchObject('
				SELECT l.*, l.nombre as name
				FROM idiomas l
				WHERE l.slug = "'.$slug.'"
				GROUP BY l.id
			');
		}

		if( count($datos) == 1 )
			return $datos[0];
		else
			return false;
	}

	//Funcion que devuelve todas las traducciones de cada idioma
	public static function getAllTraductionsById($id_idioma, $withContenido=false)
	{
		if( $withContenido )
			return Bd::getInstance()->fetchObject('SELECT * FROM idiomas_traducciones WHERE id_idioma = "'.$id_idioma.'" AND contenido != ""');
		else
			return Bd::getInstance()->fetchObject('SELECT * FROM idiomas_traducciones WHERE id_idioma = "'.$id_idioma.'"');
	}

	//Funcion que en base a idioma y shortcode devuelve su traduccion
	public static function getTraductionByLangShort($id_idioma, $shortcode)
	{
		$datos = Bd::getInstance()->fetchObject('SELECT * FROM idiomas_traducciones WHERE id_idioma = "'.$id_idioma.'" AND shortcode = "'.$shortcode.'"');

		if( count($datos) == 1 )
			return $datos[0];
		else
			return false;
	}

	public static function getIdiomasWithFiltros($comienzo, $limite, $applyLimit=true)
	{
		$search = "";
		$limit = "";

		if($applyLimit)
			$limit = "LIMIT $comienzo, $limite";

		return Bd::getInstance()->fetchObject("SELECT * FROM idiomas WHERE 1=1 ORDER BY nombre ASC $limit");
	}

	//Funcion que va a devolver todas las traducciones agrupadas por shortcode
	public static function getAllTraductionsGroupedFiltered($comienzo, $limite, $applyLimit=true)
	{
		//Recogemos variables
		$busqueda 			= Tools::getValue('busqueda');
		$translation_status	= Tools::getValue('translation_status');
		$slug_idioma 		= Tools::getValue('slug_idioma');
		$traduction_for 	= Tools::getValue('traduction_for');

		if( $traduction_for != "all" )
			$whereTraductionFor = "AND traduction_for = '".$traduction_for."'";
		else
			$whereTraductionFor = "";

		if( $applyLimit )
		{
			$multiplicador = !empty($slug_idioma) ? '1' : count(self::getLanguages());
			$limit = "LIMIT ".$comienzo*$multiplicador.", ".$limite*$multiplicador;
		}
		else
			$limit = "";

		if( !empty($slug_idioma) )
		{
			//Obtenemos el idioma seleccionado
			$datosIdiomaDefault = Bd::getInstance()->fetchObject('SELECT id FROM idiomas WHERE slug = "'.(!empty($slug_idioma) ? $slug_idioma : _DEFAULT_LANGUAGE_).'"');
			$whereIdioma = " AND id_idioma = ".(int)$datosIdiomaDefault[0]->id;
		}
		else
			$whereIdioma = "";

		if( empty($busqueda) && $translation_status == '1' )
		{
			$busqueda = " AND contenido LIKE '%".$busqueda."%'";
		}
		elseif( !empty($busqueda) )
		{
			$busqueda = " AND contenido LIKE '%".$busqueda."%'";
		}

		//Obtenemos todas las traducciones por shortcode del idioma default
		$datosShortcodes = Bd::getInstance()->fetchObject("
			SELECT *
			FROM idiomas_traducciones
			WHERE 1
			$busqueda
			$whereTraductionFor
			$whereIdioma
			ORDER BY shortcode ASC
			$limit
		");
		//Diferentes idiomas
		$datosIdiomas = self::getLanguages();

		//Array principal que contendra cada shortcode y sus traducciones
		$arrayTraductions = array();

		//Comprobamos y recorremos todas las traducciones
		if( count($datosShortcodes) > 0 )
		{
			foreach( $datosShortcodes as $key => $mainTraduction )
			{
				if( !array_key_exists($mainTraduction->shortcode, $arrayTraductions) )
					$arrayTraductions[$mainTraduction->shortcode] = array();

				//Obtenemos datos del idioma de la traduccion
				$idioma = self::getLanguages($mainTraduction->id_idioma);

				if( ($translation_status == '1' && empty($mainTraduction->contenido)) || empty($translation_status) )
					$arrayTraductions[$mainTraduction->shortcode][$idioma->slug] = $mainTraduction;

				if( count($arrayTraductions[$mainTraduction->shortcode]) == '0' )
					unset($arrayTraductions[$mainTraduction->shortcode]);
			}
		}

		return $arrayTraductions;
	}

	//Funcion que devuelve las traducciones agrupadas para un shortcode
	public static function getTraductionGrouped($id)
	{
		//Obtenemos todas las traducciones por shortcode
		$datosShortcodes = Bd::getInstance()->fetchObject('SELECT * FROM idiomas_traducciones WHERE id = "'.$id.'"');

		//Diferentes idiomas
		$datosIdiomas = self::getLanguages();

		//Array principal que contendra cada shortcode y sus traducciones
		$arrayTraductions = [];

		//Comprobamos y recorremos todas las traducciones
		if( count($datosShortcodes) > 0 )
		{
			foreach($datosShortcodes as $key => $mainTraduction)
			{
				//Recorremos los diferentes idiomas para buscar sus textos por shortcode
				foreach($datosIdiomas as $keyI => $idioma)
				{
					//Buscamos su traduccion por shortcode
					$datosTraduction = self::getTraductionByLangShort($idioma->id, $mainTraduction->shortcode);

					//Añadimos la traduccion de ese shortcode en ese array
					$arrayTraductions[$idioma->slug] = $datosTraduction;
				}
			}
		}

		return $arrayTraductions;
	}

	//Funcion que actualiza datos del usuario
	public static function insertTraduction($datos)
	{
		//Añadimos idioma por defecto
		Bd::getInstance()->insert('idiomas_traducciones', $datos);

		$idiomas = self::getLanguages();
		$defaultLanguage = $datos['id_idioma'];

		foreach( $idiomas as $i )
		{
			if( $i->id != $defaultLanguage )
			{
				$datos['id_idioma'] = $i->id;
				$datos['contenido'] = '';
				Bd::getInstance()->insert('idiomas_traducciones', $datos);
			}
		}
	}

	//Funcion que actualiza datos del usuario
	public static function updateTraduction($id, $datos)
	{
		Bd::getInstance()->update('idiomas_traducciones', $datos, 'id = "'.$id.'"');
	}

	//Funcion que crea/actualiza un idioma
	public static function administrarIdioma()
	{
		//ID producto
		$id = Tools::getValue('id');
		$msg = "ok";

		//Obtenemos los datos del idioma (si es update)
		if( $id != '0' )
			$datos = self::getLanguages($id);

		$upd['nombre'] 			= Tools::getValue('nombre');
		$upd['slug'] 			= Tools::getValue('slug');
		$upd['colour'] 			= Tools::getValue('colour');
		$upd['visible'] 		= (isset($_REQUEST['visible'])) ? '1' : '0';

		if( $upd['nombre'] != "" )
		{
			if( $upd['slug'] != "" )
			{
				//Vamos a buscar otros idiomas con mismo slug
				$datosLang = Bd::getInstance()->fetchObject('SELECT * FROM idiomas WHERE slug = "'.$upd['slug'].'"');

				if( count($datosLang) == '0' )
				{
					//DO NOTHING
				}
				else
				{
					if( $id == '0' )
						$msg = "La abreviatura que indicas ya está siendo usada en el idioma <strong>" . $datosLang[0]->nombre . "</strong>. <a href='"._ADMIN_."administrar-idioma/".$datosLang[0]->id."/' class='text-white'><u>Editar idioma</u></a>";
				}

				//Comprobamos color si no hay errores.
				if( $msg == 'ok' )
				{
					if( $upd['colour'] != "" )
					{
						//DO NOTHING
					}
					else
						$msg = "Especifica el color, coge por defecto <strong>primary</strong> si no sabes cual poner.";
				}
			}
			else
				$msg = "Debes indicar la abreviatura como mínimo para que el idioma sea funcional.";
		}
		else
			$msg = "Debes indicar el nombre del idioma";

		//Comprobamos si no tiene imagen destacada ya que sera obligatoria que suba
		if( $msg == 'ok' )
		{
			if( isset($_FILES['icon']) && $_FILES['icon']['size'] > '0' )
			{
				$imagenes 	= $_FILES['icon'];
				$ruta_img = Tools::uploadImage('assets/img/flags/', 'icon', $upd['slug'].'-flag-'.time());

				//Guardamos las imagenes en la BD.
				if( $ruta_img['type'] == 'success' )
				{
					$upd['icon'] 	= $ruta_img['data'];

					//Comrpobamos si tenia imagen destacada para eliminarla
					if( isset($datos->icon) && $datos->icon != "" && file_exists(_PATH_.$datos->icon) )
						unlink(_PATH_.$datos->icon);

					$msg = "ok";
				}
				elseif( $ruta_img['type'] == 'error' )
					$msg = $ruta_img['error'];
			}
			else
			{
				if( $id == '0' )
					$msg = "No se ha seleccionado ninguna imagen de bandera y es obligatoria.";
			}
		}

		//Solo si es OK actualizamos
		if( $msg == "ok" )
		{
			if( $id == '0' )
			{
				if( Bd::getInstance()->insert('idiomas', $upd) )
				{
					//Generamos todas las traducciones en blanco para el nuevo idioma
					$createdLanguage = Bd::getInstance()->lastId();
					$defaultLang = self::getLangBySlug(_DEFAULT_LANGUAGE_);
					$traducciones = self::getAllTraductionsByIdGroupedShortcode($defaultLang->id);

					foreach( $traducciones as $trad )
					{
						$dummyTrad = array(
							'id_idioma' => $createdLanguage,
							'traduction_for' => $trad->traduction_for,
							'shortcode' => $trad->shortcode,
							'contenido' => '',
							'fecha_creation' => Tools::datetime(),
							'fecha_creation' => Tools::datetime()
						);
						Bd::getInstance()->insert('idiomas_traducciones', $dummyTrad);
						unset($dummyTrad);
					}
					return "ok";
				}
				else
					return "Ha ocurrido un error interno al intentar crear el idioma. Inténtalo de nuevo y si el problema persiste comunícalo.";
			}
			else
			{
				//Si es update, y ha subido imagen, debemos actualizarla.
				if( isset($upd['icon']) && $upd['icon'] != "" )
				{

					//Eliminamos la imagen que ya tenia
					if( isset($datos->icon) && $datos->icon != "" && file_exists(_PATH_.$datos->icon) )
						unlink(_PATH_.$datos->icon);
				}

				if( Bd::getInstance()->update('idiomas', $upd, "id = '".$id."'") )
					return "ok";
				else
					return "Ha ocurrido un error interno al intentar guardar el idioma. Inténtalo de nuevo y si el problema persiste comunícalo.";
			}
		}
		else
			return $msg;
	}

	//Funcion que devuelve las traducciones en funcion de la url y language session
	public static function getTranslationFor($slug, $slug_language='')
	{
		if( empty($slug_language) )
			$slug_language = $_SESSION['lang'];

		//En base al slug obtenemos el id_idioma
		$datos_idioma = self::getLangBySlug($slug_language);

		$datosTraduction = Bd::getInstance()->fetchObject('SELECT * FROM idiomas_traducciones where traduction_for = "'.$slug.'" AND id_idioma = "'.$datos_idioma->id.'"');

		$arrayOfTraduction = [];

		//Recorremos y devolvemos el mismo array pero con shortcode como key
		foreach( $datosTraduction as $key => $traduction )
			$arrayOfTraduction[$traduction->shortcode] = $traduction;

		return $arrayOfTraduction;
	}

	//Funcion que devuelve las traducciones en funcion del slug y language session
	public static function getTranslationsByIdIdiomaFor($slug, $id_idioma=false)
	{
		if( empty($id_idioma) )
		{
			$datos_idioma = self::getLangBySlug($_SESSION['lang']);
			$id_idioma = $datos_idioma->id;
		}

		$datosTraduction = Bd::getInstance()->fetchObject('SELECT * FROM idiomas_traducciones where traduction_for = "'.$slug.'" AND id_idioma = "'.$id_idioma.'"');

		$arrayOfTraduction = [];

		//Recorremos y devolvemos el mismo array pero con shortcode como key
		foreach( $datosTraduction as $key => $traduction )
			$arrayOfTraduction[str_ireplace($slug.'-', '', $traduction->shortcode)] = $traduction->contenido;

		return $arrayOfTraduction;
	}

	//Funcion que devuelve solo los shortcodes agrupados 
	public static function getTraductionsForGrouped()
	{
		return Bd::getInstance()->fetchObject("SELECT traduction_for FROM idiomas_traducciones WHERE id_idioma = '1' GROUP BY traduction_for");
	}

	//Devuelve una cadena segun el shortcode, la zone de traduccion y el idioma
	public static function getTranslation($shortcode, $traduction_for, $id_lang=false)
	{
		if( !$id_lang )
		{
			$currentLang = self::getLangBySlug(_DEFAULT_LANGUAGE_);
			$id_lang = $currentLang->id;
		}
		$traduccion = Bd::getInstance()->fetchRow("SELECT contenido FROM idiomas_traducciones WHERE traduction_for =  '".$traduction_for."' AND shortcode = '".$shortcode."' AND id_idioma = ".(int)$id_lang." LIMIT 1");
		return (!empty($traduccion) && !empty($traduccion->contenido) ? $traduccion->contenido : "untranslated");
	}
	
	public static function loadPageTranslations($page, $prefix='', $lang='')
	{
		if( $page == '' )
			$page = 'home';

		if( $prefix != '' )
			$page = $prefix.'_'.$page;

		$_SESSION['traducciones'] = array_merge(self::getTranslationFor($page, $lang), self::getTranslationFor('cabecera', $lang), self::getTranslationFor('pie', $lang));
	}

	public static function getAllTraductionsByIdGroupedShortcode($id_idioma)
	{
		$traducciones = self::getAllTraductionsById($id_idioma);
		$result = array();
		if( !empty($traducciones) )
			foreach( $traducciones as $t )
				$result[$t->shortcode] = $t;
		return $result;
	}
}
