<?php

abstract class ObjectModel
{
	const TYPE_INT = 1;
	const TYPE_BOOL = 2;
	const TYPE_STRING = 3;
	const TYPE_FLOAT = 4;
	const TYPE_DATE = 5;
	const TYPE_HTML = 6;
	const TYPE_NOTHING = 7;

	const FORMAT_COMMON = 1;
	const FORMAT_LANG = 2;

	public $id;
	protected $id_lang = null;

	public static $definition = [];
	protected static $loaded_classes = [];
	protected $def;
	protected $update_fields = null;
	public $force_id = false;
	protected static $cache_objects = true;

	public function __construct($id = null, $id_lang = null)
	{
		$class_name = get_class($this);
		if( !isset(ObjectModel::$loaded_classes[$class_name]) )
		{
			$this->def = ObjectModel::getDefinition($class_name);
			if( !Validate::isTableOrIdentifier($this->def['primary']) || !Validate::isTableOrIdentifier($this->def['table']) )
				throw new CoreException('Formato de identificador o tabla no válido para la clase ' . $class_name);
			ObjectModel::$loaded_classes[$class_name] = get_object_vars($this);
		}
		else
		{
			foreach( ObjectModel::$loaded_classes[$class_name] as $key => $value )
				$this->{$key} = $value;
		}

		if( $id_lang !== null )
			$this->id_lang = (Idiomas::getLanguages($id_lang) !== false) ? $id_lang : Configuracion::get('default_language');

		if( $id )
			$this->getData($id, self::$cache_objects);
	}

	public static function getDefinition($class, $field = null)
	{
		if( is_object($class) )
			$class = get_class($class);

		if( $field === null )
			$cache_id = 'objectmodel_def_' . $class;

		if( $field !== null || !Cache::isStored($cache_id) )
		{
			$definition = $class::$definition;
			$definition['classname'] = $class;

			if( $field )
				return isset($definition['fields'][$field]) ? $definition['fields'][$field] : null;

			Cache::store($cache_id, $definition);

			return $definition;
		}

		return Cache::retrieve($cache_id);
	}

	public function getData($id, $should_cache_objects)
	{
		$cache_id = 'objectmodel_' . $this->def['classname'] . '_' . (int) $id . '_' . (int) $this->id_lang;
		if( !$should_cache_objects || !Cache::isStored($cache_id) )
		{
			$sql = "SELECT * FROM ".$this->def['table']." a ";
			if( $this->id_lang && isset($this->def['multilang']) && $this->def['multilang'] )
				$sql .= "LEFT JOIN ".$this->def['table']."_lang b ON a.`".bqSQL($this->def['primary'])."` = b.`".bqSQL($this->def['primary'])."` AND b.`id_lang` = ".(int)$this->id_lang;
			$sql .= " WHERE a.`".bqSQL($this->def['primary'])."` = ".(int)$id;

			if( $object_datas = Bd::getInstance()->fetchRow($sql, 'array') )
			{
				$objectVars = get_object_vars($this);
				if( !$this->id_lang && isset($this->def['multilang']) && $this->def['multilang'] )
				{
					$sql = 'SELECT * FROM `' . bqSQL($this->def['table']) . '_lang` WHERE `' . bqSQL($this->def['primary']) . '` = ' . (int) $id;

					if( $object_datas_lang = Bd::getInstance()->fetchArray($sql) )
					{
						foreach( $object_datas_lang as $row )
						{
							foreach( $row as $key => $value )
							{
								if( $key != $this->def['primary'] && array_key_exists($key, $objectVars) )
								{
									if( !isset($object_datas[$key]) || !is_array($object_datas[$key]) )
										$object_datas[$key] = [];

									$object_datas[$key][$row['id_lang']] = $value;
								}
							}
						}
					}
				}

				$this->id = (int) $id;
				foreach( $object_datas as $key => $value )
				{
					if( array_key_exists($key, $this->def['fields'])
						|| array_key_exists($key, $objectVars) )
					{
						if( isset($this->def['fields'][$key]['type']) && in_array($this->def['fields'][$key]['type'], [self::TYPE_BOOL]) )
						{
							if( is_array($value) )
							{
								array_walk($value, function (&$v) { $v = strval($v); });
								$this->{$key} = $value;
							}
							else
								$this->{$key} = strval($value);
						}
						else
							$this->{$key} = $value;
					}
					else
						unset($object_datas[$key]);
				}
				if( $should_cache_objects )
                    Cache::store($cache_id, $object_datas);
			}
		}
		else
		{
			$object_datas = Cache::retrieve($cache_id);
            if( $object_datas )
            {
                $this->id = (int) $id;
                foreach( $object_datas as $key => $value )
                    $this->{$key} = $value;
            }
		}
	}

