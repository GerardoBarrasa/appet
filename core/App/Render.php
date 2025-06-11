<?php

/**
 * Clase Render - Sistema de renderizado de vistas y layouts
 *
 * Maneja la renderización de páginas, layouts, componentes y assets del sistema.
 * Incluye cache de vistas, sistema de slots y gestión avanzada de templates.
 */
class Render
{
    /**
     * Configuración estática del renderizador
     */
    public static $page;
    public static $data;
    public static $layout_data;
    public static $layout = 'front-end';

    /**
     * Sistema de slots para layouts
     */
    protected static $slots = [];

    /**
     * Cache de vistas compiladas
     */
    protected static $viewCache = [];

    /**
     * Slot actual siendo capturado
     */
    protected static $currentSlot = null;

    /**
     * Configuración del renderizador
     */
    protected static $config = [
        'cache_enabled' => true,
        'cache_ttl' => 3600,
        'minify_html' => false,
        'debug_mode' => false,
        'default_layout' => 'front-end',
        'view_paths' => [
            'pages' => 'pages',
            'layouts' => 'layout',
            'components' => 'components',
            'partials' => 'partials'
        ]
    ];

    /**
     * Assets registrados
     */
    protected static $assets = [
        'css' => [],
        'js' => [
            'head' => [],
            'footer' => []
        ],
        'meta' => []
    ];

    /**
     * Inicializa la configuración del renderizador
     *
     * @param array $config Configuración personalizada
     * @return void
     */
    public static function init($config = [])
    {
        self::$config = array_merge(self::$config, $config);

        // Configurar debug mode basado en la constante global
        if (defined('_DEBUG_')) {
            self::$config['debug_mode'] = _DEBUG_;
        }
    }

    /**
     * Renderiza y devuelve el layout principal
     *
     * @return void
     */
    public static function getLayout()
    {
        try {
            // Preparar datos del layout
            self::prepareLayoutData();

            // Obtener ruta del layout
            $layoutPath = self::getLayoutPath(self::$layout);

            if (!file_exists($layoutPath)) {
                throw new Exception("Layout no encontrado: " . self::$layout);
            }

            // Renderizar layout
            self::renderTemplate($layoutPath, self::$layout_data);

        } catch (Exception $e) {
            self::handleRenderError($e, 'layout');
        }
    }

    /**
     * Renderiza una página específica
     *
     * @return void
     */
    public static function getPage()
    {
        try {
            // Preparar datos de la página
            self::preparePageData();

            // Obtener ruta de la página
            $pagePath = self::getPagePath(self::$page);

            if (!file_exists($pagePath)) {
                $pagePath = self::getPagePath('404');
            }

            // Renderizar página
            self::renderTemplate($pagePath, self::$data);

        } catch (Exception $e) {
            self::handleRenderError($e, 'page');
        }
    }

    /**
     * Configura página para mostrarla con layout
     *
     * @param string $name Nombre de la página
     * @param array $data Datos para la página
     * @return void
     */
    public static function page($name, $data = [])
    {
        self::$page = $name;
        self::$data = $data;
    }

    /**
     * Configura y muestra página sin layout
     *
     * @param string $name Nombre de la página
     * @param array $data Datos para la página
     * @return void
     */
    public static function showPage($name, $data = [])
    {
        self::$page = $name;
        self::$data = $data;
        self::$layout = false;
        self::getPage();
    }

    /**
     * Renderiza página de administración
     *
     * @return void
     */
    public static function getAdminPage()
    {
        try {
            // Preparar datos de la página admin
            self::preparePageData();

            // Obtener ruta de la página admin
            $pagePath = self::getAdminPagePath(self::$page);

            if (!file_exists($pagePath)) {
                $pagePath = self::getAdminPagePath('404');
            }

            // Renderizar página admin
            self::renderTemplate($pagePath, self::$data);

        } catch (Exception $e) {
            self::handleRenderError($e, 'admin_page');
        }
    }

    /**
     * Configura página admin para mostrarla con layout
     *
     * @param string $name Nombre de la página
     * @param array $data Datos para la página
     * @return void
     */
    public static function adminPage($name, $data = [])
    {
        self::$page = $name;
        self::$data = $data;
    }

