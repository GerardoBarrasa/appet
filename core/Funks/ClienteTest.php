<?php

class ClienteTest extends ObjectModel
{
	public $name;
	public $id_gender;
	public $birthday;
	public $newsletter;
	public $note;
	public $test_lang_field;

	public static $definition = [
		'table' => 'clientetest',
		'primary' => 'id_clientetest',
		'multilang' => true,
		'fields' => [
			'name' => ['type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 255],
			'id_gender' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
			'birthday' => ['type' => self::TYPE_DATE, 'validate' => 'isBirthDate'],
			'newsletter' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
			'note' => ['type' => self::TYPE_HTML, 'size' => 65000],
			'test_lang_field' => ['type' => self::TYPE_HTML, 'size' => 65000, 'lang' => true]
		],
	];
}
