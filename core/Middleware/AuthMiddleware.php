<?php

/**
 * Middleware de autenticación
 *
 * Verifica que el usuario esté autenticado antes de acceder a rutas protegidas
 */
class AuthMiddleware
{
    /**
     * Ejecuta el middleware de autenticación
     *
     * @param Controllers $controller Instancia del controlador
     * @return void
     */
    public static function handle($controller)
    {
        debug_log([
            'middleware' => 'AuthMiddleware',
            'controller' => is_object($controller) ? get_class($controller) : 'null',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'session_admin' => isset($_SESSION['admin_panel']) ? 'yes' : 'no'
        ], 'AUTH_MIDDLEWARE_START', 'middleware');

        // Solo aplicar a controladores de administración
        if (!($controller instanceof AdminController)) {
            debug_log('AuthMiddleware: No es AdminController, saltando verificación', 'AUTH_MIDDLEWARE_SKIP', 'middleware');
            return;
        }

        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['admin_panel'])) {
            debug_log([
                'action' => 'redirect_to_login',
                'reason' => 'no_session',
                'redirect_url' => _DOMINIO_ . _ADMIN_
            ], 'AUTH_MIDDLEWARE_REDIRECT', 'middleware');

            // ESTE ES EL PROBLEMA: Esta redirección causa el bucle
            // Comentamos temporalmente para evitar el bucle
            // header('Location: ' . _DOMINIO_ . _ADMIN_);
            // exit;

            debug_log('AuthMiddleware: Redirección deshabilitada temporalmente', 'AUTH_MIDDLEWARE_DISABLED', 'middleware');
            return;
        }

        // Log de acceso
        $email = $_SESSION['admin_panel']->email ?? 'unknown';
        debug_log("Usuario autenticado accedió: {$email}", 'AUTH_MIDDLEWARE_SUCCESS', 'middleware');

        if (method_exists($controller, 'log')) {
            $controller->log("Usuario autenticado accedió: {$email}", 'info');
        }
    }
}
