<?php

class Slugs
{
	public static function getPagesFromSlugs()
	{
        return Bd::getInstance()->fetchObject("SELECT DISTINCT(mod_id) FROM slugs ORDER BY mod_id ASC");
    }

    //Funcion que devuelve los slugs filtrados
    public static function getSlugsFiltered($comienzo, $limite, $applyLimit=true)
    {
        //Obtenemos idioma default.
        $lang = Idiomas::getDefaultLanguage();

        //Obtenemos variables de filtros
        $filter_busqueda    = (isset($_REQUEST['busqueda'])) ? Tools::getValue('busqueda', '') : '';
        $filter_page        = (isset($_REQUEST['page'])) ? Tools::getValue('page', '') : '';
        $filter_id_language = (isset($_REQUEST['id_idioma'])) ? Tools::getValue('id_idioma', '') : '';

        $wherePage = '';
        if($filter_page != '')
            $wherePage = " AND mod_id = '".$filter_page."'";

        $whereLang = '';
        if($filter_id_language != '')
            $whereLang = " AND id_language = '".$filter_id_language."'";
        else{
            if($filter_page == '' && $filter_busqueda == '')
                $whereLang = " AND id_language = '".$lang->id."'";
        }

        $whereBusqueda = '';
        if( $filter_busqueda != '' )
            $whereBusqueda = " AND (slug LIKE '%".$filter_busqueda."%' OR title LIKE '%".$filter_busqueda."%' OR description LIKE '%".$filter_busqueda."%')";

        if($applyLimit)
            $limit = "LIMIT $comienzo, $limite";
        else
            $limit = "";

        $datos = Bd::getInstance()->fetchObject("
            SELECT *
            FROM slugs
            WHERE 1=1
            ".$whereBusqueda."
            ".$wherePage."
            ".$whereLang."
            ORDER BY creation_date DESC
            $limit
        ");

        //Recorremos los destinos obtenidos para revisar si tiene traduccion de los diveross idiomas.
        if(count($datos) > 0){

            //Obtenemos todos los idiomas y simplemente vamos a comprobar si tiene traduccion para cada idioma.
            $languages = Idiomas::getLanguages();

            foreach($datos as $key => $slug)
            {
                $slug->langs = [];

                //Para cada destino, comprobamos si tiene traduccion de un idioma especifico.
                foreach($languages as $key => $lang){
                    if(Bd::getInstance()->countRows("SELECT id FROM slugs WHERE mod_id = '".$slug->mod_id."' AND id_language = '".$lang->id."'") == '1')
                        $slug->langs[$lang->slug] = true;
                    else
                        $slug->langs[$lang->slug] = false;
                }
            }
        }

        return $datos;
    }

    //Funcion que devuelve los datos de una pagina segun el SLUG
	public static function getPageDataBySlug($slug)
	{
		//Buscamos la pagina solo si existe idioma session
		if(isset($_SESSION['lang']))
		{
			//Buscamos la pagina
			$datos = Bd::getInstance()->fetchRow('
				SELECT *
				FROM slugs
				WHERE status = "active"
				AND slug = "'.$slug.'"
				AND id_language = (
					SELECT id
					FROM idiomas
					WHERE slug = "'.$_SESSION['lang'].'"
				)
			');

			if(!empty($datos))
				return $datos;
			else
				return false;
		}
		else
			return false;
	}

    //Funcion que devuelve los datos de una pagina segun el MOD_ID
    public static function getPageDataByModId($mod_id)
    {
        //Buscamos la pagina solo si existe idioma session
        if(isset($_SESSION['lang']))
        {
            //Buscamos la pagina
            $datos = Bd::getInstance()->fetchRow('
                SELECT *
                FROM slugs
                WHERE status = "active"
                AND mod_id = "'.$mod_id.'"
                AND id_language = (
                    SELECT id
                    FROM idiomas
                    WHERE slug = "'.$_SESSION['lang'].'"
                )
            ');

            if(!empty($datos))
                return $datos;
            else
                return false;
        }
        else
            return false;
    }

	public static function getById($id)
	{
        return Bd::getInstance()->fetchRow("
            SELECT *
            FROM slugs
            WHERE id = '".$id."'
        ");
    }

    public static function checkIfSlugIsAvailable($slug, $id_language, $id="")
    {
        $where = '';
        if($id != '')
            $where = " AND id != '".$id."'";

        $sql = "
            SELECT id
            FROM slugs
            WHERE slug = '".$slug."'
            AND id_language = '".$id_language."'
            ".$where."
        ";

        if(Bd::getInstance()->countRows($sql) == '1')
            return false;
        else
            return true;
    }

    //Funcion que comprueba si la pagina tiene ya un slug de traduccion.
    public static function checkIfPageIsAvailableForLanguage($page, $id_language, $id="")
    {
        $where = '';
        if($id != '')
            $where = " AND id != '".$id."'";

        $sql = "
            SELECT id
            FROM slugs
            WHERE mod_id = '".$page."'
            AND id_language = '".$id_language."'
            ".$where."
        ";

        if(Bd::getInstance()->countRows($sql) == '1')
            return false;
        else
            return true;
    }

    public static function getModBySlug($slug)
    {
        //Buscamos la pagina solo si existe idioma session
        if(isset($_SESSION['lang']))
        {
            //Buscamos la pagina
            $datos = Bd::getInstance()->fetchRow('
                SELECT mod_id
                FROM slugs
                WHERE status = "active"
                AND slug = "'.$slug.'"
                AND id_language = (
                    SELECT id
                    FROM idiomas
                    WHERE slug = "'.$_SESSION['lang'].'"
                )
            ');

            if(!empty($datos))
                return $datos;
            else
                return false;
        }
        else
            return false;
    }

    //Funcion que devuelve el slug, en base al idioma de sesion y un "mod_id".
    public static function getSlugByModId($mod_id)
    {
        //Buscamos la pagina solo si existe idioma session
        if(isset($_SESSION['lang']))
        {
            //Buscamos la pagina
            $datos = Bd::getInstance()->fetchRow('
                SELECT slug
                FROM slugs
                WHERE status = "active"
                AND mod_id = "'.$mod_id.'"
                AND id_language = (
                    SELECT id
                    FROM idiomas
                    WHERE slug = "'.$_SESSION['lang'].'"
                )
            ');

            if(!empty($datos))
                return $datos->slug;
            else
                return false;
        }
        else
            return false;
    }

    public static function getCurrentSlugByModId($mod_id, $prefix_domain = true)
    {
        $slug = self::getSlugByModId($mod_id);
        return !empty($slug) ? ($prefix_domain ? _DOMINIO_ : '').$_SESSION['lang'].'/'.$slug.'/' : $slug;
    }

    //Funcion que devuelve el slug traducido al idioma.
    public static function getSlugCompleteForIdLang($lang)
    {
        $slug_complete = $lang->slug;

        if(isset($_REQUEST['mod']) && $_REQUEST['mod'] != '')
        {
            //Obtenemos el MOD_ID a traves del slug de la pagina.
            $dataPage = self::getPageDataBySlug($_REQUEST['mod']);

            if($dataPage)
            {
                $mod_id = $dataPage->mod_id;

                $datos = Bd::getInstance()->fetchRow('
                    SELECT id, mod_id, slug
                    FROM slugs
                    WHERE status = "active"
                    AND mod_id = "'.$mod_id.'"
                    AND id_language = "'.$lang->id.'"
                ');

                //Si existe mod, entonces analizaremos cual es para tratar su URL.
                if($datos)
                {
                    switch ($datos->mod_id)
                    {
                        default:
                            if(isset($datos->slug) && $datos->slug != '')
                                $slug_complete .= "/" . $datos->slug;

                            break;
                    }
                }
            }
        }

        return $slug_complete;
    }
}
