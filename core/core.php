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
require_once _PATH_.'core/config.php';

require_once _PATH_.'core/utils.php';

require_once _PATH_.'vendor/autoload.php';

require_once _PATH_.'core/App/Autoload.php';

// Autoload de todas las clases
spl_autoload_register(array(Autoload::getInstance(), 'load'));

if( _MULTI_LANGUAGE_ )
     Idiomas::setLanguage();

$controllers = new Controllers;
$controllers->load();
