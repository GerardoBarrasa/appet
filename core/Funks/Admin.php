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

        $q = "SELECT u.*, DATE_FORMAT(u.date_created, '%d-%m-%Y %H:%i:%s') as DATE_CREATED, c.nombre AS CREDENCIAL, IFNULL(at.nombre, '---') AS TYPE, IFNULL(a.name, '---') AS ACNAME FROM usuarios u INNER JOIN credenciales c ON u.id_credencial=c.id LEFT JOIN account a ON u.account_id=a.id LEFT JOIN account_types at ON a.type_id=at.id WHERE 1=1 $search ORDER BY u.nombre ASC";

		$listado = Bd::getInstance()->fetchObject( $q.$limit);

		$total = Bd::getInstance()->countRows($q);

		return array(
			'listado' => $listado,
			'total' => $total
		);
	}

	public static function getAccountsWithFiltros($comienzo, $limite, $applyLimit=true)
	{
		$busqueda = Tools::getValue('busqueda', '');
		$search = "";
		$limit = "";

		if( $busqueda != '' )
			$search .= "AND (a.name LIKE '%".$busqueda."%' OR a.email LIKE '%".$busqueda."%')";

		if($applyLimit)
			$limit = " LIMIT $comienzo, $limite";

        $q = "SELECT a.*, DATE_FORMAT(a.create_time, '%d-%m-%Y %H:%i:%s') as CREATE_TIME, IFNULL(at.nombre, 'ROOT') AS TYPE FROM account a LEFT JOIN account_types at ON a.type_id=at.id WHERE 1 $search ORDER BY a.name ASC";

		$listado = Bd::getInstance()->fetchObject( $q.$limit);

		$total = Bd::getInstance()->countRows($q);

		return array(
			'listado' => $listado,
			'total' => $total
		);
	}

    public static function getAccountById($id)
    {
        return Bd::getInstance()->fetchRow("SELECT * FROM account WHERE id=".(int)$id);
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
