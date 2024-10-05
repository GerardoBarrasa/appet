<?php

class DefaultController extends Controllers
{
	public function execute($page)
	{
		//Layout por defecto
		Render::$layout = 'front-end';

		Tools::registerStylesheet(_ASSETS_.'fontawesome/font-awesome.min.css');
		Tools::registerStylesheet(_ASSETS_.'bootstrap/bootstrap.min.css');

		Tools::registerJavascript(_ASSETS_.'jquery/jquery.min.js');
		Tools::registerJavascript(_ASSETS_.'bootstrap/bootstrap.bundle.min.js');
		Tools::registerJavascript(_JS_.'funks.js?t='._VERSION_);

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
			Tools::redirect($_SESSION['lang']->slug."/");

		if( !empty(Configuracion::get('modo_mantenimiento', '0')) )
		{
			header('HTTP/1.1 503 Service Temporarily Unavailable');
			header('Status: 503 Service Temporarily Unavailable');
			Render::$layout = false;
			Render::showPage('mantenimiento');
		}

		//Pagina de inicio
		$this->add('',function()
		{
			$mpc = new Miprimeraclase;

			//Ejemplo uso ObjectModel
			$cliente = new ClienteTest(1);
			$old_cliente = clone $cliente;
			$cliente->name = 'Nuevo Nombre '.Tools::passwdGen(8, 'NO_NUMERIC');
			$cliente->test_lang_field = array(1 => 'texto en ESP '.time(), 2 => 'texto en EN '.time());

			//Para guardar registro existente o crear nuevo sin establecer ID
			$cliente->save();

			//Para crear nuevo registro estableciendo el ID
			/*$cliente->id = 288;
			$cliente->force_id = true;
			$cliente->add();*/

			//Para eliminar
			//$cliente->delete();

			/**
			 * Para generar los shortcodes de las traducciones de los campos de la clase
			 * @see ClientesTest::generarTraducciones para ver los parámetros
			 */
			//ClienteTest::generarTraduccionesCampos(true, array('id_gender', 'newsletter'));

			//Array de datos a enviar a la página
			$data = array(
				'test' => $mpc->getMessage(),
				'old_cliente' => $old_cliente,
				'cliente' => $cliente
			);

			Render::page('home',$data);
		});

		$this->add('politica-privacidad',function()
		{
			$data = array(
				'texto_legal' => TextosLegales::getTextoLegalByLang('politica-privacidad', $_SESSION['lang']->id),
			);
			Render::page('texto-legal', $data);
		});

		$this->add('condiciones-generales',function()
		{
			$data = array(
				'texto_legal' => TextosLegales::getTextoLegalByLang('condiciones-generales', $_SESSION['lang']->id),
			);
			Render::page('texto-legal', $data);
		});

		$this->add('politica-cookies',function()
		{
			$data = array(
				'texto_legal' => TextosLegales::getTextoLegalByLang('politica-cookies', $_SESSION['lang']->id),
			);
			Render::page('texto-legal', $data);
		});

		$this->add('test',function()
		{
			$mpc = new Miprimeraclase;

			//Array de datos a enviar a la página
			$data = array(
				'test' => $mpc->getMessage(),
			);

			Render::page('home',$data);
		});

		$this->add('404',function()
		{
			Render::page('404');
		});

		if( !$this->getRendered() )
			Tools::redirect(_ADMIN_.$_SESSION['lang']->slug."/404/");
	}
}
