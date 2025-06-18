<?php
/**
 * Script de Despliegue Automático - Versión Segura
 * Usa el sistema de logging de core.php y configuración centralizada
 */

// Modo despliegue para evitar cargar todo el framework
define('DEPLOY_MODE', true);

// Cargar solo las funciones de logging de core.php
require_once 'core/core.php';

/**
 * Función para ejecutar comandos con logging integrado
 * @param string $command Comando a ejecutar
 * @param string $description Descripción del comando
 * @return string Output del comando
 */
function executeCommand($command, $description = '') {
    deploy_log("Ejecutando: $command" . ($description ? " ($description)" : ''));

    $output = shell_exec($command . ' 2>&1');
    $exitCode = shell_exec('echo $?');

    $context = [
        'command' => $command,
        'exit_code' => trim($exitCode),
        'output_length' => strlen($output)
    ];

    deploy_log("Resultado: " . trim($output), 'INFO', $context);

    if (trim($exitCode) !== '0') {
        deploy_log("Comando falló con código " . trim($exitCode), 'WARNING', $context);
    }

    return $output;
}

// Verificar método de petición
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    deploy_log('Petición rechazada: método no es POST', 'ERROR');
    http_response_code(405);
    die('Método no permitido');
}

// Verificar signature
$headers = getallheaders();
$signature = isset($headers['X-Hub-Signature-256']) ? $headers['X-Hub-Signature-256'] : '';

if (empty($signature)) {
    deploy_log('Petición rechazada: sin signature', 'ERROR');
    http_response_code(401);
    die('No autorizado');
}

$payload = file_get_contents('php://input');
$expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, DEPLOY_SECRET);

if (!hash_equals($expectedSignature, $signature)) {
    deploy_log('Petición rechazada: signature inválida', 'ERROR', [
        'received_signature' => $signature,
        'payload_length' => strlen($payload)
    ]);
    http_response_code(401);
    die('Signature inválida');
}

// Decodificar payload
$data = json_decode($payload, true);
if (!$data) {
    deploy_log('Payload JSON inválido', 'ERROR');
    http_response_code(400);
    die('Payload inválido');
}

// Verificar rama
if ($data['ref'] !== 'refs/heads/main' && $data['ref'] !== 'refs/heads/master') {
    deploy_log('Push ignorado - rama: ' . $data['ref']);
    die('Push ignorado - no es rama principal');
}

// Iniciar despliegue
$deployContext = [
    'repository' => $data['repository']['full_name'],
    'commit' => $data['head_commit']['id'],
    'author' => $data['head_commit']['author']['name'],
    'branch' => str_replace('refs/heads/', '', $data['ref'])
];

deploy_log('=== INICIANDO DESPLIEGUE ===', 'INFO', $deployContext);
deploy_log('Mensaje del commit: ' . $data['head_commit']['message']);

$startTime = microtime(true);

try {
    // Verificar que estamos en un repositorio Git
    if (!is_dir('.git')) {
        throw new Exception('El directorio no es un repositorio Git. Ejecuta setup-repo.php primero.');
    }

    deploy_log('1. Verificando estado del repositorio...');
    executeCommand('git status --porcelain', 'Estado del repositorio');

    deploy_log('2. Descargando cambios...');
    executeCommand('git fetch origin', 'Fetch de cambios');

    deploy_log('3. Aplicando cambios...');
    executeCommand('git reset --hard origin/' . $deployContext['branch'], 'Reset hard a origin/' . $deployContext['branch']);

    deploy_log('4. Verificando archivos actualizados...');
    executeCommand('git log --oneline -3', 'Últimos commits');

    deploy_log('5. Configurando permisos...');
    executeCommand('find . -type f -name "*.php" -exec chmod 644 {} \;', 'Permisos archivos PHP');
    executeCommand('find . -type d -exec chmod 755 {} \;', 'Permisos directorios');

    // Configurar permisos especiales si existen
    if (is_dir('resources/private')) {
        executeCommand('chmod -R 777 resources/private', 'Permisos recursos privados');
    }

    if (is_dir('log')) {
        executeCommand('chmod -R 755 log', 'Permisos carpeta log');
    }

    // Log de performance
    performance_log('Deploy completo', $startTime, $deployContext);

    deploy_log('=== DESPLIEGUE COMPLETADO EXITOSAMENTE ===', 'INFO', [
        'duration' => round(microtime(true) - $startTime, 2) . 's',
        'commit' => $deployContext['commit']
    ]);

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Despliegue completado',
        'commit' => $data['head_commit']['id'],
        'duration' => round(microtime(true) - $startTime, 2) . 's',
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    deploy_log('ERROR CRÍTICO: ' . $e->getMessage(), 'ERROR', [
        'duration' => round(microtime(true) - $startTime, 2) . 's',
        'commit' => $deployContext['commit'] ?? 'unknown'
    ]);

    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
