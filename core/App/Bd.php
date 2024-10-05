<?php

class Bd
{
	public static $instance;
	public static $_servers = [];
	protected $link;

	protected $server;
	protected $user;
	protected $password;
	protected $database;

	public static function getInstance()
	{
		if( !self::$_servers ) 
			self::$_servers = ['server' => bd_host, 'user' => bd_user, 'password' => bd_pass, 'database' => bd_name];

		if( empty(self::$instance) )
		{
			self::$instance = new Bd(
				self::$_servers['server'],
				self::$_servers['user'],
				self::$_servers['password'],
				self::$_servers['database']
			);
		}

		return self::$instance;
	}

	public function __construct($server, $user, $password, $database, $connect = true)
	{
		$this->server = $server;
		$this->user = $user;
		$this->password = $password;
		$this->database = $database;

		if( $connect )
			$this->connect();
	}

	public function connect()
	{
		$this->link = new mysqli($this->server, $this->user, $this->password, $this->database) or Debug::mlog(time(),'','Error al conectar con base de datos');
		if( $this->link->connect_error )
			die('Error al conectar con base de datos. '.$this->link->connect_error);
		return $this->link;
	}

	public function disconnect()
	{
		mysqli_close($this->link);
	}

	public function __destruct()
	{
		if( !empty($this->link) )
			$this->disconnect();
	}

	public function query($sql)
	{
		if( $this->link != '' )
		{
			$l = $this->link;
			$q = $l->query($sql) or mysqli_error($l);

			if( $q )
				Debug::mlog(time(),$sql,'Ejecutada correctamente');	
			else
				Debug::mlog(time(),$sql,mysqli_error($l));	

			return $q;
		}
		else
			return false;
	}

	public function execute($sql)
	{
		$q = $this->query($sql);

		return (bool) $q;
	}

	public function getResponse($sql)
	{
		if ( $this->link != '' )
		{
			$l = $this->link;
			$q = $l->query($sql) or mysqli_error($l);
			if( $q )
				return 'Ejecutada correctamente';
			else
				return mysqli_error($l);	
		}
		else
			return false;
	}

	/* Devuelve array con datos encontrados, o bien el dato en concreto, si lo conocemos */
	public function fetchArray($sql,$query='')
	{
		$q = $this->query($sql);
		$r = $q->fetch_all(MYSQLI_ASSOC);
		if( $query != '' )
			return $r[$query];
		else
			return $r;
	}

	/* Devuelve un listado con objetos */
	public function fetchObject($sql)
	{
		$q = $this->query($sql);
		$cant = $q->num_rows;
		$lista = array();
		
		for ( $i=0; $i<$cant; $i++ )
			$lista[$i] = $q->fetch_object();
		
		return $lista;
	}

	/* Devuelve un listado con objetos */
	public function fetchObjectWithKey($sql, $key, $second_key='', $arrayFirstKey=false)
	{
		$q = $this->query($sql);
		$cant = $q->num_rows;
		$lista = array();
		if( $second_key == '' )
		{
			for ( $i=0; $i<$cant; $i++ )
			{
				$d = $q->fetch_object();
				if( $arrayFirstKey )
					$lista[$d->$key][] = $d;
				else
					$lista[$d->$key] = $d;
			}
		}
		else
		{
			for ( $i=0; $i<$cant; $i++ )
			{
				$d = $q->fetch_object();
				$lista[$d->$key."-".$d->$second_key][] = $d;
			}
		}
		return $lista;
	}

	public function fetchRow($sql, $type="object")
	{
		$result = $this->query($sql);

		if( !empty($result) && $result->num_rows == '1' )
		{
			if( $type == "object" )
				return $result->fetch_object();
			elseif( $type == "array" )
				return $result->fetch_array(MYSQLI_ASSOC);
		}

		return false;
	}

	public function fetchValue($sql)
	{
		if( !$result = $this->fetchRow($sql, 'array') )
			return false;
		return array_shift($result);
	}

	/* Cuenta filas */
	public function countRows($sql)
	{
		$q = $this->query($sql);
		return $q->num_rows;
	}
	
	/* Última id insertada*/
	public function lastId()
	{
		return mysqli_insert_id($this->link);
	}

	/* Inserta */
	public function insert($table,$array)
	{
		$names = '';
		$values = '';
		foreach( $array as $key => $val )
		{
			$names .= $key.',';
			if( $val === null )
				$val = 'NULL,';
			elseif( $val == 'SYSDATE()' )
				$val = $val.',';
			else
				$val = '"'.$val.'",';
			$values .= $val;
		}
		$names = substr($names,0,strlen($names)-1);
		$values = substr($values,0,strlen($values)-1);
		$sql = 'INSERT INTO '.$table.' ('.$names.') VALUES ('.$values.')';

		return $this->query($sql);
	}

	/* Actualiza */
	public function update($table,$array,$where)
	{
		$names = '';
		foreach( $array as $key => $val )
		{
			if( !empty($val) || (isset($val) && ($val === 0 || $val === '')) )
				$value = ( $val === 'SYSDATE()' ) ? 'SYSDATE(), ' : '"'.$val.'", ';
			else 
				$value = 'NULL, ';
			$names .= $key.'='.$value;	
		}
		$names = substr($names,0,strlen($names)-2);
		$sql = 'UPDATE '.$table.' SET '.$names.' WHERE '.$where;
		return (bool) $this->query($sql);
	}

	public function _escape($string)
	{
		return $this->link->real_escape_string($string);
	}

	public function escape($string, $html_ok = false, $bq_sql = false)
	{
		if( !is_numeric($string) )
		{
			$string = $this->_escape($string);
			if( !$html_ok )
				$string = strip_tags(Tools::nl2br($string));
			if( $bq_sql === true )
				$string = str_replace('`', '\`', $string);
		}
		return $string;
	}

	public function delete($table, $where = '', $limit = 0)
	{
		$sql = 'DELETE FROM `' . bqSQL($table) . '`' . ($where ? ' WHERE ' . $where : '') . ($limit ? ' LIMIT ' . (int) $limit : '');
		$res = $this->query($sql);
		return (bool) $res;
	}
}
