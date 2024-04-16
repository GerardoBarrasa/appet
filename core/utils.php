<?php
//var_dump custom
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

//error_log custom
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

//Obtener una traducción
function l($shortcode, $vars=array())
{
    $return = 'untranslated';
    if( !empty(Traducciones::$traducciones[$shortcode]) )
    {
        $texto = Traducciones::$traducciones[$shortcode];
        if( !empty($vars) )
            $texto = vsprintf($texto, $vars);
        $return = $texto;
    }
    return $return;
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
    $name = $fichero=='' ? 'debug' : "debug_".$fichero;
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