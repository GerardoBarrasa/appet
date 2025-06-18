<?php

/**
 * Clase Tools - Utilidades y herramientas del sistema
 *
 * Proporciona funciones de utilidad para fechas, strings, arrays, seguridad,
 * archivos, validación y muchas otras operaciones comunes del sistema.
 */
class Tools
{
    /**
     * Configuración de la clase Tools
     */
    protected static $config = [
        'timezone' => 'Europe/Madrid',
        'date_format' => 'Y-m-d H:i:s',
        'upload_max_size' => 10485760, // 10MB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'],
        'password_min_length' => 8,
        'pagination_default_limit' => 12
    ];

    // ==========================================
    // FUNCIONES PARA FECHAS Y TIEMPO
    // ==========================================

    /**
     * Fecha y hora actual en formato para base de datos
     *
     * @param string $format Formato de fecha personalizado
     * @return string Fecha formateada
     */
    public static function datetime($format = null)
    {
        $format = $format ?: self::$config['date_format'];
        return date($format);
    }

    /**
     * Convierte fecha de YYYY-MM-DD a DD/MM/YYYY
     *
     * @param string $input Fecha en formato YYYY-MM-DD
     * @param string $separator Separador a usar
     * @return string Fecha formateada
     */
    public static function fecha($input, $separator = '/')
    {
        if (empty($input) || $input === '0000-00-00') {
            return '';
        }

        try {
            $date = new DateTime($input);
            return $date->format("d{$separator}m{$separator}Y");
        } catch (Exception $e) {
            return $input; // Devolver original si hay error
        }
    }

    /**
     * Convierte datetime a fecha y hora española
     *
     * @param string $input Fecha en formato YYYY-MM-DD H:i:s
     * @param string $dateSeparator Separador de fecha
     * @param bool $includeSeconds Incluir segundos
     * @return string Fecha y hora formateada
     */
    public static function fechaConHora($input, $dateSeparator = '/', $includeSeconds = false)
    {
        if (empty($input) || $input === '0000-00-00 00:00:00') {
            return '';
        }

        try {
            $date = new DateTime($input);
            $timeFormat = $includeSeconds ? 'H:i:s' : 'H:i';
            return $date->format("d{$dateSeparator}m{$dateSeparator}Y {$timeFormat}");
        } catch (Exception $e) {
            return $input;
        }
    }

