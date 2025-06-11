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
     * @param Controllers $controller Instancia del controlador
     * @return void
     */
    public static function handleBefore($controller)
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $controller->getClientIP(),
            'method' => $_SERVER['REQUEST_METHOD'],
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
            'user_agent' => self::getUserAgent(),
            'controller' => get_class($controller),
            'page' => $controller->getPage()
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

        Tools::logError($logMessage, 0, 'requests');
    }

    /**
     * Ejecuta el middleware de logging después del controlador
     *
     * @param Controllers $controller Instancia del controlador
     * @return void
     */
    public static function handleAfter($controller)
    {
        $logMessage = sprintf(
            "RESPONSE: Controller %s finished processing page '%s'",
            get_class($controller),
            $controller->getPage()
        );

        Tools::logError($logMessage, 0, 'requests');
    }

    /**
     * Obtiene el User Agent de forma segura
     *
     * @return string
     */
    private static function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }
}
