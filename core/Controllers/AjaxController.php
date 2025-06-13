<?php

/**
 * Controlador AJAX para el frontend
 *
 * Maneja todas las peticiones AJAX del frontend de la aplicación,
 * incluyendo operaciones de búsqueda, filtros, formularios y acciones específicas.
 */
class AjaxController extends Controllers
{
    /**
     * Variables de paginación
     */
    var $comienzo = 0;
    var $limite = 10;
    var $pagina = 1;

    /**
     * Configuración del controlador
     */
    protected $config = [
        'csrf_protection' => true,
        'rate_limit' => [
            'enabled' => true,
            'max_requests' => 100,
            'time_window' => 60 // segundos
        ],
        'allowed_actions' => [
            // Acciones de prueba
            'ajax-test-get',
            'ajax-test-post',

            // Búsquedas y filtros
            'ajax-search-mascotas',
            'ajax-search-cuidadores',
            'ajax-filter-content',

            // Formularios
            'ajax-contact-form',
            'ajax-newsletter-subscribe',
            'ajax-feedback-form',

            // Contenido dinámico
            'ajax-load-more-content',
            'ajax-get-mascota-info',
            'ajax-get-cuidador-info',

            // Utilidades
            'ajax-upload-image',
            'ajax-validate-form',
            'ajax-get-location-data'
        ]
    ];

    /**
     * Ejecuta el controlador AJAX
     *
     * @param string $page Acción AJAX solicitada
     * @return void
     */
    public function execute($page)
    {
        // Configurar layout
        Render::$layout = false;

        // Verificar rate limiting
        $this->checkRateLimit();

        // Validar acción
        $this->validateAction($page);

        // Configurar headers de respuesta
        $this->setupResponseHeaders();

        // Definir rutas AJAX
        $this->defineAjaxRoutes();

        // Si no se encontró la ruta, devolver error 404
        if (!$this->getRendered()) {
            $this->sendError('Acción no encontrada', 404);
        }
    }

