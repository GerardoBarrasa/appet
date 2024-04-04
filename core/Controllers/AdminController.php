<?php

class AdminController
{
	var $page;

	var $comienzo = 0;
	var $limite   = 10;
	var $pagina   = 1;

	public function execute($page)
	{
		$this->page = $page;

		Render::$layout = 'back-end';

		Tools::registerJavascript(_ASSETS_.'jquery/jquery.min.js', 'top');
		Tools::registerJavascript(_ASSETS_._ADMIN_.'custom.js', 'top');

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
				Render::showAdminPage('login', $data);
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

			$msg_success = "";
			$msg_error = "";

			//Comprobamos update/creation
			if( isset($_REQUEST['action']) )
			{
				$result = Idiomas::administrarIdioma();

				if( $result == 'ok' )
				{
					if($id == '0')
						$msg_success = "Idioma creado correctamente.";
					else
						$msg_success = 'Idioma actualizado correctamente.';
				}
				else
					$msg_error = $result;
			}

			//Obtenemos los diferentes idiomas
			if( $id != '0' )
				$datos_idioma = Idiomas::getLanguages($id);
			else
				$datos_idioma = [];

			$data = array(
				'id' => $id,
				'datos_idioma' => $datos_idioma,
				'msg_success' => $msg_success,
				'msg_error' => $msg_error,
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
			$datos_idiomas = Idiomas::getLanguages();

			//Vamos a obtener el total de traducciones de cada idioma
			$totalTraductions = 0;
			if( count($datos_idiomas) > 0 )
			{
                foreach( $datos_idiomas as $key => $idioma )
                {
                	$totalTraductionsLanguage = count(Idiomas::getAllTraductionsById($idioma->id));
                    $totalTraductionsLanguageDone = count(Idiomas::getAllTraductionsById($idioma->id, true));

                    $idioma->totalTraductions = $totalTraductionsLanguage;
                    $idioma->totalTraductionsDone = $totalTraductionsLanguageDone;
                }
            }

			//Obtenemos los shortcodes para usar en filtros
			$datos_traductionFor = Idiomas::getTraductionsForGrouped();

			$data = array(
				'totalTraductions' => $totalTraductions,
				'datos_idiomas' => $datos_idiomas,
				'datos_traductionFor' => $datos_traductionFor,
				'comienzo' => $this->comienzo,
				'pagina' => $this->pagina,
				'limite' => $this->limite,
			);

			Render::adminPage('traducciones', $data);
		});

		$this->add('administrar-traduccion',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);
			
			//Obtenemos el data
			if( isset($_REQUEST['data']) )
				$id = $_REQUEST['data'];
			else
				header("Location: "._ADMIN_.'traducciones/');

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'jquery-toast-plugin/jquery.toast.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'jquery-toast-plugin/jquery.toast.min.js');

			//Obtenemos los diferentes idiomas
			$datos_idiomas = Idiomas::getLanguages();

			//Obtenemos los shortcodes para esta traduccion
			$datos_traducciones = Idiomas::getTraductionGrouped($id);

			$data = array(
				'datos_idiomas' => $datos_idiomas,
				'datos_traducciones' => $datos_traducciones,
			);

			Render::adminPage('administrar-traduccion', $data);
		});

		// =================================
		//  Usuarios Admin
		// =================================
		$this->add('usuarios-admin',function()
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
				
			Render::adminPage('usuarios_admin', $data);
		});

		$this->add('usuario-admin',function()
		{
			if(!isset($_SESSION['admin_panel']))
				header("Location: "._DOMINIO_._ADMIN_);

			$usuarioId 		= Tools::getValue('data');
			$usuario 		= false;
			$alert_user = "";
			
			if( Tools::getIsset('submitUpdateUsuarioAdmin') ){
				$alert_user = "El usuario ha sido modificado satisfactoriamente";
				Admin::actualizarUsuario();			
			}

			if( Tools::getIsset('submitCrearUsuarioAdmin') )
			{
				$alert_user = "El usuario ha sido creado";
				Admin::crearUsuario();
				header("Location: ". _DOMINIO_ . _ADMIN_ . 'usuarios-admin/');
			}
			
			Metas::$title = "Nuevo usuario";
			if( $usuarioId !== 'new' ){
				$usuario = Admin::getUsuarioById($usuarioId);
				Metas::$title = "Usuario: $usuario->nombre";
			}

			$data = array(
				'usuario' => $usuario,
				'alert_user' => $alert_user,
				'countries' => Country::adminGetCountries()
			);

			Tools::registerStylesheet(_ASSETS_._ADMIN_.'select2/css/select2.min.css');
			Tools::registerJavascript(_ASSETS_._ADMIN_.'select2/js/select2.min.js');
			Render::adminPage('usuario_admin', $data);
		});

		$this->add('logout',function()
		{
			Render::$layout = false;
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
	}

	public function add($page,$data)
	{
		if( $page == $this->page )
			return $data();
	}
}
?>
