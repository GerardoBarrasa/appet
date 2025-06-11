<?php

class DebugController extends Controllers
{
    public function execute($page)
    {
        Render::$layout = false;

        // Logs viewer - Lista todos los logs disponibles
        $this->add('logs', function() {
            if (!_DEBUG_) {
                header('Location:' . _DOMINIO_);
                return;
            }

            $this->showLogsViewer();
        });

        // Ver log específico
        $this->add('log', function() {
            if (!_DEBUG_) {
                header('Location:' . _DOMINIO_);
                return;
            }

            $filename = $_GET['file'] ?? '';
            if ($filename) {
                $this->showLogContent($filename);
            } else {
                header('Location:' . _DOMINIO_ . 'debug/logs/');
            }
        });

        // Limpiar logs
        $this->add('clear-logs', function() {
            if (!_DEBUG_) {
                header('Location:' . _DOMINIO_);
                return;
            }

            $type = $_GET['type'] ?? 'all';
            $this->clearLogs($type);
        });

        // Generar log de prueba
        $this->add('test-log', function() {
            if (!_DEBUG_) {
                header('Location:' . _DOMINIO_);
                return;
            }

            $this->generateTestLogs();
        });

        // Debug bd (mantener compatibilidad)
        $this->add('bd', function() {
            if (!_DEBUG_) {
                header('Location:' . _DOMINIO_);
                return;
            }

            $this->showBdDebug();
        });

        // Ajax bd test (mantener compatibilidad)
        $this->add('ajax-bd-test', function() {
            $sql = $_POST['sql'];
            echo Bd::getInstance()->getResponse($sql);
        });

        if (!$this->getRendered()) {
            header('HTTP/1.1 404 Not Found');
            exit;
        }
    }

