# ApPet 1.0.0

## Mejoras en la clase Bd

### 🔒 **Seguridad**

- **PDO con Prepared Statements**: Métodos seguros que previenen SQL injection
- **Escape automático**: Para métodos legacy que aún lo necesiten
- **Validación de parámetros**: Mejor manejo de datos de entrada


### 🚀 **Nuevas funcionalidades**

- **Transacciones**: Soporte completo para transacciones de base de datos
- **Métodos seguros**: `insertSafe()`, `updateSafe()`, `deleteSafe()`, etc.
- **Mejor manejo de errores**: Logging consistente y excepciones apropiadas


### 🔄 **Compatibilidad**

- **Métodos legacy**: Todos los métodos antiguos siguen funcionando
- **Migración gradual**: Puedes ir cambiando poco a poco a los métodos seguros
- **Doble conexión**: Mantiene tanto PDO como MySQLi para compatibilidad


### 📊 **Utilidades**

- **Información de conexión**: Método para debugging
- **Verificación de estado**: Comprobar si la conexión está activa
- **UTF-8 completo**: Soporte para utf8mb4


**Ejemplo de uso de los nuevos métodos:**

```php
$bd = Bd::getInstance();

// Método seguro (recomendado)
$usuario = $bd->fetchRowSafe(
    "SELECT * FROM usuarios WHERE email = ? AND activo = ?", 
    [$email, 1]
);

// Transacción
$bd->transaction(function($db) use ($userData) {
    $userId = $db->insertSafe('usuarios', $userData);
    $db->insertSafe('usuarios_perfiles', ['user_id' => $userId, 'perfil' => 'user']);
    return $userId;
});

// Método legacy (sigue funcionando)
$usuarios = $bd->fetchObject("SELECT * FROM usuarios");
```

## Mejoras de la clase Admin

### 🔒 **Seguridad**

- **Consultas preparadas**: Todas las consultas ahora usan parámetros preparados
- **Validación de tipos**: Conversión explícita de IDs a enteros
- **Protección contra SQL Injection**: Eliminación de concatenación directa de variables


### 🚀 **Nuevas funcionalidades**

- **Método para cambiar contraseña**: Función específica para esta operación común
- **Verificación de email**: Método para comprobar si un email ya existe
- **Mejor manejo de errores**: Código más robusto


### 📝 **Documentación**

- **PHPDoc**: Documentación completa de todos los métodos
- **Tipos de retorno**: Especificación clara de lo que devuelve cada método
- **Parámetros documentados**: Descripción de cada parámetro


### 🔄 **Mejoras de código**

- **Uso de métodos seguros**: Reemplazo de métodos legacy por los nuevos métodos seguros
- **Consistencia**: Uso consistente de la instancia de base de datos
- **Mejor organización**: Métodos agrupados por funcionalidad


**Ejemplo de mejora de seguridad:**

```php
// ANTES (vulnerable a SQL injection)
$datos = Bd::getInstance()->fetchRow("SELECT * FROM usuarios_admin WHERE email='".$usuario."' AND password='".$password."'");

// AHORA (seguro con consultas preparadas)
$datos = $db->fetchRowSafe(
    "SELECT * FROM usuarios_admin WHERE email = ? AND password = ?",
    [$usuario, $password]
);
```

**Ejemplo de mejora en actualización:**

```php
// ANTES (concatenación directa)
return Bd::getInstance()->update('usuarios_admin', $updUsuario, "id_usuario_admin = ".(int)Tools::getValue('id_usuario_admin'));

// AHORA (parámetros preparados)
return $db->updateSafe(
    'usuarios_admin',
    $updUsuario,
    'id_usuario_admin = ?',
    [$id_usuario_admin]
);
```

## Mejoras de la clase Cuidador

### 🏗️ **Arquitectura de Objeto**

- **Propiedades privadas**: Encapsulación completa de los datos
- **Getters y Setters**: Control de acceso a las propiedades
- **Hidratación**: Método para cargar datos desde la base de datos
- **Validación**: Validación automática antes de guardar


### 🔄 **Compatibilidad**

- **Métodos estáticos**: Mantiene todos los métodos estáticos existentes
- **Misma interfaz**: El código existente seguirá funcionando sin cambios
- **Migración gradual**: Puedes ir cambiando poco a poco al uso de objetos


### 🚀 **Nuevas funcionalidades del objeto**

- **CRUD completo**: `save()`, `delete()`, `loadById()`, etc.
- **Validación automática**: Valida datos antes de guardar
- **Relaciones**: Obtener mascotas y estadísticas del cuidador
- **Conversión**: `toArray()` para serialización


