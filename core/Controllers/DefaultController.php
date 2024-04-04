<?php

class DefaultController
{
	var $page;

	public function execute($page)
	{
		$this->page = $page;

		//Layout por defecto
		Render::$layout = 'front-end';
		Tools::registerJavascript(_ASSETS_.'jquery/jquery.min.js');

		$idiomas = Idiomas::getLanguages();
		foreach( $idiomas as &$idioma )
		{
			$idioma->slug_complete = Slugs::getSlugCompleteForIdLang($idioma);
		}

		Render::$layout_data = array(
			'page_name' => $this->page == '' ? 'home' : $this->page,
			'idiomas' => $idiomas
		);

		if( _MULTI_LANGUAGE_ )
			Idiomas::loadPageTranslations($this->page,'',$_SESSION['lang']);

		//Pagina de inicio
		$this->add('',function()
		{
			Metas::$title = "Bienvenido a CORE!";
			$mpc = new Miprimeraclase;
			$datos_idiomas = Idiomas::getLanguages();

			//Array de datos a enviar a la página
			$data = array(
				'datos_idiomas' => $datos_idiomas,
				'test' => $mpc->getMessage(),
			);

			Render::page('home',$data);
		});
	}

	public function add($page,$data)
	{
		if ( $page == $this->page )
			return $data();
	}
}
