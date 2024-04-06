-- TABLA USUARIOS_ADMIN --
CREATE TABLE usuarios_admin (
id int(10) NOT NULL,
nombre varchar(100) NOT NULL,
email varchar(100) NOT NULL,
password varchar(250) NOT NULL,
date_created datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE usuarios_admin ADD PRIMARY KEY (id);
ALTER TABLE usuarios_admin MODIFY id int(10) NOT NULL AUTO_INCREMENT;

-- TABLA EMAILS_CACHE --
CREATE TABLE emails_cache (
id int(255) NOT NULL,
asunto varchar(250) NOT NULL,
mensaje text NOT NULL,
destinatario varchar(100) NOT NULL,
enviado int(1) NOT NULL DEFAULT 0,
error int(1) NOT NULL DEFAULT 0,
date_sent datetime NOT NULL,
date_created datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE emails_cache ADD PRIMARY KEY (id);
ALTER TABLE emails_cache MODIFY id int(255) NOT NULL AUTO_INCREMENT;

-- TABLA CONFIGURACION --
CREATE TABLE configuracion (
id int(10) UNSIGNED NOT NULL,
nombre varchar(254) NOT NULL,
valor text,
date_modified datetime NOT NULL,
date_created datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE configuracion ADD PRIMARY KEY (id), ADD KEY nombre (nombre);
ALTER TABLE configuracion MODIFY id int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO `configuracion` (`id`, `nombre`, `valor`, `date_modified`, `date_created`) VALUES (NULL, 'cronjob_email_cantidad', '10', NOW(), NOW());

-- TABLA IDIOMAS --
CREATE TABLE idiomas (
id int(50) NOT NULL,
nombre varchar(250) NOT NULL,
slug varchar(100) NOT NULL,
icon varchar(250) NOT NULL,
colour varchar(250) NOT NULL,
isDefault int(1) NOT NULL DEFAULT '0',
visible int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE idiomas ADD PRIMARY KEY (id);
ALTER TABLE idiomas MODIFY id int(50) NOT NULL AUTO_INCREMENT;

INSERT INTO `idiomas` (`id`, `nombre`, `slug`, `icon`, `colour`, `isDefault`, `visible`) VALUES
(1, 'Español', 'es', 'assets/img/flags/es-flag-1594908630.png', '#B5002A', 1, 1),
(2, 'Portugués', 'pt', 'assets/img/flags/pt-flag-1594908667.png', '#3D9244', 0, 1);

-- TABLA IDIOMAS_TRADUCCIONES --
CREATE TABLE idiomas_traducciones (
id int(50) NOT NULL,
id_idioma int(50) NOT NULL,
traduction_for varchar(250) NOT NULL COMMENT '(URL de la traduccion)',
shortcode varchar(250) NOT NULL,
contenido text NOT NULL,
fecha_creation date NOT NULL,
fecha_update date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE idiomas_traducciones ADD PRIMARY KEY (id);
ALTER TABLE idiomas_traducciones MODIFY id int(50) NOT NULL AUTO_INCREMENT;

INSERT INTO `idiomas_traducciones` (`id_idioma`, `traduction_for`, `shortcode`, `contenido`, `fecha_creation`, `fecha_update`) VALUES
('1', 'home', 'segundo-texto', 'Texto de prueba', '2023-07-12', '2023-07-12'),
('2', 'home', 'segundo-texto', 'Test text', '2023-07-12', '2023-07-12');

-- TABLA SLUGS --
CREATE TABLE `slugs` (
  `id` int(250) NOT NULL,
  `id_language` int(50) NOT NULL,
  `slug` varchar(250) NOT NULL,
  `mod_id` varchar(250) NOT NULL,
  `page` varchar(250) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `keywords` varchar(250) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `visible` int(1) NOT NULL,
  `creation_date` datetime NOT NULL,
  `update_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `slugs` ADD PRIMARY KEY (`id`);
ALTER TABLE `slugs` MODIFY `id` int(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

INSERT INTO `slugs` (`id`, `id_language`, `slug`, `mod_id`, `page`, `title`, `description`, `keywords`, `status`, `visible`, `creation_date`, `update_date`) VALUES
(1, 1, 'home', 'home', 'home', 'Home', '', '', 'active', 1, '2021-09-22 16:42:41', '2023-03-22 19:35:08'),
(2, 1, 'politica-privacidad', 'politica-privacidad', 'politica-privacidad', 'Política de privacidad', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 21:29:40'),
(3, 1, 'condiciones-generales', 'condiciones-generales', 'condiciones-generales', 'Condiciones generales', '', '', 'active', 1, '2021-09-22 16:42:41', '2021-11-23 19:54:08'),
(4, 1, 'politica-cookies', 'politica-cookies', 'politica-cookies', 'Política de cookies', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 21:29:47'),
(5, 2, 'home', 'home', 'home', 'Home', '', '', 'active', 1, '2021-09-22 16:42:41', '2023-03-22 19:34:58'),
(6, 2, 'privacy-policy', 'politica-privacidad', 'politica-privacidad', 'Policy privacy', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 19:44:25'),
(7, 2, 'general-conditions', 'condiciones-generales', 'condiciones-generales', 'General conditions', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 19:44:32'),
(8, 2, 'cookies-policy', 'politica-cookies', 'politica-cookies', 'Cookies policy', '', '', 'active', 1, '2021-09-22 16:42:41', '2022-06-01 19:44:36');