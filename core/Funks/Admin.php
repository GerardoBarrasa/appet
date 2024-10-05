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
			$search .= "AND (ua.nombre LIKE '%".$busqueda."%' OR ua.email LIKE '%".$busqueda."%' OR ua.date_created LIKE '%".$busqueda."%')";

		if($applyLimit)
			$limit = "LIMIT $comienzo, $limite";

		if( $_SESSION['admin_panel']->id_perfil != 1 )
			$search .= " AND ua.id_perfil > 1";

		$listado = Bd::getInstance()->fetchObject("SELECT ua.*, p.nombre as perfil FROM usuarios_admin ua LEFT JOIN perfiles p ON p.id_perfil = ua.id_perfil WHERE 1=1 $search ORDER BY ua.nombre ASC $limit");

		$total = Bd::getInstance()->countRows("SELECT ua.*, p.nombre as perfil FROM usuarios_admin ua LEFT JOIN perfiles p ON p.id_perfil = ua.id_perfil WHERE 1=1 $search ORDER BY ua.nombre ASC");

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
			'email'  => Tools::getValue('email'),
			'id_perfil' => Tools::getValue('id_perfil')
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
			'id_perfil' => Tools::getValue('id_perfil'),
			'password' 	   => Tools::md5(Tools::getValue('password')),
			'date_created' => Tools::datetime()
		);

		return Bd::getInstance()->insert('usuarios_admin', $addUsuario);
	}

	public static function eliminarRegistro( $id )
	{
		return Bd::getInstance()->query("DELETE FROM usuarios_admin WHERE id_usuario_admin = ".(int)$id);
	}

	public static function getUsuarioByEmail($email)
	{
		return Bd::getInstance()->fetchRow("SELECT * FROM usuarios_admin WHERE email = '".$email."'");
	}

	public static function checkEmailExists($email, $ignore_id = false)
	{
		$where_not_id = "";
		if( !empty($ignore_id) )
			$where_not_id = " AND id_usuario_admin != ".(int)$ignore_id;
		$result = Bd::getInstance()->countRows("SELECT id_usuario_admin FROM usuarios_admin WHERE email = '".$email."'".$where_not_id);
		return empty($result) ? false : true;
	}

	public static function checkAccess($id_usuario_admin, $nombre_permiso, $redirectOnError = false)
	{
		$result = (bool) Bd::getInstance()->countRows("SELECT pp.* FROM permisos_perfiles pp JOIN permisos p ON pp.id_permiso = p.id_permiso AND p.nombre = '".$nombre_permiso."' JOIN usuarios_admin ua ON ua.id_perfil = pp.id_perfil AND ua.id_usuario_admin = ".(int)$id_usuario_admin);
		if( !$result && $redirectOnError )
		{
			Tools::registerAlert(l('admin-acceso-denegado'), 'error');
			Tools::redirect(_ADMIN_);
		}
		return $result;
	}

	public static function getPerfiles($comienzo, $limite, $applyLimit=true)
	{
		$limit = "";
		if($applyLimit)
			$limit = "LIMIT $comienzo, $limite";

		$listado = Bd::getInstance()->fetchObject("SELECT * FROM perfiles $limit");
		$total = Bd::getInstance()->countRows("SELECT * FROM perfiles");

		return array(
			'listado' => $listado,
			'total' => $total
		);
	}

	public static function getPermisosByIdPerfil($id_perfil)
	{
		return Bd::getInstance()->fetchObject("SELECT pp.*, pe.nombre as nombre_perfil, p.nombre, p.descripcion FROM permisos_perfiles pp JOIN permisos p ON pp.id_permiso = p.id_permiso JOIN perfiles pe ON pp.id_perfil = pe.id_perfil WHERE pp.id_perfil = ".(int)$id_perfil);
	}

	public static function getPermisos()
	{
		return Bd::getInstance()->fetchObject("SELECT * FROM permisos");
	}

	public static function getNombrePerfilById($id_perfil)
	{
		return Bd::getInstance()->fetchValue("SELECT nombre FROM perfiles WHERE id_perfil = ".(int)$id_perfil);
	}

	public static function guardarPermisos($id_perfil, $id_permisos)
	{
		Bd::getInstance()->query("DELETE FROM permisos_perfiles WHERE id_perfil = ".(int)$id_perfil);

		if( !empty($id_permisos) )
		{
			foreach( $id_permisos as $id_permiso )
			{
				$addPerfilPermiso = array(
					'id_perfil' => $id_perfil,
					'id_permiso' => $id_permiso
				);

				Bd::getInstance()->insert('permisos_perfiles', $addPerfilPermiso);
			}
		}
	}
}