    /**
     * Muestra el visor de logs principal
     */
    private function showLogsViewer()
    {
        $logFiles = $this->getLogFiles();
        $logStats = $this->getLogStats();

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Debug - Log Viewer</title>
            <meta charset="utf-8">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f5f5f5; }
                .container { max-width: 1200px; margin: 0 auto; }
                .header { background: #2c3e50; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
                .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
                .stat-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .stat-number { font-size: 24px; font-weight: bold; color: #3498db; }
                .log-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; }
                .log-card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden; }
                .log-header { padding: 15px; border-bottom: 1px solid #eee; }
                .log-type { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
                .log-type.php { background: #e74c3c; color: white; }
                .log-type.sql { background: #f39c12; color: white; }
                .log-type.debug { background: #9b59b6; color: white; }
                .log-type.general { background: #34495e; color: white; }
                .log-type.performance { background: #27ae60; color: white; }
                .log-body { padding: 15px; }
                .log-actions { padding: 15px; border-top: 1px solid #eee; }
                .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 10px; }
                .btn-primary { background: #3498db; color: white; }
                .btn-danger { background: #e74c3c; color: white; }
                .btn-success { background: #27ae60; color: white; }
                .btn-warning { background: #f39c12; color: white; }
                .actions { margin-bottom: 20px; }
                .no-logs { text-align: center; padding: 40px; color: #7f8c8d; }
            </style>
        </head>
        <body>
        <div class="container">
            <div class="header">
                <h1>🐛 Debug - Log Viewer</h1>
                <p>Sistema de logging avanzado - <?= date('Y-m-d H:i:s') ?></p>
            </div>

            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?= $logStats['total_files'] ?></div>
                    <div>Archivos de Log</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= formatBytes($logStats['total_size']) ?></div>
                    <div>Tamaño Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $logStats['php_errors'] ?></div>
                    <div>Errores PHP</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $logStats['sql_errors'] ?></div>
                    <div>Errores SQL</div>
                </div>
            </div>

            <div class="actions">
                <a href="<?= _DOMINIO_ ?>debug/test-log/" class="btn btn-success">🧪 Generar Logs de Prueba</a>
                <a href="<?= _DOMINIO_ ?>debug/clear-logs/?type=all" class="btn btn-danger" onclick="return confirm('¿Eliminar todos los logs?')">🗑️ Limpiar Todos</a>
                <a href="<?= _DOMINIO_ ?>debug/clear-logs/?type=old" class="btn btn-warning" onclick="return confirm('¿Eliminar logs antiguos (>7 días)?')">📅 Limpiar Antiguos</a>
                <a href="<?= _DOMINIO_ ?>debug/bd/" class="btn btn-primary">🗄️ Debug BD (Legacy)</a>
            </div>

            <?php if (empty($logFiles)): ?>
                <div class="no-logs">
                    <h3>📝 No hay logs disponibles</h3>
                    <p>Genera algunos logs de prueba para comenzar</p>
                </div>
            <?php else: ?>
                <div class="log-grid">
                    <?php foreach ($logFiles as $file): ?>
                        <div class="log-card">
                            <div class="log-header">
                                <h3><?= htmlspecialchars($file['name']) ?></h3>
                                <span class="log-type <?= $file['type'] ?>"><?= strtoupper($file['type']) ?></span>
                            </div>
                            <div class="log-body">
                                <p><strong>Tamaño:</strong> <?= formatBytes($file['size']) ?></p>
                                <p><strong>Modificado:</strong> <?= date('Y-m-d H:i:s', $file['modified']) ?></p>
                                <p><strong>Líneas:</strong> <?= $file['lines'] ?></p>
                            </div>
                            <div class="log-actions">
                                <a href="<?= _DOMINIO_ ?>debug/log/?file=<?= urlencode($file['name']) ?>" class="btn btn-primary">👁️ Ver</a>
                                <a href="<?= _DOMINIO_ ?>debug/clear-logs/?type=single&file=<?= urlencode($file['name']) ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar este log?')">🗑️ Eliminar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        </body>
        </html>
        <?php
    }

    /**
     * Muestra el contenido de un log específico
     */
    private function showLogContent($filename)
    {
        $filepath = log_folder . $filename;

        if (!file_exists($filepath)) {
            echo "Log no encontrado";
            return;
        }

        $content = file_get_contents($filepath);
        $lines = explode("\n", $content);
        $totalLines = count($lines);

        // Paginación
        $page = (int)($_GET['page'] ?? 1);
        $linesPerPage = 100;
        $totalPages = ceil($totalLines / $linesPerPage);
        $offset = ($page - 1) * $linesPerPage;
        $pageLines = array_slice($lines, $offset, $linesPerPage);

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Debug - <?= htmlspecialchars($filename) ?></title>
            <meta charset="utf-8">
            <style>
                body { font-family: 'Courier New', monospace; margin: 0; background: #1e1e1e; color: #d4d4d4; }
                .header { background: #2d2d30; padding: 15px; border-bottom: 1px solid #3e3e42; position: sticky; top: 0; z-index: 100; }
                .header h1 { margin: 0; color: #ffffff; }
                .header .info { color: #cccccc; margin-top: 5px; }
                .content { padding: 20px; }
                .line { padding: 2px 0; border-bottom: 1px solid #2d2d30; }
                .line-number { display: inline-block; width: 60px; color: #858585; text-align: right; margin-right: 15px; }
                .line-content { color: #d4d4d4; }
                .error { background: #3c1e1e; color: #f48771; }
                .warning { background: #3c3c1e; color: #dcdcaa; }
                .info { background: #1e3c3c; color: #9cdcfe; }
                .pagination { text-align: center; padding: 20px; background: #2d2d30; }
                .pagination a { color: #569cd6; text-decoration: none; margin: 0 5px; padding: 5px 10px; }
                .pagination .current { background: #569cd6; color: white; }
                .actions { padding: 15px; background: #2d2d30; }
                .btn { padding: 8px 16px; background: #0e639c; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px; }
            </style>
        </head>
        <body>
        <div class="header">
            <h1>📄 <?= htmlspecialchars($filename) ?></h1>
            <div class="info">
                Líneas: <?= $totalLines ?> | Tamaño: <?= formatBytes(filesize($filepath)) ?> |
                Página <?= $page ?> de <?= $totalPages ?>
            </div>
        </div>

        <div class="actions">
            <a href="<?= _DOMINIO_ ?>debug/logs/" class="btn">← Volver a Logs</a>
            <a href="?file=<?= urlencode($filename) ?>&page=<?= $totalPages ?>" class="btn">Ir al Final</a>
        </div>

        <div class="content">
            <?php foreach ($pageLines as $index => $line): ?>
                <?php
                $lineNumber = $offset + $index + 1;
                $cssClass = '';
                if (stripos($line, 'error') !== false) $cssClass = 'error';
                elseif (stripos($line, 'warning') !== false) $cssClass = 'warning';
                elseif (stripos($line, 'info') !== false) $cssClass = 'info';
                ?>
                <div class="line <?= $cssClass ?>">
                    <span class="line-number"><?= $lineNumber ?></span>
                    <span class="line-content"><?= htmlspecialchars($line) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?file=<?= urlencode($filename) ?>&page=<?= $page - 1 ?>">← Anterior</a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 5); $i <= min($totalPages, $page + 5); $i++): ?>
                    <a href="?file=<?= urlencode($filename) ?>&page=<?= $i ?>" <?= $i == $page ? 'class="current"' : '' ?>><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?file=<?= urlencode($filename) ?>&page=<?= $page + 1 ?>">Siguiente →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        </body>
        </html>
        <?php
    }

    /**
     * Limpia logs según el tipo especificado
     */
    private function clearLogs($type)
    {
        $deleted = 0;
        $logFiles = glob(log_folder . '*.log');

        switch ($type) {
            case 'all':
                foreach ($logFiles as $file) {
                    if (unlink($file)) $deleted++;
                }
                break;

            case 'old':
                $cutoffTime = time() - (7 * 24 * 60 * 60); // 7 días
                foreach ($logFiles as $file) {
                    if (filemtime($file) < $cutoffTime) {
                        if (unlink($file)) $deleted++;
                    }
                }
                break;

            case 'single':
                $filename = $_GET['file'] ?? '';
                if ($filename) {
                    $filepath = log_folder . $filename;
                    if (file_exists($filepath) && unlink($filepath)) {
                        $deleted = 1;
                    }
                }
                break;
        }

        // Limpiar también la sesión de debug BD
        if (isset($_SESSION['debug']['bd'])) {
            unset($_SESSION['debug']['bd']);
        }

        echo "<script>alert('$deleted archivo(s) eliminado(s)'); window.location.href='" . _DOMINIO_ . "debug/logs/';</script>";
    }

    /**
     * Genera logs de prueba para testing
     */
    private function generateTestLogs()
    {
        // Log de debug personalizado
        debug_log(['test' => 'data', 'timestamp' => time()], 'TEST_DEBUG', 'test_custom');

        // Log de performance
        $startTime = microtime(true);
        usleep(100000); // Simular operación
        performance_log('Test Operation', $startTime, ['test_param' => 'value']);

        // Simular error PHP
        trigger_error('Test PHP Warning', E_USER_WARNING);

        // Simular error SQL
        try {
            Bd::getInstance()->query("SELECT * FROM tabla_inexistente");
        } catch (Exception $e) {
            // Error ya registrado automáticamente
        }

        // Log general
        __log_error('Test general log entry', 3, 'test_general');

        echo "<script>alert('Logs de prueba generados exitosamente'); window.location.href='" . _DOMINIO_ . "debug/logs/';</script>";
    }

    /**
     * Obtiene la lista de archivos de log
     */
    private function getLogFiles()
    {
        $files = [];
        $logFiles = glob(log_folder . '*.log');

        foreach ($logFiles as $file) {
            $filename = basename($file);
            $type = 'general';

            if (strpos($filename, 'phpErrors_') === 0) $type = 'php';
            elseif (strpos($filename, 'SQLErrors_') === 0) $type = 'sql';
            elseif (strpos($filename, 'debug_') === 0) $type = 'debug';
            elseif (strpos($filename, 'performance_') === 0) $type = 'performance';

            $files[] = [
                'name' => $filename,
                'path' => $file,
                'type' => $type,
                'size' => filesize($file),
                'modified' => filemtime($file),
                'lines' => count(file($file))
            ];
        }

        // Ordenar por fecha de modificación (más reciente primero)
        usort($files, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $files;
    }

    /**
     * Obtiene estadísticas de los logs
     */
    private function getLogStats()
    {
        $files = $this->getLogFiles();
        $stats = [
            'total_files' => count($files),
            'total_size' => 0,
            'php_errors' => 0,
            'sql_errors' => 0
        ];

        foreach ($files as $file) {
            $stats['total_size'] += $file['size'];
            if ($file['type'] === 'php') $stats['php_errors'] += $file['lines'];
            if ($file['type'] === 'sql') $stats['sql_errors'] += $file['lines'];
        }

        return $stats;
    }

    /**
     * Debug BD legacy (mantener compatibilidad)
     */
    private function showBdDebug()
    {
        if (isset($_GET['id']) && $_GET['id'] == 'clean') {
            unset($_SESSION['debug']['bd']);
            header('Location:' . _DOMINIO_ . 'debug/bd/');
            return;
        }

        if (isset($_GET['id']) && $_GET['id'] == 'clean-ok') {
            $cache = $_SESSION['debug']['bd'] ?? [];
            unset($_SESSION['debug']['bd']);
            $cantidad = count($cache);
            for ($i = $cantidad - 1; $i >= 0; $i--) {
                $result = $cache[$i][2];
                if ($result != 'Ejecutada correctamente') {
                    $_SESSION['debug']['bd'][] = $cache[$i];
                }
            }
            header('Location:' . _DOMINIO_ . 'debug/bd/');
            return;
        }

        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <h1 style="font-family:verdana; font-size:26px; color:#339999;">Db Log (Legacy):</h1>
        <div style="float:right; margin-top:-40px ">
            <button onclick="document.location='<?= _DOMINIO_ ?>debug/bd/clean-ok/';" style="padding:10px 20px;">Limpiar Correctas</button>
            <button onclick="document.location='<?= _DOMINIO_ ?>debug/bd/clean/';" style="padding:10px 20px;">Limpiar Todo</button>
            <button onclick="document.location='<?= _DOMINIO_ ?>debug/logs/';" style="padding:10px 20px; background:#27ae60; color:white;">Nuevo Sistema de Logs</button>
        </div>
        <br clear="all" /><br />
        <script>
            function enviar(n) {
                $.ajax({
                    type: "POST",
                    url: "<?= _DOMINIO_ ?>debug/ajax-bd-test/",
                    data: 'sql=' + $('#sql' + n).val(),
                    success: function(data) {
                        if (data == 'Ejecutada correctamente')
                            $('#the_result_' + n).html('<div style="padding:4px 12px; border-radius:5px; color:#fff; background-color:#339999">' + data + '</div>');
                        else
                            $('#the_result_' + n).html('<div style="padding:4px 12px; border-radius:5px; color:#fff; background-color:#CC3333">' + data + '</div>');
                    }
                });
            }
        </script>
        <?php

        if (isset($_SESSION['debug']['bd'])) {
            $cantidad = count($_SESSION['debug']['bd']);
            for ($i = $cantidad - 1; $i >= 0; $i--) {
                $time = $_SESSION['debug']['bd'][$i][0];
                $sql = $_SESSION['debug']['bd'][$i][1];
                $result = $_SESSION['debug']['bd'][$i][2];
                ?>
                <div style="padding:10px; font-family:verdana; font-size:12px; color:#666; border-radius:5px 5px 0 0; border-top:1px solid #ccc; border-left:1px solid #ccc; border-right:1px solid #ccc;background-color:#f2f2f2">
                    <div style="float:left">
                        <button onclick="enviar(<?= $i ?>);">Ejecutar</button>&nbsp;&nbsp;Ejecutada a las <?= date('H:i:s', $time); ?> hs.
                    </div>
                    <div style="float:right" id="the_result_<?= $i ?>">
                        <?= $result == 'Ejecutada correctamente' ? '<div style="padding:4px 12px; border-radius:5px; color:#fff; background-color:#339999">' . $result . '</div>' : '<div style="padding:4px 12px; border-radius:5px; color:#fff; background-color:#CC3333">' . $result . '</div>' ?>
                    </div>
                    <br clear="all" />
                </div>
                <textarea style="width:100%; border:1px solid #ccc; outline:none; padding:10px; border-radius:0 0 5px 5px; font-size:16px; height:100px" id="sql<?= $i ?>"><?= $sql ?></textarea>
                <br /><br />
                <br /><br />
                <?php
            }
        }
    }

    protected function loadTraducciones()
    {
        return;
    }
}
