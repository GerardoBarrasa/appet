<?php

class Admin
{
  	public static function login($usuario, $password)
	{
		$datos = Bd::getInstance()->fetchRow("SELECT *, '".date('Y-m-d H:i:s')."' AS last_access FROM usuarios_admin WHERE email='".$usuario."' AND password='".$password."'");

		if( $datos )
		{
			$_SESSION['admin_panel'] = $datos;
            // Inicializar admin_vars si no existe
            if (!isset($_SESSION['admin_vars'])) {
                $_SESSION['admin_vars'] = array();
            }
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
        
        // Inicializar admin_vars si no existe
        if (!isset($_SESSION['admin_vars'])) {
            $_SESSION['admin_vars'] = array('entorno' => _ADMIN_);
        }
        
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
        } else {
            // Si no hay userslug, establecer entorno por defecto
            $_SESSION['admin_vars']['entorno'] = _ADMIN_;
        }
	}

	public static function validateUser()
	{
        // Evitar bucles infinitos verificando si ya se ha validado en esta solicitud
        static $validationInProgress = false;
        
        __log_error('=== VALIDATE_USER START ===', 0, 'validate_user');
        __log_error('validationInProgress: ' . ($validationInProgress ? 'true' : 'false'), 0, 'validate_user');
        __log_error('admin_panel exists: ' . (isset($_SESSION['admin_panel']) ? 'true' : 'false'), 0, 'validate_user');
        
        if ($validationInProgress) {
            __log_error('VALIDATION IN PROGRESS - RETURNING', 0, 'validate_user');
            return; // Evitar recursión
        }
        
        $validationInProgress = true;
        
        try {
            if(isset($_SESSION['admin_panel'])){
                __log_error('admin_panel data: ' . json_encode($_SESSION['admin_panel']), 0, 'validate_user');
                __log_error('admin_vars before: ' . json_encode($_SESSION['admin_vars'] ?? 'not_set'), 0, 'validate_user');
                
                // Comprobamos los datos del usuario logueado
                $getUserDataResult = Admin::getUsuarioDataById($_SESSION['admin_panel']->id_usuario_admin);
                __log_error('getUsuarioDataById result: ' . ($getUserDataResult ? 'true' : 'false'), 0, 'validate_user');
                
                if($getUserDataResult) {
                    __log_error('admin_panel after getUsuarioDataById: ' . json_encode($_SESSION['admin_panel']), 0, 'validate_user');
                    
                    // Verificar que cuidador_entorno existe antes de usarlo
                    $cuidadorEntorno = $_SESSION['admin_panel']->cuidador_entorno ?? 'not_set';
                    $currentEntorno = $_SESSION['admin_vars']['entorno'] ?? 'not_set';
                    
                    __log_error('cuidador_entorno: ' . $cuidadorEntorno, 0, 'validate_user');
                    __log_error('current entorno: ' . $currentEntorno, 0, 'validate_user');
                    
                    if(isset($_SESSION['admin_panel']->cuidador_entorno) && 
                       isset($_SESSION['admin_vars']['entorno']) && 
                       $_SESSION['admin_panel']->cuidador_entorno != '' &&
                       $_SESSION['admin_vars']['entorno'] != $_SESSION['admin_panel']->cuidador_entorno.'/'){
                    
                        $dest = $_SESSION['admin_panel']->cuidador_entorno.'/';
                        __log_error('REDIRECTING TO: ' . $dest, 0, 'validate_user');
                        header("Location: "._DOMINIO_.$dest);
                        exit;
                    }
                    
                    // Asegurar que cuidador_entorno existe y establecer entorno
                    if(isset($_SESSION['admin_panel']->cuidador_entorno) && $_SESSION['admin_panel']->cuidador_entorno != '') {
                        $_SESSION['admin_vars']['entorno'] = $_SESSION['admin_panel']->cuidador_entorno.'/';
                        __log_error('Set entorno to: ' . $_SESSION['admin_vars']['entorno'], 0, 'validate_user');
                    } else {
                        $_SESSION['admin_vars']['entorno'] = _ADMIN_;
                        __log_error('Set entorno to default: ' . $_SESSION['admin_vars']['entorno'], 0, 'validate_user');
                    }
                    
                    $validateUser = Admin::validateUserData($_SESSION['admin_panel']);
                    __log_error('validateUserData result: ' . $validateUser, 0, 'validate_user');
                    
                    if( $validateUser != 'ok' )
                    {
                        $_SESSION['actions_mensajeError'] = $validateUser;
                        $dest = $_SESSION['admin_vars']['entorno'];
                        __log_error('VALIDATION FAILED - LOGOUT AND REDIRECT TO: ' . $dest, 0, 'validate_user');
                        Admin::logout();
                        header("Location: "._DOMINIO_.$dest);
                        exit;
                    }
                    
                    __log_error('VALIDATION SUCCESS', 0, 'validate_user');
                }
                else {
                    $_SESSION['admin_vars']['entorno'] = _ADMIN_;
                    $_SESSION['actions_mensajeError'] = 'El usuario no es válido';
                    __log_error('USER DATA INVALID - LOGOUT AND REDIRECT TO ADMIN', 0, 'validate_user');
                    Admin::logout();
                    header("Location: "._DOMINIO_._ADMIN_);
                    exit;
                }
            } else {
                __log_error('NO ADMIN PANEL SESSION', 0, 'validate_user');
            }
        } finally {
            $validationInProgress = false;
            __log_error('=== VALIDATE_USER END ===', 0, 'validate_user');
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
        // Evitar bucles infinitos verificando si ya se ha consultado este usuario
        static $processingUsers = [];
        
        __log_error('=== GET_USUARIO_DATA_BY_ID START ===', 0, 'get_usuario_data');
        __log_error('id_usuario_admin: ' . $id_usuario_admin, 0, 'get_usuario_data');
        __log_error('processingUsers: ' . json_encode(array_keys($processingUsers)), 0, 'get_usuario_data');
        
        if (isset($processingUsers[$id_usuario_admin])) {
            __log_error('USER ALREADY PROCESSING - RETURNING TRUE', 0, 'get_usuario_data');
            return true; // Ya estamos procesando este usuario, evitar recursión
        }
        
        $processingUsers[$id_usuario_admin] = true;
        
        try {
            $sql = "SELECT u.*, '".(isset($_SESSION['admin_panel']->last_access) ? $_SESSION['admin_panel']->last_access : date('Y-m-d H:i:s'))."' AS last_access, IF(u.idperfil=1,'admin',IFNULL(c.slug, 'admin')) AS cuidador_slug, IF(u.idperfil=1,'admin',IF(c.slug IS NOT NULL, CONCAT('appet-',slug), '')) AS cuidador_entorno, IF(u.idperfil=1,'0',IFNULL(c.id, '')) AS cuidador_id, IF(u.idperfil=1,'ApPet',IFNULL(c.nombre, '')) AS cuidador_nombre, IF(u.idperfil=1,'1',IFNULL(c.estado, 0)) AS cuidador_estado FROM usuarios_admin u LEFT JOIN usuarios_cuidadores uc ON u.id_usuario_admin=uc.id_usuario LEFT JOIN cuidadores c ON c.id=uc.id_cuidador WHERE u.id_usuario_admin='".$id_usuario_admin."'";
            
            __log_error('SQL: ' . $sql, 0, 'get_usuario_data');
            
            $datos = Bd::getInstance()->fetchRow($sql);
            
            __log_error('Query result: ' . ($datos ? json_encode($datos) : 'false'), 0, 'get_usuario_data');

            if ($datos) {
                $_SESSION['admin_panel'] = $datos;
                __log_error('SESSION UPDATED WITH NEW DATA', 0, 'get_usuario_data');
                return true;
            }
            
            __log_error('NO DATA FOUND', 0, 'get_usuario_data');
            return false;
        } finally {
            unset($processingUsers[$id_usuario_admin]);
            __log_error('=== GET_USUARIO_DATA_BY_ID END ===', 0, 'get_usuario_data');
        }
	}

	public static function getEntornoLogo()
    {
        if(!isset($_SESSION['admin_panel']) || $_SESSION['admin_panel']->idperfil == 1){
            return _RESOURCES_._COMMON_."img/appet_logotipo.png";
        }
        else{
            // Verificar que las propiedades existan
            if(!isset($_SESSION['admin_panel']->cuidador_id) || !isset($_SESSION['admin_panel']->cuidador_slug) || 
               empty($_SESSION['admin_panel']->cuidador_id) || empty($_SESSION['admin_panel']->cuidador_slug)) {
                return _RESOURCES_._COMMON_."img/appet_logotipo.png";
            }
            
            $imagePath = _RESOURCES_PATH_.'private/cuidadores/'.$_SESSION['admin_panel']->cuidador_id.'/'.$_SESSION['admin_panel']->cuidador_id.'_'.$_SESSION['admin_panel']->cuidador_slug.'.jpg';
            
            // Verificar que el archivo existe antes de procesarlo
            if(!file_exists($imagePath)) {
                return _RESOURCES_._COMMON_."img/appet_logotipo.png";
            }
            
            try {
                $resizedImage = Tools::resize_image($imagePath, 100);
                if($resizedImage !== false) {
                    return "data:image/png;base64,".base64_encode($resizedImage);
                }
            } catch (Exception $e) {
                __log_error('Error resizing logo image: ' . $e->getMessage());
            }
            
            // Fallback al logo por defecto
            return _RESOURCES_._COMMON_."img/appet_logotipo.png";
        }
	}

	public static function validateUserData($userData): string
    {
        __log_error('=== VALIDATE_USER_DATA START ===', 0, 'validate_user_data');
        
        if (!isset($userData)) {
            __log_error('NO USER DATA PROVIDED', 0, 'validate_user_data');
            return "Datos de usuario no válidos";
        }
        
        __log_error('userData: ' . json_encode($userData), 0, 'validate_user_data');
        
        $result = 'ok';
        
        // Comprobamos si el usuario está activo
        if(isset($userData->estado) && $userData->estado == 0){
            $result = "El usuario indicado no está activo";
            __log_error('USER INACTIVE: ' . $result, 0, 'validate_user_data');
            goto end;
        }
        
        // Comprobamos si la contraseña ha sido modificada tras el último acceso
        if(isset($userData->last_access) && isset($userData->pass_updated) && $userData->last_access < $userData->pass_updated){
            $result = "La contraseña ha sido modificada, debe volver a acceder";
            __log_error('PASSWORD UPDATED: ' . $result, 0, 'validate_user_data');
            goto end;
        }
        
        // Comprobamos si es un súper admin para acceder
        if(isset($userData->cuidador_entorno) && isset($userData->idperfil) && 
           $userData->cuidador_entorno == 'admin' && $userData->idperfil != 1){
        $result = "No tiene permiso para acceder";
            __log_error('NO ADMIN PERMISSION: ' . $result, 0, 'validate_user_data');
            goto end;
        }
        
        // Comprobamos si el usuario tiene un cuidador asignado (solo para usuarios no super admin)
        if(isset($userData->idperfil) && $userData->idperfil != 1 && 
           isset($userData->cuidador_entorno) && $userData->cuidador_entorno == ''){
            $_SESSION['admin_vars']['entorno'] = _ADMIN_;
            $result = "Su usuario no está asignado a ninguna cuenta";
            __log_error('NO CUIDADOR ASSIGNED: ' . $result, 0, 'validate_user_data');
            goto end;
        }
        
        // Comprobamos si la cuenta del cuidador está activa (solo para usuarios no super admin)
        if(isset($userData->idperfil) && $userData->idperfil != 1 && 
           isset($userData->cuidador_estado) && $userData->cuidador_estado == 0){
            $_SESSION['admin_vars']['entorno'] = _ADMIN_;
            $result = "La cuenta a la que intenta acceder no está operativa";
            __log_error('CUIDADOR INACTIVE: ' . $result, 0, 'validate_user_data');
            goto end;
        }
        
        __log_error('VALIDATION PASSED', 0, 'validate_user_data');
        
        end:
        __log_error('FINAL RESULT: ' . $result, 0, 'validate_user_data');
        __log_error('=== VALIDATE_USER_DATA END ===', 0, 'validate_user_data');
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
