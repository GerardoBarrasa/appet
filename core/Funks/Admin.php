<?php

class Admin
{
    /**
     * Realiza el login de un usuario administrador
     *
     * @param string $usuario Email del usuario
     * @param string $password Contraseña encriptada
     * @return bool
     */
    public static function login($usuario, $password)
    {
        $db = Bd::getInstance();

        // Usar consulta preparada para prevenir SQL injection
        $datos = $db->fetchRowSafe(
            "SELECT id_usuario_admin FROM usuarios_admin WHERE email = ? AND password = ?",
            [$usuario, $password]
        );

        if ($datos) {
            //$_SESSION['admin_panel'] = (object)$datos;
            return Admin::getUsuarioDataById($datos->id_usuario_admin);
        }
        return false;
    }

    /**
     * Cierra la sesión del usuario administrador
     */
    public static function logout()
    {
        unset($_SESSION['admin_panel']);
        unset($_SESSION['admin_vars']);
    }

    /**
     * Obtiene y configura el entorno del usuario
     */
    public static function getEntorno()
    {
        $userslug = Tools::getValue('userslug');
        // Si estamos recibiendo un entorno comprobamos que existe y está activo, si no redirijimos a admin
        if ($userslug) {
            $entorno = Cuidador::getCuidadorBySlug($userslug);
            if (!$entorno) {
                $_SESSION['actions_mensajeError'] = 'El entorno al que intenta acceder no existe';
                $dest = $_SESSION['admin_vars']['entorno'];
                Admin::logout();
                header("Location: " . _DOMINIO_ . $dest);
                exit;
            } else {
                // Comprobamos si está activo además de existir
                if ($entorno->estado == 0) {
                    $_SESSION['actions_mensajeError'] = 'El entorno al que intenta acceder no está disponible';
                    $dest = $_SESSION['admin_vars']['entorno'];
                    Admin::logout();
                    header("Location: " . _DOMINIO_ . $dest);
                    exit;
                } else {
                    $_SESSION['admin_vars']['entorno'] = 'appet-' . $userslug . '/';
                }
            }
        }
    }

    /**
     * Valida los datos del usuario logueado
     */
    public static function validateUser()
    {
        if (isset($_SESSION['admin_panel'])) {
            // Comprobamos los datos del usuario logueado
            if (Admin::getUsuarioDataById($_SESSION['admin_panel']->id_usuario_admin)) {
                if ($_SESSION['admin_vars']['entorno'] != $_SESSION['admin_panel']->cuidador_entorno . '/') {
                    $dest = $_SESSION['admin_panel']->cuidador_entorno . '/';
                    header("Location: " . _DOMINIO_ . $dest);
                    exit;
                }
                $_SESSION['admin_vars']['entorno'] = $_SESSION['admin_panel']->cuidador_entorno . '/';
                $validateUser = Admin::validateUserData($_SESSION['admin_panel']);
                if ($validateUser != 'ok') {
                    $_SESSION['actions_mensajeError'] = $validateUser;
                    $dest = $_SESSION['admin_vars']['entorno'];
                    Admin::logout();
                    header("Location: " . _DOMINIO_ . $dest);
                    exit;
                }
            } else {
                $_SESSION['admin_vars']['entorno'] = _ADMIN_;
                $_SESSION['actions_mensajeError'] = 'El usuario no es válido';
                Render::$layout = false;
                $dest = $_SESSION['admin_vars']['entorno'];
                Admin::logout();
                header("Location: " . _DOMINIO_ . $dest);
                exit;
            }
        }
    }

