<?php

/**
 * Clase base para todos los controladores del sistema
 *
 * Esta clase proporciona la funcionalidad común que comparten todos los controladores,
 * incluyendo el sistema de rutas, manejo de idiomas, autenticación y renderizado.
 */
class Controllers
{
    /**
     * Página actual que se está procesando
     * @var string
     */
    private $page;

    /**
     * Indica si se ha renderizado alguna ruta
     * @var bool
     */
    private $rendered = false;

    /**
     * Controlador por defecto del sistema
     * @var string
     */
    var $defaultController = 'default';

    /**
     * Configuración base del controlador
     * @var array
     */
    protected $config = [
        'csrf_protection' => true,
        'multi_language' => true,
        'auto_redirect' => true
    ];

    /**
     * Rutas registradas en el controlador
     * @var array
     */
    protected $routes = [];

    /**
     * Middleware registrado
     * @var array
     */
    protected static $globalMiddleware = [];

    /**
     * Middleware específico de la instancia
     * @var array
     */
    protected $instanceMiddleware = [];

    /**
     * Carga y ejecuta el controlador apropiado
     *
     * @return void
     */
    public function load()
    {
        // Limpiar assets de sesiones anteriores
        $this->clearPreviousAssets();

        // Obtener controlador y página
        $controller = $this->getControllerName();
        $page = $this->getPageName();

        // Validar y configurar controlador
        $controller = $this->validateController($controller, $page);

        // Manejar protección CSRF para controladores AJAX
        $this->handleCSRFProtection($controller);

        // Manejar redirecciones de idioma para controlador default
        if ($controller == 'default') {
            $this->handleLanguageRedirection();
        }

        // Generar token de sesión si no existe
        $this->ensureSessionToken($controller);

        // Procesar página personalizada (slugs)
        $page = $this->processCustomPage($controller, $page);

        // Ejecutar controlador
        $this->executeController($controller, $page);
    }

    /**
     * Limpia los assets de sesiones anteriores
     *
     * @return void
     */
    protected function clearPreviousAssets()
    {
        unset($_SESSION['js_paths']);
        unset($_SESSION['css_paths']);
    }

    /**
     * Obtiene el nombre del controlador desde la URL
     *
     * @return string
     */
    protected function getControllerName()
    {
        return Tools::getValue('controller', $this->defaultController);
    }

    /**
     * Obtiene el nombre de la página desde la URL
     *
     * @return string
     */
    protected function getPageName()
    {
        return isset($_GET['mod']) ? Tools::getValue('mod') : '';
    }

    /**
     * Valida que el controlador exista, si no usa el default
     *
     * @param string $controller Nombre del controlador
     * @param string &$page Referencia a la página (se modifica si es necesario)
     * @return string Controlador validado
     */
    protected function validateController($controller, &$page)
    {
        $controllerClass = ucfirst($controller) . 'Controller';

        if (!class_exists($controllerClass)) {
            $controller = 'default';
            $page = '404';
        }

        return $controller;
    }

    /**
     * Maneja la protección CSRF para controladores AJAX
     *
     * @param string $controller Nombre del controlador
     * @return void
     */
    protected function handleCSRFProtection($controller)
    {
        if (!$this->config['csrf_protection']) {
            return;
        }

        $isAjaxController = in_array($controller, ['ajax', 'adminajax']);
        $isPostRequest = $_SERVER['REQUEST_METHOD'] == 'POST';
        $hasValidToken = !empty($_SESSION['token']) && $_SESSION['token'] == Tools::getValue('token');

        if ($isAjaxController && $isPostRequest && !$hasValidToken) {
            http_response_code(403);
            $this->sendJsonError('Token CSRF inválido', 403);
            exit;
        }
    }

    /**
     * Maneja las redirecciones de idioma para el controlador default
     *
     * @return void
     */
    protected function handleLanguageRedirection()
    {
        if (!$this->config['multi_language'] || !_MULTI_LANGUAGE_) {
            return;
        }

        $this->processLanguageSession();
    }

    /**
     * Procesa la sesión de idioma
     *
     * @return void
     */
    protected function processLanguageSession()
    {
        if (isset($_SESSION['lang'])) {
            $lang = Tools::getValue('lang');
            $language = Idiomas::getLangBySlug($lang);

            if ($language) {
                $_SESSION['id_lang'] = $language->id;
                $_SESSION['lang'] = $lang;
            } else {
                $this->setDefaultLanguage();
            }
        } else {
            $this->setDefaultLanguage();
        }
    }

