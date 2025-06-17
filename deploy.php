<?php
/**
 * Script de Despliegue Automático para IONOS
 *
 * Este script se ejecuta cuando GitHub envía un webhook
 * y actualiza automáticamente los archivos del servidor
 */

// Configuración de seguridad
define('DEPLOY_SECRET', 'Qhz8xR84CgISZhDxnohzCair546MvuqWPKHqGr3oEKU5DWVhxnw02kZhDxnorv7a');
define('GITHUB_REPO', 'https://github.com/GerardoBarrasa/appet');
define('DEPLOY_PATH', '/'); // Ruta donde está tu proyecto
define('LOG_FILE', 'log/deploy_'.date('Ymd').'.log');

// Función para escribir logs
function writeLog($message) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents(LOG_FILE, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

// Función para ejecutar comandos
function executeCommand($command) {
    writeLog("Ejecutando: $command");
    $output = shell_exec($command . ' 2>&1');
    writeLog("Resultado: " . $output);
    return $output;
}

// Verificar que es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Método no permitido');
}

// Verificar el secret token (seguridad)
$headers = getallheaders();
$signature = isset($headers['X-Hub-Signature-256']) ? $headers['X-Hub-Signature-256'] : '';

if (empty($signature)) {
    writeLog('ERROR: No se recibió signature de GitHub');
    http_response_code(401);
    die('No autorizado');
}

// Verificar la firma
$payload = file_get_contents('php://input');
$expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, DEPLOY_SECRET);

if (!hash_equals($expectedSignature, $signature)) {
    writeLog('ERROR: Signature inválida');
    http_response_code(401);
    die('Signature inválida');
}

// Decodificar el payload de GitHub
$data = json_decode($payload, true);

if (!$data) {
    writeLog('ERROR: Payload JSON inválido');
    http_response_code(400);
    die('Payload inválido');
}

// Verificar que es un push a la rama main/master
if ($data['ref'] !== 'refs/heads/main' && $data['ref'] !== 'refs/heads/master') {
    writeLog('INFO: Push ignorado - no es rama principal');
    die('Push ignorado - no es rama principal');
}

writeLog('=== INICIANDO DESPLIEGUE ===');
writeLog('Repositorio: ' . $data['repository']['full_name']);
writeLog('Commit: ' . $data['head_commit']['id']);
writeLog('Mensaje: ' . $data['head_commit']['message']);

try {
    // Cambiar al directorio del proyecto
    chdir(DEPLOY_PATH);

    // 1. Hacer backup de archivos críticos
    writeLog('1. Creando backup...');
    executeCommand('cp core/settings.php core/settings.php.backup');
    executeCommand('cp -r resources/private resources/private.backup');

    // 2. Hacer git pull
    writeLog('2. Actualizando código...');
    executeCommand('git fetch origin');
    executeCommand('git reset --hard origin/main'); // o origin/master

    // 3. Restaurar archivos críticos
    writeLog('3. Restaurando configuración...');
    executeCommand('cp core/settings.php.backup core/settings.php');
    executeCommand('cp -r resources/private.backup/* resources/private/');

    // 4. Limpiar backups
    executeCommand('rm core/settings.php.backup');
    executeCommand('rm -rf resources/private.backup');

    // 5. Establecer permisos correctos
    writeLog('4. Configurando permisos...');
    executeCommand('find . -type f -name "*.php" -exec chmod 644 {} \;');
    executeCommand('find . -type d -exec chmod 755 {} \;');
    executeCommand('chmod 777 resources/private/mascotas');
    executeCommand('chmod 777 resources/private/cuidadores');

    // 6. Limpiar cache si existe
    writeLog('5. Limpiando cache...');
    executeCommand('rm -rf cache/*');

    writeLog('=== DESPLIEGUE COMPLETADO EXITOSAMENTE ===');

    // Respuesta exitosa
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Despliegue completado',
        'commit' => $data['head_commit']['id'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    writeLog('ERROR: ' . $e->getMessage());

    // Respuesta de error
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
