<?php

/**
 * Clase para gestionar tutores
 *
 * Esta clase maneja todas las operaciones relacionadas con los tutores,
 * incluyendo CRUD, validaciones, búsquedas y relaciones con mascotas.
 */
class Tutores
{
    /**
     * Obtiene todos los tutores con filtros opcionales
     *
     * @param int $comienzo Inicio de la paginación
     * @param int $limite Límite de resultados
     * @param bool $applyLimit Aplicar límite o no
     * @param string $busqueda Término de búsqueda
     * @return array
     */
    public static function getTutoresFiltered($comienzo = 0, $limite = 20, $busqueda = '', $applyLimit = true)
    {
        $db = Bd::getInstance();
        $params = [];
        $whereConditions = ["1"];
        $joins = '';

        // Filtro de búsqueda
        if (!empty($busqueda)) {
            $whereConditions[] = "(t.nombre LIKE ? OR t.telefono_1 LIKE ? OR t.telefono_2 LIKE ? OR t.email LIKE ?)";
            $searchTerm = "%{$busqueda}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Filtro por cuidador según permisos del usuario logueado
        if (isset($_SESSION['admin_panel'])) {
            if ($_SESSION['admin_panel']->idperfil == 2) { // Cuidador
                $whereConditions[] = "t.id_cuidador = ?";
                $params[] = $_SESSION['admin_panel']->cuidador_id;
            } elseif ($_SESSION['admin_panel']->idperfil == 3) { // Tutor
                $whereConditions[] = "t.id = ?";
                $params[] = self::getTutorIdByUserId($_SESSION['admin_panel']->id_usuario_admin);
            }
        }

        // Joins para obtener información del cuidador
        $joins = "LEFT JOIN cuidadores c ON t.id_cuidador = c.id";

        $whereClause = implode(' AND ', $whereConditions);

        $selectFields = "t.*, c.nombre as cuidador_nombre, c.slug as cuidador_slug";
        $sql = "FROM tutores t {$joins} WHERE {$whereClause}";

        // Contar total de registros
        $total = $db->fetchValueSafe("SELECT COUNT(t.id) {$sql}", $params);

        // Aplicar límite y orden si es necesario
        if ($applyLimit) {
            $sql .= " ORDER BY t.nombre ASC LIMIT ?, ?";
            $params[] = (int)$comienzo;
            $params[] = (int)$limite;
        } else {
            $sql .= " ORDER BY t.nombre ASC";
        }

        $listado = $db->fetchAllSafe("SELECT {$selectFields} {$sql}", $params);

        return [
            'listado' => $listado,
            'total' => $total
        ];
    }

    /**
     * Obtiene un tutor por su ID
     *
     * @param int $id ID del tutor
     * @return object|false
     */
    public static function getTutorById($id)
    {
        $db = Bd::getInstance();

        $sql = "SELECT t.*, c.nombre as cuidador_nombre, c.slug as cuidador_slug 
                FROM tutores t 
                LEFT JOIN cuidadores c ON t.id_cuidador = c.id 
                WHERE t.id = ?";

        return $db->fetchRowSafe($sql, [(int)$id]);
    }

    /**
     * Obtiene un tutor por su slug
     *
     * @param string $slug Slug del tutor
     * @return object|false
     */
    public static function getTutorBySlug($slug)
    {
        $db = Bd::getInstance();

        $sql = "SELECT t.*, c.nombre as cuidador_nombre, c.slug as cuidador_slug 
                FROM tutores t 
                LEFT JOIN cuidadores c ON t.id_cuidador = c.id 
                WHERE t.slug = ?";

        return $db->fetchRowSafe($sql, [$slug]);
    }

    /**
     * Obtiene el ID del tutor asociado a un usuario
     *
     * @param int $userId ID del usuario
     * @return int|null
     */
    public static function getTutorIdByUserId($userId)
    {
        $db = Bd::getInstance();

        $sql = "SELECT t.id 
                FROM tutores t 
                INNER JOIN usuarios_cuidadores uc ON t.id_cuidador = uc.id_cuidador 
                WHERE uc.id_usuario = ?";

        return $db->fetchValueSafe($sql, [(int)$userId]);
    }

    /**
     * Crea un nuevo tutor
     *
     * @param array $datos Datos del tutor (opcional, si no se pasa usa $_POST)
     * @return array Resultado de la operación
     */
    public static function crearTutor($datos = null)
    {
        $db = Bd::getInstance();

        $result = [
            'success' => false,
            'errors' => [],
            'data' => null
        ];

        // Si no se pasan datos, obtenerlos de $_POST
        if ($datos === null) {
            $datos = [
                'id_cuidador' => (int)Tools::getValue('id_cuidador'),
                'nombre' => Tools::getValue('nombre'),
                'telefono_1' => Tools::getValue('telefono_1'),
                'telefono_2' => Tools::getValue('telefono_2'),
                'email' => Tools::getValue('email'),
                'notas' => Tools::getValue('notas')
            ];
        }

        // Validaciones
        $validationResult = self::validateTutorData($datos);
        if (!$validationResult['valid']) {
            $result['errors'] = $validationResult['errors'];
            return $result;
        }

        // Verificar permisos
        if (!self::canManageTutor($datos['id_cuidador'])) {
            $result['errors'][] = 'No tienes permisos para crear tutores en este cuidador';
            return $result;
        }

        // Generar slug único
        $slug = self::generateUniqueSlug($datos['nombre']);

        // Preparar datos para inserción
        $tutorData = [
            'id_cuidador' => $datos['id_cuidador'],
            'slug' => $slug,
            'nombre' => Tools::sanitizeInput($datos['nombre']),
            'telefono_1' => Tools::sanitizeInput($datos['telefono_1']),
            'telefono_2' => Tools::sanitizeInput($datos['telefono_2']),
            'email' => strtolower(Tools::sanitizeInput($datos['email'])),
            'notas' => Tools::sanitizeInput($datos['notas'])
        ];

        try {
            $tutorId = $db->insertSafe('tutores', $tutorData);

            if ($tutorId) {
                $result['success'] = true;
                $result['data'] = [
                    'id' => $tutorId,
                    'slug' => $slug,
                    'nombre' => $tutorData['nombre']
                ];

                // Log de auditoría
                debug_log([
                    'action' => 'tutor_created',
                    'tutor_id' => $tutorId,
                    'tutor_name' => $tutorData['nombre'],
                    'cuidador_id' => $datos['id_cuidador'],
                    'user_id' => $_SESSION['admin_panel']->id_usuario_admin ?? 0
                ], 'TUTOR_CREATED', 'tutores');

            } else {
                $result['errors'][] = 'Error al crear el tutor en la base de datos';
            }

        } catch (Exception $e) {
            $result['errors'][] = 'Error interno: ' . $e->getMessage();
            debug_log([
                'error' => 'Exception creating tutor',
                'message' => $e->getMessage(),
                'data' => $datos
            ], 'TUTOR_CREATE_ERROR', 'tutores');
        }

        return $result;
    }

    /**
     * Actualiza un tutor existente
     *
     * @param int $id ID del tutor
     * @param array $datos Datos del tutor (opcional, si no se pasa usa $_POST)
     * @return array Resultado de la operación
     */
    public static function actualizarTutor($id = null, $datos = null)
    {
        $db = Bd::getInstance();

        $result = [
            'success' => false,
            'errors' => [],
            'data' => null
        ];

        // Si no se pasa ID, obtenerlo de $_POST
        if ($id === null) {
            $id = (int)Tools::getValue('id');
        }

        // Validar que el tutor existe
        $tutorExistente = self::getTutorById($id);
        if (!$tutorExistente) {
            $result['errors'][] = 'El tutor no existe';
            return $result;
        }

        // Verificar permisos
        if (!self::canManageTutor($tutorExistente->id_cuidador)) {
            $result['errors'][] = 'No tienes permisos para editar este tutor';
            return $result;
        }

        // Si no se pasan datos, obtenerlos de $_POST
        if ($datos === null) {
            $datos = [
                'id_cuidador' => $tutorExistente->id_cuidador, // No permitir cambiar cuidador
                'nombre' => Tools::getValue('nombre'),
                'telefono_1' => Tools::getValue('telefono_1'),
                'telefono_2' => Tools::getValue('telefono_2'),
                'email' => Tools::getValue('email'),
                'notas' => Tools::getValue('notas')
            ];
        }

        // Validaciones
        $validationResult = self::validateTutorData($datos, $id);
        if (!$validationResult['valid']) {
            $result['errors'] = $validationResult['errors'];
            return $result;
        }

        // Preparar datos para actualización
        $tutorData = [
            'nombre' => Tools::sanitizeInput($datos['nombre']),
            'telefono_1' => Tools::sanitizeInput($datos['telefono_1']),
            'telefono_2' => Tools::sanitizeInput($datos['telefono_2']),
            'email' => strtolower(Tools::sanitizeInput($datos['email'])),
            'notas' => Tools::sanitizeInput($datos['notas'])
        ];

        // Actualizar slug si cambió el nombre
        if ($tutorExistente->nombre !== $tutorData['nombre']) {
            $tutorData['slug'] = self::generateUniqueSlug($tutorData['nombre'], $id);
        }

        try {
            $updateResult = $db->updateSafe(
                'tutores',
                $tutorData,
                'id = ?',
                [$id]
            );

            if ($updateResult) {
                $result['success'] = true;
                $result['data'] = [
                    'id' => $id,
                    'nombre' => $tutorData['nombre']
                ];

                // Log de auditoría
                debug_log([
                    'action' => 'tutor_updated',
                    'tutor_id' => $id,
                    'tutor_name' => $tutorData['nombre'],
                    'user_id' => $_SESSION['admin_panel']->id_usuario_admin ?? 0
                ], 'TUTOR_UPDATED', 'tutores');

            } else {
                $result['errors'][] = 'Error al actualizar el tutor en la base de datos';
            }

        } catch (Exception $e) {
            $result['errors'][] = 'Error interno: ' . $e->getMessage();
            debug_log([
                'error' => 'Exception updating tutor',
                'message' => $e->getMessage(),
                'tutor_id' => $id,
                'data' => $datos
            ], 'TUTOR_UPDATE_ERROR', 'tutores');
        }

        return $result;
    }

    /**
     * Elimina un tutor
     *
     * @param int $id ID del tutor
     * @return array Resultado de la operación
     */
    public static function eliminarTutor($id)
    {
        $db = Bd::getInstance();

        $result = [
            'success' => false,
            'errors' => [],
            'data' => null
        ];

        // Validar que el tutor existe
        $tutor = self::getTutorById($id);
        if (!$tutor) {
            $result['errors'][] = 'El tutor no existe';
            return $result;
        }

        // Verificar permisos
        if (!self::canManageTutor($tutor->id_cuidador)) {
            $result['errors'][] = 'No tienes permisos para eliminar este tutor';
            return $result;
        }

        // Verificar si tiene mascotas asignadas
        $mascotasAsignadas = self::getMascotasAsignadas($id);
        if ($mascotasAsignadas > 0) {
            $result['errors'][] = "No se puede eliminar el tutor porque tiene {$mascotasAsignadas} mascota(s) asignada(s)";
            return $result;
        }

        try {
            $deleteResult = $db->deleteSafe('tutores', 'id = ?', [$id]);

            if ($deleteResult) {
                $result['success'] = true;
                $result['data'] = ['id' => $id];

                // Log de auditoría
                debug_log([
                    'action' => 'tutor_deleted',
                    'tutor_id' => $id,
                    'tutor_name' => $tutor->nombre,
                    'user_id' => $_SESSION['admin_panel']->id_usuario_admin ?? 0
                ], 'TUTOR_DELETED', 'tutores');

            } else {
                $result['errors'][] = 'Error al eliminar el tutor de la base de datos';
            }

        } catch (Exception $e) {
            $result['errors'][] = 'Error interno: ' . $e->getMessage();
            debug_log([
                'error' => 'Exception deleting tutor',
                'message' => $e->getMessage(),
                'tutor_id' => $id
            ], 'TUTOR_DELETE_ERROR', 'tutores');
        }

        return $result;
    }

    /**
     * Valida los datos de un tutor
     *
     * @param array $datos Datos a validar
     * @param int $id ID del tutor (para actualizaciones)
     * @return array
     */
    protected static function validateTutorData($datos, $id = null)
    {
        $result = [
            'valid' => true,
            'errors' => []
        ];

        // Validar nombre
        if (empty($datos['nombre'])) {
            $result['errors'][] = 'El nombre es obligatorio';
        } else {
            $nombreValidation = Tools::validateNombre($datos['nombre']);
            if (!$nombreValidation['valid']) {
                $result['errors'] = array_merge($result['errors'], $nombreValidation['errors']);
            }
        }

        // Validar email si se proporciona
        if (!empty($datos['email'])) {
            $emailValidation = Tools::validateEmail($datos['email'], $id, 'tutores', 'email');
            if (!$emailValidation['valid']) {
                $result['errors'] = array_merge($result['errors'], $emailValidation['errors']);
            }
        }

        // Validar teléfonos si se proporcionan
        if (empty($datos['telefono_1'])) {
            $result['errors'][] = 'El teléfono 1 es obligatorio';
        } else {
            $phoneValidation = Tools::validatePhone($datos['telefono_1']);
            if (!$phoneValidation['valid']) {
                $result['errors'][] = 'El teléfono principal no tiene un formato válido';
            }
        }

        if (!empty($datos['telefono_2'])) {
            $phoneValidation = Tools::validatePhone($datos['telefono_2']);
            if (!$phoneValidation['valid']) {
                $result['errors'][] = 'El teléfono secundario no tiene un formato válido';
            }
        }

        $result['valid'] = empty($result['errors']);
        return $result;
    }

    /**
     * Genera un slug único para el tutor
     *
     * @param string $nombre Nombre del tutor
     * @param int $excludeId ID a excluir (para actualizaciones)
     * @return string
     */
    protected static function generateUniqueSlug($nombre, $excludeId = null)
    {
        $db = Bd::getInstance();
        $baseSlug = Tools::urlAmigable($nombre);
        $slug = $baseSlug;
        $counter = 1;

        while (self::slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Verifica si un slug ya existe
     *
     * @param string $slug Slug a verificar
     * @param int $excludeId ID a excluir
     * @return bool
     */
    protected static function slugExists($slug, $excludeId = null)
    {
        $db = Bd::getInstance();
        $params = [$slug];
        $sql = "SELECT COUNT(*) FROM tutores WHERE slug = ?";

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = (int)$excludeId;
        }

        return (int)$db->fetchValueSafe($sql, $params) > 0;
    }

    /**
     * Verifica si el usuario actual puede gestionar un tutor del cuidador especificado
     *
     * @param int $cuidadorId ID del cuidador
     * @return bool
     */
    public static function canManageTutor($cuidadorId)
    {
        if (!isset($_SESSION['admin_panel'])) {
            return false;
        }

        // Superadmin puede gestionar cualquier tutor
        if ($_SESSION['admin_panel']->idperfil == 1) {
            return true;
        }

        // Cuidadores solo pueden gestionar tutores de su cuidador
        if ($_SESSION['admin_panel']->idperfil == 2) {
            return $_SESSION['admin_panel']->cuidador_id == $cuidadorId;
        }

        // Tutores no pueden gestionar otros tutores
        return false;
    }

    /**
     * Obtiene el número de mascotas asignadas a un tutor
     *
     * @param int $tutorId ID del tutor
     * @return int
     */
    public static function getMascotasAsignadas($tutorId)
    {
        $db = Bd::getInstance();
        return (int)$db->fetchValueSafe(
            "SELECT COUNT(*) FROM mascotas_tutores WHERE id_tutor = ?",
            [(int)$tutorId]
        );
    }

    /**
     * Obtiene los tutores de una mascota
     *
     * @param int $mascotaid ID de la mascota
     * @return array
     */
    public static function getTutoresByMascota($mascotaid)
    {
        $db = Bd::getInstance();

        $sql = "SELECT t.*, mt.id_mascota 
                FROM tutores t 
                INNER JOIN mascotas_tutores mt ON t.id = mt.id_tutor 
                WHERE mt.id_mascota = ? 
                ORDER BY t.nombre";

        return $db->fetchAllSafe($sql, [(int)$mascotaid]);
    }

    /**
     * Obtiene las mascotas asignadas a un tutor
     *
     * @param int $tutorId ID del tutor
     * @return array
     */
    public static function getMascotasByTutor($tutorId)
    {
        $db = Bd::getInstance();

        $sql = "SELECT m.*, mt.id_tutor 
                FROM mascotas m 
                INNER JOIN mascotas_tutores mt ON m.id = mt.id_mascota 
                WHERE mt.id_tutor = ? 
                ORDER BY m.nombre";

        return $db->fetchAllSafe($sql, [(int)$tutorId]);
    }

    /**
     * Asigna una mascota a un tutor
     *
     * @param int $mascotaId ID de la mascota
     * @param int $tutorId ID del tutor
     * @return bool
     */
    public static function asignarMascota($mascotaId, $tutorId)
    {
        $db = Bd::getInstance();

        // Verificar que no esté ya asignada
        $exists = $db->fetchValueSafe(
            "SELECT COUNT(*) FROM mascotas_tutores WHERE id_mascota = ? AND id_tutor = ?",
            [(int)$mascotaId, (int)$tutorId]
        );

        if ($exists > 0) {
            return false; // Ya está asignada
        }

        return $db->insertSafe('mascotas_tutores', [
                'id_mascota' => (int)$mascotaId,
                'id_tutor' => (int)$tutorId
            ]) !== false;
    }

    /**
     * Desasigna una mascota de un tutor
     *
     * @param int $mascotaId ID de la mascota
     * @param int $tutorId ID del tutor
     * @return bool
     */
    public static function desasignarMascota($mascotaId, $tutorId)
    {
        $db = Bd::getInstance();

        return $db->deleteSafe(
                'mascotas_tutores',
                'id_mascota = ? AND id_tutor = ?',
                [(int)$mascotaId, (int)$tutorId]
            ) > 0;
    }

    /**
     * Busca tutores por nombre
     *
     * @param string $query Término de búsqueda
     * @param int $limite Límite de resultados
     * @return array
     */
    public static function searchByName($query, $limite = 10)
    {
        $db = Bd::getInstance();
        $params = ["%{$query}%"];
        $whereConditions = ["t.nombre LIKE ?"];

        // Filtro por cuidador según permisos
        if (isset($_SESSION['admin_panel']) && $_SESSION['admin_panel']->idperfil == 2) {
            $whereConditions[] = "t.id_cuidador = ?";
            $params[] = $_SESSION['admin_panel']->cuidador_id;
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "SELECT t.*, c.nombre as cuidador_nombre 
                FROM tutores t 
                LEFT JOIN cuidadores c ON t.id_cuidador = c.id 
                WHERE {$whereClause} 
                ORDER BY t.nombre 
                LIMIT ?";

        $params[] = (int)$limite;

        return $db->fetchAllSafe($sql, $params);
    }

    /**
     * Obtiene tutores por cuidador
     *
     * @param int $cuidadorId ID del cuidador
     * @return array
     */
    public static function getTutoresByCuidador($cuidadorId)
    {
        $db = Bd::getInstance();

        $sql = "SELECT * FROM tutores WHERE id_cuidador = ? ORDER BY nombre";

        return $db->fetchAllSafe($sql, [(int)$cuidadorId]);
    }

    /**
     * Obtiene el total de tutores
     *
     * @return int
     */
    public static function getTotalTutores()
    {
        $db = Bd::getInstance();
        $params = [];
        $whereConditions = ["1"];

        // Filtro por cuidador según permisos
        if (isset($_SESSION['admin_panel']) && $_SESSION['admin_panel']->idperfil == 2) {
            $whereConditions[] = "id_cuidador = ?";
            $params[] = $_SESSION['admin_panel']->cuidador_id;
        }

        $whereClause = implode(' AND ', $whereConditions);

        return (int)$db->fetchValueSafe(
            "SELECT COUNT(*) FROM tutores WHERE {$whereClause}",
            $params
        );
    }

    /**
     * Obtiene estadísticas de tutores
     *
     * @return array
     */
    public static function getEstadisticas()
    {
        $db = Bd::getInstance();
        $params = [];
        $whereConditions = ["1"];

        // Filtro por cuidador según permisos
        if (isset($_SESSION['admin_panel']) && $_SESSION['admin_panel']->idperfil == 2) {
            $whereConditions[] = "t.id_cuidador = ?";
            $params[] = $_SESSION['admin_panel']->cuidador_id;
        }

        $whereClause = implode(' AND ', $whereConditions);

        return [
            'total' => self::getTotalTutores(),
            'con_mascotas' => (int)$db->fetchValueSafe(
                "SELECT COUNT(DISTINCT t.id) 
                 FROM tutores t 
                 INNER JOIN mascotas_tutores mt ON t.id = mt.id_tutor 
                 WHERE {$whereClause}",
                $params
            ),
            'sin_mascotas' => (int)$db->fetchValueSafe(
                "SELECT COUNT(t.id) 
                 FROM tutores t 
                 LEFT JOIN mascotas_tutores mt ON t.id = mt.id_tutor 
                 WHERE mt.id_tutor IS NULL AND {$whereClause}",
                $params
            )
        ];
    }

    /**
     * Importa un tutor desde datos externos
     *
     * @param array $data Datos del tutor
     * @return bool
     */
    public static function importarTutor($data)
    {
        // Mapear campos del CSV/Excel a campos de la base de datos
        $tutorData = [
            'id_cuidador' => $data['id_cuidador'] ?? 1,
            'nombre' => $data['nombre'] ?? '',
            'telefono_1' => $data['telefono_1'] ?? '',
            'telefono_2' => $data['telefono_2'] ?? '',
            'email' => $data['email'] ?? '',
            'notas' => $data['notas'] ?? ''
        ];

        $result = self::crearTutor($tutorData);
        return $result['success'];
    }

    /**
     * Elimina un registro (alias para compatibilidad)
     *
     * @param int $id ID del tutor
     * @return bool
     */
    public static function eliminarRegistro($id)
    {
        $result = self::eliminarTutor($id);
        return $result['success'];
    }
}
