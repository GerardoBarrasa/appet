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
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $this->registerClassFromFile($file, $namespace);
            }
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
        // Excluir archivos que no son clases
        $excludedFiles = [
            'index', 'config', 'settings', 'core', 'utils',
            'class_index', 'autoload', 'bootstrap'
        ];

        $lowerClassName = strtolower($className);

        // Debe empezar con mayúscula y no estar en la lista de exclusión
        return ctype_upper($className[0]) &&
            !in_array($lowerClassName, $excludedFiles) &&
            !strpos($lowerClassName, 'test') !== false;
    }

    /**
     * Carga una clase
     */
    public function load($className)
    {
        // Limpiar el nombre de la clase (remover namespace si existe)
        $cleanClassName = $this->cleanClassName($className);

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
        // Remover namespaces comunes
        $className = str_ireplace(['App\\', 'Controllers\\', 'Models\\'], '', $className);

        // Remover sufijos comunes
        $className = str_ireplace('Controller', '', $className);

        return $className;
    }

    /**
     * Intenta cargar una clase por convención de nombres
     */
    private function loadByConvention($className)
    {
        $possiblePaths = [
            "core/App/{$className}.php",
            "core/Controllers/{$className}Controller.php",
            "core/Controllers/{$className}.php",
            "core/Funks/{$className}.php",
            "core/Models/{$className}.php",
            "core/Helpers/{$className}.php",
            "core/Services/{$className}.php"
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

        $this->logError("Clase no encontrada: $className");
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
        $this->classMap[$className] = $filePath;
    }

    /**
     * Verifica si una clase está registrada
     */
    public function isRegistered($className)
    {
        $cleanClassName = $this->cleanClassName($className);
        return isset($this->classMap[$cleanClassName]);
    }

    /**
     * Log de errores
     */
    private function logError($message)
    {
        if (function_exists('__log_error')) {
            __log_error("Autoload: $message");
        } else {
            error_log("Autoload: $message");
        }
    }

    /**
     * Método para debugging - muestra todas las clases cargadas
     */
    public function debug()
    {
        if (_DEBUG_) {
            echo "<h3>Clases registradas en Autoload:</h3>";
            echo "<pre>";
            foreach ($this->classMap as $class => $path) {
                echo "$class => $path\n";
            }
            echo "</pre>";
        }
    }
}
