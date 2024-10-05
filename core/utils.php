<?php
//var_dump custom
function vd($var1, $var2='')
{
    if( empty($var2) )
        dump($var1);
    else
    {
        echo '<div style="width:50%; float:left;"><pre>';
        dump($var1);
        echo '</pre></div><div style="width:50%; float:left;"><pre>';
        dump($var2);
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
    elseif( _DEBUG_ )
    {
        //Si el shortcode no está en el archivo comprobamos que tampoco está en la base de datos para crearlo
        //Solo faltaría realizar la traducción desde el panel
        if( !Traducciones::checkShortcodeExists($shortcode) )
            Traducciones::crearTraduccion($shortcode, 1, '');
        else
            $return = $shortcode.' todavía no ha sido traducido';
    }
    return $return;
}

function pSQL($string, $htmlOK = false)
{
    return Bd::getInstance()->escape($string, $htmlOK);
}

function bqSQL($string)
{
    return str_replace('`', '\`', pSQL($string));
}
