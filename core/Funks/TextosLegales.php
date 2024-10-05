<?php

class TextosLegales
{
	public static function getTextosLegalesWithLang()
	{
		$textos_legales = array();
		$result = Bd::getInstance()->fetchObject("SELECT tl.nombre as tipo_texto_legal, i.nombre as idioma, tli.* FROM textos_legales tl LEFT JOIN textos_legales_idiomas tli ON tli.id_texto_legal = tl.id_texto_legal LEFT JOIN idiomas i ON i.id = tli.id_lang");
		foreach( $result as $res )
			$textos_legales[$res->id_lang][$res->tipo_texto_legal] = $res;
		return $textos_legales;
	}

	public static function actualizarTextoLegal($id_lang, $tipo_texto_legal, $texto_legal)
	{
		$updTextoLegal = array(
			'contenido' => addslashes($texto_legal)
		);

		return Bd::getInstance()->update('textos_legales_idiomas', $updTextoLegal, 'id_texto_legal = '.(int)self::getIdByTipoTextoLegal($tipo_texto_legal).' AND id_lang = '.(int)$id_lang);
	}

	public static function getIdByTipoTextoLegal($tipo_texto_legal)
	{
		return Bd::getInstance()->fetchValue("SELECT id_texto_legal FROM textos_legales WHERE nombre = '".$tipo_texto_legal."'");
	}

	public static function getTextoLegalByLang($tipo_texto_legal, $id_lang)
	{
		return Bd::getInstance()->fetchValue("SELECT tli.contenido FROM textos_legales_idiomas tli LEFT JOIN textos_legales tl ON tl.id_texto_legal = tli.id_texto_legal AND tl.nombre = '".$tipo_texto_legal."' WHERE tli.id_lang = ".(int)$id_lang." LIMIT 1");
	}
}