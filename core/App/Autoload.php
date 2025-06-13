<?php

class Autoload
{
    protected static $instance;
    private $classMap = [];
    private $directories = [
        'App' => 'core/App/',
        'Controllers' => 'core/Controllers/',
        'Funks' => 'core/Funks/',
        'Models' => 'core/Models/',
        'Helpers' => 'core/Helpers/',
        'Services' => 'core/Services/',
        'Middleware' => 'core/Middleware/'
    ];

    protected function __construct()
    {
        $this->buildClassMap();
    }

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Construye el mapa de clases escaneando los directorios
     */
    private function buildClassMap()
    {
        foreach ($this->directories as $namespace => $directory) {
            $fullPath = _PATH_ . $directory;
            if (is_dir($fullPath)) {
                $this->scanDirectory($fullPath, $namespace);
            }
        }
    }

    /**
     * Escanea un directorio recursivamente buscando archivos PHP
     */
    private function scanDirectory($directory, $namespace = '')
    {
        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'php') {
                    $this->registerClassFromFile($file, $namespace);
                }
            }
        } catch (Exception $e) {
            $this->logError("Error escaneando directorio $directory: " . $e->getMessage());
        }
    }

    /**
     * Registra una clase basándose en el archivo encontrado
     */
    private function registerClassFromFile(SplFileInfo $file, $namespace)
    {
        $relativePath = str_replace(_PATH_, '', $file->getPathname());
        $className = $file->getBasename('.php');

        // Evitar registrar archivos que no son clases (como config.php, index.php, etc.)
        if ($this->isValidClassName($className)) {
            $this->classMap[$className] = $relativePath;

            // También registrar con namespace si aplica
            if ($namespace && $namespace !== 'App') {
                $namespacedClass = $namespace . '\\' . $className;
                $this->classMap[$namespacedClass] = $relativePath;
            }
        }
    }

    /**
     * Verifica si el nombre del archivo corresponde a una clase válida
     */
    private function isValidClassName($className)
    {
        // Validaciones básicas
        if (empty($className) || strlen($className) < 2) {
            return false;
        }

        // Excluir archivos que no son clases
        $excludedFiles = [
            'index', 'config', 'settings', 'core', 'utils',
            'class_index', 'autoload', 'bootstrap', 'functions'
        ];

        $lowerClassName = strtolower($className);

        // Verificar que no esté en la lista de exclusión
        if (in_array($lowerClassName, $excludedFiles)) {
            return false;
        }

        // Verificar que no contenga palabras de test
        if (strpos($lowerClassName, 'test') !== false) {
            return false;
        }

        // Debe empezar con mayúscula y contener solo caracteres válidos
        return ctype_upper($className[0]) &&
            preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $className);
    }

    /**
     * Carga una clase
     */
    public function load($className)
    {
        // Validar que el nombre de clase no esté vacío o sea inválido
        if (empty($className) || !is_string($className) || strlen($className) < 2) {
            return false;
        }

        // Limpiar el nombre de la clase (remover namespace si existe)
        $cleanClassName = $this->cleanClassName($className);

        // Validar el nombre limpio
        if (empty($cleanClassName) || strlen($cleanClassName) < 2) {
            return false;
        }

        // Buscar en el mapa de clases
        if (isset($this->classMap[$cleanClassName])) {
            $filePath = _PATH_ . $this->classMap[$cleanClassName];

            if (file_exists($filePath)) {
                require_once $filePath;
                return true;
            } else {
                $this->logError("Archivo no encontrado: $filePath para la clase $className");
            }
        }

        // Si no se encuentra, intentar carga por convención
        return $this->loadByConvention($cleanClassName);
    }

    /**
     * Limpia el nombre de la clase removiendo namespaces y prefijos
     */
    private function cleanClassName($className)
    {
        // Validar entrada
        if (!is_string($className) || empty($className)) {
            return '';
        }

        // Remover namespaces comunes
        $className = str_ireplace(['App\\', 'Controllers\\', 'Models\\', 'Funks\\'], '', $className);

        // Remover sufijos comunes pero mantener Controller si es parte del nombre
        if (strlen($className) > 10 && substr($className, -10) === 'Controller') {
            // Mantener Controller en el nombre
        } else {
            $className = str_ireplace('Controller', '', $className);
        }

        // Limpiar caracteres extraños
        $className = preg_replace('/[^A-Za-z0-9_]/', '', $className);

        return $className;
    }

    /**
     * Intenta cargar una clase por convención de nombres
     */
    private function loadByConvention($className)
    {
        // Validar entrada
        if (empty($className) || strlen($className) < 2) {
            return false;
        }

        $possiblePaths = [
            "core/App/{$className}.php",
            "core/Controllers/{$className}Controller.php",
            "core/Controllers/{$className}.php",
            "core/Funks/{$className}.php",
            "core/Models/{$className}.php",
            "core/Helpers/{$className}.php",
            "core/Services/{$className}.php",
            "core/Middleware/{$className}.php"
        ];

        foreach ($possiblePaths as $path) {
            $fullPath = _PATH_ . $path;
            if (file_exists($fullPath)) {
                require_once $fullPath;

                // Actualizar el mapa para futuras cargas
                $this->classMap[$className] = $path;
                return true;
            }
        }

        // Solo registrar error si es una clase que parece válida
        if ($this->isValidClassName($className)) {
            $this->logError("Clase no encontrada: $className");
        }

        return false;
    }

    /**
     * Obtiene el mapa completo de clases (útil para debugging)
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * Refresca el mapa de clases (útil en desarrollo)
     */
    public function refresh()
    {
        $this->classMap = [];
        $this->buildClassMap();
    }

    /**
     * Registra manualmente una clase (para casos especiales)
     */
    public function registerClass($className, $filePath)
    {
        if (!empty($className) && !empty($filePath)) {
            $this->classMap[$className] = $filePath;
        }
    }

    /**
     * Verifica si una clase está registrada
     */
    public function isRegistered($className)
    {
        if (empty($className)) {
            return false;
        }

        $cleanClassName = $this->cleanClassName($className);
        return isset($this->classMap[$cleanClassName]);
    }

    /**
     * Log de errores mejorado
     */
    private function logError($message)
    {
        // Solo registrar si el mensaje no está vacío y es útil
        if (empty($message) || strlen($message) < 10) {
            return;
        }

        // Evitar spam de logs para clases de una sola letra o inválidas
        if (preg_match('/Clase no encontrada: [a-z]$/', $message)) {
            return;
        }

        if (function_exists('__log_error')) {
            __log_error("Autoload: $message", 0, 'autoload');
        } else {
            error_log("Autoload: $message");
        }
    }

    /**
     * Método para debugging - muestra todas las clases cargadas
     */
    public function debug()
    {
        if (defined('_DEBUG_') && _DEBUG_) {
            echo "<h3>Clases registradas en Autoload:</h3>";
            echo "<pre>";
            foreach ($this->classMap as $class => $path) {
                echo htmlspecialchars("$class => $path") . "\n";
            }
            echo "</pre>";

            echo "<h3>Estadísticas:</h3>";
            echo "<pre>";
            echo "Total de clases registradas: " . count($this->classMap) . "\n";
            echo "Directorios escaneados: " . count($this->directories) . "\n";
            echo "</pre>";
        }
    }

    /**
     * Obtiene estadísticas del autoloader
     */
    public function getStats()
    {
        $stats = [
            'total_classes' => count($this->classMap),
            'directories_scanned' => count($this->directories),
            'classes_by_type' => []
        ];

        foreach ($this->classMap as $class => $path) {
            if (strpos($path, 'Controllers/') !== false) {
                $stats['classes_by_type']['Controllers'] = ($stats['classes_by_type']['Controllers'] ?? 0) + 1;
            } elseif (strpos($path, 'Funks/') !== false) {
                $stats['classes_by_type']['Funks'] = ($stats['classes_by_type']['Funks'] ?? 0) + 1;
            } elseif (strpos($path, 'App/') !== false) {
                $stats['classes_by_type']['App'] = ($stats['classes_by_type']['App'] ?? 0) + 1;
            } elseif (strpos($path, 'Middleware/') !== false) {
                $stats['classes_by_type']['Middleware'] = ($stats['classes_by_type']['Middleware'] ?? 0) + 1;
            } else {
                $stats['classes_by_type']['Others'] = ($stats['classes_by_type']['Others'] ?? 0) + 1;
            }
        }

        return $stats;
    }
}
