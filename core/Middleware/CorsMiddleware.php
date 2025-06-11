<?php

/**
 * Middleware de CORS
 *
 * Maneja las cabeceras CORS para peticiones cross-origin
 */
class CorsMiddleware
{
    /**
     * Dominios permitidos para CORS
     * @var array
     */
    private static $allowedOrigins = [
        'http://localhost:3000',
        'https://appet.es',
        'https://dev1.equipo5.es'
    ];

    /**
     * Métodos HTTP permitidos
     * @var array
     */
    private static $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];

    /**
     * Headers permitidos
     * @var array
     */
    private static $allowedHeaders = [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
        'Origin'
    ];

    /**
     * Ejecuta el middleware de CORS
     *
     * @param Controllers $controller Instancia del controlador
     * @return void
     */
    public static function handle($controller)
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        // Verificar si el origen está permitido
        if (self::isOriginAllowed($origin)) {
            header("Access-Control-Allow-Origin: {$origin}");
        }

        // Configurar headers CORS
        header('Access-Control-Allow-Methods: ' . implode(', ', self::$allowedMethods));
        header('Access-Control-Allow-Headers: ' . implode(', ', self::$allowedHeaders));
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // 24 horas

        // Manejar peticiones OPTIONS (preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Verifica si un origen está permitido
     *
     * @param string $origin Origen a verificar
     * @return bool
     */
    private static function isOriginAllowed($origin)
    {
        if (empty($origin)) {
            return false;
        }

        return in_array($origin, self::$allowedOrigins);
    }

    /**
     * Añade un origen permitido
     *
     * @param string $origin Origen a añadir
     * @return void
     */
    public static function addAllowedOrigin($origin)
    {
        if (!in_array($origin, self::$allowedOrigins)) {
            self::$allowedOrigins[] = $origin;
        }
    }

    /**
     * Configura los orígenes permitidos
     *
     * @param array $origins Array de orígenes
     * @return void
     */
    public static function setAllowedOrigins($origins)
    {
        self::$allowedOrigins = $origins;
    }
}
