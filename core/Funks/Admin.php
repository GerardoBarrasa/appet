<?php

class Admin
{
  	public static function login($usuario, $password)
	{
		$datos = Bd::getInstance()->fetchRow("SELECT * FROM usuarios WHERE email='".$usuario."' AND password='".$password."'");

		if( $datos )
		{
			$_SESSION['admin_panel'] = $datos;
			return true;
		}
		return false;
	}

	public static function logout()
	{
		unset($_SESSION['admin_panel']);
	}

	public static function getUsuariosWithFiltros($comienzo, $limite, $applyLimit=true)
	{
		$busqueda = Tools::getValue('busqueda', '');
		$search = "";
		$limit = "";

		if( $busqueda != '' )
			$search .= "AND (u.nombre LIKE '%".$busqueda."%' OR u.email LIKE '%".$busqueda."%' OR u.date_created LIKE '%".$busqueda."%')";

		if($applyLimit)
			$limit = " LIMIT $comienzo, $limite";

        $q = "SELECT u.*, IFNULL(at.nombre, 'ROOT') AS TYPE, IFNULL(a.name, '---') AS ACNAME FROM usuarios u LEFT JOIN account a ON u.account_id=a.id LEFT JOIN account_types at ON a.type_id=at.id WHERE 1=1 $search ORDER BY u.nombre ASC";

		$listado = Bd::getInstance()->fetchObject( $q.$limit);

		$total = Bd::getInstance()->countRows($q);

		return array(
			'listado' => $listado,
			'total' => $total
		);
	}

	public static function getUsuarioById($id)
	{
		return Bd::getInstance()->fetchRow("SELECT * FROM usuarios WHERE id=".(int)$id);
	}

	public static function actualizarUsuario()
	{
		$updUsuario = array(
			'nombre' => Tools::getValue('nombre'),
			'email'  => Tools::getValue('email')
		);

		$password = Tools::getValue('password', '');

		if( !empty($password) && strlen($password) > 0 )
			$updUsuario['password'] = Tools::md5($password);

		return Bd::getInstance()->update('usuarios', $updUsuario, "id = ".(int)Tools::getValue('id'));
	}

	public static function crearUsuario()
	{
		$addUsuario = array(
			'nombre' 	   => Tools::getValue('nombre'),
			'email' 	   => Tools::getValue('email'),
			'password' 	   => Tools::md5(Tools::getValue('password')),
			'date_created' => Tools::datetime()
		);

		return Bd::getInstance()->insert('usuarios', $addUsuario);
	}

	public static function eliminarRegistro( $id )
	{
		return Bd::getInstance()->query("DELETE FROM usuarios WHERE id = ".(int)$id);
	}
}