	public function save($auto_date = true)
	{
		return (int) $this->id > 0 ? $this->update() : $this->add($auto_date);
	}

	public function update()
	{
		$this->clearCache();

		if( property_exists($this, 'date_modified') )
		{
			$this->date_modified = Tools::datetime();
			if( isset($this->update_fields) && is_array($this->update_fields) && count($this->update_fields) )
				$this->update_fields['date_modified'] = true;
		}

		if( property_exists($this, 'date_created') && $this->date_created == null )
		{
			$this->date_created = Tools::datetime();
			if( isset($this->update_fields) && is_array($this->update_fields) && count($this->update_fields) )
				$this->update_fields['date_created'] = true;
		}

		if( !$result = Bd::getInstance()->update($this->def['table'], $this->getFields(), '`' . pSQL($this->def['primary']) . '` = ' . (int) $this->id) )
			return false;

		//Campos multiidioma
		if( isset($this->def['multilang']) && $this->def['multilang'] )
		{
			$fields = $this->getFieldsLang();
			if( is_array($fields) )
			{
				foreach( $fields as $field )
				{
					foreach( array_keys($field) as $key )
					{
						if( !Validate::isTableOrIdentifier($key) )
							throw new CoreException('Clave '.$key.' no es una tabla o identificador válido.');
					}

					$where = pSQL($this->def['primary']) . ' = ' . (int) $this->id . ' AND id_lang = ' . (int) $field['id_lang'];
					if( Bd::getInstance()->fetchValue('SELECT COUNT(*) FROM ' . pSQL($this->def['table']) . '_lang WHERE ' . $where) )
						$result &= Bd::getInstance()->update($this->def['table'] . '_lang', $field, $where);
					else
						$result &= Bd::getInstance()->insert($this->def['table'] . '_lang', $field);
				}
			}
		}

		return $result;
	}

	public function getFields()
	{
		$this->validateFields();
		$fields = $this->formatFields(self::FORMAT_COMMON);

		if( !$fields && isset($this->id) && Validate::isUnsignedId($this->id) )
			$fields[$this->def['primary']] = $this->id;
		elseif( !$fields && isset($this->id) )
			$fields[$this->def['primary']] = NULL;

		return $fields;
	}

	public function validateFields($die = true, $error_return = false)
	{
		foreach( $this->def['fields'] as $field => $data )
		{
			if( !empty($data['lang']) )
				continue;

			$message = $this->validateField($field, $this->$field, null, [], $error_return);
			if( $message !== true )
			{
				if( $die )
					throw new CoreException($message);
				return $error_return ? $message : false;
			}
		}

		return true;
	}

	public function validateField($field, $value, $id_lang = null, $skip = [], $human_errors = false)
	{
		static $id_lang_default = null;

		if( $id_lang_default === null )
			$id_lang_default = Configuracion::get('default_language');

		$data = $this->def['fields'][$field];

		//Validación de vacío. La condición del idioma no la tengo muy clara
		if( !$id_lang || $id_lang == $id_lang_default )
		{
			if( !in_array('required', $skip) && !empty($data['required']) )
			{
				if( Tools::isEmpty($value) )
				{
					if( $human_errors )
						return l('objeto-campo-obligatorio-ko', array($this->displayFieldName($field, get_class($this))));
					else
						return 'La propiedad '.get_class($this) . '->' . $field.' está vacía.';
				}
			}
		}

		//Valores por defecto
		if( !$value && !empty($data['default']) )
		{
			$value = $data['default'];
			$this->$field = $value;
		}

		//Validación tipo field
		if( !in_array('values', $skip) && !empty($data['values']) && is_array($data['values']) && !in_array($value, $data['values']) )
			return 'La propiedad '.get_class($this) . '->' . $field.' tiene un valor incorrecto. Valores admitidos: '.implode(',', $data['values']);

		//Validación tamaño del campo
		if( !in_array('size', $skip) && !empty($data['size']) )
		{
			$size = $data['size'];
			if( !is_array($data['size']) )
				$size = ['min' => 0, 'max' => $data['size']];

			$length = Tools::strlen($value);
			if( $length < $size['min'] || $length > $size['max'] )
			{
				if( $human_errors )
				{
					if( isset($data['lang']) && $data['lang'] )
						return l('objeto-campo-lang-tamano-ko', array($this->displayFieldName($field, get_class($this)), Idiomas::getLanguages($id_lang)->nombre, $size['max']));
					else
						return l('objeto-campo-tamano-ko', array($this->displayFieldName($field, get_class($this)), Idiomas::getLanguages($id_lang)->nombre, $size['max']));
				}
				else
					return 'El tamaño de la propiedad '.get_class($this) . '->' . $field.' es de '.$length.' caracteres. Debe estar entre '.$size['min'].' y '.$size['max'];
			}
		}

		//Validación establecida en cada campo
		if( !in_array('validate', $skip) && !empty($data['validate']) )
		{
			if( !method_exists('Validate', $data['validate']) )
				throw new CoreException('Función de validación no encontrada: '.$data['validate']);

			if( !empty($value) )
			{
				$res = true;
				if( !call_user_func(['Validate', $data['validate']], $value) )
					$res = false;

				if( !$res )
				{
					if( $human_errors )
						return l('objeto-campo-invalido', array($this->displayFieldName($field, get_class($this))));
					else
						return 'La propiedad '.get_class($this) . '->' . $field.' no es válida';
				}
			}
		}

		return true;
	}

