<?php

/**
 * Controlador de administración para el panel de control
 *
 * Este controlador maneja todas las rutas del panel de administración,
 * incluyendo autenticación, gestión de contenido y configuración del sistema.
 */
class AdminController
{
    /**
     * Configuración del controlador
     */
    protected $config = [
        'layout' => 'back-end',
        'pagination' => [
            'comienzo' => 0,
            'limite' => 10,
            'pagina' => 1
        ],
        'assets' => [
            'css' => [
                'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback',
                'https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap'
            ],
            'js' => []
        ]
    ];

    /**
     * Variables de paginación
     */
    var $comienzo = 0;
    var $limite = 10;
    var $pagina = 1;

    /**
     * Rutas registradas
     */
    protected $routes = [];

    /**
     * Indica si se ha renderizado alguna ruta
     */
    protected $rendered = false;

    /**
     * Página actual
     */
    protected $currentPage = '';

    /**
     * Establece la página actual
     *
     * @param string $page
     * @return void
     */
    public function setPage($page)
    {
        $this->currentPage = $page;
    }

    /**
     * Obtiene la página actual
     *
     * @return string
     */
    public function getPage()
    {
        return $this->currentPage;
    }

    /**
     * Log de actividad del controlador
     *
     * @param string $message Mensaje a registrar
     * @param string $level Nivel del log (info, warning, error)
     * @return void
     */
    public function log($message, $level = 'info')
    {
        $logMessage = sprintf(
            "[%s] [%s] [%s] %s",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            get_class($this),
            $message
        );

        debug_log($logMessage, strtoupper($level), 'admin_controller');
    }

    /**
     * Carga las traducciones para el panel de administración
     *
     * @return void
     */
    public function loadTraducciones()
    {
        $this->loadTraduccionesAdmin();
    }

    /**
     * Ejecuta el controlador para la ruta solicitada
     *
     * @param string $page Página solicitada
     * @return void
     */
    public function execute($page)
    {
        // Establecer la página actual
        $this->setPage($page);

        // Verificar si es una ruta de tipo appet-*
        if (isset($_REQUEST['userslug']) && isset($_REQUEST['mod'])) {
            $page = $_REQUEST['mod'];
            $this->setPage($page);
        }

        try {
            $this->initializeSafe($page);
            $isAuthenticated = $this->isAuthenticated();

            if (!$isAuthenticated) {
                $this->showLogin();
                return;
            }

            if (empty($page)) {
                $this->showDashboard();
                return;
            }

            $this->defineRoutes();

            if (!$this->executeRoute($page)) {
                $this->show404();
                return;
            }

        } catch (Exception $e) {
            debug_log([
                'error' => 'Exception in AdminController::execute',
                'message' => $e->getMessage(),
                'page' => $page
            ], 'ADMIN_EXECUTE_ERROR', 'admin');

            if (defined('_DEBUG_') && _DEBUG_) {
                echo "<h1>Error en AdminController</h1>";
                echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            } else {
                $this->show404();
            }
        }
    }

    /**
     * Inicializa el controlador de forma segura (sin Admin::validateUser que causa bucles)
     *
     * @param string $page Página solicitada
     * @return void
     */
    protected function initializeSafe($page)
    {
        try {
            if (class_exists('Admin')) {
                Admin::getEntorno();

                if ($this->isAuthenticated()) {
                    Admin::validateUser();
                }
            }

            if (class_exists('Render')) {
                Render::$layout = $this->config['layout'];
            }

            $this->registerAssets();

            if (class_exists('Idiomas') && class_exists('Render')) {
                $moduleName = 'Dashboard';
                if (!empty($page)) {
                    $moduleName = ucwords(str_replace('-', ' ', $page));
                }

                Render::$layout_data = [
                    'idiomas' => Idiomas::getLanguagesAdminForm(),
                    'mod' => $moduleName
                ];
            }

            $this->initializePagination();

        } catch (Exception $e) {
            debug_log([
                'error' => 'Exception in initializeSafe',
                'message' => $e->getMessage()
            ], 'ADMIN_INITIALIZE_ERROR', 'admin');
            throw $e;
        }
    }

