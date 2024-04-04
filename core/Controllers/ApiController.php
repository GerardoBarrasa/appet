<?php

class ApiController
{
	var $page;

	public function execute($page)
	{
		$this->page = $page;

		Render::$layout = false;

		if( Tools::getValue('token') != _API_TOKEN_ )
		{
			echo "Go away!";
			exit;
		}

		if( _MULTI_LANGUAGE_ )
			Idiomas::loadPageTranslations($this->page, 'api');

		$this->add('api-test',function()
		{
			//$this->result($response, 'success');
			//$this->result(false, 'error', $response, 400);
		});
	}

	private function result($data = false, $type = 'success', $error = false, $codigoEstado = 200)
    {
        header("Content-Type:application/json");
        header("HTTP/1.1 $codigoEstado $type");

        $response = array( 'type'  => $type );
                
        if( $response['type'] === 'error' )
            $response['error'] = $error;
        
        if( $response['type'] === 'success' )
            $response['data'] = $data;

        echo json_encode(Tools::arrayUtf8($response));

        return;
    }

	public function add($page,$data)
	{
		if( $page == $this->page )
			return $data();
	}
}
?>
