<?php

/**
 * Controlador principal del sistema de routing
 *
 * Maneja el enrutamiento de todas las peticiones y la carga de controladores específicos
 */
class Controllers
{
    /**
     * Middleware registrado globalmente
     */
    protected static $globalMiddleware = [
        'before' => [],
        'after' => []
    ];

    /**
     * Rutas registradas
     */
    protected $routes = [];

    /**
     * Indica si se ha renderizado alguna ruta
     */
    protected $rendered = false;

    /**
     * Controlador actual
     */
    protected $currentController = null;

    /**
     * Registra middleware global
     *
     * @param string $type Tipo de middleware (before/after)
     * @param callable $middleware Función o array [clase, método]
     * @return void
     */
    public static function registerGlobalMiddleware($type, $middleware)
    {
        if (!isset(self::$globalMiddleware[$type])) {
            self::$globalMiddleware[$type] = [];
        }

        self::$globalMiddleware[$type][] = $middleware;
    }

    /**
     * Ejecuta middleware
     *
     * @param string $type Tipo de middleware
     * @param mixed $controller Controlador actual
     * @return void
     */
    protected function executeMiddleware($type, $controller = null)
    {
        if (!isset(self::$globalMiddleware[$type])) {
            return;
        }

        // Log de middleware que se va a ejecutar
        debug_log([
            'middleware_type' => $type,
            'middleware_count' => count(self::$globalMiddleware[$type]),
            'controller' => is_object($controller) ? get_class($controller) : 'null',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
        ], 'MIDDLEWARE_EXECUTION', 'middleware');

        foreach (self::$globalMiddleware[$type] as $index => $middleware) {
            try {
                // Log del middleware específico que se va a ejecutar
                $middlewareName = is_array($middleware) ? $middleware[0] : 'callable';
                debug_log([
                    'middleware_index' => $index,
                    'middleware_name' => $middlewareName,
                    'type' => $type
                ], 'MIDDLEWARE_BEFORE_EXECUTION', 'middleware');

                if (is_callable($middleware)) {
                    if (is_array($middleware)) {
                        call_user_func($middleware, $controller);
                    } else {
                        $middleware($controller);
                    }
                }

                debug_log([
                    'middleware_index' => $index,
                    'middleware_name' => $middlewareName,
                    'status' => 'completed'
                ], 'MIDDLEWARE_AFTER_EXECUTION', 'middleware');

            } catch (Exception $e) {
                debug_log([
                    'middleware_index' => $index,
                    'middleware_name' => $middlewareName ?? 'unknown',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 'MIDDLEWARE_ERROR', 'middleware');

                __log_error("Error en middleware {$type}: " . $e->getMessage(), 3, 'middleware_errors');
            }
        }
    }

    /**
     * Carga y ejecuta el controlador apropiado
     *
     * @return void
     */
    public function load()
    {
        try {
            // Obtener la URL solicitada
            $requestUri = $this->getRequestUri();
            $segments = $this->parseUri($requestUri);

            // Determinar el controlador y la página
            $controllerInfo = $this->determineController($segments);
            $controllerName = $controllerInfo['controller'];
            $page = $controllerInfo['page'];

            // Log de la petición
            debug_log([
                'request_uri' => $requestUri,
                'segments' => $segments,
                'controller' => $controllerName,
                'page' => $page,
                'session_admin' => isset($_SESSION['admin_panel']) ? 'yes' : 'no',
                'redirect_to_admin' => defined('_REDIRECT_TO_ADMIN_') ? (_REDIRECT_TO_ADMIN_ ? 'yes' : 'no') : 'undefined',
                'middleware_before_count' => count(self::$globalMiddleware['before'] ?? [])
            ], 'ROUTING', 'routing');

            // Crear instancia del controlador
            if (class_exists($controllerName)) {
                $this->currentController = new $controllerName();

                // Establecer la página en el controlador
                if (method_exists($this->currentController, 'setPage')) {
                    $this->currentController->setPage($page);
                }

                $this->executeMiddleware('before', $this->currentController);

                // Log antes de ejecutar el controlador
                debug_log([
                    'controller' => $controllerName,
                    'page' => $page,
                    'about_to_execute' => true
                ], 'CONTROLLER_EXECUTION', 'routing');

                // Ejecutar el controlador
                $this->currentController->execute($page);

                // Log después de ejecutar el controlador
                debug_log([
                    'controller' => $controllerName,
                    'page' => $page,
                    'execution_completed' => true
                ], 'CONTROLLER_EXECUTION', 'routing');

                // Ejecutar middleware after
                $this->executeMiddleware('after', $this->currentController);

            } else {
                throw new Exception("Controlador no encontrado: {$controllerName}");
            }

        } catch (Exception $e) {
            debug_log([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
            ], 'ROUTING_ERROR', 'routing');

            __log_error("Error en Controllers::load(): " . $e->getMessage(), 3, 'routing_errors');
            $this->handleError($e);
        }
    }

    /**
     * Lista todos los middleware registrados
     *
     * @return array
     */
    public static function getRegisteredMiddleware()
    {
        return self::$globalMiddleware;
    }

    /**
     * Limpia todos los middleware registrados
     *
     * @param string $type Tipo específico o null para todos
     * @return void
     */
    public static function clearMiddleware($type = null)
    {
        if ($type === null) {
            self::$globalMiddleware = ['before' => [], 'after' => []];
        } else {
            self::$globalMiddleware[$type] = [];
        }

        debug_log([
            'action' => 'middleware_cleared',
            'type' => $type ?? 'all'
        ], 'MIDDLEWARE_MANAGEMENT', 'middleware');
    }

    /**
     * Obtiene la URI de la petición
     *
     * @return string
     */
    protected function getRequestUri()
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remover query string
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        // Normalizar trailing slash
        if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
            $requestUri = rtrim($requestUri, '/');
        }

