<?php

class Cuidador
{
  	public static function getCuidadorWithFiltros($comienzo, $limite, $applyLimit=true)
	{
		/*$busqueda = Tools::getValue('busqueda', '');
		$search = "";
		$limit = "";

		if( $busqueda != '' )
			$search .= "AND (nombre LIKE '%".$busqueda."%' OR email LIKE '%".$busqueda."%' OR date_created LIKE '%".$busqueda."%')";

		if($applyLimit)
			$limit = "LIMIT $comienzo, $limite";

		$listado = Bd::getInstance()->fetchObject("SELECT * FROM usuarios_admin WHERE 1 $search ORDER BY nombre $limit");

		$total = Bd::getInstance()->countRows("SELECT * FROM usuarios_admin WHERE 1 $search ORDER BY nombre");

		return array(
			'listado' => $listado,
			'total' => $total
		);*/
	}

	public static function getCuidadorById($id_cuidador)
	{
		return Bd::getInstance()->fetchRow("SELECT * FROM cuidadores WHERE id='$id_cuidador'");
	}

    public static function getCuidadorBySlug($slug)
    {
        return Bd::getInstance()->fetchRow("SELECT * FROM cuidadores WHERE slug='$slug'");
    }

	public static function actualizarCuidador()
	{
		/*$updUsuario = array(
			'nombre' => Tools::getValue('nombre'),
			'email'  => Tools::getValue('email')
		);

		$password = Tools::getValue('password', '');

		if( !empty($password) && strlen($password) > 0 )
			$updUsuario['password'] = Tools::md5($password);

		return Bd::getInstance()->update('usuarios_admin', $updUsuario, "id_usuario_admin = ".(int)Tools::getValue('id_usuario_admin'));*/
	}

	public static function crearCuidador()
	{
		/*$addUsuario = array(
			'nombre' 	   => Tools::getValue('nombre'),
			'email' 	   => Tools::getValue('email'),
			'password' 	   => Tools::md5(Tools::getValue('password')),
			'date_created' => Tools::datetime()
		);

		return Bd::getInstance()->insert('usuarios_admin', $addUsuario);*/
	}

	public static function eliminarRegistro( $id )
	{
		return Bd::getInstance()->query("DELETE FROM cuidadores WHERE id = '$id'");
	}
}