### 📝 **Ejemplos de uso**

**Uso como objeto (nuevo):**

```php
// Crear nuevo cuidador
$cuidador = new Cuidador();
$cuidador->setNombre('Juan Pérez');
$cuidador->setEmail('juan@example.com');
$cuidador->setTelefono('123456789');
$cuidador->save();

// Cargar cuidador existente
$cuidador = new Cuidador();
$cuidador->loadById(1);
echo $cuidador->getNombre();

// Obtener mascotas del cuidador
$mascotas = $cuidador->getMascotas();

// Obtener estadísticas
$stats = $cuidador->getEstadisticas();
```

**Uso estático (compatible con código existente):**

```php
// Sigue funcionando igual que antes
$cuidador = Cuidador::getCuidadorById(1);
$mascotas = Cuidador::getMascotasByCuidador(1);
$stats = Cuidador::getEstadisticasCuidador(1);
```

## Mejoras de la clase Caracteristicas

### 🏗️ **Arquitectura de Objeto Completa**

- **Propiedades específicas**: `tipo`, `opciones`, `requerido`, `orden` para características avanzadas
- **Validación robusta**: Valida tipos de campo y opciones requeridas
- **Renderizado HTML**: Método `renderField()` para generar campos de formulario automáticamente


### 🎨 **Tipos de Características Soportados**

- **text**: Campo de texto simple
- **textarea**: Área de texto
- **number**: Campo numérico
- **email**: Campo de email con validación
- **url**: Campo de URL
- **date**: Selector de fecha
- **select**: Lista desplegable
- **radio**: Botones de radio
- **checkbox**: Casillas de verificación múltiple


### 🔧 **Funcionalidades Avanzadas**

- **Gestión de opciones**: Soporte para JSON y texto separado por comas
- **Renderizado automático**: Genera HTML según el tipo de característica
- **Estadísticas de uso**: Analiza cómo se usan las características
- **Relaciones**: Obtiene mascotas que usan cada característica


### 📝 **Ejemplos de uso**

**Crear una característica como objeto:**

```php
// Crear característica de texto
$caracteristica = new Caracteristicas();
$caracteristica->setNombre('Color de pelo');
$caracteristica->setTipo('text');
$caracteristica->setDescripcion('Color principal del pelaje');
$caracteristica->setRequerido(1);
$caracteristica->save();

// Crear característica de selección
$caracteristica = new Caracteristicas();
$caracteristica->setNombre('Tamaño');
$caracteristica->setTipo('select');
$caracteristica->setOpciones(['Pequeño', 'Mediano', 'Grande']);
$caracteristica->save();
```

**Renderizar campos de formulario:**

```php
$caracteristica = new Caracteristicas();
$caracteristica->loadById(1);

// Generar HTML del campo
echo $caracteristica->renderField('Valor actual', [
    'class' => 'form-control custom-class',
    'id' => 'mi_campo'
]);
```

**Uso estático (compatible):**

```php
// Sigue funcionando igual que antes
$caracteristicas = Caracteristicas::getCaracteristicas(true);
$caracteristica = Caracteristicas::getCaracteristicaById(1);
Caracteristicas::updateCaracteristicasByMascota(1);
```

### 🚀 **Nuevas capacidades**

1. **Renderizado automático**: Genera campos HTML según el tipo
2. **Validación de tipos**: Asegura que las opciones sean correctas
3. **Estadísticas avanzadas**: Analiza el uso de cada característica
4. **Gestión de opciones**: Soporte flexible para diferentes formatos
5. **Compatibilidad total**: Mantiene toda la funcionalidad existente

## Mejoras de la clase Mascotas

### ✅ **Compatibilidad Total**

- **Métodos existentes**: `getMascotaById()`, `getMascotaBySlug()`, `getMascotasFiltered()`, `eliminarRegistro()`
- **Misma interfaz**: Todo el código existente seguirá funcionando sin cambios
- **Filtros de cuidador**: Mantiene la lógica de filtrado por cuidador de la sesión


### 🚀 **Nuevas Funcionalidades**

- **Uso como objeto**: Ahora puedes usar `new Mascotas()` para crear instancias
- **CRUD completo**: `save()`, `delete()`, `loadById()`, etc.
- **Métodos adicionales**: `crearMascota()`, `actualizarMascota()`, `getEstadisticas()`
- **Validación**: Validación automática antes de guardar


