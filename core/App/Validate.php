<?php

class Validate
{
	/**
     * Check for birthDate validity. To avoid year in two digits, disallow date < 200 years ago
     *
     * @param string $date birthdate to validate
     * @param string $format optional format
     *
     * @return bool Validity is ok or not
     */
    public static function isBirthDate($date, $format = 'Y-m-d')
    {
        if (empty($date) || $date == '0000-00-00') {
            return true;
        }

        $d = DateTime::createFromFormat($format, $date);
        if (!empty(DateTime::getLastErrors()['warning_count']) || false === $d) {
            return false;
        }
        $twoHundredYearsAgo = new Datetime();
        $twoHundredYearsAgo->sub(new DateInterval('P200Y'));

        return $d->setTime(0, 0, 0) <= new Datetime() && $d->setTime(0, 0, 0) >= $twoHundredYearsAgo;
    }

    /**
     * Check for boolean validity.
     *
     * @param bool $bool Boolean to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isBool($bool)
    {
        return $bool === null || is_bool($bool) || preg_match('/^(0|1)$/', $bool);
    }

    /**
     * Check for HTML field validity (no XSS please !).
     *
     * @param string $html HTML field to validate
     *
     * @return bool Validity is ok or not
     */
	public static function isCleanHtml($html, $allow_iframe = false)
    {
        $events = 'onmousedown|onmousemove|onmmouseup|onmouseover|onmouseout|onload|onunload|onfocus|onblur|onchange';
        $events .= '|onsubmit|ondblclick|onclick|onkeydown|onkeyup|onkeypress|onmouseenter|onmouseleave|onerror|onselect|onreset|onabort|ondragdrop|onresize|onactivate|onafterprint|onmoveend';
        $events .= '|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus|onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onmove';
        $events .= '|onbounce|oncellchange|oncontextmenu|oncontrolselect|oncopy|oncut|ondataavailable|ondatasetchanged|ondatasetcomplete|ondeactivate|ondrag|ondragend|ondragenter|onmousewheel';
        $events .= '|ondragleave|ondragover|ondragstart|ondrop|onerrorupdate|onfilterchange|onfinish|onfocusin|onfocusout|onhashchange|onhelp|oninput|onlosecapture|onmessage|onmouseup|onmovestart';
        $events .= '|onoffline|ononline|onpaste|onpropertychange|onreadystatechange|onresizeend|onresizestart|onrowenter|onrowexit|onrowsdelete|onrowsinserted|onscroll|onsearch|onselectionchange';
        $events .= '|onselectstart|onstart|onstop|onanimationcancel|onanimationend|onanimationiteration|onanimationstart';

        if (preg_match('/<[\s]*script/ims', $html) || preg_match('/(' . $events . ')[\s]*=/ims', $html) || preg_match('/.*script\:/ims', $html)) {
            return false;
        }

        if (!$allow_iframe && preg_match('/<[\s]*(i?frame|form|input|embed|object)/ims', $html)) {
            return false;
        }

        return true;
    }

    /**
     * Check color validity.
     *
     * @param string $color Color to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isColor($color)
    {
        return preg_match('/^(#[0-9a-fA-F]{6}|[a-zA-Z0-9-]*)$/', $color);
    }

    /**
     * Check for Latitude/Longitude.
     *
     * @param string $data Coordinate to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isCoordinate($data)
    {
        return $data === null || preg_match('/^\-?[0-9]{1,8}\.[0-9]{1,8}$/s', $data);
    }

    /**
     * Check for date validity.
     *
     * @param string $date Date to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isDate($date)
    {
        if (!preg_match('/^([0-9]{4})-((?:0?[0-9])|(?:1[0-2]))-((?:0?[0-9])|(?:[1-2][0-9])|(?:3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date, $matches)) {
            return false;
        }

        return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }

    /**
     * Check for date format.
     *
     * @param string $date Date to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isDateFormat($date)
    {
        return (bool) preg_match('/^([0-9]{4})-((0?[0-9])|(1[0-2]))-((0?[0-9])|([1-2][0-9])|(3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date);
    }

    public static function isDateOrNull($date)
    {
        if (null === $date || $date === '0000-00-00 00:00:00' || $date === '0000-00-00') {
            return true;
        }

        return self::isDate($date);
    }

    /**
     * @param string $dni to validate
     *
     * @return bool
     */
    public static function isDniLite($dni)
    {
        return empty($dni) || (bool) preg_match('/^[0-9A-Za-z-.]{1,16}$/U', $dni);
    }

