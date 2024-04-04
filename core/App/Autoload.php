<?php

class Autoload
{
	protected static $instance;
	public $index = [];

	protected function __construct()
	{
		$this->index = include _PATH_.'core/class_index.php';
	}

	public static function getInstance()
	{
		if (!static::$instance) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function load($className)
	{
		$className = str_ireplace(array('App\\', 'Controllers\\'), '', $className);
		if( isset($this->index[$className]) )
		{
			if( file_exists(_PATH_.'core/'.$this->index[$className]['path']) )
				require_once _PATH_.'core/'.$this->index[$className]['path'];
			else
				echo('Clase '.$className.' no existe en array');
		}
	}
}
