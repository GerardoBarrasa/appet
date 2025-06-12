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

            // Asegurar que no hay salida previa
            ob_clean();
            header('Content-Type: application/json');
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

            // Asegurar que no hay salida previa
            ob_clean();
            header('Content-Type: application/json');
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

            // Asegurar que no hay salida previa
            ob_clean();
            header('Content-Type: application/json');
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

            // Asegurar que no hay salida previa
            ob_clean();
            header('Content-Type: application/json');
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

            // Asegurar que no hay salida previa
            ob_clean();
            header('Content-Type: application/json');
            die(json_encode($response));
        });

        $this->add('ajax-get-mascotas-admin',function()
        {
            // Log para debugging
            __log_error('=== AJAX GET MASCOTAS START ===', 0, 'ajax_mascotas');
            __log_error('POST data: ' . json_encode($_POST), 0, 'ajax_mascotas');

            $comienzo	= Tools::getValue('comienzo', 0);
            $limite 	= Tools::getValue('limite', 10);
            $pagina		= Tools::getValue('pagina', 1);

            __log_error("Parámetros: comienzo=$comienzo, limite=$limite, pagina=$pagina", 0, 'ajax_mascotas');

            try {
                $mascotas   = Mascotas::getMascotasFiltered($comienzo, $limite);
                $total      = count(Mascotas::getMascotasFiltered(0, null, false));

                __log_error('Mascotas encontradas: ' . count($mascotas), 0, 'ajax_mascotas');
                __log_error('Total mascotas: ' . $total, 0, 'ajax_mascotas');

                $data = array(
                    'comienzo'  => $comienzo,
                    'limite' 	=> $limite,
                    'pagina' 	=> $pagina,
                    'mascotas'  => $mascotas,
                    'total' 	=> $total
                );

                $html = Render::getAjaxPage('admin_mascotas_list', $data);
                __log_error('HTML generado: ' . strlen($html) . ' caracteres', 0, 'ajax_mascotas');

                if (!empty($html)) {
                    $response = array(
                        'type' => 'success',
                        'html' => $html
                    );
                } else {
                    $response = array(
                        'type' => 'error',
                        'error' => 'No se pudo generar el contenido HTML'
                    );
                }
            } catch (Exception $e) {
                __log_error('Error en ajax-get-mascotas-admin: ' . $e->getMessage(), 0, 'ajax_mascotas');
                $response = array(
                    'type' => 'error',
                    'error' => 'Error interno del servidor: ' . $e->getMessage()
                );
            }

            __log_error('Respuesta final: ' . json_encode($response), 0, 'ajax_mascotas');
            __log_error('=== AJAX GET MASCOTAS END ===', 0, 'ajax_mascotas');

            // Limpiar cualquier salida previa y enviar respuesta JSON
            ob_clean();
            header('Content-Type: application/json');
            die(json_encode($response));
        });

        $this->add('ajax-contenido-modal',function()
        {
            __log_error('=== AJAX CONTENIDO MODAL START ===', 0, 'ajax_modal');
            __log_error('POST data: ' . json_encode($_POST), 0, 'ajax_modal');

            $comienzo	= Tools::getValue('comienzo', 0);
            $limite 	= Tools::getValue('limite', 10);
            $pagina		= Tools::getValue('pagina', 1);

            $mascotas   = Mascotas::getMascotasFiltered($comienzo, $limite);
            $total      = count(Mascotas::getMascotasFiltered(0, null, false));

            $data = array(
                'comienzo'  => $comienzo,
                'limite' 	=> $limite,
                'pagina' 	=> $pagina,
                'mascotas'  => $mascotas,
                'total' 	=> $total
            );

            $html = Render::getAjaxPage('admin_modal_content', $data);

            if (!empty($html)) {
                $response = array(
                    'type' => 'success',
                    'html' => $html
                );
            } else {
                $response = array(
                    'type' => 'error',
                    'error' => 'Hubo un error cargando el html del modal'
                );
            }

            __log_error('Respuesta modal: ' . json_encode($response), 0, 'ajax_modal');
            __log_error('=== AJAX CONTENIDO MODAL END ===', 0, 'ajax_modal');

            // Limpiar cualquier salida previa y enviar respuesta JSON
            ob_clean();
            header('Content-Type: application/json');
            die(json_encode($response));
        });

        $this->add('ajax-save-mascota-evaluation',function()
        {
            __log_error('=== AJAX SAVE EVALUATION START ===', 0, 'ajax_evaluation');
            __log_error('POST data: ' . json_encode($_POST), 0, 'ajax_evaluation');

            $response = array(
                'type' => 'success',
                'message' => 'Evaluación guardada correctamente'
            );

            __log_error('Respuesta evaluación: ' . json_encode($response), 0, 'ajax_evaluation');
            __log_error('=== AJAX SAVE EVALUATION END ===', 0, 'ajax_evaluation');

            // Limpiar cualquier salida previa y enviar respuesta JSON
            ob_clean();
            header('Content-Type: application/json');
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
