# Core 4.3

Este repositorio contiene la versión estable de CORE 4.3! :smiley::smiley::smiley:

## Documentación sobre Core 4.3

Para comenzar a desarrollar con Core:

- Editar core/config.php
- Abre el navegador.

Core utiliza **MVC**:

**Ahora puedes utilizar composer**

### M => core/Funks

Crea todas las clases que quieras. Recuerda añadir la clase al archivo core/class_index.php

### V => layout/ | pages/

El layout contendrá las partes fijas de una web como por ejemplo pie y cabecera. Las páginas serán los bloques que se cargarán al navegar por las URLs del proyecto.

### C => core/Controllers

Actualmente trabajamos con los siguientes controladores:

- AdminController.php para páginas del backend.
- AjaxController.php para todas las páginas que se llamarán por Ajax desde la parte pública
- AdminajaxController.php para todas las páginas que se llamarán por Ajax desde el panel de control
- CronController.php para tareas programadas
- DebugController.php NO TOCAR. Es para que funcione el debug.
- DefaultController.php para las páginas del frontend.
- ApiController.php para crear un webservice

Pero puedes crear todos los que quieras, para ello **no te olvides de incluirlos en core/class_index.php**. 

Puedes debugear, siempre que la constante *_DEBUG_* tenga valor *true*, tecleando "log" en cualquier página del proyecto.

### Uso de funciones del framework

Utiliza todas las funciones de las clases del framework (carpeta **core/App**) mediante llamadas estáticas, por ejemplo Tools::getValue('PARAMETRO')

### ObjectModel

Entre las clases del Core se encuentra ObjectModel, que permite crear objetos diferentes a partir de una clase que extienda ObjectModel. La principal ventaja es que una vez creadas las tablas en la base de datos y definida toda la clase es posible crear, actualizar y eliminar entidades mediante el uso de funciones. Además se pueden gestionar automáticamente los campos que impliquen el uso de varios idiomas.

```
$cliente = new ClienteTest(1);
$old_cliente = clone $cliente;
$cliente->name = 'Nuevo Nombre '.Tools::passwdGen(8, 'NO_NUMERIC');
$cliente->test_lang_field = array(1 => 'texto en ESP '.time(), 2 => 'texto en EN '.time());
```

Guardar
``$cliente->save();``

Crear
```
$cliente->id = 288;
$cliente->force_id = true;
$cliente->add();
```

Eliminar
``$cliente->delete();`` 

Crear traducciones a partir de los campos de la clase, incluidos los placeholder
``ClienteTest::generarTraduccionesCampos(true, array('id_gender', 'newsletter'));``

### Tareas programadas (CRONS)

El enlace para ejecutar una tarea programada es https://**dominio.com**/cronjob/**metodo**/**cronjob_token**/

El cronjob_token se define en settings.php

### Instalador: proceso y pasos adicionales manuales

Para ejecutar el instalador solo es necesario acceder a la raíz del dominio donde estén los archivos del framework, por ejemplo https://pruebacore.com o http://localhost/miprimerframework/ . Solo hay que seguir los pasos del proceso rellenando los campos necesarios y se crearán los archivos necesarios.
