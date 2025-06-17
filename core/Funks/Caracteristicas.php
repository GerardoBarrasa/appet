<?php

class Caracteristicas
{
    // Propiedades de la clase
    private $id;
    private $nombre;
    private $slug;
    private $descripcion;
    private $tipo;
    private $opciones;
    private $requerido;
    private $estado;
    private $orden;
    private $fecha_creacion;
    private $fecha_actualizacion;

    // Instancia de la base de datos
    private $db;

    public function __construct() {
        $this->db = Bd::getInstance();
        $this->estado = 1; // Valor por defecto
        $this->requerido = 0; // Valor por defecto
        $this->orden = 0;
        $this->descripcion = '';
        $this->tipo = 'text'; // Valor por defecto
        $this->opciones = '';
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getSlug() { return $this->slug; }
    public function getDescripcion() { return $this->descripcion; }
    public function getTipo() { return $this->tipo; }
    public function getOpciones() { return $this->opciones; }
    public function getRequerido() { return $this->requerido; }
    public function getEstado() { return $this->estado; }
    public function getOrden() { return $this->orden; }
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
    public function setSlug($slug) { $this->slug = $this->generateSlug($slug); }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function setTipo($tipo) { $this->tipo = $tipo; }
    public function setOpciones($opciones) {
        // Si es array, convertir a JSON
        if (is_array($opciones)) {
            $this->opciones = json_encode($opciones);
        } else {
            $this->opciones = $opciones;
        }
    }
    public function setRequerido($requerido) { $this->requerido = $requerido; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setOrden($orden) { $this->orden = $orden; }
    public function setFechaCreacion($fecha_creacion) { $this->fecha_creacion = $fecha_creacion; }
    public function setFechaActualizacion($fecha_actualizacion) { $this->fecha_actualizacion = $fecha_actualizacion; }

    // Método para generar slug automáticamente
    private function generateSlug($text) {
        return Tools::urlAmigable($text);
    }

    // Método para cargar una característica por ID
    public function loadById($id) {
        $id = (int)$id;
        $row = $this->db->fetchRowSafe("SELECT * FROM caracteristicas WHERE id = ?", [$id], PDO::FETCH_ASSOC);

        if ($row) {
            $this->hydrate($row);
            return true;
        }
        return false;
    }

    // Método para cargar una característica por slug
    public function loadBySlug($slug) {
        $row = $this->db->fetchRowSafe("SELECT * FROM caracteristicas WHERE slug = ?", [$slug], PDO::FETCH_ASSOC);

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
        $this->slug = $data['slug'];
        $this->descripcion = $data['descripcion'] ?? '';
        $this->tipo = $data['tipo'] ?? 'text';
        $this->opciones = $data['opciones'] ?? '';
        $this->requerido = $data['requerido'] ?? 0;
        $this->estado = $data['estado'] ?? 1;
        $this->orden = $data['orden'] ?? 0;
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

    // Método para crear nueva característica
    private function create() {
        // Validar datos antes de crear
        $errors = $this->validate();
        if (!empty($errors)) {
            return false;
        }

        $data = [
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'opciones' => $this->opciones,
            'requerido' => $this->requerido,
            'estado' => $this->estado,
            'orden' => $this->orden,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];

        $result = $this->db->insertSafe('caracteristicas', $data);

        if ($result) {
            $this->id = $this->db->lastId();
            $this->fecha_creacion = $data['fecha_creacion'];
            $this->fecha_actualizacion = $data['fecha_actualizacion'];
            return true;
        }
        return false;
    }

    // Método para actualizar característica existente
    private function update() {
        // Validar datos antes de actualizar
        $errors = $this->validate();
        if (!empty($errors)) {
            return false;
        }

        $data = [
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'opciones' => $this->opciones,
            'requerido' => $this->requerido,
            'estado' => $this->estado,
            'orden' => $this->orden,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];

        $result = $this->db->updateSafe('caracteristicas', $data, 'id = ?', [$this->id]);

        if ($result) {
            $this->fecha_actualizacion = $data['fecha_actualizacion'];
            return true;
        }
        return false;
    }

    // Método para eliminar característica
    public function delete() {
        if (!$this->id) return false;

        // Verificar que no esté siendo usada por mascotas
        $mascotasUsando = $this->db->fetchValueSafe(
            "SELECT COUNT(*) FROM mascotas_caracteristicas WHERE id_caracteristica = ?",
            [$this->id]
        );

        if ($mascotasUsando > 0) {
            return false; // No se puede eliminar si está siendo usada
        }

        return $this->db->deleteSafe('caracteristicas', 'id = ?', [$this->id]);
    }

    // Método para desactivar característica (soft delete)
    public function desactivar() {
        if (!$this->id) return false;

        $this->estado = 0;
        return $this->update();
    }

    // Método para activar característica
    public function activar() {
        if (!$this->id) return false;

        $this->estado = 1;
        return $this->update();
    }

    // Método para obtener las opciones como array
    public function getOpcionesArray() {
        if (empty($this->opciones)) {
            return [];
        }

        // Si es JSON, decodificar
        $decoded = json_decode($this->opciones, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Si no es JSON, dividir por comas
        return array_map('trim', explode(',', $this->opciones));
    }

    // Método para obtener las mascotas que usan esta característica
    public function getMascotasUsando() {
        if (!$this->id) return [];

        $sql = "SELECT m.*, mc.valor 
               FROM mascotas m 
               INNER JOIN mascotas_caracteristicas mc ON m.id = mc.id_mascota 
               WHERE mc.id_caracteristica = ? 
               ORDER BY m.nombre";

        return $this->db->fetchAllSafe($sql, [$this->id]);
    }

    // Método para obtener estadísticas de uso
    public function getEstadisticasUso() {
        if (!$this->id) return [];

        $stats = [];

        // Total de mascotas que usan esta característica
        $stats['total_mascotas'] = $this->db->fetchValueSafe(
            "SELECT COUNT(*) FROM mascotas_caracteristicas WHERE id_caracteristica = ?",
            [$this->id]
        );

        // Valores más comunes (para características de texto)
        if ($this->tipo === 'text' || $this->tipo === 'textarea') {
            $stats['valores_comunes'] = $this->db->fetchAllSafe(
                "SELECT valor, COUNT(*) as cantidad 
                FROM mascotas_caracteristicas 
                WHERE id_caracteristica = ? AND valor != '' 
                GROUP BY valor 
                ORDER BY cantidad DESC 
                LIMIT 10",
                [$this->id]
            );
        }

        // Distribución de valores (para características de selección)
        if ($this->tipo === 'select' || $this->tipo === 'radio') {
            $stats['distribucion_valores'] = $this->db->fetchAllSafe(
                "SELECT valor, COUNT(*) as cantidad 
                FROM mascotas_caracteristicas 
                WHERE id_caracteristica = ? 
                GROUP BY valor 
                ORDER BY cantidad DESC",
                [$this->id]
            );
        }

        return $stats;
    }

    // Método para validar los datos antes de guardar
    public function validate() {
        $errors = [];

        if (empty($this->nombre)) {
            $errors[] = "El nombre es obligatorio";
        }

        if (empty($this->slug)) {
            $errors[] = "El slug es obligatorio";
        }

        // Validar que el slug sea único
        if (!empty($this->slug)) {
            $sql = "SELECT COUNT(*) as count FROM caracteristicas WHERE slug = ?";
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

        // Validar tipo
        $tiposValidos = ['text', 'textarea', 'number', 'select', 'radio', 'checkbox', 'date', 'email', 'url'];
        if (!in_array($this->tipo, $tiposValidos)) {
            $errors[] = "Tipo de característica no válido";
        }

        // Validar opciones para tipos que las requieren
        if (in_array($this->tipo, ['select', 'radio', 'checkbox']) && empty($this->opciones)) {
            $errors[] = "Las opciones son obligatorias para este tipo de característica";
        }

        return $errors;
    }

    // Método para convertir el objeto a array
    public function toArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'opciones' => $this->opciones,
            'opciones_array' => $this->getOpcionesArray(),
            'requerido' => $this->requerido,
            'estado' => $this->estado,
            'orden' => $this->orden,
            'fecha_creacion' => $this->fecha_creacion,
            'fecha_actualizacion' => $this->fecha_actualizacion
        ];
    }

    // Método para generar HTML del campo
    public function renderField($valor = '', $atributos = []) {
        $html = '';
        $id = $atributos['id'] ?? 'caracteristica_' . $this->id;
        $name = $atributos['name'] ?? 'caracteristica_' . $this->id;
        $class = $atributos['class'] ?? 'form-control';
        $required = $this->requerido ? 'required' : '';

        switch ($this->tipo) {
            case 'text':
                $html = "<input type='text' id='{$id}' name='{$name}' class='{$class}' value='{$valor}' {$required}>";
                break;

            case 'textarea':
                $html = "<textarea id='{$id}' name='{$name}' class='{$class}' {$required}>{$valor}</textarea>";
                break;

            case 'number':
                $html = "<input type='number' id='{$id}' name='{$name}' class='{$class}' value='{$valor}' {$required}>";
                break;

            case 'email':
                $html = "<input type='email' id='{$id}' name='{$name}' class='{$class}' value='{$valor}' {$required}>";
                break;

            case 'url':
                $html = "<input type='url' id='{$id}' name='{$name}' class='{$class}' value='{$valor}' {$required}>";
                break;

            case 'date':
                $html = "<input type='date' id='{$id}' name='{$name}' class='{$class}' value='{$valor}' {$required}>";
                break;

            case 'select':
                $opciones = $this->getOpcionesArray();
                $html = "<select id='{$id}' name='{$name}' class='{$class}' {$required}>";
                $html .= "<option value=''>Seleccionar...</option>";
                foreach ($opciones as $opcion) {
                    $selected = ($valor == $opcion) ? 'selected' : '';
                    $html .= "<option value='{$opcion}' {$selected}>{$opcion}</option>";
                }
                $html .= "</select>";
                break;

            case 'radio':
                $opciones = $this->getOpcionesArray();
                foreach ($opciones as $opcion) {
                    $checked = ($valor == $opcion) ? 'checked' : '';
                    $html .= "<div class='form-check'>";
                    $html .= "<input type='radio' id='{$id}_{$opcion}' name='{$name}' class='form-check-input' value='{$opcion}' {$checked} {$required}>";
                    $html .= "<label class='form-check-label' for='{$id}_{$opcion}'>{$opcion}</label>";
                    $html .= "</div>";
                }
                break;

            case 'checkbox':
                $opciones = $this->getOpcionesArray();
                $valoresSeleccionados = is_array($valor) ? $valor : explode(',', $valor);
                foreach ($opciones as $opcion) {
                    $checked = in_array($opcion, $valoresSeleccionados) ? 'checked' : '';
                    $html .= "<div class='form-check'>";
                    $html .= "<input type='checkbox' id='{$id}_{$opcion}' name='{$name}[]' class='form-check-input' value='{$opcion}' {$checked}>";
                    $html .= "<label class='form-check-label' for='{$id}_{$opcion}'>{$opcion}</label>";
                    $html .= "</div>";
                }
                break;
        }

        return $html;
    }

    // ==========================================
    // MÉTODOS ESTÁTICOS (COMPATIBILIDAD)
    // ==========================================

    /**
     * Actualiza las características de una mascota
     *
     * @param int $idmascota ID de la mascota
     * @return array Características actualizadas
     */
    public static function updateCaracteristicasByMascota($idmascota = null)
    {
        $db = Bd::getInstance();

        // Si no se proporciona ID, tomarlo del POST
        if ($idmascota === null) {
            $idmascota = (int)Tools::getValue('idmascota');
        } else {
            $idmascota = (int)$idmascota;
        }

        if (!$idmascota) {
            __log_error("updateCaracteristicasByMascota: ID de mascota no válido");
            return [];
        }

        // Iniciar transacción para asegurar consistencia
        return $db->transaction(function($db) use ($idmascota) {
            // Verificar si tenemos evaluations en el POST
            if (isset($_POST['evaluations']) && is_array($_POST['evaluations'])) {
                foreach ($_POST['evaluations'] as $evaluation) {
                    if (isset($evaluation['id']) && isset($evaluation['value'])) {
                        $idCaracteristica = (int)$evaluation['id'];
                        $valor = $evaluation['value'];

                        if ($idCaracteristica > 0) {
                            // Si es un array (checkbox), convertir a string
                            if (is_array($valor)) {
                                $valor = implode(',', $valor);
                            }

                            // Verificar si ya existe esta característica para esta mascota
                            $existente = $db->fetchRowSafe(
                                "SELECT id_mascota FROM mascotas_caracteristicas WHERE id_mascota = ? AND id_caracteristica = ?",
                                [$idmascota, $idCaracteristica]
                            );

                            if ($existente) {
                                // Actualizar el valor existente
                                $db->updateSafe(
                                    'mascotas_caracteristicas',
                                    ['valor' => $valor],
                                    'id_mascota = ? AND id_caracteristica = ?',
                                    [$idmascota, $idCaracteristica]
                                );
                            } else {
                                // Insertar nuevo valor
                                $db->insertSafe(
                                    'mascotas_caracteristicas',
                                    [
                                        'id_mascota' => $idmascota,
                                        'id_caracteristica' => $idCaracteristica,
                                        'valor' => $valor
                                    ]
                                );
                            }
                        }
                    }
                }
            }
            // Formato tradicional (POST con caracteristica_X)
            else {
                foreach ($_POST as $clave => $valor) {
                    // Ignorar el campo idmascota
                    if ($clave == 'idmascota') {
                        continue;
                    }

                    // Verificar si la clave es una característica (formato: caracteristica_X)
                    if (strpos($clave, 'caracteristica_') === 0) {
                        $idCaracteristica = (int)substr($clave, strlen('caracteristica_'));

                        if ($idCaracteristica > 0) {
                            // Si es un array (checkbox), convertir a string
                            if (is_array($valor)) {
                                $valor = implode(',', $valor);
                            }

                            // Primero verificamos si ya existe esta característica para esta mascota
                            $existente = $db->fetchRowSafe(
                                "SELECT id_mascota FROM mascotas_caracteristicas WHERE id_mascota = ? AND id_caracteristica = ?",
                                [$idmascota, $idCaracteristica]
                            );

                            if ($existente) {
                                // Actualizar el valor existente
                                $db->updateSafe(
                                    'mascotas_caracteristicas',
                                    ['valor' => $valor],
                                    'id_mascota = ? AND id_caracteristica = ?',
                                    [$idmascota, $idCaracteristica]
                                );
                            } else {
                                // Insertar nuevo valor
                                $db->insertSafe(
                                    'mascotas_caracteristicas',
                                    [
                                        'id_mascota' => $idmascota,
                                        'id_caracteristica' => $idCaracteristica,
                                        'valor' => $valor
                                    ]
                                );
                            }
                        }
                    }
                }
            }

            // Devolver las características actualizadas
            return self::getCaracteristicasByMascota($idmascota);
        });
    }

    /**
     * Obtiene las características de una mascota
     *
     * @param int $idmascota ID de la mascota
     * @return array Características de la mascota
     */
    public static function getCaracteristicasByMascota($idmascota)
    {
        $db = Bd::getInstance();
        $idmascota = (int)$idmascota;

        if (!$idmascota) {
            __log_error("getCaracteristicasByMascota: ID de mascota no válido");
            return [];
        }

        $sql = "SELECT mc.* FROM mascotas_caracteristicas mc WHERE mc.id_mascota = ?";
        return $db->fetchAllSafe($sql, [$idmascota]);
    }

    /**
     * Obtiene las características de una mascota agrupadas por ID de característica
     *
     * @param int $idmascota ID de la mascota
     * @return array Características agrupadas por ID
     */
    public static function getCaracteristicasByMascotaGrouped($idmascota)
    {
        $db = Bd::getInstance();
        $idmascota = (int)$idmascota;

        if (!$idmascota) {
            __log_error("getCaracteristicasByMascotaGrouped: ID de mascota no válido");
            return [];
        }

        // Usamos el método legacy para mantener la compatibilidad con el código que espera este formato
        $sql = "SELECT mc.* FROM mascotas_caracteristicas mc WHERE mc.id_mascota = ?";
        $stmt = $db->prepare($sql, [$idmascota]);

        // Convertir a formato de objeto con clave
        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $row) {
            $result[$row->id_caracteristica] = $row;
        }

        return $result;
    }

    /**
     * Obtiene todas las características disponibles
     *
     * @param bool $activas Filtrar solo características activas
     * @return array Lista de características
     */
    public static function getCaracteristicas($activas = false)
    {
        $db = Bd::getInstance();

        $sql = "SELECT c.* FROM caracteristicas c WHERE 1";
        $params = [];

        if ($activas) {
            $sql .= " AND c.estado = ?";
            $params[] = 1;
        }

        $sql .= " ORDER BY c.nombre ASC";

        return $db->fetchAllSafe($sql, $params);
    }

    /**
     * Obtiene una característica por su ID
     *
     * @param int $id ID de la característica
     * @return object|false Datos de la característica o false si no existe
     */
    public static function getCaracteristicaById($id)
    {
        $db = Bd::getInstance();
        $id = (int)$id;

        if (!$id) {
            return false;
        }

        return $db->fetchRowSafe(
            "SELECT * FROM caracteristicas WHERE id = ?",
            [$id]
        );
    }

    /**
     * Obtiene una característica por su slug
     *
     * @param string $slug Slug de la característica
     * @return object|false Datos de la característica o false si no existe
     */
    public static function getCaracteristicaBySlug($slug)
    {
        $db = Bd::getInstance();

        if (empty($slug)) {
            return false;
        }

        return $db->fetchRowSafe(
            "SELECT * FROM caracteristicas WHERE slug = ?",
            [$slug]
        );
    }

    /**
     * Elimina una característica
     *
     * @param int $id ID de la característica
     * @return bool Resultado de la operación
     */
    public static function eliminarRegistro($id)
    {
        $caracteristica = new self();
        if ($caracteristica->loadById($id)) {
            return $caracteristica->delete();
        }
        return false;
    }

    /**
     * Elimina una característica de una mascota
     *
     * @param int $idmascota ID de la mascota
     * @param int $idcaracteristica ID de la característica
     * @return bool Resultado de la operación
     */
    public static function eliminarCaracteristicaMascota($idmascota, $idcaracteristica)
    {
        $db = Bd::getInstance();
        $idmascota = (int)$idmascota;
        $idcaracteristica = (int)$idcaracteristica;

        if (!$idmascota || !$idcaracteristica) {
            return false;
        }

        return $db->deleteSafe(
            'mascotas_caracteristicas',
            'id_mascota = ? AND id_caracteristica = ?',
            [$idmascota, $idcaracteristica]
        );
    }

    /**
     * Crea una nueva característica
     *
     * @param array $datos Datos de la característica
     * @return int|false ID de la característica creada o false en caso de error
     */
    public static function crearCaracteristica($datos)
    {
        $caracteristica = new self();

        // Establecer propiedades
        foreach ($datos as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($caracteristica, $setter)) {
                $caracteristica->$setter($value);
            }
        }

        if ($caracteristica->save()) {
            return $caracteristica->getId();
        }

        return false;
    }

    /**
     * Actualiza una característica existente
     *
     * @param int $id ID de la característica
     * @param array $datos Datos a actualizar
     * @return bool Resultado de la operación
     */
    public static function actualizarCaracteristica($id, $datos)
    {
        $caracteristica = new self();
        if (!$caracteristica->loadById($id)) {
            return false;
        }

        // Actualizar propiedades
        foreach ($datos as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($caracteristica, $setter)) {
                $caracteristica->$setter($value);
            }
        }

        return $caracteristica->save();
    }

