<?php

/**
 * Clase para gestión de permisos y control de acceso
 *
 * Esta clase maneja los permisos de usuarios basándose en sus perfiles
 * y proporciona métodos para verificar accesos y gestionar permisos.
 */
class Permisos
{
    /**
     * Cache de permisos del usuario actual
     */
    private static $userPermissions = null;

    /**
     * Cache de todos los permisos disponibles
     */
    private static $allPermissions = null;

    /**
     * Cache de todos los perfiles
     */
    private static $allProfiles = null;

    /**
     * Verifica si el usuario actual tiene un permiso específico
     *
     * @param string $permiso Nombre del permiso a verificar
     * @return bool True si tiene el permiso, false si no
     */
    public static function tienePermiso($permiso)
    {
        // Si no hay usuario logueado, no tiene permisos
        if (!isset($_SESSION['admin_panel'])) {
            return false;
        }

        // Los superadmin (perfil 1) tienen todos los permisos
        if ($_SESSION['admin_panel']->idperfil == 1) {
            return true;
        }

        // Cargar permisos del usuario si no están en cache
        if (self::$userPermissions === null) {
            self::cargarPermisosUsuario();
        }

        // Verificar si el permiso está en la lista del usuario
        return in_array($permiso, self::$userPermissions);
    }

    /**
     * Verifica si el usuario actual tiene alguno de los permisos especificados
     *
     * @param array $permisos Array de nombres de permisos
     * @return bool True si tiene al menos uno de los permisos
     */
    public static function tieneAlgunPermiso($permisos)
    {
        foreach ($permisos as $permiso) {
            if (self::tienePermiso($permiso)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica si el usuario actual tiene todos los permisos especificados
     *
     * @param array $permisos Array de nombres de permisos
     * @return bool True si tiene todos los permisos
     */
    public static function tieneTodosLosPermisos($permisos)
    {
        foreach ($permisos as $permiso) {
            if (!self::tienePermiso($permiso)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Requiere que el usuario tenga un permiso específico
     * Redirige al login o muestra error si no lo tiene
     *
     * @param string $permiso Nombre del permiso requerido
     * @param string $redirectUrl URL de redirección (opcional)
     * @return void
     */
    public static function requierePermiso($permiso, $redirectUrl = null)
    {
        if (!self::tienePermiso($permiso)) {
            self::denegarAcceso($redirectUrl);
        }
    }

    /**
     * Requiere que el usuario tenga alguno de los permisos especificados
     *
     * @param array $permisos Array de nombres de permisos
     * @param string $redirectUrl URL de redirección (opcional)
     * @return void
     */
    public static function requiereAlgunPermiso($permisos, $redirectUrl = null)
    {
        if (!self::tieneAlgunPermiso($permisos)) {
            self::denegarAcceso($redirectUrl);
        }
    }

    /**
     * Carga los permisos del usuario actual desde la base de datos
     *
     * @return void
     */
    private static function cargarPermisosUsuario()
    {
        if (!isset($_SESSION['admin_panel'])) {
            self::$userPermissions = [];
            return;
        }

        $db = Bd::getInstance();
        $idPerfil = $_SESSION['admin_panel']->idperfil;

        $sql = "SELECT p.nombre 
                FROM permisos p 
                INNER JOIN permisos_perfiles pp ON p.id_permiso = pp.id_permiso 
                WHERE pp.id_perfil = ?";

        $permisos = $db->fetchAllSafe($sql, [$idPerfil]);

        self::$userPermissions = [];
        foreach ($permisos as $permiso) {
            self::$userPermissions[] = $permiso->nombre;
        }
    }

    /**
     * Obtiene todos los permisos del usuario actual
     *
     * @return array Array con los nombres de los permisos
     */
    public static function getPermisosUsuario()
    {
        if (self::$userPermissions === null) {
            self::cargarPermisosUsuario();
        }
        return self::$userPermissions;
    }

    /**
     * Obtiene todos los permisos disponibles en el sistema
     *
     * @return array Array de objetos con los permisos
     */
    public static function getTodosLosPermisos()
    {
        if (self::$allPermissions === null) {
            $db = Bd::getInstance();
            self::$allPermissions = $db->fetchAllSafe(
                "SELECT * FROM permisos ORDER BY nombre",
                []
            );
        }
        return self::$allPermissions;
    }

    /**
     * Obtiene todos los perfiles disponibles
     *
     * @return array Array de objetos con los perfiles
     */
    public static function getTodosLosPerfiles()
    {
        if (self::$allProfiles === null) {
            $db = Bd::getInstance();
            self::$allProfiles = $db->fetchAllSafe(
                "SELECT * FROM perfiles ORDER BY id_perfil",
                []
            );
        }
        return self::$allProfiles;
    }

    /**
     * Obtiene los permisos de un perfil específico
     *
     * @param int $idPerfil ID del perfil
     * @return array Array con los nombres de los permisos
     */
    public static function getPermisosPorPerfil($idPerfil)
    {
        $db = Bd::getInstance();

        $sql = "SELECT p.nombre 
                FROM permisos p 
                INNER JOIN permisos_perfiles pp ON p.id_permiso = pp.id_permiso 
                WHERE pp.id_perfil = ?";

        $permisos = $db->fetchAllSafe($sql, [$idPerfil]);

        $resultado = [];
        foreach ($permisos as $permiso) {
            $resultado[] = $permiso->nombre;
        }

        return $resultado;
    }

    /**
     * Asigna un permiso a un perfil
     *
     * @param int $idPerfil ID del perfil
     * @param int $idPermiso ID del permiso
     * @return bool True si se asignó correctamente
     */
    public static function asignarPermisoAPerfil($idPerfil, $idPermiso)
    {
        $db = Bd::getInstance();

        // Verificar que no exista ya la relación
        $existe = $db->fetchValueSafe(
            "SELECT COUNT(*) FROM permisos_perfiles WHERE id_perfil = ? AND id_permiso = ?",
            [$idPerfil, $idPermiso]
        );

        if ($existe > 0) {
            return true; // Ya existe
        }

        return $db->insertSafe('permisos_perfiles', [
            'id_perfil' => $idPerfil,
            'id_permiso' => $idPermiso
        ]);
    }

    /**
     * Remueve un permiso de un perfil
     *
     * @param int $idPerfil ID del perfil
     * @param int $idPermiso ID del permiso
     * @return bool True si se removió correctamente
     */
    public static function removerPermisoDelPerfil($idPerfil, $idPermiso)
    {
        $db = Bd::getInstance();

        return $db->deleteSafe(
            'permisos_perfiles',
            'id_perfil = ? AND id_permiso = ?',
            [$idPerfil, $idPermiso]
        );
    }

    /**
     * Actualiza todos los permisos de un perfil
     *
     * @param int $idPerfil ID del perfil
     * @param array $permisos Array de IDs de permisos
     * @return bool True si se actualizó correctamente
     */
    public static function actualizarPermisosDelPerfil($idPerfil, $permisos)
    {
        $db = Bd::getInstance();

        try {
            // Iniciar transacción
            $db->beginTransaction();

            // Eliminar todos los permisos actuales del perfil
            $db->deleteSafe('permisos_perfiles', 'id_perfil = ?', [$idPerfil]);

            // Insertar los nuevos permisos
            foreach ($permisos as $idPermiso) {
                $db->insertSafe('permisos_perfiles', [
                    'id_perfil' => $idPerfil,
                    'id_permiso' => $idPermiso
                ]);
            }

            // Confirmar transacción
            $db->commit();

            // Limpiar cache
            self::limpiarCache();

            return true;

        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }

    /**
     * Crea un nuevo permiso
     *
     * @param string $nombre Nombre del permiso
     * @param string $descripcion Descripción del permiso
     * @return int|bool ID del permiso creado o false en caso de error
     */
    public static function crearPermiso($nombre, $descripcion)
    {
        $db = Bd::getInstance();

        // Verificar que no exista ya un permiso con ese nombre
        $existe = $db->fetchValueSafe(
            "SELECT COUNT(*) FROM permisos WHERE nombre = ?",
            [$nombre]
        );

        if ($existe > 0) {
            return false;
        }

        $resultado = $db->insertSafe('permisos', [
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ]);

        if ($resultado) {
            self::limpiarCache();
        }

        return $resultado;
    }

    /**
     * Actualiza un permiso existente
     *
     * @param int $idPermiso ID del permiso
     * @param string $nombre Nuevo nombre del permiso
     * @param string $descripcion Nueva descripción del permiso
     * @return bool True si se actualizó correctamente
     */
    public static function actualizarPermiso($idPermiso, $nombre, $descripcion)
    {
        $db = Bd::getInstance();

        $resultado = $db->updateSafe(
            'permisos',
            [
                'nombre' => $nombre,
                'descripcion' => $descripcion
            ],
            'id_permiso = ?',
            [$idPermiso]
        );

        if ($resultado) {
            self::limpiarCache();
        }

        return $resultado;
    }

    /**
     * Elimina un permiso
     *
     * @param int $idPermiso ID del permiso
     * @return bool True si se eliminó correctamente
     */
    public static function eliminarPermiso($idPermiso)
    {
        $db = Bd::getInstance();

        try {
            $db->beginTransaction();

            // Eliminar relaciones con perfiles
            $db->deleteSafe('permisos_perfiles', 'id_permiso = ?', [$idPermiso]);

            // Eliminar el permiso
            $resultado = $db->deleteSafe('permisos', 'id_permiso = ?', [$idPermiso]);

            $db->commit();

            if ($resultado) {
                self::limpiarCache();
            }

            return $resultado;

        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }

    /**
     * Verifica si el usuario puede acceder a una mascota específica
     *
     * @param int $idMascota ID de la mascota
     * @return bool True si puede acceder
     */
    public static function puedeAccederMascota($idMascota)
    {
        if (!isset($_SESSION['admin_panel'])) {
            return false;
        }

        // Los superadmin pueden acceder a todo
        if ($_SESSION['admin_panel']->idperfil == 1) {
            return true;
        }

        // Los cuidadores solo pueden acceder a sus mascotas
        if ($_SESSION['admin_panel']->idperfil == 2) {
            $db = Bd::getInstance();
            $mascota = $db->fetchRowSafe(
                "SELECT id_cuidador FROM mascotas WHERE id = ?",
                [$idMascota]
            );

            return $mascota && $mascota->id_cuidador == $_SESSION['admin_panel']->cuidador_id;
        }

        // Los tutores solo pueden acceder a las mascotas que tienen asignadas
        if ($_SESSION['admin_panel']->idperfil == 3) {
            $db = Bd::getInstance();
            $relacion = $db->fetchRowSafe(
                "SELECT mt.id_mascota 
                 FROM mascotas_tutores mt 
                 INNER JOIN tutores t ON mt.id_tutor = t.id 
                 INNER JOIN usuarios_admin ua ON ua.id_usuario_admin = ? 
                 WHERE mt.id_mascota = ? AND t.id_cuidador = ua.cuidador_id",
                [$_SESSION['admin_panel']->id_usuario_admin, $idMascota]
            );

            return $relacion !== false;
        }

        return false;
    }

    /**
     * Verifica si el usuario puede gestionar un cuidador específico
     *
     * @param int $idCuidador ID del cuidador
     * @return bool True si puede gestionar
     */
    public static function puedeGestionarCuidador($idCuidador)
    {
        if (!isset($_SESSION['admin_panel'])) {
            return false;
        }

        // Los superadmin pueden gestionar todo
        if ($_SESSION['admin_panel']->idperfil == 1) {
            return true;
        }

        // Los cuidadores solo pueden gestionar su propia cuenta
        if ($_SESSION['admin_panel']->idperfil == 2) {
            return $_SESSION['admin_panel']->cuidador_id == $idCuidador;
        }

        // Los tutores no pueden gestionar cuidadores
        return false;
    }

    /**
     * Deniega el acceso y redirige o muestra error
     *
     * @param string $redirectUrl URL de redirección
     * @return void
     */
    private static function denegarAcceso($redirectUrl = null)
    {
        if (class_exists('Tools')) {
            Tools::registerAlert('No tienes permisos para acceder a esta sección.', 'error');
        }

        if ($redirectUrl) {
            header("Location: {$redirectUrl}");
        } else {
            $adminPath = defined('_ADMIN_') ? _ADMIN_ : 'admin/';
            header("Location: " . _DOMINIO_ . $adminPath);
        }
        exit;
    }

    /**
     * Limpia el cache de permisos
     *
     * @return void
     */
    public static function limpiarCache()
    {
        self::$userPermissions = null;
        self::$allPermissions = null;
        self::$allProfiles = null;
    }

    /**
     * Obtiene el nombre del perfil del usuario actual
     *
     * @return string Nombre del perfil
     */
    public static function getNombrePerfilUsuario()
    {
        if (!isset($_SESSION['admin_panel'])) {
            return 'Invitado';
        }

        $perfiles = self::getTodosLosPerfiles();
        foreach ($perfiles as $perfil) {
            if ($perfil->id_perfil == $_SESSION['admin_panel']->idperfil) {
                return $perfil->nombre;
            }
        }

        return 'Desconocido';
    }

    /**
     * Verifica si el usuario es superadmin
     *
     * @return bool True si es superadmin
     */
    public static function esSuperAdmin()
    {
        return isset($_SESSION['admin_panel']) && $_SESSION['admin_panel']->idperfil == 1;
    }

    /**
     * Verifica si el usuario es cuidador
     *
     * @return bool True si es cuidador
     */
    public static function esCuidador()
    {
        return isset($_SESSION['admin_panel']) && $_SESSION['admin_panel']->idperfil == 2;
    }

    /**
     * Verifica si el usuario es tutor
     *
     * @return bool True si es tutor
     */
    public static function esTutor()
    {
        return isset($_SESSION['admin_panel']) && $_SESSION['admin_panel']->idperfil == 3;
    }

    /**
     * Obtiene estadísticas de permisos
     *
     * @return array Array con estadísticas
     */
    public static function getEstadisticas()
    {
        $db = Bd::getInstance();

        return [
            'total_permisos' => $db->fetchValueSafe("SELECT COUNT(*) FROM permisos"),
            'total_perfiles' => $db->fetchValueSafe("SELECT COUNT(*) FROM perfiles"),
            'total_relaciones' => $db->fetchValueSafe("SELECT COUNT(*) FROM permisos_perfiles"),
            'usuarios_por_perfil' => $db->fetchAllSafe(
                "SELECT p.nombre, COUNT(ua.id_usuario_admin) as total 
                 FROM perfiles p 
                 LEFT JOIN usuarios_admin ua ON p.id_perfil = ua.idperfil 
                 GROUP BY p.id_perfil, p.nombre 
                 ORDER BY p.id_perfil"
            )
        ];
    }
}