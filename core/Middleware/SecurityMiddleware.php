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
     * @param mixed $controller Instancia del controlador
     * @return void
     */
    public static function handle($controller)
    {
        // Añadir headers de seguridad
        self::addSecurityHeaders();

        // Verificar rate limiting básico (más permisivo durante desarrollo)
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
     * @param mixed $controller Instancia del controlador
     * @return void
     */
    private static function checkRateLimit($controller)
    {
        // Obtener IP del cliente
        $ip = self::getClientIP();

        // Si el controlador tiene método getClientIP, usarlo
        if (is_object($controller) && method_exists($controller, 'getClientIP')) {
            $ip = $controller->getClientIP();
        }

        $key = 'rate_limit_' . md5($ip);

        // Límites más permisivos durante desarrollo
        $maxRequests = defined('_DEBUG_') && _DEBUG_ ? 500 : 100; // 500 en desarrollo, 100 en producción
        $timeWindow = 60; // 1 minuto

        // Implementación básica de rate limiting usando sesión
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'last_request' => time()
            ];
        } else {
            $timeDiff = time() - $_SESSION[$key]['last_request'];

            // Reset counter cada minuto
            if ($timeDiff > $timeWindow) {
                $_SESSION[$key] = [
                    'count' => 1,
                    'last_request' => time()
                ];
            } else {
                $_SESSION[$key]['count']++;
                $_SESSION[$key]['last_request'] = time();

                // Verificar límite
                if ($_SESSION[$key]['count'] > $maxRequests) {
                    // Log del rate limit
                    if (function_exists('debug_log')) {
                        debug_log([
                            'ip' => $ip,
                            'count' => $_SESSION[$key]['count'],
                            'max_requests' => $maxRequests,
                            'controller' => is_object($controller) ? get_class($controller) : 'unknown',
                            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
                        ], 'RATE_LIMIT_EXCEEDED', 'security');
                    }

                    http_response_code(429);
                    header('Retry-After: ' . $timeWindow);
                    die('Too Many Requests - Please wait before trying again');
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

    /**
     * Verifica si una IP está en whitelist
     *
     * @param string $ip IP a verificar
     * @return bool
     */
    public static function isWhitelistedIP($ip)
    {
        $whitelist = [
            '127.0.0.1',
            '::1',
            'localhost'
        ];

        // Añadir IPs de configuración si existen
        if (defined('_SECURITY_WHITELIST_IPS_')) {
            $configIPs = explode(',', _SECURITY_WHITELIST_IPS_);
            $whitelist = array_merge($whitelist, array_map('trim', $configIPs));
        }

        return in_array($ip, $whitelist);
    }

    /**
     * Bloquea una IP temporalmente
     *
     * @param string $ip IP a bloquear
     * @param int $duration Duración en segundos
     * @return void
     */
    public static function blockIP($ip, $duration = 3600)
    {
        $key = 'blocked_ip_' . md5($ip);
        $_SESSION[$key] = time() + $duration;

        if (function_exists('debug_log')) {
            debug_log([
                'ip' => $ip,
                'duration' => $duration,
                'blocked_until' => date('Y-m-d H:i:s', time() + $duration)
            ], 'IP_BLOCKED', 'security');
        }
    }

    /**
     * Verifica si una IP está bloqueada
     *
     * @param string $ip IP a verificar
     * @return bool
     */
    public static function isBlockedIP($ip)
    {
        $key = 'blocked_ip_' . md5($ip);

        if (isset($_SESSION[$key])) {
            if (time() < $_SESSION[$key]) {
                return true;
            } else {
                // El bloqueo ha expirado
                unset($_SESSION[$key]);
            }
        }

        return false;
    }

    /**
     * Valida token CSRF
     *
     * @param string $token Token a validar
     * @return bool
     */
    public static function validateCSRFToken($token)
    {
        return isset($_SESSION['token']) && $_SESSION['token'] === $token;
    }

    /**
     * Genera un token CSRF
     *
     * @return string
     */
    public static function generateCSRFToken()
    {
        if (!isset($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['token'];
    }

    /**
     * Resetea el contador de rate limiting para una IP
     *
     * @param string $ip IP a resetear (opcional, usa la actual si no se especifica)
     * @return void
     */
    public static function resetRateLimit($ip = null)
    {
        if ($ip === null) {
            $ip = self::getClientIP();
        }

        $key = 'rate_limit_' . md5($ip);
        unset($_SESSION[$key]);

        if (function_exists('debug_log')) {
            debug_log([
                'ip' => $ip,
                'action' => 'rate_limit_reset'
            ], 'RATE_LIMIT_RESET', 'security');
        }
    }
}
