<?php

class CronsController
{
	var $page;

	public function execute($page)
	{
		$this->page = $page;

		if( Tools::getValue('token') != _CRONJOB_TOKEN_ )
		{
			echo "Go away!";
			exit;
		}

		Render::$layout = false;

		$this->add('enviar-emails',function()
		{
			$cantidad = Configuracion::get('cronjob_email_cantidad');

			$emailIdsToSend = Bd::getInstance()->fetchObject("SELECT id FROM emails_cache WHERE enviado = 0 AND error = 0 ORDER BY id ASC LIMIT 0,".$cantidad);

			if( !empty($emailIdsToSend) )
				foreach( $emailIdsToSend as $email )
					Sendmail::sendCachedMail($email->id);
		});
	}

	public function add($page,$data)
	{
		if ( $page == $this->page )
			return $data();
	}
}
