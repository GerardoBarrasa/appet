<?php

class Mascotas
{
    // Propiedades de la clase (para uso como objeto)
    private $id;
    private $id_cuidador;
    private $slug;
    private $nombre;
    private $alias;
    private $tipo;
    private $genero;
    private $raza;
    private $peso;
    private $edad;
    private $edad_fecha;
    private $nacimiento_fecha;
    private $esterilizado;
    private $ultimo_celo;
    private $notas_internas;
    private $observaciones;
    private $estado;
    private $fecha_creacion;
    private $fecha_actualizacion;

    // Instancia de la base de datos
    private $db;

    public function __construct() {
        $this->db = Bd::getInstance();
        $this->esterilizado = 0; // Valor por defecto
        $this->peso = 0;
        $this->edad = 0;
        $this->estado = 1;
        $this->notas_internas = '';
        $this->observaciones = '';
        $this->alias = '';
    }

    // ==========================================
    // MÉTODOS DE OBJETO (NUEVOS)
    // ==========================================

    // Getters
    public function getId() { return $this->id; }
    public function getIdCuidador() { return $this->id_cuidador; }
    public function getSlug() { return $this->slug; }
    public function getNombre() { return $this->nombre; }
    public function getAlias() { return $this->alias; }
    public function getTipo() { return $this->tipo; }
    public function getGenero() { return $this->genero; }
    public function getRaza() { return $this->raza; }
    public function getPeso() { return $this->peso; }
    public function getEdad() { return $this->edad; }
    public function getEdadFecha() { return $this->edad_fecha; }
    public function getNacimientoFecha() { return $this->nacimiento_fecha; }
    public function getEsterilizado() { return $this->esterilizado; }
    public function getUltimoCelo() { return $this->ultimo_celo; }
    public function getNotasInternas() { return $this->notas_internas; }
    public function getObservaciones() { return $this->observaciones; }
    public function getEstado() { return $this->estado; }
    public function getFechaCreacion() { return $this->fecha_creacion; }
    public function getFechaActualizacion() { return $this->fecha_actualizacion; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setIdCuidador($id_cuidador) { $this->id_cuidador = $id_cuidador; }
    public function setSlug($slug) { $this->slug = $slug; }
    public function setNombre($nombre) {
        $this->nombre = $nombre;
        // Auto-generar slug si no existe
        if (empty($this->slug)) {
            $this->slug = $this->generateSlug($nombre);
        }
    }
    public function setAlias($alias) { $this->alias = $alias; }
    public function setTipo($tipo) { $this->tipo = $tipo; }
    public function setGenero($genero) { $this->genero = $genero; }
    public function setRaza($raza) { $this->raza = $raza; }
    public function setPeso($peso) { $this->peso = $peso; }
    public function setEdad($edad) { $this->edad = $edad; }
    public function setEdadFecha($edad_fecha) { $this->edad_fecha = $edad_fecha; }
    public function setNacimientoFecha($nacimiento_fecha) { $this->nacimiento_fecha = $nacimiento_fecha; }
    public function setEsterilizado($esterilizado) { $this->esterilizado = $esterilizado; }
    public function setUltimoCelo($ultimo_celo) { $this->ultimo_celo = $ultimo_celo; }
    public function setNotasInternas($notas_internas) { $this->notas_internas = $notas_internas; }
    public function setObservaciones($observaciones) { $this->observaciones = $observaciones; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setFechaCreacion($fecha_creacion) { $this->fecha_creacion = $fecha_creacion; }
    public function setFechaActualizacion($fecha_actualizacion) { $this->fecha_actualizacion = $fecha_actualizacion; }

    // Método para generar slug automáticamente
    private function generateSlug($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }

    // Método para cargar una mascota por ID
    public function loadById($id) {
        $id = (int)$id;
        $row = $this->db->fetchRowSafe("SELECT * FROM mascotas WHERE id = ?", [$id], PDO::FETCH_ASSOC);

        if ($row) {
            $this->hydrate($row);
            return true;
        }
        return false;
    }

    // Método para cargar una mascota por slug
    public function loadBySlug($slug) {
        $row = $this->db->fetchRowSafe("SELECT * FROM mascotas WHERE slug = ?", [$slug], PDO::FETCH_ASSOC);

        if ($row) {
            $this->hydrate($row);
            return true;
        }
        return false;
    }

    // Método para hidratar el objeto con datos de la BD
    private function hydrate($data) {
        $this->id = $data['id'];
        $this->id_cuidador = $data['id_cuidador'];
        $this->slug = $data['slug'];
        $this->nombre = $data['nombre'];
        $this->alias = $data['alias'] ?? '';
        $this->tipo = $data['tipo'];
        $this->genero = $data['genero'];
        $this->raza = $data['raza'] ?? '';
        $this->peso = $data['peso'] ?? 0;
        $this->edad = $data['edad'] ?? 0;
        $this->edad_fecha = $data['edad_fecha'] ?? null;
        $this->nacimiento_fecha = $data['nacimiento_fecha'] ?? null;
        $this->esterilizado = $data['esterilizado'] ?? 0;
        $this->ultimo_celo = $data['ultimo_celo'] ?? null;
        $this->notas_internas = $data['notas_internas'] ?? '';
        $this->observaciones = $data['observaciones'] ?? '';
        $this->estado = $data['estado'] ?? 1;
        $this->fecha_creacion = $data['fecha_creacion'] ?? null;
        $this->fecha_actualizacion = $data['fecha_actualizacion'] ?? null;
    }

    // Método para guardar (crear o actualizar)
    public function save() {
        if ($this->id) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    // Método para crear nueva mascota
    private function create() {
        $data = [
            'id_cuidador' => $this->id_cuidador,
            'slug' => $this->slug,
            'nombre' => $this->nombre,
            'alias' => $this->alias,
            'tipo' => $this->tipo,
            'genero' => $this->genero,
            'raza' => $this->raza,
            'peso' => $this->peso,
            'edad' => $this->edad,
            'edad_fecha' => $this->edad_fecha,
            'nacimiento_fecha' => $this->nacimiento_fecha,
            'esterilizado' => $this->esterilizado,
            'ultimo_celo' => $this->ultimo_celo,
            'notas_internas' => $this->notas_internas,
            'observaciones' => $this->observaciones,
            'estado' => $this->estado,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];

        $result = $this->db->insertSafe('mascotas', $data);

        if ($result) {
            $this->id = $this->db->lastId();
            $this->fecha_creacion = $data['fecha_creacion'];
            $this->fecha_actualizacion = $data['fecha_actualizacion'];
            return true;
        }
        return false;
    }

    // Método para actualizar mascota existente
    private function update() {
        $data = [
            'id_cuidador' => $this->id_cuidador,
            'slug' => $this->slug,
            'nombre' => $this->nombre,
            'alias' => $this->alias,
            'tipo' => $this->tipo,
            'genero' => $this->genero,
            'raza' => $this->raza,
            'peso' => $this->peso,
            'edad' => $this->edad,
            'edad_fecha' => $this->edad_fecha,
            'nacimiento_fecha' => $this->nacimiento_fecha,
            'esterilizado' => $this->esterilizado,
            'ultimo_celo' => $this->ultimo_celo,
            'notas_internas' => $this->notas_internas,
            'observaciones' => $this->observaciones,
            'estado' => $this->estado,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];

        $result = $this->db->updateSafe('mascotas', $data, 'id = ?', [$this->id]);

        if ($result) {
            $this->fecha_actualizacion = $data['fecha_actualizacion'];
            return true;
        }
        return false;
    }

    // Método para eliminar mascota
    public function delete() {
        if (!$this->id) return false;

        return $this->db->deleteSafe('mascotas', 'id = ?', [$this->id]);
    }

    // Método para obtener las características de la mascota
    public function getCaracteristicas() {
        if (!$this->id) return [];

        $sql = "SELECT c.*, mc.valor 
                FROM caracteristicas c 
                INNER JOIN mascotas_caracteristicas mc ON c.id = mc.id_caracteristica 
                WHERE mc.id_mascota = ? 
                ORDER BY c.nombre";

        return $this->db->fetchAllSafe($sql, [$this->id]);
    }

    // Método para establecer una característica
    public function setCaracteristica($id_caracteristica, $valor) {
        if (!$this->id) return false;

        $id_caracteristica = (int)$id_caracteristica;

        // Verificar si ya existe
        $existente = $this->db->fetchRowSafe(
            "SELECT id FROM mascotas_caracteristicas WHERE id_mascota = ? AND id_caracteristica = ?",
            [$this->id, $id_caracteristica]
        );

        if ($existente) {
            // Actualizar
            return $this->db->updateSafe(
                'mascotas_caracteristicas',
                ['valor' => $valor],
                'id_mascota = ? AND id_caracteristica = ?',
                [$this->id, $id_caracteristica]
            );
        } else {
            // Insertar
            return $this->db->insertSafe('mascotas_caracteristicas', [
                'id_mascota' => $this->id,
                'id_caracteristica' => $id_caracteristica,
                'valor' => $valor
            ]);
        }
    }

    // Método para calcular la edad automáticamente
    public function calcularEdad() {
        if ($this->nacimiento_fecha) {
            $nacimiento = new DateTime($this->nacimiento_fecha);
            $hoy = new DateTime();
            $edad = $hoy->diff($nacimiento);
            $this->edad = $edad->y;
            $this->edad_fecha = $hoy->format('Y-m-d');
        }
    }

    // Método para validar los datos antes de guardar
    public function validate() {
        $errors = [];

        if (empty($this->nombre)) {
            $errors[] = "El nombre es obligatorio";
        }

        if (empty($this->id_cuidador)) {
            $errors[] = "El cuidador es obligatorio";
        }

        if (empty($this->tipo)) {
            $errors[] = "El tipo de mascota es obligatorio";
        }

        if (empty($this->genero)) {
            $errors[] = "El género es obligatorio";
        }

        // Validar que el slug sea único
        if (!empty($this->slug)) {
            $sql = "SELECT COUNT(*) as count FROM mascotas WHERE slug = ?";
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
            'id_cuidador' => $this->id_cuidador,
            'slug' => $this->slug,
            'nombre' => $this->nombre,
            'alias' => $this->alias,
            'tipo' => $this->tipo,
            'genero' => $this->genero,
            'raza' => $this->raza,
            'peso' => $this->peso,
            'edad' => $this->edad,
            'edad_fecha' => $this->edad_fecha,
            'nacimiento_fecha' => $this->nacimiento_fecha,
            'esterilizado' => $this->esterilizado,
            'ultimo_celo' => $this->ultimo_celo,
            'notas_internas' => $this->notas_internas,
            'observaciones' => $this->observaciones,
            'estado' => $this->estado,
            'fecha_creacion' => $this->fecha_creacion,
            'fecha_actualizacion' => $this->fecha_actualizacion
        ];
    }

    // ==========================================
    // MÉTODOS ESTÁTICOS (EXISTENTES + MEJORADOS)
    // ==========================================

    /**
     * Obtiene una mascota por ID (método existente mejorado)
     *
     * @param int $id_mascota ID de la mascota
     * @return object|false
     */
    public static function getMascotaById($id_mascota)
    {
        $db = Bd::getInstance();
        $filtro_cuidador = $_SESSION['admin_panel']->cuidador_id == 0 ? '' : " AND m.id_cuidador='".$_SESSION['admin_panel']->cuidador_id."'";

        $sql = "SELECT m.*, mg.nombre AS GENERO, mt.nombre AS TIPO 
                FROM mascotas m 
                INNER JOIN mascotas_tipo mt ON m.tipo=mt.id 
                INNER JOIN mascotas_genero mg ON m.genero=mg.id 
                WHERE m.id = ? {$filtro_cuidador}";

        return $db->fetchRowSafe($sql, [(int)$id_mascota]);
    }

    /**
     * Obtiene una mascota por slug (método existente mejorado)
     *
     * @param string $slug Slug de la mascota
     * @return object|false
     */
    public static function getMascotaBySlug($slug)
    {
        $db = Bd::getInstance();
        $filtro_cuidador = $_SESSION['admin_panel']->cuidador_id == 0 ? '' : " AND id_cuidador='".$_SESSION['admin_panel']->cuidador_id."'";

        $sql = "SELECT * FROM mascotas WHERE slug = ? {$filtro_cuidador}";

        return $db->fetchRowSafe($sql, [$slug]);
    }

    /**
     * Obtiene mascotas filtradas (método existente mejorado)
     *
     * @param int $comienzo Inicio de la paginación
     * @param int $limite Límite de resultados
     * @param bool $applyLimit Aplicar límite o no
     * @return array
     */
    public static function getMascotasFiltered($comienzo, $limite, $applyLimit = true)
    {
        $db = Bd::getInstance();
        $filtro_cuidador = $_SESSION['admin_panel']->cuidador_id == 0 ? '' : " AND m.id_cuidador='".$_SESSION['admin_panel']->cuidador_id."'";

        // Obtener variables de filtros
        $filter_busqueda = Tools::getValue('busqueda', '');
        $params = [];
        $whereConditions = ["1"];

        if ($filter_busqueda != '') {
            $whereConditions[] = "(m.nombre LIKE ? OR m.alias LIKE ? OR m.slug LIKE ?)";
            $params[] = "%{$filter_busqueda}%";
            $params[] = "%{$filter_busqueda}%";
            $params[] = "%{$filter_busqueda}%";
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "SELECT m.*, mg.nombre AS GENERO, mt.nombre AS TIPO 
                FROM mascotas m 
                INNER JOIN mascotas_tipo mt ON m.tipo=mt.id 
                INNER JOIN mascotas_genero mg ON m.genero=mg.id 
                WHERE {$whereClause} {$filtro_cuidador} 
                ORDER BY m.id DESC";

        if ($applyLimit && $comienzo !== null && $limite !== null) {
            $sql .= " LIMIT ?, ?";
            $params[] = (int)$comienzo;
            $params[] = (int)$limite;
        }

        return $db->fetchAllSafe($sql, $params, PDO::FETCH_OBJ);
    }

    /**
     * Elimina una mascota (método existente mejorado)
     *
     * @param int $id ID de la mascota
     * @return bool
     */
    public static function eliminarRegistro($id)
    {
        $mascota = new self();
        if ($mascota->loadById($id)) {
            return $mascota->delete();
        }
        return false;
    }

    // ==========================================
    // MÉTODOS ESTÁTICOS ADICIONALES
    // ==========================================

    /**
     * Obtiene todas las mascotas de un cuidador
     *
     * @param int $id_cuidador ID del cuidador
     * @param bool $solo_activas Solo mascotas activas
     * @return array
     */
    public static function getAllByCuidador($id_cuidador, $solo_activas = true)
    {
        $db = Bd::getInstance();
        $id_cuidador = (int)$id_cuidador;

        $sql = "SELECT m.*, mg.nombre AS GENERO, mt.nombre AS TIPO 
                FROM mascotas m 
                INNER JOIN mascotas_tipo mt ON m.tipo=mt.id 
                INNER JOIN mascotas_genero mg ON m.genero=mg.id 
                WHERE m.id_cuidador = ?";

        $params = [$id_cuidador];

        if ($solo_activas) {
            $sql .= " AND m.estado = ?";
            $params[] = 1;
        }

        $sql .= " ORDER BY m.nombre";

        return $db->fetchAllSafe($sql, $params, PDO::FETCH_OBJ);
    }

    /**
     * Obtiene todas las mascotas
     *
     * @param bool $solo_activas Solo mascotas activas
     * @return array
     */
    public static function getAll($solo_activas = true)
    {
        $db = Bd::getInstance();

        $sql = "SELECT m.*, mg.nombre AS GENERO, mt.nombre AS TIPO 
                FROM mascotas m 
                INNER JOIN mascotas_tipo mt ON m.tipo=mt.id 
                INNER JOIN mascotas_genero mg ON m.genero=mg.id 
                WHERE 1";

        $params = [];

        if ($solo_activas) {
            $sql .= " AND m.estado = ?";
            $params[] = 1;
        }

        $sql .= " ORDER BY m.nombre";

        return $db->fetchAllSafe($sql, $params, PDO::FETCH_OBJ);
    }

    /**
     * Busca mascotas por nombre
     *
     * @param string $nombre Nombre a buscar
     * @param int $limite Límite de resultados
     * @return array
     */
    public static function searchByName($nombre, $limite = 10)
    {
        $db = Bd::getInstance();

        if (empty($nombre)) {
            return [];
        }

        $sql = "SELECT m.*, mg.nombre AS GENERO, mt.nombre AS TIPO 
                FROM mascotas m 
                INNER JOIN mascotas_tipo mt ON m.tipo=mt.id 
                INNER JOIN mascotas_genero mg ON m.genero=mg.id 
                WHERE (m.nombre LIKE ? OR m.alias LIKE ?) 
                AND m.estado = 1 
                ORDER BY m.nombre 
                LIMIT ?";

        return $db->fetchAllSafe($sql, [
            "%{$nombre}%",
            "%{$nombre}%",
            (int)$limite
        ], PDO::FETCH_OBJ);
    }

    /**
     * Crea una nueva mascota usando datos del POST o array
     *
     * @param array $datos Datos de la mascota (opcional)
     * @return int|false ID de la mascota creada o false en caso de error
     */
    public static function crearMascota($datos = null)
    {
        $mascota = new self();

        if ($datos === null) {
            $datos = [
                'id_cuidador' => Tools::getValue('id_cuidador'),
                'nombre' => Tools::getValue('nombre'),
                'alias' => Tools::getValue('alias'),
                'tipo' => Tools::getValue('tipo'),
                'genero' => Tools::getValue('genero'),
                'raza' => Tools::getValue('raza'),
                'peso' => Tools::getValue('peso'),
                'nacimiento_fecha' => Tools::getValue('nacimiento_fecha'),
                'esterilizado' => Tools::getValue('esterilizado', 0),
                'ultimo_celo' => Tools::getValue('ultimo_celo'),
                'notas_internas' => Tools::getValue('notas_internas'),
                'observaciones' => Tools::getValue('observaciones')
            ];

            $slug = Tools::getValue('slug');
            if (!empty($slug)) {
                $datos['slug'] = $slug;
            }
        }

        // Establecer propiedades
        foreach ($datos as $key => $value) {
            $setter = 'set' . ucfirst(str_replace('_', '', ucwords($key, '_')));
            if (method_exists($mascota, $setter)) {
                $mascota->$setter($value);
            }
        }

        if ($mascota->save()) {
            return $mascota->getId();
        }

        return false;
    }

    /**
     * Actualiza una mascota existente
     *
     * @param int $id_mascota ID de la mascota
     * @param array $datos Datos a actualizar (opcional)
     * @return bool
     */
    public static function actualizarMascota($id_mascota = null, $datos = null)
    {
        if ($id_mascota === null) {
            $id_mascota = (int)Tools::getValue('id_mascota');
        }

        $mascota = new self();
        if (!$mascota->loadById($id_mascota)) {
            return false;
        }

        if ($datos === null) {
            $datos = [
                'id_cuidador' => Tools::getValue('id_cuidador'),
                'nombre' => Tools::getValue('nombre'),
                'alias' => Tools::getValue('alias'),
                'tipo' => Tools::getValue('tipo'),
                'genero' => Tools::getValue('genero'),
                'raza' => Tools::getValue('raza'),
                'peso' => Tools::getValue('peso'),
                'nacimiento_fecha' => Tools::getValue('nacimiento_fecha'),
                'esterilizado' => Tools::getValue('esterilizado', 0),
                'ultimo_celo' => Tools::getValue('ultimo_celo'),
                'notas_internas' => Tools::getValue('notas_internas'),
                'observaciones' => Tools::getValue('observaciones')
            ];

            $slug = Tools::getValue('slug');
            if (!empty($slug)) {
                $datos['slug'] = $slug;
            }
        }

        // Actualizar propiedades
        foreach ($datos as $key => $value) {
            $setter = 'set' . ucfirst(str_replace('_', '', ucwords($key, '_')));
            if (method_exists($mascota, $setter)) {
                $mascota->$setter($value);
            }
        }

        return $mascota->save();
    }

    /**
     * Obtiene estadísticas básicas de mascotas
     *
     * @return array
     */
    public static function getEstadisticas()
    {
        $db = Bd::getInstance();

        $stats = [];
        $stats['total'] = $db->fetchValueSafe("SELECT COUNT(*) FROM mascotas");

        $stats['por_tipo'] = $db->fetchAllSafe(
            "SELECT mt.nombre, COUNT(*) as cantidad 
             FROM mascotas m 
             INNER JOIN mascotas_tipo mt ON m.tipo = mt.id 
             GROUP BY m.tipo 
             ORDER BY cantidad DESC"
        );

        $stats['por_genero'] = $db->fetchAllSafe(
            "SELECT mg.nombre, COUNT(*) as cantidad 
             FROM mascotas m 
             INNER JOIN mascotas_genero mg ON m.genero = mg.id 
             GROUP BY m.genero 
             ORDER BY cantidad DESC"
        );

        return $stats;
    }

    /**
     * Genera un slug único basado en el nombre
     *
     * @param string $nombre Nombre de la mascota
     * @param int $id_mascota ID de la mascota (para excluirlo)
     * @return string
     */
    public static function generarSlugUnico($nombre, $id_mascota = 0)
    {
        $db = Bd::getInstance();
        $slug_base = Tools::urlAmigable($nombre);
        $slug = $slug_base;
        $contador = 1;

        while (true) {
            $params = [$slug];
            $sql = "SELECT COUNT(*) FROM mascotas WHERE slug = ?";

            if ($id_mascota > 0) {
                $sql .= " AND id != ?";
                $params[] = (int)$id_mascota;
            }

            $count = (int)$db->fetchValueSafe($sql, $params);

            if ($count === 0) {
                break;
            }

            $slug = $slug_base . '-' . $contador;
            $contador++;
        }

        return $slug;
    }

    /**
     * Obtiene el número total de mascotas
     *
     * @param bool $solo_activas Solo mascotas activas
     * @return int
     */
    public static function getTotalMascotas($solo_activas = false)
    {
        $db = Bd::getInstance();

        $sql = "SELECT COUNT(*) FROM mascotas";
        $params = [];

        if ($solo_activas) {
            $sql .= " WHERE estado = ?";
            $params[] = 1;
        }

        return (int)$db->fetchValueSafe($sql, $params);
    }
}
