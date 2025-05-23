<?php

class AdminajaxController extends Controllers
{
	var $comienzo = 0;
	var $limite   = 10;
	var $pagina   = 1;

	public function execute($page)
	{
		Render::$layout = false;
        // Obtenemos el entorno si existe
        Admin::getEntorno();
        // Validamos los datos del usuario logueado
        Admin::validateUser();

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

		//Funcion que devuelve los slugs filtrados
		$this->add('ajax-get-slugs-filtered',function()
		{
			//Variables default
			$comienzo 		= Tools::getValue('comienzo');
			$limite 		= Tools::getValue('limite');
			$pagina 		= Tools::getValue('pagina');

			//Obtenemos datos filtrados
			$datos = Slugs::getSlugsFiltered($comienzo, $limite);

			$data = [
				'datos' => $datos,
				'comienzo' => $comienzo,
				'limite' => $limite,
				'pagina' => $pagina,
				'languages' => Idiomas::getLanguages()
			];

			$html = Render::getAjaxPage('admin_slugs_admin',$data);

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

		$this->add('ajax-get-traductions-filtered', function()
		{
			$comienzo		= Tools::getValue('comienzo');
			$limite 		= Tools::getValue('limite');
			$pagina			= Tools::getValue('pagina');
			
			$traducciones = Traducciones::getTraduccionesWithFiltros( $comienzo, $limite, true );

			$data = array(
				'comienzo'  => $comienzo,
				'limite' 	=> $limite,
				'pagina' 	=> $pagina,
				'traducciones'  => $traducciones['listado'],
				'total' 	=> $traducciones['total']
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
			$id = Tools::getValue('id');
			$modelo = Tools::getValue('modelo');
			$response = array(
				'type' => 'error',
				'error' => 'No se ha podido eliminar el registro'
			);

			if( method_exists($modelo, 'eliminarRegistro') && $modelo::eliminarRegistro($id) )
			{
				$response = array(
					'type' => 'success'
				);
			}
			
			die(json_encode($response));
		});

        $this->add('ajax-get-mascotas-admin',function()
        {
            __log_error(json_encode($_POST));

            $comienzo	= Tools::getValue('comienzo');
            $limite 	= Tools::getValue('limite');
            $pagina		= Tools::getValue('pagina');

            $mascotas   = Mascotas::getMascotasFiltered( $comienzo, $limite );
            $total      = count(Mascotas::getMascotasFiltered( $comienzo, $limite, false ));

            $data = array(
                'comienzo'  => $comienzo,
                'limite' 	=> $limite,
                'pagina' 	=> $pagina,
                'mascotas'  => $mascotas,
                'total' 	=> $total
            );

            $html = Render::getAjaxPage('admin_mascotas_list',$data);

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

        $this->add('ajax-contenido-modal',function()
        {
            __log_error(json_encode($_POST));

            $comienzo	= Tools::getValue('comienzo');
            $limite 	= Tools::getValue('limite');
            $pagina		= Tools::getValue('pagina');

            $mascotas   = Mascotas::getMascotasFiltered( $comienzo, $limite );
            $total      = count(Mascotas::getMascotasFiltered( $comienzo, $limite, false ));

            $data = array(
                'comienzo'  => $comienzo,
                'limite' 	=> $limite,
                'pagina' 	=> $pagina,
                'mascotas'  => $mascotas,
                'total' 	=> $total
            );

            $html = Render::getAjaxPage('admin_modal_content',$data);

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

        $this->add('ajax-save-mascota-evaluation',function()
        {
            __log_error(json_encode($_POST));

            $html = '';

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

		if( !$this->getRendered() )
		{
			header('HTTP/1.1 404 Not Found');
			exit;
		}
	}

	protected function loadTraducciones()
	{
		$this->loadTraduccionesAdmin();
	}
}
