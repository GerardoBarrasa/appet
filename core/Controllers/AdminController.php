<?php

/**
 * Controlador de administración para el panel de control
 *
 * Este controlador maneja todas las rutas del panel de administración,
 * incluyendo autenticación, gestión de contenido y configuración del sistema.
 */
class AdminController extends Controllers
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
     * Ejecuta el controlador para la ruta solicitada
     *
     * @param string $page Página solicitada
     * @return void
     */
    public function execute($page)
    {
        // Verificar autenticación para páginas que no sean login
        if (!isset($_SESSION['admin_panel']) && $page != '') {
            header("Location: " . _DOMINIO_ . _ADMIN_);
            exit;
        }

        // Inicializar el controlador
        $this->initialize($page);

        // Definir rutas
        $this->defineRoutes();

        // Si no se encontró ninguna ruta, redirigir a 404
        if (!$this->getRendered()) {
            header('Location: ' . _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . '404/');
            exit;
        }
    }

    /**
     * Inicializa el controlador con la configuración necesaria
     *
     * @param string $page Página solicitada
     * @return void
     */
    protected function initialize($page)
    {
        // Obtener y validar entorno
        Admin::getEntorno();
        Admin::validateUser();

        // Configurar layout
        Render::$layout = $this->config['layout'];

        // Registrar assets
        $this->registerAssets();

        // Configurar datos del layout
        Render::$layout_data = [
            'idiomas' => Idiomas::getLanguagesAdminForm()
        ];

        // Inicializar paginación
        $this->initializePagination();
    }

    /**
     * Registra los assets CSS y JS necesarios
     *
     * @return void
     */
    protected function registerAssets()
    {
        // CSS
        foreach ($this->config['assets']['css'] as $css) {
            Tools::registerStylesheet($css);
        }

        Tools::registerStylesheet(_ASSETS_ . _COMMON_ . 'bootstrap-5.3.3-dist/css/bootstrap.min.css');
        Tools::registerStylesheet(_ASSETS_ . _COMMON_ . 'bootstrap-slider/css/bootstrap-slider.min.css');
        Tools::registerStylesheet(_ASSETS_ . _COMMON_ . 'toastr/toastr.min.css');
        Tools::registerStylesheet(_ASSETS_ . _COMMON_ . 'fontawesome-free-6.6.0-web/css/all.css');
        Tools::registerStylesheet(_RESOURCES_ . _ADMIN_ . 'css/adminlte.min.css');
        Tools::registerStylesheet(_RESOURCES_ . _ADMIN_ . 'css/style-admin.css?v=' . time());

        // JS
        Tools::registerJavascript(_ASSETS_ . _COMMON_ . 'jquery-3.7.1.min.js', 'top');
        Tools::registerJavascript(_ASSETS_ . _COMMON_ . 'bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js');
        Tools::registerJavascript(_ASSETS_ . _COMMON_ . 'bootstrap-slider/bootstrap-slider.min.js');
        Tools::registerJavascript(_ASSETS_ . _COMMON_ . 'underscore.js');
        Tools::registerJavascript(_ASSETS_ . _COMMON_ . 'toastar/toastr.min.js');
        Tools::registerJavascript(_RESOURCES_ . _ADMIN_ . 'js/adminlte.min.js');
        Tools::registerJavascript(_RESOURCES_ . _ADMIN_ . 'js/custom.js?v=' . time(), 'top');
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
     * Define las rutas disponibles en el controlador
     *
     * @return void
     */
    protected function defineRoutes()
    {
        // Autenticación
        $this->add('', [$this, 'loginAction']);
        $this->add('logout', [$this, 'logoutAction']);

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

        // Página 404
        $this->add('404', [$this, 'notFoundAction']);
    }

    /**
     * Verifica si el usuario está autenticado
     *
     * @return bool
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['admin_panel']);
    }

    /**
     * Redirige al login si no está autenticado
     *
     * @return void
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
            exit;
        }
    }

    /**
     * Verifica si el usuario es super admin
     *
     * @return bool
     */
    protected function isSuperAdmin()
    {
        return $this->isAuthenticated() && empty($_SESSION['admin_panel']->id_country);
    }

    /**
     * Requiere permisos de super admin
     *
     * @return void
     */
    protected function requireSuperAdmin()
    {
        if (!$this->isSuperAdmin()) {
            header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
            exit;
        }
    }

    // ==========================================
    // ACCIONES DEL CONTROLADOR
    // ==========================================

    /**
     * Acción de login/dashboard principal
     *
     * @return void
     */
    public function loginAction()
    {
        if (!$this->isAuthenticated()) {
            $this->handleLogin();
        } else {
            $this->showDashboard();
        }
    }

    /**
     * Maneja el proceso de login
     *
     * @return void
     */
    protected function handleLogin()
    {
        $mensajeError = $_SESSION['actions_mensajeError'] ?? '';
        unset($_SESSION['actions_mensajeError']);

        Render::$layout = 'actions';

        // Procesar formulario de login
        if (isset($_REQUEST['btn-login'])) {
            $usuario = Tools::getValue('usuario');
            $password = Tools::md5(Tools::getValue('password'));

            if (Admin::login($usuario, $password)) {
                header('Location:' . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
                exit;
            } else {
                $mensajeError = "Usuario y/o contrase&ntilde;a incorrectos.";
            }
        }

        $data = [
            'mensajeError' => $mensajeError,
        ];

        Metas::$title = "&iexcl;Con&eacute;ctate!";
        Render::adminPage('login', $data);
    }

    /**
     * Muestra el dashboard principal
     *
     * @return void
     */
    protected function showDashboard()
    {
        Metas::$title = "Inicio";
        Render::adminPage('home');
    }

    /**
     * Acción de logout
     *
     * @return void
     */
    public function logoutAction()
    {
        Tools::logError('LOGOUT');
        Render::$layout = false;
        $dest = $_SESSION['admin_vars']['entorno'];
        Admin::logout();
        header("Location: " . _DOMINIO_ . $dest);
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

        Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'footable/footable.bootstrap.min.css');
        Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'footable/footable.min.js');

        $data = [
            'comienzo' => $this->comienzo,
            'pagina' => $this->pagina,
            'limite' => $this->limite
        ];

        Render::adminPage('idiomas', $data);
    }

    /**
     * Acción para administrar un idioma específico
     *
     * @return void
     */
    public function administrarIdiomaAction()
    {
        $this->requireAuth();

        $id = Tools::getValue('data');
        if (!$id) {
            header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . 'idiomas/');
            exit;
        }

        // Procesar formulario
        if (isset($_REQUEST['action'])) {
            $result = Idiomas::administrarIdioma();

            if ($result == 'ok') {
                if ($id == '0') {
                    Tools::registerAlert("Idioma creado correctamente.", "success");
                    header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . 'idiomas/');
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

        Render::adminPage('administrar-idioma', $data);
    }

    /**
     * Acción para gestión de traducciones
     *
     * @return void
     */
    public function traduccionesAction()
    {
        $this->requireAuth();

        Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'footable/footable.bootstrap.min.css');
        Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'footable/footable.min.js');

        $idiomas = Idiomas::getLanguages();
        $porcentajeTraduccionesPorIdioma = [];

        foreach ($idiomas as $idioma) {
            $porcentajeTraduccionesPorIdioma[$idioma->id] = Traducciones::getStatsTraduccionesByIdioma($idioma->id);
        }

        $data = [
            'porcentajeTraduccionesPorIdioma' => $porcentajeTraduccionesPorIdioma,
            'comienzo' => $this->comienzo,
            'pagina' => $this->pagina,
            'limite' => $this->limite,
        ];

        Render::adminPage('traducciones', $data);
    }

    /**
     * Acción para editar una traducción específica
     *
     * @return void
     */
    public function traduccionAction()
    {
        $this->requireAuth();

        Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'select2/css/select2.min.css');
        Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'select2/js/select2.min.js');

        $traduccionId = Tools::getValue('data');
        $traduccion = false;

        // Procesar actualización
        if (Tools::getIsset('submitUpdateTraduccion')) {
            $this->handleUpdateTraduccion();
        }

        // Procesar creación
        if (Tools::getIsset('submitCrearTraduccion')) {
            $this->handleCreateTraduccion();
        }

        Metas::$title = "Editando traducción";
        if ($traduccionId !== 'new') {
            $traduccion = Traducciones::getTraduccionById($traduccionId);
        }

        $data = [
            'traduccion' => $traduccion
        ];

        Render::adminPage('traduccion', $data);
    }

    /**
     * Maneja la actualización de una traducción
     *
     * @return void
     */
    protected function handleUpdateTraduccion()
    {
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

        $idiomas = Idiomas::getLanguages();
        foreach ($idiomas as $idioma) {
            Traducciones::regenerarCacheTraduccionesByIdioma($idioma->id, _PATH_ . 'translations/' . $idioma->slug . '.php');
        }

        Render::$layout = false;
        Tools::registerAlert("Caché de traducciones regenerada correctamente.", "success");
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

        Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'select2/css/select2.min.css');
        Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'select2/js/select2.min.js');
        Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'footable/footable.bootstrap.min.css');
        Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'footable/footable.min.js');

        $data = [
            'comienzo' => $this->comienzo,
            'pagina' => $this->pagina,
            'limite' => $this->limite,
            'languages' => Idiomas::getLanguages(),
            'slugsPages' => Slugs::getPagesFromSlugs(),
            'languageDefault' => Idiomas::getDefaultLanguage()
        ];

        Metas::$title = "Páginas meta";
        Render::adminPage('slugs_admin', $data);
    }

    /**
     * Acción para administrar un slug específico
     *
     * @return void
     */
    public function administrarSlugAction()
    {
        $this->requireSuperAdmin();

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

        if ($id !== 'new') {
            $datos = Slugs::getById($id);
            Metas::$title = "Slug: $datos->slug";
        }

        $data = [
            'datos' => $datos,
            'slugsPages' => Slugs::getPagesFromSlugs(),
            'languages' => Idiomas::getLanguages(),
        ];

        Render::adminPage('slug_admin', $data);
    }

    /**
     * Maneja la actualización de un slug
     *
     * @return int Número de errores
     */
    protected function handleUpdateSlug()
    {
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

        Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'footable/footable.bootstrap.min.css');
        Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'footable/footable.min.js');

        $data = [
            'comienzo' => $this->comienzo,
            'pagina' => $this->pagina,
            'limite' => $this->limite
        ];

        Render::adminPage('usuarios_admin', $data);
    }

    /**
     * Acción para administrar un usuario admin específico
     *
     * @return void
     */
    public function usuarioAdminAction()
    {
        $this->requireAuth();

        $usuarioId = Tools::getValue('data');
        $usuario = false;

        // Procesar formularios
        if (Tools::getIsset('submitUpdateUsuarioAdmin')) {
            Admin::actualizarUsuario();
            Tools::registerAlert("El usuario ha sido modificado satisfactoriamente", "success");
        }

        if (Tools::getIsset('submitCrearUsuarioAdmin')) {
            Admin::crearUsuario();
            Tools::registerAlert("El usuario ha sido creado", "success");
        }

        Metas::$title = "Nuevo usuario";
        if ($usuarioId !== 'new') {
            $usuario = Admin::getUsuarioById($usuarioId);
            Metas::$title = "Usuario: $usuario->nombre";
        }

        $data = [
            'usuario' => $usuario
        ];

        Tools::registerStylesheet(_ASSETS_ . _ADMIN_ . 'select2/css/select2.min.css');
        Tools::registerJavascript(_ASSETS_ . _ADMIN_ . 'select2/js/select2.min.js');
        Render::adminPage('usuario_admin', $data);
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

        Render::adminPage('mascotas', $data);
    }

    /**
     * Acción para administrar una mascota específica
     *
     * @return void
     */
    public function mascotaAction()
    {
        $this->requireAuth();

        $requestData = Tools::getValue('data');
        if (!$requestData) {
            header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . 'mascotas/');
            exit;
        }

        $data = explode('-', $requestData);
        $idMascota = $data[1] ?? 0;

        $mascota = Mascotas::getMascotaById($idMascota);
        $mascotaCaracteristicas = Caracteristicas::getCaracteristicasByMascota($idMascota);
        $caracteristicas = Caracteristicas::getCaracteristicas();

        $data = [
            'mascota' => $mascota,
            'caracteristicas' => $caracteristicas,
            'mascotaCaracteristicas' => $mascotaCaracteristicas,
        ];

        Render::adminPage('mascota', $data);
    }

    /**
     * Acción para página 404
     *
     * @return void
     */
    public function notFoundAction()
    {
        http_response_code(404);
        Render::adminPage('404');
    }

    /**
     * Carga las traducciones para el panel de administración
     *
     * @return void
     */
    protected function loadTraducciones()
    {
        $this->loadTraduccionesAdmin();
    }
}
