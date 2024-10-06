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
