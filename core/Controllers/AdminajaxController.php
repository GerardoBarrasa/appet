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
     * Indica si la acción ya ha sido renderizada
     */
    protected $rendered = false;

    /**
     * Página actual que se está procesando
     */
    protected $currentPage = '';

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
        // Establecer la página actual
        $this->currentPage = $page;

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

    /**
     * Añade una ruta al controlador
     *
     * @param string $route Nombre de la ruta
     * @param callable $callback Función a ejecutar
     * @return void
     */
    public function add($route, $callback)
    {
        if ($this->currentPage === $route && is_callable($callback)) {
            call_user_func($callback);
            $this->setRendered(true);
        }
    }

    /**
     * Establece el estado de renderizado
     *
     * @param bool $rendered Estado de renderizado
     * @return void
     */
    public function setRendered($rendered = true)
    {
        $this->rendered = $rendered;
    }

    /**
     * Obtiene el estado de renderizado
     *
     * @return bool Estado de renderizado
     */
    public function getRendered()
    {
        return $this->rendered;
    }

    /**
     * Registra un mensaje en el log
     *
     * @param string $message Mensaje a registrar
     * @param string $level Nivel de log (info, warning, error)
     * @return void
     */
    public function log($message, $level = 'info')
    {
        if (function_exists('debug_log')) {
            debug_log([
                'message' => $message,
                'level' => $level,
                'controller' => 'AdminajaxController',
                'ip' => Tools::getClientIP(),
                'user_id' => $_SESSION['admin_panel']->id_usuario_admin ?? 0
            ], 'ADMINAJAX_LOG', 'adminajax');
        }
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

    /**
     * Crea un nuevo idioma
     *
     * @return void
     */
    public function createIdioma()
    {
        try {
            $this->validateRequiredFields(['nombre', 'codigo', 'slug']);

            $result = Idiomas::crearIdioma();

            if ($result) {
                $this->log("Idioma creado: " . Tools::getValue('nombre'), 'info');
                $this->sendSuccess(['message' => 'Idioma creado correctamente']);
            } else {
                $this->sendError('Error al crear el idioma');
            }
        } catch (Exception $e) {
            $this->log("Error en createIdioma: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Actualiza un idioma
     *
     * @return void
     */
    public function updateIdioma()
    {
        try {
            $this->validateRequiredFields(['id', 'nombre', 'codigo', 'slug']);

            $result = Idiomas::actualizarIdioma();

            if ($result) {
                $this->log("Idioma actualizado: " . Tools::getValue('nombre'), 'info');
                $this->sendSuccess(['message' => 'Idioma actualizado correctamente']);
            } else {
                $this->sendError('Error al actualizar el idioma');
            }
        } catch (Exception $e) {
            $this->log("Error en updateIdioma: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Elimina un idioma
     *
     * @return void
     */
    public function deleteIdioma()
    {
        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de idioma no válido');
                return;
            }

            $result = Idiomas::eliminarIdioma($id);

            if ($result) {
                $this->log("Idioma eliminado: ID {$id}", 'info');
                $this->sendSuccess(['message' => 'Idioma eliminado correctamente']);
            } else {
                $this->sendError('Error al eliminar el idioma');
            }
        } catch (Exception $e) {
            $this->log("Error en deleteIdioma: " . $e->getMessage(), 'error');
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

    /**
     * Crea un nuevo slug
     *
     * @return void
     */
    public function createSlug()
    {
        try {
            $this->validateRequiredFields(['slug', 'id_idioma', 'url']);

            $result = Slugs::crearSlug();

            if ($result) {
                $this->log("Slug creado: " . Tools::getValue('slug'), 'info');
                $this->sendSuccess(['message' => 'Slug creado correctamente']);
            } else {
                $this->sendError('Error al crear el slug');
            }
        } catch (Exception $e) {
            $this->log("Error en createSlug: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Actualiza un slug
     *
     * @return void
     */
    public function updateSlug()
    {
        try {
            $this->validateRequiredFields(['id', 'slug', 'id_idioma', 'url']);

            $result = Slugs::actualizarSlug();

            if ($result) {
                $this->log("Slug actualizado: " . Tools::getValue('slug'), 'info');
                $this->sendSuccess(['message' => 'Slug actualizado correctamente']);
            } else {
                $this->sendError('Error al actualizar el slug');
            }
        } catch (Exception $e) {
            $this->log("Error en updateSlug: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Elimina un slug
     *
     * @return void
     */
    public function deleteSlug()
    {
        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de slug no válido');
                return;
            }

            $result = Slugs::eliminarSlug($id);

            if ($result) {
                $this->log("Slug eliminado: ID {$id}", 'info');
                $this->sendSuccess(['message' => 'Slug eliminado correctamente']);
            } else {
                $this->sendError('Error al eliminar el slug');
            }
        } catch (Exception $e) {
            $this->log("Error en deleteSlug: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    // ==========================================
    // MÉTODOS PARA TRADUCCIONES
    // ==========================================

    /**
     * Obtiene traducciones filtradas
     *
     * @return void
     */
    public function getTraduccionesFiltered()
    {
        try {
            $comienzo = (int)Tools::getValue('comienzo', 0);
            $limite = (int)Tools::getValue('limite', 10);
            $pagina = (int)Tools::getValue('pagina', 1);

            $traducciones = Traducciones::getTraduccionesWithFiltros($comienzo, $limite, true);

            $data = [
                'comienzo' => $comienzo,
                'limite' => $limite,
                'pagina' => $pagina,
                'traducciones' => $traducciones['listado'],
                'total' => $traducciones['total']
            ];

            $html = Render::getAjaxPage('admin_traducciones', $data);

            if (!empty($html)) {
                $this->sendSuccess(['html' => $html]);
            } else {
                $this->sendError('Error cargando el contenido');
            }
        } catch (Exception $e) {
            $this->log("Error en getTraduccionesFiltered: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Crea una nueva traducción
     *
     * @return void
     */
    public function createTraduccion()
    {
        try {
            $this->validateRequiredFields(['clave', 'id_idioma', 'valor']);

            $result = Traducciones::crearTraduccion();

            if ($result) {
                $this->log("Traducción creada: " . Tools::getValue('clave'), 'info');
                $this->sendSuccess(['message' => 'Traducción creada correctamente']);
            } else {
                $this->sendError('Error al crear la traducción');
            }
        } catch (Exception $e) {
            $this->log("Error en createTraduccion: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Actualiza una traducción
     *
     * @return void
     */
    public function updateTraduccion()
    {
        try {
            $this->validateRequiredFields(['id', 'clave', 'id_idioma', 'valor']);

            $result = Traducciones::actualizarTraduccion();

            if ($result) {
                $this->log("Traducción actualizada: " . Tools::getValue('clave'), 'info');
                $this->sendSuccess(['message' => 'Traducción actualizada correctamente']);
            } else {
                $this->sendError('Error al actualizar la traducción');
            }
        } catch (Exception $e) {
            $this->log("Error en updateTraduccion: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Elimina una traducción
     *
     * @return void
     */
    public function deleteTraduccion()
    {
        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de traducción no válido');
                return;
            }

            $result = Traducciones::eliminarTraduccion($id);

            if ($result) {
                $this->log("Traducción eliminada: ID {$id}", 'info');
                $this->sendSuccess(['message' => 'Traducción eliminada correctamente']);
            } else {
                $this->sendError('Error al eliminar la traducción');
            }
        } catch (Exception $e) {
            $this->log("Error en deleteTraduccion: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Regenera el caché de traducciones
     *
     * @return void
     */
    public function regenerarCacheTraducciones()
    {
        try {
            $idiomas = Idiomas::getLanguages();
            $regenerados = 0;

            foreach ($idiomas as $idioma) {
                $archivo = _PATH_ . 'translations/' . $idioma->slug . '.php';
                if (Traducciones::regenerarCacheTraduccionesByIdioma($idioma->id, $archivo)) {
                    $regenerados++;
                }
            }

            $this->log("Cache de traducciones regenerado para {$regenerados} idiomas", 'info');
            $this->sendSuccess([
                'message' => "Cache regenerado para {$regenerados} idiomas",
                'regenerados' => $regenerados
            ]);
        } catch (Exception $e) {
            $this->log("Error en regenerarCacheTraducciones: " . $e->getMessage(), 'error');
            $this->sendError('Error al regenerar el cache');
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
     * Crea una nueva mascota
     *
     * @return void
     */
    public function createMascota()
    {
        try {
            $this->validateRequiredFields(['nombre', 'tipo', 'id_cuidador']);

            $result = Mascotas::crearMascota();

            if ($result) {
                $this->log("Mascota creada: " . Tools::getValue('nombre'), 'info');
                $this->sendSuccess([
                    'message' => 'Mascota creada correctamente',
                    'id' => $result
                ]);
            } else {
                $this->sendError('Error al crear la mascota');
            }
        } catch (Exception $e) {
            $this->log("Error en createMascota: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Actualiza una mascota
     *
     * @return void
     */
    public function updateMascota()
    {
        try {
            $this->validateRequiredFields(['id', 'nombre', 'tipo']);

            $result = Mascotas::actualizarMascota();

            if ($result) {
                $this->log("Mascota actualizada: " . Tools::getValue('nombre'), 'info');
                $this->sendSuccess(['message' => 'Mascota actualizada correctamente']);
            } else {
                $this->sendError('Error al actualizar la mascota');
            }
        } catch (Exception $e) {
            $this->log("Error en updateMascota: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Elimina una mascota
     *
     * @return void
     */
    public function deleteMascota()
    {
        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de mascota no válido');
                return;
            }

            $result = Mascotas::eliminarMascota($id);

            if ($result) {
                $this->log("Mascota eliminada: ID {$id}", 'info');
                $this->sendSuccess(['message' => 'Mascota eliminada correctamente']);
            } else {
                $this->sendError('Error al eliminar la mascota');
            }
        } catch (Exception $e) {
            $this->log("Error en deleteMascota: " . $e->getMessage(), 'error');
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
            // Obtener idmascota del POST
            $idMascota = (int)Tools::getValue('idmascota');

            if (!$idMascota) {
                $this->sendError('ID de mascota no válido');
                return;
            }

            // Verificar que la mascota existe
            $mascota = Mascotas::getMascotaById($idMascota);
            if (!$mascota) {
                $this->sendError('Mascota no encontrada');
                return;
            }

            // Registrar los datos recibidos para depuración
            $this->log("Datos recibidos para mascota ID {$idMascota}: " . print_r($_POST, true), 'info');

            // Actualizar características
            $caracteristicas = Caracteristicas::updateCaracteristicasByMascota($idMascota);

            $this->log("Evaluación de mascota guardada: ID {$idMascota}", 'info');
            $this->sendSuccess([
                'message' => 'Evaluación guardada correctamente',
                'caracteristicas' => $caracteristicas
            ]);
        } catch (Exception $e) {
            $this->log("Error en saveMascotaEvaluation: " . $e->getMessage(), 'error');
            $this->sendError('Error al guardar la evaluación: ' . $e->getMessage());
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
    // MÉTODOS PARA CUIDADORES
    // ==========================================

    /**
     * Obtiene cuidadores con filtros
     *
     * @return void
     */
    public function getCuidadoresAdmin()
    {
        try {
            $comienzo = (int)Tools::getValue('comienzo', 0);
            $limite = (int)Tools::getValue('limite', 10);
            $pagina = (int)Tools::getValue('pagina', 1);

            $cuidadores = Cuidador::getCuidadoresFiltered($comienzo, $limite);
            $total = count(Cuidador::getCuidadoresFiltered($comienzo, $limite, false));

            $data = [
                'comienzo' => $comienzo,
                'limite' => $limite,
                'pagina' => $pagina,
                'cuidadores' => $cuidadores,
                'total' => $total
            ];

            $html = Render::getAjaxPage('admin_cuidadores_list', $data);

            if (!empty($html)) {
                $this->sendSuccess(['html' => $html]);
            } else {
                $this->sendError('Error cargando el contenido');
            }
        } catch (Exception $e) {
            $this->log("Error en getCuidadoresAdmin: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Crea un nuevo cuidador
     *
     * @return void
     */
    public function createCuidador()
    {
        try {
            $this->validateRequiredFields(['nombre', 'email']);

            $result = Cuidador::crearCuidador();

            if ($result) {
                $this->log("Cuidador creado: " . Tools::getValue('nombre'), 'info');
                $this->sendSuccess([
                    'message' => 'Cuidador creado correctamente',
                    'id' => $result
                ]);
            } else {
                $this->sendError('Error al crear el cuidador');
            }
        } catch (Exception $e) {
            $this->log("Error en createCuidador: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Actualiza un cuidador
     *
     * @return void
     */
    public function updateCuidador()
    {
        try {
            $this->validateRequiredFields(['id', 'nombre', 'email']);

            $result = Cuidador::actualizarCuidador();

            if ($result) {
                $this->log("Cuidador actualizado: " . Tools::getValue('nombre'), 'info');
                $this->sendSuccess(['message' => 'Cuidador actualizado correctamente']);
            } else {
                $this->sendError('Error al actualizar el cuidador');
            }
        } catch (Exception $e) {
            $this->log("Error en updateCuidador: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Elimina un cuidador
     *
     * @return void
     */
    public function deleteCuidador()
    {
        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de cuidador no válido');
                return;
            }

            $result = Cuidador::eliminarCuidador($id);

            if ($result) {
                $this->log("Cuidador eliminado: ID {$id}", 'info');
                $this->sendSuccess(['message' => 'Cuidador eliminado correctamente']);
            } else {
                $this->sendError('Error al eliminar el cuidador');
            }
        } catch (Exception $e) {
            $this->log("Error en deleteCuidador: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    // ==========================================
    // MÉTODOS PARA CARACTERÍSTICAS
    // ==========================================

    /**
     * Obtiene características con filtros
     *
     * @return void
     */
    public function getCaracteristicasAdmin()
    {
        try {
            $comienzo = (int)Tools::getValue('comienzo', 0);
            $limite = (int)Tools::getValue('limite', 10);
            $pagina = (int)Tools::getValue('pagina', 1);

            $caracteristicas = Caracteristicas::getCaracteristicasFiltered($comienzo, $limite);
            $total = count(Caracteristicas::getCaracteristicasFiltered($comienzo, $limite, false));

            $data = [
                'comienzo' => $comienzo,
                'limite' => $limite,
                'pagina' => $pagina,
                'caracteristicas' => $caracteristicas,
                'total' => $total
            ];

            $html = Render::getAjaxPage('admin_caracteristicas_list', $data);

            if (!empty($html)) {
                $this->sendSuccess(['html' => $html]);
            } else {
                $this->sendError('Error cargando el contenido');
            }
        } catch (Exception $e) {
            $this->log("Error en getCaracteristicasAdmin: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Crea una nueva característica
     *
     * @return void
     */
    public function createCaracteristica()
    {
        try {
            $this->validateRequiredFields(['nombre', 'tipo']);

            $result = Caracteristicas::crearCaracteristica();

            if ($result) {
                $this->log("Característica creada: " . Tools::getValue('nombre'), 'info');
                $this->sendSuccess([
                    'message' => 'Característica creada correctamente',
                    'id' => $result
                ]);
            } else {
                $this->sendError('Error al crear la característica');
            }
        } catch (Exception $e) {
            $this->log("Error en createCaracteristica: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Actualiza una característica
     *
     * @return void
     */
    public function updateCaracteristica()
    {
        try {
            $this->validateRequiredFields(['id', 'nombre', 'tipo']);

            $result = Caracteristicas::actualizarCaracteristica();

            if ($result) {
                $this->log("Característica actualizada: " . Tools::getValue('nombre'), 'info');
                $this->sendSuccess(['message' => 'Característica actualizada correctamente']);
            } else {
                $this->sendError('Error al actualizar la característica');
            }
        } catch (Exception $e) {
            $this->log("Error en updateCaracteristica: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Elimina una característica
     *
     * @return void
     */
    public function deleteCaracteristica()
    {
        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de característica no válido');
                return;
            }

            $result = Caracteristicas::eliminarCaracteristica($id);

            if ($result) {
                $this->log("Característica eliminada: ID {$id}", 'info');
                $this->sendSuccess(['message' => 'Característica eliminada correctamente']);
            } else {
                $this->sendError('Error al eliminar la característica');
            }
        } catch (Exception $e) {
            $this->log("Error en deleteCaracteristica: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
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

    /**
     * Exporta datos
     *
     * @return void
     */
    public function exportData()
    {
        try {
            $tipo = Tools::getValue('tipo');
            $formato = Tools::getValue('formato', 'csv');

            if (!in_array($formato, ['csv', 'json', 'excel'])) {
                $this->sendError('Formato no soportado');
                return;
            }

            $data = [];
            $filename = '';

            switch ($tipo) {
                case 'mascotas':
                    $data = Mascotas::getMascotasFiltered(0, 1000, false);
                    $filename = 'mascotas_export_' . date('Ymd');
                    break;

                case 'cuidadores':
                    $data = Cuidador::getCuidadoresFiltered(0, 1000, false);
                    $filename = 'cuidadores_export_' . date('Ymd');
                    break;

                default:
                    $this->sendError('Tipo de exportación no válido');
                    return;
            }

            $result = '';

            if ($formato === 'csv') {
                $result = $this->generateCSV($data);
            } elseif ($formato === 'json') {
                $result = json_encode($data);
            }

            $this->sendSuccess([
                'data' => $result,
                'filename' => $filename . '.' . $formato
            ]);
        } catch (Exception $e) {
            $this->log("Error en exportData: " . $e->getMessage(), 'error');
            $this->sendError('Error al exportar datos');
        }
    }

    /**
     * Genera un CSV a partir de un array de datos
     *
     * @param array $data Datos a exportar
     * @return string CSV generado
     */
    protected function generateCSV($data)
    {
        if (empty($data)) {
            return '';
        }

        $csv = '';
        $headers = array_keys((array)$data[0]);

        // Cabeceras
        $csv .= implode(',', $headers) . "\n";

        // Datos
        foreach ($data as $row) {
            $rowData = [];
            foreach ($headers as $header) {
                $value = isset($row->$header) ? $row->$header : '';
                $value = str_replace('"', '""', $value); // Escapar comillas
                $rowData[] = '"' . $value . '"';
            }
            $csv .= implode(',', $rowData) . "\n";
        }

        return $csv;
    }

    /**
     * Importa datos
     *
     * @return void
     */
    public function importData()
    {
        try {
            $tipo = Tools::getValue('tipo');

            if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
                $this->sendError('Error al subir el archivo');
                return;
            }

            $file = $_FILES['archivo'];
            $extension = Tools::getExtension($file['name']);

            if ($extension !== 'csv') {
                $this->sendError('Solo se permiten archivos CSV');
                return;
            }

            $content = file_get_contents($file['tmp_name']);
            $lines = explode("\n", $content);
            $headers = str_getcsv(array_shift($lines));

            $data = [];
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;

                $row = str_getcsv($line);
                $data[] = array_combine($headers, $row);
            }

            $imported = 0;
            $errors = [];

            switch ($tipo) {
                case 'mascotas':
                    foreach ($data as $index => $row) {
                        try {
                            if (Mascotas::importarMascota($row)) {
                                $imported++;
                            } else {
                                $errors[] = "Fila " . ($index + 2) . ": Error al importar mascota";
                            }
                        } catch (Exception $e) {
                            $errors[] = "Fila " . ($index + 2) . ": " . $e->getMessage();
                        }
                    }
                    break;

                default:
                    $this->sendError('Tipo de importación no válido');
                    return;
            }

            $this->log("Importación completada: {$imported} registros importados, " . count($errors) . " errores", 'info');
            $this->sendSuccess([
                'message' => 'Importación completada',
                'imported' => $imported,
                'errors' => $errors,
                'total' => count($data)
            ]);
        } catch (Exception $e) {
            $this->log("Error en importData: " . $e->getMessage(), 'error');
            $this->sendError('Error al importar datos');
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
