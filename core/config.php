<?php

if( !file_exists(_PATH_.'core/settings.php') )
{
    if( file_exists(_PATH_.'install') )
        header('Location: install/');
    else
        die('Error: El directorio de instalación no existe.');
}

require_once('settings.php');

/* Configuración */
date_default_timezone_set('Europe/Madrid');

/* Configuramos dominio */
if( _DEBUG_ )
    define( '_DOMINIO_', _ROOT_DOMINIO_DEV_ );
else
    define( '_DOMINIO_', _ROOT_DOMINIO_ );

define( '_ASSETS_',_DOMINIO_.'assets/' );
define( '_ASSETS_PATH_',_PATH_.'assets/' );
define( '_RESOURCES_',_DOMINIO_.'resources/' );
define( '_RESOURCES_PATH_',_PATH_.'resources/' );
define( '_CSS_',_DOMINIO_.'css/' );
define( '_JS_',_DOMINIO_.'js/' );
define( '_INCLUDES_',_PATH_.'includes/' );
define( '_ADMIN_', 'admin/' );
define( '_PUBLIC_', 'public/' );
define( '_COMMON_', 'common/' );
define( '_WARNING_MAIL_', 'info@equipo5.es' );
define( '_REDIRECT_TO_ADMIN_', true ); // Nueva constante para controlar la redirección
$_SESSION['admin_vars']['entorno'] = _ADMIN_;

/* Configuracion de Base de datos */
if( _DEBUG_ )
{
    define( 'bd_name', _BD_NAME_DEV_ );
    define( 'bd_host', _BD_HOST_DEV_ );
    define( 'bd_user', _BD_USER_DEV_ );
    define( 'bd_pass', _BD_PASS_DEV_ );
}
else
{
    define( 'bd_name', _BD_NAME_ );
    define( 'bd_host', _BD_HOST_ );
    define( 'bd_user', _BD_USER_ );
    define( 'bd_pass', _BD_PASS_ );
}

/* Metodos de pago */
if( _DEBUG_ )
{
    define( '_TPV_PRODUCCION_', '0' );
    define( '_PAYPAL_PRODUCCION_', '0' );
}
else
{
    define( '_TPV_PRODUCCION_', '1' );
    define( '_PAYPAL_PRODUCCION_', '1' );
}

/* Configuración de API */
if( _DEBUG_ )
{
    // Token de desarrollo - más simple para testing
    define( '_API_TOKEN_', 'dev_appet_2024_7f8e9d1c2b3a4e5f6789abcdef012345' );
}
else
{
    // Token de producción - más seguro y complejo
    define( '_API_TOKEN_', 'prod_appet_2024_a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456' );
}

/* Configuración adicional de seguridad API */
define( '_API_VERSION_', 'v1' );
define( '_API_RATE_LIMIT_', 1000 ); // Peticiones por hora
define( '_API_RATE_WINDOW_', 3600 ); // Ventana de tiempo en segundos