### 📝 **Ejemplos de uso**

**Uso existente (sin cambios):**

```php
// Sigue funcionando igual que antes
$mascota = Mascotas::getMascotaById(1);
$mascotas = Mascotas::getMascotasFiltered(0, 10);
Mascotas::eliminarRegistro(1);
```

**Nuevo uso como objeto:**

```php
// Crear nueva mascota
$mascota = new Mascotas();
$mascota->setNombre('Firulais');
$mascota->setIdCuidador(1);
$mascota->setTipo(1);
$mascota->setGenero(1);
$mascota->save();

// Cargar mascota existente
$mascota = new Mascotas();
$mascota->loadById(1);
echo $mascota->getNombre();

// Obtener características
$caracteristicas = $mascota->getCaracteristicas();
```

**Nuevos métodos estáticos:**

```php
// Crear mascota desde POST
$id = Mascotas::crearMascota();

// Actualizar mascota
Mascotas::actualizarMascota(1);

// Obtener estadísticas
$stats = Mascotas::getEstadisticas();

// Buscar por nombre
$mascotas = Mascotas::searchByName('Firulais');
```

## Mejoras Implementadas en DefaultController

### 1. **Estructura Modular y Organizada**

- **Métodos separados**: Dividí la lógica en métodos específicos para cada tarea
- **Configuración centralizada**: Añadí una propiedad `$config` para centralizar la configuración
- **Acciones como métodos**: Convertí las funciones anónimas en métodos de clase


### 2. **Mejor Manejo de Rutas**

- **Método `defineRoutes()`**: Centraliza la definición de rutas
- **Callbacks como métodos**: Usa `[$this, 'methodName']` en lugar de funciones anónimas
- **Fácil extensión**: Simplemente añade nuevos métodos y rutas


### 3. **Mejoras de Seguridad y Rendimiento**

- **Código HTTP correcto**: Establece el código 404 para páginas no encontradas
- **Carga condicional**: Solo carga lo necesario
- **Constante de configuración**: Añadí `_REDIRECT_TO_ADMIN_` para controlar la redirección


### 4. **Mejor Documentación**

- **DocBlocks**: Documentación completa para todos los métodos
- **Comentarios explicativos**: Explican el propósito de cada sección
- **Tipado en PHPDoc**: Indica los tipos de parámetros y retornos


### 5. **Flexibilidad Mejorada**

- **Fácil de extender**: Añadir nuevas rutas es tan simple como crear un método y registrarlo
- **Configuración centralizada**: Cambiar el layout o añadir assets es más sencillo
- **Mejor manejo de errores**: Redirecciones más claras y códigos HTTP correctos


## Ventajas de esta Implementación

1. **Compatibilidad**: Mantiene la misma funcionalidad que el código original
2. **Mantenibilidad**: Código más organizado y fácil de entender
3. **Extensibilidad**: Fácil de añadir nuevas rutas y funcionalidades
4. **Rendimiento**: Carga solo lo necesario para cada ruta
5. **Seguridad**: Mejor manejo de redirecciones y códigos HTTP


## Uso de la Nueva Implementación

Para añadir una nueva ruta, simplemente:

1. Crea un nuevo método en el controlador:


```php
public function contactAction()
{
    $data = [
        'current_page' => 'contacto'
    ];
    
    Render::page('contacto', $data);
}
```

2. Registra la ruta en `defineRoutes()`:


```php
$this->add('contacto', [$this, 'contactAction']);
```

## Mejoras Implementadas en AdminController

### 1. **Estructura Modular y Organizada**

- **Métodos separados por funcionalidad**: Cada acción tiene su propio método
- **Configuración centralizada**: Array `$config` para assets, paginación, etc.
- **Helpers de autenticación**: Métodos para verificar permisos y autenticación


### 2. **Mejor Gestión de Autenticación y Autorización**

- **`isAuthenticated()`**: Verifica si el usuario está logueado
- **`requireAuth()`**: Requiere autenticación para acceder
- **`isSuperAdmin()`**: Verifica si es super administrador
- **`requireSuperAdmin()`**: Requiere permisos de super admin


### 3. **Organización de Rutas por Funcionalidad**

- **Autenticación**: Login/logout
- **Gestión de idiomas**: Crear, editar idiomas
- **Gestión de traducciones**: Crear, editar traducciones
- **Gestión de slugs**: Configuración de URLs
- **Gestión de usuarios**: Administrar usuarios del panel
- **Gestión de mascotas**: CRUD de mascotas


### 4. **Mejor Manejo de Formularios**

