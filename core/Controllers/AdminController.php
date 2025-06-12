<?php

class AdminController extends Controllers
{
	var $comienzo = 0;
	var $limite   = 10;
	var $pagina   = 1;

	public function execute($page)
	{
        // Primero verificamos si hay sesión de admin
        $hasAdminSession = isset($_SESSION['admin_panel']);
        
        // Si no hay sesión y la página no está vacía, redirigir al login
        if (!$hasAdminSession && $page != '') {
            header("Location: "._DOMINIO_._ADMIN_);
            exit;
        }

        // Obtenemos el entorno si existe (solo si hay sesión)
        if ($hasAdminSession) {
            Admin::getEntorno();
            // Validamos los datos del usuario logueado
            Admin::validateUser();
        }

		Render::$layout = 'back-end';

		Tools::registerStylesheet('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');
		Tools::registerStylesheet('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');
		Tools::registerStylesheet(_ASSETS_._COMMON_.'bootstrap-5.3.3-dist/css/bootstrap.min.css');
		Tools::registerStylesheet(_ASSETS_._COMMON_.'bootstrap-slider/css/bootstrap-slider.min.css');
		Tools::registerStylesheet(_ASSETS_._COMMON_.'toastr/toastr.min.css');
		Tools::registerStylesheet(_ASSETS_._COMMON_.'fontawesome-free-6.6.0-web/css/all.css');
		Tools::registerStylesheet(_RESOURCES_._ADMIN_.'css/adminlte.min.css');
		Tools::registerStylesheet(_RESOURCES_._ADMIN_.'css/style-admin.css?v='.time());

		Tools::registerJavascript(_ASSETS_._COMMON_.'jquery-3.7.1.min.js', 'top');
		Tools::registerJavascript(_ASSETS_._COMMON_.'bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js');
		Tools::registerJavascript(_ASSETS_._COMMON_.'bootstrap-slider/bootstrap-slider.min.js');
		Tools::registerJavascript(_ASSETS_._COMMON_.'underscore.js');
		Tools::registerJavascript(_ASSETS_._COMMON_.'toastar/toastr.min.js');
		Tools::registerJavascript(_RESOURCES_._ADMIN_.'js/adminlte.min.js');
		Tools::registerJavascript(_RESOURCES_._ADMIN_.'js/custom.js?v='.time(), 'top');

		Render::$layout_data = array(
			'idiomas' => Idiomas::getLanguagesAdminForm()
		);

		//Inicio - Página de login o dashboard
		$this->add('',function()
		{
			//Comprobamos si existe la sesion de admin
			if( !isset($_SESSION['admin_panel']) )
			{
				//Mensaje de error defecto
				$mensajeError = $_SESSION['actions_mensajeError'] ?? '';
                unset($_SESSION['actions_mensajeError']);
                Render::$layout = 'actions';

				//Comprobamos datos de acceso
				if( isset($_REQUEST['btn-login']) )
				{
					//Obtenemos valores del login
					$usuario 	= Tools::getValue('usuario');
					$password	= Tools::md5(Tools::getValue('password'));

					if(Admin::login($usuario, $password)) {
                        // Después del login exitoso, obtenemos el entorno y validamos
                        Admin::getEntorno();
                        Admin::validateUser();
                        header('Location:' . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
                        exit;
                    }
					else {
                        $mensajeError = "Usuario y/o contrase&ntilde;a incorrectos.";
                    }
				}

				//Guardamos variables para enviar a la pagina
				$data = array(
					'mensajeError' => $mensajeError,
				);

				//Metas Config
				Metas::$title = "&iexcl;Con&eacute;ctate!";

                Render::adminPage('login', $data);
			}
			else
			{
				Metas::$title = "Inicio";
				Render::adminPage('home');
			}
		});

		// =================================
		//  Idiomas
		// =================================
		$this->add('idiomas',function()
		{
			if(!isset($_SESSION['admin_panel'])) {
                header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
                exit;
            }

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'footable/footable.bootstrap.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'footable/footable.min.js');
			
			$data = array(
				'comienzo' => $this->comienzo,
				'pagina'   => $this->pagina,
				'limite'   => $this->limite
			);

			Render::adminPage('idiomas', $data);
		});

		// PAGE - Administrar el idioma
		$this->add('administrar-idioma',function()
		{
			if(!isset($_SESSION['admin_panel'])) {
                header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
                exit;
            }

			//Obtenemos el data
			if( isset($_REQUEST['data']) )
				$id = $_REQUEST['data'];
			else {
                header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . 'idiomas/');
                exit;
            }

			//Comprobamos update/creation
			if( isset($_REQUEST['action']) )
			{
				$result = Idiomas::administrarIdioma();

				if( $result == 'ok' )
				{
					if($id == '0'){
						Tools::registerAlert("Idioma creado correctamente.", "success");
						header("Location: "._DOMINIO_.$_SESSION['admin_vars']['entorno'].'idiomas/');
						die;
					} else{
						Tools::registerAlert("Idioma actualizado correctamente.", "success");
					}
				}
				else
					Tools::registerAlert($result);
			}

			//Obtenemos los diferentes idiomas
			if( $id != '0' )
				$datos_idioma = Idiomas::getLanguages($id);
			else
				$datos_idioma = [];

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
			if(!isset($_SESSION['admin_panel'])) {
                header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
                exit;
            }

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
			);

			Render::adminPage('traducciones', $data);
		});

