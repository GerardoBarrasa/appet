<?php

/*
     ----   ----    ---    ----
    |      |    |  | _ |  |_
    |      |    |  |  \   |
     ----   ----   -   -   ----   4.1
*/

/**
 * @author Anelis Network
 */
ini_set('session.gc_maxlifetime', 43200);
@session_start();

// Definimos constantes
define( 'DS', DIRECTORY_SEPARATOR );
define( '_PATH_', str_replace(DS.'core',DS,dirname(__FILE__)) );
define( 'log_folder', _PATH_.'log/' );
define( 'log_max_kb', 2048 );
if (!file_exists(log_folder)) {
    mkdir(log_folder, 0755, true);
}

// Configurar logging de PHP
ini_set("log_errors", 1);
ini_set("error_log", log_folder . "phpErrors_" . date("Ymd") . ".log");

// Incluimos configuracion
require_once _PATH_.'core/config.php';

require_once _PATH_.'core/utils.php';

require_once _PATH_.'vendor/autoload.php';

require_once _PATH_.'core/App/Autoload.php';

// Autoload de todas las clases
spl_autoload_register(array(Autoload::getInstance(), 'load'));

/**
 * Sistema de logging mejorado
 * @param string|array $message Mensaje a registrar
 * @param int $type Tipo de log (0=debug, 1=email, 3=archivo, 99=SQL)
 * @param string $fichero Nombre personalizado del archivo
 * @return bool
 */
function __log_error($message = 'Error inesperado', int $type = 3, string $fichero = ''): bool
{
    $date = date('Ymd');
    $timestamp = date('Y-m-d H:i:s');

    // Convertir arrays/objetos a JSON
    if (is_array($message) || is_object($message)) {
        $message = json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // Obtener información del contexto
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
    $caller = isset($backtrace[1]) ? $backtrace[1] : $backtrace[0];
    $file = isset($caller['file']) ? basename($caller['file']) : 'unknown';
    $line = isset($caller['line']) ? $caller['line'] : 'unknown';
    $function = isset($caller['function']) ? $caller['function'] : 'unknown';

    $context = "[{$file}:{$line}] {$function}()";
    $fullMessage = "{$timestamp} - {$context} - {$message}" . PHP_EOL;

    switch ($type) {
        case 0: // Debug personalizado
            $filename = $fichero ? "debug_{$fichero}_{$date}.log" : "debug_general_{$date}.log";
            $filepath = log_folder . $filename;
            break;

        case 1: // Email
            if (defined('_WARNING_MAIL_') && _WARNING_MAIL_) {
                return error_log($fullMessage, 1, _WARNING_MAIL_);
            }
            return false;

        case 99: // Errores SQL
            $filename = "SQLErrors_{$date}.log";
            $filepath = log_folder . $filename;
            break;

        case 3: // Archivo general
        default:
            if ($fichero) {
                $filename = "{$fichero}_{$date}.log";
            } else {
                $filename = "general_{$date}.log";
            }
            $filepath = log_folder . $filename;
            break;
    }

    // Escribir al archivo
    if (isset($filepath)) {
        return error_log($fullMessage, 3, $filepath);
    }

    return false;
}

/**
 * Función de debug personalizada para desarrolladores
 * @param mixed $data Datos a registrar
 * @param string $label Etiqueta descriptiva
 * @param string $filename Nombre del archivo (opcional)
 * @return bool
 */
function debug_log($data, string $label = 'DEBUG', string $filename = ''): bool
{

    $message = "=== {$label} ===" . PHP_EOL;

    if (is_string($data)) {
        $message .= $data;
    } else {
        $message .= print_r($data, true);
    }

    $message .= PHP_EOL . "==================" . PHP_EOL;

    return __log_error($message, 0, $filename ?: 'custom');
}

/**
 * Log específico para performance
 * @param string $operation Operación realizada
 * @param float $startTime Tiempo de inicio
 * @param array $additionalData Datos adicionales
 * @return bool
 */
function performance_log(string $operation, float $startTime, array $additionalData = []): bool
{
    $executionTime = microtime(true) - $startTime;
    $memoryUsage = memory_get_usage(true);
    $peakMemory = memory_get_peak_usage(true);

    $data = [
        'operation' => $operation,
        'execution_time' => round($executionTime, 4) . 's',
        'memory_usage' => formatBytes($memoryUsage),
        'peak_memory' => formatBytes($peakMemory),
        'timestamp' => date('Y-m-d H:i:s'),
        'additional_data' => $additionalData
    ];

    return __log_error($data, 0, 'performance');
}

/**
 * Formatear bytes en formato legible
 */
function formatBytes($bytes, $precision = 2): string
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Manejador personalizado de errores PHP
 */
function customErrorHandler($severity, $message, $file, $line)
{
    // Solo registrar Warning y superiores
    if ($severity >= E_WARNING) {
        $errorTypes = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE_ERROR',
            E_CORE_WARNING => 'CORE_WARNING',
            E_COMPILE_ERROR => 'COMPILE_ERROR',
            E_COMPILE_WARNING => 'COMPILE_WARNING',
            E_USER_ERROR => 'USER_ERROR',
            E_USER_WARNING => 'USER_WARNING',
            E_USER_NOTICE => 'USER_NOTICE',
            E_STRICT => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER_DEPRECATED'
        ];

        $errorType = isset($errorTypes[$severity]) ? $errorTypes[$severity] : 'UNKNOWN';
        $errorMessage = "[{$errorType}] {$message} in {$file} on line {$line}";

        // Escribir directamente al log de PHP
        error_log(date('Y-m-d H:i:s') . " - " . $errorMessage . PHP_EOL, 3, log_folder . "phpErrors_" . date("Ymd") . ".log");
    }

    // No interferir con el manejo normal de errores
    return false;
}

