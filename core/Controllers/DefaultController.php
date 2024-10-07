<?php

class DefaultController extends Controllers
{
	public function execute($page)
	{
		//Layout por defecto
		Render::$layout = 'front-end';
		Tools::registerJavascript(_JS_._PUBLIC_.'jquery/jquery.min.js');

		$idiomas = Idiomas::getLanguages();
		foreach( $idiomas as &$idioma )
		{
			$idioma->slug_complete = Slugs::getSlugCompleteForIdLang($idioma);
		}

		Render::$layout_data = array(
			'page_name' => $page == '' ? 'home' : $page,
			'idiomas' => $idiomas
		);

		if( !empty($metaData = Slugs::getPageDataByModId(($page == '' ? 'home' : $page))) )
		{
			Metas::$title = (isset($metaData->title)) ? $metaData->title : _TITULO_;
			Metas::$description = (isset($metaData->description)) ? $metaData->description : _TITULO_;
		}
		else
			header('Location:'._DOMINIO_.$_SESSION['lang']."/");

		//Pagina de inicio
		$this->add('',function()
		{
			$mpc = new Miprimeraclase;
			$datos_idiomas = Idiomas::getLanguages();

			//Array de datos a enviar a la página
			$data = array(
				'datos_idiomas' => $datos_idiomas,
				'test' => $mpc->getMessage(),
			);

			Render::page('home',$data);
		});

		//Pagina de inicio
		$this->add('test',function()
		{
			$mpc = new Miprimeraclase;
			$datos_idiomas = Idiomas::getLanguages();

			//Array de datos a enviar a la página
			$data = array(
				'datos_idiomas' => $datos_idiomas,
				'test' => $mpc->getMessage(),
			);

			Render::page('home',$data);
		});

		$this->add('404',function()
		{
			Render::page('404');
		});

		if( !$this->getRendered() )
		{
			header('Location: ' . _DOMINIO_.$_SESSION['lang'].'/404/');
			exit;
		}
	}
}
