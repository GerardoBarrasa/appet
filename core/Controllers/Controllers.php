<?php

class Controllers
{
	private $page;
	private $rendered = false;

	//Controlador por defecto
	var $defaultController = 'default';

	//Leemos controlador
	public function load()
	{
		unset($_SESSION['js_paths']);
		unset($_SESSION['css_paths']);

		//Controlador
		$controller = Tools::getValue('controller', $this->defaultController);

		if( $controller == 'default' || $controller == 'admin' )
		{
			if( $controller == 'default' )
			{
				//Controlamos redirecciones a URLs con idiomas
				$this->handleRedirectionDefaultController();
			}

			if( empty($_SESSION['token']) )
				$_SESSION['token'] = Tools::passwdGen(32);
		}

		//Pagina
		if( isset($_GET['mod']) )
			$page = Tools::getValue('mod');
		else
			$page = '';

		//Si no existe controlador establecemos default y mostramos 404
		if( !class_exists(ucfirst($controller).'Controller') )
		{
			$controller = 'default';
			$page = '404';
		}

		if( (($controller == 'ajax' || $controller == 'adminajax') && $_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SESSION['token']) || $_SESSION['token'] != Tools::getValue('token')) )
		)
		{
			header('HTTP/1.1 403 Forbidden');
			exit;
		}

		//Comprobamos custom page
		if( $controller == 'default' && $page != '' )
		{
			//Buscamos el SLUG en la BBDD.
			$data_slug = Slugs::getModBySlug($page);

			//Si existe el slug cambiamos el valor de page al MOD_ID
			if(!empty($data_slug) && isset($data_slug->mod_id) && $data_slug->mod_id != '')
				$page = $data_slug->mod_id;
			else
				$page = '404';
		}

		//Ejecutamos controlador
		$controller_name = ucfirst($controller).'Controller';
		$current_controller = new $controller_name();
		$current_controller->setPage($page);
		if( _MULTI_LANGUAGE_ )
			$current_controller->loadTraducciones();
		$current_controller->execute($page);
	}

	private function handleRedirectionDefaultController()
	{
		//Idioma en URL en front 
		if( isset($_GET['lang']) )
		{
			//Obtenemos el parametro idioma de la URL.
			$lang = Tools::getValue('lang');

			//Comprobamos que exista el idioma, sino redirigimos al idioma defecto o al de la sesion.
			$language = Idiomas::getLangBySlug($lang);

			//Si el idioma existe y el idioma del slug de la URL no es el mismo que en la sesión actual actualizamos la sesion, pero si no existe comprobamos si existe la sesion para redirigir a la home o redirigir a la home con el idioma default.
			if( $language && $language->id != $_SESSION['lang']->id )
			{
				$_SESSION['lang'] = $language;
			}
			elseif( empty($language) )
			{
				//Si el idioma indicado en la URL es erróneo, pasamos a comprobar si existe la sesion de idioma para redirigir a la home.
				if(!empty($_SESSION['lang']))
					Tools::redirect($_SESSION['lang']->slug.'/');
				else
				{
					Idiomas::setLanguage();
					Tools::redirect($_SESSION['lang']->slug.'/');
				}
			}
		}
		//Si no hay idioma indicado en la URL, pasamos a comprobar si existe la sesion de idioma para redirigir a la home.
		else
		{
			if(!empty($_SESSION['lang']))
				Tools::redirect($_SESSION['lang']->slug.'/');
			else
			{
				Idiomas::setLanguage();
				Tools::redirect($_SESSION['lang']->slug.'/');
			}
		}
	}

	protected function setPage($value)
	{
		$this->page = $value;
	}

	protected function getPage()
	{
		return $this->page;
	}

	protected function setRendered($value)
	{
		$this->rendered = $value;
	}

	protected function getRendered()
	{
		return $this->rendered;
	}

	protected function add($page,$data)
	{
		if ( $page == $this->getPage() )
		{
			$this->setRendered(true);
			return $data();
		}
	}

	protected function loadTraducciones()
	{
		Traducciones::loadTraducciones($_SESSION['lang']->id);
	}

	protected function loadTraduccionesAdmin()
	{
		if( !empty($_SESSION['admin_panel']) )
			$_SESSION['admin_lang'] = Idiomas::getLanguages($_SESSION['admin_panel']->id_lang);

		if( empty($_SESSION['admin_lang']) )
		{
			$iso_code = false;
			if( !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
			{
				$langNavegador = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
				$idiomasDisponibles = Idiomas::getLanguagesVisiblesArray();
				$iso_code = in_array($langNavegador, $idiomasDisponibles) ? $langNavegador : false;
			}
			else
				$_SESSION['admin_lang'] = self::getLanguages(Configuracion::get('default_language'));

			if( !empty($iso_code) )
			{
				$lang = Idiomas::getLangBySlug($iso_code);
				if( !empty($lang) )
					$_SESSION['admin_lang'] = $lang;
				else
					die('Idioma inválido.');
			}
			else
				$_SESSION['admin_lang'] = self::getLanguages(Configuracion::get('default_language'));
		}
		Traducciones::loadTraducciones($_SESSION['admin_lang']->id);
	}
}
