<?php

class CronsController extends Controllers
{
	public function execute($page)
	{
		if( Tools::getValue('token') != _CRONJOB_TOKEN_ )
		{
			echo "Go away!";
			exit;
		}

		Render::$layout = false;

		$this->add('enviar-emails',function()
		{
			$cantidad = Configuracion::get('cronjob_email_cantidad');

			$emailIdsToSend = Bd::getInstance()->fetchObject("SELECT id_email FROM emails_cache WHERE enviado = 0 AND error = 0 ORDER BY id_email ASC LIMIT 0,".$cantidad);

			if( !empty($emailIdsToSend) )
				foreach( $emailIdsToSend as $email )
					Sendmail::sendCachedMail($email->id_email);
		});

		if( !$this->getRendered() )
		{
			header('HTTP/1.1 404 Not Found');
			exit;
		}
	}

	protected function loadTraducciones()
	{
		return;
	}
}