- **Métodos específicos**: `handleLogin()`, `handleUpdateTraduccion()`, etc.
- **Validación centralizada**: Lógica de validación en métodos separados
- **Mensajes de feedback**: Uso consistente de `Tools::registerAlert()`


### 5. **Mejoras de Seguridad**

- **Verificación de permisos**: Cada acción verifica los permisos necesarios
- **Redirecciones seguras**: Redirecciones apropiadas según el contexto
- **Validación de datos**: Validación antes de procesar formularios


### 6. **Mejor Documentación y Mantenibilidad**

- **DocBlocks completos**: Documentación para todos los métodos
- **Código más legible**: Estructura clara y fácil de seguir
- **Separación de responsabilidades**: Cada método tiene una función específica


## Ventajas de la Nueva Implementación

1. **Mantenibilidad**: Código mucho más fácil de mantener y debuggear
2. **Extensibilidad**: Fácil añadir nuevas funcionalidades
3. **Seguridad**: Mejor control de acceso y validaciones
4. **Rendimiento**: Carga solo los assets necesarios para cada sección
5. **Compatibilidad**: Mantiene toda la funcionalidad original


## Ejemplo de Uso

Para añadir una nueva sección al panel de administración:

1. **Añadir la ruta** en `defineRoutes()`:


```php
$this->add('nueva-seccion', [$this, 'nuevaSeccionAction']);
```

2. **Crear el método de acción**:


```php
public function nuevaSeccionAction()
{
    $this->requireAuth(); // o $this->requireSuperAdmin()
    
    // Lógica específica
    $data = [
        'datos' => $this->obtenerDatos()
    ];
    
    Render::adminPage('nueva_seccion', $data);
}
```

## Mejoras Implementadas en Controllers.php

### 1. **Estructura Modular y Organizada**

- **Métodos específicos**: Dividí `load()` en métodos más pequeños y específicos
- **Configuración centralizada**: Array `$config` para configuraciones del controlador
- **Mejor organización**: Agrupé métodos por funcionalidad


### 2. **Sistema de Rutas Mejorado**

- **Registro de rutas**: Las rutas se almacenan en un array para referencia
- **Ejecución flexible**: Soporte para callbacks y métodos de clase
- **Verificación de rutas**: Métodos para verificar si una ruta existe
- **Ejecución manual**: Posibilidad de ejecutar rutas específicas


### 3. **Sistema de Middleware**

- **Middleware antes/después**: Ejecuta código antes y después del controlador
- **Registro flexible**: Fácil registro de middleware personalizado
- **Extensibilidad**: Permite añadir funcionalidades transversales


### 4. **Mejor Manejo de Seguridad**

- **Protección CSRF mejorada**: Manejo más robusto de tokens
- **Validación de controladores**: Verificación de existencia de clases
- **Respuestas JSON seguras**: Manejo apropiado de errores AJAX


### 5. **Utilidades Adicionales**

- **Métodos de redirección**: Redirecciones más flexibles
- **Generación de URLs**: Creación automática de URLs
- **Detección de peticiones**: AJAX, POST, GET
- **Logging**: Sistema de logs para controladores
- **IP del cliente**: Obtención segura de la IP


### 6. **Mejor Documentación**

- **DocBlocks completos**: Documentación detallada para todos los métodos
- **Comentarios explicativos**: Explicación del propósito de cada sección
- **Tipado en PHPDoc**: Especificación de tipos de parámetros y retornos

## **Sistema de Middleware**

```php
// Registrar middleware que se ejecute antes de todos los controladores
$controller->registerMiddleware('before', function($ctrl) {
    // Código que se ejecuta antes
});

// Registrar middleware que se ejecute después
$controller->registerMiddleware('after', function($ctrl) {
    // Código que se ejecuta después
});
```

### **Utilidades de Petición**

```php
// En cualquier controlador hijo
if ($this->isAjaxRequest()) {
    // Manejar petición AJAX
}

if ($this->isPostRequest()) {
    // Manejar petición POST
}

$clientIP = $this->getClientIP();
```

### **Sistema de Logging**

```php
// En cualquier controlador hijo
$this->log('Usuario accedió a la página', 'info');
$this->log('Error en validación', 'error');
```

### **Generación de URLs**

```php
// Generar URL para una ruta
$url = $this->generateUrl('contacto', ['id' => 123]);
// Resultado: http://dominio.com/es/contacto/?id=123

// Redireccionar a una ruta
$this->redirectToRoute('dashboard');
```

