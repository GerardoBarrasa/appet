<?php

abstract class Cache
{
	/**
	 * @var array Store local cache
	 */
	protected static $local = [];

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public static function store($key, $value)
	{
		// PHP is not efficient at storing array
		// Better delete the whole cache if there are
		// more than 1000 elements in the array
		if (count(Cache::$local) > 1000) {
			Cache::$local = [];
		}
		Cache::$local[$key] = $value;
	}

	public static function clear()
	{
		Cache::$local = [];
	}

	/**
	 * @param string $key
	 *
	 * @return mixed|null The cache item if found, null otherwise
	 */
	public static function retrieve($key)
	{
		return isset(Cache::$local[$key]) ? Cache::$local[$key] : null;
	}

	/**
	 * @return array
	 */
	public static function retrieveAll()
	{
		return Cache::$local;
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public static function isStored($key)
	{
		return isset(Cache::$local[$key]);
	}

	/**
	 * @param string $key
	 */
	public static function clean($key)
	{
		if( strpos($key, '*') !== false )
		{
			$regexp = str_replace('\\*', '.*', preg_quote($key, '#'));
			foreach( array_keys(Cache::$local) as $key )
			{
				if( preg_match('#^' . $regexp . '$#', $key) )
					unset(Cache::$local[$key]);
			}
		}
		else
			unset(Cache::$local[$key]);
	}
}