    /**
     * Calcula años transcurridos entre fechas
     *
     * @param string $fechaInicio Fecha de inicio
     * @param string $fechaFin Fecha de fin (por defecto hoy)
     * @return int Años transcurridos
     */
    public static function calcularAniosTranscurridos($fechaInicio, $fechaFin = '')
    {
        if (empty($fechaInicio)) {
            return 0;
        }

        try {
            $inicio = new DateTime($fechaInicio);
            $fin = new DateTime($fechaFin ?: date('Y-m-d'));
            return $inicio->diff($fin)->y;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Calcula diferencia entre fechas en días
     *
     * @param string $fechaInicio Fecha de inicio
     * @param string $fechaFin Fecha de fin
     * @return int Días de diferencia
     */
    public static function calcularDiasTranscurridos($fechaInicio, $fechaFin = '')
    {
        if (empty($fechaInicio)) {
            return 0;
        }

        try {
            $inicio = new DateTime($fechaInicio);
            $fin = new DateTime($fechaFin ?: date('Y-m-d'));
            return $inicio->diff($fin)->days;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Formatea una fecha de forma relativa (hace X tiempo)
     *
     * @param string $fecha Fecha a formatear
     * @param string $idioma Idioma para el formato
     * @return string Fecha relativa
     */
    public static function fechaRelativa($fecha, $idioma = 'es')
    {
        if (empty($fecha)) {
            return '';
        }

        try {
            $fechaObj = new DateTime($fecha);
            $ahora = new DateTime();
            $diff = $ahora->diff($fechaObj);

            $textos = [
                'es' => [
                    'ahora' => 'ahora mismo',
                    'minutos' => 'hace %d minutos',
                    'minuto' => 'hace 1 minuto',
                    'horas' => 'hace %d horas',
                    'hora' => 'hace 1 hora',
                    'dias' => 'hace %d días',
                    'dia' => 'hace 1 día',
                    'semanas' => 'hace %d semanas',
                    'semana' => 'hace 1 semana',
                    'meses' => 'hace %d meses',
                    'mes' => 'hace 1 mes',
                    'años' => 'hace %d años',
                    'año' => 'hace 1 año'
                ]
            ];

            $t = $textos[$idioma] ?? $textos['es'];

            if ($diff->y > 0) {
                return sprintf($diff->y == 1 ? $t['año'] : $t['años'], $diff->y);
            } elseif ($diff->m > 0) {
                return sprintf($diff->m == 1 ? $t['mes'] : $t['meses'], $diff->m);
            } elseif ($diff->d > 7) {
                $semanas = floor($diff->d / 7);
                return sprintf($semanas == 1 ? $t['semana'] : $t['semanas'], $semanas);
            } elseif ($diff->d > 0) {
                return sprintf($diff->d == 1 ? $t['dia'] : $t['dias'], $diff->d);
            } elseif ($diff->h > 0) {
                return sprintf($diff->h == 1 ? $t['hora'] : $t['horas'], $diff->h);
            } elseif ($diff->i > 0) {
                return sprintf($diff->i == 1 ? $t['minuto'] : $t['minutos'], $diff->i);
            } else {
                return $t['ahora'];
            }
        } catch (Exception $e) {
            return $fecha;
        }
    }

    /**
     * Valida si una fecha es válida
     *
     * @param string $fecha Fecha a validar
     * @param string $formato Formato esperado
     * @return bool
     */
    public static function validarFecha($fecha, $formato = 'Y-m-d')
    {
        if (empty($fecha)) {
            return false;
        }

        $d = DateTime::createFromFormat($formato, $fecha);
        return $d && $d->format($formato) === $fecha;
    }

    // ==========================================
    // FUNCIONES PARA STRINGS
    // ==========================================

    /**
     * Obtiene la extensión de un archivo en minúsculas
     *
     * @param string $file Nombre del archivo
     * @return string Extensión sin punto
     */
    public static function getExtension($file)
    {
        if (empty($file)) {
            return '';
        }

        $pathInfo = pathinfo($file);
        return strtolower($pathInfo['extension'] ?? '');
    }

    /**
     * Convierte un string a URL amigable
     *
     * @param string $var String a convertir
     * @param bool $allowDot Permitir puntos
     * @param int $maxLength Longitud máxima
     * @return string URL amigable
     */
    public static function urlAmigable($var, $allowDot = true, $maxLength = 100)
    {
        if (empty($var)) {
            return '';
        }

        // Convertir caracteres especiales
        $var = self::removeAccents($var);

        // Convertir a minúsculas
        $var = strtolower($var);

        // Definir patrón según si se permiten puntos
        $pattern = $allowDot ? "/[^a-z0-9.\-_]+/" : "/[^a-z0-9\-_]+/";

        // Reemplazar caracteres no permitidos con guiones
        $var = preg_replace($pattern, "-", $var);

        // Eliminar guiones múltiples
        $var = preg_replace('/-+/', '-', $var);

        // Eliminar guiones al inicio y final
        $var = trim($var, '-');

        // Limitar longitud
        if (strlen($var) > $maxLength) {
            $var = substr($var, 0, $maxLength);
            $var = trim($var, '-');
        }

        return $var;
    }

    /**
     * Elimina acentos y caracteres especiales
     *
     * @param string $string String a procesar
     * @return string String sin acentos
     */
    public static function removeAccents($string)
    {
        $unwanted = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ā' => 'a', 'ã' => 'a', 'å' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e', 'ē' => 'e', 'ė' => 'e', 'ę' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i', 'ī' => 'i', 'į' => 'i', 'ı' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'ō' => 'o', 'õ' => 'o', 'ø' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u', 'ū' => 'u', 'ų' => 'u',
            'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n',
            'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
            'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A', 'Ā' => 'A', 'Ã' => 'A', 'Å' => 'A',
            'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E', 'Ē' => 'E', 'Ė' => 'E', 'Ę' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I', 'Ī' => 'I', 'Į' => 'I', 'İ' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O', 'Ō' => 'O', 'Õ' => 'O', 'Ø' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U', 'Ū' => 'U', 'Ų' => 'U',
            'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N', 'Ņ' => 'N',
            'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C'
        ];

        return strtr($string, $unwanted);
    }

    /**
     * Limita caracteres y añade puntos suspensivos
     *
     * @param int $caracteres Cantidad de caracteres
     * @param string $string Cadena a limitar
     * @param bool $dots Añadir puntos suspensivos
     * @param bool $wordBoundary Cortar en límite de palabra
     * @return string String limitado
     */
    public static function cortarString($caracteres, $string, $dots = true, $wordBoundary = true)
    {
        if (empty($string) || strlen($string) <= $caracteres) {
            return $string;
        }

        $truncated = substr($string, 0, $caracteres);

        if ($wordBoundary) {
            $lastSpace = strrpos($truncated, ' ');
            if ($lastSpace !== false && $lastSpace > $caracteres * 0.7) {
                $truncated = substr($truncated, 0, $lastSpace);
            }
        }

        return $truncated . ($dots ? '...' : '');
    }

    /**
     * Detecta si el string es UTF-8
     *
     * @param string $string String a verificar
     * @return bool
     */
    public static function isUtf8($string)
    {
        return mb_check_encoding($string, 'UTF-8');
    }

    /**
     * Valida formato de email
     *
     * @param string $email Email a validar
     * @return bool
     */
    public static function isEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida formato de URL
     *
     * @param string $url URL a validar
     * @return bool
     */
    public static function isUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Valida número de teléfono
     *
     * @param string $phone Teléfono a validar
     * @return bool
     */
    public static function isPhone($phone)
    {
        $pattern = '/^[\+]?[0-9\s\-$$$$]{7,15}$/';
        return preg_match($pattern, $phone);
    }

    /**
     * Encripta o desencripta un string en base64
     *
     * @param string $var Cadena a procesar
     * @param string $tipo encrypt|decrypt
     * @param int $count Número de iteraciones
     * @return string Resultado procesado
     */
    public static function b64($var, $tipo = 'encrypt', $count = 10)
    {
        if (empty($var)) {
            return '';
        }

        $result = $var;

        for ($i = 0; $i < $count; $i++) {
            if ($tipo == 'encrypt') {
                $result = base64_encode($result);
            } elseif ($tipo == 'decrypt') {
                $result = base64_decode($result);
                if ($result === false) {
                    return ''; // Error en decodificación
                }
            }
        }

        return $result;
    }

    /**
     * Busca palabras de un string en un array
     *
     * @param array $array Array donde buscar
     * @param string $str String a buscar
     * @param bool $caseSensitive Búsqueda sensible a mayúsculas
     * @return bool
     */
    public static function searchStrInArray($array, $str, $caseSensitive = false)
    {
        if (empty($array) || empty($str)) {
            return false;
        }

        foreach ($array as $item) {
            $haystack = $caseSensitive ? $str : strtolower($str);
            $needle = $caseSensitive ? $item : strtolower($item);

            if (strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Genera contraseña aleatoria segura
     *
     * @param int $length Longitud de la contraseña
     * @param string $flag Tipo de contraseña
     * @param bool $includeSymbols Incluir símbolos
     * @return string Contraseña generada
     */
    public static function passwdGen($length = 12, $flag = 'ALPHANUMERIC', $includeSymbols = false)
    {
        $length = max(4, (int)$length);

        $chars = '';
        switch ($flag) {
            case 'NUMERIC':
                $chars = '0123456789';
                break;
            case 'NO_NUMERIC':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 'RANDOM':
                return substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes($length))), 0, $length);
            case 'ALPHANUMERIC':
            default:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }

        if ($includeSymbols && $flag !== 'NUMERIC') {
            $chars .= '!@#$%^&*()_+-=[]{}|;:,.<>?';
        }

        $password = '';
        $charsLength = strlen($chars);

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $charsLength - 1)];
        }

        return $password;
    }

