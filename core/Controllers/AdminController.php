<?php

class AdminController extends Controllers
{
	var $comienzo = 0;
	var $limite   = 10;
	var $pagina   = 1;

	public function execute($page)
	{
		if( !isset($_SESSION['admin_panel']) && $page != '' ){
			header('HTTP/1.1 403 Forbidden');
			exit;
		}

		Render::$layout = 'back-end';

        // Cargamos hojas de estilos comunes a todo el back-end
        /* Google Font: Source Sans Pro */
		Tools::registerStylesheet("https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback");
        /* Font Awesome Icons */
		Tools::registerStylesheet(_ASSETS_._ADMIN_.'plugins/fontawesome-free/css/all.min.css');
        /* overlayScrollbars */
		Tools::registerStylesheet(_ASSETS_._ADMIN_.'plugins/overlayScrollbars/css/OverlayScrollbars.min.css');
        /* Theme style */
		Tools::registerStylesheet(_ASSETS_._ADMIN_.'dist/css/adminlte.min.css');

        // Cargamos javascripts comunes a todo el back-end

        /* jQuery */
        Tools::registerJavascript(_ASSETS_._ADMIN_.'plugins/jquery/jquery.min.js', 'top');
        /* Bootstrap */
        Tools::registerJavascript(_ASSETS_._ADMIN_.'plugins/bootstrap/js/bootstrap.bundle.min.js', 'top');
        /* overlayScrollbars */
        Tools::registerJavascript(_ASSETS_._ADMIN_.'plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js');
        /* AdminLTE App */
        Tools::registerJavascript(_ASSETS_._ADMIN_.'dist/js/adminlte.js');
        /* Custom */
        Tools::registerJavascript(_ASSETS_._ADMIN_.'dist/js/custom.js');

		/*Tools::registerStylesheet(_ASSETS_._ADMIN_.'bootstrap.min.css');
		Tools::registerJavascript(_ASSETS_.'jquery/jquery.min.js', 'top');
		Tools::registerJavascript(_ASSETS_._ADMIN_.'custom.js', 'top');*/

		Render::$layout_data = array(
			'idiomas' => Idiomas::getLanguagesAdminForm()
		);

		//Inicio
		$this->add('',function()
		{
			//Comprobamos si existe la sesion de admin
			if( !isset($_SESSION['admin_panel']) )
			{
				//Mensaje de error defecto
				$mensajeError = '';

				//Comprobamos datos de acceso
				if( isset($_REQUEST['btn-login']) )
				{

					//Obtenemos valores del login
					$usuario 	= Tools::getValue('usuario');
					$password	= Tools::md5(Tools::getValue('password'));

					if(Admin::login($usuario, $password))
						header('Location:'._DOMINIO_."admin/");
					else
						$mensajeError = "Usuario y/o contrase&ntilde;a incorrectos.";
				}

				//Guardamos variables para enviar a la pagina
				$data = array(
					'mensajeError' => $mensajeError,

				);

				//Metas Config
				Metas::$title = "&iexcl;Con&eacute;ctate!";

				//Renderizamos pagina admin
				Render::actionPage('login', $data);
			}

			else
			{
                /* jQuery Mapael */
                Tools::registerJavascript(_ASSETS_._ADMIN_.'plugins/jquery-mousewheel/jquery.mousewheel.js');
                Tools::registerJavascript(_ASSETS_._ADMIN_.'plugins/raphael/raphael.min.js');
                Tools::registerJavascript(_ASSETS_._ADMIN_.'plugins/jquery-mapael/jquery.mapael.min.js');
                Tools::registerJavascript(_ASSETS_._ADMIN_.'plugins/jquery-mapael/maps/usa_states.min.js');
                Tools::registerJavascript(_ASSETS_._ADMIN_.'plugins/chart.js/Chart.min.js');
                /* jQuery ChartJS */
                Tools::registerJavascript(_ASSETS_._ADMIN_.'plugins/chart.js/Chart.min.js');
                /* DEMO */
                Tools::registerJavascript(_ASSETS_._ADMIN_.'dist/js/demo.js');
                /* AdminLTE dashboard demo (This is only for demo purposes) */
                Tools::registerJavascript(_ASSETS_._ADMIN_.'dist/js/pages/dashboard2.js');

				Metas::$title = "Inicio";
				Render::adminPage('home');
			}
		});



        $this->add('accounts',function()
        {
            if(!isset($_SESSION['admin_panel']))
                header("Location: "._DOMINIO_._ADMIN_);

            $data = array(
                'comienzo' => $this->comienzo,
                'pagina'   => $this->pagina,
                'limite'   => $this->limite
            );

            Render::adminPage('accounts', $data);
        });

        $this->add('account',function()
        {
            if(!isset($_SESSION['admin_panel']))
                header("Location: "._DOMINIO_._ADMIN_);

            //Obtenemos el data
            if( isset($_REQUEST['data']) )
                $id = $_REQUEST['data'];
            else
                header("Location: "._DOMINIO_._ADMIN_);

            $account = Admin::getAccountById( $id );

            $data = array(
                'account' => $account
            );

            Render::adminPage('account', $data);
        });

		// =================================
		//  Idiomas
		// =================================
		$this->add('idiomas',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

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
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			//Obtenemos el data
			if( isset($_REQUEST['data']) )
				$id = $_REQUEST['data'];
			else
				header("Location: "._ADMIN_.'idiomas/');

			//Comprobamos update/creation
			if( isset($_REQUEST['action']) )
			{
				$result = Idiomas::administrarIdioma();

				if( $result == 'ok' )
				{
					if($id == '0'){
						Tools::registerAlert("Idioma creado correctamente.", "success");
						header("Location: "._ADMIN_.'idiomas/');
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
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

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
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

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
					header("Location: "._DOMINIO_._ADMIN_."traduccion/".(int)$traduccionId."/");
					die;
				}
				else
				{
					Tools::registerAlert("Shortcode vacío o ya existe", "error");
					header("Location: "._DOMINIO_._ADMIN_."traducciones/");
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
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			$idiomas = Idiomas::getLanguages();
			foreach( $idiomas as $idioma )
			{
				Traducciones::regenerarCacheTraduccionesByIdioma($idioma->id, _PATH_.'translations/'.$idioma->slug.'.php');
			}

			Render::$layout = false;
			Tools::registerAlert("Caché de traducciones regenerada correctamente.", "success");
			header("Location: "._DOMINIO_._ADMIN_."traducciones/");
		});

		// =================================
		//  Configuración -> Páginas
		// =================================
		$this->add('slugs',function()
		{
			if(!isset($_SESSION['admin_panel']) || !empty($_SESSION['admin_panel']->id_country))
				header("Location: "._DOMINIO_._ADMIN_);

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
			if(!isset($_SESSION['admin_panel']) || !empty($_SESSION['admin_panel']->id_country))
				header("Location: "._DOMINIO_._ADMIN_);

			//Obtenemos el data
			if( isset($_REQUEST['data']) )
				$id = $_REQUEST['data'];
			else
				header("Location: "._ADMIN_.'slugs/');

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
		$this->add('usuarios',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			$data = array(
				'comienzo' => $this->comienzo,
				'pagina'   => $this->pagina,
				'limite'   => $this->limite
			);

			Render::adminPage('usuarios', $data);
		});

		$this->add('usuario-admin',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

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

		$this->add('logout',function()
		{
			Admin::logout();
			header("Location: "._DOMINIO_._ADMIN_);
		});



		/**
		 * SECCIONES DEMO
		 */

		$this->add('email-inbox',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('email-inbox');
		});

		$this->add('email-read',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('email-read');
		});

		$this->add('email-compose',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('email-compose');
		});

		$this->add('ui-alerts',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-alerts');
		});

		$this->add('ui-buttons',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-buttons');
		});