    /**
     * Establece el idioma por defecto
     *
     * @return void
     */
    protected function setDefaultLanguage()
    {
        $defaultLang = Idiomas::getDefaultLanguage();
        $_SESSION['id_lang'] = $defaultLang->id;
        $_SESSION['lang'] = $defaultLang->slug;
    }

    /**
     * Asegura que exista un token de sesión
     *
     * @param string $controller Nombre del controlador
     * @return void
     */
    protected function ensureSessionToken($controller)
    {
        if (in_array($controller, ['default', 'admin']) && empty($_SESSION['token'])) {
            $_SESSION['token'] = Tools::passwdGen(32);
        }
    }

    /**
     * Procesa páginas personalizadas usando el sistema de slugs
     *
     * @param string $controller Nombre del controlador
     * @param string $page Página solicitada
     * @return string Página procesada
     */
    protected function processCustomPage($controller, $page)
    {
        if (!in_array($controller, ['default', 'admin']) || empty($page)) {
            return $page;
        }

        // Buscar el slug en la base de datos
        $data_slug = Slugs::getModBySlug($page, $controller);

        if (!empty($data_slug) && isset($data_slug->mod_id) && $data_slug->mod_id != '') {
            return $data_slug->mod_id;
        }

        return '404';
    }

    /**
     * Ejecuta el controlador especificado
     *
     * @param string $controller Nombre del controlador
     * @param string $page Página a procesar
     * @return void
     */
    protected function executeController($controller, $page)
    {
        $controllerClass = ucfirst($controller) . 'Controller';
        $currentController = new $controllerClass();

        $currentController->setPage($page);

        // Cargar traducciones si está habilitado el multiidioma
        if (_MULTI_LANGUAGE_) {
            $currentController->loadTraducciones();
        }

        // Ejecutar middleware antes del controlador
        $this->executeMiddleware('before', $currentController);

        // Ejecutar el controlador
        $currentController->execute($page);

        // Ejecutar middleware después del controlador
        $this->executeMiddleware('after', $currentController);
    }

    /**
     * Ejecuta el middleware registrado
     *
     * @param string $timing Momento de ejecución ('before' o 'after')
     * @param Controllers $controller Instancia del controlador
     * @return void
     */
    protected function executeMiddleware($timing, $controller)
    {
        // Ejecutar middleware global
        if (isset(self::$globalMiddleware[$timing])) {
            foreach (self::$globalMiddleware[$timing] as $middleware) {
                if (is_callable($middleware)) {
                    call_user_func($middleware, $controller);
                }
            }
        }

        // Ejecutar middleware de la instancia
        if (isset($this->instanceMiddleware[$timing])) {
            foreach ($this->instanceMiddleware[$timing] as $middleware) {
                if (is_callable($middleware)) {
                    call_user_func($middleware, $controller);
                }
            }
        }
    }

    /**
     * Registra middleware global para todos los controladores
     *
     * @param string $timing Momento de ejecución ('before' o 'after')
     * @param callable $callback Función a ejecutar
     * @return void
     */
    public static function registerGlobalMiddleware($timing, $callback)
    {
        if (!isset(self::$globalMiddleware[$timing])) {
            self::$globalMiddleware[$timing] = [];
        }

        self::$globalMiddleware[$timing][] = $callback;
    }

    /**
     * Registra middleware para esta instancia del controlador
     *
     * @param string $timing Momento de ejecución ('before' o 'after')
     * @param callable $callback Función a ejecutar
     * @return void
     */
    public function registerMiddleware($timing, $callback)
    {
        if (!isset($this->instanceMiddleware[$timing])) {
            $this->instanceMiddleware[$timing] = [];
        }

        $this->instanceMiddleware[$timing][] = $callback;
    }

    /**
     * Envía una respuesta JSON de error
     *
     * @param string $message Mensaje de error
     * @param int $code Código HTTP
     * @return void
     */
    protected function sendJsonError($message, $code = 400)
    {
        header('Content-Type: application/json');
        header("HTTP/1.1 $code Error");

        echo json_encode([
            'type' => 'error',
            'error' => $message,
            'code' => $code
        ]);
    }

    // ==========================================
    // MÉTODOS PARA CONTROLADORES HIJOS
    // ==========================================

    /**
     * Establece la página actual
     *
     * @param string $value Nombre de la página
     * @return void
     */
    protected function setPage($value)
    {
        $this->page = $value;
    }

    /**
     * Obtiene la página actual
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Establece si se ha renderizado una ruta
     *
     * @param bool $value Estado del renderizado
     * @return void
     */
    protected function setRendered($value)
    {
        $this->rendered = $value;
    }