    // ==========================================
    // FUNCIONES PARA ARRAYS
    // ==========================================

    /**
     * Convierte array a array numérico si es necesario
     *
     * @param array $array Array a procesar
     * @return array Array procesado
     */
    public static function arrayPassing($array)
    {
        if (!is_array($array)) {
            return [$array];
        }

        if (!isset($array[0]) || (count($array) == 1 && !empty($array[0]))) {
            return [$array];
        }

        return $array;
    }

    /**
     * Filtra array por clave y valor
     *
     * @param array $array Array a filtrar
     * @param string $key Clave a buscar
     * @param mixed $value Valor a buscar
     * @return array Array filtrado
     */
    public static function arrayFilter($array, $key, $value)
    {
        return array_filter($array, function($item) use ($key, $value) {
            return isset($item[$key]) && $item[$key] == $value;
        });
    }

    /**
     * Agrupa array por clave
     *
     * @param array $array Array a agrupar
     * @param string $key Clave para agrupar
     * @return array Array agrupado
     */
    public static function arrayGroupBy($array, $key)
    {
        $result = [];
        foreach ($array as $item) {
            $groupKey = is_object($item) ? $item->$key : $item[$key];
            $result[$groupKey][] = $item;
        }
        return $result;
    }

    /**
     * Extrae columna de array de objetos/arrays
     *
     * @param array $array Array fuente
     * @param string $column Columna a extraer
     * @param string $indexKey Clave para indexar (opcional)
     * @return array Array con valores extraídos
     */
    public static function arrayColumn($array, $column, $indexKey = null)
    {
        if (function_exists('array_column')) {
            return array_column($array, $column, $indexKey);
        }

        $result = [];
        foreach ($array as $item) {
            $value = is_object($item) ? $item->$column : $item[$column];

            if ($indexKey !== null) {
                $key = is_object($item) ? $item->$indexKey : $item[$indexKey];
                $result[$key] = $value;
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Genera breadcrumbs en HTML
     *
     * @param array $array Array de breadcrumbs
     * @param bool $homeDefault Incluir enlace a home
     * @param string $separator Separador visual
     * @return string HTML de breadcrumbs
     */
    public static function breadcrumbs($array, $homeDefault = true, $separator = '/')
    {
        $result = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';

        if ($homeDefault) {
            $result .= '<li class="breadcrumb-item"><a href="' . _DOMINIO_ . '">Inicio</a></li>';
        }

        $count = count($array);
        $current = 0;

        foreach ($array as $title => $url) {
            $current++;
            $isLast = ($current === $count);

            if (!empty($url) && !$isLast) {
                $result .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($title) . '</a></li>';
            } else {
                $result .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($title) . '</li>';
            }
        }

        $result .= '</ol></nav>';
        return $result;
    }

    // ==========================================
    // FUNCIONES DE SEGURIDAD
    // ==========================================

    /**
     * Encripta un string con MD5 y token de seguridad
     *
     * @param string $val Texto a encriptar
     * @return string String encriptado
     */
    public static function md5($val)
    {
        if (empty($val)) {
            return '';
        }

        $val = md5($val);
        $val = $val . _SECURITY_TOKEN_ . $val . _SECURITY_TOKEN_ . $val;
        return md5(md5($val));
    }

    /**
     * Hash seguro usando password_hash
     *
     * @param string $password Contraseña a hashear
     * @param int $algo Algoritmo a usar
     * @return string Hash generado
     */
    public static function hashPassword($password, $algo = PASSWORD_DEFAULT)
    {
        return password_hash($password, $algo);
    }

    /**
     * Verifica hash de contraseña
     *
     * @param string $password Contraseña en texto plano
     * @param string $hash Hash a verificar
     * @return bool
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Genera token CSRF seguro
     *
     * @param int $length Longitud del token
     * @return string Token generado
     */
    public static function generateCSRFToken($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Comprueba si una variable existe en POST o GET
     *
     * @param string $key Nombre de la variable
     * @return bool
     */
    public static function getIsset($key)
    {
        if (!isset($key) || empty($key) || !is_string($key))
            return false;

        return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
    }

    /**
     * Obtiene un valor de POST o GET. Si no está disponible devuelve $default_value
     *
     * @param string $key Nombre de la variable
     * @param mixed $default_value (opcional)
     * @return mixed Valor
     */
    public static function getValue($key, $default_value = false)
    {
        if (!isset($key) || empty($key) || !is_string($key)) {
            return false;
        }

        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default_value));

        if (is_string($ret)) {
            return stripslashes(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret))));
        }

