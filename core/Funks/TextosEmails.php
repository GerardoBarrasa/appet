<?php

class TextosEmails
{
	public static function getTextosEmailsWithLang()
	{
		$textos_emails = array();
		$result = Bd::getInstance()->fetchObject("SELECT te.nombre as tipo_texto_email, i.nombre as idioma, tei.* FROM textos_emails te LEFT JOIN textos_emails_idiomas tei ON tei.id_texto_email = te.id_texto_email LEFT JOIN idiomas i ON i.id = tei.id_lang");
		foreach( $result as $res )
			$textos_emails[$res->id_lang][$res->tipo_texto_email] = $res;
		return $textos_emails;
	}

	public static function actualizarTextoEmail($id_lang, $tipo_texto_email, $asunto, $texto_email)
	{
		$updTextoEmail = array(
			'asunto' => addslashes($asunto),
			'contenido' => addslashes($texto_email)
		);

		return Bd::getInstance()->update('textos_emails_idiomas', $updTextoEmail, 'id_texto_email = '.(int)self::getIdByTipoTextoEmail($tipo_texto_email).' AND id_lang = '.(int)$id_lang);
	}

	public static function getIdByTipoTextoEmail($tipo_texto_email)
	{
		return Bd::getInstance()->fetchValue("SELECT id_texto_email FROM textos_emails WHERE nombre = '".$tipo_texto_email."'");
	}

	public static function getTextosEmailByLang($tipo_texto_email, $id_lang)
	{
		return Bd::getInstance()->fetchRow("SELECT tei.asunto, tei.contenido FROM textos_emails_idiomas tei LEFT JOIN textos_emails te ON te.id_texto_email = tei.id_texto_email AND te.nombre = '".$tipo_texto_email."' WHERE tei.id_lang = ".(int)$id_lang." LIMIT 1");
	}

	public static function crearTextoEmail($nombre)
	{
		$addTextoEmail = array(
			'nombre' => $nombre
		);

		if( Bd::getInstance()->insert('textos_emails', $addTextoEmail) )
		{
			$id_texto_email = Bd::getInstance()->lastId();

			$idiomas = Idiomas::getLanguages();
			foreach( $idiomas as $idioma )
			{
				$addTextoEmailIdioma = array(
					'id_texto_email' => $id_texto_email,
					'id_lang' => $idioma->id,
					'asunto' => '',
					'contenido' => ''
				);
				if( !Bd::getInstance()->insert('textos_emails_idiomas', $addTextoEmailIdioma) )
					return false;
			}
			return $id_texto_email;
		}
		return false;
	}

	public static function checkNombreExists($nombre, $ignore_id = false)
	{
		$where_not_id = "";
		if( !empty($ignore_id) )
			$where_not_id = " AND id_texto_email != ".(int)$ignore_id;
		$result = Bd::getInstance()->countRows("SELECT id_texto_email FROM textos_emails WHERE nombre = '".$nombre."'".$where_not_id);
		return empty($result) ? false : true;
	}

	public static function getTextosEmail($nombre, $id_lang, $vars=array())
	{
		$textos = self::getTextosEmailByLang($nombre, $id_lang);
		return array(
			'asunto' => Sendmail::setEmailVars($textos->asunto, $vars),
			'contenido' => Sendmail::setEmailVars($textos->contenido, $vars)
		);
	}
}