    /**
     * Obtiene usuarios administradores con filtros
     *
     * @param int $comienzo Inicio de la paginación
     * @param int $limite Límite de resultados
     * @param bool $applyLimit Aplicar límite o no
     * @return array
     */
    public static function getUsuariosWithFiltros($comienzo, $limite, $filtros, $applyLimit = true)
    {
        $db = Bd::getInstance();
        $busqueda = Tools::getValue('busqueda', '');
        $params = [];
        $whereConditions = ["1"];
        $joins = '';

        if (!empty($filtros['busqueda'])) {
            $whereConditions[] = "(u.nombre LIKE ? OR u.email LIKE ?)";
            $params[] = "%{$filtros['busqueda']}%";
            $params[] = "%{$filtros['busqueda']}%";
        }
        if (!empty($filtros['tipo'])) {
            $whereConditions[] = "u.idperfil = ?";
            $params[] = "{$filtros['tipo']}";
        }
        // Nos aseguramos de que un usuario que no sea admin no pueda ver usuarios con mayor rango que el suyo (un idperfil menor) ni otros usuarios con su mismo rango pero asignados a otro cuidador (en caso de perfil 2) ni otros tutores asociados a un cuidador que no sea el logueado
        if($_SESSION['admin_panel']->idperfil == 2){// Es un cuidador
            $joins = "LEFT JOIN usuarios_cuidadores uc ON u.id_usuario_admin = uc.id_usuario";
            $whereConditions[] = "uc.id_cuidador = ?";
            $params[] = $_SESSION['admin_panel']->cuidador_id;
        }
        else if($_SESSION['admin_panel']->idperfil == 3){// Es un tutor, solo puede ver su perfil
            $whereConditions[] = "u.id_usuario_admin = ?";
            $params[] = $_SESSION['admin_panel']->id_usuario_admin;
        }

        $whereClause = implode(' AND ', $whereConditions);
        $selectForList = "SELECT * ";
        $selectForCount = "SELECT COUNT(u.id_usuario_admin) ";
        $sql = "FROM usuarios_admin u $joins WHERE {$whereClause}";

        // Contar total de registros
        $total = $db->fetchValueSafe($selectForCount.$sql, $params);

        // Aplicar límite si es necesario
        if ($applyLimit) {
            $sql .= " GROUP BY u.id_usuario_admin ORDER BY u.date_created DESC LIMIT ?, ?";
            $sql = $selectForList.$sql;
            $params[] = (int)$comienzo;
            $params[] = (int)$limite;
        }

        $listado = $db->fetchAllSafe($sql, $params);

        return [
            'listado' => $listado,
            'total' => $total
        ];
    }

    /**
     * Obtiene un usuario administrador por ID
     *
     * @param int $id_usuario_admin ID del usuario
     * @return object|false
     */
    public static function getUsuarioById($id_usuario_admin)
    {
        $db = Bd::getInstance();
        return $db->fetchRowSafe(
            "SELECT * FROM usuarios_admin WHERE id_usuario_admin = ?",
            [(int)$id_usuario_admin]
        );
    }

    /**
     * Obtiene datos completos de un usuario administrador por ID
     *
     * @param int $id_usuario_admin ID del usuario
     * @return bool
     */
    public static function getUsuarioDataById($id_usuario_admin): bool
    {
        $db = Bd::getInstance();

        $sql = "SELECT u.*, ? AS last_access, 
                IF(u.idperfil=1,'admin',IFNULL(c.slug, 'admin')) AS cuidador_slug, 
                IF(u.idperfil=1,'admin',IF(c.slug IS NOT NULL, CONCAT('appet-',slug), '')) AS cuidador_entorno, 
                IF(u.idperfil=1,'0',IFNULL(c.id, '')) AS cuidador_id, 
                IF(u.idperfil=1,'ApPet',IFNULL(c.nombre, '')) AS cuidador_nombre, 
                IF(u.idperfil=1,'1',IFNULL(c.estado, 0)) AS cuidador_estado 
                FROM usuarios_admin u 
                LEFT JOIN usuarios_cuidadores uc ON u.id_usuario_admin=uc.id_usuario 
                LEFT JOIN cuidadores c ON c.id=uc.id_cuidador 
                WHERE u.id_usuario_admin = ?";

        $datos = $db->fetchRowSafe($sql, [
            $_SESSION['admin_panel']->last_access ?? date('Y-m-d H:i:s'),
            (int)$id_usuario_admin
        ]);

        if ($datos) {
            $_SESSION['admin_panel'] = (object)$datos;
            return true;
        }
        return false;
    }

    /**
     * Obtiene el logo del entorno actual
     *
     * @return string URL del logo
     */
    public static function getEntornoLogo()
    {
        if ($_SESSION['admin_panel']->idperfil == 1) {
            return _RESOURCES_ . _COMMON_ . "img/appet_logotipo.png";
        } else {
            return "data:image/png;base64," . base64_encode(Tools::resize_image(
                    _RESOURCES_PATH_ . 'private/cuidadores/' . $_SESSION['admin_panel']->cuidador_id . '/' .
                    $_SESSION['admin_panel']->cuidador_id . '_' . $_SESSION['admin_panel']->cuidador_slug . '.jpg',
                    100
                ));
        }
    }

    /**
     * Valida los datos del usuario
     *
     * @param object $userData Datos del usuario
     * @return string
     */
    public static function validateUserData($userData): string
    {
        $result = 'ok';
        // Comprobamos si el usuario está activo
        if ($userData->estado == 0) {
            $result = "El usuario indicado no está activo";
            goto end;
        }
        // Comprobamos si la contraseña ha sido modificada tras el último acceso
        if ($userData->last_access < $userData->pass_updated) {
            $result = "La contraseña ha sido modificada, debe volver a acceder";
            goto end;
        }
        // Comprobamos si es un súper admin para acceder
        if ($userData->cuidador_entorno == 'admin' && $userData->idperfil != 1) {
            $result = "No tiene permiso para acceder";
            goto end;
        }
        // Comprobamos si el usuario tiene un cuidador asignado
        if ($userData->cuidador_entorno == '') {
            $_SESSION['admin_vars']['entorno'] = _ADMIN_;
            $result = "Su usuario no está asignado a ninguna cuenta";
            goto end;
        }
        // Comprobamos si la cuenta del cuidador está activa
        if ($userData->cuidador_estado == 0) {
            $_SESSION['admin_vars']['entorno'] = _ADMIN_;
            $result = "La cuenta a la que intenta acceder no está operativa";
            goto end;
        }
        end:
        return $result;
    }