    /**
     * Configura y muestra página admin sin layout
     *
     * @param string $name Nombre de la página
     * @param array $data Datos para la página
     * @return void
     */
    public static function showAdminPage($name, $data = [])
    {
        self::$page = $name;
        self::$data = $data;
        self::$layout = false;
        self::getAdminPage();
    }

    /**
     * Renderiza un bloque/componente
     *
     * @param string $page Nombre del bloque
     * @param array $data Datos para el bloque
     * @return void
     */
    public static function bloq($page, $data = [])
    {
        try {
            $blockPath = self::getBlockPath($page);

            if (!file_exists($blockPath)) {
                $blockPath = self::getBlockPath('404');
            }

            self::renderTemplate($blockPath, $data);

        } catch (Exception $e) {
            self::handleRenderError($e, 'block');
        }
    }

    /**
     * Obtiene contenido de página AJAX
     *
     * @param string $name Nombre de la página AJAX
     * @param array $data Datos para la página
     * @return string Contenido HTML
     */
    public static function getAjaxPage($name, $data = [])
    {
        try {
            $ajaxPath = self::getAjaxPagePath($name);

            if (!file_exists($ajaxPath)) {
                $ajaxPath = self::getAjaxPagePath('404');
            }

            return self::renderTemplateToString($ajaxPath, $data);

        } catch (Exception $e) {
            self::handleRenderError($e, 'ajax');
            return '<div class="error">Error cargando contenido AJAX</div>';
        }
    }

    /**
     * Obtiene contenido de página PDF
     *
     * @param string $name Nombre de la página PDF
     * @param array $data Datos para la página
     * @return string Contenido HTML
     */
    public static function getPDFPage($name, $data = [])
    {
        try {
            $pdfPath = self::getPDFPagePath($name);

            if (!file_exists($pdfPath)) {
                return '<html><body>Página PDF no encontrada.</body></html>';
            }

            return self::renderTemplateToString($pdfPath, $data);

        } catch (Exception $e) {
            self::handleRenderError($e, 'pdf');
            return '<html><body>Error generando PDF.</body></html>';
        }
    }

    // ==========================================
    // SISTEMA DE COMPONENTES Y PARTIALS
    // ==========================================

    /**
     * Renderiza un componente
     *
     * @param string $component Nombre del componente
     * @param array $data Datos para el componente
     * @return string Contenido HTML del componente
     */
    public static function component($component, $data = [])
    {
        try {
            $componentPath = self::getComponentPath($component);

            if (!file_exists($componentPath)) {
                if (self::$config['debug_mode']) {
                    return "<!-- Componente no encontrado: {$component} -->";
                }
                return '';
            }

            return self::renderTemplateToString($componentPath, $data);

        } catch (Exception $e) {
            if (self::$config['debug_mode']) {
                return "<!-- Error en componente {$component}: " . $e->getMessage() . " -->";
            }
            return '';
        }
    }

    /**
     * Renderiza un partial
     *
     * @param string $partial Nombre del partial
     * @param array $data Datos para el partial
     * @return string Contenido HTML del partial
     */
    public static function partial($partial, $data = [])
    {
        try {
            $partialPath = self::getPartialPath($partial);

            if (!file_exists($partialPath)) {
                if (self::$config['debug_mode']) {
                    return "<!-- Partial no encontrado: {$partial} -->";
                }
                return '';
            }

            return self::renderTemplateToString($partialPath, $data);

        } catch (Exception $e) {
            if (self::$config['debug_mode']) {
                return "<!-- Error en partial {$partial}: " . $e->getMessage() . " -->";
            }
            return '';
        }
    }

    // ==========================================
    // SISTEMA DE SLOTS
    // ==========================================

    /**
     * Define un slot en el layout
     *
     * @param string $name Nombre del slot
     * @param string $default Contenido por defecto
     * @return void
     */
    public static function slot($name, $default = '')
    {
        echo isset(self::$slots[$name]) ? self::$slots[$name] : $default;
    }

    /**
     * Establece contenido para un slot
     *
     * @param string $name Nombre del slot
     * @param string $content Contenido del slot
     * @return void
     */
    public static function setSlot($name, $content)
    {
        self::$slots[$name] = $content;
    }