		$this->add('traduccion',function()
		{
			if(!isset($_SESSION['admin_panel'])) {
                header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
                exit;
            }

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'select2/css/select2.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'select2/js/select2.min.js');

			$traduccionId = Tools::getValue('data');
			$traduccion = false;

			if( Tools::getIsset('submitUpdateTraduccion') )
			{
				$traduccionId = Tools::getValue('id_traduccion');
				$shortcode = Tools::getValue('shortcode');
				if( !empty($shortcode) && !Traducciones::checkShortcodeExists($shortcode, $traduccionId) )
				{
					$textos = Tools::getValue('texto');
					Traducciones::actualizarTraduccion($traduccionId, $shortcode, $textos);
					Tools::registerAlert("Traducción actualizada correctamente.", "success");
				}
				else
					Tools::registerAlert("Shortcode vacío o ya existe", "error");
			}

			if( Tools::getIsset('submitCrearTraduccion') )
			{
				$shortcode = Tools::getValue('shortcode');
				if( !empty($shortcode) && !Traducciones::checkShortcodeExists($shortcode) )
				{
					$id_lang = Tools::getValue('id_idioma');
					$texto = Tools::getValue('texto');
					$traduccionId = Traducciones::crearTraduccion($shortcode, $id_lang, $texto);
					Tools::registerAlert("Traducción creada correctamente.", "success");
					header("Location: "._DOMINIO_.$_SESSION['admin_vars']['entorno']."traduccion/".(int)$traduccionId."/");
					die;
				}
				else
				{
					Tools::registerAlert("Shortcode vacío o ya existe", "error");
					header("Location: "._DOMINIO_.$_SESSION['admin_vars']['entorno']."traducciones/");
					die;
				}
			}
			
			Metas::$title = "Editando traducción";
			if( $traduccionId !== 'new' )
				$traduccion = Traducciones::getTraduccionById($traduccionId);

			$data = array(
				'traduccion' => $traduccion
			);

			Render::adminPage('traduccion', $data);
		});

		$this->add('regenerar-cache-traducciones',function()
		{
			if(!isset($_SESSION['admin_panel'])) {
                header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
                exit;
            }

			$idiomas = Idiomas::getLanguages();
			foreach( $idiomas as $idioma )
			{
				Traducciones::regenerarCacheTraduccionesByIdioma($idioma->id, _PATH_.'translations/'.$idioma->slug.'.php');
			}

			Render::$layout = false;
			Tools::registerAlert("Caché de traducciones regenerada correctamente.", "success");
			header("Location: "._DOMINIO_.$_SESSION['admin_vars']['entorno']."traducciones/");
            exit;
		});

