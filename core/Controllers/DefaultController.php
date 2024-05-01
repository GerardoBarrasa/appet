<?php

class DefaultController extends Controllers
{
	public function execute($page)
	{
		//Layout por defecto
		Render::$layout = 'front-end';
		Tools::registerJavascript(_ASSETS_.'jquery/jquery.min.js');

		$idiomas = Idiomas::getLanguages();
		foreach( $idiomas as &$idioma )
		{
			$idioma->slug_complete = Slugs::getSlugCompleteForIdLang($idioma);
		}

		Render::$layout_data = array(
			'page_name' => $page == '' ? 'home' : $page,
			'idiomas' => $idiomas
		);

		if( !empty($metaData = Slugs::getPageDataByModId(($page == '' ? 'home' : $page))) )
		{
			Metas::$title = (isset($metaData->title)) ? $metaData->title : _TITULO_;
			Metas::$description = (isset($metaData->description)) ? $metaData->description : _TITULO_;
		}
		else
			header('Location:'._DOMINIO_.$_SESSION['lang']."/");

		//Pagina de inicio
		$this->add('',function()
		{
			$mpc = new Miprimeraclase;
			$datos_idiomas = Idiomas::getLanguages();

			//Array de datos a enviar a la página
			$data = array(
				'datos_idiomas' => $datos_idiomas,
				'test' => $mpc->getMessage(),
			);

			Render::page('home',$data);
		});

		//Pagina de inicio
		$this->add('test',function()
		{
			$mpc = new Miprimeraclase;
			$datos_idiomas = Idiomas::getLanguages();

			//Array de datos a enviar a la página
			$data = array(
				'datos_idiomas' => $datos_idiomas,
				'test' => $mpc->getMessage(),
			);

			Render::page('home',$data);
		});

		//Pagina de login
		$this->add('login',function()
		{
			$datos_idiomas = Idiomas::getLanguages();

			//Array de datos a enviar a la página
			$data = array(
				'datos_idiomas' => $datos_idiomas,
			);

            Render::actionPage('login', $data);
		});

		//Pagina de login
        $this->add('register',function()
        {
            // Valores por defecto
            $mensajeError   = '';
            $mensajeSuccess = '';
            $updateSlug     = false;

            //Comprobamos datos de acceso
            if( isset($_REQUEST['btn-register']) )
            {
                //Obtenemos valores del registro
                // TODO ver de añadir los campos restantes (CP, localidad, provincia...)
                $nombre     = Tools::getValue('nombre');
                $email      = Tools::getValue('email');
                $password   = Tools::md5(Tools::getValue('password'));
                $rpassword  = Tools::md5(Tools::getValue('rpassword'));
                $provincia  = Tools::md5(Tools::getValue('provincia'));
                $localidad  = Tools::md5(Tools::getValue('localidad'));
                $direccion = Tools::md5(Tools::getValue('direccion'));
                $slug       = Tools::urlAmigable($nombre, false);

                if($nombre == '' || $email == '' || $password = '' || $rpassword = '' || $provincia = '' || $localidad = '' || $direccion = '' ){//Comprobamos que los campos no estén vacíos
                    $mensajeError .= "Todos los campos son obligatorios.<br>";
                }
                else{// Ahora comprobamos la validez de los campos
                    if($password != $rpassword){
                        $mensajeError .= "Las contraseñas deben coincidir.<br>";
                    }
                    if(Account::getAccountByEmail($email) !== false){// Si existe alguna cuenta con este correo
                        $mensajeError .= "Ya existe una cuenta con ese email asociado.<br>";
                    }
                }
                // Si no hay errores procedemos, si no dejamos continuar a la página de destino
                if($mensajeError == ''){
                    if(Account::getAccountBySlug($slug) !== false){// Existe una cuenta con ese slug, tenemos que generar otro, para ello guardamos la cuenta y obtenemos el ID
                        $updateSlug = true;
                        $slug = 'NULL';
                    }
                    // TODO ver de añadir los campos restantes (CP, localidad, provincia...)
                    $addAccount = array(
                        'name' 	        => $nombre,
                        'email' 	    => $email,
                        'password' 	    => $password,
                        'provincia'     => $provincia,
                        'localidad'     => $localidad,
                        'direccion'     => $direccion,
                        'slug' 	        => $slug,
                        'type_id'       => 1,// Por defecto guardería TODO esto debería venir del formulario de registro
                        'date_created'  => Tools::datetime()
                    );
                    if(Account::crearAccount($addAccount)){
                        $mensajeSuccess .= "Cuenta creada correctamente";
                        $id_account = Bd::getInstance()->lastId();
                        if($updateSlug){// Actualizamos slug
                            $slug = Tools::urlAmigable($nombre, false).'-'.$id_account;
                            Account::updateAccount($id_account, $slug);
                        }
                        // TODO Comprobamos si existe un usuario con ese correo, ya veremos qué acciones hacer aquí
                        if(Admin::getUsuarioByEmail($email) !== false){// Si existe algún usuario con este correo
                            $mensajeError .= "Ya existe un usuario con ese email.<br>";
                        }
                        else{
                            $addUsuario = array(
                                'nombre' 	    => $nombre,
                                'email' 	    => $email,
                                'password' 	    => $password,
                                'account_id'    => $id_account,
                                'estado'        => 0,
                                'id_credencial' => 2,
                                'date_created'  => Tools::datetime()
                            );
                            if(Admin::crearUsuario($addUsuario)){
                                $mensajeSuccess .= "Usuario creado correctamente";
                            }
                            else{
                                $mensajeError .= "Error al crear el usuario";
                            }
                        }
                    }
                    else{
                        $mensajeError .= "Error al crear la cuenta";
                    }
                }
            }
            $datos_idiomas = Idiomas::getLanguages();

            //Array de datos a enviar a la página
            $data = array(
                'datos_idiomas' => $datos_idiomas,
                'mensajeError' => $mensajeError,
                'mensajeSuccess' => $mensajeSuccess,
            );

            Render::actionPage('register', $data);
        });

		//Pagina de login
		$this->add('forgot-password',function()
		{
			$datos_idiomas = Idiomas::getLanguages();

			//Array de datos a enviar a la página
			$data = array(
				'datos_idiomas' => $datos_idiomas,
			);

            Render::actionPage('forgot-password', $data);
		});

		$this->add('404',function()
		{
			Render::page('404');
		});

		if( !$this->getRendered() )
		{
			header('Location: ' . _DOMINIO_.$_SESSION['lang'].'/404/');
			exit;
		}
	}

}
