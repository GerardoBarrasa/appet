<?php

/**
 * Middleware de logging
 *
 * Registra todas las peticiones que llegan al sistema
 */
class LoggingMiddleware
{
    /**
     * Ejecuta el middleware de logging antes del controlador
     *
     * @param mixed $controller Instancia del controlador
     * @return void
     */
    public static function handleBefore($controller)
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => Tools::getClientIP($controller),
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
            'user_agent' => self::getUserAgent(),
            'controller' => get_class($controller),
            'page' => self::getPage($controller)
        ];

        $logMessage = sprintf(
            "REQUEST: %s %s from %s [%s] - Controller: %s, Page: %s",
            $logData['method'],
            $logData['uri'],
            $logData['ip'],
            $logData['user_agent'],
            $logData['controller'],
            $logData['page']
        );

        debug_log($logMessage, 'INFO', 'requests');
    }

    /**
     * Ejecuta el middleware de logging después del controlador
     *
     * @param mixed $controller Instancia del controlador
     * @return void
     */
    public static function handleAfter($controller)
    {
        $logMessage = sprintf(
            "RESPONSE: Controller %s finished processing page '%s'",
            get_class($controller),
            self::getPage($controller)
        );

        debug_log($logMessage, 'INFO', 'requests');
    }

    /**
     * Obtiene la página actual de forma segura
     *
     * @param mixed $controller Instancia del controlador
     * @return string
     */
    private static function getPage($controller)
    {
        // Intentar usar el método del controlador si existe
        if (method_exists($controller, 'getPage')) {
            return $controller->getPage();
        }

        // Fallback a obtener de la URL
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remover query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Obtener segmentos
        $segments = explode('/', trim($uri, '/'));

        // Retornar el último segmento o 'home' si está vacío
        return end($segments) ?: 'home';
    }

    /**
     * Obtiene el User Agent de forma segura
     *
     * @return string
     */
    private static function getUserAgent()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        // Truncar si es muy largo
        if (strlen($userAgent) > 200) {
            $userAgent = substr($userAgent, 0, 200) . '...';
        }

        return $userAgent;
    }

    /**
     * Log de actividad específica del middleware
     *
     * @param string $message Mensaje a registrar
     * @param string $level Nivel del log
     * @return void
     */
    public static function log($message, $level = 'info')
    {
        $logMessage = sprintf(
            "[%s] [MIDDLEWARE] %s",
            date('Y-m-d H:i:s'),
            $message
        );

        debug_log($logMessage, strtoupper($level), 'middleware');
    }

    /**
     * Registra información de sesión si está disponible
     *
     * @param mixed $controller Instancia del controlador
     * @return void
     */
    public static function logSessionInfo($controller)
    {
        $sessionData = [];

        // Información de sesión admin
        if (isset($_SESSION['admin_panel'])) {
            $sessionData['admin_user'] = $_SESSION['admin_panel']->email ?? 'unknown';
            $sessionData['admin_authenticated'] = true;
        } else {
            $sessionData['admin_authenticated'] = false;
        }

        // Información de idioma
        if (isset($_SESSION['lang'])) {
            $sessionData['language'] = $_SESSION['lang'];
        }

        // Información de IP
        $sessionData['client_ip'] = Tools::getClientIP();

        if (!empty($sessionData)) {
            debug_log([
                'session_info' => $sessionData,
                'controller' => get_class($controller),
                'timestamp' => date('Y-m-d H:i:s')
            ], 'SESSION_INFO', 'sessions');
        }
    }

    /**
     * Registra errores específicos del middleware
     *
     * @param string $error Descripción del error
     * @param mixed $controller Instancia del controlador
     * @return void
     */
    public static function logError($error, $controller = null)
    {
        $errorData = [
            'error' => $error,
            'timestamp' => date('Y-m-d H:i:s'),
            'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
        ];

        if ($controller) {
            $errorData['controller'] = get_class($controller);
            $errorData['client_ip'] = Tools::getClientIP();
        }

        debug_log($errorData, 'ERROR', 'middleware_errors');
    }
}