## Cómo Usar el Sistema de Middleware

### **1. Middleware Global (para todos los controladores)**

```php
// En core.php o donde inicialices la aplicación
Controllers::registerGlobalMiddleware('before', ['MiMiddleware', 'handle']);
```

### **2. Middleware Específico (en un controlador)**

```php
// En el método initialize() de cualquier controlador
$this->registerMiddleware('before', function($controller) {
    // Lógica específica para este controlador
});
```

### **3. Crear Middleware Personalizado**

```php
class MiMiddleware
{
    public static function handle($controller)
    {
        // Tu lógica aquí
        if (!$algunaCondicion) {
            header('Location: /error');
            exit;
        }
    }
}
```

## Funcionalidades del Middleware Incluido

- **Seguridad**: Headers de seguridad, rate limiting, sanitización
- **Logging**: Registro completo de peticiones y respuestas
- **Autenticación**: Verificación automática para el panel de admin

## Mejoras Implementadas en AdminajaxController

### 🔒 **Seguridad Mejorada**

- **Rate Limiting específico** para peticiones AJAX
- **Validación de acciones permitidas** con whitelist
- **Verificación de autenticación** obligatoria
- **Validación de campos requeridos** automática
- **Logging de seguridad** para acciones sospechosas


### 📊 **Funcionalidades Nuevas**

- **Búsqueda global** en todo el sistema
- **Estadísticas en tiempo real**
- **Subida de archivos** con validación
- **Gestión completa de CRUD** para todas las entidades
- **Modales dinámicos** con contenido específico


### 🎯 **Organización Mejorada**

- **Métodos agrupados por funcionalidad**
- **Documentación completa** de cada método
- **Manejo de errores robusto** con try-catch
- **Respuestas JSON estandarizadas**
- **Configuración centralizada**


### 🚀 **Nuevas Acciones AJAX**

#### **Usuarios Admin**

- `ajax-create-usuario-admin` - Crear usuario
- `ajax-update-usuario-admin` - Actualizar usuario
- `ajax-delete-usuario-admin` - Eliminar usuario
- `ajax-toggle-usuario-status` - Cambiar estado


#### **Utilidades**

- `ajax-search-global` - Búsqueda global
- `ajax-get-stats` - Estadísticas del sistema
- `ajax-upload-file` - Subida de archivos
- `ajax-export-data` - Exportar datos
- `ajax-import-data` - Importar datos


#### **Mascotas Avanzado**

- `ajax-get-mascota-details` - Detalles completos
- `ajax-create-mascota` - Crear mascota
- `ajax-update-mascota` - Actualizar mascota


### 💡 **Características Destacadas**

1. **Rate Limiting Inteligente**: 60 peticiones por minuto por usuario
2. **Validación Automática**: Campos requeridos y tipos de datos
3. **Logging Completo**: Todas las acciones se registran
4. **Respuestas Consistentes**: Formato JSON estandarizado
5. **Manejo de Errores**: Try-catch en todos los métodos


### 📝 **Ejemplo de Uso desde JavaScript**

```javascript
// Búsqueda global
$.ajax({
    url: '/adminajax/ajax-search-global/',
    method: 'POST',
    data: {
        q: 'término de búsqueda',
        tipo: 'mascotas',
        limite: 10
    },
    success: function(response) {
        console.log(response.resultados);
    }
});

// Obtener estadísticas
$.ajax({
    url: '/adminajax/ajax-get-stats/',
    method: 'POST',
    data: { tipo: 'general' },
    success: function(response) {
        console.log(response.stats);
    }
});
```

## 🎨 **Render.php - Sistema de Renderizado Avanzado**

### **Nuevas Características:**

- **Sistema de Slots**: Para layouts dinámicos con `slot()`, `setSlot()`, `startSlot()`, `endSlot()`
- **Gestión de Assets**: Registro automático de CSS/JS con `addCSS()`, `addJS()`, `renderCSS()`, `renderJS()`
- **Sistema de Componentes**: `component()` y `partial()` para reutilización
- **Cache de Vistas**: Optimización de rendimiento
- **Minificación HTML**: Reducción de tamaño automática
- **Manejo de Errores Robusto**: Con logging y modo debug


### **Ejemplo de Uso:**

```php
// En el layout
Render::slot('content', 'Contenido por defecto');
Render::renderCSS(); // Renderiza todos los CSS registrados

// En las páginas
Render::startSlot('content');
echo '<h1>Mi contenido</h1>';
Render::endSlot();

// Componentes reutilizables
echo Render::component('user-card', ['user' => $userData]);
```

