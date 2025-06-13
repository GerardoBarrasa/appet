<?php

/**
 * Controlador de tareas programadas (Cron Jobs)
 *
 * Maneja todas las tareas automatizadas del sistema que se ejecutan
 * de forma programada, incluyendo limpieza, mantenimiento y notificaciones.
 */
class CronsController extends Controllers
{
    /**
     * Configuración del controlador
     */
    protected $config = [
        'max_execution_time' => 300, // 5 minutos
        'memory_limit' => '256M',
        'log_execution' => true,
        'email_batch_size' => 50,
        'cleanup_days' => 30,
        'backup_retention_days' => 7
    ];

    /**
     * Tareas disponibles y sus configuraciones
     */
    protected $availableTasks = [
        'enviar-emails' => [
            'description' => 'Envía emails pendientes en cola',
            'frequency' => '*/5 * * * *', // Cada 5 minutos
            'timeout' => 120
        ],
        'limpiar-logs' => [
            'description' => 'Limpia logs antiguos del sistema',
            'frequency' => '0 2 * * *', // Diario a las 2:00 AM
            'timeout' => 300
        ],
        'limpiar-cache' => [
            'description' => 'Limpia archivos de cache expirados',
            'frequency' => '0 3 * * *', // Diario a las 3:00 AM
            'timeout' => 180
        ],
        'limpiar-sesiones' => [
            'description' => 'Limpia sesiones expiradas',
            'frequency' => '0 4 * * *', // Diario a las 4:00 AM
            'timeout' => 60
        ],
        'backup-database' => [
            'description' => 'Realiza backup de la base de datos',
            'frequency' => '0 1 * * *', // Diario a la 1:00 AM
            'timeout' => 600
        ],
        'verificar-salud' => [
            'description' => 'Verifica el estado del sistema',
            'frequency' => '*/15 * * * *', // Cada 15 minutos
            'timeout' => 30
        ],
        'generar-reportes' => [
            'description' => 'Genera reportes automáticos',
            'frequency' => '0 6 * * 1', // Lunes a las 6:00 AM
            'timeout' => 300
        ],
        'limpiar-archivos-temp' => [
            'description' => 'Limpia archivos temporales',
            'frequency' => '0 5 * * *', // Diario a las 5:00 AM
            'timeout' => 120
        ]
    ];

    /**
     * Ejecuta el controlador de cron jobs
     *
     * @param string $page Tarea a ejecutar
     * @return void
     */
    public function execute($page)
    {
        // Verificar autenticación por token
        if (!$this->validateCronToken()) {
            $this->sendUnauthorized();
            return;
        }

        // Configurar entorno para cron jobs
        $this->setupCronEnvironment();

        // Sin layout para cron jobs
        Render::$layout = false;

        // Log del inicio de ejecución
        $this->logExecution($page, 'start');

        // Definir tareas disponibles
        $this->defineCronTasks();

        // Si no se encontró la tarea, mostrar error
        if (!$this->getRendered()) {
            $this->logExecution($page, 'error', 'Tarea no encontrada');
            http_response_code(404);
            echo "Tarea no encontrada: {$page}";
            exit;
        }
    }

    /**
     * Valida el token de autenticación para cron jobs
     *
     * @return bool
     */
    protected function validateCronToken()
    {
        $token = Tools::getValue('token');
        return !empty($token) && $token === _CRONJOB_TOKEN_;
    }

    /**
     * Envía respuesta de no autorizado
     *
     * @return void
     */
    protected function sendUnauthorized()
    {
        http_response_code(401);
        echo "Unauthorized access";
        exit;
    }

    /**
     * Configura el entorno para la ejecución de cron jobs
     *
     * @return void
     */
    protected function setupCronEnvironment()
    {
        // Configurar límites de ejecución
        set_time_limit($this->config['max_execution_time']);
        ini_set('memory_limit', $this->config['memory_limit']);

        // Configurar zona horaria
        date_default_timezone_set('Europe/Madrid');

        // Desactivar salida de errores para cron
        ini_set('display_errors', 0);

        // Configurar logging de errores
        ini_set('log_errors', 1);
    }

    /**
     * Define todas las tareas de cron disponibles
     *
     * @return void
     */
    protected function defineCronTasks()
    {
        // Envío de emails
        $this->add('enviar-emails', [$this, 'enviarEmails']);

        // Tareas de limpieza
        $this->add('limpiar-logs', [$this, 'limpiarLogs']);
        $this->add('limpiar-cache', [$this, 'limpiarCache']);
        $this->add('limpiar-sesiones', [$this, 'limpiarSesiones']);
        $this->add('limpiar-archivos-temp', [$this, 'limpiarArchivosTemp']);

        // Backup y mantenimiento
        $this->add('backup-database', [$this, 'backupDatabase']);
        $this->add('verificar-salud', [$this, 'verificarSalud']);

        // Reportes
        $this->add('generar-reportes', [$this, 'generarReportes']);

        // Utilidades
        $this->add('listar-tareas', [$this, 'listarTareas']);
        $this->add('estado-sistema', [$this, 'estadoSistema']);
    }