    /**
     * Verifica el rate limiting para peticiones AJAX
     *
     * @return void
     */
    protected function checkRateLimit()
    {
        if (!$this->config['rate_limit']['enabled']) {
            return;
        }

        $ip = $this->getClientIP();
        $key = 'ajax_rate_limit_' . md5($ip);

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'last_request' => time()
            ];
        } else {
            $timeDiff = time() - $_SESSION[$key]['last_request'];

            if ($timeDiff > $this->config['rate_limit']['time_window']) {
                $_SESSION[$key] = [
                    'count' => 1,
                    'last_request' => time()
                ];
            } else {
                $_SESSION[$key]['count']++;
                $_SESSION[$key]['last_request'] = time();

                if ($_SESSION[$key]['count'] > $this->config['rate_limit']['max_requests']) {
                    $this->sendError('Demasiadas peticiones', 429);
                }
            }
        }
    }

    /**
     * Valida que la acción esté permitida
     *
     * @param string $action Acción a validar
     * @return void
     */
    protected function validateAction($action)
    {
        if (!in_array($action, $this->config['allowed_actions'])) {
            $this->log("Acción no permitida intentada: {$action}", 'warning');
            $this->sendError('Acción no permitida', 403);
        }
    }

    /**
     * Configura headers de respuesta
     *
     * @return void
     */
    protected function setupResponseHeaders()
    {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }

    /**
     * Define todas las rutas AJAX
     *
     * @return void
     */
    protected function defineAjaxRoutes()
    {
        // ==========================================
        // ACCIONES DE PRUEBA (LEGACY)
        // ==========================================

        $this->add('ajax-test-get', [$this, 'testGetAction']);
        $this->add('ajax-test-post', [$this, 'testPostAction']);

        // ==========================================
        // BÚSQUEDAS Y FILTROS
        // ==========================================

        $this->add('ajax-search-mascotas', [$this, 'searchMascotas']);
        $this->add('ajax-search-cuidadores', [$this, 'searchCuidadores']);
        $this->add('ajax-filter-content', [$this, 'filterContent']);

        // ==========================================
        // FORMULARIOS
        // ==========================================

        $this->add('ajax-contact-form', [$this, 'processContactForm']);
        $this->add('ajax-newsletter-subscribe', [$this, 'processNewsletterSubscribe']);
        $this->add('ajax-feedback-form', [$this, 'processFeedbackForm']);

        // ==========================================
        // CONTENIDO DINÁMICO
        // ==========================================

        $this->add('ajax-load-more-content', [$this, 'loadMoreContent']);
        $this->add('ajax-get-mascota-info', [$this, 'getMascotaInfo']);
        $this->add('ajax-get-cuidador-info', [$this, 'getCuidadorInfo']);

        // ==========================================
        // UTILIDADES
        // ==========================================

        $this->add('ajax-upload-image', [$this, 'uploadImage']);
        $this->add('ajax-validate-form', [$this, 'validateForm']);
        $this->add('ajax-get-location-data', [$this, 'getLocationData']);
    }

    // ==========================================
    // ACCIONES DE PRUEBA (LEGACY)
    // ==========================================

    /**
     * Acción de prueba GET (mantenida por compatibilidad)
     *
     * @return void
     */
    public function testGetAction()
    {
        try {
            $data = [
                'var1' => 'PRUEBA GET',
                'timestamp' => time(),
                'method' => 'GET'
            ];

            $html = Render::getAjaxPage('test_ajax', $data);

            if (!empty($html)) {
                $this->sendSuccess(['html' => $html]);
            } else {
                $this->sendError('Error cargando el contenido HTML');
            }
        } catch (Exception $e) {
            $this->log("Error en testGetAction: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Acción de prueba POST (mantenida por compatibilidad)
     *
     * @return void
     */
    public function testPostAction()
    {
        try {
            $data = [
                'var1' => Tools::getValue('var1', 'PRUEBA POST'),
                'timestamp' => time(),
                'method' => 'POST'
            ];

            $html = Render::getAjaxPage('test_ajax', $data);

            if (!empty($html)) {
                $this->sendSuccess(['html' => $html]);
            } else {
                $this->sendError('Error cargando el contenido HTML');
            }
        } catch (Exception $e) {
            $this->log("Error en testPostAction: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    // ==========================================
    // BÚSQUEDAS Y FILTROS
    // ==========================================

    /**
     * Busca mascotas por criterios
     *
     * @return void
     */
    public function searchMascotas()
    {
        try {
            $query = Tools::getValue('q', '');
            $tipo = Tools::getValue('tipo', '');
            $genero = Tools::getValue('genero', '');
            $limite = (int)Tools::getValue('limite', 10);

            if (strlen($query) < 2) {
                $this->sendError('La búsqueda debe tener al menos 2 caracteres');
                return;
            }

            // Aquí iría la lógica de búsqueda real
            // Por ahora simulamos resultados
            $resultados = [
                [
                    'id' => 1,
                    'nombre' => 'Luna',
                    'tipo' => 'Perro',
                    'genero' => 'Hembra',
                    'edad' => 3
                ],
                [
                    'id' => 2,
                    'nombre' => 'Max',
                    'tipo' => 'Gato',
                    'genero' => 'Macho',
                    'edad' => 2
                ]
            ];

            $this->sendSuccess([
                'resultados' => $resultados,
                'total' => count($resultados),
                'query' => $query
            ]);
        } catch (Exception $e) {
            $this->log("Error en searchMascotas: " . $e->getMessage(), 'error');
            $this->sendError('Error en la búsqueda');
        }
    }

    /**
     * Busca cuidadores por criterios
     *
     * @return void
     */
    public function searchCuidadores()
    {
        try {
            $query = Tools::getValue('q', '');
            $ubicacion = Tools::getValue('ubicacion', '');
            $limite = (int)Tools::getValue('limite', 10);

            if (strlen($query) < 2) {
                $this->sendError('La búsqueda debe tener al menos 2 caracteres');
                return;
            }

            // Usar la clase Cuidador para buscar
            $resultados = Cuidador::buscarCuidadores($query, $limite);

            $this->sendSuccess([
                'resultados' => $resultados,
                'total' => count($resultados),
                'query' => $query
            ]);
        } catch (Exception $e) {
            $this->log("Error en searchCuidadores: " . $e->getMessage(), 'error');
            $this->sendError('Error en la búsqueda');
        }
    }

    /**
     * Filtra contenido por criterios
     *
     * @return void
     */
    public function filterContent()
    {
        try {
            $tipo = Tools::getValue('tipo', '');
            $categoria = Tools::getValue('categoria', '');
            $orden = Tools::getValue('orden', 'fecha_desc');
            $pagina = (int)Tools::getValue('pagina', 1);
            $limite = (int)Tools::getValue('limite', 10);

            $comienzo = ($pagina - 1) * $limite;

            // Aquí iría la lógica de filtrado real
            $data = [
                'tipo' => $tipo,
                'categoria' => $categoria,
                'orden' => $orden,
                'pagina' => $pagina,
                'limite' => $limite
            ];

            $html = Render::getAjaxPage('filtered_content', $data);

            if (!empty($html)) {
                $this->sendSuccess(['html' => $html]);
            } else {
                $this->sendError('No se encontraron resultados');
            }
        } catch (Exception $e) {
            $this->log("Error en filterContent: " . $e->getMessage(), 'error');
            $this->sendError('Error al filtrar contenido');
        }
    }

    // ==========================================
    // FORMULARIOS
    // ==========================================

    /**
     * Procesa formulario de contacto
     *
     * @return void
     */
    public function processContactForm()
    {
        try {
            $this->validateRequiredFields(['nombre', 'email', 'mensaje']);

            $nombre = Tools::getValue('nombre');
            $email = Tools::getValue('email');
            $telefono = Tools::getValue('telefono', '');
            $asunto = Tools::getValue('asunto', 'Contacto desde web');
            $mensaje = Tools::getValue('mensaje');

            // Validar email
            if (!Tools::isEmail($email)) {
                $this->sendError('El email no tiene un formato válido');
                return;
            }

            // Validar teléfono si se proporciona
            if (!empty($telefono) && !Tools::isPhone($telefono)) {
                $this->sendError('El teléfono no tiene un formato válido');
                return;
            }

            // Aquí iría la lógica para enviar el email
            // Por ahora simulamos el envío exitoso

            $this->log("Formulario de contacto enviado por: {$email}", 'info');
            $this->sendSuccess([
                'message' => 'Tu mensaje ha sido enviado correctamente. Te responderemos pronto.'
            ]);
        } catch (Exception $e) {
            $this->log("Error en processContactForm: " . $e->getMessage(), 'error');
            $this->sendError('Error al enviar el mensaje');
        }
    }

    /**
     * Procesa suscripción a newsletter
     *
     * @return void
     */
    public function processNewsletterSubscribe()
    {
        try {
            $this->validateRequiredFields(['email']);

            $email = Tools::getValue('email');
            $nombre = Tools::getValue('nombre', '');

            // Validar email
            if (!Tools::isEmail($email)) {
                $this->sendError('El email no tiene un formato válido');
                return;
            }

            // Verificar si ya está suscrito
            // Aquí iría la lógica para verificar en base de datos

            // Simular suscripción exitosa
            $this->log("Nueva suscripción al newsletter: {$email}", 'info');
            $this->sendSuccess([
                'message' => '¡Gracias por suscribirte! Recibirás nuestras novedades en tu email.'
            ]);
        } catch (Exception $e) {
            $this->log("Error en processNewsletterSubscribe: " . $e->getMessage(), 'error');
            $this->sendError('Error al procesar la suscripción');
        }
    }

    /**
     * Procesa formulario de feedback
     *
     * @return void
     */
    public function processFeedbackForm()
    {
        try {
            $this->validateRequiredFields(['rating', 'comentario']);

            $rating = (int)Tools::getValue('rating');
            $comentario = Tools::getValue('comentario');
            $email = Tools::getValue('email', '');

            // Validar rating
            if ($rating < 1 || $rating > 5) {
                $this->sendError('La valoración debe estar entre 1 y 5');
                return;
            }

            // Validar email si se proporciona
            if (!empty($email) && !Tools::isEmail($email)) {
                $this->sendError('El email no tiene un formato válido');
                return;
            }

            // Aquí iría la lógica para guardar el feedback

            $this->log("Nuevo feedback recibido con rating: {$rating}", 'info');
            $this->sendSuccess([
                'message' => 'Gracias por tu feedback. Nos ayuda a mejorar.'
            ]);
        } catch (Exception $e) {
            $this->log("Error en processFeedbackForm: " . $e->getMessage(), 'error');
            $this->sendError('Error al enviar el feedback');
        }
    }

    // ==========================================
    // CONTENIDO DINÁMICO
    // ==========================================

    /**
     * Carga más contenido (paginación infinita)
     *
     * @return void
     */
    public function loadMoreContent()
    {
        try {
            $tipo = Tools::getValue('tipo', 'general');
            $pagina = (int)Tools::getValue('pagina', 1);
            $limite = (int)Tools::getValue('limite', 10);

            $comienzo = ($pagina - 1) * $limite;

            $data = [
                'tipo' => $tipo,
                'pagina' => $pagina,
                'limite' => $limite,
                'comienzo' => $comienzo
            ];

            $html = Render::getAjaxPage('load_more_content', $data);

            if (!empty($html)) {
                $this->sendSuccess([
                    'html' => $html,
                    'hasMore' => true, // Aquí iría la lógica real para determinar si hay más contenido
                    'nextPage' => $pagina + 1
                ]);
            } else {
                $this->sendSuccess([
                    'html' => '',
                    'hasMore' => false,
                    'message' => 'No hay más contenido disponible'
                ]);
            }
        } catch (Exception $e) {
            $this->log("Error en loadMoreContent: " . $e->getMessage(), 'error');
            $this->sendError('Error al cargar más contenido');
        }
    }

    /**
     * Obtiene información de una mascota
     *
     * @return void
     */
    public function getMascotaInfo()
    {
        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de mascota no válido');
                return;
            }

            // Usar la clase Mascotas para obtener información
            $mascota = Mascotas::getMascotaById($id);

            if (!$mascota) {
                $this->sendError('Mascota no encontrada');
                return;
            }

            $this->sendSuccess([
                'mascota' => $mascota
            ]);
        } catch (Exception $e) {
            $this->log("Error en getMascotaInfo: " . $e->getMessage(), 'error');
            $this->sendError('Error al obtener información de la mascota');
        }
    }

    /**
     * Obtiene información de un cuidador
     *
     * @return void
     */
    public function getCuidadorInfo()
    {
        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de cuidador no válido');
                return;
            }

            // Usar la clase Cuidador para obtener información
            $cuidador = Cuidador::getCuidadorById($id);

            if (!$cuidador) {
                $this->sendError('Cuidador no encontrado');
                return;
            }

            $this->sendSuccess([
                'cuidador' => $cuidador
            ]);
        } catch (Exception $e) {
            $this->log("Error en getCuidadorInfo: " . $e->getMessage(), 'error');
            $this->sendError('Error al obtener información del cuidador');
        }
    }

    // ==========================================
    // UTILIDADES
    // ==========================================

    /**
     * Sube una imagen
     *
     * @return void
     */
    public function uploadImage()
    {
        try {
            if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
                $this->sendError('Error al subir la imagen');
                return;
            }

            $file = $_FILES['imagen'];
            $extension = Tools::getExtension($file['name']);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($extension, $allowedExtensions)) {
                $this->sendError('Formato de imagen no permitido');
                return;
            }

            // Verificar tamaño (máximo 5MB)
            if ($file['size'] > 5242880) {
                $this->sendError('La imagen es demasiado grande (máximo 5MB)');
                return;
            }

            // Generar nombre único
            $fileName = uniqid() . '.' . $extension;
            $uploadPath = _PATH_ . 'uploads/images/';

            // Crear directorio si no existe
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $fullPath = $uploadPath . $fileName;

            if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                $this->log("Imagen subida: {$fileName}", 'info');
                $this->sendSuccess([
                    'message' => 'Imagen subida correctamente',
                    'filename' => $fileName,
                    'url' => _DOMINIO_ . 'uploads/images/' . $fileName
                ]);
            } else {
                $this->sendError('Error al guardar la imagen');
            }
        } catch (Exception $e) {
            $this->log("Error en uploadImage: " . $e->getMessage(), 'error');
            $this->sendError('Error interno del servidor');
        }
    }

    /**
     * Valida un formulario
     *
     * @return void
     */
    public function validateForm()
    {
        try {
            $formType = Tools::getValue('form_type');
            $formData = Tools::getValue('form_data', []);

            $errors = [];
            $valid = true;

            switch ($formType) {
                case 'contact':
                    if (empty($formData['nombre'])) {
                        $errors['nombre'] = 'El nombre es obligatorio';
                        $valid = false;
                    }
                    if (empty($formData['email'])) {
                        $errors['email'] = 'El email es obligatorio';
                        $valid = false;
                    } elseif (!Tools::isEmail($formData['email'])) {
                        $errors['email'] = 'El email no tiene un formato válido';
                        $valid = false;
                    }
                    if (empty($formData['mensaje'])) {
                        $errors['mensaje'] = 'El mensaje es obligatorio';
                        $valid = false;
                    }
                    break;

                case 'newsletter':
                    if (empty($formData['email'])) {
                        $errors['email'] = 'El email es obligatorio';
                        $valid = false;
                    } elseif (!Tools::isEmail($formData['email'])) {
                        $errors['email'] = 'El email no tiene un formato válido';
                        $valid = false;
                    }
                    break;

                default:
                    $this->sendError('Tipo de formulario no válido');
                    return;
            }

            $this->sendSuccess([
                'valid' => $valid,
                'errors' => $errors
            ]);
        } catch (Exception $e) {
            $this->log("Error en validateForm: " . $e->getMessage(), 'error');
            $this->sendError('Error al validar el formulario');
        }
    }

    /**
     * Obtiene datos de ubicación
     *
     * @return void
     */
    public function getLocationData()
    {
        try {
            $query = Tools::getValue('q', '');

            if (strlen($query) < 3) {
                $this->sendError('La búsqueda debe tener al menos 3 caracteres');
                return;
            }

            // Aquí iría la integración con un servicio de geolocalización
            // Por ahora simulamos datos
            $locations = [
                [
                    'name' => 'Madrid, España',
                    'lat' => 40.4168,
                    'lng' => -3.7038
                ],
                [
                    'name' => 'Barcelona, España',
                    'lat' => 41.3851,
                    'lng' => 2.1734
                ]
            ];

            $this->sendSuccess([
                'locations' => $locations,
                'query' => $query
            ]);
        } catch (Exception $e) {
            $this->log("Error en getLocationData: " . $e->getMessage(), 'error');
            $this->sendError('Error al obtener datos de ubicación');
        }
    }

    // ==========================================
    // MÉTODOS DE UTILIDAD
    // ==========================================

    /**
     * Valida que los campos requeridos estén presentes
     *
     * @param array $fields Campos requeridos
     * @return void
     * @throws Exception Si falta algún campo
     */
    protected function validateRequiredFields($fields)
    {
        foreach ($fields as $field) {
            if (!Tools::getIsset($field) || empty(Tools::getValue($field))) {
                throw new Exception("Campo requerido faltante: {$field}");
            }
        }
    }

    /**
     * Envía respuesta de éxito
     *
     * @param array $data Datos a enviar
     * @return void
     */
    protected function sendSuccess($data = [])
    {
        $response = array_merge(['type' => 'success'], $data);
        die(json_encode($response));
    }

    /**
     * Envía respuesta de error
     *
     * @param string $message Mensaje de error
     * @param int $code Código HTTP
     * @return void
     */
    protected function sendError($message, $code = 400)
    {
        http_response_code($code);
        die(json_encode([
            'type' => 'error',
            'error' => $message,
            'code' => $code
        ]));
    }

    /**
     * Carga las traducciones para el frontend
     *
     * @return void
     */
    protected function loadTraducciones()
    {
        if (isset($_SESSION['id_lang'])) {
            Traducciones::loadTraducciones($_SESSION['id_lang']);
        }
    }
}
