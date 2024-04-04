# Appet - Herramienta de gestión de guarderías y alojamientos para mascotas

## Changelog

El formato está basado en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

Este proyecto se adhiere al versionado semántico [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

### [Unreleased]

### [1.0.0-alpha] - 25-03-2024
##### Feature
- Instalación de Core 4.1




****************************************************************************************



# Core 4.1

Este repositorio contiene la versión estable de CORE 4.1! :smiley::smiley::smiley:

## Documentación sobre Core 4.1

Para comenzar a desarrollar con Core:

- Editar core/config.php
- Abre el navegador.

Core utiliza **MVC**:

**Ahora puedes utilizar composer**

### M => core/Funks

Crea todas las clases que quieras. Recuerda añadir la clase al archivo core/autoload.php

### V => layout/ | pages/

El layout contendrá las partes fijas de una web como por ejemplo pie y cabecera. Las páginas serán los bloques que se cargarán al navegar por las URLs del proyecto.

### C => core/Controllers

Actualmente trabajamos con los siguientes controladores:

- AdminController.php para páginas del backend.
- AjaxController.php para todas las páginas que se llamarán por Ajax
- CronController.php para tareas programadas
- DebugController.php NO TOCAR. Es para que funcione el debug.
- DefaultController.php para las páginas del frontend.
- ApiController.php para crear un webservice

Pero puedes crear todos los que quieras, para ello **no te olvides de cargarlos en Controllers.php**. 

Puedes debugear, siempre que la constante *_DEBUG_* tenga valor *true*, presionando "log" en cualquier página del proyecto.

### Uso de funciones del framework

Utiliza todas las funciones de las clases del framework (carpeta **core/App**) mediante llamadas estáticas, por ejemplo Tools::getValue('PARAMETRO')

### Tareas programadas (CRONS)

El enlace para ejecutar una tarea programada es https://**dominio.com**/cronjob/**metodo**/**cronjob_token**/

El cronjob_token se define en settings.php

### Instalador: proceso y pasos adicionales manuales

Para ejecutar el instalar solo es necesario acceder a la raíz del dominio donde estén los archivos del framework, por ejemplo https://pruebacore.com o http://localhost/miprimerframework/ . Solo hay que seguir los pasos del proceso rellenando los campos necesarios y se crearán los archivos necesarios.

Después será necesario crear alguna traducciones manualmente, ya que dependerá de cual sea el idioma por defecto del core:

| Traduction_for | Shortcode | Texto |
|----------------|-----------|-------|
| email | email-footer-automatic | Este es un email automático. Por favor no responda directamente a él. |
| email | email-footer-automatic-2 | Este email ha sido enviado automáticamente desde |
| email | email-footer-privacy | Política de privacidad |