        return $requestUri;
    }

    /**
     * Parsea la URI en segmentos
     *
     * @param string $uri
     * @return array
     */
    protected function parseUri($uri)
    {
        // Remover el directorio base si existe
        if (defined('_BASE_PATH_') && _BASE_PATH_ !== '/') {
            $basePath = rtrim(_BASE_PATH_, '/');
            if (strpos($uri, $basePath) === 0) {
                $uri = substr($uri, strlen($basePath));
            }
        }

        // Limpiar y dividir
        $uri = trim($uri, '/');
        return $uri === '' ? [] : explode('/', $uri);
    }

    /**
     * Determina qué controlador usar basado en la URI
     *
     * @param array $segments
     * @return array
     */
    protected function determineController($segments)
    {
        // Si no hay segmentos, determinar controlador por defecto
        if (empty($segments)) {
            return $this->getDefaultController();
        }

        $firstSegment = $segments[0];

        // Verificar si es una ruta de administración
        if ($this->isAdminRoute($firstSegment)) {
            return [
                'controller' => 'AdminController',
                'page' => isset($segments[1]) ? $segments[1] : ''
            ];
        }

        // Verificar si es una ruta de tipo appet-*
        if (!empty($firstSegment) && strpos($firstSegment, 'appet-') === 0) {
            debug_log([
                'special_route' => 'appet',
                'segment' => $firstSegment,
                'userslug' => substr($firstSegment, 6) // Extraer el userslug (después de "appet-")
            ], 'ROUTING_APPET', 'routing');

            // Guardar el userslug en $_REQUEST para que esté disponible en el controlador
            $_REQUEST['userslug'] = substr($firstSegment, 6);

            // Si hay un segundo segmento, es el mod
            if (isset($segments[1])) {
                $_REQUEST['mod'] = $segments[1];
            }

            // Si hay un tercer segmento, es data
            if (isset($segments[2])) {
                $_REQUEST['data'] = $segments[2];
            }

            // Si hay un cuarto segmento, es data2
            if (isset($segments[3])) {
                $_REQUEST['data2'] = $segments[3];
            }

            return [
                'controller' => 'AdminController',
                'page' => isset($segments[1]) ? $segments[1] : ''
            ];
        }

        // Verificar rutas especiales
        $specialRoutes = [
            'api' => 'ApiController',
            'ajax' => 'AjaxController',
            'adminajax' => 'AdminajaxController',
            'debug' => 'DebugController',
            'crons' => 'CronsController'
        ];

        if (isset($specialRoutes[$firstSegment])) {
            return [
                'controller' => $specialRoutes[$firstSegment],
                'page' => isset($segments[1]) ? $segments[1] : ''
            ];
        }

        // Por defecto, usar DefaultController
        return [
            'controller' => 'DefaultController',
            'page' => $firstSegment
        ];
    }

    /**
     * Obtiene el controlador por defecto
     *
     * @return array
     */
    protected function getDefaultController()
    {
        // Si _REDIRECT_TO_ADMIN_ está habilitado, ir a admin
        if (defined('_REDIRECT_TO_ADMIN_') && _REDIRECT_TO_ADMIN_ === true) {
            return [
                'controller' => 'AdminController',
                'page' => ''
            ];
        }

        // Por defecto, usar DefaultController
        return [
            'controller' => 'DefaultController',
            'page' => ''
        ];
    }

    /**
     * Verifica si es una ruta de administración
     *
     * @param string $segment
     * @return bool
     */
    protected function isAdminRoute($segment)
    {
        $adminPaths = ['admin'];

        // Añadir _ADMIN_ si está definido
        if (defined('_ADMIN_') && _ADMIN_ !== '') {
            $adminPath = trim(_ADMIN_, '/');
            if (!empty($adminPath)) {
                $adminPaths[] = $adminPath;
            }
        }

        return in_array($segment, $adminPaths);
    }

    /**
     * Maneja errores del sistema de routing
     *
     * @param Exception $e
     * @return void
     */
    protected function handleError($e)
    {
        if (defined('_DEBUG_') && _DEBUG_) {
            echo "<h1>Error de Routing</h1>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            // En producción, mostrar página de error genérica
            http_response_code(500);
            echo "<h1>Error del Sistema</h1>";
            echo "<p>Ha ocurrido un error interno. Contacta con el administrador.</p>";
        }
    }

    /**
     * Añade una ruta al controlador actual
     *
     * @param string $route
     * @param callable $callback
     * @return void
     */
    public function add($route, $callback)
    {
        $this->routes[$route] = $callback;
    }

    /**
     * Ejecuta una ruta específica
     *
     * @param string $route
     * @return bool
     */
    public function executeRoute($route)
    {
        if (isset($this->routes[$route])) {
            try {
                call_user_func($this->routes[$route]);
                $this->rendered = true;
                return true;
            } catch (Exception $e) {
                __log_error("Error ejecutando ruta '{$route}': " . $e->getMessage(), 3, 'routing_errors');
                return false;
            }
        }

        return false;
    }

    /**
     * Verifica si se ha renderizado alguna ruta
     *
     * @return bool
     */
    public function getRendered()
    {
        return $this->rendered;
    }

    /**
     * Establece el estado de renderizado
     *
     * @param bool $rendered
     * @return void
     */
    public function setRendered($rendered = true)
    {
        $this->rendered = $rendered;
    }

    /**
     * Obtiene el controlador actual
     *
     * @return mixed
     */
    public function getCurrentController()
    {
        return $this->currentController;
    }

    /**
     * Log de actividad
     *
     * @param string $message
     * @param string $level
     * @return void
     */
    public function log($message, $level = 'info')
    {
        debug_log($message, strtoupper($level), 'controllers');
    }
}
