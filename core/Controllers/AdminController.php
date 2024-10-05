<?php

class AdminController extends Controllers
{
	var $comienzo = 0;
	var $limite   = 10;
	var $pagina   = 1;

	public function execute($page)
	{
		if( (!isset($_SESSION['admin_panel']) && $page != '') ){
			header('HTTP/1.1 403 Forbidden');
			exit;
		}

		Render::$layout = 'back-end';

		Tools::registerStylesheet(_ASSETS_._ADMIN_.'bootstrap.min.css');
		Tools::registerStylesheet(_ASSETS_._ADMIN_.'metismenu.min.css');
		Tools::registerStylesheet(_ASSETS_._ADMIN_.'icons.css');
		Tools::registerStylesheet(_ASSETS_._ADMIN_.'style.css');
		Tools::registerStylesheet(_ASSETS_._ADMIN_.'sweetalert2.min.css');
		Tools::registerStylesheet(_ASSETS_._ADMIN_.'custom.css');

		Tools::registerJavascript(_ASSETS_.'jquery/jquery.min.js', 'top');
		Tools::registerJavascript(_ASSETS_._ADMIN_.'custom.js', 'top');
		Tools::registerJavascript(_ASSETS_._ADMIN_.'bootstrap.bundle.min.js', 'bottom');
		Tools::registerJavascript(_ASSETS_._ADMIN_.'metismenu.min.js', 'bottom');
		Tools::registerJavascript(_ASSETS_._ADMIN_.'jquery.slimscroll.js', 'bottom');
		Tools::registerJavascript(_ASSETS_._ADMIN_.'waves.min.js', 'bottom');
		Tools::registerJavascript(_ASSETS_._ADMIN_.'sweetalert2.all.min.js', 'bottom');
		Tools::registerJavascript(_ASSETS_._ADMIN_.'app.js', 'bottom');

		Render::$layout_data = array(
			'idiomas' => Idiomas::getLanguagesAdminForm()
		);

		if( !empty($_SESSION['admin_panel']) )
			$_SESSION['admin_panel'] = Admin::getUsuarioById($_SESSION['admin_panel']->id_usuario_admin);

		//Inicio
		$this->add('',function()
		{
			//Comprobamos si existe la sesion de admin
			if( !isset($_SESSION['admin_panel']) )
			{
				//Mensaje de error defecto
				$mensajeError = '';
				$mensajeSuccess = '';

				if( Tools::getIsset('submitAskForNewPassword') )
				{
					$email = Tools::getValue('password_email');
					$usuario_admin = Admin::getUsuarioByEmail($email);
					if( !empty($usuario_admin) )
					{
						if( empty($usuario_admin->last_password_gen) || ((time() - 600) > $usuario_admin->last_password_gen) )
						{
							$new_password = Tools::passwdGen(12);
							Bd::getInstance()->update('usuarios_admin', array('password' => Tools::md5($new_password), 'last_password_gen' => time()), 'id_usuario_admin = '.(int)$usuario_admin->id_usuario_admin);
							Sendmail::sendTemplate('recuperar-password-admin', $usuario_admin->id_lang, $email, array('%nombre%' => $usuario_admin->nombre, '%password%' => $new_password), '');
						}
						else
							$mensajeError = l('admin-login-ask-for-password-ko-tiempo-espera');
					}
					if( empty($mensajeError) )
						$mensajeSuccess = l('admin-login-ask-for-password-ok');
				}

				//Comprobamos datos de acceso
				if( isset($_REQUEST['btn-login']) )
				{
					//Obtenemos valores del login
					$usuario 	= Tools::getValue('usuario');
					$password	= Tools::md5(Tools::getValue('password'));

					if(Admin::login($usuario, $password))
						Tools::redirect(_ADMIN_);
					else
						$mensajeError = l('admin-login-error');
				}

				//Guardamos variables para enviar a la pagina
				$data = array(
					'mensajeError' => $mensajeError,
					'mensajeSuccess' => $mensajeSuccess
				);

				//Metas Config
				Metas::$title = l('admin-login-title');

				//Renderizamos pagina admin
				Render::showAdminPage('login', $data);
			}
			else
			{
				Metas::$title = l('admin-home-title');
				Render::adminPage('home');
			}
		});

		// =================================
		//  Idiomas
		// =================================
		$this->add('idiomas',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_IDIOMAS', true);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'footable/footable.bootstrap.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'footable/footable.min.js');
			
			$data = array(
				'comienzo' => $this->comienzo,
				'pagina'   => $this->pagina,
				'limite'   => $this->limite
			);

			Metas::$title = l('admin-idiomas-title');
			Render::adminPage('idiomas', $data);
		});

		// PAGE - Administrar el idioma
		$this->add('administrar-idioma',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_IDIOMAS', true);

			//Obtenemos el data
			if( isset($_REQUEST['data']) )
				$id = $_REQUEST['data'];
			else
				Tools::redirect(_ADMIN_.'idiomas/');

			$datos_idioma = false;

			if( Tools::getIsset('submitUpdateIdioma') )
			{
				$nombre = Tools::getValue('nombre');
				$slug = Tools::getValue('slug');
				if( !empty($nombre) )
				{
					if( !empty($slug) )
					{
						$datosLang = Bd::getInstance()->fetchObject('SELECT * FROM idiomas WHERE slug = "'.$slug.'" AND id != '.(int)$id);

						if( empty($datosLang) && count($datosLang) == '0' )
						{
							if( Idiomas::actualizarIdioma() )
								Tools::registerAlert(l('admin-idioma-update-ok'), 'success');
							else
								Tools::registerAlert(l('admin-idioma-update-ko'), 'error');
						}
						else
							Tools::registerAlert(l('admin-idioma-update-ko-abreviatura-otro-idioma', array($datosLang[0]->nombre)), 'error');
					}
					else
						Tools::registerAlert(l('admin-idioma-add-ko-abreviatura-vacia'), 'error');
				}
				else
					Tools::registerAlert(l('admin-idioma-add-ko-nombre-vacio'), 'error');
			}

			if( Tools::getIsset('submitCrearIdioma') )
			{
				$nombre = Tools::getValue('nombre');
				$slug = Tools::getValue('slug');
				if( !empty($nombre) )
				{
					if( !empty($slug) )
					{
						$datosLang = Bd::getInstance()->fetchObject('SELECT * FROM idiomas WHERE slug = "'.$slug.'"');

						if( empty($datosLang) && count($datosLang) == '0' )
						{
							if( isset($_FILES['icon']) && $_FILES['icon']['size'] > '0' )
							{
								$id_new_lang = Idiomas::crearIdioma();
								if( !empty($id_new_lang) )
								{
									Traducciones::generarTraduccionesVacias($id_new_lang);
									Tools::registerAlert(l('admin-idioma-add-ok'), 'success');
									Tools::redirect(_ADMIN_.'administrar-idioma/'.$id_new_lang.'/');
									die;
								}
								else
									Tools::registerAlert(l('admin-idioma-add-ko-creando'), 'error');
							}
							else
								Tools::registerAlert(l('admin-idioma-add-ko-icono'), 'error');
						}
						else
							Tools::registerAlert(l('admin-idioma-add-ko-abreviatura-otro-idioma', array($datosLang[0]->nombre)), 'error');
					}
					else
						Tools::registerAlert(l('admin-idioma-add-ko-abreviatura-vacia'), 'error');
				}
				else
					Tools::registerAlert(l('admin-idioma-add-ko-nombre-vacio'), 'error');
			}

			Metas::$title = l('admin-idioma-title-nuevo');
			if( $id !== 'new' )
			{
				$datos_idioma = Idiomas::getLanguages($id);
				Metas::$title = l('admin-idioma-title', array($datos_idioma->nombre));
			}

			$data = array(
				'id' => $id,
				'datos_idioma' => $datos_idioma,
			);

			Render::adminPage('administrar-idioma', $data);
		});

		// =================================
		//  Traducciones
		// =================================
		$this->add('traducciones',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_TRADUCCIONES', true);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'footable/footable.bootstrap.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'footable/footable.min.js');

			//Obtenemos los diferentes idiomas
			$idiomas = Idiomas::getLanguages();

			$porcentajeTraduccionesPorIdioma = array();
			foreach( $idiomas as $idioma )
				$porcentajeTraduccionesPorIdioma[$idioma->id] = Traducciones::getStatsTraduccionesByIdioma($idioma->id);

			$data = array(
				'porcentajeTraduccionesPorIdioma' => $porcentajeTraduccionesPorIdioma,
				'comienzo' => $this->comienzo,
				'pagina' => $this->pagina,
				'limite' => $this->limite,
				'zonas' => Traducciones::getZonas()
			);

			Metas::$title = l('admin-traducciones-title');
			Render::adminPage('traducciones', $data);
		});

		$this->add('traduccion',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_TRADUCCIONES', true);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'select2/css/select2.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'select2/js/select2.min.js');

			$traduccionId = Tools::getValue('data');
			$traduccion = false;

			if( Tools::getIsset('submitUpdateTraduccion') )
			{
				$traduccionId = Tools::getValue('id_traduccion');
				$shortcode = Tools::getValue('shortcode');
				$zona = Tools::getValue('zona');
				if( !empty($shortcode) && !Traducciones::checkShortcodeExists($shortcode, $traduccionId) )
				{
					$textos = Tools::getValue('texto');
					Traducciones::actualizarTraduccion($traduccionId, $shortcode, $textos, $zona);
					Tools::registerAlert(l('admin-traduccion-update-ok'), "success");
				}
				else
					Tools::registerAlert(l('admin-traduccion-error-shortcode'), "error");
			}

			if( Tools::getIsset('submitCrearTraduccion') )
			{
				$shortcode = Tools::getValue('shortcode');
				if( !empty($shortcode) && !Traducciones::checkShortcodeExists($shortcode) )
				{
					$id_lang = Tools::getValue('id_idioma');
					$texto = Tools::getValue('texto');
					$zona = Tools::getValue('zona');
					$traduccionId = Traducciones::crearTraduccion($shortcode, $id_lang, $texto, $zona);
					Tools::registerAlert(l('admin-traduccion-add-ok'), "success");
					Tools::redirect(_ADMIN_."traduccion/".(int)$traduccionId."/");
				}
				else
				{
					Tools::registerAlert(l('admin-traduccion-error-shortcode'), "error");
					Tools::redirect(_ADMIN_."traducciones/");
				}
			}
			
			Metas::$title = l('admin-traduccion-title');
			if( $traduccionId !== 'new' )
				$traduccion = Traducciones::getTraduccionById($traduccionId);

			$data = array(
				'traduccion' => $traduccion
			);

			Render::adminPage('traduccion', $data);
		});

		$this->add('regenerar-cache-traducciones',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_TRADUCCIONES', true);

			$idiomas = Idiomas::getLanguages();
			foreach( $idiomas as $idioma )
			{
				Traducciones::regenerarCacheTraduccionesByIdioma($idioma->id, _PATH_.'translations/'.$idioma->slug.'.php');
			}

			Render::$layout = false;
			Tools::registerAlert(l('admin-traducciones-regenerar-cache-ok'), "success");
			Tools::redirect(_ADMIN_."traducciones/");
		});

		// =================================
		//  Configuración -> Configuración
		// =================================
		$this->add('configuracion',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_CONFIGURACION', true);

			if( Tools::getIsset('submitUpdateConfiguracionAdmin') )
			{
				$updateRes = true;
				$updateRes &= Configuracion::updateValue('default_language', Tools::getValue('default_language'));
				$updateRes &= Configuracion::updateValue('modo_mantenimiento', (int)Tools::getValue('modo_mantenimiento', '0'));

				if( $updateRes )
					Tools::registerAlert(l('admin-configuracion-save-ok'), 'success');
				else
					Tools::registerAlert(l('admin-configuracion-save-ko'), 'error');
			}

			$data = array(
				'default_language' => Configuracion::get('default_language'),
				'modo_mantenimiento' => Configuracion::get('modo_mantenimiento', '0')
			);

			Metas::$title = l('admin-configuracion-title');
			Render::adminPage('configuracion', $data);
		});

		// =================================
		//  Configuración -> Páginas
		// =================================
		$this->add('slugs',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_SLUGS', true);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'select2/css/select2.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'select2/js/select2.min.js');

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'footable/footable.bootstrap.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'footable/footable.min.js');

			$data = array(
				'comienzo' => $this->comienzo,
				'pagina'   => $this->pagina,
				'limite'   => $this->limite,
				'languages' => Idiomas::getLanguages(),
				'slugsPages' => Slugs::getPagesFromSlugs(),
				'languageDefault' => Idiomas::getLanguages(Configuracion::get('default_language'))
			);

			Metas::$title = l('admin-slugs-title');
			Render::adminPage('slugs_admin', $data);
		});

		$this->add('administrar-slug',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_SLUGS', true);
			
			//Obtenemos el data
			if( isset($_REQUEST['data']) )
				$id = $_REQUEST['data'];
			else
				Tools::redirect(_ADMIN_."slugs/");

			$msg_error = 0;
			$datos = false;

			if( Tools::getIsset('submitUpdateSlug') )
			{
				$id 				= Tools::getValue('id');
				$slug 				= Tools::getValue('slug');
				$mod_id				= Tools::getValue('page');
				$id_language 		= Tools::getValue('id_language');
				$title 				= Tools::getValue('title');
				$description 		= Tools::getValue('description');
				$keywords 			= Tools::getValue('keywords');
				$status 			= Tools::getValue('status', 'active');
				$pageName 			= '';

				//Comprobamos datos
				if($slug != "")
				{
					if($mod_id != '')
					{
						$pageName = str_replace("-", " ", $mod_id);
						$pageName = ucfirst($pageName);

						if($id_language != '')
						{
							if($title != '')
							{
								$slug = Tools::urlAmigable($slug);

								//Comprobamos si el slug, para el idioma es único.
								if(Slugs::checkIfSlugIsAvailable($slug, $id_language, $id))
								{
									//Comprobamos si tiene ya creado para ese MOD_ID en ese idioma.
									if(Slugs::checkIfPageIsAvailableForLanguage($mod_id, $id_language, $id)){
										Tools::registerAlert(l('admin-slug-update-ok'), "success");
									}
									else{
										Tools::registerAlert(l('admin-slug-update-ko-pagina-usada-idioma', array('<strong>'.$pageName.'</strong>')));
										$msg_error++;
									}
								}
								else{
									Tools::registerAlert(l('admin-slug-update-ko-slug-usado', array('<strong>'.$pageName.'</strong>')));
									$msg_error++;
								}
							}else{
								Tools::registerAlert(l('admin-slug-update-ko-title-vacio'));
								$msg_error++;
							}
						} else{
							Tools::registerAlert(l('admin-slug-update-ko-idioma-vacio'));
							$msg_error++;
						}
					} else{
						Tools::registerAlert(l('admin-slug-update-ko-pagina-vacia'));
						$msg_error++;
					}
				}else{
					Tools::registerAlert(l('admin-slug-update-ko-slug-vacio'));
					$msg_error++;
				}

				if($msg_error == 0)
				{
					$data['id_language'] 			= $id_language;
					$data['slug'] 					= $slug;
					$data['title'] 					= $title;
					$data['description'] 			= $description;
					$data['keywords'] 				= $keywords;
					$data['status'] 				= $status;
					$data['update_date']			= Tools::datetime();

					Bd::getInstance()->update('slugs', $data, "id = '".$id."'");
				}
			}

			if( $id !== 'new' ){
				$datos = Slugs::getById($id);
				Metas::$title = l('admin-slug-title', array($datos->slug));
			}		

			$data = [
				'datos' => $datos,
				'slugsPages' => Slugs::getPagesFromSlugs(),
				'languages' => Idiomas::getLanguages(),
			];

			Render::adminPage('slug_admin', $data);
		});

		// =================================
		//  Configuración -> Textos Legales
		// =================================
		$this->add('textos-legales',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_TEXTOS_LEGALES', true);

			Tools::registerJavascript(_ASSETS_.'tinymce/tinymce.min.js');
			Tools::registerJavascript(_ASSETS_.'tinymce/langs/es.js');

			if( Tools::getIsset('submitUpdateTextosLegales') )
			{
				$textos_legales = Tools::getValue('textos-legales');
				foreach( $textos_legales as $id_lang => $tipos_textos_legales )
				{
					foreach( $tipos_textos_legales as $tipo_texto_legal => $texto_legal )
					{
						TextosLegales::actualizarTextoLegal($id_lang, $tipo_texto_legal, $texto_legal);
					}
				}

				Tools::registerAlert(l('admin-textos-legales-update-ok'), 'success');
			}

			$data = array(
				'textos_legales' => TextosLegales::getTextosLegalesWithLang()
			);

			Metas::$title = l('admin-textos-legales-title');
			Render::adminPage('textos_legales', $data);
		});

		// =================================
		//  Configuración -> Textos Emails
		// =================================
		$this->add('textos-emails',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_TEXTOS_EMAILS', true);

			Tools::registerJavascript(_ASSETS_.'tinymce/tinymce.min.js');
			Tools::registerJavascript(_ASSETS_.'tinymce/langs/es.js');

			if( Tools::getIsset('submitAddTextosEmails') )
			{
				$nombre_interno = Tools::getValue('nombre_interno');
				if( !empty($nombre_interno) && !TextosEmails::checkNombreExists($nombre_interno) )
				{
					if( TextosEmails::crearTextoEmail($nombre_interno) )
						Tools::registerAlert(l('admin-textos-emails-add-ok'), 'success');
					else
						Tools::registerAlert(l('admin-textos-emails-add-ko'), 'error');
				}
				else
					Tools::registerAlert(l('admin-textos-emails-add-ko-nombre-incorrecto'), 'error');
			}

			if( Tools::getIsset('submitUpdateTextosEmails') )
			{
				$textos_emails = Tools::getValue('textos-emails');
				foreach( $textos_emails as $id_lang => $tipos_textos_emails )
				{
					foreach( $tipos_textos_emails as $tipos_texto_email => $texto_email )
					{
						TextosEmails::actualizarTextoEmail($id_lang, $tipos_texto_email, $texto_email['asunto'], $texto_email['contenido']);
					}
				}

				Tools::registerAlert(l('admin-textos-emails-update-ok'), 'success');
			}

			$data = array(
				'textos_emails' => TextosEmails::getTextosEmailsWithLang()
			);

			Metas::$title = l('admin-textos-emails-title');
			Render::adminPage('textos_emails', $data);
		});

		// =================================
		//  Usuarios Admin
		// =================================
		$this->add('usuarios-admin',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_USUARIOS_ADMIN', true);
			
			Tools::registerStylesheet(_ASSETS_._ADMIN_.'footable/footable.bootstrap.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'footable/footable.min.js');

			$data = array(
				'comienzo' => $this->comienzo,
				'pagina'   => $this->pagina,
				'limite'   => $this->limite
			);

			Metas::$title = l('admin-usuarios-admin-title');
			Render::adminPage('usuarios_admin', $data);
		});

		$this->add('usuario-admin',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_USUARIOS_ADMIN', true);

			$usuarioId 		= Tools::getValue('data');
			$usuario 		= false;
			
			if( Tools::getIsset('submitUpdateUsuarioAdmin') )
			{
				$email = Tools::getValue('email');
				if( !Admin::checkEmailExists($email, $usuarioId) )
				{
					Admin::actualizarUsuario();
					Tools::registerAlert(l('admin-usuario-admin-update-ok'), "success");
				}
				else
					Tools::registerAlert(l('admin-usuario-admin-update-ko-email-existe'), "error");
			}

			if( Tools::getIsset('submitCrearUsuarioAdmin') )
			{
				$email = Tools::getValue('email');
				if( !Admin::checkEmailExists($email) )
				{
					Admin::crearUsuario();
					Tools::registerAlert(l('admin-usuario-admin-add-ok'), "success");
					Tools::redirect(_ADMIN_."usuarios-admin/");
					exit;
				}
				else
					Tools::registerAlert(l('admin-usuario-admin-add-ko-email-existe'), "error");
			}

			$perfiles = Admin::getPerfiles(0, 0, false);

			Metas::$title = l('admin-usuario-admin-title-nuevo');
			if( $usuarioId !== 'new' ){
				$usuario = Admin::getUsuarioById($usuarioId);
				Metas::$title = l('admin-usuario-admin-title', array($usuario->nombre));
			}

			$data = array(
				'usuario' => $usuario,
				'perfiles' => $perfiles['listado']
			);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'select2/css/select2.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'select2/js/select2.min.js');
			Render::adminPage('usuario_admin', $data);
		});

		// =================================
		//  Perfiles y permisos
		// =================================
		$this->add('permisos',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_PERMISOS', true);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'footable/footable.bootstrap.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'footable/footable.min.js');

			$data = array(
				'comienzo' => $this->comienzo,
				'pagina'   => $this->pagina,
				'limite'   => $this->limite
			);

			Metas::$title = l('admin-permisos-title');
			Render::adminPage('permisos', $data);
		});

		$this->add('permiso',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_PERMISOS', true);

			$id_perfil = Tools::getValue('data');

			if( Tools::getIsset('submitUpdatePermisos') )
			{
				$submit_id_perfil = Tools::getValue('id_perfil');
				$permisos = Tools::getValue('id_permiso');
	
				Admin::guardarPermisos($submit_id_perfil, $permisos);
				Tools::registerAlert(l('admin-permiso-update-ok'), "success");
			}

			$permisosPerfil = Admin::getPermisosByIdPerfil($id_perfil);
			$permisos = Admin::getPermisos();

			$data = array(
				'id_perfil' => $id_perfil,
				'permisos' => $permisos,
				'permisosPerfil' => $permisosPerfil,
			);

			Metas::$title = l('admin-permiso-title', array(Admin::getNombrePerfilById($id_perfil)));
			Render::adminPage('permiso', $data);
		});

		$this->add('logout',function()
		{
			Render::$layout = false;
			Admin::logout();
			Tools::redirect(_ADMIN_);
		});

		/**
		 * SECCIONES DEMO
		 */
		$this->add('email-inbox',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('email-inbox');
		});

		$this->add('email-read',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('email-read');
		});

		$this->add('email-compose',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('email-compose');
		});

		$this->add('ui-alerts',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-alerts');
		});

		$this->add('ui-buttons',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-buttons');
		});

		$this->add('ui-badge',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-badge');
		});

		$this->add('ui-cards',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-cards');
		});

		$this->add('ui-carousel',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-carousel');
		});

		$this->add('ui-dropdowns',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-dropdowns');
		});

		$this->add('ui-grid',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);
			Render::adminPage('ui-grid');
		});

		$this->add('ui-images',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-images');
		});

		$this->add('ui-modals',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-modals');
		});

		$this->add('ui-pagination',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-pagination');
		});

		$this->add('ui-popover-tooltips',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-popover-tooltips');
		});

		$this->add('ui-progressbars',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-progressbars');
		});

		$this->add('ui-tabs-accordions',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-tabs-accordions');
		});

		$this->add('ui-typography',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-typography');
		});

		$this->add('ui-video',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('ui-video');
		});

		$this->add('components-lightbox',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'magnific-popup.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jquery.magnific-popup.min.js');

			Render::adminPage('components-lightbox');
		});

		$this->add('components-rangeslider',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'ion.rangeSlider.css');
			Tools::registerStylesheet(_ASSETS_._ADMIN_.'ion.rangeSlider.skinModern.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'ion.rangeSlider.min.js');

			Render::adminPage('components-rangeslider');
		});

		$this->add('components-session-timeout',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerJavascript(_ASSETS_._ADMIN_.'bootstrap-session-timeout.min.js');

			Render::adminPage('components-session-timeout');
		});

		$this->add('components-sweet-alert',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('components-sweet-alert');
		});

		$this->add('form-elements',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('form-elements');
		});

		$this->add('form-validation',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerJavascript(_ASSETS_._ADMIN_.'parsley.min.js');

			Render::adminPage('form-validation');
		});

		$this->add('form-advanced',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'bootstrap-colorpicker/css/bootstrap-colorpicker.min.css');
			Tools::registerStylesheet(_ASSETS_._ADMIN_.'bootstrap-md-datetimepicker/css/bootstrap-material-datetimepicker.css');
			Tools::registerStylesheet(_ASSETS_._ADMIN_.'select2/css/select2.min.css');
			Tools::registerStylesheet(_ASSETS_._ADMIN_.'bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'bootstrap-md-datetimepicker/js/moment-with-locales.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'bootstrap-md-datetimepicker/js/bootstrap-material-datetimepicker.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'bootstrap-colorpicker/js/bootstrap-colorpicker.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'select2/js/select2.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'bootstrap-maxlength/bootstrap-maxlength.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'bootstrap-filestyle/js/bootstrap-filestyle.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js');

			Render::adminPage('form-advanced');
		});

		$this->add('form-editors',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerJavascript(_ASSETS_.'tinymce/tinymce.min.js');
			Tools::registerJavascript(_ASSETS_.'tinymce/langs/es.js');

			Render::adminPage('form-editors');
		});

		$this->add('form-uploads',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'dropzone/dropzone.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'dropzone/dropzone.js');

			Render::adminPage('form-uploads');
		});

		$this->add('form-xeditable',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'x-editable/css/bootstrap-editable.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'moment.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'x-editable/js/bootstrap-editable.min.js');

			Render::adminPage('form-xeditable');
		});

		$this->add('charts-chartist',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'chartist/css/chartist.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'chartist/js/chartist.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'chartist/js/chartist-plugin-tooltip.min.js');

			Render::adminPage('charts-chartist');
		});

		$this->add('charts-chartjs',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerJavascript(_ASSETS_._ADMIN_.'chart.js/chart.min.js');

			Render::adminPage('charts-chartjs');
		});

		$this->add('charts-flot',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);
			
			Tools::registerJavascript(_ASSETS_._ADMIN_.'flot-chart/jquery.flot.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'flot-chart/jquery.flot.time.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'flot-chart/jquery.flot.tooltip.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'flot-chart/jquery.flot.resize.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'flot-chart/jquery.flot.pie.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'flot-chart/jquery.flot.selection.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'flot-chart/jquery.flot.stack.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'flot-chart/curvedLines.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'flot-chart/jquery.flot.crosshair.js');

			Render::adminPage('charts-flot');
		});

		$this->add('charts-c3',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'c3/c3.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'d3/d3.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'c3/c3.min.js');

			Render::adminPage('charts-c3');
		});

		$this->add('charts-morris',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'morris/morris.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'morris/morris.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'raphael/raphael-min.js');

			Render::adminPage('charts-morris');
		});

		$this->add('charts-other',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerJavascript(_ASSETS_._ADMIN_.'jquery-knob/excanvas.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jquery-knob/jquery.knob.js');

			Render::adminPage('charts-other');
		});

		$this->add('tables-basic',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('tables-basic');
		});

		$this->add('tables-responsive',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'footable/footable.bootstrap.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'footable/footable.min.js');

			Render::adminPage('tables-responsive');
		});

		$this->add('tables-editable',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerJavascript(_ASSETS_._ADMIN_.'tiny-editable/mindmup-editabletable.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'tiny-editable/numeric-input-example.js');

			Render::adminPage('tables-editable');
		});

		$this->add('icons-material',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('icons-material');
		});

		$this->add('icons-ion',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('icons-ion');
		});

		$this->add('icons-fontawesome',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('icons-fontawesome');
		});

		$this->add('icons-themify',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('icons-themify');
		});

		$this->add('icons-dripicons',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('icons-dripicons');
		});

		$this->add('icons-typicons',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Render::adminPage('icons-typicons');
		});

		$this->add('calendar',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'fullcalendar/css/fullcalendar.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jquery-ui/jquery-ui.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'moment.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'fullcalendar/js/fullcalendar.min.js');

			Render::adminPage('calendar');
		});

		$this->add('maps-google',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerJavascript('https://maps.google.com/maps/api/js?key=AIzaSyCtSAR45TFgZjOs4nBFFZnII-6mMHLfSYI');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'gmaps/gmaps.min.js');

			Render::adminPage('maps-google');
		});

		$this->add('maps-vector',function()
		{
			if(!isset($_SESSION['admin_panel']))
				Tools::redirect(_ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'jvectormap/jquery-jvectormap-2.0.2.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jvectormap/jquery-jvectormap-2.0.2.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jvectormap/jquery-jvectormap-world-mill-en.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jvectormap/gdp-data.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jvectormap/jquery-jvectormap-us-aea-en.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jvectormap/jquery-jvectormap-uk-mill-en.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jvectormap/jquery-jvectormap-us-il-chicago-mill-en.js');

			Render::adminPage('maps-vector');
		});

		$this->add('404',function()
		{
			Render::adminPage('404');
		});

		if( !$this->getRendered() )
			Tools::redirect(_ADMIN_."404/");
	}

	protected function loadTraducciones()
	{
		$this->loadTraduccionesAdmin();
	}
}