    /**
     * Inicia la captura de contenido para un slot
     *
     * @param string $name Nombre del slot
     * @return void
     */
    public static function startSlot($name)
    {
        ob_start();
        self::$currentSlot = $name;
    }

    /**
     * Finaliza la captura de contenido para un slot
     *
     * @return void
     */
    public static function endSlot()
    {
        if (isset(self::$currentSlot)) {
            self::$slots[self::$currentSlot] = ob_get_clean();
            self::$currentSlot = null;
        }
    }

    // ==========================================
    // GESTIÓN DE ASSETS
    // ==========================================

    /**
     * Registra un archivo CSS
     *
     * @param string $href URL del archivo CSS
     * @param array $attributes Atributos adicionales
     * @return void
     */
    public static function addCSS($href, $attributes = [])
    {
        $css = array_merge([
            'href' => $href,
            'rel' => 'stylesheet',
            'type' => 'text/css'
        ], $attributes);

        self::$assets['css'][] = $css;
    }

    /**
     * Registra un archivo JavaScript
     *
     * @param string $src URL del archivo JS
     * @param string $position Posición (head|footer)
     * @param array $attributes Atributos adicionales
     * @return void
     */
    public static function addJS($src, $position = 'footer', $attributes = [])
    {
        $js = array_merge([
            'src' => $src,
            'type' => 'text/javascript'
        ], $attributes);

        if (!in_array($position, ['head', 'footer'])) {
            $position = 'footer';
        }

        self::$assets['js'][$position][] = $js;
    }

    /**
     * Añade una meta tag
     *
     * @param array $attributes Atributos de la meta tag
     * @return void
     */
    public static function addMeta($attributes)
    {
        self::$assets['meta'][] = $attributes;
    }

    /**
     * Renderiza los CSS registrados
     *
     * @return string HTML de los CSS
     */
    public static function renderCSS()
    {
        $html = '';
        foreach (self::$assets['css'] as $css) {
            $html .= '<link';
            foreach ($css as $attr => $value) {
                $html .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
            }
            $html .= '>' . "\n";
        }
        return $html;
    }

    /**
     * Renderiza los JavaScript registrados
     *
     * @param string $position Posición a renderizar
     * @return string HTML de los JS
     */
    public static function renderJS($position = 'footer')
    {
        $html = '';
        if (isset(self::$assets['js'][$position])) {
            foreach (self::$assets['js'][$position] as $js) {
                $html .= '<script';
                foreach ($js as $attr => $value) {
                    $html .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
                }
                $html .= '></script>' . "\n";
            }
        }
        return $html;
    }

    /**
     * Renderiza las meta tags registradas
     *
     * @return string HTML de las meta tags
     */
    public static function renderMeta()
    {
        $html = '';
        foreach (self::$assets['meta'] as $meta) {
            $html .= '<meta';
            foreach ($meta as $attr => $value) {
                $html .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
            }
            $html .= '>' . "\n";
        }
        return $html;
    }

    // ==========================================
    // MÉTODOS DE UTILIDAD INTERNOS
    // ==========================================

    /**
     * Prepara los datos del layout
     *
     * @return void
     */
    protected static function prepareLayoutData()
    {
        if (self::$layout_data) {
            foreach (self::$layout_data as $key => $value) {
                ${$key} = $value;
            }
        }

        if (self::$data) {
            foreach (self::$data as $key => $value) {
                ${$key} = $value;
            }
        }
    }

    /**
     * Prepara los datos de la página
     *
     * @return void
     */
    protected static function preparePageData()
    {
        if (self::$layout_data) {
            foreach (self::$layout_data as $key => $value) {
                ${$key} = $value;
            }
        }

        if (self::$data) {
            foreach (self::$data as $key => $value) {
                ${$key} = $value;
            }
        }
    }

    /**
     * Renderiza un template con datos
     *
     * @param string $templatePath Ruta del template
     * @param array $data Datos para el template
     * @return void
     */
    protected static function renderTemplate($templatePath, $data = [])
    {
        // Extraer variables para el template
        if ($data) {
            extract($data, EXTR_SKIP);
        }

        // Incluir el template
        include($templatePath);
    }