    // ==========================================
    // TAREAS DE CRON
    // ==========================================

    /**
     * Envía emails pendientes en cola
     *
     * @return void
     */
    public function enviarEmails()
    {
        try {
            $cantidad = $this->config['email_batch_size'];
            $db = Bd::getInstance();

            // Obtener emails pendientes
            $emailsPendientes = $db->fetchAllSafe(
                "SELECT id_email FROM emails_cache 
                 WHERE enviado = 0 AND error = 0 
                 ORDER BY id_email ASC 
                 LIMIT ?",
                [$cantidad]
            );

            $enviados = 0;
            $errores = 0;

            if (!empty($emailsPendientes)) {
                foreach ($emailsPendientes as $email) {
                    try {
                        if (Sendmail::sendCachedMail($email->id_email)) {
                            $enviados++;
                        } else {
                            $errores++;
                        }
                    } catch (Exception $e) {
                        $errores++;
                        $this->logError("Error enviando email {$email->id_email}: " . $e->getMessage());
                    }
                }
            }

            $mensaje = "Emails procesados: {$enviados} enviados, {$errores} errores";
            $this->logExecution('enviar-emails', 'success', $mensaje);
            echo $mensaje;

        } catch (Exception $e) {
            $this->logExecution('enviar-emails', 'error', $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * Limpia logs antiguos del sistema
     *
     * @return void
     */
    public function limpiarLogs()
    {
        try {
            $dias = $this->config['cleanup_days'];
            $logDir = log_folder;
            $archivosEliminados = 0;

            if (is_dir($logDir)) {
                $fechaLimite = time() - ($dias * 24 * 60 * 60);
                $archivos = glob($logDir . '*.log');

                foreach ($archivos as $archivo) {
                    if (filemtime($archivo) < $fechaLimite) {
                        if (unlink($archivo)) {
                            $archivosEliminados++;
                        }
                    }
                }
            }

            $mensaje = "Logs limpiados: {$archivosEliminados} archivos eliminados";
            $this->logExecution('limpiar-logs', 'success', $mensaje);
            echo $mensaje;

        } catch (Exception $e) {
            $this->logExecution('limpiar-logs', 'error', $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * Limpia archivos de cache expirados
     *
     * @return void
     */
    public function limpiarCache()
    {
        try {
            $cacheDir = _PATH_ . 'cache/';
            $archivosEliminados = 0;

            if (is_dir($cacheDir)) {
                $this->limpiarDirectorioCache($cacheDir, $archivosEliminados);
            }

            $mensaje = "Cache limpiado: {$archivosEliminados} archivos eliminados";
            $this->logExecution('limpiar-cache', 'success', $mensaje);
            echo $mensaje;

        } catch (Exception $e) {
            $this->logExecution('limpiar-cache', 'error', $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * Limpia sesiones expiradas
     *
     * @return void
     */
    public function limpiarSesiones()
    {
        try {
            $sessionPath = session_save_path();
            $archivosEliminados = 0;

            if (empty($sessionPath)) {
                $sessionPath = sys_get_temp_dir();
            }

            if (is_dir($sessionPath)) {
                $archivos = glob($sessionPath . '/sess_*');
                $tiempoLimite = time() - (24 * 60 * 60); // 24 horas

                foreach ($archivos as $archivo) {
                    if (filemtime($archivo) < $tiempoLimite) {
                        if (unlink($archivo)) {
                            $archivosEliminados++;
                        }
                    }
                }
            }

            $mensaje = "Sesiones limpiadas: {$archivosEliminados} archivos eliminados";
            $this->logExecution('limpiar-sesiones', 'success', $mensaje);
            echo $mensaje;

        } catch (Exception $e) {
            $this->logExecution('limpiar-sesiones', 'error', $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * Limpia archivos temporales
     *
     * @return void
     */
    public function limpiarArchivosTemp()
    {
        try {
            $tempDirs = [
                _PATH_ . 'temp/',
                _PATH_ . 'uploads/temp/',
                sys_get_temp_dir() . '/appet_temp/'
            ];

            $archivosEliminados = 0;

            foreach ($tempDirs as $dir) {
                if (is_dir($dir)) {
                    $this->limpiarDirectorioTemp($dir, $archivosEliminados);
                }
            }

            $mensaje = "Archivos temporales limpiados: {$archivosEliminados} archivos eliminados";
            $this->logExecution('limpiar-archivos-temp', 'success', $mensaje);
            echo $mensaje;

        } catch (Exception $e) {
            $this->logExecution('limpiar-archivos-temp', 'error', $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * Realiza backup de la base de datos
     *
     * @return void
     */
    public function backupDatabase()
    {
        try {
            $backupDir = _PATH_ . 'backups/database/';

            // Crear directorio si no existe
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $fecha = date('Y-m-d_H-i-s');
            $nombreArchivo = "backup_database_{$fecha}.sql";
            $rutaCompleta = $backupDir . $nombreArchivo;

            // Comando mysqldump
            $comando = sprintf(
                'mysqldump -h%s -u%s -p%s %s > %s',
                bd_host,
                bd_user,
                bd_pass,
                bd_name,
                $rutaCompleta
            );

            // Ejecutar backup
            exec($comando, $output, $returnCode);

            if ($returnCode === 0 && file_exists($rutaCompleta)) {
                // Limpiar backups antiguos
                $this->limpiarBackupsAntiguos($backupDir);

                $tamaño = $this->formatBytes(filesize($rutaCompleta));
                $mensaje = "Backup creado: {$nombreArchivo} ({$tamaño})";
                $this->logExecution('backup-database', 'success', $mensaje);
                echo $mensaje;
            } else {
                throw new Exception("Error ejecutando mysqldump");
            }

        } catch (Exception $e) {
            $this->logExecution('backup-database', 'error', $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * Verifica el estado de salud del sistema
     *
     * @return void
     */
    public function verificarSalud()
    {
        try {
            $checks = [];
            $errores = 0;

            // Verificar conexión a base de datos
            try {
                $db = Bd::getInstance();
                $db->fetchValueSafe("SELECT 1");
                $checks['database'] = 'OK';
            } catch (Exception $e) {
                $checks['database'] = 'ERROR: ' . $e->getMessage();
                $errores++;
            }

            // Verificar espacio en disco
            $espacioLibre = disk_free_space(_PATH_);
            $espacioTotal = disk_total_space(_PATH_);
            $porcentajeUso = (($espacioTotal - $espacioLibre) / $espacioTotal) * 100;

            if ($porcentajeUso > 90) {
                $checks['disk_space'] = 'WARNING: Espacio en disco bajo (' . round($porcentajeUso, 2) . '% usado)';
                $errores++;
            } else {
                $checks['disk_space'] = 'OK (' . round($porcentajeUso, 2) . '% usado)';
            }

            // Verificar directorio de logs
            if (!is_writable(log_folder)) {
                $checks['log_directory'] = 'ERROR: Directorio de logs no escribible';
                $errores++;
            } else {
                $checks['log_directory'] = 'OK';
            }

            // Verificar memoria
            $usoMemoria = memory_get_usage(true);
            $limiteMemoria = $this->parseBytes(ini_get('memory_limit'));
            $porcentajeMemoria = ($usoMemoria / $limiteMemoria) * 100;

            if ($porcentajeMemoria > 80) {
                $checks['memory'] = 'WARNING: Uso de memoria alto (' . round($porcentajeMemoria, 2) . '%)';
            } else {
                $checks['memory'] = 'OK (' . round($porcentajeMemoria, 2) . '%)';
            }

            $estado = $errores > 0 ? 'WARNING' : 'OK';
            $mensaje = "Estado del sistema: {$estado} - " . json_encode($checks);

            $this->logExecution('verificar-salud', $errores > 0 ? 'warning' : 'success', $mensaje);
            echo $mensaje;

        } catch (Exception $e) {
            $this->logExecution('verificar-salud', 'error', $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * Genera reportes automáticos del sistema
     *
     * @return void
     */
    public function generarReportes()
    {
        try {
            $db = Bd::getInstance();
            $fecha = date('Y-m-d');

            // Estadísticas básicas
            $stats = [
                'fecha' => $fecha,
                'total_mascotas' => $db->fetchValueSafe("SELECT COUNT(*) FROM mascotas"),
                'total_cuidadores' => $db->fetchValueSafe("SELECT COUNT(*) FROM cuidadores"),
                'total_usuarios_admin' => $db->fetchValueSafe("SELECT COUNT(*) FROM usuarios_admin"),
                'emails_pendientes' => $db->fetchValueSafe("SELECT COUNT(*) FROM emails_cache WHERE enviado = 0"),
                'espacio_disco' => $this->formatBytes(disk_free_space(_PATH_)),
                'uso_memoria' => $this->formatBytes(memory_get_usage(true))
            ];

            // Guardar reporte
            $reporteDir = _PATH_ . 'reports/';
            if (!is_dir($reporteDir)) {
                mkdir($reporteDir, 0755, true);
            }

            $nombreArchivo = "reporte_sistema_{$fecha}.json";
            file_put_contents($reporteDir . $nombreArchivo, json_encode($stats, JSON_PRETTY_PRINT));

            $mensaje = "Reporte generado: {$nombreArchivo}";
            $this->logExecution('generar-reportes', 'success', $mensaje);
            echo $mensaje;

        } catch (Exception $e) {
            $this->logExecution('generar-reportes', 'error', $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * Lista todas las tareas disponibles
     *
     * @return void
     */
    public function listarTareas()
    {
        echo "Tareas disponibles:\n\n";

        foreach ($this->availableTasks as $tarea => $config) {
            echo "- {$tarea}\n";
            echo "  Descripción: {$config['description']}\n";
            echo "  Frecuencia: {$config['frequency']}\n";
            echo "  Timeout: {$config['timeout']}s\n\n";
        }
    }

    /**
     * Muestra el estado actual del sistema
     *
     * @return void
     */
    public function estadoSistema()
    {
        $info = [
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION,
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'disk_free_space' => $this->formatBytes(disk_free_space(_PATH_)),
            'server_load' => sys_getloadavg()
        ];

        echo json_encode($info, JSON_PRETTY_PRINT);
    }

    // ==========================================
    // MÉTODOS DE UTILIDAD
    // ==========================================

    /**
     * Limpia un directorio de cache recursivamente
     *
     * @param string $dir Directorio a limpiar
     * @param int &$contador Contador de archivos eliminados
     * @return void
     */
    protected function limpiarDirectorioCache($dir, &$contador)
    {
        $archivos = glob($dir . '*');
        $tiempoLimite = time() - (24 * 60 * 60); // 24 horas

        foreach ($archivos as $archivo) {
            if (is_file($archivo) && filemtime($archivo) < $tiempoLimite) {
                if (unlink($archivo)) {
                    $contador++;
                }
            } elseif (is_dir($archivo)) {
                $this->limpiarDirectorioCache($archivo . '/', $contador);
            }
        }
    }

    /**
     * Limpia un directorio temporal
     *
     * @param string $dir Directorio a limpiar
     * @param int &$contador Contador de archivos eliminados
     * @return void
     */
    protected function limpiarDirectorioTemp($dir, &$contador)
    {
        $archivos = glob($dir . '*');
        $tiempoLimite = time() - (2 * 60 * 60); // 2 horas

        foreach ($archivos as $archivo) {
            if (is_file($archivo) && filemtime($archivo) < $tiempoLimite) {
                if (unlink($archivo)) {
                    $contador++;
                }
            }
        }
    }

    /**
     * Limpia backups antiguos
     *
     * @param string $backupDir Directorio de backups
     * @return void
     */
    protected function limpiarBackupsAntiguos($backupDir)
    {
        $archivos = glob($backupDir . 'backup_database_*.sql');
        $tiempoLimite = time() - ($this->config['backup_retention_days'] * 24 * 60 * 60);

        foreach ($archivos as $archivo) {
            if (filemtime($archivo) < $tiempoLimite) {
                unlink($archivo);
            }
        }
    }

    /**
     * Registra la ejecución de una tarea
     *
     * @param string $tarea Nombre de la tarea
     * @param string $estado Estado de la ejecución
     * @param string $mensaje Mensaje adicional
     * @return void
     */
    protected function logExecution($tarea, $estado, $mensaje = '')
    {
        if (!$this->config['log_execution']) {
            return;
        }

        $logMessage = sprintf(
            "[%s] CRON %s: %s - %s",
            date('Y-m-d H:i:s'),
            strtoupper($estado),
            $tarea,
            $mensaje
        );

        Tools::logError($logMessage, 0, 'cron');
    }

    /**
     * Registra un error
     *
     * @param string $mensaje Mensaje de error
     * @return void
     */
    protected function logError($mensaje)
    {
        Tools::logError($mensaje, 99);
    }

    /**
     * Formatea bytes a formato legible
     *
     * @param int $bytes Bytes a formatear
     * @return string Tamaño formateado
     */
    protected function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Convierte string de memoria a bytes
     *
     * @param string $val Valor de memoria (ej: "256M")
     * @return int Bytes
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
     * No cargar traducciones para cron jobs
     *
     * @return void
     */
    protected function loadTraducciones()
    {
        return;
    }
}
