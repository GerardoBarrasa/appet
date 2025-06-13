<?php

class Cuidador {

    // Propiedades de la clase
    private $id;
    private $nombre;
    private $email;
    private $slug;
    private $telefono;
    private $direccion;
    private $ciudad;
    private $codigo_postal;
    private $provincia;
    private $pais;
    private $descripcion;
    private $estado;
    private $fecha_creacion;
    private $fecha_actualizacion;

    // Instancia de la base de datos
    private $db;

    public function __construct() {
        $this->db = Bd::getInstance();
        $this->estado = 1; // Valor por defecto
        $this->telefono = '';
        $this->direccion = '';
        $this->ciudad = '';
        $this->codigo_postal = '';
        $this->provincia = '';
        $this->pais = '';
        $this->descripcion = '';
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getEmail() { return $this->email; }
    public function getSlug() { return $this->slug; }
    public function getTelefono() { return $this->telefono; }
    public function getDireccion() { return $this->direccion; }
    public function getCiudad() { return $this->ciudad; }
    public function getCodigoPostal() { return $this->codigo_postal; }
    public function getProvincia() { return $this->provincia; }
    public function getPais() { return $this->pais; }
    public function getDescripcion() { return $this->descripcion; }
    public function getEstado() { return $this->estado; }
    public function getFechaCreacion() { return $this->fecha_creacion; }
    public function getFechaActualizacion() { return $this->fecha_actualizacion; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) {
        $this->nombre = $nombre;
        // Auto-generar slug si no existe
        if (empty($this->slug)) {
            $this->slug = $this->generateSlug($nombre);
        }
    }
    public function setEmail($email) { $this->email = $email; }
    public function setSlug($slug) { $this->slug = $this->generateSlug($slug); }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }
    public function setCiudad($ciudad) { $this->ciudad = $ciudad; }
    public function setCodigoPostal($codigo_postal) { $this->codigo_postal = $codigo_postal; }
    public function setProvincia($provincia) { $this->provincia = $provincia; }
    public function setPais($pais) { $this->pais = $pais; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setFechaCreacion($fecha_creacion) { $this->fecha_creacion = $fecha_creacion; }
    public function setFechaActualizacion($fecha_actualizacion) { $this->fecha_actualizacion = $fecha_actualizacion; }

    // Método para generar slug automáticamente
    private function generateSlug($text) {
        return Tools::urlAmigable($text);
    }

    // Método para cargar un cuidador por ID
    public function loadById($id) {
        $id = (int)$id;
        $row = $this->db->fetchRowSafe("SELECT * FROM cuidadores WHERE id = ?", [$id], PDO::FETCH_ASSOC);

        if ($row) {
            $this->hydrate($row);
            return true;
        }
        return false;
    }

    // Método para cargar un cuidador por slug
    public function loadBySlug($slug) {
        $row = $this->db->fetchRowSafe("SELECT * FROM cuidadores WHERE slug = ?", [$slug], PDO::FETCH_ASSOC);

        if ($row) {
            $this->hydrate($row);
            return true;
        }
        return false;
    }

    // Método para cargar un cuidador por email
    public function loadByEmail($email) {
        $row = $this->db->fetchRowSafe("SELECT * FROM cuidadores WHERE email = ?", [$email], PDO::FETCH_ASSOC);

        if ($row) {
            $this->hydrate($row);
            return true;
        }
        return false;
    }

    // Método para hidratar el objeto con datos de la BD
    private function hydrate($data) {
        $this->id = $data['id'];
        $this->nombre = $data['nombre'];
        $this->email = $data['email'];
        $this->slug = $data['slug'];
        $this->telefono = $data['telefono'];
        $this->direccion = $data['direccion'];
        $this->ciudad = $data['ciudad'];
        $this->codigo_postal = $data['codigo_postal'];
        $this->provincia = $data['provincia'];
        $this->pais = $data['pais'];
        $this->descripcion = $data['descripcion'];
        $this->estado = $data['estado'];
        $this->fecha_creacion = $data['fecha_creacion'];
        $this->fecha_actualizacion = $data['fecha_actualizacion'];
    }

    // Método para guardar (crear o actualizar)
    public function save() {
        if ($this->id) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    // Método para crear nuevo cuidador
    private function create() {
        // Validar datos antes de crear
        $errors = $this->validate();
        if (!empty($errors)) {
            return false;
        }

        $data = [
            'nombre' => $this->nombre,
            'email' => $this->email,
            'slug' => $this->slug,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'codigo_postal' => $this->codigo_postal,
            'provincia' => $this->provincia,
            'pais' => $this->pais,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];

        $result = $this->db->insertSafe('cuidadores', $data);

        if ($result) {
            $this->id = $this->db->lastId();
            $this->fecha_creacion = $data['fecha_creacion'];
            $this->fecha_actualizacion = $data['fecha_actualizacion'];
            return true;
        }
        return false;
    }

    // Método para actualizar cuidador existente
    private function update() {
        // Validar datos antes de actualizar
        $errors = $this->validate();
        if (!empty($errors)) {
            return false;
        }

        $data = [
            'nombre' => $this->nombre,
            'email' => $this->email,
            'slug' => $this->slug,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'codigo_postal' => $this->codigo_postal,
            'provincia' => $this->provincia,
            'pais' => $this->pais,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];

        $result = $this->db->updateSafe('cuidadores', $data, 'id = ?', [$this->id]);

        if ($result) {
            $this->fecha_actualizacion = $data['fecha_actualizacion'];
            return true;
        }
        return false;
    }

    // Método para eliminar cuidador
    public function delete() {
        if (!$this->id) return false;

        // Verificar que no tenga mascotas asociadas
        $mascotasAsociadas = $this->db->fetchValueSafe(
            "SELECT COUNT(*) FROM mascotas WHERE id_cuidador = ?",
            [$this->id]
        );

        if ($mascotasAsociadas > 0) {
            return false; // No se puede eliminar si tiene mascotas
        }

        return $this->db->deleteSafe('cuidadores', 'id = ?', [$this->id]);
    }

    // Método para desactivar cuidador (soft delete)
    public function desactivar() {
        if (!$this->id) return false;

        $this->estado = 0;
        return $this->update();
    }

    // Método para activar cuidador
    public function activar() {
        if (!$this->id) return false;

        $this->estado = 1;
        return $this->update();
    }

    // Método para obtener las mascotas del cuidador
    public function getMascotas($solo_activas = true) {
        if (!$this->id) return [];

        $sql = "SELECT m.*, mt.nombre as tipo_nombre, mg.nombre as genero_nombre 
                FROM mascotas m 
                LEFT JOIN mascotas_tipo mt ON m.tipo = mt.id 
                LEFT JOIN mascotas_genero mg ON m.genero = mg.id 
                WHERE m.id_cuidador = ?";

        $params = [$this->id];

        if ($solo_activas) {
            $sql .= " AND m.estado = ?";
            $params[] = 1;
        }

        $sql .= " ORDER BY m.nombre";

        return $this->db->fetchAllSafe($sql, $params);
    }

    // Método para obtener estadísticas del cuidador
    public function getEstadisticas() {
        if (!$this->id) return [];

        $stats = [];

        // Total de mascotas
        $stats['total_mascotas'] = $this->db->fetchValueSafe(
            "SELECT COUNT(*) FROM mascotas WHERE id_cuidador = ?",
            [$this->id]
        );

        // Mascotas activas
        $stats['mascotas_activas'] = $this->db->fetchValueSafe(
            "SELECT COUNT(*) FROM mascotas WHERE id_cuidador = ? AND estado = 1",
            [$this->id]
        );

        // Mascotas por tipo
        $stats['mascotas_por_tipo'] = $this->db->fetchAllSafe(
            "SELECT mt.nombre, COUNT(*) as cantidad 
             FROM mascotas m 
             INNER JOIN mascotas_tipo mt ON m.tipo = mt.id 
             WHERE m.id_cuidador = ? 
             GROUP BY m.tipo",
            [$this->id]
        );

        // Última actividad
        $ultimaMascota = $this->db->fetchRowSafe(
            "SELECT fecha_creacion FROM mascotas WHERE id_cuidador = ? ORDER BY fecha_creacion DESC LIMIT 1",
            [$this->id]
        );

        $stats['ultima_actividad'] = $ultimaMascota ? $ultimaMascota->fecha_creacion : null;

        return $stats;
    }

    // Método para validar los datos antes de guardar
    public function validate() {
        $errors = [];

        if (empty($this->nombre)) {
            $errors[] = "El nombre es obligatorio";
        }

        if (empty($this->email)) {
            $errors[] = "El email es obligatorio";
        } elseif (!Tools::isEmail($this->email)) {
            $errors[] = "El email no tiene un formato válido";
        }

        // Validar que el email sea único
        if (!empty($this->email)) {
            $sql = "SELECT COUNT(*) as count FROM cuidadores WHERE email = ?";
            $params = [$this->email];

            if ($this->id) {
                $sql .= " AND id != ?";
                $params[] = $this->id;
            }

            $count = $this->db->fetchValueSafe($sql, $params);
            if ($count > 0) {
                $errors[] = "El email ya está en uso";
            }
        }

        // Validar que el slug sea único
        if (!empty($this->slug)) {
            $sql = "SELECT COUNT(*) as count FROM cuidadores WHERE slug = ?";
            $params = [$this->slug];

            if ($this->id) {
                $sql .= " AND id != ?";
                $params[] = $this->id;
            }

            $count = $this->db->fetchValueSafe($sql, $params);
            if ($count > 0) {
                $errors[] = "El slug ya existe";
            }
        }

        return $errors;
    }

    // Método para convertir el objeto a array
    public function toArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'slug' => $this->slug,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'codigo_postal' => $this->codigo_postal,
            'provincia' => $this->provincia,
            'pais' => $this->pais,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'fecha_creacion' => $this->fecha_creacion,
            'fecha_actualizacion' => $this->fecha_actualizacion
        ];
    }