    /**
     * Renderiza un template y devuelve el contenido como string
     *
     * @param string $templatePath Ruta del template
     * @param array $data Datos para el template
     * @return string Contenido renderizado
     */
    protected static function renderTemplateToString($templatePath, $data = [])
    {
        // Extraer variables para el template
        if ($data) {
            extract($data, EXTR_SKIP);
        }

        // Capturar salida
        ob_start();
        include($templatePath);
        $content = ob_get_clean();

        // Minificar HTML si está habilitado
        if (self::$config['minify_html']) {
            $content = self::minifyHTML($content);
        }

        return $content;
    }

    /**
     * Obtiene la ruta del layout
     *
     * @param string $layout Nombre del layout
     * @return string Ruta completa del layout
     */
    protected static function getLayoutPath($layout)
    {
        return _PATH_ . self::$config['view_paths']['layouts'] . DIRECTORY_SEPARATOR . $layout . '.php';
    }

    /**
     * Obtiene la ruta de una página
     *
     * @param string $page Nombre de la página
     * @return string Ruta completa de la página
     */
    protected static function getPagePath($page)
    {
        return _PATH_ . self::$config['view_paths']['pages'] . DIRECTORY_SEPARATOR . $page . '.php';
    }

    /**
     * Obtiene la ruta de una página de administración
     *
     * @param string $page Nombre de la página
     * @return string Ruta completa de la página
     */
    protected static function getAdminPagePath($page)
    {
        return _PATH_ . self::$config['view_paths']['pages'] . DIRECTORY_SEPARATOR . _ADMIN_ . DIRECTORY_SEPARATOR . $page . '.php';
    }

    /**
     * Obtiene la ruta de un bloque
     *
     * @param string $block Nombre del bloque
     * @return string Ruta completa del bloque
     */
    protected static function getBlockPath($block)
    {
        return _PATH_ . self::$config['view_paths']['pages'] . DIRECTORY_SEPARATOR . $block . '.php';
    }

    /**
     * Obtiene la ruta de una página AJAX
     *
     * @param string $page Nombre de la página
     * @return string Ruta completa de la página
     */
    protected static function getAjaxPagePath($page)
    {
        return _PATH_ . self::$config['view_paths']['pages'] . DIRECTORY_SEPARATOR . 'ajax' . DIRECTORY_SEPARATOR . $page . '.php';
    }

    /**
     * Obtiene la ruta de una página PDF
     *
     * @param string $page Nombre de la página
     * @return string Ruta completa de la página
     */
    protected static function getPDFPagePath($page)
    {
        return _PATH_ . self::$config['view_paths']['pages'] . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $page . '.php';
    }

    /**
     * Obtiene la ruta de un componente
     *
     * @param string $component Nombre del componente
     * @return string Ruta completa del componente
     */
    protected static function getComponentPath($component)
    {
        return _PATH_ . self::$config['view_paths']['components'] . DIRECTORY_SEPARATOR . $component . '.php';
    }

    /**
     * Obtiene la ruta de un partial
     *
     * @param string $partial Nombre del partial
     * @return string Ruta completa del partial
     */
    protected static function getPartialPath($partial)
    {
        return _PATH_ . self::$config['view_paths']['partials'] . DIRECTORY_SEPARATOR . $partial . '.php';
    }

