<?php

class Admin
{
  	public static function login($usuario, $password)
	{
		$datos = Bd::getInstance()->fetchRow("SELECT *, '".date('Y-m-d H:i:s')."' AS last_access FROM usuarios_admin WHERE email='".$usuario."' AND password='".$password."'");

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
		unset($_SESSION['admin_vars']);
	}

	public static function getEntorno()
	{
        $userslug 	= Tools::getValue('userslug');
        // Si estamos recibiendo un entorno comprobamos que existe y está activo, si no redirijimos a admin
        if($userslug){
            $entorno = Cuidador::getCuidadorBySlug($userslug);
            if(!$entorno) {
                $_SESSION['actions_mensajeError'] = 'El entorno al que intenta acceder no existe';
                $dest = $_SESSION['admin_vars']['entorno'];
                Admin::logout();
                header("Location: "._DOMINIO_.$dest);
                exit;
            }
            else{
                // Comprobamos si está activo además de existir
                if($entorno->estado == 0){
                    $_SESSION['actions_mensajeError'] = 'El entorno al que intenta acceder no está disponible';
                    $dest = $_SESSION['admin_vars']['entorno'];
                    Admin::logout();
                    header("Location: "._DOMINIO_.$dest);
                    exit;
                }
                else{
                    $_SESSION['admin_vars']['entorno'] = 'appet-'.$userslug.'/';
                }
            }
        }
	}

	public static function validateUser()
	{
        if(isset($_SESSION['admin_panel'])){
            // Comprobamos los datos del usuario logueado
            if(Admin::getUsuarioDataById($_SESSION['admin_panel']->id_usuario_admin)) {
                if($_SESSION['admin_vars']['entorno'] != $_SESSION['admin_panel']->cuidador_entorno.'/'){
                    $dest = $_SESSION['admin_panel']->cuidador_entorno.'/';
                    header("Location: "._DOMINIO_.$dest);
                    exit;
                }
                $_SESSION['admin_vars']['entorno'] = $_SESSION['admin_panel']->cuidador_entorno.'/';
                $validateUser = Admin::validateUserData($_SESSION['admin_panel']);
                if( $validateUser != 'ok' )
                {
                    $_SESSION['actions_mensajeError'] = $validateUser;
                    $dest = $_SESSION['admin_vars']['entorno'];
                    Admin::logout();
                    header("Location: "._DOMINIO_.$dest);
                    exit;
                }
            }
            else {
                $_SESSION['admin_vars']['entorno'] = _ADMIN_;
                $_SESSION['actions_mensajeError'] = 'El usuario no es válido';
                Render::$layout = false;
                $dest = $_SESSION['admin_vars']['entorno'];
                Admin::logout();
                header("Location: "._DOMINIO_.$dest);
                exit;
            }
        }
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

		$listado = Bd::getInstance()->fetchObject("SELECT * FROM usuarios_admin WHERE 1 $search ORDER BY nombre $limit");

		$total = Bd::getInstance()->countRows("SELECT * FROM usuarios_admin WHERE 1 $search ORDER BY nombre");

		return array(
			'listado' => $listado,
			'total' => $total
		);
	}

	public static function getUsuarioById($id_usuario_admin)
	{
		return Bd::getInstance()->fetchRow("SELECT * FROM usuarios_admin WHERE id_usuario_admin=".(int)$id_usuario_admin);
	}

	public static function getUsuarioDataById($id_usuario_admin): bool
    {
        $datos = Bd::getInstance()->fetchRow("SELECT u.*, '".$_SESSION['admin_panel']->last_access."' AS last_access, IF(u.idperfil=1,'admin',IFNULL(c.slug, 'admin')) AS cuidador_slug, IF(u.idperfil=1,'admin',IF(c.slug IS NOT NULL, CONCAT('appet-',slug), '')) AS cuidador_entorno, IF(u.idperfil=1,'0',IFNULL(c.id, '')) AS cuidador_id, IF(u.idperfil=1,'ApPet',IFNULL(c.nombre, '')) AS cuidador_nombre, IF(u.idperfil=1,'1',IFNULL(c.estado, 0)) AS cuidador_estado FROM usuarios_admin u LEFT JOIN usuarios_cuidadores uc ON u.id_usuario_admin=uc.id_usuario LEFT JOIN cuidadores c ON c.id=uc.id_cuidador WHERE u.id_usuario_admin='".$id_usuario_admin."'");

        if( $datos )
        {
            $_SESSION['admin_panel'] = $datos;
            return true;
        }
        return false;
	}

	public static function getEntornoLogo()
    {
        if($_SESSION['admin_panel']->idperfil == 1){
            return _RESOURCES_._COMMON_."img/appet_logotipo.png";
        }
        else{
            return "data:image/png;base64,".base64_encode(Tools::resize_image(_RESOURCES_PATH_.'private/cuidadores/'.$_SESSION['admin_panel']->cuidador_id.'/'.$_SESSION['admin_panel']->cuidador_id.'_'.$_SESSION['admin_panel']->cuidador_slug.'.jpg', 100));
        }
	}

	public static function validateUserData($userData): string
    {
        $result = 'ok';
        // Comprobamos si el usuario está activo
        if($userData->estado == 0){
            $result = "El usuario indicado no está activo";
            goto end;
        }
        // Comprobamos si la contraseña ha sido modificada tras el último acceso
        if($userData->last_access < $userData->pass_updated){
            $result = "La contraseña ha sido modificada, debe volver a acceder";
            goto end;
        }
        // Comprobamos si es un súper admin para acceder
        if($userData->cuidador_entorno == 'admin' && $userData->idperfil != 1){
            $result = "No tiene permiso para acceder";
            goto end;
        }
        // Comprobamos si el usuario tiene un cuidador asignado
        if($userData->cuidador_entorno == ''){
            $_SESSION['admin_vars']['entorno'] = _ADMIN_;
            $result = "Su usuario no está asignado a ninguna cuenta";
            goto end;
        }
        // Comprobamos si la cuenta del cuidador está activa
        if($userData->cuidador_estado == 0){
            $_SESSION['admin_vars']['entorno'] = _ADMIN_;
            $result = "La cuenta a la que intenta acceder no está operativa";
            goto end;
        }
        end:
        return $result;
	}

    public static function showDashborad()
    {
        if($_SESSION['admin_panel']->idperfil == 1){

        }
        else{

        }
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