## 🛠️ **Tools.php - Utilidades Mejoradas**

### **Funciones de Fechas Modernizadas:**

- **`fechaRelativa()`**: "hace 2 horas", "hace 3 días"
- **`calcularDiasTranscurridos()`**: Diferencia en días
- **`validarFecha()`**: Validación robusta de fechas
- **Uso de DateTime**: Manejo moderno de fechas


### **Seguridad Mejorada:**

- **`hashPassword()`** y **`verifyPassword()`**: Hash seguro con `password_hash()`
- **`generateCSRFToken()`**: Tokens seguros
- **`sanitizeString()`**: Prevención XSS mejorada
- **`validatePassword()`**: Validación de fortaleza


### **Gestión de Archivos Avanzada:**

- **`uploadImage()`**: Subida con validación completa
- **`createThumbnail()`**: Generación de miniaturas con múltiples opciones
- **`formatBytes()`**: Formato legible de tamaños
- **Soporte para WebP**: Formato moderno de imágenes


### **Paginación Mejorada:**

- **Información de registros**: "Mostrando 1 a 10 de 100"
- **Navegación completa**: Primera, anterior, siguiente, última
- **Responsive**: Diferentes tamaños y alineaciones
- **Accesibilidad**: ARIA labels y navegación por teclado


### **API y JSON:**

- **`jsonResponse()`**: Respuestas estandarizadas
- **`validateJSON()`**: Validación robusta
- **Headers automáticos**: Content-Type y códigos HTTP


### **Sistema de Alertas:**

- **`registerAlert()`**: Alertas persistentes entre páginas
- **`getAlert()`**: Recuperación y limpieza automática
- **Múltiples tipos**: success, error, warning, info


### 📊 **Ejemplos Prácticos:**

```php
// Subida de imagen con thumbnail
$result = Tools::uploadImage('uploads/avatars/', 'avatar', 'user_123', [
    'create_thumbnail' => true,
    'thumbnail_size' => [150, 150],
    'max_size' => 5242880 // 5MB
]);

// Paginación avanzada
echo Tools::getPaginador(2, 10, 150, 'loadPage', [
    'show_info' => true,
    'size' => 'sm',
    'alignment' => 'center'
]);

// Validación de contraseña
$validation = Tools::validatePassword('MiPassword123!');
if (!$validation['valid']) {
    foreach ($validation['errors'] as $error) {
        echo Tools::alert($error, 'error');
    }
}

// Respuesta JSON estandarizada
Tools::jsonResponse(['users' => $users], 'success', 'Usuarios obtenidos correctamente');

// Fecha relativa
echo Tools::fechaRelativa('2024-01-15 10:30:00'); // "hace 2 días"
```

### 🔧 **Configuración Flexible:**

```php
// Configurar Tools
Tools::setConfig('upload_max_size', 20971520); // 20MB
Tools::setConfig('password_min_length', 10);

// Configurar Render
Render::setConfig('minify_html', true);
Render::setConfig('cache_enabled', true);
```

## **Controlador de AJAX - Mejoras Implementadas:**

### **1. Estructura y Organización**

- **Documentación completa** con PHPDoc
- **Configuración centralizada** con rate limiting y acciones permitidas
- **Organización por secciones** (pruebas, búsquedas, formularios, etc.)


### **2. Seguridad**

- **Rate limiting** para prevenir abuso
- **Validación de acciones** permitidas
- **Validación de campos** requeridos
- **Sanitización de datos** de entrada
- **Manejo seguro de archivos** subidos


### **3. Funcionalidades Nuevas**

- **Búsqueda de mascotas** y cuidadores
- **Formularios de contacto** y newsletter
- **Carga dinámica** de contenido (infinite scroll)
- **Subida de imágenes** con validación
- **Validación de formularios** en tiempo real
- **Datos de geolocalización**


### **4. Manejo de Errores**

- **Try-catch** en todas las acciones
- **Logging** de errores y actividades
- **Respuestas JSON** estandarizadas
- **Códigos HTTP** apropiados


### **5. Compatibilidad**

- **Mantiene las acciones legacy** (`ajax-test-get`, `ajax-test-post`)
- **Integración** con las clases existentes (Mascotas, Cuidador)
- **Uso del sistema** de renderizado existente


### **6. Características Técnicas**

- **Headers apropiados** para AJAX
- **Validación de tipos** de archivo
- **Límites de tamaño** para uploads
- **Paginación** y filtrado
- **Respuestas estructuradas**

