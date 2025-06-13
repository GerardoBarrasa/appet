<?php

/**
 * Controlador AJAX para el panel de administración
 *
 * Maneja todas las peticiones AJAX del panel de administración,
 * incluyendo operaciones CRUD, filtros, búsquedas y acciones específicas.
 */
class AdminajaxController extends Controllers
{
    /**
     * Variables de paginación
     */
    var $comienzo = 0;
    var $limite = 10;
    var $pagina = 1;

    /**
     * Configuración del controlador
     */
    protected $config = [
        'csrf_protection' => true,
        'rate_limit' => [
            'enabled' => true,
            'max_requests' => 60,
            'time_window' => 60 // segundos
        ],
        'allowed_actions' => [
            // Usuarios admin
            'ajax-get-usuarios-admin',
            'ajax-create-usuario-admin',
            'ajax-update-usuario-admin',
            'ajax-delete-usuario-admin',
            'ajax-toggle-usuario-status',

            // Idiomas
            'ajax-get-idiomas-admin',
            'ajax-create-idioma',
            'ajax-update-idioma',
            'ajax-delete-idioma',

            // Slugs
            'ajax-get-slugs-filtered',
            'ajax-create-slug',
            'ajax-update-slug',
            'ajax-delete-slug',

            // Traducciones
            'ajax-get-traductions-filtered',
            'ajax-create-traduccion',
            'ajax-update-traduccion',
            'ajax-delete-traduccion',
            'ajax-regenerar-cache-traducciones',

            // Mascotas
            'ajax-get-mascotas-admin',
            'ajax-create-mascota',
            'ajax-update-mascota',
            'ajax-delete-mascota',
            'ajax-save-mascota-evaluation',
            'ajax-get-mascota-details',

            // Cuidadores
            'ajax-get-cuidadores-admin',
            'ajax-create-cuidador',
            'ajax-update-cuidador',
            'ajax-delete-cuidador',

            // Características
            'ajax-get-caracteristicas-admin',
            'ajax-create-caracteristica',
            'ajax-update-caracteristica',
            'ajax-delete-caracteristica',

            // Utilidades
            'ajax-eliminar-registro',
            'ajax-contenido-modal',
            'ajax-upload-file',
            'ajax-search-global',
            'ajax-get-stats',
            'ajax-export-data',
            'ajax-import-data'
        ]
    ];

    /**
     * Ejecuta el controlador AJAX
     *
     * @param string $page Acción AJAX solicitada
     * @return void
     */
    public function execute($page)
    {
        // Configurar layout
        Render::$layout = false;

        // Verificar autenticación
        $this->requireAuthentication();

        // Obtener y validar entorno
        Admin::getEntorno();
        Admin::validateUser();

        // Verificar rate limiting
        $this->checkRateLimit();

        // Validar acción
        $this->validateAction($page);

        // Configurar headers de respuesta
        $this->setupResponseHeaders();

        // Definir rutas AJAX
        $this->defineAjaxRoutes();

        // Si no se encontró la ruta, devolver error 404
        if (!$this->getRendered()) {
            $this->sendError('Acción no encontrada', 404);
        }
    }

    /**
     * Verifica que el usuario esté autenticado
     *
     * @return void
     */
    protected function requireAuthentication()
    {
        if (!isset($_SESSION['admin_panel'])) {
            $this->sendError('No autenticado', 401);
        }
    }

