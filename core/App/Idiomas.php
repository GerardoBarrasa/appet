<?php

class Idiomas
{
	public static function setLanguage()
	{
		if( !isset($_SESSION['lang']) || empty($_SESSION['lang']) )
		{
			if( !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
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

	public static function getIdiomasWithFiltros($comienzo, $limite, $applyLimit=true)
	{
		$search = "";
		$limit = "";

		if($applyLimit)
			$limit = "LIMIT $comienzo, $limite";

		return Bd::getInstance()->fetchObject("SELECT * FROM idiomas WHERE 1=1 ORDER BY nombre ASC $limit");
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

				if( count($datosLang) > '0' && $id == '0' )
				{
					$msg = "La abreviatura que indicas ya está siendo usada en el idioma <strong>" . $datosLang[0]->nombre . "</strong>. <a href='"._ADMIN_."administrar-idioma/".$datosLang[0]->id."/' class='text-white'><u>Editar idioma</u></a>";
				}

				//Comprobamos color si no hay errores.
				if( $msg == 'ok' )
				{
					if( empty($upd['colour']) )
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
				$ruta_img = Tools::uploadImage(_RESOURCES_PATH_._COMMON_.'img/flags/', 'icon', $upd['slug'].'-flag-'.time());

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
					$shortcodes = Traducciones::getShortcodes();
					foreach( $shortcodes as $shortcode )
					{
						$addTraduccionIdioma = array(
							'id_traduccion' => $shortcode->id_traduccion,
							'id_lang' => $createdLanguage,
							'texto' => ''
						);

						Bd::getInstance()->insert('idiomas_traducciones', $addTraduccionIdioma);
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
}
