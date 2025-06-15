<?php

class Bd
{
    public static $instance;
    public static $_servers = [];
    public $link;
    private $pdo;

    protected $server;
    protected $user;
    protected $password;
    protected $database;
    private $inTransaction = false;

    public static function getInstance()
    {
        if (!self::$_servers) {
            self::$_servers = [
                'server' => bd_host,
                'user' => bd_user,
                'password' => bd_pass,
                'database' => bd_name
            ];
        }

        if (empty(self::$instance)) {
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

        if ($connect) {
            $this->connect();
        }
    }

    public function connect()
    {
        try {
            // Conexión PDO (preferida)
            $dsn = "mysql:host={$this->server};dbname={$this->database};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            $this->pdo = new PDO($dsn, $this->user, $this->password, $options);

            // Mantener compatibilidad con MySQLi para código legacy
            $this->link = new mysqli($this->server, $this->user, $this->password, $this->database);

            if ($this->link->connect_error) {
                throw new Exception('MySQLi connection failed: ' . $this->link->connect_error);
            }

            $this->link->set_charset("utf8mb4");

        } catch (PDOException $e) {
            $this->logSqlError('PDO Connection Error', '', $e->getMessage());
            die('Error al conectar con base de datos PDO: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->logSqlError('MySQLi Connection Error', '', $e->getMessage());
            die('Error al conectar con base de datos MySQLi: ' . $e->getMessage());
        }

        return $this->pdo;
    }

    public function disconnect()
    {
        if ($this->pdo) {
            $this->pdo = null;
        }
        if ($this->link) {
            mysqli_close($this->link);
        }
    }

    public function __destruct()
    {
        if (!empty($this->link) || !empty($this->pdo)) {
            $this->disconnect();
        }
    }

    /**
     * Registra errores SQL en el log específico
     */
    private function logSqlError($operation, $sql, $error, $params = [])
    {
        $logData = [
            'operation' => $operation,
            'sql' => $sql,
            'error' => $error,
            'params' => $params,
            'server' => $this->server,
            'database' => $this->database
        ];

        __log_error($logData, 99);

        // También registrar en debug de BD para compatibilidad
        if (_DEBUG_ && isset($_SESSION)) {
            $_SESSION['debug']['bd'][] = [time(), $sql, $error];
        }
    }

    // ==========================================
    // MÉTODOS SEGUROS CON PDO (RECOMENDADOS)
    // ==========================================

    /**
     * Ejecuta una consulta preparada de forma segura
     */
    public function prepare($sql, $params = [])
    {
        $startTime = microtime(true);

        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);

            if (_DEBUG_) {
                performance_log('PDO Prepare/Execute', $startTime, [
                    'sql' => $sql,
                    'params' => $params,
                    'rows_affected' => $stmt->rowCount()
                ]);
            }

            return $stmt;
        } catch (PDOException $e) {
            $this->logSqlError('PDO Prepare/Execute', $sql, $e->getMessage(), $params);
            throw new Exception('Query execution failed: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene una sola fila de forma segura
     */
    public function fetchRowSafe($sql, $params = [], $fetchMode = PDO::FETCH_OBJ)
    {
        try {
            $stmt = $this->prepare($sql, $params);
            $result = $stmt->fetch($fetchMode);
            return $result ?: false;
        } catch (Exception $e) {
            $this->logSqlError('Fetch Row Safe', $sql, $e->getMessage(), $params);
            return false;
        }
    }

    /**
     * Obtiene múltiples filas de forma segura
     */
    public function fetchAllSafe($sql, $params = [], $fetchMode = PDO::FETCH_OBJ)
    {
        try {
            $stmt = $this->prepare($sql, $params);
            return $stmt->fetchAll($fetchMode);
        } catch (Exception $e) {
            $this->logSqlError('Fetch All Safe', $sql, $e->getMessage(), $params);
            return [];
        }
    }

    /**
     * Obtiene un valor único de forma segura
     */
    public function fetchValueSafe($sql, $params = [])
    {
        try {
            $stmt = $this->prepare($sql, $params);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            $this->logSqlError('Fetch Value Safe', $sql, $e->getMessage(), $params);
            return false;
        }
    }

    /**
     * Cuenta filas de forma segura
     */
    public function countRowsSafe($sql, $params = [])
    {
        try {
            $stmt = $this->prepare($sql, $params);
            return $stmt->rowCount();
        } catch (Exception $e) {
            $this->logSqlError('Count Rows Safe', $sql, $e->getMessage(), $params);
            return 0;
        }
    }

    /**
     * Inserta datos de forma segura
     */
    public function insertSafe($table, $data)
    {
        $columns = array_keys($data);
        $placeholders = ':' . implode(', :', $columns);
        $columnsList = implode(', ', $columns);

        $sql = "INSERT INTO {$table} ({$columnsList}) VALUES ({$placeholders})";

        try {
            $stmt = $this->prepare($sql, $data);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            $this->logSqlError('Insert Safe', $sql, $e->getMessage(), $data);
            return false;
        }
    }

    /**
     * Actualiza datos de forma segura
     */
    public function updateSafe($table, $data, $where, $whereParams = [])
    {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = :{$column}";
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE {$where}";

        // Combinar parámetros de datos y WHERE usando nombres únicos para WHERE
        $allParams = $data;

        // Si whereParams es un array asociativo, añadirlo directamente
        if (is_array($whereParams) && !empty($whereParams)) {
            // Si es array indexado, convertir a nombrado
            if (isset($whereParams[0])) {
                // Reemplazar ? en WHERE con parámetros nombrados
                $whereParamNames = [];
                $whereIndex = 0;
                $newWhere = preg_replace_callback('/\?/', function($matches) use (&$whereParamNames, &$whereIndex) {
                    $paramName = 'where_param_' . $whereIndex;
                    $whereParamNames[] = $paramName;
                    $whereIndex++;
                    return ':' . $paramName;
                }, $where);

                // Actualizar la consulta SQL
                $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE {$newWhere}";

                // Añadir parámetros WHERE con nombres únicos
                foreach ($whereParams as $index => $value) {
                    if (isset($whereParamNames[$index])) {
                        $allParams[$whereParamNames[$index]] = $value;
                    }
                }
            } else {
                // Es array asociativo, añadir directamente
                $allParams = array_merge($allParams, $whereParams);
            }
        }

        try {
            $stmt = $this->prepare($sql, $allParams);
            return $stmt->rowCount();
        } catch (Exception $e) {
            $this->logSqlError('Update Safe', $sql, $e->getMessage(), $allParams);
            return false;
        }
    }

    /**
     * Elimina datos de forma segura
     */
    public function deleteSafe($table, $where, $whereParams = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";

        try {
            $stmt = $this->prepare($sql, $whereParams);
            return $stmt->rowCount();
        } catch (Exception $e) {
            $this->logSqlError('Delete Safe', $sql, $e->getMessage(), $whereParams);
            return false;
        }
    }

    // ==========================================
    // TRANSACCIONES
    // ==========================================

    /**
     * Inicia una transacción
     */
    public function beginTransaction()
    {
        if (!$this->inTransaction) {
            try {
                $this->pdo->beginTransaction();
                $this->inTransaction = true;

                if (_DEBUG_) {
                    debug_log('Transaction started', 'DB_TRANSACTION');
                }
            } catch (PDOException $e) {
                $this->logSqlError('Begin Transaction', '', $e->getMessage());
            }
        }
        return $this;
    }

    /**
     * Confirma una transacción
     */
    public function commit()
    {
        if ($this->inTransaction) {
            try {
                $this->pdo->commit();
                $this->inTransaction = false;

                if (_DEBUG_) {
                    debug_log('Transaction committed', 'DB_TRANSACTION');
                }
            } catch (PDOException $e) {
                $this->logSqlError('Commit Transaction', '', $e->getMessage());
            }
        }
        return $this;
    }

    /**
     * Revierte una transacción
     */
    public function rollback()
    {
        if ($this->inTransaction) {
            try {
                $this->pdo->rollback();
                $this->inTransaction = false;

                if (_DEBUG_) {
                    debug_log('Transaction rolled back', 'DB_TRANSACTION');
                }
            } catch (PDOException $e) {
                $this->logSqlError('Rollback Transaction', '', $e->getMessage());
            }
        }
        return $this;
    }

    /**
     * Ejecuta múltiples operaciones en una transacción
     */
    public function transaction(callable $callback)
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            $this->logSqlError('Transaction Callback', '', $e->getMessage());
            throw $e;
        }
    }

    // ==========================================
    // MÉTODOS LEGACY (COMPATIBILIDAD)
    // ==========================================

    /**
     * @deprecated Usar prepare() en su lugar
     */
    public function query($sql)
    {
        if ($this->link != '') {
            $l = $this->link;
            $q = $l->query($sql);

            if (!$q) {
                $error = mysqli_error($l);
                $this->logSqlError('Legacy Query', $sql, $error);
            } else {
                // Registrar consulta exitosa en debug
                if (_DEBUG_ && isset($_SESSION)) {
                    $_SESSION['debug']['bd'][] = [time(), $sql, 'Ejecutada correctamente'];
                }
            }

            return $q;
        } else {
            return false;
        }
    }

    /**
     * @deprecated Usar prepare() en su lugar
     */
    public function execute($sql)
    {
        $q = $this->query($sql);
        return (bool) $q;
    }

    /**
     * @deprecated Usar prepare() en su lugar
     */
    public function getResponse($sql)
    {
        if ($this->link != '') {
            $l = $this->link;
            $q = $l->query($sql);
            if ($q) {
                return 'Ejecutada correctamente';
            } else {
                $error = mysqli_error($l);
                $this->logSqlError('Get Response', $sql, $error);
                return $error;
            }
        } else {
            return false;
        }
    }

    /**
     * @deprecated Usar fetchAllSafe() en su lugar
     */
    public function fetchArray($sql, $query = '')
    {
        $q = $this->query($sql);
        if (!$q) return [];

        $r = $q->fetch_all(MYSQLI_ASSOC);
        if ($query != '') {
            return $r[$query] ?? null;
        } else {
            return $r;
        }
    }

    /**
     * @deprecated Usar fetchAllSafe() en su lugar
     */
    public function fetchObject($sql)
    {
        $q = $this->query($sql);
        if (!$q) return [];

        $cant = $q->num_rows;
        $lista = array();

        for ($i = 0; $i < $cant; $i++) {
            $lista[$i] = $q->fetch_object();
        }

        return $lista;
    }

    /**
     * @deprecated Usar fetchAllSafe() en su lugar
     */
    public function fetchObjectWithKey($sql, $key, $second_key = '', $arrayFirstKey = false)
    {
        $q = $this->query($sql);
        if (!$q) return [];

        $cant = $q->num_rows;
        $lista = array();

        if ($second_key == '') {
            for ($i = 0; $i < $cant; $i++) {
                $d = $q->fetch_object();
                if ($arrayFirstKey) {
                    $lista[$d->$key][] = $d;
                } else {
                    $lista[$d->$key] = $d;
                }
            }
        } else {
            for ($i = 0; $i < $cant; $i++) {
                $d = $q->fetch_object();
                $lista[$d->$key . "-" . $d->$second_key][] = $d;
            }
        }
        return $lista;
    }

    /**
     * @deprecated Usar fetchRowSafe() en su lugar
     */
    public function fetchRow($sql, $type = "object")
    {
        $result = $this->query($sql);

        if (!empty($result) && $result->num_rows == '1') {
            if ($type == "object") {
                return $result->fetch_object();
            } elseif ($type == "array") {
                return $result->fetch_array(MYSQLI_ASSOC);
            }
        }

        return false;
    }

    /**
     * @deprecated Usar fetchValueSafe() en su lugar
     */
    public function fetchValue($sql)
    {
        if (!$result = $this->fetchRow($sql, 'array')) {
            return false;
        }
        return array_shift($result);
    }

    /**
     * @deprecated Usar countRowsSafe() en su lugar
     */
    public function countRows($sql)
    {
        $q = $this->query($sql);
        return $q ? $q->num_rows : 0;
    }

    /**
     * Última ID insertada
     */
    public function lastId()
    {
        if ($this->pdo) {
            return $this->pdo->lastInsertId();
        }
        return mysqli_insert_id($this->link);
    }

    /**
     * @deprecated Usar insertSafe() en su lugar
     */
    public function insert($table, $array)
    {
        $names = '';
        $values = '';
        foreach ($array as $key => $val) {
            $names .= $key . ',';
            $values .= ($val == 'SYSDATE()') ? 'SYSDATE(),' : '"' . mysqli_real_escape_string($this->link, $val) . '",';
        }
        $names = substr($names, 0, strlen($names) - 1);
        $values = substr($values, 0, strlen($values) - 1);
        $sql = 'INSERT INTO ' . $table . ' (' . $names . ') VALUES (' . $values . ')';

        return $this->query($sql);
    }

    /**
     * @deprecated Usar updateSafe() en su lugar
     */
    public function update($table, $array, $where)
    {
        $names = '';
        foreach ($array as $key => $val) {
            if (!empty($val) || (isset($val) && $val == 0)) {
                $value = ($val == 'SYSDATE()') ? 'SYSDATE(), ' : '"' . mysqli_real_escape_string($this->link, $val) . '", ';
            } else {
                $value = 'NULL, ';
            }
            $names .= $key . '=' . $value;
        }
        $names = substr($names, 0, strlen($names) - 2);
        $sql = 'UPDATE ' . $table . ' SET ' . $names . ' WHERE ' . $where;
        return $this->query($sql);
    }

    // ==========================================
    // MÉTODOS DE UTILIDAD
    // ==========================================

    /**
     * Escapa una cadena para prevenir SQL injection (solo para métodos legacy)
     */
    public function escape($string)
    {
        if ($this->link) {
            return mysqli_real_escape_string($this->link, $string);
        }
        return addslashes($string);
    }

    /**
     * Obtiene información de la conexión
     */
    public function getConnectionInfo()
    {
        return [
            'server' => $this->server,
            'database' => $this->database,
            'user' => $this->user,
            'pdo_available' => !empty($this->pdo),
            'mysqli_available' => !empty($this->link),
            'in_transaction' => $this->inTransaction
        ];
    }

    /**
     * Verifica si la conexión está activa
     */
    public function isConnected()
    {
        if ($this->pdo) {
            try {
                $this->pdo->query('SELECT 1');
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        if ($this->link) {
            return mysqli_ping($this->link);
        }

        return false;
    }
}
