<?php

class Admin
{
  	public static function login($usuario, $password)
	{
		$datos = Bd::getInstance()->fetchRow("SELECT * FROM usuarios_admin WHERE email='".$usuario."' AND password='".$password."'");

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
			$search .= "AND (nombre LIKE '%".$busqueda."%' OR email LIKE '%".$busqueda."%' OR date_created LIKE '%".$busqueda."%')";

		if($applyLimit)
			$limit = "LIMIT $comienzo, $limite";

		$listado = Bd::getInstance()->fetchObject("SELECT * FROM usuarios_admin WHERE 1=1 $search ORDER BY nombre ASC $limit");

		$total = Bd::getInstance()->countRows("SELECT * FROM usuarios_admin WHERE 1=1 $search ORDER BY nombre ASC");

		return array(
			'listado' => $listado,
			'total' => $total
		);
	}

	public static function getUsuarioById($id_usuario_admin)
	{
		return Bd::getInstance()->fetchRow("SELECT * FROM usuarios_admin WHERE id_usuario_admin=".(int)$id_usuario_admin);
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

		return Bd::getInstance()->update('usuarios_admin', $updUsuario, "id_usuario_admin = ".(int)Tools::getValue('id_usuario_admin'));
	}

	public static function crearUsuario()
	{
		$addUsuario = array(
			'nombre' 	   => Tools::getValue('nombre'),
			'email' 	   => Tools::getValue('email'),
			'password' 	   => Tools::md5(Tools::getValue('password')),
			'date_created' => Tools::datetime()
		);

		return Bd::getInstance()->insert('usuarios_admin', $addUsuario);
	}

	public static function eliminarRegistro( $id )
	{
		return Bd::getInstance()->query("DELETE FROM usuarios_admin WHERE id_usuario_admin = ".(int)$id);
	}
}
