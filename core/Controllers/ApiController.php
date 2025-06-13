<?php

/**
 * Controlador de API REST
 *
 * Proporciona endpoints de API para acceso externo a los datos del sistema.
 * Incluye autenticación, versionado, rate limiting y respuestas estandarizadas.
 */
class ApiController extends Controllers
{
    /**
     * Configuración del controlador API
     */
    protected $config = [
        'version' => 'v1',
        'rate_limit' => [
            'enabled' => true,
            'max_requests' => 1000,
            'time_window' => 3600 // 1 hora
        ],
        'pagination' => [
            'default_limit' => 20,
            'max_limit' => 100
        ],
        'cors' => [
            'enabled' => true,
            'allowed_origins' => ['*'],
            'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'allowed_headers' => ['Content-Type', 'Authorization', 'X-API-Key']
        ],
        'auth' => [
            'required' => true,
            'methods' => ['token', 'api_key']
        ]
    ];

    /**
     * Endpoints disponibles y sus configuraciones
     */
    protected $availableEndpoints = [
        // Información de la API
        'info' => [
            'method' => 'GET',
            'auth_required' => false,
            'description' => 'Información de la API'
        ],
        'status' => [
            'method' => 'GET',
            'auth_required' => false,
            'description' => 'Estado de la API'
        ],

        // Mascotas
        'mascotas' => [
            'method' => 'GET',
            'auth_required' => true,
            'description' => 'Lista de mascotas'
        ],
        'mascota' => [
            'method' => 'GET',
            'auth_required' => true,
            'description' => 'Información de una mascota específica'
        ],
        'mascotas/search' => [
            'method' => 'GET',
            'auth_required' => true,
            'description' => 'Búsqueda de mascotas'
        ],

        // Cuidadores
        'cuidadores' => [
            'method' => 'GET',
            'auth_required' => true,
            'description' => 'Lista de cuidadores'
        ],
        'cuidador' => [
            'method' => 'GET',
            'auth_required' => true,
            'description' => 'Información de un cuidador específico'
        ],
        'cuidadores/search' => [
            'method' => 'GET',
            'auth_required' => true,
            'description' => 'Búsqueda de cuidadores'
        ],

        // Características
        'caracteristicas' => [
            'method' => 'GET',
            'auth_required' => true,
            'description' => 'Lista de características'
        ],

        // Estadísticas
        'stats' => [
            'method' => 'GET',
            'auth_required' => true,
            'description' => 'Estadísticas del sistema'
        ],

        // Utilidades
        'validate' => [
            'method' => 'POST',
            'auth_required' => true,
            'description' => 'Validación de datos'
        ]
    ];

    /**
     * Información del usuario autenticado
     */
    protected $authenticatedUser = null;

    /**
     * Ejecuta el controlador de API
     *
     * @param string $page Endpoint solicitado
     * @return void
     */
    public function execute($page)
    {
        // Sin layout para API
        Render::$layout = false;

        // Configurar headers CORS
        $this->setupCorsHeaders();

        // Manejar peticiones OPTIONS (preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $this->handleOptionsRequest();
            return;
        }

        // Configurar headers de respuesta
        $this->setupResponseHeaders();

        // Verificar rate limiting
        $this->checkRateLimit();

        // Log de la petición
        $this->logApiRequest($page);

        // Definir endpoints de la API
        $this->defineApiEndpoints();

