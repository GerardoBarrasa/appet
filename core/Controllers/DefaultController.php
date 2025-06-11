<?php

/**
 * Controlador principal para el frontend de la aplicación
 *
 * Este controlador maneja todas las rutas públicas del sitio web
 */
class DefaultController extends Controllers
{
    /**
     * Configuración del controlador
     */
    protected $config = [
        'layout' => 'front-end',
        'assets' => [
            'js' => [
                'jquery/jquery.min.js'
            ],
            'css' => []
        ]
    ];

    /**
     * Ejecuta el controlador para la ruta solicitada
     *
     * @param string $page Página solicitada
     * @return void
     */
    public function execute($page)
    {
        // Si la aplicación está configurada para redireccionar todo al admin
        if (defined('_REDIRECT_TO_ADMIN_') && _REDIRECT_TO_ADMIN_ === true) {
            header('Location: ' . _DOMINIO_._ADMIN_);
            exit;
        }

        // Inicializar el controlador
        $this->initialize($page);

        // Definir rutas
        $this->defineRoutes();

        // Si no se encontró ninguna ruta, mostrar 404
        if (!$this->getRendered()) {
            $this->redirectTo404();
        }
    }

    /**
     * Inicializa el controlador con la configuración necesaria
     *
     * @param string $page Página solicitada
     * @return void
     */
    protected function initialize($page)
    {
        // Configurar layout
        Render::$layout = $this->config['layout'];

        // Registrar assets
        foreach ($this->config['assets']['js'] as $js) {
            Tools::registerJavascript(_JS_._PUBLIC_.$js);
        }

        foreach ($this->config['assets']['css'] as $css) {
            Tools::registerStylesheet(_CSS_._PUBLIC_.$css);
        }

        // Cargar idiomas
        $idiomas = $this->loadLanguages();

        // Configurar datos del layout
        Render::$layout_data = [
            'page_name' => empty($page) ? 'home' : $page,
            'idiomas' => $idiomas
        ];

        // Configurar metadatos
        $this->setupMetadata($page);
    }

    /**
     * Carga los idiomas disponibles y configura sus slugs
     *
     * @return array Idiomas disponibles
     */
    protected function loadLanguages()
    {
        $idiomas = Idiomas::getLanguages();

        foreach ($idiomas as &$idioma) {
            $idioma->slug_complete = Slugs::getSlugCompleteForIdLang($idioma);
        }

        return $idiomas;
    }

    /**
     * Configura los metadatos para SEO
     *
     * @param string $page Página actual
     * @return void
     */
    protected function setupMetadata($page)
    {
        $pageId = empty($page) ? 'home' : $page;
        $metaData = Slugs::getPageDataByModId($pageId);

        if (!empty($metaData)) {
            Metas::$title = isset($metaData->title) ? $metaData->title : _TITULO_;
            Metas::$description = isset($metaData->description) ? $metaData->description : _TITULO_;

            // Configurar palabras clave si existen
            if (isset($metaData->keywords) && !empty($metaData->keywords)) {
                Metas::$keywords = $metaData->keywords;
            }
        } else {
            // Si no hay metadatos, redirigir a la página de inicio
            header('Location:' . _DOMINIO_ . $_SESSION['lang'] . "/");
            exit;
        }
    }

    /**
     * Define las rutas disponibles en el controlador
     *
     * @return void
     */
    protected function defineRoutes()
    {
        // Página de inicio
        $this->add('', [$this, 'homeAction']);

        // Página de prueba
        $this->add('test', [$this, 'testAction']);

        // Página 404
        $this->add('404', [$this, 'notFoundAction']);

        // Aquí se pueden añadir más rutas
        // $this->add('contacto', [$this, 'contactAction']);
        // $this->add('sobre-nosotros', [$this, 'aboutAction']);
    }

    /**
     * Redirige a la página 404
     *
     * @return void
     */
    protected function redirectTo404()
    {
        header('Location: ' . _DOMINIO_ . $_SESSION['lang'] . '/404/');
        exit;
    }

    /**
     * Acción para la página de inicio
     *
     * @return void
     */
    public function homeAction()
    {
        $mpc = new Miprimeraclase();
        $datos_idiomas = Idiomas::getLanguages();

        // Array de datos a enviar a la página
        $data = [
            'datos_idiomas' => $datos_idiomas,
            'test' => $mpc->getMessage(),
            'current_page' => 'home'
        ];

        Render::page('home', $data);
    }

    /**
     * Acción para la página de prueba
     *
     * @return void
     */
    public function testAction()
    {
        $mpc = new Miprimeraclase();
        $datos_idiomas = Idiomas::getLanguages();

        // Array de datos a enviar a la página
        $data = [
            'datos_idiomas' => $datos_idiomas,
            'test' => $mpc->getMessage(),
            'current_page' => 'test'
        ];

        Render::page('home', $data);
    }

    /**
     * Acción para la página 404
     *
     * @return void
     */
    public function notFoundAction()
    {
        // Establecer código de estado HTTP 404
        http_response_code(404);

        Render::page('404', [
            'current_page' => '404'
        ]);
    }
}