		// =================================
		//  Configuración -> Páginas
		// =================================
		$this->add('slugs',function()
		{
			if(!isset($_SESSION['admin_panel']) || !empty($_SESSION['admin_panel']->id_country)) {
                header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
                exit;
            }

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
				'languageDefault' => Idiomas::getDefaultLanguage()
			);

			Metas::$title = "Páginas meta";
			Render::adminPage('slugs_admin', $data);
		});

		$this->add('administrar-slug',function()
		{
			if(!isset($_SESSION['admin_panel']) || !empty($_SESSION['admin_panel']->id_country)) {
                header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno']);
                exit;
            }
			
			//Obtenemos el data
			if( isset($_REQUEST['data']) )
				$id = $_REQUEST['data'];
			else {
                header("Location: " . _DOMINIO_ . $_SESSION['admin_vars']['entorno'] . 'slugs/');
                exit;
            }

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
										Tools::registerAlert("Página actualizada correctamente", "success");
									}
									else{
										Tools::registerAlert("La página seleccionada <strong>".$pageName."</strong> ya está siendo usado para este idioma.");
										$msg_error++;
									}
								}
								else{
									Tools::registerAlert("El slug indicado <strong>".$pageName."</strong> ya está siendo usado y no puede ser usado.");
									$msg_error++;
								}
							}else{
								Tools::registerAlert("Indica el title del slug, este aparecerá en la pestaña del navegador y ayuda a nivel SEO.");
								$msg_error++;
							}
						} else{
							Tools::registerAlert("Debes seleccionar el idioma al que pertenece este slug.");
							$msg_error++;
						}
					} else{
						Tools::registerAlert("Selecciona la página a la que pertenece el slug.");
						$msg_error++;
					}
				}else{
					Tools::registerAlert("Debes indicar el slug.");
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
				Metas::$title = "Slug: $datos->slug";
			}			

			$data = [
				'datos' => $datos,
				'slugsPages' => Slugs::getPagesFromSlugs(),
				'languages' => Idiomas::getLanguages(),
			];

			Render::adminPage('slug_admin', $data);
		});

		// =================================
		//  Usuarios Admin
		// =================================
		$this->add('usuarios-admin',function()
		{
			if(!isset($_SESSION['admin_panel'])) {
                header("Location: " . _DOMINIO_.$_SESSION['admin_vars']['entorno']);
                exit;
            }
			
			Tools::registerStylesheet(_ASSETS_._ADMIN_.'footable/footable.bootstrap.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'footable/footable.min.js');

			$data = array(
				'comienzo' => $this->comienzo,
				'pagina'   => $this->pagina,
				'limite'   => $this->limite
			);
				
			Render::adminPage('usuarios_admin', $data);
		});

		$this->add('usuario-admin',function()
		{
			if(!isset($_SESSION['admin_panel'])) {
                header("Location: " . _DOMINIO_.$_SESSION['admin_vars']['entorno']);
                exit;
            }

			$usuarioId 		= Tools::getValue('data');
			$usuario 		= false;
			
			if( Tools::getIsset('submitUpdateUsuarioAdmin') )
			{
				Admin::actualizarUsuario();
				Tools::registerAlert("El usuario ha sido modificado satisfactoriamente", "success");
			}

			if( Tools::getIsset('submitCrearUsuarioAdmin') )
			{
				Admin::crearUsuario();
				Tools::registerAlert("El usuario ha sido creado", "success");
			}
			
			Metas::$title = "Nuevo usuario";
			if( $usuarioId !== 'new' ){
				$usuario = Admin::getUsuarioById($usuarioId);
				Metas::$title = "Usuario: $usuario->nombre";
			}

			$data = array(
				'usuario' => $usuario
			);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'select2/css/select2.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'select2/js/select2.min.js');
			Render::adminPage('usuario_admin', $data);
		});

		$this->add('mascotas',function()
		{
			if(!isset($_SESSION['admin_panel'])) {
                header("Location: " . _DOMINIO_.$_SESSION['admin_vars']['entorno']);
                exit;
            }

			$data = array(
                'comienzo' => $this->comienzo,
                'pagina'   => $this->pagina,
                'limite'   => $this->limite
			);

			Render::adminPage('mascotas', $data);
		});

		$this->add('mascota',function()
		{
			if(!isset($_SESSION['admin_panel'])) {
                header("Location: " . _DOMINIO_.$_SESSION['admin_vars']['entorno']);
                exit;
            }
            if( isset($_REQUEST['data']) )
                $data = explode('-',$_REQUEST['data']);
            $idMascota              = $data[1];
            $mascota                = Mascotas::getMascotaById($idMascota);
            $mascotaCaracteristicas = Caracteriticas::getCaracteristicasByMascota($idMascota);
            $caracteristicas        = Caracteriticas::getCaracteristicas();

			$data = array(
				'mascota'                   => $mascota,
				'caracteristicas'    => $caracteristicas,
				'mascotaCaracteristicas'    => $mascotaCaracteristicas,
			);

			Render::adminPage('mascota', $data);
		});

		$this->add('logout',function()
		{
            Tools::logError('LOGOUT');
			Render::$layout = false;
            $dest = isset($_SESSION['admin_vars']['entorno']) ? $_SESSION['admin_vars']['entorno'] : _ADMIN_;
            Admin::logout();
            header("Location: "._DOMINIO_.$dest);
            exit;
		});

		$this->add('404',function()
		{
			// Si no hay sesión de admin y llegamos a 404, redirigir al login
			if(!isset($_SESSION['admin_panel'])) {
                header("Location: " . _DOMINIO_ . _ADMIN_);
                exit;
            }
			Render::adminPage('404');
		});

		if( !$this->getRendered() )
		{
            // Si no hay sesión de admin, redirigir al login en lugar de 404
            if(!isset($_SESSION['admin_panel'])) {
                header("Location: " . _DOMINIO_ . _ADMIN_);
                exit;
            }
            
            // Si hay sesión pero la página no existe, mostrar 404
			header('Location: ' . _DOMINIO_.$_SESSION['admin_vars']['entorno'].'404/');
			exit;
		}
	}

	protected function loadTraducciones()
	{
		$this->loadTraduccionesAdmin();
	}
}