    /**
     * Registra los assets CSS y JS necesarios
     *
     * @return void
     */
    protected function registerAssets()
    {
        if (!class_exists('Tools')) {
            return;
        }

        // CSS
        foreach ($this->config['assets']['css'] as $css) {
            Tools::registerStylesheet($css);
        }

        if (defined('_ASSETS_') && defined('_COMMON_')) {
            Tools::registerStylesheet(_ASSETS_ . _COMMON_ . 'bootstrap-5.3.3-dist/css/bootstrap.min.css');
            Tools::registerStylesheet(_ASSETS_ . _COMMON_ . 'bootstrap-slider/css/bootstrap-slider.min.css');
            Tools::registerStylesheet(_ASSETS_ . _COMMON_ . 'toastr/toastr.min.css');
            Tools::registerStylesheet(_ASSETS_ . _COMMON_ . 'fontawesome-free-6.6.0-web/css/all.css');
        }

        if (defined('_RESOURCES_') && defined('_ADMIN_')) {
            Tools::registerStylesheet(_RESOURCES_ . _ADMIN_ . 'css/adminlte.min.css');
            Tools::registerStylesheet(_RESOURCES_ . _ADMIN_ . 'css/style-admin.css?v=' . time());
        }

        // JS
        if (defined('_ASSETS_') && defined('_COMMON_')) {
            Tools::registerJavascript(_ASSETS_ . _COMMON_ . 'jquery-3.7.1.min.js', 'top');
            Tools::registerJavascript(_ASSETS_ . _COMMON_ . 'bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js');
            Tools::registerJavascript(_ASSETS_ . _COMMON_ . 'bootstrap-slider/bootstrap-slider.min.js');
            Tools::registerJavascript(_ASSETS_ . _COMMON_ . 'bootstrap-switch/js/bootstrap-switch.min.js');
            Tools::registerJavascript(_ASSETS_ . _COMMON_ . 'underscore.js');
            Tools::registerJavascript(_ASSETS_ . _COMMON_ . 'toastar/toastr.min.js');
        }

        if (defined('_RESOURCES_') && defined('_ADMIN_')) {
            Tools::registerJavascript(_RESOURCES_ . _ADMIN_ . 'js/adminlte.min.js');
            Tools::registerJavascript(_RESOURCES_ . _ADMIN_ . 'js/custom.js?v=' . time(), 'top');
            Tools::registerJavascript(_RESOURCES_ . _ADMIN_ . 'js/jquery.ba-throttle-debounce.min.js', 'top');
        }
    }

    /**
     * Inicializa las variables de paginación
     *
     * @return void
     */
    protected function initializePagination()
    {
        $this->comienzo = $this->config['pagination']['comienzo'];
        $this->limite = $this->config['pagination']['limite'];
        $this->pagina = $this->config['pagination']['pagina'];
    }

    /**
     * Añade una ruta al controlador
     *
     * @param string $route Ruta
     * @param callable|array $callback Callback a ejecutar
     * @return void
     */
    protected function add($route, $callback)
    {
        $this->routes[$route] = $callback;
    }

    /**
     * Ejecuta una ruta específica
     *
     * @param string $route Ruta a ejecutar
     * @return bool True si se ejecutó, false si no existe
     */
    protected function executeRoute($route)
    {
        if (isset($this->routes[$route])) {
            try {
                $callback = $this->routes[$route];

                if (is_callable($callback)) {
                    call_user_func($callback);
                } elseif (is_array($callback) && count($callback) == 2) {
                    call_user_func_array($callback, []);
                }

                $this->setRendered(true);
                return true;
            } catch (Exception $e) {
                $this->log("Error ejecutando ruta '{$route}': " . $e->getMessage(), 'error');
                return false;
            }
        }

        return false;
    }

    /**
     * Establece el estado de renderizado
     *
     * @param bool $rendered
     * @return void
     */
    protected function setRendered($rendered = true)
    {
        $this->rendered = $rendered;
    }

    /**
     * Obtiene el estado de renderizado
     *
     * @return bool
     */
    protected function getRendered()
    {
        return $this->rendered;
    }

    /**
     * Define las rutas disponibles en el controlador
     *
     * @return void
     */
    protected function defineRoutes()
    {
        // Autenticación (disponible siempre)
        $this->add('logout', [$this, 'logoutAction']);

        // Solo definir rutas protegidas si está autenticado
        if ($this->isAuthenticated()) {
            // Gestión de idiomas
            $this->add('idiomas', [$this, 'idiomasAction']);
            $this->add('administrar-idioma', [$this, 'administrarIdiomaAction']);

            // Gestión de traducciones
            $this->add('traducciones', [$this, 'traduccionesAction']);
            $this->add('traduccion', [$this, 'traduccionAction']);
            $this->add('regenerar-cache-traducciones', [$this, 'regenerarCacheTraduccionesAction']);

            // Gestión de slugs/páginas
            $this->add('slugs', [$this, 'slugsAction']);
            $this->add('administrar-slug', [$this, 'administrarSlugAction']);

            // Gestión de usuarios admin
            $this->add('usuarios-admin', [$this, 'usuariosAdminAction']);
            $this->add('usuario-admin', [$this, 'usuarioAdminAction']);

            // Gestión de mascotas
            $this->add('mascotas', [$this, 'mascotasAction']);
            $this->add('mascota', [$this, 'mascotaAction']);
            $this->add('nueva-mascota', [$this, 'nuevaMascotaAction']);
        }

        // Página 404
        $this->add('404', [$this, 'show404']);
    }