	public static function displayFieldName($field, $class = __CLASS__, $htmlentities = true, Context $context = null)
	{
		return l('objeto-'.strtolower($class).'-campo-'.strtolower($field));
	}

	protected function formatFields($type, $id_lang = null)
	{
		$fields = [];

		if( isset($this->id) )
			$fields[$this->def['primary']] = $this->id;

		foreach( $this->def['fields'] as $field => $data )
		{
			if( ($type == self::FORMAT_LANG && empty($data['lang'])) || ($type == self::FORMAT_COMMON && !empty($data['lang'])) )
				continue;

			if( is_array($this->update_fields) )
			{
				if( !empty($data['lang']) && (empty($this->update_fields[$field]) || ($type == self::FORMAT_LANG && empty($this->update_fields[$field][$id_lang]))) )
					continue;
			}

			$value = $this->$field;
			if( $type == self::FORMAT_LANG && $id_lang && is_array($value) )
			{
				if( !empty($value[$id_lang]) )
					$value = $value[$id_lang];
				elseif( !empty($data['required']) )
					$value = $value[Configuracion::get('default_language')];
				else
					$value = '';
			}

			$purify = isset($data['validate']) && strtolower($data['validate']) == 'iscleanhtml';

			$fields[$field] = ObjectModel::formatValue($value, $data['type'], false, $purify, !empty($data['allow_null']));
		}

		return $fields;
	}

	public static function formatValue($value, $type, $with_quotes = false, $purify = true, $allow_null = false)
	{
		if( $allow_null && $value === null )
			return NULL;

		switch ($type) {
			case self::TYPE_INT:
				return (int) $value;

			case self::TYPE_BOOL:
				return (int) $value;

			case self::TYPE_FLOAT:
				return (float) str_replace(',', '.', $value);

			case self::TYPE_DATE:
				if( !$value )
					$value = '0000-00-00';

				if( $with_quotes )
					return '\'' . pSQL($value) . '\'';

				return pSQL($value);

			case self::TYPE_HTML:
				if( $value === NULL )
					return $value;
				/*if( $purify )
					$value = Tools::purifyHTML($value);*/

				if( $with_quotes )
					return '\'' . pSQL($value, true) . '\'';

				return pSQL($value, true);

			case self::TYPE_NOTHING:
				return $value;

			case self::TYPE_STRING:
			default:
				if( $with_quotes )
					return '\'' . pSQL($value) . '\'';

				return pSQL($value);
		}
	}

	public function getFieldsLang()
	{
		$this->validateFieldsLang();

		$fields = [];
		if( !is_int($this->id_lang) || $this->id_lang <= 0 )
		{
			foreach( Idiomas::getIDs() as $id_lang )
			{
				$fields[$id_lang] = $this->formatFields(self::FORMAT_LANG, $id_lang);
				$fields[$id_lang]['id_lang'] = $id_lang;
			}
		}
		else
		{
			$fields = [$this->id_lang => $this->formatFields(self::FORMAT_LANG, $this->id_lang)];
			$fields[$this->id_lang]['id_lang'] = $this->id_lang;
		}

		return $fields;
	}

