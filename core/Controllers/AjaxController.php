<?php

class AjaxController
{
	var $page;

	var $comienzo = 0;
	var $limite   = 10;
	var $pagina   = 1;

	public function execute($page)
	{
		$this->page = $page;

		Render::$layout = false;

		$this->add('ajax-test',function()
		{
			$data = array(
				'var1' => Tools::getValue('var1')
			);
			$html = Render::getAjaxPage('test_ajax',$data);

			if( !empty($html) )
			{
				$response = array(
					'type' => 'success',
					'html' => $html
				);
			}
			else
			{
				$response = array(
					'type' => 'error',
					'error' => 'Hubo algun problema cargando el html'
				);
			}

			die(json_encode($response));
		});

		$this->add('ajax-get-usuarios-admin',function()
		{
			$comienzo		= Tools::getValue('comienzo');
			$limite 		= Tools::getValue('limite');
			$pagina			= Tools::getValue('pagina');
			
			$usuarios = Admin::getUsuariosWithFiltros( $comienzo, $limite, true );

			$data = array(
				'comienzo'  => $comienzo,
				'limite' 	=> $limite,
				'pagina' 	=> $pagina,
				'usuarios'  => $usuarios['listado'],
				'total' 	=> $usuarios['total']
			);

			$html = Render::getAjaxPage('admin_usuarios_admin',$data);

			if( !empty($html) )
			{
				$response = array(
					'type' => 'success',
					'html' => $html
				);
			}
			else
			{
				$response = array(
					'type' => 'error',
					'html' => 'Hubo un error cargando el html'
				);
			}

			die(json_encode($response));
		});

		$this->add('ajax-get-idiomas-admin',function()
		{
			$comienzo		= Tools::getValue('comienzo');
			$limite 		= Tools::getValue('limite');
			$pagina			= Tools::getValue('pagina');
			
			//Obtenemos mensajes de actualidad
			$idiomas = Idiomas::getIdiomasWithFiltros( $comienzo, $limite, true );
			$total = count($idiomas);

			$data = array(
				'comienzo'  => $comienzo,
				'limite' 	=> $limite,
				'pagina' 	=> $pagina,
				'idiomas'  => $idiomas,
				'total' 	=> $total
			);

			$html = Render::getAjaxPage('admin_idiomas',$data);

			if( !empty($html) )
			{
				$response = array(
					'type' => 'success',
					'html' => $html
				);
			}
			else
			{
				$response = array(
					'type' => 'error',
					'html' => 'Hubo un error cargando el html'
				);
			}

			die(json_encode($response));
		});

		$this->add('ajax-update-traduction',function()
		{
			$id 					= Tools::getValue("id");
			$adm['contenido'] 		= addslashes(trim($_REQUEST["contenido"]));
			$action 				= Tools::getValue("action");
			$msg_error 				= "";

			if($adm['contenido'] == "")
				$msg_error = "Debes indicar la traducción rellenando el campo de texto correspondiente";

			//Segun el action actuamos
			if($msg_error == ""){
				switch ($action) {
					case 'create':

						//Solo cogemos los datos a crear
						$adm['id_idioma'] 		= Tools::getValue("id_idioma");
						$adm['traduction_for'] 	= Tools::getValue("traduction_for");
						$adm['shortcode'] 		= Tools::getValue("shortcode");
						$adm['fecha_creation'] 	= date('Y-m-d');
						$adm['fecha_update'] 	= date('Y-m-d');

						//Insertamos la traduccion
						$result = Idiomas::insertTraduction($adm);

						echo "ok";

						break;
					
					case 'update':

						//Actualizamos contenido y fecha
						$adm['fecha_update'] = date('Y-m-d');

						Idiomas::updateTraduction($id, $adm);

						echo "ok";

						break;
				}
			}
			else
				echo $msg_error;
		});

		$this->add('ajax-get-traductions-filtered', function()
		{
			//Obtenemos los diferentes idiomas
			$datos_idiomas = Idiomas::getLanguages();
		    $totalIdiomas  = count($datos_idiomas);

			//Recogemos variables
			$slug_idioma 		= Tools::getValue('slug_idioma');
			$translation_status = Tools::getValue('translation_status');
			$comienzo 			= Tools::getValue('comienzo');
			$limite 			= Tools::getValue('limite');
			$pagina 			= Tools::getValue('pagina');

			$filtroBloqueado = false;
			if( $translation_status == '1' && empty($slug_idioma) )
				$filtroBloqueado = true;

			$datos_traducciones = array();
			$traducciones_originales = array();
			if( !$filtroBloqueado )
			{
			    //Obtenemos todas las traducciones segmentadas por shortcode y predominando el español
				$datos_traducciones = Idiomas::getAllTraductionsGroupedFiltered($comienzo, $limite);

				if( !empty($slug_idioma) )
				{
					$idioma_default = Idiomas::getLangBySlug(_DEFAULT_LANGUAGE_);
					//Obtenemos textos en idioma por defecto
					$traducciones_originales = Idiomas::getAllTraductionsByIdGroupedShortcode($idioma_default->id);
				}
			}

			$totalTraducciones = count($datos_traducciones);
			$idioma_busqueda = array();
			$contador =0;
			
			foreach ($datos_traducciones as $datosKey => $dato_traduccion)
			{
				$contador2 =0;
				foreach($dato_traduccion as $datoKey => $idioma)
				{
					$idioma_busqueda[$contador][$contador2]= $datoKey;
					$contador2++;
				}
				$contador++;
			}

			$data = array(
				'comienzo'  => $comienzo,
				'idioma_busqueda' => $idioma_busqueda,
				'totalIdiomas' =>  $totalIdiomas,
				'limite' 	=> $limite,
				'pagina' 	=> $pagina,
				'slug_idioma' => $slug_idioma,
				'datos_idiomas' => $datos_idiomas,
				'datos_traducciones' => $datos_traducciones,
				'totalTraducciones' => $totalTraducciones,
				'traducciones_originales' => $traducciones_originales,
				'filtroBloqueado' => $filtroBloqueado
			);

			$html = Render::getAjaxPage('admin_traducciones',$data);

			if( !empty($html) )
			{
				$response = array(
					'type' => 'success',
					'html' => $html
				);
			}
			else
			{
				$response = array(
					'type' => 'error',
					'html' => 'Hubo un error cargando el html'
				);
			}

			die(json_encode($response));
		});

		$this->add('ajax-eliminar-registro',function()
		{
			$completado 	   = false;
			$id				   = Tools::getValue('id');
			$modelo			   = Tools::getValue('modelo');

			if( method_exists($modelo, 'eliminarRegistro') && $modelo::eliminarRegistro($id) )
				$completado = true;
			
			die( $completado );
		});
	}

	public function add($page,$data)
	{
		if ( $page == $this->page )
			return $data();
	}
}
?>
