<?php

/**
 * Middleware de cache
 *
 * Maneja el cache de respuestas HTTP
 */
class CacheMiddleware
{
    /**
     * Rutas que deben ser cacheadas
     * @var array
     */
    private static $cacheableRoutes = [
        'home' => 3600,      // 1 hora
        'about' => 7200,     // 2 horas
        'contact' => 1800    // 30 minutos
    ];

    /**
     * Ejecuta el middleware de cache antes del controlador
     *
     * @param Controllers $controller Instancia del controlador
     * @return void
     */
    public static function handleBefore($controller)
    {
        $page = $controller->getCurrentPage();

        // Solo cachear rutas específicas
        if (!isset(self::$cacheableRoutes[$page])) {
            return;
        }

        $cacheTime = self::$cacheableRoutes[$page];
        $cacheKey = self::generateCacheKey($controller);
        $cacheFile = self::getCacheFilePath($cacheKey);

        // Verificar si existe cache válido
        if (self::isCacheValid($cacheFile, $cacheTime)) {
            // Servir desde cache
            self::serveCachedContent($cacheFile);
            exit;
        }

        // Iniciar buffer de salida para capturar el contenido
        ob_start();
    }

    /**
     * Ejecuta el middleware de cache después del controlador
     *
     * @param Controllers $controller Instancia del controlador
     * @return void
     */
    public static function handleAfter($controller)
    {
        $page = $controller->getCurrentPage();

        // Solo cachear rutas específicas
        if (!isset(self::$cacheableRoutes[$page])) {
            return;
        }

        // Obtener el contenido del buffer
        $content = ob_get_contents();

        if (!empty($content)) {
            $cacheKey = self::generateCacheKey($controller);
            $cacheFile = self::getCacheFilePath($cacheKey);

            // Guardar en cache
            self::saveToCache($cacheFile, $content);
        }
    }

    /**
     * Genera una clave de cache única
     *
     * @param Controllers $controller Instancia del controlador
     * @return string
     */
    private static function generateCacheKey($controller)
    {
        $factors = [
            get_class($controller),
            $controller->getCurrentPage(),
            $_SESSION['lang'] ?? 'es',
            $_SERVER['REQUEST_URI'] ?? ''
        ];

        return md5(implode('|', $factors));
    }

    /**
     * Obtiene la ruta del archivo de cache
     *
     * @param string $cacheKey Clave de cache
     * @return string
     */
    private static function getCacheFilePath($cacheKey)
    {
        $cacheDir = _PATH_ . 'cache/pages/';

        // Crear directorio si no existe
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        return $cacheDir . $cacheKey . '.html';
    }

    /**
     * Verifica si el cache es válido
     *
     * @param string $cacheFile Ruta del archivo de cache
     * @param int $cacheTime Tiempo de vida del cache en segundos
     * @return bool
     */
    private static function isCacheValid($cacheFile, $cacheTime)
    {
        if (!file_exists($cacheFile)) {
            return false;
        }

        $fileTime = filemtime($cacheFile);
        $currentTime = time();

        return ($currentTime - $fileTime) < $cacheTime;
    }

    /**
     * Sirve contenido desde cache
     *
     * @param string $cacheFile Ruta del archivo de cache
     * @return void
     */
    private static function serveCachedContent($cacheFile)
    {
        // Añadir header para indicar que viene del cache
        header('X-Cache: HIT');
        header('Content-Type: text/html; charset=UTF-8');

        // Servir el contenido
        readfile($cacheFile);
    }

    /**
     * Guarda contenido en cache
     *
     * @param string $cacheFile Ruta del archivo de cache
     * @param string $content Contenido a guardar
     * @return bool
     */
    private static function saveToCache($cacheFile, $content)
    {
        return file_put_contents($cacheFile, $content) !== false;
    }

    /**
     * Limpia el cache de una página específica
     *
     * @param string $page Página a limpiar
     * @return bool
     */
    public static function clearPageCache($page)
    {
        $pattern = _PATH_ . 'cache/pages/*' . md5($page) . '*.html';
        $files = glob($pattern);

        $success = true;
        foreach ($files as $file) {
            if (!unlink($file)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Limpia todo el cache
     *
     * @return bool
     */
    public static function clearAllCache()
    {
        $cacheDir = _PATH_ . 'cache/pages/';
        $files = glob($cacheDir . '*.html');

        $success = true;
        foreach ($files as $file) {
            if (!unlink($file)) {
                $success = false;
            }
        }

        return $success;
    }
}
