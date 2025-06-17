<?php

class Generos
{
    // Propiedades de la clase que coinciden con la estructura de la tabla mascotas_genero
    private $id;
    private $nombre;
    private $slug;

    // Instancia de la base de datos
    private $db;

    public function __construct() {
        $this->db = Bd::getInstance();
    }

    // ==========================================
    // GETTERS Y SETTERS
    // ==========================================

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getSlug() { return $this->slug; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) {
        $this->nombre = $nombre;
        // Regenerar slug cuando se cambia el nombre
        if (!empty($nombre)) {
            $this->slug = $this->generateSlug($nombre);
        }
    }
    public function setSlug($slug) { $this->slug = $slug; }

    // ==========================================
    // MÉTODOS PRIVADOS
    // ==========================================

    /**
     * Genera un slug único basado en el nombre
     */
    private function generateSlug($text) {
        $slug_base = $this->createUrlSafeSlug($text);
        return $this->ensureUniqueSlug($slug_base);
    }

    /**
     * Crea un slug seguro para URL
     */
    private function createUrlSafeSlug($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[áàäâ]/u', 'a', $text);
        $text = preg_replace('/[éèëê]/u', 'e', $text);
        $text = preg_replace('/[íìïî]/u', 'i', $text);
        $text = preg_replace('/[óòöô]/u', 'o', $text);
        $text = preg_replace('/[úùüû]/u', 'u', $text);
        $text = preg_replace('/[ñ]/u', 'n', $text);
        $text = preg_replace('/[ç]/u', 'c', $text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }

    /**
     * Asegura que el slug sea único
     */
    private function ensureUniqueSlug($slug_base) {
        $slug = $slug_base;
        $counter = 1;

        while (!$this->isSlugUnique($slug)) {
            $slug = $slug_base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Verifica si un slug es único
     */
    private function isSlugUnique($slug) {
        $params = [$slug];
        $sql = "SELECT COUNT(*) FROM mascotas_genero WHERE slug = ?";

        if ($this->id) {
            $sql .= " AND id != ?";
            $params[] = $this->id;
        }

        $count = (int)$this->db->fetchValueSafe($sql, $params);
        return $count === 0;
    }

    /**
     * Hidrata el objeto con datos de la BD
     */
    private function hydrate($data) {
        $this->id = $data['id'];
        $this->nombre = $data['nombre'];
        $this->slug = $data['slug'];
    }

    // ==========================================
    // MÉTODOS DE CARGA
    // ==========================================

    /**
     * Carga un género por ID
     */
    public function loadById($id) {
        $id = (int)$id;
        $row = $this->db->fetchRowSafe("SELECT * FROM mascotas_genero WHERE id = ?", [$id], PDO::FETCH_ASSOC);

        if ($row) {
            $this->hydrate($row);
            return true;
        }
        return false;
    }

    /**
     * Carga un género por slug
     */
    public function loadBySlug($slug) {
        $row = $this->db->fetchRowSafe("SELECT * FROM mascotas_genero WHERE slug = ?", [$slug], PDO::FETCH_ASSOC);

        if ($row) {
            $this->hydrate($row);
            return true;
        }
        return false;
    }

    // ==========================================
    // MÉTODOS DE PERSISTENCIA
    // ==========================================

    /**
     * Guarda el género (crear o actualizar)
     */
    public function save() {
        if ($this->id) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    /**
     * Crea un nuevo género
     */
    private function create() {
        $data = [
            'nombre' => $this->nombre,
            'slug' => $this->slug
        ];

        error_log("DEBUG - Creando género: " . print_r($data, true));

        $result = $this->db->insertSafe('mascotas_genero', $data);

        if ($result) {
            $this->id = $this->db->lastId();
            error_log("DEBUG - Género creado con ID: " . $this->id);
            return true;
        }

        error_log("DEBUG - Error al crear género");
        return false;
    }

    /**
     * Actualiza un género existente
     */
    private function update() {
        $data = [
            'nombre' => $this->nombre,
            'slug' => $this->slug
        ];

        error_log("DEBUG - Actualizando género ID {$this->id}: " . print_r($data, true));

        $result = $this->db->updateSafe('mascotas_genero', $data, 'id = :genero_id', ['genero_id' => $this->id]);

        if ($result !== false) {
            error_log("DEBUG - Género actualizado correctamente");
            return true;
        }

        error_log("DEBUG - Error al actualizar género");
        return false;
    }

    /**
     * Elimina el género
     */
    public function delete() {
        if (!$this->id) return false;

        // Verificar si hay mascotas usando este género
        $count = $this->db->fetchValueSafe("SELECT COUNT(*) FROM mascotas WHERE genero = ?", [$this->id]);

        if ($count > 0) {
            error_log("DEBUG - No se puede eliminar género ID {$this->id}: tiene {$count} mascotas asociadas");
            return false;
        }

        $result = $this->db->deleteSafe('mascotas_genero', 'id = ?', [$this->id]);

        if ($result) {
            error_log("DEBUG - Género ID {$this->id} eliminado correctamente");
        }

        return $result;
    }

    /**
     * Valida los datos antes de guardar
     */
    public function validate() {
        $errors = [];

        if (empty($this->nombre)) {
            $errors[] = "El nombre es obligatorio";
        }

        // Validar que el nombre sea único
        if (!empty($this->nombre)) {
            $sql = "SELECT COUNT(*) FROM mascotas_genero WHERE nombre = ?";
            $params = [$this->nombre];

            if ($this->id) {
                $sql .= " AND id != ?";
                $params[] = $this->id;
            }

            $count = $this->db->fetchValueSafe($sql, $params);
            if ($count > 0) {
                $errors[] = "Ya existe un género con este nombre";
            }
        }

        if (!empty($errors)) {
            error_log("DEBUG - Errores de validación en género: " . print_r($errors, true));
        }

        return $errors;
    }

    /**
     * Convierte el objeto a array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'slug' => $this->slug
        ];
    }

    // ==========================================
    // MÉTODOS ESTÁTICOS
    // ==========================================

    /**
     * Obtiene todos los géneros
     */
    public static function getTodosLosGeneros() {
        $db = Bd::getInstance();

        $sql = "SELECT * FROM mascotas_genero ORDER BY nombre ASC";

        return $db->fetchAllSafe($sql, [], PDO::FETCH_OBJ);
    }

    /**
     * Obtiene géneros como array para selects
     */
    public static function getGenerosParaSelect() {
        $db = Bd::getInstance();

        $sql = "SELECT id, nombre FROM mascotas_genero ORDER BY nombre ASC";
        $generos = $db->fetchAllSafe($sql, [], PDO::FETCH_OBJ);

        $options = [];
        foreach ($generos as $genero) {
            $options[$genero->id] = $genero->nombre;
        }

        return $options;
    }

    /**
     * Obtiene un género por ID
     */
    public static function getGeneroById($id) {
        $db = Bd::getInstance();

        $sql = "SELECT * FROM mascotas_genero WHERE id = ?";

        return $db->fetchRowSafe($sql, [(int)$id], PDO::FETCH_OBJ);
    }

    /**
     * Obtiene un género por slug
     */
    public static function getGeneroBySlug($slug) {
        $db = Bd::getInstance();

        $sql = "SELECT * FROM mascotas_genero WHERE slug = ?";

        return $db->fetchRowSafe($sql, [$slug], PDO::FETCH_OBJ);
    }

    /**
     * Obtiene un género por nombre
     */
    public static function getGeneroByNombre($nombre) {
        $db = Bd::getInstance();

        $sql = "SELECT * FROM mascotas_genero WHERE nombre = ?";

        return $db->fetchRowSafe($sql, [$nombre], PDO::FETCH_OBJ);
    }

    /**
     * Busca géneros por término
     */
    public static function buscarGeneros($termino) {
        $db = Bd::getInstance();

        $sql = "SELECT * FROM mascotas_genero WHERE nombre LIKE ? ORDER BY nombre ASC";

        return $db->fetchAllSafe($sql, ["%{$termino}%"], PDO::FETCH_OBJ);
    }

    /**
     * Obtiene estadísticas de géneros
     */
    public static function getEstadisticas() {
        $db = Bd::getInstance();

        $stats = [];
        $stats['total'] = $db->fetchValueSafe("SELECT COUNT(*) FROM mascotas_genero");

        $stats['uso_por_genero'] = $db->fetchAllSafe(
            "SELECT mg.nombre, COUNT(m.id) as cantidad_mascotas 
             FROM mascotas_genero mg 
             LEFT JOIN mascotas m ON mg.id = m.genero 
             GROUP BY mg.id 
             ORDER BY cantidad_mascotas DESC"
        );

        return $stats;
    }

    /**
     * Crea un nuevo género usando datos del POST o array
     */
    public static function crearGenero($datos = null) {
        $genero = new self();

        if ($datos === null) {
            $datos = [
                'nombre' => Tools::getValue('nombre')
            ];

            $slug = Tools::getValue('slug');
            if (!empty($slug)) {
                $datos['slug'] = $slug;
            }
        }

        // Establecer propiedades
        foreach ($datos as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($genero, $setter)) {
                $genero->$setter($value);
            }
        }

        // Validar antes de guardar
        $errors = $genero->validate();
        if (!empty($errors)) {
            error_log("DEBUG - Errores al crear género: " . print_r($errors, true));
            return false;
        }

        if ($genero->save()) {
            return $genero->getId();
        }

        return false;
    }

    /**
     * Actualiza un género existente
     */
    public static function actualizarGenero($id_genero = null, $datos = null) {
        if ($id_genero === null) {
            $id_genero = (int)Tools::getValue('id_genero');
        }

        error_log("DEBUG - actualizarGenero llamado con ID: {$id_genero}");

        $genero = new self();
        if (!$genero->loadById($id_genero)) {
            error_log("DEBUG - No se pudo cargar el género con ID: {$id_genero}");
            return false;
        }

        if ($datos === null) {
            $datos = [
                'nombre' => Tools::getValue('nombre')
            ];

            $slug = Tools::getValue('slug');
            if (!empty($slug)) {
                $datos['slug'] = $slug;
            }
        }

        error_log("DEBUG - Datos a actualizar: " . print_r($datos, true));

        // Actualizar propiedades
        foreach ($datos as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($genero, $setter)) {
                $genero->$setter($value);
            }
        }

        // Validar antes de guardar
        $errors = $genero->validate();
        if (!empty($errors)) {
            error_log("DEBUG - Errores de validación: " . print_r($errors, true));
            return false;
        }

        return $genero->save();
    }

    /**
     * Elimina un género
     */
    public static function eliminarGenero($id) {
        $genero = new self();
        if ($genero->loadById($id)) {
            return $genero->delete();
        }
        return false;
    }

    /**
     * Obtiene el número total de géneros
     */
    public static function getTotalGeneros() {
        $db = Bd::getInstance();
        return (int)$db->fetchValueSafe("SELECT COUNT(*) FROM mascotas_genero");
    }

    /**
     * Genera un slug único estático
     */
    public static function generarSlugUnico($nombre, $id_genero = 0) {
        $db = Bd::getInstance();

        // Crear slug base seguro para URL
        $slug_base = self::createStaticUrlSafeSlug($nombre);
        $slug = $slug_base;
        $counter = 1;

        // Verificar unicidad
        while (true) {
            $params = [$slug];
            $sql = "SELECT COUNT(*) FROM mascotas_genero WHERE slug = ?";

            if ($id_genero > 0) {
                $sql .= " AND id != ?";
                $params[] = (int)$id_genero;
            }

            $count = (int)$db->fetchValueSafe($sql, $params);

            if ($count === 0) {
                break;
            }

            $slug = $slug_base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Método estático para crear slug seguro para URL
     */
    private static function createStaticUrlSafeSlug($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[áàäâ]/u', 'a', $text);
        $text = preg_replace('/[éèëê]/u', 'e', $text);
        $text = preg_replace('/[íìïî]/u', 'i', $text);
        $text = preg_replace('/[óòöô]/u', 'o', $text);
        $text = preg_replace('/[úùüû]/u', 'u', $text);
        $text = preg_replace('/[ñ]/u', 'n', $text);
        $text = preg_replace('/[ç]/u', 'c', $text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }

    /**
     * Verifica si un género está siendo usado por mascotas
     */
    public static function estaEnUso($id_genero) {
        $db = Bd::getInstance();

        $count = $db->fetchValueSafe("SELECT COUNT(*) FROM mascotas WHERE genero = ?", [(int)$id_genero]);

        return $count > 0;
    }

    /**
     * Obtiene las mascotas que usan un género específico
     */
    public static function getMascotasPorGenero($id_genero) {
        $db = Bd::getInstance();

        $sql = "SELECT m.*, c.nombre as cuidador_nombre 
                FROM mascotas m 
                INNER JOIN cuidadores c ON m.id_cuidador = c.id 
                WHERE m.genero = ? 
                ORDER BY m.nombre ASC";

        return $db->fetchAllSafe($sql, [(int)$id_genero], PDO::FETCH_OBJ);
    }
}