// Registrar el manejador de errores personalizado
set_error_handler('customErrorHandler');

// Cargar clases principales manualmente si es necesario
if (!class_exists('Controllers')) {
    require_once _PATH_ . 'core/Controllers/Controllers.php';
}

// Verificar que las clases middleware existan antes de registrarlas
$middlewareClasses = [
    'SecurityMiddleware' => 'core/Middleware/SecurityMiddleware.php',
    'LoggingMiddleware' => 'core/Middleware/LoggingMiddleware.php',
    'AuthMiddleware' => 'core/Middleware/AuthMiddleware.php'
];

foreach ($middlewareClasses as $className => $filePath) {
    if (!class_exists($className)) {
        $fullPath = _PATH_ . $filePath;
        if (file_exists($fullPath)) {
            require_once $fullPath;
        } else {
            __log_error("Middleware no encontrado: $className en $fullPath", 3, 'core_errors');
        }
    }
}

// Registrar middleware global solo si las clases existen
if (class_exists('Controllers')) {
    if (class_exists('SecurityMiddleware')) {
        Controllers::registerGlobalMiddleware('before', ['SecurityMiddleware', 'handle']);
    }

    if (class_exists('LoggingMiddleware')) {
        Controllers::registerGlobalMiddleware('before', ['LoggingMiddleware', 'handleBefore']);
        Controllers::registerGlobalMiddleware('after', ['LoggingMiddleware', 'handleAfter']);
    }

    // Registrar middleware de autenticación solo para admin
    if (class_exists('AuthMiddleware') && class_exists('AdminController')) {
        Controllers::registerGlobalMiddleware('before', function($controller) {
            if ($controller instanceof AdminController) {
                AuthMiddleware::handle($controller);
            }
        });
    }
}

// Configurar idiomas si está habilitado
if (defined('_MULTI_LANGUAGE_') && _MULTI_LANGUAGE_) {
    if (class_exists('Idiomas')) {
        Idiomas::setLanguage();
    } else {
        __log_error("Clase Idiomas no encontrada pero _MULTI_LANGUAGE_ está habilitado", 3, 'core_errors');
    }
}

// Inicializar el sistema de controladores
try {
    if (class_exists('Controllers')) {
        $controllers = new Controllers;
        $controllers->load();
    } else {
        throw new Exception("Clase Controllers no encontrada después de intentar cargarla");
    }
} catch (Exception $e) {
    __log_error("Error fatal al inicializar Controllers: " . $e->getMessage(), 3, 'core_errors');

    // Mostrar error amigable al usuario
    if (defined('_DEBUG_') && _DEBUG_) {
        echo "<h1>Error de Inicialización</h1>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>Revisa los logs en: " . log_folder . "</p>";

        // Mostrar información de debug del autoloader
        if (class_exists('Autoload')) {
            $autoload = Autoload::getInstance();
            $autoload->debug();
        }
    } else {
        echo "<h1>Error del Sistema</h1>";
        echo "<p>Ha ocurrido un error interno. Contacta con el administrador.</p>";
    }

    exit(1);
}
