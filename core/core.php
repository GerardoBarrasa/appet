<?php

/*
     ----   ----    ---    ----
    |      |    |  | _ |  |_
    |      |    |  |  \   |
     ----   ----   -   -   ----   4.1
*/

/**
 * @author Anelis Network
 */
ini_set('session.gc_maxlifetime', 43200);
@session_start();

// Definimos constantes
define( 'DS', DIRECTORY_SEPARATOR );
define( '_PATH_', str_replace(DS.'core',DS,dirname(__FILE__)) );
define( 'log_folder', _PATH_.'log/' );
define( 'log_max_kb', 2048 );
if (!file_exists(log_folder)) {
    mkdir(log_folder);
}
ini_set("log_errors", 1);
ini_set("error_log", log_folder . "/PHP_errors_" . date("Ymd") . ".log");

// Incluimos configuracion
require_once _PATH_.'core/config.php';

require_once _PATH_.'core/utils.php';

require_once _PATH_.'vendor/autoload.php';

require_once _PATH_.'core/App/Autoload.php';

// Autoload de todas las clases
spl_autoload_register(array(Autoload::getInstance(), 'load'));

/**
 * @param string|array $message
 * @param int $type
 * @param string $fichero
 * @return bool
 */
function __log_error($message = 'Error inesperado', int $type = 3, string $fichero = ''): bool
{
    $tipo = $type;
    $name = $fichero=='' ? 'errores_varios' : "debug_".$fichero;
    $destino = '';
    switch ($type){
        case 1:
            $destino = _WARNING_MAIL_;
            break;
        case 0:// Error con fichero personalizado para crear un log aparte para debug
            $tipo = 3;
            break;
        case 99:// Error de query, lo añadimos a otro fichero diferente
            $tipo = 3;
            $name = "errores_query";
            break;
        default:// Error general
            $tipo = 3;
    }
    !is_array($message) ?: $message = json_encode($message);
    $destiny = $destino == '' ? log_folder."{$name}_".date('Ymd').".log" : $destino;
    $description = date('Y-m-d H:i:s')." - ".$message."\r\n";
    return error_log($description, $tipo, $destiny);
}

if( _MULTI_LANGUAGE_ )
     Idiomas::setLanguage();

$controllers = new Controllers;
$controllers->load();
