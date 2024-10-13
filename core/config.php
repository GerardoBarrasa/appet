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