    /**
     * Verifica si el usuario está autenticado
     *
     * @return bool
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['admin_panel']) && !empty($_SESSION['admin_panel']);
    }

    /**
     * Verifica si el usuario es super admin
     *
     * @return bool
     */
    protected function isSuperAdmin()
    {
        return $this->isAuthenticated() &&
            isset($_SESSION['admin_panel']->id_country) &&
            empty($_SESSION['admin_panel']->id_country);
    }

    /**
     * Requiere autenticación
     *
     * @return void
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            $this->showLogin();
            exit;
        }
    }

    /**
     * Requiere permisos de super admin
     *
     * @return void
     */
    protected function requireSuperAdmin()
    {
        if (!$this->isSuperAdmin()) {
            $this->showLogin();
            exit;
        }
    }

    // ==========================================
    // ACCIONES DEL CONTROLADOR
    // ==========================================

    /**
     * Muestra el formulario de login o procesa el login
     *
     * @return void
     */
    public function showLogin()
    {
        // Si ya está autenticado, mostrar dashboard en su lugar
        if ($this->isAuthenticated()) {
            $this->showDashboard();
            return;
        }

        $mensajeError = $_SESSION['actions_mensajeError'] ?? '';
        unset($_SESSION['actions_mensajeError']);

        if (class_exists('Render')) {
            Render::$layout = 'actions';
        }

        // Procesar formulario de login
        if (isset($_REQUEST['btn-login']) && class_exists('Admin') && class_exists('Tools')) {
            $usuario = Tools::getValue('usuario');
            $password = Tools::md5(Tools::getValue('password'));

            if (Admin::login($usuario, $password)) {
                // Después del login exitoso, redirigir al dashboard
                $adminPath = defined('_ADMIN_') ? _ADMIN_ : 'admin/';
                $dashboardUrl = _DOMINIO_ . $adminPath;
                header("Location: {$dashboardUrl}");
                exit;
            } else {
                $mensajeError = "Usuario y/o contrase&ntilde;a incorrectos.";
                debug_log("Login failed for user: {$usuario}", 'LOGIN_FAILED', 'admin');
            }
        }

        $data = [
            'mensajeError' => $mensajeError,
        ];

        if (class_exists('Metas')) {
            Metas::$title = "&iexcl;Con&eacute;ctate!";
        }

        if (class_exists('Render')) {
            Render::adminPage('login', $data);
        } else {
            echo "<h1>Login</h1>";
            echo "<p>Render class not available</p>";
        }

        $this->setRendered(true);
    }

    /**
     * Muestra el dashboard principal
     *
     * @return void
     */
    public function showDashboard()
    {
        if (!$this->isAuthenticated()) {
            debug_log("User not authenticated, cannot show dashboard", 'DASHBOARD_ERROR', 'admin');
            $this->showLogin();
            return;
        }

        if (class_exists('Metas')) {
            Metas::$title = "Inicio";
        }

        if (class_exists('Render')) {
            Render::adminPage('home');
        } else {
            echo "<h1>Dashboard</h1>";
            echo "<p>Bienvenido al panel de administración</p>";
        }

        $this->setRendered(true);
    }

    /**
     * Muestra página 404
     *
     * @return void
     */
    public function show404()
    {
        http_response_code(404);

        if (class_exists('Render')) {
            Render::adminPage('404');
        } else {
            echo "<h1>404 - Página no encontrada</h1>";
        }

        $this->setRendered(true);
    }

    /**
     * Acción de logout
     *
     * @return void
     */
    public function logoutAction()
    {
        if (class_exists('Tools')) {
            Tools::logError('LOGOUT');
        }

        debug_log("User logout", 'LOGOUT', 'admin');

        if (class_exists('Render')) {
            Render::$layout = false;
        }

        if (class_exists('Admin')) {
            Admin::logout();
        } else {
            // Logout manual si no existe la clase Admin
            unset($_SESSION['admin_panel']);
            unset($_SESSION['admin_vars']);
        }

        // Redirigir al login después del logout
        $adminPath = defined('_ADMIN_') ? _ADMIN_ : 'admin/';
        $loginUrl = _DOMINIO_ . $adminPath;
        header("Location: {$loginUrl}");
        exit;
    }