	public function validateFieldsLang($die = true, $errorReturn = false)
	{
		$defaultLang = (int) Configuracion::get('default_language');
		foreach( $this->def['fields'] as $field => $data )
		{
			if( empty($data['lang']) )
				continue;

			$values = $this->$field;

			if( !is_array($values) )
				$values = [$this->id_lang => $values];

			if( !isset($values[$defaultLang]) )
				$values[$defaultLang] = '';

			foreach( $values as $id_lang => $value )
			{
				if( is_array($this->update_fields) && empty($this->update_fields[$field][$id_lang]) )
					continue;

				$message = $this->validateField($field, $value, $id_lang, [], $errorReturn);
				if( $message !== true )
				{
					if( $die )
						throw new CoreException($message);
					return $errorReturn ? $message : false;
				}
			}
		}

		return true;
	}

	public function add($auto_date = true)
	{
		if( isset($this->id) && !$this->force_id )
			unset($this->id);

		if( $auto_date && property_exists($this, 'date_modified') )
			$this->date_modified = Tools::datetime();

		if( $auto_date && property_exists($this, 'date_created') )
			$this->date_created = Tools::datetime();

		if( !$result = Bd::getInstance()->insert($this->def['table'], $this->getFields()) )
			return false;

		$this->id = Bd::getInstance()->lastId();

		if( !$result )
			return false;

		if( !empty($this->def['multilang']) )
		{
			$fields = $this->getFieldsLang();
			if( $fields && is_array($fields) )
			{
				foreach( $fields as $field )
				{
					foreach( array_keys($field) as $key )
					{
						if( !Validate::isTableOrIdentifier($key) )
							throw new CoreException('Clave '.$key.' no es una tabla o identificador.');
					}
					$field[$this->def['primary']] = (int) $this->id;

					$result &= Bd::getInstance()->insert($this->def['table'] . '_lang', $field);
				}
			}
		}

		return $result;
	}

	public function delete()
	{
		$this->clearCache();

		$result = true;

		if( !empty($this->def['multilang']) )
			$result &= Bd::getInstance()->delete($this->def['table'] . '_lang', '`' . bqSQL($this->def['primary']) . '` = ' . (int) $this->id);

		if( $result )
			$result &= Bd::getInstance()->delete($this->def['table'], '`' . bqSQL($this->def['primary']) . '` = ' . (int) $this->id);

		if( !$result )
			return false;

		return $result;
	}

	/**
	 * Generar los shortcodes a partir de los campos de un objeto
	 * @param $placeholder bool - Si es true se crearán adicionalmente los campos con el sufijo "-placeholder"
	 * @param $skipPlaceholderFields array - Array de campos para ignorar la creación de la traducción con placeholder
	 * @param $skipPlaceholderFields array - Array de campos para ignorar la creación de la traducción
	 */
	public static function generarTraduccionesCampos($placeholder = true, $skipPlaceholderFields = array(), $skipFields = array())
	{
		$class_name = get_called_class();
		$fields = array_keys($class_name::$definition['fields']);
		vd('-- GENERACIÓN DE CAMPOS "'.$class_name.'" --');
		foreach( $fields as $field )
		{
			$result = 'Campo: '.$field.' -> ';
			$shortcode = 'objeto-'.strtolower($class_name).'-campo-'.$field;
			if( !in_array($field, $skipFields) && !Traducciones::checkShortcodeExists($shortcode) )
			{
				Traducciones::crearTraduccion($shortcode, Configuracion::get('default_language'), '', 'objeto '.strtolower($class_name));
				$result .= 'Creado correctamente: '.$shortcode;
			}
			else
			{
				$result .= 'Ya existe';
			}
			vd($result);

			if( $placeholder )
			{
				$result = 'Campo placeholder: '.$field.' -> ';
				$shortcode = 'objeto-'.strtolower($class_name).'-campo-'.$field.'-placeholder';
				if( !in_array($field, $skipPlaceholderFields) && !Traducciones::checkShortcodeExists($shortcode) )
				{
					Traducciones::crearTraduccion($shortcode, Configuracion::get('default_language'), '', 'objeto '.strtolower($class_name));
					$result .= 'Creado correctamente: '.$shortcode;
				}
				else
				{
					$result .= 'Ya existe';
				}
				vd($result);
			}
		}
	}

	public function clearCache($all = false)
	{
		if( $all )
			Cache::clean('objectmodel_' . $this->def['classname'] . '_*');
		elseif ($this->id)
			Cache::clean('objectmodel_' . $this->def['classname'] . '_' . (int) $this->id . '_*');
	}

	/**
	 * Enables object caching.
	 */
	public static function enableCache()
	{
		ObjectModel::$cache_objects = true;
	}

	/**
	 * Disables object caching.
	 */
	public static function disableCache()
	{
		ObjectModel::$cache_objects = false;
	}
}