    /**
     * Obtiene las características más utilizadas
     *
     * @param int $limit Límite de resultados
     * @return array Lista de características más utilizadas
     */
    public static function getCaracteristicasMasUtilizadas($limit = 10)
    {
        $db = Bd::getInstance();
        $limit = (int)$limit;

        $sql = "SELECT c.*, COUNT(mc.id_mascota) as total_mascotas 
               FROM caracteristicas c 
               INNER JOIN mascotas_caracteristicas mc ON c.id = mc.id_caracteristica 
               GROUP BY c.id, c.nombre 
               ORDER BY total_mascotas DESC, c.nombre";

        if ($limit > 0) {
            $sql .= " LIMIT ?";
            return $db->fetchAllSafe($sql, [$limit]);
        }

        return $db->fetchAllSafe($sql);
    }

    /**
     * Obtiene todas las características activas ordenadas
     *
     * @return array Lista de características activas
     */
    public static function getCaracteristicasActivas()
    {
        return self::getCaracteristicas(true);
    }

    /**
     * Busca características por nombre
     *
     * @param string $termino Término de búsqueda
     * @param int $limite Límite de resultados
     * @return array
     */
    public static function buscarCaracteristicas($termino, $limite = 10)
    {
        $db = Bd::getInstance();

        if (empty($termino)) {
            return [];
        }

        $sql = "SELECT * FROM caracteristicas 
               WHERE (nombre LIKE ? OR slug LIKE ?) 
               ORDER BY nombre 
               LIMIT ?";

        return $db->fetchAllSafe($sql, [
            "%{$termino}%",
            "%{$termino}%",
            (int)$limite
        ]);
    }

    /**
     * Obtiene el número total de características
     *
     * @param bool $solo_activas Solo características activas
     * @return int
     */
    public static function getTotalCaracteristicas($solo_activas = false)
    {
        $db = Bd::getInstance();

        $sql = "SELECT COUNT(*) FROM caracteristicas";
        $params = [];

        if ($solo_activas) {
            $sql .= " WHERE estado = ?";
            $params[] = 1;
        }

        return (int)$db->fetchValueSafe($sql, $params);
    }

    /**
     * Genera un slug único basado en el nombre
     *
     * @param string $nombre Nombre de la característica
     * @param int $id_caracteristica ID de la característica (para excluirlo)
     * @return string
     */
    public static function generarSlugUnico($nombre, $id_caracteristica = 0)
    {
        $db = Bd::getInstance();
        $slug_base = Tools::urlAmigable($nombre);
        $slug = $slug_base;
        $contador = 1;

        while (true) {
            $params = [$slug];
            $sql = "SELECT COUNT(*) FROM caracteristicas WHERE slug = ?";

            if ($id_caracteristica > 0) {
                $sql .= " AND id != ?";
                $params[] = (int)$id_caracteristica;
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
}