    // ==========================================
    // MÉTODOS ESTÁTICOS (COMPATIBILIDAD)
    // ==========================================

    /**
     * Obtiene cuidadores con filtros aplicados
     *
     * @param int $comienzo Inicio de la paginación
     * @param int $limite Límite de resultados
     * @param bool $applyLimit Aplicar límite o no
     * @return array
     */
    public static function getCuidadorWithFiltros($comienzo, $limite, $applyLimit = true)
    {
        $db = Bd::getInstance();
        $busqueda = Tools::getValue('busqueda', '');
        $estado = Tools::getValue('estado', '');
        $params = [];
        $whereConditions = ["1"];

        if ($busqueda != '') {
            $whereConditions[] = "(nombre LIKE ? OR email LIKE ? OR slug LIKE ? OR telefono LIKE ?)";
            $params[] = "%{$busqueda}%";
            $params[] = "%{$busqueda}%";
            $params[] = "%{$busqueda}%";
            $params[] = "%{$busqueda}%";
        }

        if ($estado !== '') {
            $whereConditions[] = "estado = ?";
            $params[] = (int)$estado;
        }

        $whereClause = implode(' AND ', $whereConditions);
        $sql = "SELECT * FROM cuidadores WHERE {$whereClause} ORDER BY nombre";

        // Contar total de registros
        $total = $db->fetchValueSafe("SELECT COUNT(*) FROM cuidadores WHERE {$whereClause}", $params);

        // Aplicar límite si es necesario
        if ($applyLimit) {
            $sql .= " LIMIT ?, ?";
            $params[] = (int)$comienzo;
            $params[] = (int)$limite;
        }

        $listado = $db->fetchAllSafe($sql, $params);

        return [
            'listado' => $listado,
            'total' => $total
        ];
    }

