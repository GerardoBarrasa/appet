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
        // Solo aplicar a controladores de administración
        if (!($controller instanceof AdminController)) {
            return;
        }

        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['admin_panel'])) {
            // Redirigir al login
            header('Location: ' . _DOMINIO_ . _ADMIN_);
            exit;
        }

        // Log de acceso
        $email = $_SESSION['admin_panel']->email ?? 'unknown';
        $controller->log("Usuario autenticado accedió: {$email}", 'info');
    }
}