    /**
     * Verifica si se ha renderizado alguna ruta
     *
     * @return bool
     */
    protected function getRendered()
    {
        return $this->rendered;
    }

    /**
     * Registra una ruta en el controlador
     *
     * @param string $route Ruta a registrar
     * @param callable|array $callback Función o método a ejecutar
     * @return mixed|null Resultado de la ejecución si coincide la ruta, null en caso contrario
     */
    protected function add($route, $callback)
    {
        // Registrar la ruta
        $this->routes[$route] = $callback;

        // Si la ruta coincide con la página actual, ejecutarla
        if ($route == $this->getPage()) {
            $this->setRendered(true);

            // Ejecutar el callback
            if (is_callable($callback)) {
                return call_user_func($callback);
            } elseif (is_array($callback) && count($callback) == 2) {
                return call_user_func_array($callback, []);
            }
        }

        // Retornar null si no se ejecutó la ruta
        return null;
    }

    /**
     * Obtiene todas las rutas registradas
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Verifica si una ruta está registrada
     *
     * @param string $route Ruta a verificar
     * @return bool
     */
    public function hasRoute($route)
    {
        return isset($this->routes[$route]);
    }

    /**
     * Ejecuta una ruta específica
     *
     * @param string $route Ruta a ejecutar
     * @param array $params Parámetros adicionales
     * @return mixed Resultado de la ejecución
     */
    public function executeRoute($route, $params = [])
    {
        if (!$this->hasRoute($route)) {
            return false;
        }

        $callback = $this->routes[$route];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        } elseif (is_array($callback) && count($callback) == 2) {
            return call_user_func_array($callback, $params);
        }

        return false;
    }

    // ==========================================
    // MÉTODOS DE TRADUCCIONES
    // ==========================================

    /**
     * Carga las traducciones para el frontend
     *
     * @return void
     */
    protected function loadTraducciones()
    {
        if (isset($_SESSION['id_lang'])) {
            Traducciones::loadTraducciones($_SESSION['id_lang']);
        }
    }

    /**
     * Carga las traducciones para el panel de administración
     *
     * @return void
     */
    protected function loadTraduccionesAdmin()
    {
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
        $iso_code = _DEFAULT_LANGUAGE_;

        // Detectar idioma del navegador si está disponible
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langNavegador = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $idiomasDisponibles = Idiomas::getLanguagesVisiblesArray();
            $iso_code = in_array($langNavegador, $idiomasDisponibles) ? $langNavegador : _DEFAULT_LANGUAGE_;
        }

        $lang = Idiomas::getLangBySlug($iso_code);
        if (!empty($lang)) {
            $_SESSION['admin_id_lang'] = $lang->id;
        } else {
            die('Idioma inválido.');
        }
    }

    // ==========================================
    // MÉTODOS PÚBLICOS PARA MIDDLEWARE
    // ==========================================

    /**
     * Obtiene la IP del cliente (público para middleware)
     *
     * @return string
     */
    public function getClientIP()
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    /**
     * Log de actividad del controlador (público para middleware)
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

        Tools::logError($logMessage, 0, 'controllers');
    }

    // ==========================================
    // MÉTODOS DE UTILIDAD
    // ==========================================

    /**
     * Redirige a una URL específica
     *
     * @param string $url URL de destino
     * @param int $code Código HTTP de redirección
     * @return void
     */
    protected function redirect($url, $code = 302)
    {
        http_response_code($code);
        header("Location: $url");
        exit;
    }

    /**
     * Redirige a una ruta del mismo controlador
     *
     * @param string $route Ruta de destino
     * @param array $params Parámetros adicionales
     * @return void
     */
    protected function redirectToRoute($route, $params = [])
    {
        $url = $this->generateUrl($route, $params);
        $this->redirect($url);
    }

    /**
     * Genera una URL para una ruta específica
     *
     * @param string $route Ruta
     * @param array $params Parámetros
     * @return string URL generada
     */
    protected function generateUrl($route, $params = [])
    {
        $url = _DOMINIO_;

        // Añadir idioma si está configurado
        if (_MULTI_LANGUAGE_ && isset($_SESSION['lang'])) {
            $url .= $_SESSION['lang'] . '/';
        }

        // Añadir ruta
        if (!empty($route)) {
            $url .= $route . '/';
        }

        // Añadir parámetros como query string
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * Verifica si la petición es AJAX
     *
     * @return bool
     */
    protected function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Verifica si la petición es POST
     *
     * @return bool
     */
    protected function isPostRequest()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verifica si la petición es GET
     *
     * @return bool
     */
    protected function isGetRequest()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}
