<?php

class Controllers
{
	//Controlador por defecto
	var $defaultController = 'default';
	var $cntrl = array();

	public function __construct()
	{
		//Cargamos controladores
		$this->cntrl['default'] = new DefaultController;
		$this->cntrl['ajax']	= new AjaxController;
		$this->cntrl['admin']   = new AdminController;
		$this->cntrl['debug']   = new DebugController;
		$this->cntrl['crons']   = new CronsController;
		$this->cntrl['api']   	= new ApiController;
	}

	//Leemos controlador
	public function load()
	{	
		unset($_SESSION['js_paths']);
		unset($_SESSION['css_paths']);

		//Controlador
		$controller = Tools::getValue('controller', $this->defaultController);

		//Idioma
		if(isset($_GET['lang']))
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
		elseif($controller == 'default')
		{
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

		//Pagina
		if( isset($_GET['mod']) )
			$page = Tools::getValue('mod');
		else
			$page = '';

		//La pagina será el controlador si existe un controlador llamado así
		if( array_key_exists($page,$this->cntrl) )
		{
			$controller = $page;
			$page = '';
		}

		//Si no existe controlador
		if( !array_key_exists($controller,$this->cntrl) )
		{
			$controller = 'default';
			$page = '404';
		}

		//Comprobamos custom page.
		if($page != '' && $controller == 'default')
		{
			//Buscamos el SLUG en la BBDD.
			$data_slug = Slugs::getModBySlug($page);

			if(!empty($data_slug) && isset($data_slug->mod_id) && $data_slug->mod_id != '')
				$page = $data_slug->mod_id;
		}

		if( !isset($_REQUEST['mod']) )
			$_REQUEST['mod'] = '';

		//Ejecutamos controlador
		$this->cntrl[$controller]->execute($page);
	}
}