    /**
     * Maneja errores de renderizado
     *
     * @param Exception $e Excepción capturada
     * @param string $type Tipo de error
     * @return void
     */
    protected static function handleRenderError($e, $type)
    {
        $message = "Error de renderizado ({$type}): " . $e->getMessage();

        // Log del error
        if (function_exists('__log_error')) {
            __log_error($message);
        } else {
            error_log($message);
        }

        // Mostrar error en modo debug
        if (self::$config['debug_mode']) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px;'>";
            echo "<strong>Error de Renderizado:</strong> " . htmlspecialchars($e->getMessage());
            echo "<br><strong>Archivo:</strong> " . htmlspecialchars($e->getFile());
            echo "<br><strong>Línea:</strong> " . $e->getLine();
            echo "</div>";
        } else {
            echo "<div>Error cargando el contenido.</div>";
        }
    }

    /**
     * Minifica HTML eliminando espacios innecesarios
     *
     * @param string $html HTML a minificar
     * @return string HTML minificado
     */
    protected static function minifyHTML($html)
    {
        // Eliminar comentarios HTML
        $html = preg_replace('/<!--(?!<!)[^\[>].*?-->/s', '', $html);

        // Eliminar espacios en blanco innecesarios
        $html = preg_replace('/\s+/', ' ', $html);

        // Eliminar espacios alrededor de tags
        $html = preg_replace('/>\s+</', '><', $html);

        return trim($html);
    }

    // ==========================================
    // MÉTODOS ESTÁTICOS DE UTILIDAD
    // ==========================================

    /**
     * Limpia el cache de vistas
     *
     * @return bool
     */
    public static function clearCache()
    {
        self::$viewCache = [];

        // Limpiar cache de archivos si existe
        $cacheDir = _PATH_ . 'cache/views/';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '*.php');
            foreach ($files as $file) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * Obtiene información de debug del renderizador
     *
     * @return array
     */
    public static function getDebugInfo()
    {
        return [
            'config' => self::$config,
            'current_page' => self::$page,
            'current_layout' => self::$layout,
            'slots' => array_keys(self::$slots),
            'assets' => [
                'css_count' => count(self::$assets['css']),
                'js_head_count' => count(self::$assets['js']['head']),
                'js_footer_count' => count(self::$assets['js']['footer']),
                'meta_count' => count(self::$assets['meta'])
            ]
        ];
    }

    /**
     * Establece configuración del renderizador
     *
     * @param string $key Clave de configuración
     * @param mixed $value Valor de configuración
     * @return void
     */
    public static function setConfig($key, $value)
    {
        self::$config[$key] = $value;
    }

    /**
     * Obtiene configuración del renderizador
     *
     * @param string $key Clave de configuración
     * @param mixed $default Valor por defecto
     * @return mixed
     */
    public static function getConfig($key, $default = null)
    {
        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }

    /**
     * Sanitiza string para prevenir XSS
     *
     * @param string $string String a sanitizar
     * @param bool $allowHtml Permitir HTML básico
     * @return string String sanitizado
     */
    public static function sanitizeString($string, $allowHtml = false)
    {
        if (empty($string)) {
            return '';
        }

        if ($allowHtml) {
            // Permitir solo tags básicos seguros
            $allowedTags = '<p><br><strong><em><u><a><ul><ol><li>';
            return strip_tags($string, $allowedTags);
        }

        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Convierte array a UTF-8
     *
     * @param array $array Array a convertir
     * @return array Array convertido
     */
    public static function arrayUtf8($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = self::arrayUtf8($value);
                } elseif (is_string($value)) {
                    $array[$key] = mb_convert_encoding($value, 'UTF-8', 'auto');
                }
            }
        }
        return $array;
    }

    /**
     * Formatea bytes a formato legible
     *
     * @param int $bytes Bytes a formatear
     * @param int $precision Precisión decimal
     * @return string Tamaño formateado
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Genera respuesta JSON estandarizada
     *
     * @param mixed $data Datos a enviar
     * @param string $status Estado de la respuesta
     * @param string $message Mensaje opcional
     * @param int $httpCode Código HTTP
     * @return void
     */
    public static function jsonResponse($data = null, $status = 'success', $message = '', $httpCode = 200)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=UTF-8');

        $response = [
            'status' => $status,
            'timestamp' => time(),
            'data' => $data
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        echo json_encode(self::arrayUtf8($response), JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Genera alerta HTML
     *
     * @param string $message Mensaje de la alerta
     * @param string $type Tipo de alerta (success, error, warning, info)
     * @param bool $dismissible Permitir cerrar
     * @return string HTML de la alerta
     */
    public static function alert($message, $type = 'info', $dismissible = true)
    {
        $classes = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];

        $class = $classes[$type] ?? $classes['info'];
        $dismissBtn = $dismissible ? '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' : '';

        return "<div class=\"alert {$class} alert-dismissible\" role=\"alert\">{$message}{$dismissBtn}</div>";
    }

    /**
     * Obtiene y limpia alerta de sesión
     *
     * @return array|null Datos de la alerta
     */
    public static function getAlert()
    {
        if (isset($_SESSION['alert'])) {
            $alert = $_SESSION['alert'];
            unset($_SESSION['alert']);
            return $alert;
        }
        return null;
    }
}