## **controlador de CRONJOBS - Mejoras Implementadas:**

### **1. Estructura y Configuración**

- **Configuración centralizada** con límites de tiempo, memoria y parámetros
- **Lista de tareas disponibles** con descripciones y frecuencias
- **Documentación completa** con PHPDoc


### **2. Funcionalidades Básicas**

- **Envío de emails** (mejorado del original)
- **Limpieza de logs** antiguos
- **Limpieza de cache** expirado
- **Limpieza de sesiones** expiradas
- **Limpieza de archivos temporales**
- **Backup de base de datos** con mysqldump
- **Verificación de salud** del sistema
- **Generación de reportes** automáticos


### **3. Seguridad y Robustez**

- **Validación de token** mejorada
- **Configuración de entorno** apropiada para cron jobs
- **Manejo de errores** con try-catch
- **Logging detallado** de todas las operaciones
- **Límites de tiempo y memoria**


### **4. Utilidades del Sistema**

- **Listado de tareas** disponibles
- **Estado del sistema** en tiempo real
- **Monitoreo de recursos** (memoria, disco, carga)
- **Limpieza automática** de backups antiguos


### **5. Características Técnicas**

- **Ejecución en lotes** para emails
- **Verificación de salud** completa del sistema
- **Formateo de bytes** legible
- **Manejo de directorios** recursivo
- **Retención configurable** de backups


### **6. Tareas Programadas Típicas**

- **Cada 5 minutos**: Envío de emails
- **Cada 15 minutos**: Verificación de salud
- **Diario**: Limpieza de logs, cache, sesiones, backup
- **Semanal**: Generación de reportes

## **controlador de API - Mejoras Implementadas:**

### **1. Estructura y Configuración**

- **Versionado de API** (v1)
- **Configuración centralizada** para rate limiting, paginación, CORS
- **Lista de endpoints disponibles** con documentación
- **Autenticación flexible** (token, API key)


### **2. Seguridad y Autenticación**

- **Rate limiting** configurable por IP
- **Autenticación por token** mejorada
- **Headers CORS** configurables
- **Validación de métodos HTTP**
- **Logging de peticiones** detallado


### **3. Endpoints Principales**

#### **Información de la API:**

- `GET /api/info` - Información general
- `GET /api/status` - Estado del sistema
- `GET /api/endpoints` - Lista de endpoints


#### **Mascotas:**

- `GET /api/mascotas` - Lista paginada
- `GET /api/mascota?id=X` - Mascota específica
- `GET /api/mascotas/search?q=query` - Búsqueda


#### **Cuidadores:**

- `GET /api/cuidadores` - Lista paginada
- `GET /api/cuidador?id=X` - Cuidador específico
- `GET /api/cuidadores/search?q=query` - Búsqueda


#### **Otros:**

- `GET /api/caracteristicas` - Características disponibles
- `GET /api/stats` - Estadísticas del sistema
- `POST /api/validate` - Validación de datos


### **4. Características Técnicas**

- **Paginación automática** con límites configurables
- **Filtros dinámicos** por parámetros GET
- **Respuestas JSON estandarizadas** con metadatos
- **Manejo de errores** robusto con códigos HTTP apropiados
- **Headers informativos** (rate limit, versión, etc.)


### **5. Funcionalidades Avanzadas**

- **Manejo de CORS** completo para peticiones cross-origin
- **Peticiones OPTIONS** (preflight) manejadas automáticamente
- **Monitoreo de recursos** (memoria, base de datos, uptime)
- **Validación de datos** extensible
- **Logging específico** para API


### **6. Integración con el Sistema**

- **Usa las clases existentes** (Mascotas, Cuidador, Caracteristicas)
- **Compatible con el sistema de autenticación** actual
- **Respeta la estructura** del framework existente
- **Fácil extensión** para nuevos endpoints


### **7. Respuestas Estandarizadas**

```json
{
  "success": true,
  "timestamp": 1640995200,
  "data": {...},
  "pagination": {...}
}
```
## **Tokens de Autenticación:**
### **1. Diferenciación por Entorno**

- **Desarrollo**: Token más corto y predecible para facilitar testing
- **Producción**: Token más largo y complejo para mayor seguridad


### **2. Estructura del Token**

- **Prefijo identificativo**: `dev_` o `prod_`
- **Nombre del proyecto**: `appet`
- **Año**: `2024`
- **Hash aleatorio**: Caracteres hexadecimales seguros


### **3. Configuración Adicional**