        // Si no se encontró el endpoint, devolver error 404
        if (!$this->getRendered()) {
            $this->sendError('Endpoint no encontrado', 404);
        }
    }

    /**
     * Configura headers CORS
     *
     * @return void
     */
    protected function setupCorsHeaders()
    {
        if (!$this->config['cors']['enabled']) {
            return;
        }

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowedOrigins = $this->config['cors']['allowed_origins'];

        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: " . ($origin ?: '*'));
        }

        header('Access-Control-Allow-Methods: ' . implode(', ', $this->config['cors']['allowed_methods']));
        header('Access-Control-Allow-Headers: ' . implode(', ', $this->config['cors']['allowed_headers']));
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    }

    /**
     * Maneja peticiones OPTIONS (preflight)
     *
     * @return void
     */
    protected function handleOptionsRequest()
    {
        http_response_code(200);
        exit;
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
        header('X-API-Version: ' . $this->config['version']);
        header('X-RateLimit-Limit: ' . $this->config['rate_limit']['max_requests']);
    }

    /**
     * Verifica rate limiting
     *
     * @return void
     */
    protected function checkRateLimit()
    {
        if (!$this->config['rate_limit']['enabled']) {
            return;
        }

        $ip = $this->getClientIP();
        $key = 'api_rate_limit_' . md5($ip);

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'reset_time' => time() + $this->config['rate_limit']['time_window']
            ];
        } else {
            if (time() > $_SESSION[$key]['reset_time']) {
                $_SESSION[$key] = [
                    'count' => 1,
                    'reset_time' => time() + $this->config['rate_limit']['time_window']
                ];
            } else {
                $_SESSION[$key]['count']++;

                if ($_SESSION[$key]['count'] > $this->config['rate_limit']['max_requests']) {
                    header('X-RateLimit-Remaining: 0');
                    header('X-RateLimit-Reset: ' . $_SESSION[$key]['reset_time']);
                    $this->sendError('Rate limit exceeded', 429);
                }
            }
        }

        $remaining = max(0, $this->config['rate_limit']['max_requests'] - $_SESSION[$key]['count']);
        header('X-RateLimit-Remaining: ' . $remaining);
        header('X-RateLimit-Reset: ' . $_SESSION[$key]['reset_time']);
    }

    /**
     * Verifica autenticación
     *
     * @param bool $required Si la autenticación es requerida
     * @return bool
     */
    protected function checkAuthentication($required = true)
    {
        if (!$required) {
            return true;
        }

        $token = $this->getAuthToken();

        if (empty($token)) {
            if ($required) {
                $this->sendError('Token de autenticación requerido', 401);
            }
            return false;
        }

        // Verificar token de API
        if ($token !== _API_TOKEN_) {
            if ($required) {
                $this->sendError('Token de autenticación inválido', 401);
            }
            return false;
        }

        // Aquí podrías cargar información del usuario autenticado
        $this->authenticatedUser = [
            'token' => $token,
            'authenticated_at' => time()
        ];

        return true;
    }

    /**
     * Obtiene el token de autenticación
     *
     * @return string|null
     */
    protected function getAuthToken()
    {
        // Verificar en header Authorization
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        // Verificar en header X-API-Key
        if (!empty($_SERVER['HTTP_X_API_KEY'])) {
            return $_SERVER['HTTP_X_API_KEY'];
        }

        // Verificar en parámetro GET/POST
        return Tools::getValue('token');
    }

    /**
     * Define todos los endpoints de la API
     *
     * @return void
     */
    protected function defineApiEndpoints()
    {
        // ==========================================
        // INFORMACIÓN DE LA API
        // ==========================================

        $this->add('', [$this, 'apiInfo']);
        $this->add('info', [$this, 'apiInfo']);
        $this->add('status', [$this, 'apiStatus']);
        $this->add('endpoints', [$this, 'apiEndpoints']);

        // ==========================================
        // MASCOTAS
        // ==========================================

        $this->add('mascotas', [$this, 'getMascotas']);
        $this->add('mascota', [$this, 'getMascota']);
        $this->add('mascotas/search', [$this, 'searchMascotas']);

        // ==========================================
        // CUIDADORES
        // ==========================================

        $this->add('cuidadores', [$this, 'getCuidadores']);
        $this->add('cuidador', [$this, 'getCuidador']);
        $this->add('cuidadores/search', [$this, 'searchCuidadores']);

        // ==========================================
        // CARACTERÍSTICAS
        // ==========================================

        $this->add('caracteristicas', [$this, 'getCaracteristicas']);

        // ==========================================
        // ESTADÍSTICAS
        // ==========================================

        $this->add('stats', [$this, 'getStats']);

        // ==========================================
        // UTILIDADES
        // ==========================================

        $this->add('validate', [$this, 'validateData']);

        // ==========================================
        // PRUEBAS (LEGACY)
        // ==========================================

        $this->add('test', [$this, 'apiTest']);
    }

    // ==========================================
    // ENDPOINTS DE INFORMACIÓN
    // ==========================================

    /**
     * Información general de la API
     *
     * @return void
     */
    public function apiInfo()
    {
        $this->checkAuthentication(false);

        $info = [
            'name' => 'ApPet API',
            'version' => $this->config['version'],
            'description' => 'API REST para el sistema de gestión de mascotas ApPet',
            'timestamp' => time(),
            'server_time' => date('Y-m-d H:i:s'),
            'endpoints_count' => count($this->availableEndpoints),
            'rate_limit' => [
                'max_requests' => $this->config['rate_limit']['max_requests'],
                'time_window' => $this->config['rate_limit']['time_window']
            ],
            'pagination' => [
                'default_limit' => $this->config['pagination']['default_limit'],
                'max_limit' => $this->config['pagination']['max_limit']
            ]
        ];

        $this->sendSuccess($info);
    }

    /**
     * Estado de la API
     *
     * @return void
     */
    public function apiStatus()
    {
        $this->checkAuthentication(false);

        $status = [
            'status' => 'online',
            'timestamp' => time(),
            'server_time' => date('Y-m-d H:i:s'),
            'uptime' => $this->getServerUptime(),
            'memory_usage' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => $this->parseBytes(ini_get('memory_limit'))
            ],
            'database' => $this->checkDatabaseStatus(),
            'version' => $this->config['version']
        ];

        $this->sendSuccess($status);
    }

    /**
     * Lista de endpoints disponibles
     *
     * @return void
     */
    public function apiEndpoints()
    {
        $this->checkAuthentication(true);

        $endpoints = [];
        foreach ($this->availableEndpoints as $endpoint => $config) {
            $endpoints[] = [
                'endpoint' => $endpoint,
                'method' => $config['method'],
                'auth_required' => $config['auth_required'],
                'description' => $config['description'],
                'url' => _DOMINIO_ . 'api/' . $endpoint
            ];
        }

        $this->sendSuccess([
            'endpoints' => $endpoints,
            'total' => count($endpoints)
        ]);
    }

    // ==========================================
    // ENDPOINTS DE MASCOTAS
    // ==========================================

    /**
     * Obtiene lista de mascotas
     *
     * @return void
     */
    public function getMascotas()
    {
        $this->checkAuthentication(true);

        try {
            $pagination = $this->getPaginationParams();
            $filters = $this->getFilterParams(['tipo', 'genero', 'estado']);

            // Simular obtención de mascotas (aquí iría la lógica real)
            $mascotas = [
                [
                    'id' => 1,
                    'nombre' => 'Luna',
                    'tipo' => 'Perro',
                    'genero' => 'Hembra',
                    'edad' => 3,
                    'estado' => 1
                ],
                [
                    'id' => 2,
                    'nombre' => 'Max',
                    'tipo' => 'Gato',
                    'genero' => 'Macho',
                    'edad' => 2,
                    'estado' => 1
                ]
            ];

            $response = [
                'data' => $mascotas,
                'pagination' => [
                    'page' => $pagination['page'],
                    'limit' => $pagination['limit'],
                    'total' => count($mascotas),
                    'pages' => 1
                ],
                'filters' => $filters
            ];

            $this->sendSuccess($response);
        } catch (Exception $e) {
            $this->sendError('Error obteniendo mascotas: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene información de una mascota específica
     *
     * @return void
     */
    public function getMascota()
    {
        $this->checkAuthentication(true);

        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de mascota requerido', 400);
                return;
            }

            // Aquí iría la lógica real para obtener la mascota
            $mascota = [
                'id' => $id,
                'nombre' => 'Luna',
                'tipo' => 'Perro',
                'genero' => 'Hembra',
                'edad' => 3,
                'peso' => 25.5,
                'estado' => 1,
                'caracteristicas' => [
                    ['nombre' => 'Color', 'valor' => 'Dorado'],
                    ['nombre' => 'Tamaño', 'valor' => 'Grande']
                ]
            ];

            $this->sendSuccess($mascota);
        } catch (Exception $e) {
            $this->sendError('Error obteniendo mascota: ' . $e->getMessage());
        }
    }

    /**
     * Busca mascotas por criterios
     *
     * @return void
     */
    public function searchMascotas()
    {
        $this->checkAuthentication(true);

        try {
            $query = Tools::getValue('q', '');
            $pagination = $this->getPaginationParams();

            if (strlen($query) < 2) {
                $this->sendError('La búsqueda debe tener al menos 2 caracteres', 400);
                return;
            }

            // Aquí iría la lógica real de búsqueda
            $resultados = [
                [
                    'id' => 1,
                    'nombre' => 'Luna',
                    'tipo' => 'Perro',
                    'relevancia' => 0.95
                ]
            ];

            $response = [
                'query' => $query,
                'data' => $resultados,
                'pagination' => [
                    'page' => $pagination['page'],
                    'limit' => $pagination['limit'],
                    'total' => count($resultados),
                    'pages' => 1
                ]
            ];

            $this->sendSuccess($response);
        } catch (Exception $e) {
            $this->sendError('Error en búsqueda: ' . $e->getMessage());
        }
    }

    // ==========================================
    // ENDPOINTS DE CUIDADORES
    // ==========================================

    /**
     * Obtiene lista de cuidadores
     *
     * @return void
     */
    public function getCuidadores()
    {
        $this->checkAuthentication(true);

        try {
            $pagination = $this->getPaginationParams();
            $filters = $this->getFilterParams(['estado', 'ciudad']);

            // Usar la clase Cuidador para obtener datos reales
            $cuidadores = Cuidador::getCuidadoresActivos();

            $response = [
                'data' => $cuidadores,
                'pagination' => [
                    'page' => $pagination['page'],
                    'limit' => $pagination['limit'],
                    'total' => count($cuidadores),
                    'pages' => ceil(count($cuidadores) / $pagination['limit'])
                ],
                'filters' => $filters
            ];

            $this->sendSuccess($response);
        } catch (Exception $e) {
            $this->sendError('Error obteniendo cuidadores: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene información de un cuidador específico
     *
     * @return void
     */
    public function getCuidador()
    {
        $this->checkAuthentication(true);

        try {
            $id = (int)Tools::getValue('id');

            if (!$id) {
                $this->sendError('ID de cuidador requerido', 400);
                return;
            }

            $cuidador = Cuidador::getCuidadorById($id);

            if (!$cuidador) {
                $this->sendError('Cuidador no encontrado', 404);
                return;
            }

            $this->sendSuccess($cuidador);
        } catch (Exception $e) {
            $this->sendError('Error obteniendo cuidador: ' . $e->getMessage());
        }
    }

    /**
     * Busca cuidadores por criterios
     *
     * @return void
     */
    public function searchCuidadores()
    {
        $this->checkAuthentication(true);

        try {
            $query = Tools::getValue('q', '');
            $pagination = $this->getPaginationParams();

            if (strlen($query) < 2) {
                $this->sendError('La búsqueda debe tener al menos 2 caracteres', 400);
                return;
            }

            $resultados = Cuidador::buscarCuidadores($query, $pagination['limit']);

            $response = [
                'query' => $query,
                'data' => $resultados,
                'pagination' => [
                    'page' => $pagination['page'],
                    'limit' => $pagination['limit'],
                    'total' => count($resultados),
                    'pages' => 1
                ]
            ];

            $this->sendSuccess($response);
        } catch (Exception $e) {
            $this->sendError('Error en búsqueda: ' . $e->getMessage());
        }
    }

    // ==========================================
    // OTROS ENDPOINTS
    // ==========================================

    /**
     * Obtiene características disponibles
     *
     * @return void
     */
    public function getCaracteristicas()
    {
        $this->checkAuthentication(true);

        try {
            $caracteristicas = Caracteristicas::getCaracteristicasActivas();

            $this->sendSuccess([
                'data' => $caracteristicas,
                'total' => count($caracteristicas)
            ]);
        } catch (Exception $e) {
            $this->sendError('Error obteniendo características: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas del sistema
     *
     * @return void
     */
    public function getStats()
    {
        $this->checkAuthentication(true);

        try {
            $stats = [
                'mascotas' => [
                    'total' => Mascotas::getTotalMascotas(),
                    'activas' => Mascotas::getTotalMascotas(true)
                ],
                'cuidadores' => [
                    'total' => Cuidador::getTotalCuidadores(),
                    'activos' => Cuidador::getTotalCuidadores(true)
                ],
                'caracteristicas' => [
                    'total' => Caracteristicas::getTotalCaracteristicas(),
                    'activas' => Caracteristicas::getTotalCaracteristicas(true)
                ]
            ];

            $this->sendSuccess($stats);
        } catch (Exception $e) {
            $this->sendError('Error obteniendo estadísticas: ' . $e->getMessage());
        }
    }

    /**
     * Valida datos enviados
     *
     * @return void
     */
    public function validateData()
    {
        $this->checkAuthentication(true);

        try {
            $tipo = Tools::getValue('tipo');
            $datos = Tools::getValue('datos', []);

            $errores = [];
            $valido = true;

            switch ($tipo) {
                case 'email':
                    if (!Tools::isEmail($datos['email'] ?? '')) {
                        $errores['email'] = 'Email no válido';
                        $valido = false;
                    }
                    break;

                case 'telefono':
                    if (!Tools::isPhone($datos['telefono'] ?? '')) {
                        $errores['telefono'] = 'Teléfono no válido';
                        $valido = false;
                    }
                    break;

                default:
                    $this->sendError('Tipo de validación no soportado', 400);
                    return;
            }

            $this->sendSuccess([
                'valid' => $valido,
                'errors' => $errores
            ]);
        } catch (Exception $e) {
            $this->sendError('Error en validación: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint de prueba (legacy)
     *
     * @return void
     */
    public function apiTest()
    {
        $this->checkAuthentication(true);

        $response = [
            'message' => 'API funcionando correctamente',
            'timestamp' => time(),
            'method' => $_SERVER['REQUEST_METHOD'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'ip' => $this->getClientIP()
        ];

        $this->sendSuccess($response);
    }

    // ==========================================
    // MÉTODOS DE UTILIDAD
    // ==========================================

    /**
     * Obtiene parámetros de paginación
     *
     * @return array
     */
    protected function getPaginationParams()
    {
        $page = max(1, (int)Tools::getValue('page', 1));
        $limit = min(
            $this->config['pagination']['max_limit'],
            max(1, (int)Tools::getValue('limit', $this->config['pagination']['default_limit']))
        );

        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => ($page - 1) * $limit
        ];
    }

    /**
     * Obtiene parámetros de filtros
     *
     * @param array $allowedFilters Filtros permitidos
     * @return array
     */
    protected function getFilterParams($allowedFilters = [])
    {
        $filters = [];

        foreach ($allowedFilters as $filter) {
            $value = Tools::getValue($filter);
            if (!empty($value)) {
                $filters[$filter] = $value;
            }
        }

        return $filters;
    }

    /**
     * Registra petición de API
     *
     * @param string $endpoint Endpoint solicitado
     * @return void
     */
    protected function logApiRequest($endpoint)
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $this->getClientIP(),
            'method' => $_SERVER['REQUEST_METHOD'],
            'endpoint' => $endpoint,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'authenticated' => !empty($this->authenticatedUser)
        ];

        $logMessage = sprintf(
            "API REQUEST: %s %s from %s [%s]",
            $logData['method'],
            $logData['endpoint'],
            $logData['ip'],
            $logData['authenticated'] ? 'AUTH' : 'GUEST'
        );

        Tools::logError($logMessage, 0, 'api');
    }

    /**
     * Verifica estado de la base de datos
     *
     * @return array
     */
    protected function checkDatabaseStatus()
    {
        try {
            $db = Bd::getInstance();
            $db->fetchValueSafe("SELECT 1");
            return ['status' => 'connected', 'error' => null];
        } catch (Exception $e) {
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtiene uptime del servidor
     *
     * @return string|null
     */
    protected function getServerUptime()
    {
        if (function_exists('sys_getloadavg')) {
            $uptime = shell_exec('uptime');
            return trim($uptime);
        }
        return null;
    }

    /**
     * Convierte string de memoria a bytes
     *
     * @param string $val Valor de memoria
     * @return int
     */
    protected function parseBytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $val = (int)$val;

        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    /**
     * Envía respuesta de éxito
     *
     * @param mixed $data Datos a enviar
     * @param string $message Mensaje opcional
     * @return void
     */
    protected function sendSuccess($data = null, $message = '')
    {
        $response = [
            'success' => true,
            'timestamp' => time(),
            'data' => $data
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        http_response_code(200);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
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
        $response = [
            'success' => false,
            'error' => $message,
            'timestamp' => time(),
            'code' => $code
        ];

        http_response_code($code);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * No cargar traducciones para API
     *
     * @return void
     */
    protected function loadTraducciones()
    {
        return;
    }
}