    /**
     * Acción para gestión de idiomas
     *
     * @return void
     */
    public function idiomasAction()
    {
        $this->requireAuth();

        if (class_exists('Tools') && defined('_ASSETS_') && defined('_ADMIN_')) {
            Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'footable/footable.bootstrap.min.css');
            Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'footable/footable.min.js');
        }

        $data = [
            'comienzo' => $this->comienzo,
            'pagina' => $this->pagina,
            'limite' => $this->limite
        ];

        if (class_exists('Render')) {
            Render::adminPage('idiomas', $data);
        }

        $this->setRendered(true);
    }

    /**
     * Acción para administrar un idioma específico
     *
     * @return void
     */
    public function administrarIdiomaAction()
    {
        $this->requireAuth();

        if (!class_exists('Tools') || !class_exists('Idiomas')) {
            $this->show404();
            return;
        }

        $id = Tools::getValue('data');
        if (!$id) {
            $adminPath = defined('_ADMIN_') ? _ADMIN_ : 'admin/';
            header("Location: " . _DOMINIO_ . $adminPath . 'idiomas/');
            exit;
        }

        // Procesar formulario
        if (isset($_REQUEST['action'])) {
            $result = Idiomas::administrarIdioma();

            if ($result == 'ok') {
                if ($id == '0') {
                    Tools::registerAlert("Idioma creado correctamente.", "success");
                    $adminPath = defined('_ADMIN_') ? _ADMIN_ : 'admin/';
                    header("Location: " . _DOMINIO_ . $adminPath . 'idiomas/');
                    exit;
                } else {
                    Tools::registerAlert("Idioma actualizado correctamente.", "success");
                }
            } else {
                Tools::registerAlert($result);
            }
        }

        $datos_idioma = ($id != '0') ? Idiomas::getLanguages($id) : [];

        $data = [
            'id' => $id,
            'datos_idioma' => $datos_idioma,
        ];

        if (class_exists('Render')) {
            Render::adminPage('administrar-idioma', $data);
        }

        $this->setRendered(true);
    }

    /**
     * Acción para gestión de traducciones
     *
     * @return void
     */
    public function traduccionesAction()
    {
        $this->requireAuth();

        if (class_exists('Tools') && defined('_ASSETS_') && defined('_ADMIN_')) {
            Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'footable/footable.bootstrap.min.css');
            Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'footable/footable.min.js');
        }

        $idiomas = [];
        $porcentajeTraduccionesPorIdioma = [];

        if (class_exists('Idiomas') && class_exists('Traducciones')) {
            $idiomas = Idiomas::getLanguages();
            foreach ($idiomas as $idioma) {
                $porcentajeTraduccionesPorIdioma[$idioma->id] = Traducciones::getStatsTraduccionesByIdioma($idioma->id);
            }
        }

        $data = [
            'porcentajeTraduccionesPorIdioma' => $porcentajeTraduccionesPorIdioma,
            'comienzo' => $this->comienzo,
            'pagina' => $this->pagina,
            'limite' => $this->limite,
        ];

        if (class_exists('Render')) {
            Render::adminPage('traducciones', $data);
        }

        $this->setRendered(true);
    }

    /**
     * Acción para editar una traducción específica
     *
     * @return void
     */
    public function traduccionAction()
    {
        $this->requireAuth();

        if (class_exists('Tools') && defined('_ASSETS_') && defined('_ADMIN_')) {
            Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'select2/css/select2.min.css');
            Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'select2/js/select2.min.js');
        }

        $traduccionId = class_exists('Tools') ? Tools::getValue('data') : '';
        $traduccion = false;

        // Procesar actualización
        if (class_exists('Tools') && Tools::getIsset('submitUpdateTraduccion')) {
            $this->handleUpdateTraduccion();
        }

        // Procesar creación
        if (class_exists('Tools') && Tools::getIsset('submitCrearTraduccion')) {
            $this->handleCreateTraduccion();
        }

        if (class_exists('Metas')) {
            Metas::$title = "Editando traducción";
        }

        if ($traduccionId !== 'new' && class_exists('Traducciones')) {
            $traduccion = Traducciones::getTraduccionById($traduccionId);
        }

        $data = [
            'traduccion' => $traduccion
        ];

        if (class_exists('Render')) {
            Render::adminPage('traduccion', $data);
        }

        $this->setRendered(true);
    }

    /**
     * Maneja la actualización de una traducción
     *
     * @return void
     */
    protected function handleUpdateTraduccion()
    {
        if (!class_exists('Tools') || !class_exists('Traducciones')) {
            return;
        }

        $traduccionId = Tools::getValue('id_traduccion');
        $shortcode = Tools::getValue('shortcode');

        if (!empty($shortcode) && !Traducciones::checkShortcodeExists($shortcode, $traduccionId)) {
            $textos = Tools::getValue('texto');
            Traducciones::actualizarTraduccion($traduccionId, $shortcode, $textos);
            Tools::registerAlert("Traducción actualizada correctamente.", "success");
        } else {
            Tools::registerAlert("Shortcode vacío o ya existe", "error");
        }
    }

    /**
     * Maneja la creación de una nueva traducción
     *
     * @return void
     */
    protected function handleCreateTraduccion()
    {
        if (!class_exists('Tools') || !class_exists('Traducciones')) {
            return;
        }

        $shortcode = Tools::getValue('shortcode');

        if (!empty($shortcode) && !Traducciones::checkShortcodeExists($shortcode)) {
            $id_lang = Tools::getValue('id_idioma');
            $texto = Tools::getValue('texto');
            $traduccionId = Traducciones::crearTraduccion($shortcode, $id_lang, $texto);
            Tools::registerAlert("Traducción creada correctamente.", "success");
            header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . "traduccion/" . (int)$traduccionId . "/");
            exit;
        } else {
            Tools::registerAlert("Shortcode vacío o ya existe", "error");
            header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . "traducciones/");
            exit;
        }
    }

    /**
     * Acción para regenerar caché de traducciones
     *
     * @return void
     */
    public function regenerarCacheTraduccionesAction()
    {
        $this->requireAuth();

        if (class_exists('Idiomas') && class_exists('Traducciones')) {
            $idiomas = Idiomas::getLanguages();
            foreach ($idiomas as $idioma) {
                Traducciones::regenerarCacheTraduccionesByIdioma($idioma->id, _PATH_ . 'translations/' . $idioma->slug . '.php');
            }
        }

        if (class_exists('Render')) {
            Render::$layout = false;
        }

        if (class_exists('Tools')) {
            Tools::registerAlert("Caché de traducciones regenerada correctamente.", "success");
        }

        header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . "traducciones/");
        exit;
    }

    /**
     * Acción para gestión de slugs/páginas
     *
     * @return void
     */
    public function slugsAction()
    {
        $this->requireSuperAdmin();

        if (class_exists('Tools') && defined('_ASSETS_') && defined('_ADMIN_')) {
            Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'select2/css/select2.min.css');
            Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'select2/js/select2.min.js');
            Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'footable/footable.bootstrap.min.css');
            Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'footable/footable.min.js');
        }

        $data = [
            'comienzo' => $this->comienzo,
            'pagina' => $this->pagina,
            'limite' => $this->limite,
            'languages' => class_exists('Idiomas') ? Idiomas::getLanguages() : [],
            'slugsPages' => class_exists('Slugs') ? Slugs::getPagesFromSlugs() : [],
            'languageDefault' => class_exists('Idiomas') ? Idiomas::getDefaultLanguage() : null
        ];

        if (class_exists('Metas')) {
            Metas::$title = "Páginas meta";
        }

        if (class_exists('Render')) {
            Render::adminPage('slugs_admin', $data);
        }

        $this->setRendered(true);
    }

    /**
     * Acción para administrar un slug específico
     *
     * @return void
     */
    public function administrarSlugAction()
    {
        $this->requireSuperAdmin();

        if (!class_exists('Tools')) {
            $this->show404();
            return;
        }

        $id = Tools::getValue('data');
        if (!$id) {
            header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . 'slugs/');
            exit;
        }

        $msg_error = 0;
        $datos = false;

        // Procesar formulario
        if (Tools::getIsset('submitUpdateSlug')) {
            $msg_error = $this->handleUpdateSlug();
        }

        if ($id !== 'new' && class_exists('Slugs')) {
            $datos = Slugs::getById($id);
            if (class_exists('Metas') && $datos) {
                Metas::$title = "Slug: $datos->slug";
            }
        }

        $data = [
            'datos' => $datos,
            'slugsPages' => class_exists('Slugs') ? Slugs::getPagesFromSlugs() : [],
            'languages' => class_exists('Idiomas') ? Idiomas::getLanguages() : [],
        ];

        if (class_exists('Render')) {
            Render::adminPage('slug_admin', $data);
        }

        $this->setRendered(true);
    }

    /**
     * Maneja la actualización de un slug
     *
     * @return int Número de errores
     */
    protected function handleUpdateSlug()
    {
        if (!class_exists('Tools') || !class_exists('Slugs') || !class_exists('Bd')) {
            return 1;
        }

        $id = Tools::getValue('id');
        $slug = Tools::getValue('slug');
        $mod_id = Tools::getValue('page');
        $id_language = Tools::getValue('id_language');
        $title = Tools::getValue('title');
        $description = Tools::getValue('description');
        $keywords = Tools::getValue('keywords');
        $status = Tools::getValue('status', 'active');
        $msg_error = 0;

        // Validaciones
        if (empty($slug)) {
            Tools::registerAlert("Debes indicar el slug.");
            $msg_error++;
        } elseif (empty($mod_id)) {
            Tools::registerAlert("Selecciona la página a la que pertenece el slug.");
            $msg_error++;
        } elseif (empty($id_language)) {
            Tools::registerAlert("Debes seleccionar el idioma al que pertenece este slug.");
            $msg_error++;
        } elseif (empty($title)) {
            Tools::registerAlert("Indica el title del slug, este aparecerá en la pestaña del navegador y ayuda a nivel SEO.");
            $msg_error++;
        } else {
            $slug = Tools::urlAmigable($slug);
            $pageName = str_replace("-", " ", $mod_id);
            $pageName = ucfirst($pageName);

            if (!Slugs::checkIfSlugIsAvailable($slug, $id_language, $id)) {
                Tools::registerAlert("El slug indicado <strong>$pageName</strong> ya está siendo usado y no puede ser usado.");
                $msg_error++;
            } elseif (!Slugs::checkIfPageIsAvailableForLanguage($mod_id, $id_language, $id)) {
                Tools::registerAlert("La página seleccionada <strong>$pageName</strong> ya está siendo usado para este idioma.");
                $msg_error++;
            }
        }

        if ($msg_error == 0) {
            $data = [
                'id_language' => $id_language,
                'slug' => $slug,
                'title' => $title,
                'description' => $description,
                'keywords' => $keywords,
                'status' => $status,
                'update_date' => Tools::datetime()
            ];

            Bd::getInstance()->update('slugs', $data, "id = '$id'");
            Tools::registerAlert("Página actualizada correctamente", "success");
        }

        return $msg_error;
    }

    /**
     * Acción para gestión de usuarios admin
     *
     * @return void
     */
    public function usuariosAdminAction()
    {
        $this->requireAuth();

        if (class_exists('Tools') && defined('_ASSETS_') && defined('_ADMIN_')) {
            Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'footable/footable.bootstrap.min.css');
            Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'footable/footable.min.js');
        }

        $data = [
            'comienzo' => $this->comienzo,
            'pagina' => $this->pagina,
            'limite' => $this->limite
        ];

        if (class_exists('Render')) {
            Render::adminPage('usuarios_admin', $data);
        }

        $this->setRendered(true);
    }

    /**
     * Acción para administrar un usuario admin específico
     *
     * @return void
     */
    public function usuarioAdminAction()
    {
        $this->requireAuth();

        $usuarioId = class_exists('Tools') ? Tools::getValue('data') : '';
        $usuario = false;

        // Procesar formularios
        if (class_exists('Tools') && class_exists('Admin')) {
            if (Tools::getIsset('submitUpdateUsuarioAdmin')) {
                Admin::actualizarUsuario();
                Tools::registerAlert("El usuario ha sido modificado satisfactoriamente", "success");
            }

            if (Tools::getIsset('submitCrearUsuarioAdmin')) {
                Admin::crearUsuario();
                Tools::registerAlert("El usuario ha sido creado", "success");
            }
        }

        if (class_exists('Metas')) {
            Metas::$title = "Nuevo usuario";
        }

        if ($usuarioId !== 'new' && class_exists('Admin')) {
            $usuario = Admin::getUsuarioById($usuarioId);
            if (class_exists('Metas') && $usuario) {
                Metas::$title = "Usuario: $usuario->nombre";
            }
        }

        $data = [
            'usuario' => $usuario
        ];

        if (class_exists('Tools') && defined('_ASSETS_') && defined('_ADMIN_')) {
            Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'select2/css/select2.min.css');
            Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'select2/js/select2.min.js');
        }

        if (class_exists('Render')) {
            Render::adminPage('usuario_admin', $data);
        }

        $this->setRendered(true);
    }

    /**
     * Acción para gestión de mascotas
     *
     * @return void
     */
    public function mascotasAction()
    {
        $this->requireAuth();

        $data = [
            'comienzo' => $this->comienzo,
            'pagina' => $this->pagina,
            'limite' => $this->limite
        ];

        if (class_exists('Render')) {
            Render::adminPage('mascotas', $data);
        }

        $this->setRendered(true);
    }

    /**
     * Acción para administrar una mascota específica
     *
     * @return void
     */
    public function mascotaAction()
    {
        $this->requireAuth();

        if (!class_exists('Tools')) {
            $this->show404();
            return;
        }

        $requestData = Tools::getValue('data');
        if (!$requestData) {
            header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . 'mascotas/');
            exit;
        }

        $data = explode('-', $requestData);
        $idMascota = $data[1] ?? 0;

        $mascota = class_exists('Mascotas') ? Mascotas::getMascotaById($idMascota) : null;
        $mascotaCaracteristicas = class_exists('Caracteristicas') ? Caracteristicas::getCaracteristicasByMascota($idMascota) : [];
        $caracteristicas = class_exists('Caracteristicas') ? Caracteristicas::getCaracteristicas() : [];

        $data = [
            'mascota' => $mascota,
            'caracteristicas' => $caracteristicas,
            'mascotaCaracteristicas' => $mascotaCaracteristicas,
        ];

        if (class_exists('Render')) {
            Render::adminPage('mascota', $data);
        }

        $this->setRendered(true);
    }

    /**
     * Acción para crear una nueva mascota
     *
     * @return void
     */
    public function nuevaMascotaAction()
    {
        $this->requireAuth();

        // Verificar que el usuario sea un cuidador válido
        if (!isset($_SESSION['admin_panel']->cuidador_id) || $_SESSION['admin_panel']->cuidador_id == 0) {
            Tools::registerAlert("Solo los cuidadores pueden crear mascotas.", "error");
            header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
            exit;
        }

        // Procesar formulario de creación
        if (class_exists('Tools') && Tools::getIsset('submitCrearMascota')) {
            $this->handleCreateMascota();
        }

        // Obtener datos necesarios para el formulario
        $tipos = class_exists('Mascotas') ? $this->getTiposMascota() : [];
        $generos = class_exists('Mascotas') ? $this->getGenerosMascota() : [];
        $razas = class_exists('Razas') ? Razas::getRazas() : [];

        // Crear breadcrumb dinámico
        $breadcrumb = [
            [
                'title' => 'Inicio',
                'url' => _DOMINIO_ . $_SESSION['admin_vars']['entorno'],
                'icon' => 'fas fa-home'
            ],
            [
                'title' => 'Mascotas',
                'url' => _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . 'mascotas/',
                'icon' => 'fas fa-paw'
            ],
            [
                'title' => 'Nueva Mascota',
                'url' => '',
                'icon' => 'fas fa-plus',
                'active' => true
            ]
        ];

        $data = [
            'tipos' => $tipos,
            'generos' => $generos,
            'razas' => $razas,
            'breadcrumb' => $breadcrumb
        ];

        if (class_exists('Render')) {
            Render::$layout_data = array_merge(
                Render::$layout_data ?? [],
                ['breadcrumb' => $breadcrumb]
            );
        }

        if (class_exists('Metas')) {
            Metas::$title = "Nueva Mascota";
        }

        if (class_exists('Render')) {
            Render::adminPage('nueva-mascota', $data);
        }

        $this->setRendered(true);
    }

    /**
     * Maneja la creación de una nueva mascota
     *
     * @return void
     */
    protected function handleCreateMascota()
    {
        if (!class_exists('Tools') || !class_exists('Mascotas')) {
            Tools::registerAlert("Error interno: clases requeridas no encontradas.", "error");
            return;
        }

        // Inicializar array de errores
        $errors = [];

        // Validar campos requeridos
        $nombre         = Tools::getValue('nombre');
        $tipo           = Tools::getValue('tipo');
        $genero         = Tools::getValue('genero');
        $id_cuidador    = $_SESSION['admin_panel']->cuidador_id ?? 0;

        if (empty($nombre)) {
            $errors[] = "El nombre es obligatorio";
        }

        if (empty($tipo)) {
            $errors[] = "El tipo de mascota es obligatorio";
        }

        if (empty($genero)) {
            $errors[] = "El género es obligatorio";
        }

        if (!$id_cuidador || empty($id_cuidador) || $id_cuidador == 0) {
            $errors[] = "Solo los cuidadores pueden crear mascotas";
        }

        // Si hay errores, mostrarlos y salir
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Tools::registerAlert($error, "error");
            }
            return;
        }

        // Verificar si el método crearMascota existe
        if (!method_exists('Mascotas', 'crearMascota')) {
            Tools::registerAlert("Error interno: método crearMascota no encontrado.", "error");
            return;
        }

        try {
            // Preparar los datos para crear la mascota
            $datosCreacion = [
                'id_cuidador' => $id_cuidador,
                'nombre' => $nombre,
                'alias' => Tools::getValue('alias'),
                'tipo' => $tipo,
                'genero' => $genero,
                'raza' => Tools::getValue('raza'),
                'peso' => Tools::getValue('peso'),
                'nacimiento_fecha' => Tools::getValue('nacimiento_fecha'),
                'edad' => Tools::getValue('edad'),
                'edad_fecha' => Tools::getValue('edad_fecha'),
                'esterilizado' => Tools::getValue('esterilizado'),
                'ultimo_celo' => Tools::getValue('ultimo_celo'),
                'notas_internas' => Tools::getValue('notas_internas'),
                'observaciones' => Tools::getValue('observaciones')
            ];

            // Crear la mascota
            $mascotaId = Mascotas::crearMascota($datosCreacion);

            if ($mascotaId && is_numeric($mascotaId) && $mascotaId > 0) {
                // Limpiar cualquier alerta previa para evitar conflictos
                if (isset($_SESSION['alerts'])) {
                    unset($_SESSION['alerts']);
                }

                Tools::registerAlert("Mascota creada correctamente.", "success");

                // Redirigir a la página de edición de la mascota recién creada
                $adminPath = $_SESSION['admin_vars']['entorno'] ?? 'admin/';
                $redirectUrl = _DOMINIO_ . $adminPath . "mascota/mascota-{$mascotaId}/";

                header("Location: {$redirectUrl}");
                exit;
            } else {
                Tools::registerAlert("Error al crear la mascota. Inténtalo de nuevo.", "error");
            }

        } catch (Exception $e) {
            debug_log([
                'error' => 'Exception in handleCreateMascota',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 'CREATE_MASCOTA_EXCEPTION', 'admin');

            Tools::registerAlert("Error al crear la mascota: " . $e->getMessage(), "error");
        }
    }

    /**
     * Obtiene los tipos de mascota disponibles
     *
     * @return array
     */
    protected function getTiposMascota()
    {
        if (!class_exists('Bd')) {
            return [];
        }

        $db = Bd::getInstance();
        return $db->fetchAllSafe("SELECT * FROM mascotas_tipo ORDER BY nombre", [], PDO::FETCH_OBJ);
    }

    /**
     * Obtiene los géneros de mascota disponibles
     *
     * @return array
     */
    protected function getGenerosMascota()
    {
        if (!class_exists('Bd')) {
            return [];
        }

        $db = Bd::getInstance();
        return $db->fetchAllSafe("SELECT * FROM mascotas_genero ORDER BY nombre", [], PDO::FETCH_OBJ);
    }

    /**
     * Carga las traducciones para el panel de administración
     *
     * @return void
     */
    protected function loadTraduccionesAdmin()
    {
        if (!class_exists('Idiomas') || !class_exists('Traducciones')) {
            return;
        }

        if (!isset($_SESSION['admin_id_lang']) || empty($_SESSION['admin_id_lang'])) {
            $this->setAdminLanguage();
        }

        Traducciones::loadTraducciones($_SESSION['admin_id_lang']);
    }

    /**
     * Establece el idioma para el panel de administración
     *
     * @return void
     */
    protected function setAdminLanguage()
    {
        if (!class_exists('Idiomas')) {
            return;
        }

        $iso_code = defined('_DEFAULT_LANGUAGE_') ? _DEFAULT_LANGUAGE_ : 'es';

        // Detectar idioma del navegador si está disponible
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langNavegador = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $idiomasDisponibles = Idiomas::getLanguagesVisiblesArray();
            $iso_code = in_array($langNavegador, $idiomasDisponibles) ? $langNavegador : $iso_code;
        }

        $lang = Idiomas::getLangBySlug($iso_code);
        if (!empty($lang)) {
            $_SESSION['admin_id_lang'] = $lang->id;
        } else {
            debug_log("Invalid language: {$iso_code}", 'ERROR', 'admin');
        }
    }
}
