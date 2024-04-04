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
if (!file_exists(log_folder)) {
    mkdir(log_folder);
}
define( 'log_max_kb', 2048 );
ini_set("log_errors", 1);
ini_set("error_log", log_folder . "/PHP_errors_" . date("Ymd") . ".log");

// Incluimos configuracion
require dirname(__FILE__).'/config.php';

require _PATH_.'/vendor/autoload.php';

require_once _PATH_.'core/App/Autoload.php';

// Autoload de todas las clases
spl_autoload_register(array(Autoload::getInstance(), 'load'));

// Función genérica var_dump
function vd($var1, $var2='')
{
    if( empty($var2) )
    {
        echo '<pre>';
        var_dump($var1);
        echo '</pre>';
    }
    else
    {
        echo '<div style="width:50%; float:left;"><pre>';
        var_dump($var1);
        echo '</pre></div>';
        echo '<div style="width:50%; float:left;"><pre>';
        var_dump($var2);
        echo '</pre></div><div style="clear:both;"></div>';
    }
}

function el($var, $base64_encode=true, $json_encode=true)
{
    if( $base64_encode && $json_encode )
        $var_to_dump = base64_encode(json_encode($var));
    elseif( $base64_encode && !$json_encode )
        $var_to_dump = base64_encode($var);
    elseif( !$base64_encode && $json_encode )
        $var_to_dump = json_encode($var);
    else
        $var_to_dump = $var;

    error_log($var_to_dump);
}
/**
 * @param string|array|object $message
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
            $destino = _RECEPTOR_;
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
    is_string($message) ?: $message=json_encode($message);
    $destiny = $destino == '' ? log_folder."{$name}_".date('Ymd').".log" : $destino;
    $description = date('Y-m-d H:i:s')." - ".$message."\r\n";
    return error_log($description, $tipo, $destiny);
}
//Declaramos funcion idiomas
function l($shortcode, $vars=array())
{
    $texto = $_SESSION['traducciones'][$shortcode]->contenido;
    if( $texto == '' )
        return 'untranslated';
    if( !empty($vars) )
        $texto = vsprintf($texto, $vars);
    return $texto;
}

Idiomas::setLanguage();

$controllers = new Controllers;
$controllers->load();
