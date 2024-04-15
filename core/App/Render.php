<?php

class Render
{
	public static $page;
	public static $data;
	public static $layout_data;
	public static $layout = 'front-end';

	//Funcion que devuelve el layaout.
	public static function getLayout()
	{
		if( self::$layout_data )
		{
			foreach(self::$layout_data as $key => $value)
				${$key} = $value;
		}

		if( self::$data )
		{
			foreach(self::$data as $key => $value)
				${$key} = $value;
		}

		if(self::$layout)
			include(_PATH_.'layout/'.self::$layout.'.php');
	}

	//Creamos pagina
	public static function getPage()
	{
		if( self::$layout_data )
		{
			foreach(self::$layout_data as $key => $value)
				${$key} = $value;
		}

		if( self::$data )
		{
			foreach(self::$data as $key => $value)
				${$key} = $value;
		}

		$file = _PATH_.'pages'.DIRECTORY_SEPARATOR.self::$page.'.php';

		if( !file_exists($file) )
			$file = _PATH_.'pages'.DIRECTORY_SEPARATOR.'404.php';
	
		include($file);
	}

	//Configuramos página para mostrarla con layout
	public static function page($name,$data=array())
	{
		self::$page = $name;
		self::$data = $data;
	}

	//Configuramos y mostramos pagina sin layout
	public static function showPage($name,$data=array())
	{
		self::$page = $name;
		self::$data = $data;
		self::$layout = false;
		self::getPage();
	}

	//Creamos pagina
	public static function getAdminPage()
	{
		if( self::$layout_data )
		{
			foreach(self::$layout_data as $key => $value)
				${$key} = $value;
		}

		if ( self::$data )
		{
			foreach(self::$data as $key => $value)
				${$key} = $value;
		}

		$file = _PATH_.'pages/'._ADMIN_.DIRECTORY_SEPARATOR.self::$page.'.php';

		if ( !file_exists($file) )
			$file = _PATH_.'pages/'._ADMIN_.DIRECTORY_SEPARATOR.'404.php';

		include($file);
	}

	//Configuramos página admin para mostrarla con layout
	public static function adminPage($name,$data=array())
	{
		self::$page = $name;
		self::$data = $data;
	}

	//Configuramos página admin para mostrarla con layout
	public static function actionPage($name,$data=array())
	{
		self::$page = $name;
		self::$data = $data;
        self::$layout = "actions";
	}

	//Configuramos y mostramos pagina
	public static function showAdminPage($name,$data=array())
	{
		self::$page = $name;
		self::$data = $data;
		self::$layout = false;
		self::getAdminPage();
	}

	//Mostramos bloque
	public static function bloq($page,$data=array())
	{
		if ( $data )
		{
			foreach($data as $key => $value)
				${$key} = $value;
		}

		$file = _PATH_.'pages'.DIRECTORY_SEPARATOR.$page.'.php';

		if ( !file_exists($file) )
			$file = _PATH_.'pages'.DIRECTORY_SEPARATOR.'404.php';

		include($file);
	}

	public static function getAjaxPage($name,$data=array())
	{
		if ( $data )
		{
			foreach($data as $key => $value)
				${$key} = $value;
		}

		$file = _PATH_.'pages/ajax'.DIRECTORY_SEPARATOR.$name.'.php';

		if ( !file_exists($file) )
			$file = _PATH_.'pages/ajax'.DIRECTORY_SEPARATOR.'404.php';

		ob_start();
		include($file);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public static function getPDFPage($name, $data=array())
	{
		if ( $data )
		{
			foreach($data as $key => $value)
				${$key} = $value;
		}

		$file = _PATH_.'pages/pdf'.DIRECTORY_SEPARATOR.$name.'.php';

		if ( !file_exists($file) )
			return '<html>No existe la pagina.</html>';

		ob_start();
		include($file);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