    /**
     * Obtiene un cuidador por su ID
     *
     * @param int $id_cuidador ID del cuidador
     * @return object|false
     */
    public static function getCuidadorById($id_cuidador)
    {
        $db = Bd::getInstance();
        $id_cuidador = (int)$id_cuidador;

        if (!$id_cuidador) {
            return false;
        }

        return $db->fetchRowSafe(
            "SELECT * FROM cuidadores WHERE id = ?",
            [$id_cuidador]
        );
    }

    /**
     * Obtiene un cuidador por su slug
     *
     * @param string $slug Slug del cuidador
     * @return object|false
     */
    public static function getCuidadorBySlug($slug)
    {
        $db = Bd::getInstance();

        if (empty($slug)) {
            return false;
        }

        return $db->fetchRowSafe(
            "SELECT * FROM cuidadores WHERE slug = ?",
            [$slug]
        );
    }

    /**
     * Obtiene un cuidador por su email
     *
     * @param string $email Email del cuidador
     * @return object|false
     */
    public static function getCuidadorByEmail($email)
    {
        $db = Bd::getInstance();

        if (empty($email)) {
            return false;
        }

        return $db->fetchRowSafe(
            "SELECT * FROM cuidadores WHERE email = ?",
            [$email]
        );
    }

    /**
     * Actualiza los datos de un cuidador
     *
     * @param int $id_cuidador ID del cuidador
     * @param array $datos Datos a actualizar
     * @return bool
     */
    public static function actualizarCuidador($id_cuidador = null, $datos = null)
    {
        // Si no se proporcionan parámetros, tomarlos del POST
        if ($id_cuidador === null) {
            $id_cuidador = (int)Tools::getValue('id_cuidador');
        }

        $cuidador = new self();
        if (!$cuidador->loadById($id_cuidador)) {
            return false;
        }

        if ($datos === null) {
            $datos = [
                'nombre' => Tools::getValue('nombre'),
                'email' => Tools::getValue('email'),
                'telefono' => Tools::getValue('telefono'),
                'direccion' => Tools::getValue('direccion'),
                'ciudad' => Tools::getValue('ciudad'),
                'codigo_postal' => Tools::getValue('codigo_postal'),
                'provincia' => Tools::getValue('provincia'),
                'pais' => Tools::getValue('pais'),
                'descripcion' => Tools::getValue('descripcion'),
                'estado' => (int)Tools::getValue('estado', 1)
            ];

            $slug = Tools::getValue('slug');
            if (!empty($slug)) {
                $datos['slug'] = $slug;
            }
        }

        // Actualizar propiedades
        foreach ($datos as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($cuidador, $setter)) {
                $cuidador->$setter($value);
            }
        }