- **`_API_VERSION_`**: Versión de la API
- **`_API_RATE_LIMIT_`**: Límite de peticiones por hora
- **`_API_RATE_WINDOW_`**: Ventana de tiempo para rate limiting


## **Cómo Usar los Tokens:**

### **1. En Headers HTTP:**

```shellscript
# Usando Authorization Bearer
curl -H "Authorization: Bearer dev_appet_2024_7f8e9d1c2b3a4e5f6789abcdef012345" \
     https://tudominio.com/api/mascotas

# Usando X-API-Key
curl -H "X-API-Key: dev_appet_2024_7f8e9d1c2b3a4e5f6789abcdef012345" \
     https://tudominio.com/api/mascotas
```

### **2. Como Parámetro GET:**

```plaintext
https://tudominio.com/api/mascotas?token=dev_appet_2024_7f8e9d1c2b3a4e5f6789abcdef012345
```

### **3. En JavaScript/AJAX:**

```javascript
fetch('/api/mascotas', {
    headers: {
        'Authorization': 'Bearer dev_appet_2024_7f8e9d1c2b3a4e5f6789abcdef012345',
        'Content-Type': 'application/json'
    }
})
```

## **🚀 Nuevas Funcionalidades del sistema de LOG:**

### **1. Sistema de Logging Mejorado en `core.php`:**

- **Separación automática** por tipos de error
- **Archivos organizados por fecha** (AAAAMMDD)
- **Contexto detallado** con archivo, línea y función
- **Manejador personalizado** de errores PHP


### **2. Tipos de Logs Generados:**

- **`phpErrors_AAAAMMDD.log`** - Errores PHP (Warning y superiores)
- **`SQLErrors_AAAAMMDD.log`** - Errores de base de datos
- **`debug_custom_AAAAMMDD.log`** - Logs personalizados
- **`performance_AAAAMMDD.log`** - Métricas de rendimiento
- **`general_AAAAMMDD.log`** - Logs generales


### **3. Funciones de Debug Personalizadas:**

#### **`debug_log($data, $label, $filename)`**

```php
// Ejemplo de uso
debug_log($userData, 'USER_LOGIN', 'authentication');
debug_log(['query' => $sql, 'params' => $params], 'DATABASE_QUERY');
```

#### **`performance_log($operation, $startTime, $additionalData)`**

```php
// Ejemplo de uso
$start = microtime(true);
// ... operación ...
performance_log('User Registration', $start, ['user_id' => 123]);
```

### **4. DebugController Completamente Renovado:**

#### **Nuevas URLs disponibles:**

- **`/debug/logs/`** - Visor principal de logs con estadísticas
- **`/debug/log/?file=nombre.log`** - Ver contenido específico con paginación
- **`/debug/clear-logs/?type=all`** - Limpiar todos los logs
- **`/debug/clear-logs/?type=old`** - Limpiar logs antiguos (>7 días)
- **`/debug/test-log/`** - Generar logs de prueba
- **`/debug/bd/`** - Debug BD legacy (compatibilidad)


### **5. Interfaz Visual Moderna:**

- **Dashboard con estadísticas** (archivos, tamaño, errores)
- **Tarjetas por tipo de log** con colores distintivos
- **Visor de contenido** con syntax highlighting
- **Paginación automática** para archivos grandes
- **Acciones rápidas** (ver, eliminar, limpiar)


### **6. Mejoras en `Bd.php`:**

- **Logging automático** de errores SQL
- **Métricas de performance** en modo debug
- **Contexto detallado** en logs de transacciones
- **Compatibilidad** con sistema legacy


### **7. Características Avanzadas:**

- **Rotación automática** por días
- **Formateo inteligente** de tamaños de archivo
- **Detección automática** de tipos de error
- **Limpieza programada** de logs antiguos
- **Estadísticas en tiempo real**


## **📊 Ejemplos de Uso:**

### **Debug Personalizado:**

```php
// En cualquier parte del código
debug_log($_POST, 'FORM_SUBMISSION', 'forms');
debug_log($apiResponse, 'API_RESPONSE', 'external_apis');
```

### **Monitoreo de Performance:**

```php
$start = microtime(true);
$result = $heavyOperation();
performance_log('Heavy Operation', $start, ['records' => count($result)]);
```

### **Logging Automático:**

Los errores PHP y SQL se registran automáticamente sin necesidad de código adicional.

El sistema ahora proporciona **visibilidad completa** de lo que ocurre en la aplicación, con herramientas profesionales para debugging y monitoreo.