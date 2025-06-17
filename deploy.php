<?php
/**
 * Script de Despliegue Automático - Versión Mejorada
 * Maneja mejor los errores y proporciona más información
 */

// Configuración
define('DEPLOY_SECRET', 'tu_clave_secreta_muy_segura_2024');
define('LOG_FILE', 'deploy.log');

// Función para escribir logs con más detalle
function writeLog($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message\n";
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);

    // También mostrar en pantalla para debugging
    if ($level === 'ERROR') {
        echo "ERROR: $message\n";
    }
}

// Función para ejecutar comandos con mejor manejo de errores
function executeCommand($command, $description = '') {
    writeLog("Ejecutando: $command" . ($description ? " ($description)" : ''));

    $output = shell_exec($command . ' 2>&1');
    $exitCode = shell_exec('echo $?');

    writeLog("Salida: " . trim($output));
    writeLog("Código de salida: " . trim($exitCode));

    if (trim($exitCode) !== '0') {
        writeLog("ADVERTENCIA: Comando falló con código " . trim($exitCode), 'WARNING');
    }

    return $output;
}

// Verificar método de petición
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    writeLog('Petición rechazada: método no es POST', 'ERROR');
    http_response_code(405);
    die('Método no permitido');
}

// Verificar signature
$headers = getallheaders();
$signature = isset($headers['X-Hub-Signature-256']) ? $headers['X-Hub-Signature-256'] : '';

if (empty($signature)) {
    writeLog('Petición rechazada: sin signature', 'ERROR');
    http_response_code(401);
    die('No autorizado');
}

$payload = file_get_contents('php://input');
$expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, DEPLOY_SECRET);

if (!hash_equals($expectedSignature, $signature)) {
    writeLog('Petición rechazada: signature inválida', 'ERROR');
    http_response_code(401);
    die('Signature inválida');
}

// Decodificar payload
$data = json_decode($payload, true);
if (!$data) {
    writeLog('Payload JSON inválido', 'ERROR');
    http_response_code(400);
    die('Payload inválido');
}

// Verificar rama
if ($data['ref'] !== 'refs/heads/main' && $data['ref'] !== 'refs/heads/master') {
    writeLog('Push ignorado - rama: ' . $data['ref']);
    die('Push ignorado - no es rama principal');
}

// Iniciar despliegue
writeLog('=== INICIANDO DESPLIEGUE ===');
writeLog('Repositorio: ' . $data['repository']['full_name']);
writeLog('Commit: ' . $data['head_commit']['id']);
writeLog('Mensaje: ' . $data['head_commit']['message']);
writeLog('Autor: ' . $data['head_commit']['author']['name']);

try {
    // Verificar que estamos en un repositorio Git
    if (!is_dir('.git')) {
        throw new Exception('El directorio no es un repositorio Git. Ejecuta setup-repo.php primero.');
    }

    writeLog('1. Verificando estado del repositorio...');
    executeCommand('git status --porcelain', 'Estado del repositorio');

    writeLog('2. Descargando cambios...');
    executeCommand('git fetch origin', 'Fetch de cambios');

    writeLog('3. Aplicando cambios...');
    executeCommand('git reset --hard origin/main', 'Reset hard a origin/main');

    writeLog('4. Verificando archivos actualizados...');
    executeCommand('git log --oneline -3', 'Últimos commits');

    writeLog('5. Configurando permisos...');
    executeCommand('find . -type f -name "*.php" -exec chmod 644 {} \;', 'Permisos archivos PHP');
    executeCommand('find . -type d -exec chmod 755 {} \;', 'Permisos directorios');

    // Configurar permisos especiales si existen
    if (is_dir('resources/private')) {
        executeCommand('chmod -R 777 resources/private', 'Permisos recursos privados');
    }

    writeLog('=== DESPLIEGUE COMPLETADO EXITOSAMENTE ===');

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Despliegue completado',
        'commit' => $data['head_commit']['id'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    writeLog('ERROR CRÍTICO: ' . $e->getMessage(), 'ERROR');

    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
