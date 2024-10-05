<?php

class Configuracion
{
	protected static $_initialized = false;
	protected static $_cache = null;

	/**
	 * Carga todos los datos de configuración
	 */
	public static function loadConfiguration()
	{
		$results = Bd::getInstance()->fetchArray('SELECT c.`nombre`, c.`valor` FROM `configuracion` c');
		if( $results )
		{
			foreach( $results as $row )
			{
				if( $row['valor'] === null )
					$row['valor'] = '';

				if( !isset(self::$_cache[$row['nombre']]) )
					self::$_cache[$row['nombre']] = $row['valor'];
			}
			self::$_initialized = true;
		}
	}

	/**
	 * Obtiene un valor de la tabla configuracion
	 *
	 * @param string $nombre
	 * @return string Value
	 */
	public static function get($key, $default = false)
	{
		if( !self::$_initialized )
			self::loadConfiguration();

		if( self::checkKey($key) )
			return self::$_cache[$key];

		return $default;
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
			throw new CoreException('Variable $keys no es un array');

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
		return isset(self::$_cache[$key]);
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

		if( (!empty(self::get($key)) || self::get($key) === '0' || self::get($key) === '') && self::checkKey($key)  )
		{
			$updConfig = array(
				'valor' => $value,
				'date_modified' => $currentTime,
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

		if( $result )
			self::$_cache[$key] = $value;

		return $result;
	}
}
