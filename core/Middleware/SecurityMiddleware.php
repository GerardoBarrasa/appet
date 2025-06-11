<?php

/**
 * Middleware de seguridad
 *
 * Aplica medidas de seguridad básicas a todas las peticiones
 */
class SecurityMiddleware
{
    /**
     * Ejecuta el middleware de seguridad
     *
     * @param Controllers $controller Instancia del controlador
     * @return void
     */
    public static function handle($controller)
    {
        // Añadir headers de seguridad
        self::addSecurityHeaders();

        // Verificar rate limiting básico
        self::checkRateLimit($controller);

        // Sanitizar datos de entrada
        self::sanitizeInput();
    }

    /**
     * Añade headers de seguridad
     *
     * @return void
     */
    private static function addSecurityHeaders()
    {
        // Prevenir clickjacking
        header('X-Frame-Options: DENY');

        // Prevenir MIME type sniffing
        header('X-Content-Type-Options: nosniff');

        // Habilitar XSS protection
        header('X-XSS-Protection: 1; mode=block');

        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }

    /**
     * Verifica rate limiting básico
     *
     * @param Controllers $controller Instancia del controlador
     * @return void
     */
    private static function checkRateLimit($controller)
    {
        $ip = $controller->getClientIP();
        $key = 'rate_limit_' . md5($ip);

        // Implementación básica de rate limiting usando sesión
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'last_request' => time()
            ];
        } else {
            $timeDiff = time() - $_SESSION[$key]['last_request'];

            // Reset counter cada minuto
            if ($timeDiff > 60) {
                $_SESSION[$key] = [
                    'count' => 1,
                    'last_request' => time()
                ];
            } else {
                $_SESSION[$key]['count']++;
                $_SESSION[$key]['last_request'] = time();

                // Límite de 100 peticiones por minuto
                if ($_SESSION[$key]['count'] > 100) {
                    http_response_code(429);
                    die('Too Many Requests');
                }
            }
        }
    }

    /**
     * Sanitiza datos de entrada básicos
     *
     * @return void
     */
    private static function sanitizeInput()
    {
        // Sanitizar $_GET
        foreach ($_GET as $key => $value) {
            if (is_string($value)) {
                $_GET[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }
    }

    /**
     * Obtiene la IP del cliente de forma estática
     *
     * @return string
     */
    public static function getClientIP()
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
}
