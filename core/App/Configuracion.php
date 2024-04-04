<?php

class Configuracion
{
	/**
	 * Obtiene un valor de la tabla configuracion
	 *
	 * @param string $nombre
	 * @return string Value
	 */
	public static function get($key)
	{
		$result = Bd::getInstance()->fetchRow('SELECT valor FROM configuracion WHERE nombre = "'.$key.'"');

		if( !empty($result) )
			return $result->valor;

		return false;
	}

    /**
     * Obtiene varios valores de la tabla configuracion
     *
     * @throws Exception
     * @param array $keys
     * @return array $key => $value
     */
	public static function getMultiple($keys)
	{
		if (!is_array($keys))
			throw new \Exception('Variable $keys no es un array');

        $result = array();

        foreach( $keys as $k )
            $result[$k] = self::get($k);

        return (!empty($result) ? $result : false);
	}

    /**
     * Comprueba si un nombre existe en la tabla
     *
     * @param string $key
     * @return bool
     */
    public static function checkKey($key)
    {
        $result = Bd::getInstance()->fetchRow('SELECT nombre FROM configuracion WHERE nombre = "'.$key.'"');
        return (!empty($result) ? true : false);
    }

    /**
     * Actualiza un nombre y valor en la base de datos. Si no existe lo crea
     *
     * @param string $key
     * @param string $value
     * @return bool Resultado actualizacion
     */
    public static function updateValue($key, $value)
    {
        $result = false;

        $currentTime = Tools::datetime();

        if( self::checkKey($key) )
        {
            $updConfig = array(
                'valor' => $value,
                'date_modified' => $currentTime,
                'date_created' => $currentTime
            );
            if( Bd::getInstance()->update('configuracion', $updConfig, 'nombre = "'.$key.'"') )
                $result = true;
        }
        else
        {
            $addConfig = array(
                'nombre' => $key,
                'valor' => $value,
                'date_modified' => $currentTime,
                'date_created' => $currentTime
            );

            if( Bd::getInstance()->insert('configuracion', $addConfig) )
                $result = true;
        }

        return $result;
    }
}
