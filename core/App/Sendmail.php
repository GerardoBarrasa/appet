<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Sendmail
{
	/**
	 * Monta el mensaje que se quiere enviar en un HTML corporativo
	 *
	 * @param string $mensaje Texto a enviar
	 * @return string $mensaje Email con HTML
	 */
	public static function prepareMail($mensaje, $id_lang = false)
	{
		$mensaje = '
			<div style="width:100%; height:100%; background-color:#f0f0f0; text-align:center; font-size:12px; color:#333;">
				<br /><br />
				'.Traducciones::getTextoByShortcodeIdioma('email-footer-automatic', $id_lang).'
				<br /><br />
				<div style="border:1px solid #ccc; background-color:#fff; padding:26px; width:860px; color:#333; font-size:14px; line-height:22px; text-align:left; margin:0 auto;">
				<center><a href="'._DOMINIO_.'"><img src="'._ASSETS_.'img/logo.png" alt="logo"></a></center>
					<br /><br />
					'.$mensaje.'
					<br /><br />
					<div style="border-top:1px solid #ccc; padding-top:24px; font-size:12px; color:#666;">
						'.Traducciones::getTextoByShortcodeIdioma('email-footer-automatic-2', $id_lang).' '._TITULO_.'. <a href="'.Slugs::getCurrentSlugByModId('politica-privacidad', $id_lang).'" style="color: #D8A109;">'.Traducciones::getTextoByShortcodeIdioma('email-footer-privacy', $id_lang).'</a>
					</div>
				</div>
				<br /><br />
			</div>
		';
		return $mensaje;
	}

	/**
	 * Agrega el mensaje a la cola de emails de la base de datos
	 *
	 * @param string $destinatario Email del destinatario del mensaje
	 * @param string $asunto Asunto del email
	 * @param string $mensaje Texto del mensaje
	 * @return bool
	 */
	public static function enqueueMail($destinatario,$asunto,$mensaje)
	{
		$addEmail = array(
			'date_created' => 'SYSDATE()',
			'destinatario' => $destinatario,
			'asunto' => $asunto,
			'mensaje' => addslashes($mensaje),	
		);

		//Guardamos cache
		if( Bd::getInstance()->insert('emails_cache', $addEmail) )
			return true;
		else
			return false;
	}

	/**
	 * Envia un email corporativo en el momento
	 *
	 * @param string $destinatario Email del destinatario del mensaje
	 * @param string $asunto Asunto del email
	 * @param string $mensaje Texto del mensaje
	 * @return bool
	 */
	public static function send($destinatario,$asunto,$mensaje, $id_lang = false, $attachment_path = '')
	{
		$mensaje = self::prepareMail($mensaje, $id_lang);

		$mail = new PHPMailer();

		$mail->SMTPSecure = 'tls';
		$mail->IsSMTP();
		$mail->Host = _SMTP_SERVER_;
		$mail->SMTPAuth = true;
		$mail->Port = _SMTP_PORT_;
		$mail->SetFrom(_SMTP_USER_, _TITULO_);
		$mail->Username = _SMTP_USER_;
		$mail->Password = _SMTP_PASSWORD_; 							
		$mail->SetLanguage("es");
		$mail->CharSet = _SMTP_CHARSET_ISO_;
		$mail->WordWrap = 50;					
		$mail->IsHTML(true);
		$mail->AltBody = Tools::utf8_to_iso8859_1(strip_tags($mensaje));
		$mail->AddAddress($destinatario, _TITULO_);
		$mail->Subject = Tools::utf8_to_iso8859_1($asunto);
		$mail->Body = Tools::utf8_to_iso8859_1($mensaje);
		//$mail->SMTPDebug = 2;
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false
			)
		);

		if( !empty($attachment_path) )
			$mail->AddAttachment($attachment_path, basename($attachment_path));

		if( $mail->Send() )
			return true;
		return false;
	}

	public static function sendCachedMail($id)
	{
		$emailData = Bd::getInstance()->fetchRow('SELECT * FROM emails_cache WHERE id_email = "'.(int)$id.'"', 'array');

		if ( $emailData )
		{
			//Configuramos valores
			$destinatario = $emailData['destinatario'];
			$asunto = $emailData['asunto'];
			$mensaje = $emailData['mensaje'];
			$id_idioma = $emailData['id_idioma'];
			
			if( !self::send($destinatario, $asunto, $mensaje, $id_idioma) )
			{
				echo 'Error al enviar email a '.$destinatario.' con asunto: "'.$asunto.'"<br/>';
				Bd::getInstance()->query('UPDATE emails_cache SET error = 1 WHERE id_email = "'.$id.'"');
				return false;
			}
			else
			{
				echo 'Email enviado a '.$destinatario.' con asunto: "'.$asunto.'"<br/>';
				Bd::getInstance()->query('UPDATE emails_cache SET enviado = 1, date_sent = "'.Tools::datetime().'" WHERE id_email = "'.$id.'"');
				return true;	
			}
		}
		else
		{
			echo 'ID de email incorrecto<br/>';
			return false;
		}
	}

	public static function sendTest()
	{		
		//Configuramos valores
		$destinatario = _RECEPTOR_;
		$asunto = 'Asunto de prueba: á é í ó ú ñ.';
		$mensaje = 'Mensaje de prueba: á é í ó ú ñ.';

		$mail = new PHPMailer();

		$mail->SMTPSecure = 'tls';
		$mail->IsSMTP();
		$mail->Host = _SMTP_SERVER_;
		$mail->SMTPAuth = true;
		$mail->Port = _SMTP_PORT_;
		$mail->SetFrom(_SMTP_USER_, _TITULO_);
		$mail->Username = _SMTP_USER_;
		$mail->Password = _SMTP_PASSWORD_; 							
		$mail->SetLanguage("es");
		$mail->CharSet = _SMTP_CHARSET_ISO_;
		$mail->WordWrap = 50;
		$mail->IsHTML(true);
		$mail->AltBody = Tools::utf8_to_iso8859_1(strip_tags($mensaje));
		$mail->AddAddress($destinatario, _TITULO_);
		$mail->Subject = Tools::utf8_to_iso8859_1($asunto);
		$mail->Body = Tools::utf8_to_iso8859_1($mensaje);
		//$mail->SMTPDebug = 2;
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false
			)
		);

		if( !$mail->Send() )
			echo 'Error al enviar email a '.$destinatario.' con asunto: "'.$asunto.'"';
		else 
			echo 'Email enviado a '.$destinatario.' con asunto: "'.$asunto.'"';
	}

	public static function setEmailVars($texto, $vars=array())
	{
		if( !empty($vars) )
		{
			foreach( $vars as $key => $var )
			{
				$texto = str_ireplace($key, $var, $texto);
			}
		}
		return $texto;
	}

	public static function sendTemplate($plantilla, $id_lang, $destinatario, $vars = array(), $attachment_path = '')
	{
		$textos_email = TextosEmails::getTextosEmail($plantilla, $id_lang, $vars);
		return Sendmail::send($destinatario, $textos_email['asunto'], $textos_email['contenido'], $id_lang, $attachment_path);
	}
}