        return $ret;
    }


    /*
    |--------------------------------------------------------------------------
    | Funciones de utilidad
    |--------------------------------------------------------------------------
    */

    /**
     * Genera mensaje de alerta en HTML con el texto enviado
     *
     * @param string $texto Texto
     * @return html
     */
    public static function warning($texto)
    {
        return '<div class="warning"><img src="'._DOMINIO_.'img/warning.png" align="absmiddle"> '.$texto.'</div>';
    }

    /**
     * Genera mensaje de confirmacion en HTML con el texto enviado
     *
     * @param string $texto Texto
     * @return html
     */
    public static function confirm($texto)
    {
        return '<div class="confirm"><img src="'._DOMINIO_.'img/ok.png" align="absmiddle"> '.$texto.'</div>';
    }

    /**
     * Redirección mediante JavaScript
     *
     * @param string $url redirecciona a la url indicada mediante el string
     */
    public static function location($url,$time=0)
    {
        if( $time != 0 )
        {
            ?>
            <script language="javascript">
                setTimeout("document.location='<?=$url?>'",<?=$time?>);
            </script>
            <?php
        }
        else
        {
            ?>
            <script language="javascript">
                document.location="<?=$url?>";
            </script>
            <?php
        }
    }

    /**
     * Mensaje que aparecerá al redireccionar. Muestra un logo y un gif que se encuentran en la carpeta img
     *
     * @param string $message Cadena de caracteres que quieres mostrar.
     */
    public static function redirMessage($message)
    {
        ?>
        <div style="position:absolute; width:100%; height:100%; background-color:#000; top:0; left:0; z-index:2000; opacity: 0.7; filter: alpha(opacity=75);"></div>
        <div style="position:fixed; top:50%; left:50%; background-color:#fff; width:500px; height:300px; margin-left:-250px; border-radius:5px; margin-top:-150px; border:1px solid #ccc; z-index:3000; text-align:center">
            <br /><br /><br />
            <img src="<?=_DOMINIO_?>img/logo.png" class="flat" />
            <br /><br />
            <span style="font-family:Bree Serif; font-size:20px; color:#666;"><?=$message?></span>
            <br /><br /><br />
            <img src="<?=_DOMINIO_?>img/loading.gif" class="flat" />
        </div>
        <?php
    }

    /**
     * Sube imagen (si la hay) a la ruta mandada
     *
     * @param string $ruta ruta a la que queremos mandar la imagen
     * @param string $nombre
     * @param string $nombre_imagen
     */
    public static function uploadImage($ruta, $nombre, $nombre_imagen)
    {
        if($_FILES[$nombre]["name"] != '')
        {
            $ruta_completa = _PATH_ . $ruta;

            //Comprobamos el directorio ya que si no lo creamos
            if(!file_exists($ruta_completa))
                mkdir ($ruta_completa, 0777, true);

            $nombrecompleto = $_FILES[$nombre]["name"];
            $extension = self::getExtension($nombrecompleto);

            if($extension == "jpg" || $extension == "JPG" ||$extension == "jpeg" || $extension == "JPEG" || $extension == "png" || $extension == "PNG")
            {
                $ico = "$nombre_imagen.$extension";
                $temp = $_FILES[$nombre]['tmp_name'];
                $ruta_bd = $ruta . $ico;	// Ruta que se almacenará en la base de datos para mostrar.
                $ruta = _PATH_.$ruta;		// Ruta absoluta desde el path. Para agregar la imagen al sitio que le corresponde.
                move_uploaded_file($temp, $ruta . $ico);
                return array(
                    'type' => 'success',
                    'data' => $ruta_bd
                );
            }
            else
            {
                return array(
                    'type' => 'error',
                    'error' => 'El archivo no tiene una extensión válida.'
                );
            }
        }
        else
        {
            return array(
                'type' => 'error',
                'error' => 'Ha habido un problema subiendo el archivo.'
            );
        }
    }

    /**
     * Crear Thumbnail
     */
    public static function thumb($file,$upload,$width,$height,$type='fit',$name='')
    {
        if( empty($name) )
        {
            $nombre_img = explode('/',$file);
            $nombre_img = $nombre_img[count($nombre_img)-1];
        }
        else
            $nombre_img = $name;

        $extension = self::getExtension($file);

        if( $extension == "jpeg" || $extension == "jpg" )
            $img_original = imagecreatefromjpeg($file);
        elseif( $extension == "png" )
            $img_original = imagecreatefrompng($file);

        list($ancho, $alto) = getimagesize($file);

        $x1 = 0;
        $y1 = 0;
        $x2 = 0;
        $y2 = 0;

        //Redimensionamos deformando imagen
        if( $type == 'resize' )
        {
            $ancho_final = $width;
            $alto_final = $height;
            $ancho_imagen = $ancho_final;
            $alto_imagen = $alto_final;
        }

        //Redimensionamos sin deformar imagen. En fit: Cambiamos un valor dependiendo de cual es mas grande.
        if( $type == 'fit' || $type == 'crop' )
        {
            $max_ancho = $width;
            $max_alto = $height;
            $ancho_ratio = $max_ancho / $ancho;
            $alto_ratio = $max_alto / $alto;
            if( $ancho >= $alto )
            {
                $ancho_final = $width;
                $alto_final = ceil($ancho_ratio * $alto);
            }
            else
            {
                $alto_final = $height;
                $ancho_final = ceil($alto_ratio * $ancho);
            }
            $ancho_imagen = $ancho_final;
            $alto_imagen = $alto_final;
        }

        //En crop: mantenemos tamaño añadiendo blanco
        if( $type == 'crop' )
        {
            if( $alto_final < $height )
            {
                $margen = ($height-$alto_final)/2;
                $y1 = $margen;
            }
            if( $ancho_final < $width )
            {
                $margen = ($width-$ancho_final)/2;
                $x1 = $margen;
            }

            $ancho_imagen = $width;
            $alto_imagen = $height;
        }

        $thumbnail_tmp = imagecreatetruecolor($ancho_imagen,$alto_imagen);
        $trans_colour = imagecolorallocatealpha($thumbnail_tmp, 255, 255, 255, 127);
        imagefill($thumbnail_tmp, 0, 0, $trans_colour);

        imagecopyresampled($thumbnail_tmp, $img_original, $x1, $y1, $x2, $y2, $ancho_final, $alto_final, $ancho, $alto);
        imagedestroy($img_original);

        $calidad = 100;
        $ruta_carpeta_thumbnail = _PATH_ . $upload . $nombre_img;

        if( !file_exists(_PATH_.$upload) )
            mkdir(_PATH_.$upload, 0777, true);

        if($extension == "jpeg" || $extension == "jpg")
            return imagejpeg($thumbnail_tmp, $ruta_carpeta_thumbnail,$calidad);
        elseif($extension == "png")
            return imagepng($thumbnail_tmp,$ruta_carpeta_thumbnail);
    }

    /**
     * Crea paginador
     *
     * @param int $page Pagina
     * @param int $cantidad_por_pagina
     * @param string|array $clase Clase de Funks. Si es array 'prefix' y 'clase'
     * @param string $nombre_funcion_clase Nombre de la funcion de la clase $clase que se llamará
     * @param string $nombre_funcion_js Nobre de la funcion javascript que ejecuta el ajax
     * @param string $extra_data Datos extra del paginador
     * @param string $size Tamaño del paginador (''|'sm'|'lg')
     * @param string $alineacion Alineación del paginador (''|'center'|'end')
     */
    public static function getPaginador($page, $cantidad_por_pagina, $clase, $nombre_funcion_clase, $nombre_funcion_js, $extra_data='', $size = '', $alineacion = '')
    {
        //Total de pujas realizadas para calcular si hay mas pujas que paginas
        $object_result = $clase::$nombre_funcion_clase(0, $cantidad_por_pagina, false);
        if( isset($object_result['total']) )
            $cantidad_total = $object_result['total'];
        else
            $cantidad_total = count($clase::$nombre_funcion_clase(0, $cantidad_por_pagina, false));

        if ( $cantidad_total > $cantidad_por_pagina ) {

            $paginas = ceil($cantidad_total/$cantidad_por_pagina);

            ?>
            <nav>
                <ul class="pagination <?=(!empty($size) ? 'pagination-'.$size : '')?> <?=(!empty($alineacion) ? 'justify-content-'.$alineacion : '')?>">
                    <?php
                    if($page != 1){
                        $pagina_anterior = $page-1;
                        $comienzo = ($pagina_anterior-1) * $cantidad_por_pagina;
                        ?>
                        <li class="prev page-item">
                            <a class="page-link" href="javascript:void(0)" onclick="<?=$nombre_funcion_js?>(<?=$comienzo?>, <?=$cantidad_por_pagina?>, <?=$pagina_anterior?><?=(!empty($extra_data)) ? ', '.$extra_data : '';?>);">
                                <i class="fa fa-angle-double-left"></i>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                    <?php
                    $show_pages = 10;
                    $start = $page > ($show_pages/2) ? $page-($show_pages/2) : 1;
                    $finish = $start+$show_pages > $paginas ? $paginas : $start+$show_pages;
                    $start = $finish-$show_pages < 1 ? 1 : $finish-$show_pages;
                    for ( $i=$start; $i<=$finish; $i++ ) {
                        $comienzo = ($i-1) * $cantidad_por_pagina;
                        ?>
                        <li class="page-item <?=$page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="javascript:void(0)" onclick="<?=$nombre_funcion_js?>(<?=$comienzo?>, <?=$cantidad_por_pagina?>, <?=$i?><?=(!empty($extra_data)) ? ', '.$extra_data : '';?>);">
                                <?=$i?>
                            </a>
                        </li>
                        <?php
                    }

                    if($page != $finish){
                        $pagina_siguiente= $page+1;
                        $comienzo = ($pagina_siguiente-1) * $cantidad_por_pagina;
                        ?>
                        <li class="next page-item">
                            <a class="page-link" href="javascript:void(0)" onclick="<?=$nombre_funcion_js?>(<?=$comienzo?>, <?=$cantidad_por_pagina?>, <?=$pagina_siguiente?><?=(!empty($extra_data)) ? ', '.$extra_data : '';?>);">
                                <i class="fa fa-angle-double-right"></i>
                            </a>
                        </li>
                        <?php
                    }

                    ?>
                </ul>
            </nav>
            <?php
        }
    }

    /**
     * Crea paginador
     *
     * @param string|array $data String o array de datos. Hay que tenerlo en cuenta en front
     * @param string $type Tipo de respuesta ('success'|'error')
     * @param string $error Mensaje de error
     * @param int $codigoEstado Código HTTP
     */
    private function result($data = false, $type = 'success', $error = false, $codigoEstado = 200)
    {
        header("Content-Type:application/json");
        header("HTTP/1.1 $codigoEstado $type");

        $response = array( 'type'  => $type );

        if( $response['type'] === 'error' )
            $response['error'] = $error;

        if( $response['type'] === 'success' )
            $response['data'] = $data;

        echo json_encode(Tools::arrayUtf8($response));

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Funciones para cargar librerias
    |--------------------------------------------------------------------------
    */

    /**
     * Cargamos FontAwesome
     */
    public static function loadFontawesome()
    {
        ?>
        <link rel="stylesheet" href="<?=_DOMINIO_?>assets/fontawesome/font-awesome.min.css">
        <?php
    }

    /**
     * Cargamos Bootstrap
     *
     * @param string $type both|js|css
     */
    public static function loadBootstrap($type="both")
    {
        if( $type == 'both' || $type == 'css' )
        {
            ?>
            <link rel="stylesheet" href="<?=_DOMINIO_?>assets/bootstrap/bootstrap.min.css">
            <?php
        }

        if( $type == 'both' || $type == 'js' )
        {
            ?>
            <script src="<?=_DOMINIO_?>assets/bootstrap/bootstrap.min.js"></script>
            <?php
        }
    }

    /**
     * Cargamos Sweetalert
     */
    public static function loadSweetalert()
    {
        ?>
        <script src="<?=_DOMINIO_?>assets/sweetalert/sweet-alert.min.js"></script>
        <link rel="stylesheet" href="<?=_DOMINIO_?>assets/sweetalert/sweet-alert.css">
        <?php
    }

    /**
     * Cargamos Tinymce
     */
    public static function loadTinymce()
    {
        /* Poner aqui los id de los textarea */
        ?>
        <script src='<?=_DOMINIO_;?>assets/tinymce/tinymce.min.js'></script>
        <script>
            tinymce.init({
                selector: '#descripcion_larga',
                plugins: "advlist"
            });
        </script>
        <script src='<?=_DOMINIO_;?>assets/tinymce/langs/es.js'></script>
        <?php
    }

    /**
     * Cargamos libreria de la carpeta Helpers
     * @param string $relative_file_path
     */
    public static function loadHelper($relative_file_path)
    {
        require_once _PATH_.'core/Helpers/'.$relative_file_path;
    }

    /**
     * Guarda un array de urls para cargarlos posteriormente en la vista
     * @param string $path
     * @param string $position top|bottom
     */
    public static function registerJavascript($path, $position='bottom')
    {
        if( empty($_SESSION['js_paths']) )
        {
            $_SESSION['js_paths'] = array(
                'top' => array(),
                'bottom' => array()
            );
        }

        if( !empty($_SESSION['js_paths'][$position]) )
        {
            $alreadyExists = false;
            foreach( $_SESSION['js_paths'][$position] as $js )
            {
                if( $path == $js )
                    $alreadyExists = true;
            }

            if( !$alreadyExists )
                $_SESSION['js_paths'][$position][] = $path;
        }
        else
            $_SESSION['js_paths'][$position][] = $path;
    }

    /**
     * Guarda un array de urls para cargarlos posteriormente en la vista
     * @param string $path
     */
    public static function registerStylesheet($path)
    {
        if( !empty($_SESSION['css_paths']) )
        {
            $alreadyExists = false;
            foreach( $_SESSION['css_paths'] as $css )
            {
                if( $path == $css )
                    $alreadyExists = true;
            }

            if( !$alreadyExists )
                $_SESSION['css_paths'][] = $path;
        }
        else
            $_SESSION['css_paths'][] = $path;
    }

    /**
     * Guarda un error o un success en la sesion del servidor
     * @param string $msg			Mensaje que irá en el toast
     * @param string $type			Tipo de alerta (success, error, warning, info)
     * @param int $timer			Timer para que desaparezca el toast
     * @param string $bgColor		Color de fondo del toast
     */
    public static function registerAlert($msg, $type = "error", $timer = 3000, $bgColor = false){
        $types = ["success", "error", "warning", "info"];
        if(in_array($type, $types) && !empty($msg)) {
            $alert = [
                'message' => $msg,
                'type' => $type,
                'timer' => $timer,
                'background' => $bgColor
            ];

            // Agregar la nueva alerta al array
            $_SESSION['alerts'][] = $alert;
        }
    }



    /**
     * @param array|string|object $message
     * @param int $type
     * @param string $fichero
     * @return bool
     */
    public static function logError(array|string|object $message = 'Error inesperado', int $type = 3, string $fichero = ''): bool
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
        !is_array($message) && !is_object($message) ?: $message = json_encode($message);
        $destiny = $destino == '' ? log_folder.$name."_".date('Ymd').".log" : $destino;
        $description = date('Y-m-d H:i:s')." - ".$message."\r\n";
        return error_log($description, $tipo, $destiny);
    }


    /**
     * @param $path
     * @param int $width
     * @param int|string $height
     * @return false|string
     */
    public static function resize_image($path, int $width=300, int|string $height='auto'): false|string
    {
        $fileData = file_get_contents($path);
        $im = imagecreatefromstring($fileData);
        $source_width = imagesx($im);
        $source_height = imagesy($im);
        $ratio =  $source_height / $source_width;

        $new_width = $width; // assign new width to new resized image
        $new_height = $height == 'auto' ? $ratio * $width : $height;

        $thumb = imagecreatetruecolor($new_width, $new_height);

        $transparency = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefilledrectangle($thumb, 0, 0, $new_width, $new_height, $transparency);

        imagecopyresampled($thumb, $im, 0, 0, 0, 0, $new_width, $new_height, $source_width, $source_height);
        ob_start();
        imagepng($thumb);
        $imagedata = ob_get_clean();
        imagedestroy($im);
        return $imagedata;
    }

    /**
     * Obtiene la IP del cliente de forma segura
     *
     * @return string
     */
    public static function getClientIP()
    {
        // Verificar cabeceras de proxy
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['X-Forwarded-For'])) {
                return $headers['X-Forwarded-For'];
            }
        }

        // Verificar variables de servidor comunes para proxies
        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key])) {
                // Si contiene múltiples IPs, tomar la primera
                if (strpos($_SERVER[$key], ',') !== false) {
                    $ips = explode(',', $_SERVER[$key]);
                    return trim($ips[0]);
                }
                return $_SERVER[$key];
            }
        }

        // Si no se encuentra ninguna IP, devolver una predeterminada
        return '0.0.0.0';
    }

    /**
     * Valida si una dirección IP es válida
     *
     * @param string $ip Dirección IP a validar
     * @param bool $allowPrivate Permitir IPs privadas
     * @return bool
     */
    public static function isValidIP($ip, $allowPrivate = true)
    {
        $flags = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6;

        if (!$allowPrivate) {
            $flags |= FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;
    }

    /**
     * Verifica si una IP está en una lista blanca
     *
     * @param string $ip IP a verificar
     * @param array $whitelist Lista de IPs permitidas
     * @return bool
     */
    public static function isIPInWhitelist($ip, $whitelist = [])
    {
        // Lista blanca predeterminada
        $defaultWhitelist = [
            '127.0.0.1',
            '::1'
        ];

        $whitelist = array_merge($defaultWhitelist, $whitelist);

        // Verificar coincidencia exacta
        if (in_array($ip, $whitelist)) {
            return true;
        }

        // Verificar rangos CIDR
        foreach ($whitelist as $range) {
            if (strpos($range, '/') !== false) {
                if (self::isIPInCIDR($ip, $range)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica si una IP está en un rango CIDR
     *
     * @param string $ip IP a verificar
     * @param string $cidr Rango CIDR (ej: 192.168.1.0/24)
     * @return bool
     */
    public static function isIPInCIDR($ip, $cidr)
    {
        list($subnet, $bits) = explode('/', $cidr);

        // Convertir IP y subred a binario
        $ipBinary = ip2long($ip);
        $subnetBinary = ip2long($subnet);
        $mask = -1 << (32 - $bits);

        // Comparar usando la máscara
        return ($ipBinary & $mask) === ($subnetBinary & $mask);
    }

    // ==========================================
    // FUNCIONES DE VALIDACIÓN
    // ==========================================

    /**
     * Valida un nombre completo
     *
     * @param string $nombre Nombre a validar
     * @param int $minLength Longitud mínima
     * @param int $maxLength Longitud máxima
     * @return array Resultado de validación
     */
    public static function validateNombre($nombre, $minLength = 3, $maxLength = 100)
    {
        $result = [
            'valid' => true,
            'errors' => []
        ];

        $nombre = trim($nombre);

        // Verificar que no esté vacío
        if (empty($nombre)) {
            $result['valid'] = false;
            $result['errors'][] = 'El nombre es obligatorio';
            return $result;
        }

        // Verificar longitud mínima
        if (strlen($nombre) < $minLength) {
            $result['valid'] = false;
            $result['errors'][] = "El nombre debe tener al menos {$minLength} caracteres";
        }

        // Verificar longitud máxima
        if (strlen($nombre) > $maxLength) {
            $result['valid'] = false;
            $result['errors'][] = "El nombre no puede tener más de {$maxLength} caracteres";
        }

        // Verificar que solo contenga letras, espacios, acentos y algunos caracteres especiales
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-\'\.]+$/', $nombre)) {
            $result['valid'] = false;
            $result['errors'][] = 'El nombre solo puede contener letras, espacios, guiones y apostrofes';
        }

        // Verificar que no tenga espacios múltiples
        if (preg_match('/\s{2,}/', $nombre)) {
            $result['valid'] = false;
            $result['errors'][] = 'El nombre no puede tener espacios múltiples';
        }

        return $result;
    }

    /**
     * Valida formato y disponibilidad de email
     *
     * @param string $email Email a validar
     * @param int $excludeUserId ID de usuario a excluir (para actualizaciones)
     * @param string $table Tabla donde verificar duplicados
     * @param string $emailField Campo de email en la tabla
     * @param string $idField Campo ID en la tabla
     * @return array Resultado de validación
     */
    public static function validateEmail($email, $excludeUserId = 0, $table = 'usuarios_admin', $emailField = 'email', $idField = 'id_usuario_admin')
    {
        $result = [
            'valid' => true,
            'errors' => []
        ];

        $email = trim(strtolower($email));

        // Verificar que no esté vacío
        if (empty($email)) {
            $result['valid'] = false;
            $result['errors'][] = 'El email es obligatorio';
            return $result;
        }

        // Verificar formato
        if (!self::isEmail($email)) {
            $result['valid'] = false;
            $result['errors'][] = 'El formato del email no es válido';
            return $result;
        }

        // Verificar longitud máxima
        if (strlen($email) > 100) {
            $result['valid'] = false;
            $result['errors'][] = 'El email no puede tener más de 100 caracteres';
        }

        // Verificar disponibilidad en base de datos
        if ($result['valid']) {
            $db = Bd::getInstance();
            $params = [$email];
            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$emailField} = ?";

            if ($excludeUserId > 0) {
                $sql .= " AND {$idField} != ?";
                $params[] = (int)$excludeUserId;
            }

            $count = (int)$db->fetchValueSafe($sql, $params);
            if ($count > 0) {
                $result['valid'] = false;
                $result['errors'][] = 'Este email ya está registrado en el sistema';
            }
        }

        return $result;
    }

    /**
     * Valida fortaleza de contraseña (versión mejorada)
     *
     * @param string $password Contraseña a validar
     * @param int $minLength Longitud mínima
     * @param bool $requireUppercase Requerir mayúsculas
     * @param bool $requireLowercase Requerir minúsculas
     * @param bool $requireNumbers Requerir números
     * @param bool $requireSymbols Requerir símbolos
     * @return array Resultado de validación
     */
    public static function validatePasswordStrength($password, $minLength = null, $requireUppercase = true, $requireLowercase = true, $requireNumbers = true, $requireSymbols = true)
    {
        $minLength = $minLength ?: self::$config['password_min_length'];

        $result = [
            'valid' => true,
            'score' => 0,
            'strength' => 'weak',
            'errors' => [],
            'suggestions' => []
        ];

        // Verificar que no esté vacía
        if (empty($password)) {
            $result['valid'] = false;
            $result['errors'][] = 'La contraseña es obligatoria';
            return $result;
        }

        // Verificar longitud mínima
        if (strlen($password) < $minLength) {
            $result['valid'] = false;
            $result['errors'][] = "La contraseña debe tener al menos {$minLength} caracteres";
        } else {
            $result['score'] += 1;
        }

        // Verificar mayúsculas
        if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = 'La contraseña debe incluir al menos una letra mayúscula';
        } elseif (preg_match('/[A-Z]/', $password)) {
            $result['score'] += 1;
        }

        // Verificar minúsculas
        if ($requireLowercase && !preg_match('/[a-z]/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = 'La contraseña debe incluir al menos una letra minúscula';
        } elseif (preg_match('/[a-z]/', $password)) {
            $result['score'] += 1;
        }

        // Verificar números
        if ($requireNumbers && !preg_match('/[0-9]/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = 'La contraseña debe incluir al menos un número';
        } elseif (preg_match('/[0-9]/', $password)) {
            $result['score'] += 1;
        }

        // Verificar símbolos
        if ($requireSymbols && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = 'La contraseña debe incluir al menos un símbolo especial';
        } elseif (preg_match('/[^A-Za-z0-9]/', $password)) {
            $result['score'] += 1;
        }

        // Verificar patrones comunes débiles
        $weakPatterns = [
            '/^123+/',
            '/^abc+/i',
            '/^qwe+/i',
            '/password/i',
            '/admin/i',
            '/^(.)\1{2,}/', // Caracteres repetidos
        ];

        foreach ($weakPatterns as $pattern) {
            if (preg_match($pattern, $password)) {
                $result['suggestions'][] = 'Evita usar patrones comunes o secuencias obvias';
                break;
            }
        }

        // Calcular fortaleza
        if ($result['score'] >= 4) {
            $result['strength'] = 'strong';
        } elseif ($result['score'] >= 3) {
            $result['strength'] = 'medium';
        }

        // Verificar longitud máxima
        if (strlen($password) > 255) {
            $result['valid'] = false;
            $result['errors'][] = 'La contraseña no puede tener más de 255 caracteres';
        }

        return $result;
    }

    /**
     * Valida múltiples campos de un formulario
     *
     * @param array $fields Array de campos a validar
     * @param array $rules Reglas de validación
     * @return array Resultado de validación
     */
    public static function validateFields($fields, $rules)
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'field_errors' => []
        ];

        foreach ($rules as $fieldName => $fieldRules) {
            $value = $fields[$fieldName] ?? '';
            $fieldErrors = [];

            foreach ($fieldRules as $rule => $params) {
                switch ($rule) {
                    case 'required':
                        if (empty(trim($value))) {
                            $fieldErrors[] = $params['message'] ?? "El campo {$fieldName} es obligatorio";
                        }
                        break;

                    case 'min_length':
                        if (strlen(trim($value)) < $params['value']) {
                            $fieldErrors[] = $params['message'] ?? "El campo {$fieldName} debe tener al menos {$params['value']} caracteres";
                        }
                        break;

                    case 'max_length':
                        if (strlen(trim($value)) > $params['value']) {
                            $fieldErrors[] = $params['message'] ?? "El campo {$fieldName} no puede tener más de {$params['value']} caracteres";
                        }
                        break;

                    case 'email':
                        if (!empty($value) && !self::isEmail($value)) {
                            $fieldErrors[] = $params['message'] ?? "El formato del email no es válido";
                        }
                        break;

                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $fieldErrors[] = $params['message'] ?? "El campo {$fieldName} debe ser numérico";
                        }
                        break;

                    case 'regex':
                        if (!empty($value) && !preg_match($params['pattern'], $value)) {
                            $fieldErrors[] = $params['message'] ?? "El formato del campo {$fieldName} no es válido";
                        }
                        break;
                }
            }
        }

        if (!empty($fieldErrors)) {
            $result['valid'] = false;
            $result['field_errors'][$fieldName] = $fieldErrors;
            $result['errors'] = array_merge($result['errors'], $fieldErrors);
        }
    }

    /**
     * Sanitiza una cadena para prevenir XSS
     *
     * @param string $input Cadena a sanitizar
     * @param bool $allowHtml Permitir HTML básico
     * @return string Cadena sanitizada
     */
    public static function sanitizeInput($input, $allowHtml = false)
    {
        if (empty($input)) {
            return '';
        }

        // Eliminar espacios en blanco al inicio y final
        $input = trim($input);

        if ($allowHtml) {
            // Permitir solo HTML básico y seguro
            $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><a>';
            $input = strip_tags($input, $allowedTags);

            // Sanitizar atributos peligrosos
            $input = preg_replace('/(<[^>]+)(on\w+\s*=\s*["\'][^"\']*["\'])/i', '$1', $input);
            $input = preg_replace('/(<[^>]+)(javascript\s*:)/i', '$1', $input);
        } else {
            // Eliminar todas las etiquetas HTML
            $input = strip_tags($input);
        }

        // Convertir caracteres especiales
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        return $input;
    }

    /**
     * Valida un número de teléfono español
     *
     * @param string $phone Teléfono a validar
     * @return array Resultado de validación
     */
    public static function validateSpanishPhone($phone)
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'formatted' => ''
        ];

        $phone = trim($phone);

        if (empty($phone)) {
            return $result; // Teléfono opcional
        }

        // Eliminar espacios, guiones y paréntesis
        $cleanPhone = preg_replace('/[\s\-$$$$]/', '', $phone);

        // Patrones para teléfonos españoles
        $patterns = [
            '/^(\+34|0034)?[6789]\d{8}$/', // Móviles y fijos españoles
            '/^(\+34|0034)?\d{9}$/' // Formato general español
        ];

        $isValid = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $cleanPhone)) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            $result['valid'] = false;
            $result['errors'][] = 'El formato del teléfono no es válido (debe ser un teléfono español)';
        } else {
            // Formatear el teléfono
            $cleanPhone = preg_replace('/^(\+34|0034)/', '', $cleanPhone);
            $result['formatted'] = '+34 ' . substr($cleanPhone, 0, 3) . ' ' . substr($cleanPhone, 3, 3) . ' ' . substr($cleanPhone, 6, 3);
        }

        return $result;
    }
}
