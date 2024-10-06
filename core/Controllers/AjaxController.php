<?php

class AjaxController extends Controllers
{
	var $comienzo = 0;
	var $limite   = 10;
	var $pagina   = 1;

	public function execute($page)
	{
		Render::$layout = false;

		$this->add('ajax-test-get',function()
		{
			$data = array(
				'var1' => 'PRUEBA GET'
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

		$this->add('ajax-test-post',function()
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

		if( !$this->getRendered() )
		{
			header('HTTP/1.1 404 Not Found');
			exit;
		}
	}
}