    /**
     * Check for barcode validity (EAN-13).
     *
     * @param string $ean13 Barcode to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isEan13($ean13)
    {
        return !$ean13 || preg_match('/^[0-9]{0,13}$/', $ean13);
    }

	/**
	 * Comprueba si el string tiene formato valido email
	 *
	 * @param string $mail 
	 * @return bool
	 */
	public static function isEmail($mail)
	{
		return (filter_var($mail, FILTER_VALIDATE_EMAIL) ? true : false);
	}

	/**
     * Check for a float number validity.
     *
     * @param float $float Float number to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isFloat($float)
    {
        return (string) ((float) $float) == (string) $float;
    }

    public static function isUnsignedFloat($float)
    {
        return (string) ((float) $float) == (string) $float && $float >= 0;
    }

    /**
     * Check for an integer validity.
     *
     * @param int $value Integer to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isInt($value)
    {
        return (string) (int) $value === (string) $value || $value === false;
    }

    /**
     * Check for ISBN.
     *
     * @param string $isbn validate
     *
     * @return bool Validity is ok or not
     */
    public static function isIsbn($isbn)
    {
        return !$isbn || preg_match('/^[0-9-]{0,32}$/', $isbn);
    }

    /**
     * Check for language code (ISO) validity.
     *
     * @param string $iso_code Language code (ISO) to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isLanguageIsoCode($iso_code)
    {
        return preg_match('/^[a-zA-Z]{2,3}$/', $iso_code);
    }

    public static function isLanguageCode($s)
    {
        return preg_match('/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/', $s);
    }

    /**
     * Check object validity.
     *
     * @param object $object Object to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isLoadedObject($object)
    {
        return is_object($object) && $object->id;
    }

	/**
     * Check for MD5 string validity.
     *
     * @param string $md5 MD5 string to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isMd5($md5)
    {
        return preg_match('/^[a-f0-9A-F]{32}$/', $md5);
    }

    /**
     * Check for MPN validity.
     *
     * @param string $mpn to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isMpn($mpn)
    {
        return Tools::strlen($mpn) <= 40;
    }

    /**
     * Check whether given name is valid
     *
     * @param string $name Name to validate
     *
     * @return bool
     */
    public static function isName($name)
    {
        return preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u', $name);
    }

    /**
     * Check for price validity (including negative price).
     *
     * @param string $price Price to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isNegativePrice($price)
    {
        return preg_match('/^[-]?[0-9]{1,10}(\.[0-9]{1,9})?$/', $price);
    }

    /**
     * Check for an percentage validity (between 0 and 100).
     *
     * @param float $value Float to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPercentage($value)
    {
        return Validate::isFloat($value) && $value >= 0 && $value <= 100;
    }

    /**
     * Check for phone number validity.
     *
     * @param string $number Phone number to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPhoneNumber($number)
    {
        return preg_match('/^[+0-9. ()\/-]*$/', $number);
    }

    /**
     * Check for postal code validity.
     *
     * @param string $postcode Postal code to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPostCode($postcode)
    {
        return empty($postcode) || preg_match('/^[a-zA-Z 0-9-]+$/', $postcode);
    }

    /**
     * Check for price validity.
     *
     * @param string $price Price to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPrice($price)
    {
        return preg_match('/^[0-9]{1,10}(\.[0-9]{1,9})?$/', $price);
    }

    /**
     * Check for SHA1 string validity.
     *
     * @param string $sha1 SHA1 string to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isSha1($sha1)
    {
        return preg_match('/^[a-fA-F0-9]{40}$/', $sha1);
    }

    /**
     * Check if $data is a string.
     *
     * @param string $data Data to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isString($data)
    {
        return is_string($data);
    }

	/**
	 * Check for table or identifier validity
	 * Mostly used in database for table names and id_table.
	 *
	 * @param string $table Table/identifier to validate
	 *
	 * @return bool Validity is ok or not
	 */
	public static function isTableOrIdentifier($table)
	{
		return preg_match('/^[a-zA-Z0-9_-]+$/', $table);
	}

	/**
	 * Check for an integer validity (unsigned)
	 * Mostly used in database for auto-increment.
	 *
	 * @param int $id Integer to validate
	 *
	 * @return bool Validity is ok or not
	 */
	public static function isUnsignedId($id)
	{
		return Validate::isUnsignedInt($id); /* Because an id could be equal to zero when there is no association */
	}

	public static function isNullOrUnsignedId($id)
    {
        return $id === null || Validate::isUnsignedId($id);
    }

	/**
     * Check for an integer validity (unsigned).
     *
     * @param int $value Integer to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isUnsignedInt($value)
    {
        return (is_numeric($value) || is_string($value)) && (string) (int) $value === (string) $value && $value < 4294967296 && $value >= 0;
    }

	/**
     * Check for barcode validity (UPC).
     *
     * @param string $upc Barcode to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isUpc($upc)
    {
        return !$upc || preg_match('/^[0-9]{0,12}$/', $upc);
    }
}