    /**
     * Muestra el dashboard según el tipo de usuario
     */
    public static function showDashborad()
    {
        if ($_SESSION['admin_panel']->idperfil == 1) {
            // Dashboard para administradores
        } else {
            // Dashboard para cuidadores
        }
    }

    /**
     * Actualiza los datos de un usuario administrador
     *
     * @return array Resultado de la operación
     */
    public static function actualizarUsuario()
    {
        $db = Bd::getInstance();
        $id_usuario_admin = (int)Tools::getValue('id_usuario_admin');

        $result = [
            'success' => false,
            'errors' => [],
            'data' => null
        ];

        // Validar que el usuario existe
        if ($id_usuario_admin <= 0) {
            $result['errors'][] = 'ID de usuario no válido';
            return $result;
        }

        $usuarioExistente = self::getUsuarioById($id_usuario_admin);
        if (!$usuarioExistente) {
            $result['errors'][] = 'El usuario no existe';
            return $result;
        }

        // Obtener y sanitizar datos
        $nombre = Tools::sanitizeInput(Tools::getValue('nombre'));
        $apellidos = Tools::sanitizeInput(Tools::getValue('apellidos'));
        $email = Tools::sanitizeInput(Tools::getValue('email'));
        $password = Tools::getValue('password', '');

        // Validar nombre
        $nombreValidation = Tools::validateNombre($nombre);
        if (!$nombreValidation['valid']) {
            $result['errors'] = array_merge($result['errors'], $nombreValidation['errors']);
        }

        // Validar email
        $emailValidation = Tools::validateEmail($email, $id_usuario_admin);
        if (!$emailValidation['valid']) {
            $result['errors'] = array_merge($result['errors'], $emailValidation['errors']);
        }

        // Validar contraseña si se proporciona
        if (!empty($password)) {
            $passwordValidation = Tools::validatePasswordStrength($password);
            if (!$passwordValidation['valid']) {
                $result['errors'] = array_merge($result['errors'], $passwordValidation['errors']);
            }
        }

        // Si hay errores, retornar
        if (!empty($result['errors'])) {
            return $result;
        }

        // Preparar datos para actualización
        $updUsuario = [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => strtolower($email)
        ];

        // Añadir contraseña si se proporciona
        if (!empty($password)) {
            $updUsuario['password'] = Tools::md5($password);
            $updUsuario['pass_updated'] = Tools::datetime();
        }

        // Realizar actualización
        try {
            $updateResult = $db->updateSafe(
                'usuarios_admin',
                $updUsuario,
                'id_usuario_admin = ?',
                [$id_usuario_admin]
            );

            if ($updateResult) {
                $result['success'] = true;
                $result['data'] = [
                    'id_usuario_admin' => $id_usuario_admin,
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'email' => strtolower($email),
                    'password_updated' => !empty($password)
                ];
            } else {
                $result['errors'][] = 'Error al actualizar el usuario en la base de datos';
            }
        } catch (Exception $e) {
            $result['errors'][] = 'Error interno: ' . $e->getMessage();
            Tools::logError('Error actualizando usuario: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Crea un nuevo usuario administrador
     *
     * @return array Resultado de la operación
     */
    public static function crearUsuario()
    {
        $db = Bd::getInstance();

        $result = [
            'success' => false,
            'errors' => [],
            'data' => null
        ];

        // Obtener y sanitizar datos
        $nombre = Tools::sanitizeInput(Tools::getValue('nombre'));
        $apellidos = Tools::sanitizeInput(Tools::getValue('apellidos'));
        $email = Tools::sanitizeInput(Tools::getValue('email'));
        $password = Tools::getValue('password');
        $idperfil = (int)Tools::getValue('idperfil', 2); // Por defecto perfil de cuidador

        // Validar nombre
        $nombreValidation = Tools::validateNombre($nombre);
        if (!$nombreValidation['valid']) {
            $result['errors'] = array_merge($result['errors'], $nombreValidation['errors']);
        }

        // Validar email
        $emailValidation = Tools::validateEmail($email);
        if (!$emailValidation['valid']) {
            $result['errors'] = array_merge($result['errors'], $emailValidation['errors']);
        }

        // Validar contraseña
        if (empty($password)) {
            $result['errors'][] = 'La contraseña es obligatoria';
        } else {
            $passwordValidation = Tools::validatePasswordStrength($password);
            if (!$passwordValidation['valid']) {
                $result['errors'] = array_merge($result['errors'], $passwordValidation['errors']);
            }
        }

        // Validar perfil
        if (!in_array($idperfil, [1, 2, 3])) {
            $result['errors'][] = 'El perfil seleccionado no es válido';
        }

        // Verificar permisos para crear usuarios
        if (isset($_SESSION['admin_panel'])) {
            // Solo superadmin puede crear otros superadmin
            if ($idperfil == 1 && $_SESSION['admin_panel']->idperfil != 1) {
                $result['errors'][] = 'No tienes permisos para crear usuarios administradores';
            }

            // Los cuidadores solo pueden crear tutores de su mismo cuidador
            if ($_SESSION['admin_panel']->idperfil == 2 && $idperfil != 3) {
                $result['errors'][] = 'Solo puedes crear usuarios con perfil de tutor';
            }
        }

        // Si hay errores, retornar
        if (!empty($result['errors'])) {
            return $result;
        }

        // Preparar datos para inserción
        $addUsuario = [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => strtolower($email),
            'password' => Tools::md5($password),
            'date_created' => Tools::datetime(),
            'pass_updated' => Tools::datetime(),
            'idperfil' => $idperfil,
            'estado' => 1
        ];

        // Realizar inserción
        try {
            $userId = $db->insertSafe('usuarios_admin', $addUsuario);

            if ($userId) {
                // Si es un cuidador o tutor, asociarlo al cuidador correspondiente
                if ($idperfil > 1 && isset($_SESSION['admin_panel'])) {
                    $cuidadorId = $_SESSION['admin_panel']->cuidador_id ?? 1;

                    $db->insertSafe('usuarios_cuidadores', [
                        'id_usuario' => $userId,
                        'id_cuidador' => $cuidadorId
                    ]);
                }

                $result['success'] = true;
                $result['data'] = [
                    'id_usuario_admin' => $userId,
                    'nombre' => $nombre,
                    'email' => strtolower($email),
                    'idperfil' => $idperfil
                ];
            } else {
                $result['errors'][] = 'Error al crear el usuario en la base de datos';
            }
        } catch (Exception $e) {
            $result['errors'][] = 'Error interno: ' . $e->getMessage();
            Tools::logError('Error creando usuario: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Elimina un usuario administrador
     *
     * @param int $id ID del usuario
     * @return bool
     */
    public static function eliminarRegistro($id)
    {
        $db = Bd::getInstance();
        return $db->deleteSafe('usuarios_admin', 'id_usuario_admin = ?', [(int)$id]);
    }

    /**
     * Cambia la contraseña de un usuario
     *
     * @param int $id_usuario ID del usuario
     * @param string $password Nueva contraseña
     * @return bool
     */
    public static function cambiarPassword($id_usuario, $password)
    {
        $db = Bd::getInstance();

        return $db->updateSafe(
            'usuarios_admin',
            [
                'password' => Tools::md5($password),
                'pass_updated' => date('Y-m-d H:i:s')
            ],
            'id_usuario_admin = ?',
            [(int)$id_usuario]
        );
    }

    /**
     * Verifica si un email ya existe en la base de datos
     *
     * @param string $email Email a verificar
     * @param int $id_usuario ID del usuario (para excluirlo en actualizaciones)
     * @return bool
     */
    public static function emailExiste($email, $id_usuario = 0)
    {
        $db = Bd::getInstance();

        $params = [$email];
        $sql = "SELECT COUNT(*) FROM usuarios_admin WHERE email = ?";

        if ($id_usuario > 0) {
            $sql .= " AND id_usuario_admin != ?";
            $params[] = (int)$id_usuario;
        }

        return (int)$db->fetchValueSafe($sql, $params) > 0;
    }

    /**
     * Obtiene el total de usuarios administradores
     *
     * @return int
     */
    public static function getTotalUsuarios()
    {
        $db = Bd::getInstance();
        return (int)$db->fetchValueSafe("SELECT COUNT(*) FROM usuarios_admin");
    }

    /**
     * Obtiene errores de validación formateados para mostrar en frontend
     *
     * @param array $errors Array de errores
     * @return string HTML con errores formateados
     */
    public static function formatValidationErrors($errors)
    {
        if (empty($errors)) {
            return '';
        }

        $html = '<div class="alert alert-danger"><ul class="mb-0">';
        foreach ($errors as $error) {
            $html .= '<li>' . htmlspecialchars($error) . '</li>';
        }
        $html .= '</ul></div>';

        return $html;
    }
}