    /**
     * Verifica el rate limiting para peticiones AJAX
     *
     * @return void
     */
    protected function checkRateLimit()
    {
        if (!$this->config['rate_limit']['enabled']) {
            return;
        }

        $ip = Tools::getClientIP();
        $userId = $_SESSION['admin_panel']->id_usuario_admin ?? 0;
        $key = 'ajax_rate_limit_' . md5($ip . '_' . $userId);

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'last_request' => time()
            ];
        } else {
            $timeDiff = time() - $_SESSION[$key]['last_request'];

            if ($timeDiff > $this->config['rate_limit']['time_window']) {
                $_SESSION[$key] = [
                    'count' => 1,
                    'last_request' => time()
                ];
            } else {
                $_SESSION[$key]['count']++;
                $_SESSION[$key]['last_request'] = time();

                if ($_SESSION[$key]['count'] > $this->config['rate_limit']['max_requests']) {
                    $this->sendError('Demasiadas peticiones', 429);
                }
            }
        }
    }

    /**
     * Valida que la acción esté permitida
     *
     * @param string $action Acción a validar
     * @return void
     */
    protected function validateAction($action)
    {
        if (!in_array($action, $this->config['allowed_actions'])) {
            $this->log("Acción no permitida intentada: {$action}", 'warning');
            $this->sendError('Acción no permitida', 403);
        }
    }

    /**
     * Configura headers de respuesta
     *
     * @return void
     */
    protected function setupResponseHeaders()
    {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }

    /**
     * Define todas las rutas AJAX
     *
     * @return void
     */
    protected function defineAjaxRoutes()
    {
        // ==========================================
        // USUARIOS ADMIN
        // ==========================================

        $this->add('ajax-get-usuarios-admin', [$this, 'getUsuariosAdmin']);
        $this->add('ajax-create-usuario-admin', [$this, 'createUsuarioAdmin']);
        $this->add('ajax-update-usuario-admin', [$this, 'updateUsuarioAdmin']);
        $this->add('ajax-delete-usuario-admin', [$this, 'deleteUsuarioAdmin']);
        $this->add('ajax-toggle-usuario-status', [$this, 'toggleUsuarioStatus']);

        // ==========================================
        // IDIOMAS
        // ==========================================

        $this->add('ajax-get-idiomas-admin', [$this, 'getIdiomasAdmin']);
        $this->add('ajax-create-idioma', [$this, 'createIdioma']);
        $this->add('ajax-update-idioma', [$this, 'updateIdioma']);
        $this->add('ajax-delete-idioma', [$this, 'deleteIdioma']);

        // ==========================================
        // SLUGS
        // ==========================================

        $this->add('ajax-get-slugs-filtered', [$this, 'getSlugsFiltered']);
        $this->add('ajax-create-slug', [$this, 'createSlug']);
        $this->add('ajax-update-slug', [$this, 'updateSlug']);
        $this->add('ajax-delete-slug', [$this, 'deleteSlug']);

        // ==========================================
        // MASCOTAS
        // ==========================================

        $this->add('ajax-get-mascotas-admin', [$this, 'getMascotasAdmin']);
        $this->add('ajax-create-mascota', [$this, 'createMascota']);
        $this->add('ajax-update-mascota', [$this, 'updateMascota']);
        $this->add('ajax-delete-mascota', [$this, 'deleteMascota']);
        $this->add('ajax-save-mascota-evaluation', [$this, 'saveMascotaEvaluation']);
        $this->add('ajax-get-mascota-details', [$this, 'getMascotaDetails']);

        // ==========================================
        // CUIDADORES
        // ==========================================

        $this->add('ajax-get-cuidadores-admin', [$this, 'getCuidadoresAdmin']);
        $this->add('ajax-create-cuidador', [$this, 'createCuidador']);
        $this->add('ajax-update-cuidador', [$this, 'updateCuidador']);
        $this->add('ajax-delete-cuidador', [$this, 'deleteCuidador']);

        // ==========================================
        // CARACTERÍSTICAS
        // ==========================================

        $this->add('ajax-get-caracteristicas-admin', [$this, 'getCaracteristicasAdmin']);
        $this->add('ajax-create-caracteristica', [$this, 'createCaracteristica']);
        $this->add('ajax-update-caracteristica', [$this, 'updateCaracteristica']);
        $this->add('ajax-delete-caracteristica', [$this, 'deleteCaracteristica']);

        // ==========================================
        // UTILIDADES GENERALES
        // ==========================================

        $this->add('ajax-eliminar-registro', [$this, 'eliminarRegistro']);
        $this->add('ajax-contenido-modal', [$this, 'getContenidoModal']);
        $this->add('ajax-upload-file', [$this, 'uploadFile']);
        $this->add('ajax-search-global', [$this, 'searchGlobal']);
        $this->add('ajax-get-stats', [$this, 'getStats']);
        $this->add('ajax-export-data', [$this, 'exportData']);
        $this->add('ajax-import-data', [$this, 'importData']);
    }

    // ==========================================
    // MÉTODOS PARA USUARIOS ADMIN
    // ==========================================

    /**
     * Obtiene usuarios admin con filtros
     *
     * @return void
     */
    public function getUsuariosAdmin()
    {
        try {
            $comienzo = (int)Tools::getValue('comienzo', 0);
            $limite = (int)Tools::getValue('limite', 10);
            $pagina = (int)Tools::getValue('pagina', 1);

            $usuarios = Admin::getUsuariosWithFiltros($comienzo, $limite, true);

            $data = [
                'comienzo' => $comienzo,
                'limite' => $limite,
                'pagina' => $pagina,
                'usuarios' => $usuarios['listado'],
                'total' => $usuarios['total']
            ];

            $html = Render::getAjaxPage('admin_usuarios_admin', $data);

            if (!empty($html)) {
                $this->sendSuccess(['html' => $html]);
            } else {
                $this->sendError('Error cargando el contenido');
            }
        } catch (Exception $e) {
            $this->log("Error en getUsuariosAdmin: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Crea un nuevo usuario admin
     *
     * @return void
     */
    public function createUsuarioAdmin()
    {
        try {
            $this->validateRequiredFields(['nombre', 'email', 'password']);

            $email = Tools::getValue('email');

            // Verificar que el email no exista
            if (Admin::emailExiste($email)) {
                $this->sendError('El email ya está en uso');
                return;
            }

            $id = Admin::crearUsuario();

            if ($id) {
                $this->log("Usuario admin creado: {$email}", 'info');
                $this->sendSuccess([
                    'message' => 'Usuario creado correctamente',
                    'id' => $id
                ]);
            } else {
                $this->sendError('Error al crear el usuario');
            }
        } catch (Exception $e) {
            $this->log("Error en createUsuarioAdmin: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Actualiza un usuario admin
     *
     * @return void
     */
    public function updateUsuarioAdmin()
    {
        try {
            $this->validateRequiredFields(['id_usuario_admin', 'nombre', 'email']);

            $id = (int)Tools::getValue('id_usuario_admin');
            $email = Tools::getValue('email');

            // Verificar que el email no exista para otro usuario
            if (Admin::emailExiste($email, $id)) {
                $this->sendError('El email ya está en uso por otro usuario');
                return;
            }

            $result = Admin::actualizarUsuario();

            if ($result) {
                $this->log("Usuario admin actualizado: {$email}", 'info');
                $this->sendSuccess(['message' => 'Usuario actualizado correctamente']);
            } else {
                $this->sendError('Error al actualizar el usuario');
            }
        } catch (Exception $e) {
            $this->log("Error en updateUsuarioAdmin: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Elimina un usuario admin
     *
     * @return void
     */
    public function deleteUsuarioAdmin()
    {
        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de usuario no válido');
                return;
            }

            // No permitir eliminar el propio usuario
            if ($id == $_SESSION['admin_panel']->id_usuario_admin) {
                $this->sendError('No puedes eliminar tu propio usuario');
                return;
            }

            $result = Admin::eliminarRegistro($id);

            if ($result) {
                $this->log("Usuario admin eliminado: ID {$id}", 'info');
                $this->sendSuccess(['message' => 'Usuario eliminado correctamente']);
            } else {
                $this->sendError('Error al eliminar el usuario');
            }
        } catch (Exception $e) {
            $this->log("Error en deleteUsuarioAdmin: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Cambia el estado de un usuario admin
     *
     * @return void
     */
    public function toggleUsuarioStatus()
    {
        try {
            $id = (int)Tools::getValue('id');
            $estado = (int)Tools::getValue('estado');

            if (!$id) {
                $this->sendError('ID de usuario no válido');
                return;
            }

            // No permitir desactivar el propio usuario
            if ($id == $_SESSION['admin_panel']->id_usuario_admin && $estado == 0) {
                $this->sendError('No puedes desactivar tu propio usuario');
                return;
            }

            $db = Bd::getInstance();
            $result = $db->updateSafe(
                'usuarios_admin',
                ['estado' => $estado],
                'id_usuario_admin = ?',
                [$id]
            );

            if ($result) {
                $accion = $estado ? 'activado' : 'desactivado';
                $this->log("Usuario admin {$accion}: ID {$id}", 'info');
                $this->sendSuccess(['message' => "Usuario {$accion} correctamente"]);
            } else {
                $this->sendError('Error al cambiar el estado del usuario');
            }
        } catch (Exception $e) {
            $this->log("Error en toggleUsuarioStatus: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    // ==========================================
    // MÉTODOS PARA IDIOMAS
    // ==========================================

    /**
     * Obtiene idiomas con filtros
     *
     * @return void
     */
    public function getIdiomasAdmin()
    {
        try {
            $comienzo = (int)Tools::getValue('comienzo', 0);
            $limite = (int)Tools::getValue('limite', 10);
            $pagina = (int)Tools::getValue('pagina', 1);

            $idiomas = Idiomas::getIdiomasWithFiltros($comienzo, $limite, true);
            $total = count($idiomas);

            $data = [
                'comienzo' => $comienzo,
                'limite' => $limite,
                'pagina' => $pagina,
                'idiomas' => $idiomas,
                'total' => $total
            ];

            $html = Render::getAjaxPage('admin_idiomas', $data);

            if (!empty($html)) {
                $this->sendSuccess(['html' => $html]);
            } else {
                $this->sendError('Error cargando el contenido');
            }
        } catch (Exception $e) {
            $this->log("Error en getIdiomasAdmin: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    // ==========================================
    // MÉTODOS PARA SLUGS
    // ==========================================

    /**
     * Obtiene slugs filtrados
     *
     * @return void
     */
    public function getSlugsFiltered()
    {
        try {
            $comienzo = (int)Tools::getValue('comienzo', 0);
            $limite = (int)Tools::getValue('limite', 10);
            $pagina = (int)Tools::getValue('pagina', 1);

            $datos = Slugs::getSlugsFiltered($comienzo, $limite);

            $data = [
                'datos' => $datos,
                'comienzo' => $comienzo,
                'limite' => $limite,
                'pagina' => $pagina,
                'languages' => Idiomas::getLanguages()
            ];

            $html = Render::getAjaxPage('admin_slugs_admin', $data);

            if (!empty($html)) {
                $this->sendSuccess(['html' => $html]);
            } else {
                $this->sendError('Error cargando el contenido');
            }
        } catch (Exception $e) {
            $this->log("Error en getSlugsFiltered: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }



    // ==========================================
    // MÉTODOS PARA MASCOTAS
    // ==========================================

    /**
     * Obtiene mascotas con filtros
     *
     * @return void
     */
    public function getMascotasAdmin()
    {
        try {
            $comienzo = (int)Tools::getValue('comienzo', 0);
            $limite = (int)Tools::getValue('limite', 10);
            $pagina = (int)Tools::getValue('pagina', 1);

            $mascotas = Mascotas::getMascotasFiltered($comienzo, $limite);
            $total = count(Mascotas::getMascotasFiltered($comienzo, $limite, false));

            $data = [
                'comienzo' => $comienzo,
                'limite' => $limite,
                'pagina' => $pagina,
                'mascotas' => $mascotas,
                'total' => $total
            ];

            $html = Render::getAjaxPage('admin_mascotas_list', $data);

            if (!empty($html)) {
                $this->sendSuccess(['html' => $html]);
            } else {
                $this->sendError('Error cargando el contenido');
            }
        } catch (Exception $e) {
            $this->log("Error en getMascotasAdmin: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Guarda evaluación de mascota
     *
     * @return void
     */
    public function saveMascotaEvaluation()
    {
        try {
            $idMascota = (int)Tools::getValue('idmascota');

            if (!$idMascota) {
                $this->sendError('ID de mascota no válido');
                return;
            }

            // Verificar que la mascota existe y pertenece al cuidador correcto
            $mascota = Mascotas::getMascotaById($idMascota);
            if (!$mascota) {
                $this->sendError('Mascota no encontrada');
                return;
            }

            // Actualizar características
            $caracteristicas = Caracteristicas::updateCaracteristicasByMascota($idMascota);

            $this->log("Evaluación de mascota guardada: ID {$idMascota}", 'info');
            $this->sendSuccess([
                'message' => 'Evaluación guardada correctamente',
                'caracteristicas' => $caracteristicas
            ]);
        } catch (Exception $e) {
            $this->log("Error en saveMascotaEvaluation: " . $e->getMessage(), 'error');
            $this->sendError('Error al guardar la evaluación');
        }
    }

    /**
     * Obtiene detalles de una mascota
     *
     * @return void
     */
    public function getMascotaDetails()
    {
        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de mascota no válido');
                return;
            }

            $mascota = Mascotas::getMascotaById($id);
            if (!$mascota) {
                $this->sendError('Mascota no encontrada');
                return;
            }

            $caracteristicas = Caracteristicas::getCaracteristicasByMascota($id);

            $this->sendSuccess([
                'mascota' => $mascota,
                'caracteristicas' => $caracteristicas
            ]);
        } catch (Exception $e) {
            $this->log("Error en getMascotaDetails: " . $e->getMessage(), 'error');
            $this->sendError('Error al obtener los detalles');
        }
    }

    // ==========================================
    // MÉTODOS GENERALES
    // ==========================================

    /**
     * Elimina un registro genérico
     *
     * @return void
     */
    public function eliminarRegistro()
    {
        try {
            $id = Tools::getValue('id');
            $modelo = Tools::getValue('modelo');

            if (empty($id) || empty($modelo)) {
                $this->sendError('Parámetros incompletos');
                return;
            }

            // Validar que el modelo existe y tiene el método
            if (!class_exists($modelo) || !method_exists($modelo, 'eliminarRegistro')) {
                $this->sendError('Modelo no válido');
                return;
            }

            $result = $modelo::eliminarRegistro($id);

            if ($result) {
                $this->log("Registro eliminado: {$modelo} ID {$id}", 'info');
                $this->sendSuccess(['message' => 'Registro eliminado correctamente']);
            } else {
                $this->sendError('Error al eliminar el registro');
            }
        } catch (Exception $e) {
            $this->log("Error en eliminarRegistro: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Obtiene contenido para modal
     *
     * @return void
     */
    public function getContenidoModal()
    {
        try {
            $tipo = Tools::getValue('tipo');
            $id = Tools::getValue('id');

            $data = [];
            $template = '';

            switch ($tipo) {
                case 'mascota':
                    $data['mascota'] = Mascotas::getMascotaById($id);
                    $data['caracteristicas'] = Caracteristicas::getCaracteristicasByMascota($id);
                    $template = 'admin_modal_mascota';
                    break;

                case 'cuidador':
                    $data['cuidador'] = Cuidador::getCuidadorById($id);
                    $template = 'admin_modal_cuidador';
                    break;

                default:
                    $this->sendError('Tipo de modal no válido');
                    return;
            }

            $html = Render::getAjaxPage($template, $data);

            if (!empty($html)) {
                $this->sendSuccess(['html' => $html]);
            } else {
                $this->sendError('Error cargando el contenido del modal');
            }
        } catch (Exception $e) {
            $this->log("Error en getContenidoModal: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Búsqueda global en el sistema
     *
     * @return void
     */
    public function searchGlobal()
    {
        try {
            $query = Tools::getValue('q');
            $tipo = Tools::getValue('tipo', 'all');
            $limite = (int)Tools::getValue('limite', 10);

            if (strlen($query) < 2) {
                $this->sendError('La búsqueda debe tener al menos 2 caracteres');
                return;
            }

            $resultados = [];

            if ($tipo == 'all' || $tipo == 'mascotas') {
                $resultados['mascotas'] = Mascotas::searchByName($query, $limite);
            }

            if ($tipo == 'all' || $tipo == 'cuidadores') {
                $resultados['cuidadores'] = Cuidador::buscarCuidadores($query, $limite);
            }

            if ($tipo == 'all' || $tipo == 'usuarios') {
                // Implementar búsqueda de usuarios si es necesario
            }

            $this->sendSuccess([
                'resultados' => $resultados,
                'query' => $query,
                'total' => array_sum(array_map('count', $resultados))
            ]);
        } catch (Exception $e) {
            $this->log("Error en searchGlobal: " . $e->getMessage(), 'error');
            $this->sendError('Error en la búsqueda');
        }
    }

    /**
     * Obtiene estadísticas del sistema
     *
     * @return void
     */
    public function getStats()
    {
        try {
            $tipo = Tools::getValue('tipo', 'general');
            $stats = [];

            switch ($tipo) {
                case 'general':
                    $stats = [
                        'mascotas' => Mascotas::getTotalMascotas(),
                        'cuidadores' => Cuidador::getTotalCuidadores(),
                        'usuarios_admin' => Admin::getTotalUsuarios(),
                        'caracteristicas' => Caracteristicas::getTotalCaracteristicas()
                    ];
                    break;

                case 'mascotas':
                    $stats = Mascotas::getEstadisticas();
                    break;

                default:
                    $this->sendError('Tipo de estadística no válido');
                    return;
            }

            $this->sendSuccess(['stats' => $stats]);
        } catch (Exception $e) {
            $this->log("Error en getStats: " . $e->getMessage(), 'error');
            $this->sendError('Error al obtener estadísticas');
        }
    }

    /**
     * Sube un archivo
     *
     * @return void
     */
    public function uploadFile()
    {
        try {
            $tipo = Tools::getValue('tipo');
            $allowedTypes = ['imagen', 'documento', 'avatar'];

            if (!in_array($tipo, $allowedTypes)) {
                $this->sendError('Tipo de archivo no permitido');
                return;
            }

            if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
                $this->sendError('Error al subir el archivo');
                return;
            }

            $file = $_FILES['archivo'];
            $extension = Tools::getExtension($file['name']);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];

            if (!in_array($extension, $allowedExtensions)) {
                $this->sendError('Extensión de archivo no permitida');
                return;
            }

            // Generar nombre único
            $fileName = uniqid() . '.' . $extension;
            $uploadPath = _PATH_ . 'uploads/' . $tipo . '/';

            // Crear directorio si no existe
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $fullPath = $uploadPath . $fileName;

            if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                $this->log("Archivo subido: {$fileName}", 'info');
                $this->sendSuccess([
                    'message' => 'Archivo subido correctamente',
                    'filename' => $fileName,
                    'url' => _DOMINIO_ . 'uploads/' . $tipo . '/' . $fileName
                ]);
            } else {
                $this->sendError('Error al mover el archivo');
            }
        } catch (Exception $e) {
            $this->log("Error en uploadFile: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    // ==========================================
    // MÉTODOS DE UTILIDAD
    // ==========================================

    /**
     * Valida que los campos requeridos estén presentes
     *
     * @param array $fields Campos requeridos
     * @return void
     * @throws Exception Si falta algún campo
     */
    protected function validateRequiredFields($fields)
    {
        foreach ($fields as $field) {
            if (!Tools::getIsset($field) || empty(Tools::getValue($field))) {
                throw new Exception("Campo requerido faltante: {$field}");
            }
        }
    }

    /**
     * Envía respuesta de éxito
     *
     * @param array $data Datos a enviar
     * @return void
     */
    protected function sendSuccess($data = [])
    {
        $response = array_merge(['type' => 'success'], $data);
        die(json_encode($response));
    }

    /**
     * Envía respuesta de error
     *
     * @param string $message Mensaje de error
     * @param int $code Código HTTP
     * @return void
     */
    protected function sendError($message, $code = 400)
    {
        http_response_code($code);
        die(json_encode([
            'type' => 'error',
            'error' => $message,
            'code' => $code
        ]));
    }

}
