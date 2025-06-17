<?php
/**
 * Script para configurar el webhook inicial
 * Ejecutar UNA SOLA VEZ después de subir el deploy.php
 */

// Configuración
$deployPath = '/'; // Ajusta según tu hosting
$repoUrl = 'https://github.com/GerardoBarrasa/appet';

echo "<h2>Configurando Despliegue Automático</h2>";

// 1. Verificar que git está disponible
echo "<h3>1. Verificando Git...</h3>";
$gitVersion = shell_exec('git --version 2>&1');
if (strpos($gitVersion, 'git version') !== false) {
    echo "✅ Git disponible: " . $gitVersion;
} else {
    echo "❌ Git no está disponible. Contacta con IONOS para habilitarlo.";
    exit;
}

// 2. Clonar repositorio si no existe
echo "<h3>2. Configurando repositorio...</h3>";
if (!is_dir('.git')) {
    echo "Clonando repositorio...<br>";
    $output = shell_exec("git clone $repoUrl . 2>&1");
    echo "<pre>$output</pre>";
} else {
    echo "✅ Repositorio ya existe<br>";
}

// 3. Configurar git
echo "<h3>3. Configurando Git...</h3>";
shell_exec('git config user.name "Deploy Bot"');
shell_exec('git config user.email "deploy@tu-dominio.com"');
echo "✅ Git configurado<br>";

// 4. Verificar permisos
echo "<h3>4. Verificando permisos...</h3>";
if (is_writable('.')) {
    echo "✅ Directorio escribible<br>";
} else {
    echo "❌ Directorio no escribible. Ajusta permisos.<br>";
}

// 5. Crear archivo de log
echo "<h3>5. Creando archivo de log...</h3>";
if (touch('deploy.log')) {
    chmod('deploy.log', 0666);
    echo "✅ Archivo de log creado<br>";
} else {
    echo "❌ No se pudo crear el archivo de log<br>";
}

echo "<h3>✅ Configuración completada</h3>";
echo "<p><strong>Siguiente paso:</strong> Configurar el webhook en GitHub</p>";
?>
