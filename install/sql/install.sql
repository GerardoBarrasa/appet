-- TABLA USUARIOS_ADMIN --
CREATE TABLE `usuarios_admin` (
  `id_usuario_admin` int(10) NOT NULL,
  `id_lang` int(50) NOT NULL DEFAULT 1,
  `id_perfil` int(10) NOT NULL DEFAULT 1,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `last_password_gen` int(10) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE usuarios_admin ADD PRIMARY KEY (id_usuario_admin);
ALTER TABLE usuarios_admin MODIFY id_usuario_admin int(10) NOT NULL AUTO_INCREMENT;

-- TABLA PERFILES --
CREATE TABLE `perfiles` (
  `id_perfil` int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `perfiles` (`id_perfil`, `nombre`) VALUES
(1, 'Superadmin');

-- TABLA PERMISOS --
CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permisos` (`id_permiso`, `nombre`, `descripcion`) VALUES
(1, 'ACCESS_PERMISOS', 'Acceso para gestionar los permisos y los perfiles'),
(2, 'ACCESS_USUARIOS_ADMIN', 'Acceso para gestionar los usuarios admin'),
(3, 'ACCESS_IDIOMAS', 'Acceso apartado Idiomas'),
(4, 'ACCESS_TRADUCCIONES', 'Acceso apartado Traducciones'),
(5, 'ACCESS_CONFIGURACION', 'Acceso Configuración general'),
(6, 'ACCESS_SLUGS', 'Acceso Slugs'),
(7, 'ACCESS_TEXTOS_LEGALES', 'Acceso Textos legales'),
(8, 'ACCESS_TEXTOS_EMAILS', 'Acceso Textos emails');

-- TABLA PERMISOS_PERFILES --
CREATE TABLE `permisos_perfiles` (
  `id_perfil` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `permisos_perfiles` ADD PRIMARY KEY (`id_perfil`,`id_permiso`);

INSERT INTO `permisos_perfiles` (`id_perfil`, `id_permiso`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8);

-- TABLA EMAILS_CACHE --
CREATE TABLE emails_cache (
id_email int(255) NOT NULL,
asunto varchar(250) NOT NULL,
mensaje text NOT NULL,
destinatario varchar(100) NOT NULL,
enviado int(1) NOT NULL DEFAULT 0,
error int(1) NOT NULL DEFAULT 0,
date_sent datetime NOT NULL,
date_created datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE emails_cache ADD PRIMARY KEY (id_email);
ALTER TABLE emails_cache MODIFY id_email int(255) NOT NULL AUTO_INCREMENT;

-- TABLA CONFIGURACION --
CREATE TABLE configuracion (
id_configuracion int(10) UNSIGNED NOT NULL,
nombre varchar(254) NOT NULL,
valor text,
date_modified datetime NOT NULL,
date_created datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE configuracion ADD PRIMARY KEY (id_configuracion), ADD KEY nombre (nombre);
ALTER TABLE configuracion MODIFY id_configuracion int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO `configuracion` (`id_configuracion`, `nombre`, `valor`, `date_modified`, `date_created`) VALUES 
(NULL, 'cronjob_email_cantidad', '10', NOW(), NOW()),
(NULL, 'default_language', '1', NOW(), NOW()),
(NULL, 'modo_mantenimiento', '0', NOW(), NOW());

-- TABLA IDIOMAS --
CREATE TABLE idiomas (
id int(50) NOT NULL,
nombre varchar(250) NOT NULL,
slug varchar(100) NOT NULL,
icon varchar(250) NOT NULL,
colour varchar(250) NOT NULL,
visible int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE idiomas ADD PRIMARY KEY (id);
ALTER TABLE idiomas MODIFY id int(50) NOT NULL AUTO_INCREMENT;

INSERT INTO `idiomas` (`id`, `nombre`, `slug`, `icon`, `colour`, `visible`) VALUES
(1, 'Español', 'es', 'assets/img/flags/es-flag-1594908630.png', '#B5002A', 1),
(2, 'English', 'en', 'assets/img/flags/en-flag-1594908667.png', '#3D9244', 1);

-- TABLA TRADUCCIONES --
CREATE TABLE `traducciones` (
  `id_traduccion` int(10) NOT NULL,
  `shortcode` varchar(255) NOT NULL,
  `zona` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE `traducciones` ADD PRIMARY KEY (`id_traduccion`);
ALTER TABLE `traducciones` MODIFY `id_traduccion` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `traducciones` (`id_traduccion`, `shortcode`, `zona`) VALUES
(1, 'segundo-texto', 'home'),
(2, 'email-footer-automatic', 'email'),
(3, 'email-footer-automatic-2', 'email'),
(4, 'email-footer-privacy', 'email'),
(5, 'admin-login-email', 'admin login'),
(6, 'admin-login-email-placeholder', 'admin login'),
(7, 'admin-login-password', 'admin login'),
(8, 'admin-login-password-placeholder', 'admin login'),
(9, 'admin-login-button', 'admin login'),
(10, 'admin-login-error', 'admin login'),
(11, 'admin-login-title', 'admin login'),
(12, 'admin-home-title', 'admin home'),
(13, 'admin-idiomas-title', 'admin idiomas'),
(14, 'admin-idiomas-nuevo', 'admin idiomas'),
(15, 'admin-idiomas-campo-id', 'admin idiomas'),
(16, 'admin-idiomas-campo-nombre', 'admin idiomas'),
(17, 'admin-idiomas-campo-abreviatura', 'admin idiomas'),
(18, 'admin-idiomas-campo-icono', 'admin idiomas'),
(19, 'admin-idiomas-campo-color', 'admin idiomas'),
(20, 'admin-idiomas-campo-activo', 'admin idiomas'),
(21, 'admin-listado-acciones', 'admin listados'),
(22, 'admin-listado-editar', 'admin listados'),
(23, 'admin-listado-vacio', 'admin listados'),
(24, 'admin-listado-idiomas-cantidad', 'admin idiomas'),
(25, 'admin-idioma-add-ok', 'admin idiomas'),
(26, 'admin-idioma-update-ok', 'admin idiomas'),
(27, 'admin-idioma-title-nuevo', 'admin idiomas'),
(28, 'admin-idioma-title', 'admin idiomas'),
(29, 'admin-idiomas-campo-nombre-placeholder', 'admin idiomas'),
(30, 'admin-idiomas-campo-abreviatura-placeholder', 'admin idiomas'),
(31, 'admin-idiomas-campo-color-placeholder', 'admin idiomas'),
(32, 'admin-si', 'admin'),
(33, 'admin-no', 'admin'),
(34, 'admin-idiomas-campo-icono-extra', 'admin idiomas'),
(35, 'admin-actualizar', 'admin'),
(36, 'admin-crear', 'admin'),
(37, 'admin-traducciones-title', 'admin traducciones'),
(38, 'admin-traducciones-nuevo', 'admin traducciones'),
(39, 'admin-traducciones-regenerar-cache', 'admin traducciones'),
(40, 'admin-search-field', 'admin'),
(41, 'admin-traducciones-campo-estado', 'admin traducciones'),
(42, 'admin-traducciones-campo-estado-todas', 'admin traducciones'),
(43, 'admin-traducciones-campo-estado-pendientes', 'admin traducciones'),
(44, 'admin-traducciones-campo-idioma', 'admin traducciones'),
(45, 'admin-traducciones-campo-idioma-todos', 'admin traducciones'),
(46, 'admin-textos-emails-nuevo', 'admin traducciones'),
(47, 'admin-traducciones-campo-idioma-extra', 'admin traducciones'),
(48, 'admin-traducciones-campo-shortcode', 'admin traducciones'),
(49, 'admin-traducciones-campo-shortcode-placeholder', 'admin traducciones'),
(50, 'admin-traducciones-campo-texto', 'admin traducciones'),
(51, 'admin-traducciones-campo-texto-placeholder', 'admin traducciones'),
(52, 'admin-traducciones-porcentaje', 'admin traducciones'),
(53, 'admin-search-field-placeholder', 'admin'),
(54, 'admin-traduccion-update-ok', 'admin traducciones'),
(55, 'admin-traduccion-add-ok', 'admin traducciones'),
(56, 'admin-traduccion-error-shortcode', 'admin traducciones'),
(57, 'admin-traduccion-title', 'admin traducciones'),
(58, 'admin-traducciones-regenerar-cache-ok', 'admin traducciones'),
(59, 'admin-slugs-title', 'admin slugs'),
(60, 'admin-slugs-filtro-paginas', 'admin slugs'),
(61, 'admin-slugs-filtro-idioma', 'admin slugs'),
(62, 'admin-slugs-explicacion', 'admin slugs'),
(63, 'admin-slugs-campo-slug', 'admin slugs'),
(64, 'admin-slugs-campo-title', 'admin slugs'),
(65, 'admin-slugs-campo-description', 'admin slugs'),
(66, 'admin-slug-title', 'admin slugs'),
(67, 'admin-slugs-campo-pagina', 'admin slugs'),
(68, 'admin-slugs-elige-pagina', 'admin slugs'),
(69, 'admin-slugs-campo-idioma', 'admin slugs'),
(70, 'admin-slugs-elige-idioma', 'admin slugs'),
(71, 'admin-slug-update-ok', 'admin slugs'),
(72, 'admin-slug-update-ko-pagina-usada-idioma', 'admin slugs'),
(73, 'admin-slug-update-ko-slug-usado', 'admin slugs'),
(74, 'admin-slug-update-ko-title-vacio', 'admin slugs'),
(75, 'admin-slug-update-ko-idioma-vacio', 'admin slugs'),
(76, 'admin-slug-update-ko-pagina-vacia', 'admin slugs'),
(77, 'admin-slug-update-ko-slug-vacio', 'admin slugs'),
(78, 'admin-listado-traducciones-cantidad', 'admin traducciones'),
(79, 'admin-usuarios-admin-title', 'admin usuarios'),
(80, 'admin-usuarios-admin-nuevo', 'admin usuarios'),
(81, 'admin-usuarios-admin-campo-nombre', 'admin usuarios'),
(82, 'admin-usuarios-admin-campo-email', 'admin usuarios'),
(83, 'admin-usuarios-admin-campo-desde', 'admin usuarios'),
(84, 'admin-eliminar', 'admin'),
(85, 'admin-listado-usuarios-admin-cantidad', 'admin usuarios'),
(86, 'admin-usuario-admin-title', 'admin usuarios'),
(87, 'admin-usuario-admin-fecha-registro', 'admin usuarios'),
(88, 'admin-usuarios-admin-campo-password', 'admin usuarios'),
(89, 'admin-usuario-admin-update-ok', 'admin usuarios'),
(90, 'admin-usuario-admin-title-nuevo', 'admin usuarios'),
(91, 'admin-usuario-admin-add-ok', 'admin usuarios'),
(92, 'admin-menu-idiomas', 'admin menu'),
(93, 'admin-menu-traducciones', 'admin menu'),
(94, 'admin-menu-configuracion', 'admin menu'),
(95, 'admin-menu-paginas', 'admin menu'),
(96, 'admin-menu-usuarios-admin', 'admin menu'),
(97, 'admin-logout', 'admin'),
(98, 'admin-eliminar-registro-ko', 'admin'),
(99, 'admin-confirmar-eliminar-title', 'admin'),
(100, 'admin-confirmar-eliminar-text', 'admin'),
(101, 'admin-confirmar-eliminar-ok-btn', 'admin'),
(102, 'admin-confirmar-eliminar-cancel-btn', 'admin'),
(103, 'admin-eliminar-ok-title', 'admin'),
(104, 'admin-eliminar-ok-text', 'admin'),
(105, 'admin-idioma-add-ko-icono', 'admin idiomas'),
(106, 'admin-idioma-add-ko-creando', 'admin idiomas'),
(107, 'admin-idioma-add-ko-abreviatura-otro-idioma', 'admin idiomas'),
(108, 'admin-idioma-add-ko-nombre-vacio', 'admin idiomas'),
(109, 'admin-idioma-add-ko-abreviatura-vacia', 'admin idiomas'),
(110, 'admin-idioma-update-ko-abreviatura-otro-idioma', 'admin idiomas'),
(111, 'admin-idioma-update-ko', 'admin idiomas'),
(112, 'admin-textos-legales-title', 'admin textos legales'),
(113, 'admin-textos-legales-politica-privacidad', 'admin textos legales'),
(114, 'admin-textos-legales-condiciones-generales', 'admin textos legales'),
(115, 'admin-textos-legales-politica-cookies', 'admin textos legales'),
(116, 'admin-menu-textos-legales', 'admin'),
(117, 'admin-menu-textos-emails', 'admin'),
(118, 'admin-textos-emails-title', 'admin textos emails'),
(119, 'admin-textos-emails-recuperar-password-admin', 'admin textos emails'),
(120, 'admin-textos-emails-asunto', 'admin textos emails'),
(121, 'admin-textos-emails-contenido', 'admin textos emails'),
(122, 'admin-textos-legales-update-ok', 'admin textos legales'),
(123, 'admin-textos-emails-update-ok', 'admin textos emails'),
(124, 'admin-textos-emails-nombre-interno', 'admin textos emails'),
(125, 'admin-textos-emails-nombre-interno-placeholder', 'admin textos emails'),
(126, 'admin-textos-emails-add-ko-nombre-incorrecto', 'admin textos emails'),
(127, 'admin-textos-emails-add-ko', 'admin textos emails'),
(128, 'admin-textos-emails-add-ok', 'admin textos emails'),
(129, 'admin-login-ask-for-password-title', 'admin login'),
(130, 'admin-login-ask-for-password-email', 'admin login'),
(131, 'admin-solicitar', 'admin'),
(132, 'admin-login-ask-for-password-ok', 'admin login'),
(133, 'admin-login-ask-for-password-ko-tiempo-espera', 'admin login'),
(134, 'admin-usuario-admin-update-ko-email-existe', 'admin usuarios'),
(135, 'admin-usuario-admin-add-ko-email-existe', 'admin usuarios'),
(136, 'admin-perfil', 'admin'),
(137, 'objeto-campo-obligatorio-ko', 'objeto'),
(138, 'objeto-campo-lang-tamano-ko', 'objeto'),
(139, 'objeto-campo-tamano-ko', 'objeto'),
(140, 'objeto-campo-invalido', 'objeto'),
(141, 'admin-configuracion-title', 'admin configuracion'),
(142, 'admin-configuracion-idioma-predeterminado', 'admin configuracion'),
(143, 'admin-guardar', 'admin'),
(144, 'admin-configuracion-save-ok', 'admin configuracion'),
(145, 'admin-configuracion-save-ko', 'admin configuracion'),
(146, 'admin-traducciones-campo-zona', 'admin traducciones'),
(147, 'admin-traducciones-campo-zona-placeholder', 'admin traducciones'),
(148, 'admin-menu-listado', 'admin'),
(149, 'admin-menu-permisos', 'admin'),
(150, 'admin-usuarios-admin-campo-perfil', 'admin usuarios'),
(151, 'admin-acceso-denegado', 'admin'),
(152, 'admin-permisos-title', 'admin permisos'),
(153, 'admin-perfiles-campo-id', 'admin permisos'),
(154, 'admin-perfiles-campo-nombre', 'admin permisos'),
(155, 'admin-listado-perfiles-cantidad', 'admin permisos'),
(156, 'admin-permiso-title', 'admin permisos'),
(157, 'admin-permiso-nombre', 'admin permisos'),
(158, 'admin-permiso-descripcion', 'admin permisos'),
(159, 'admin-permiso-update-ok', 'admin permisos'),
(160, 'admin-configuracion-modo-mantenimiento', 'admin configuracion'),
(161, 'modo-mantenimiento-title', 'mantenimiento'),
(162, 'modo-mantenimiento-texto', 'mantenimiento');

-- TABLA IDIOMAS_TRADUCCIONES --
CREATE TABLE `idiomas_traducciones` (
  `id_traduccion` int(10) NOT NULL,
  `id_lang` int(10) NOT NULL,
  `texto` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE `idiomas_traducciones` ADD PRIMARY KEY (`id_traduccion`,`id_lang`);

INSERT INTO `idiomas_traducciones` (`id_traduccion`, `id_lang`, `texto`) VALUES
(1, 1, 'Prueba de texto'),
(1, 2, 'Test Text'),
(2, 1, 'Por favor, no responda directamente. Este es un e-mail automático.'),
(2, 2, ''),
(3, 1, 'Este e-mail ha sido enviado automáticamente desde'),
(3, 2, ''),
(4, 1, 'Política de privacidad'),
(4, 2, ''),
(5, 1, 'E-mail'),
(5, 2, ''),
(6, 1, 'Introduce tu e-mail'),
(6, 2, ''),
(7, 1, 'Contraseña'),
(7, 2, ''),
(8, 1, 'Introduce tu contraseña'),
(8, 2, ''),
(9, 1, 'Entrar'),
(9, 2, ''),
(10, 1, 'Usuario y/o contraseña incorrectos'),
(10, 2, ''),
(11, 1, '¡Conéctate!'),
(11, 2, ''),
(12, 1, 'Inicio'),
(12, 2, ''),
(13, 1, 'Idiomas'),
(13, 2, ''),
(14, 1, 'Nuevo idioma'),
(14, 2, ''),
(15, 1, 'ID'),
(15, 2, ''),
(16, 1, 'Nombre'),
(16, 2, ''),
(17, 1, 'Abreviatura'),
(17, 2, ''),
(18, 1, 'Icono'),
(18, 2, ''),
(19, 1, 'Color'),
(19, 2, ''),
(20, 1, 'Activo'),
(20, 2, ''),
(21, 1, 'Acciones'),
(21, 2, ''),
(22, 1, 'Editar'),
(22, 2, ''),
(23, 1, 'No se han encontrado resultados'),
(23, 2, ''),
(24, 1, '%d idiomas encontrados'),
(24, 2, ''),
(25, 1, 'Idioma creado correctamente'),
(25, 2, ''),
(26, 1, 'Idioma actualizado correctamente'),
(26, 2, ''),
(27, 1, 'Nuevo idioma'),
(27, 2, ''),
(28, 1, 'Idioma %s'),
(28, 2, ''),
(29, 1, 'Nombre del idioma'),
(29, 2, ''),
(30, 1, 'Ejemplo en inglés: en'),
(30, 2, ''),
(31, 1, 'Color (solo uso interno)'),
(31, 2, ''),
(32, 1, 'Sí'),
(32, 2, ''),
(33, 1, 'No'),
(33, 2, ''),
(34, 1, 'Búscala %1$saquí%2$s, abajo aparecerán banderas relacionadas con el mismo diseño, descárgala a 128px.'),
(34, 2, ''),
(35, 1, 'Actualizar'),
(35, 2, ''),
(36, 1, 'Crear'),
(36, 2, ''),
(37, 1, 'Traducciones'),
(37, 2, ''),
(38, 1, 'Crear traducción'),
(38, 2, ''),
(39, 1, 'Regenerar caché'),
(39, 2, ''),
(40, 1, 'Búsqueda'),
(40, 2, ''),
(41, 1, 'Estado'),
(41, 2, ''),
(42, 1, 'Todas las traducciones'),
(42, 2, ''),
(43, 1, 'Traducciones pendientes'),
(43, 2, ''),
(44, 1, 'Idioma'),
(44, 2, ''),
(45, 1, 'Todos'),
(45, 2, ''),
(46, 1, 'Crear plantilla'),
(46, 2, ''),
(47, 1, 'Creación de la traducción en el idioma por defecto, podrás añadir el resto de traducciones de esta cadena desde el listado'),
(47, 2, ''),
(48, 1, 'Shortcode'),
(48, 2, ''),
(49, 1, 'Ejemplo: header-usuario-logout'),
(49, 2, ''),
(50, 1, 'Texto'),
(50, 2, ''),
(51, 1, 'Especifica el texto para este idioma'),
(51, 2, ''),
(52, 1, 'Traducción al %d'),
(52, 2, ''),
(53, 1, 'Busca un contenido...'),
(53, 2, ''),
(54, 1, 'Traducción actualizada correctamente'),
(54, 2, ''),
(55, 1, 'Traducción creada correctamente'),
(55, 2, ''),
(56, 1, 'Shortcode vacío o ya existe'),
(56, 2, ''),
(57, 1, 'Editando traducción'),
(57, 2, ''),
(58, 1, 'Caché de traducciones regenerada correctamente'),
(58, 2, ''),
(59, 1, 'Páginas y SEO'),
(59, 2, ''),
(60, 1, 'Todas las páginas'),
(60, 2, ''),
(61, 1, 'Idiomas'),
(61, 2, ''),
(62, 1, 'Administra los diferentes slugs (urls disponibles) de %1$s. Por defecto sólo salen los slugs en el idioma %2$s. Pero usando los filtros se pueden ver los slugs en los diversos idiomas para su edición.'),
(62, 2, ''),
(63, 1, 'Slug'),
(63, 2, ''),
(64, 1, 'Title'),
(64, 2, ''),
(65, 1, 'Description'),
(65, 2, ''),
(66, 1, 'Actualizar página: %s'),
(66, 2, ''),
(67, 1, 'Página'),
(67, 2, ''),
(68, 1, 'Selecciona página'),
(68, 2, ''),
(69, 1, 'Idioma'),
(69, 2, ''),
(70, 1, 'Selecciona idioma'),
(70, 2, ''),
(71, 1, 'Página actualizada correctamente'),
(71, 2, ''),
(72, 1, 'La página seleccionada %s ya está siendo usado para este idioma'),
(72, 2, ''),
(73, 1, 'El slug indicado %s ya está siendo usado y no puede ser usado'),
(73, 2, ''),
(74, 1, 'Indica el title del slug, este aparecerá en la pestaña del navegador y ayuda a nivel SEO'),
(74, 2, ''),
(75, 1, 'Debes seleccionar el idioma al que pertenece este slug'),
(75, 2, ''),
(76, 1, 'Selecciona la página a la que pertenece el slug'),
(76, 2, ''),
(77, 1, 'Debes indicar el slug'),
(77, 2, ''),
(78, 1, '%d traducciones encontradas'),
(78, 2, ''),
(79, 1, 'Usuarios admin'),
(79, 2, ''),
(80, 1, 'Nuevo usuario'),
(80, 2, ''),
(81, 1, 'Nombre'),
(81, 2, ''),
(82, 1, 'E-mail'),
(82, 2, ''),
(83, 1, 'Desde'),
(83, 2, ''),
(84, 1, 'Eliminar'),
(84, 2, ''),
(85, 1, '%d usuarios encontrados'),
(85, 2, ''),
(86, 1, 'Ficha de %s'),
(86, 2, ''),
(87, 1, 'Fecha de creación:'),
(87, 2, ''),
(88, 1, 'Contraseña'),
(88, 2, ''),
(89, 1, 'El usuario ha sido modificado correctamente'),
(89, 2, ''),
(90, 1, 'Nuevo usuario admin'),
(90, 2, ''),
(91, 1, 'El usuario ha sido creado correctamente'),
(91, 2, ''),
(92, 1, 'Idiomas'),
(92, 2, ''),
(93, 1, 'Traducciones'),
(93, 2, ''),
(94, 1, 'Configuración'),
(94, 2, ''),
(95, 1, 'Páginas'),
(95, 2, ''),
(96, 1, 'Usuarios admin'),
(96, 2, ''),
(97, 1, 'Cerrar sesión'),
(97, 2, ''),
(98, 1, 'No se ha podido eliminar el registro'),
(98, 2, ''),
(99, 1, '¿Seguro que deseas continuar?'),
(99, 2, ''),
(100, 1, 'Esta acción es irreversible'),
(100, 2, ''),
(101, 1, 'Sí, continuar'),
(101, 2, ''),
(102, 1, 'Cancelar'),
(102, 2, ''),
(103, 1, '¡Hecho!'),
(103, 2, ''),
(104, 1, 'Es historia'),
(104, 2, ''),
(105, 1, 'Debes seleccionar una imagen válida'),
(105, 2, ''),
(106, 1, 'Ha habido un error creando el idioma'),
(106, 2, ''),
(107, 1, 'La abreviatura que indicas ya está siendo usada en el idioma %s'),
(107, 2, ''),
(108, 1, 'Debes indicar el nombre del idioma'),
(108, 2, ''),
(109, 1, 'Debes indicar la abreviatura'),
(109, 2, ''),
(110, 1, 'La abreviatura ya existe en otro idioma'),
(110, 2, ''),
(111, 1, 'Ha habido un error al actualizar el idioma'),
(111, 2, ''),
(112, 1, 'Textos legales'),
(112, 2, ''),
(113, 1, 'Política de privacidad'),
(113, 2, ''),
(114, 1, 'Condiciones generales'),
(114, 2, ''),
(115, 1, 'Política de cookies'),
(115, 2, ''),
(116, 1, 'Textos legales'),
(116, 2, ''),
(117, 1, 'Textos emails'),
(117, 2, ''),
(118, 1, 'Textos e-mails'),
(118, 2, ''),
(119, 1, 'Recuperar contraseña panel de administración'),
(119, 2, ''),
(120, 1, 'Asunto'),
(120, 2, ''),
(121, 1, 'Contenido'),
(121, 2, ''),
(122, 1, 'Textos legales actualizados correctamente'),
(122, 2, ''),
(123, 1, 'Textos de emails actualizados correctamente'),
(123, 2, ''),
(124, 1, 'Nombre interno'),
(124, 2, ''),
(125, 1, 'Ejemplo: recuperar-password-admin'),
(125, 2, ''),
(126, 1, 'El nombre interno ya existe o no es válido'),
(126, 2, ''),
(127, 1, 'Ha habido un error creando la nueva plantilla de email'),
(127, 2, ''),
(128, 1, 'Plantilla de email creada correctamente'),
(128, 2, ''),
(129, 1, '¿Has olvidado tu contraseña?'),
(129, 2, ''),
(130, 1, 'E-mail'),
(130, 2, ''),
(131, 1, 'Solicitar'),
(131, 2, ''),
(132, 1, 'Si tu cuenta es válida recibirás un email con tu nueva contraseña. Recuerda cambiarla cuando inicies sesión.'),
(132, 2, ''),
(133, 1, 'Ya has solicitado una nueva contraseña recientemente, espera un rato e inténtalo de nuevo'),
(133, 2, ''),
(134, 1, 'Ya existe otra cuenta con el correo electrónico indicado'),
(134, 2, ''),
(135, 1, 'Ya existe otra cuenta con el correo electrónico indicado'),
(135, 2, ''),
(136, 1, 'Perfil'),
(136, 2, ''),
(137, 1, 'El campo %s es obligatorio.'),
(137, 2, ''),
(138, 1, 'El valor del campo %1$s (idioma %2$s) excede el tamaño máximo permitido de %3$s.'),
(138, 2, ''),
(139, 1, 'El campo %1$s es demasiado largo. Máximo %2$s caracteres.'),
(139, 2, ''),
(140, 1, 'El campo %s es inválido.'),
(140, 2, ''),
(141, 1, 'Configuración'),
(141, 2, ''),
(142, 1, 'Idioma predeterminado'),
(142, 2, ''),
(143, 1, 'Guardar'),
(143, 2, ''),
(144, 1, 'La configuración se ha actualizado correctamente'),
(144, 2, ''),
(145, 1, 'Ha habido un error al actualizar la configuración'),
(145, 2, ''),
(146, 1, 'Zona'),
(146, 2, ''),
(147, 1, 'Ejemplo: admin traducciones'),
(147, 2, ''),
(148, 1, 'Listado'),
(148, 2, ''),
(149, 1, 'Permisos'),
(149, 2, ''),
(150, 1, 'Perfil'),
(150, 2, ''),
(151, 1, 'Acceso denegado'),
(151, 2, 'Access denied'),
(152, 1, 'Permisos'),
(152, 2, ''),
(153, 1, 'ID'),
(153, 2, ''),
(154, 1, 'Nombre'),
(154, 2, ''),
(155, 1, '%d perfiles encontrados'),
(155, 2, ''),
(156, 1, 'Permisos %s'),
(156, 2, ''),
(157, 1, 'Nombre'),
(157, 2, ''),
(158, 1, 'Descripción'),
(158, 2, ''),
(159, 1, 'Permisos de perfil actualizados correctamente'),
(159, 2, ''),
(160, 1, 'Web en mantenimiento'),
(160, 2, ''),
(161, 1, '¡Volvemos pronto!'),
(161, 2, ''),
(162, 1, 'Sitio web en mantenimiento'),
(162, 2, '');

-- TABLA SLUGS --
CREATE TABLE `slugs` (
  `id` int(250) NOT NULL,
  `id_language` int(50) NOT NULL,
  `slug` varchar(250) NOT NULL,
  `mod_id` varchar(250) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `keywords` varchar(250) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `visible` int(1) NOT NULL,
  `creation_date` datetime NOT NULL,
  `update_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE `slugs` ADD PRIMARY KEY (`id`);
ALTER TABLE `slugs` MODIFY `id` int(250) NOT NULL AUTO_INCREMENT;

INSERT INTO `slugs` (`id`, `id_language`, `slug`, `mod_id`, `title`, `description`, `keywords`, `status`, `visible`, `creation_date`, `update_date`) VALUES
(1, 1, 'home', 'home', 'Bienvenido a CORE!', '', '', 'active', 1, '2021-09-22 16:42:41', '2024-04-10 14:06:40'),
(2, 1, 'politica-privacidad', 'politica-privacidad', 'Política de privacidad', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 21:29:40'),
(3, 1, 'condiciones-generales', 'condiciones-generales', 'Condiciones generales', '', '', 'active', 1, '2021-09-22 16:42:41', '2021-11-23 19:54:08'),
(4, 1, 'politica-cookies', 'politica-cookies', 'Política de cookies', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 21:29:47'),
(5, 2, 'home', 'home', 'Home', '', '', 'active', 1, '2021-09-22 16:42:41', '2023-03-22 19:34:58'),
(6, 2, 'privacy-policy', 'politica-privacidad', 'Policy privacy', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 19:44:25'),
(7, 2, 'general-conditions', 'condiciones-generales', 'General conditions', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 19:44:32'),
(8, 2, 'cookies-policy', 'politica-cookies', 'Cookies policy', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 19:44:36'),
(9, 1, '404', '404', 'Página no encontrada', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 21:29:47'),
(10, 2, '404', '404', 'Not Found', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 19:44:36'),
(11, 1, 'prueba', 'test', 'PÁGINA TEST', '', '', 'active', 1, '2021-09-22 16:42:41', '2024-04-02 18:33:14'),
(12, 2, 'test', 'test', 'TEST PAGE', '', '', 'active', 1, '2021-09-22 16:42:41', '2023-03-22 19:34:58');

-- TABLA TEXTOS_LEGALES --
CREATE TABLE `textos_legales` (
  `id_texto_legal` int(10) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `textos_legales` ADD PRIMARY KEY (`id_texto_legal`);
ALTER TABLE `textos_legales` MODIFY `id_texto_legal` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `textos_legales` (`id_texto_legal`, `nombre`) VALUES
(1, 'politica-privacidad'),
(2, 'condiciones-generales'),
(3, 'politica-cookies');

-- TABLA TEXTOS_LEGALES_IDIOMAS --
CREATE TABLE `textos_legales_idiomas` (
  `id_texto_legal` int(10) NOT NULL,
  `id_lang` int(50) NOT NULL,
  `contenido` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `textos_legales_idiomas` ADD PRIMARY KEY (`id_texto_legal`,`id_lang`);

INSERT INTO `textos_legales_idiomas` (`id_texto_legal`, `id_lang`, `contenido`) VALUES
(1, 1, '<h1>Pol&iacute;tica de privacidad</h1>'),
(1, 2, '<h1>Privacy policy</h1>'),
(2, 1, '<h1>Condiciones generales</h1>'),
(2, 2, '<h1>General conditions</h1>'),
(3, 1, '<h1>Pol&iacute;tica de cookies</h1>'),
(3, 2, '<h1>Cookies policy</h1>');

-- TABLA TEXTOS_IDIOMAS --
CREATE TABLE `textos_emails` (
  `id_texto_email` int(10) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `textos_emails` ADD PRIMARY KEY (`id_texto_email`);
ALTER TABLE `textos_emails` MODIFY `id_texto_email` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `textos_emails` (`id_texto_email`, `nombre`) VALUES
(1, 'recuperar-password-admin');

-- TABLA TEXTOS_EMAILS_IDIOMAS --
CREATE TABLE `textos_emails_idiomas` (
  `id_texto_email` int(10) NOT NULL,
  `id_lang` int(50) NOT NULL,
  `asunto` varchar(250) NOT NULL,
  `contenido` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `textos_emails_idiomas` ADD PRIMARY KEY (`id_texto_email`,`id_lang`);

INSERT INTO `textos_emails_idiomas` (`id_texto_email`, `id_lang`, `asunto`, `contenido`) VALUES
(1, 1, 'Nueva contraseña solicitada', '<p>Hola %nombre%,</p>\r\n<p>Esta es tu nueva contrase&ntilde;a: %password%</p>'),
(1, 2, '', '');

-- TABLA CLIENTETEST --
CREATE TABLE `clientetest` (
  `id_clientetest` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `id_gender` tinyint(1) NOT NULL DEFAULT 0,
  `birthday` date DEFAULT NULL,
  `newsletter` tinyint(1) NOT NULL DEFAULT 0,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `clientetest` ADD PRIMARY KEY (`id_clientetest`);
ALTER TABLE `clientetest` MODIFY `id_clientetest` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

INSERT INTO `clientetest` (`id_clientetest`, `name`, `id_gender`, `birthday`, `newsletter`, `note`) VALUES
(1, 'Nuevo Nombre ALAMLOPU', 0, '0000-00-00', 0, NULL);

-- TABLA CLIENTETEST_LANG --

CREATE TABLE `clientetest_lang` (
  `id_clientetest` int(10) NOT NULL,
  `id_lang` int(50) NOT NULL,
  `test_lang_field` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `clientetest_lang` ADD PRIMARY KEY (`id_clientetest`,`id_lang`);

INSERT INTO `clientetest_lang` (`id_clientetest`, `id_lang`, `test_lang_field`) VALUES
(1, 1, 'texto en ESP 1713436321'),
(1, 2, 'texto en EN 1713436321');