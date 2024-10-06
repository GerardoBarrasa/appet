<?php

class ApiController extends Controllers
{
	public function execute($page)
	{
		Render::$layout = false;

		if( Tools::getValue('token') != _API_TOKEN_ )
		{
			echo "Go away!";
			exit;
		}

		$this->add('api-test',function()
		{
			//$this->result($response, 'success');
			//$this->result(false, 'error', $response, 400);
		});

		if( !$this->getRendered() )
		{
			header('HTTP/1.1 404 Not Found');
			exit;
		}
	}

	private function result($data = false, $type = 'success', $error = false, $codigoEstado = 200)
	{
		Tools::jsonResult($data, $type, $error, $codigoEstado);
		exit;
	}

	protected function loadTraducciones()
	{
		return;
	}
}