        return $cuidador->save();
    }

    /**
     * Crea un nuevo cuidador
     *
     * @param array $datos Datos del cuidador
     * @return int|false ID del cuidador creado o false en caso de error
     */
    public static function crearCuidador($datos = null)
    {
        $cuidador = new self();

        if ($datos === null) {
            $datos = [
                'nombre' => Tools::getValue('nombre'),
                'email' => Tools::getValue('email'),
                'telefono' => Tools::getValue('telefono'),
                'direccion' => Tools::getValue('direccion'),
                'ciudad' => Tools::getValue('ciudad'),
                'codigo_postal' => Tools::getValue('codigo_postal'),
                'provincia' => Tools::getValue('provincia'),
                'pais' => Tools::getValue('pais'),
                'descripcion' => Tools::getValue('descripcion'),
                'estado' => (int)Tools::getValue('estado', 1)
            ];

            $slug = Tools::getValue('slug');
            if (!empty($slug)) {
                $datos['slug'] = $slug;
            }
        }

        // Establecer propiedades
        foreach ($datos as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($cuidador, $setter)) {
                $cuidador->$setter($value);
            }
        }

        if ($cuidador->save()) {
            return $cuidador->getId();
        }

        return false;
    }

    /**
     * Elimina un cuidador
     *
     * @param int $id ID del cuidador
     * @return bool
     */
    public static function eliminarRegistro($id)
    {
        $cuidador = new self();
        if ($cuidador->loadById($id)) {
            return $cuidador->delete();
        }
        return false;
    }

    /**
     * Obtiene las mascotas de un cuidador
     *
     * @param int $id_cuidador ID del cuidador
     * @param bool $solo_activas Solo mascotas activas
     * @return array
     */
    public static function getMascotasByCuidador($id_cuidador, $solo_activas = true)
    {
        $cuidador = new self();
        if ($cuidador->loadById($id_cuidador)) {
            return $cuidador->getMascotas($solo_activas);
        }
        return [];
    }

    /**
     * Obtiene estadísticas de un cuidador
     *
     * @param int $id_cuidador ID del cuidador
     * @return array
     */
    public static function getEstadisticasCuidador($id_cuidador)
    {
        $cuidador = new self();
        if ($cuidador->loadById($id_cuidador)) {
            return $cuidador->getEstadisticas();
        }
        return [];
    }

    /**
     * Obtiene todos los cuidadores activos
     *
     * @return array
     */
    public static function getCuidadoresActivos()
    {
        $db = Bd::getInstance();

        return $db->fetchAllSafe(
            "SELECT * FROM cuidadores WHERE estado = 1 ORDER BY nombre",
            []
        );
    }

    /**
     * Busca cuidadores por nombre o email
     *
     * @param string $termino Término de búsqueda
     * @param int $limite Límite de resultados
     * @return array
     */
    public static function buscarCuidadores($termino, $limite = 10)
    {
        $db = Bd::getInstance();

        if (empty($termino)) {
            return [];
        }

        $sql = "SELECT * FROM cuidadores 
                WHERE (nombre LIKE ? OR email LIKE ? OR slug LIKE ?) 
                AND estado = 1 
                ORDER BY nombre 
                LIMIT ?";

        return $db->fetchAllSafe($sql, [
            "%{$termino}%",
            "%{$termino}%",
            "%{$termino}%",
            (int)$limite
        ]);
    }

    /**
     * Verifica si un slug está disponible
     *
     * @param string $slug Slug a verificar
     * @param int $id_cuidador ID del cuidador (para excluirlo en actualizaciones)
     * @return bool
     */
    public static function slugDisponible($slug, $id_cuidador = 0)
    {
        $db = Bd::getInstance();

        if (empty($slug)) {
            return false;
        }

        $params = [$slug];
        $sql = "SELECT COUNT(*) FROM cuidadores WHERE slug = ?";

        if ($id_cuidador > 0) {
            $sql .= " AND id != ?";
            $params[] = (int)$id_cuidador;
        }

        return (int)$db->fetchValueSafe($sql, $params) === 0;
    }

    /**
     * Verifica si un email está disponible
     *
     * @param string $email Email a verificar
     * @param int $id_cuidador ID del cuidador (para excluirlo en actualizaciones)
     * @return bool
     */
    public static function emailDisponible($email, $id_cuidador = 0)
    {
        $db = Bd::getInstance();

        if (empty($email)) {
            return false;
        }

        $params = [$email];
        $sql = "SELECT COUNT(*) FROM cuidadores WHERE email = ?";

        if ($id_cuidador > 0) {
            $sql .= " AND id != ?";
            $params[] = (int)$id_cuidador;
        }

        return (int)$db->fetchValueSafe($sql, $params) === 0;
    }

    /**
     * Genera un slug único basado en el nombre
     *
     * @param string $nombre Nombre del cuidador
     * @param int $id_cuidador ID del cuidador (para excluirlo)
     * @return string
     */
    public static function generarSlugUnico($nombre, $id_cuidador = 0)
    {
        $slug_base = Tools::urlAmigable($nombre);
        $slug = $slug_base;
        $contador = 1;

        while (!self::slugDisponible($slug, $id_cuidador)) {
            $slug = $slug_base . '-' . $contador;
            $contador++;
        }

        return $slug;
    }

    /**
     * Obtiene el número total de cuidadores
     *
     * @param bool $solo_activos Solo cuidadores activos
     * @return int
     */
    public static function getTotalCuidadores($solo_activos = false)
    {
        $db = Bd::getInstance();

        $sql = "SELECT COUNT(*) FROM cuidadores";
        $params = [];

        if ($solo_activos) {
            $sql .= " WHERE estado = ?";
            $params[] = 1;
        }

        return (int)$db->fetchValueSafe($sql, $params);
    }
}
