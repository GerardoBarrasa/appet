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

			//Comprobamos que exista el idioma, si no redirigimos al idioma defecto o al de la sesion.
			$language = Idiomas::getLangBySlug($lang);

			//Si el idioma existe, actualizamos la sesion, pero si no existe comprobamos si existe la sesion para redirigir a la home o redirigir a la home con el idioma default.
			if($language)
			{
				$_SESSION['id_lang'] = $language->id;
				$_SESSION['lang'] = $lang;
			}
			else{

				//Si el idioma indicado en la URL es erróneo, pasamos a comprobar si existe la sesion de idioma para redirigir a la home.
				if(isset($_SESSION['lang'])){
					header('Location: ' . _DOMINIO_.$_SESSION['lang'].'/');
				}
				else{
					$defaultLang 		= Idiomas::getDefaultLanguage();
					$_SESSION['id_lang'] = $defaultLang->id;
					$_SESSION['lang'] 	= $defaultLang->slug;
					header('Location: ' . _DOMINIO_.$defaultLang->slug.'/');
				}
			}
		}
		//Si no hay idioma indicado en la URL, pasamos a comprobar si existe la sesion de idioma para redirigir a la home.
		else
		{
			if(isset($_SESSION['lang'])){
				header('Location: ' . _DOMINIO_.$_SESSION['lang'].'/');
			}
			else{
				$defaultLang 		= Idiomas::getDefaultLanguage();
				$_SESSION['id_lang'] = $defaultLang->id;
				$_SESSION['lang'] 	= $defaultLang->slug;
				header('Location: ' . _DOMINIO_.$defaultLang->slug.'/');
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
		Traducciones::loadTraducciones($_SESSION['id_lang']);
	}

	protected function loadTraduccionesAdmin()
	{
		if( !isset($_SESSION['admin_id_lang']) || empty($_SESSION['admin_id_lang']) )
		{
			$iso_code = _DEFAULT_LANGUAGE_;
			if( !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
			{
				$langNavegador = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
				$idiomasDisponibles = Idiomas::getLanguagesVisiblesArray();
				$iso_code = in_array($langNavegador, $idiomasDisponibles) ? $langNavegador : _DEFAULT_LANGUAGE_;
			}
			else
				$_SESSION['admin_id_lang'] = _DEFAULT_LANGUAGE_;

			$lang = Idiomas::getLangBySlug($iso_code);
			if( !empty($lang) )
				$_SESSION['admin_id_lang'] = $lang->id;
			else
				die('Idioma inválido.');
		}
		Traducciones::loadTraducciones($_SESSION['admin_id_lang']);
	}
}