		$this->add('ui-badge',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-badge');
		});

		$this->add('ui-cards',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-cards');
		});

		$this->add('ui-carousel',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-carousel');
		});

		$this->add('ui-dropdowns',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-dropdowns');
		});

		$this->add('ui-grid',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);
			Render::adminPage('ui-grid');
		});

		$this->add('ui-images',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-images');
		});

		$this->add('ui-modals',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-modals');
		});

		$this->add('ui-pagination',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-pagination');
		});

		$this->add('ui-popover-tooltips',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-popover-tooltips');
		});

		$this->add('ui-progressbars',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-progressbars');
		});

		$this->add('ui-tabs-accordions',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-tabs-accordions');
		});

		$this->add('ui-typography',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-typography');
		});

		$this->add('ui-video',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('ui-video');
		});

		$this->add('components-lightbox',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'magnific-popup.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jquery.magnific-popup.min.js');

			Render::adminPage('components-lightbox');
		});

		$this->add('components-rangeslider',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'ion.rangeSlider.css');
			Tools::registerStylesheet(_ASSETS_._ADMIN_.'ion.rangeSlider.skinModern.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'ion.rangeSlider.min.js');

			Render::adminPage('components-rangeslider');
		});

		$this->add('components-session-timeout',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerJavascript(_ASSETS_._ADMIN_.'bootstrap-session-timeout.min.js');

			Render::adminPage('components-session-timeout');
		});

		$this->add('components-sweet-alert',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('components-sweet-alert');
		});

		$this->add('form-elements',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('form-elements');
		});

		$this->add('form-validation',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerJavascript(_ASSETS_._ADMIN_.'parsley.min.js');

			Render::adminPage('form-validation');
		});

		$this->add('form-advanced',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

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
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerJavascript(_ASSETS_.'tinymce/tinymce.min.js');
			Tools::registerJavascript(_ASSETS_.'tinymce/langs/es.js');

			Render::adminPage('form-editors');
		});

		$this->add('form-uploads',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'dropzone/dropzone.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'dropzone/dropzone.js');

			Render::adminPage('form-uploads');
		});

		$this->add('form-xeditable',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'x-editable/css/bootstrap-editable.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'moment.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'x-editable/js/bootstrap-editable.min.js');

			Render::adminPage('form-xeditable');
		});

		$this->add('charts-chartist',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'chartist/css/chartist.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'chartist/js/chartist.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'chartist/js/chartist-plugin-tooltip.min.js');

			Render::adminPage('charts-chartist');
		});

		$this->add('charts-chartjs',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerJavascript(_ASSETS_._ADMIN_.'chart.js/chart.min.js');

			Render::adminPage('charts-chartjs');
		});

		$this->add('charts-flot',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

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
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'c3/c3.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'d3/d3.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'c3/c3.min.js');

			Render::adminPage('charts-c3');
		});

		$this->add('charts-morris',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'morris/morris.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'morris/morris.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'raphael/raphael-min.js');

			Render::adminPage('charts-morris');
		});

		$this->add('charts-other',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerJavascript(_ASSETS_._ADMIN_.'jquery-knob/excanvas.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jquery-knob/jquery.knob.js');

			Render::adminPage('charts-other');
		});

		$this->add('tables-basic',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('tables-basic');
		});

		$this->add('tables-responsive',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'footable/footable.bootstrap.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'footable/footable.min.js');

			Render::adminPage('tables-responsive');
		});

		$this->add('tables-editable',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerJavascript(_ASSETS_._ADMIN_.'tiny-editable/mindmup-editabletable.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'tiny-editable/numeric-input-example.js');

			Render::adminPage('tables-editable');
		});

		$this->add('icons-material',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('icons-material');
		});

		$this->add('icons-ion',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('icons-ion');
		});

		$this->add('icons-fontawesome',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('icons-fontawesome');
		});

		$this->add('icons-themify',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('icons-themify');
		});

		$this->add('icons-dripicons',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('icons-dripicons');
		});

		$this->add('icons-typicons',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Render::adminPage('icons-typicons');
		});

		$this->add('calendar',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'fullcalendar/css/fullcalendar.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jquery-ui/jquery-ui.min.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'moment.js');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'fullcalendar/js/fullcalendar.min.js');

			Render::adminPage('calendar');
		});

		$this->add('maps-google',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			Tools::registerJavascript('https://maps.google.com/maps/api/js?key=AIzaSyCtSAR45TFgZjOs4nBFFZnII-6mMHLfSYI');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'gmaps/gmaps.min.js');

			Render::adminPage('maps-google');
		});

		$this->add('maps-vector',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

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
		{
			header('Location: ' . _DOMINIO_._ADMIN_.'404/');
			exit;
		}
	}

	protected function loadTraducciones()
	{
		$this->loadTraduccionesAdmin();
	}
}
