<div class="left side-menu">
	<div class="slimscroll-menu" id="remove-scroll">
		<div id="sidebar-menu">
			<ul class="metismenu" id="side-menu">

				<?php
				if(
					Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_IDIOMAS') ||
					Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_TRADUCCIONES')
				)
				{
					?>
					<li>
						<a href="javascript:void(0);" class="waves-effect" id="idiomas"><i class="ion-ios7-world-outline"></i><span> <?=l('admin-menu-idiomas');?> <span class="float-right menu-arrow"><i class="mdi mdi-plus"></i></span> </span></a>
						<ul class="submenu">
							<?php
							if(	Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_TRADUCCIONES') )
							{
								?>
								<li><a href="<?=_DOMINIO_._ADMIN_;?>traducciones/" class="waves-effect"><?=l('admin-menu-traducciones');?></a></li>
								<?php
							}

							if(	Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_IDIOMAS') )
							{
								?>
								<li><a href="<?=_DOMINIO_._ADMIN_;?>idiomas/" class="waves-effect"><?=l('admin-menu-idiomas');?></a></li>
								<?php
							}
							?>
						</ul>
					</li>
					<?php
				}

				if(
					Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_CONFIGURACION') ||
					Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_SLUGS') ||
					Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_TEXTOS_LEGALES') ||
					Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_TEXTOS_EMAILS')
				)
				{
					?>
					<li>
						<a href="javascript:void(0);" class="waves-effect" id="configuracion"><i class="ion-ios7-cog"></i><span> <?=l('admin-menu-configuracion');?> <span class="float-right menu-arrow"><i class="mdi mdi-plus"></i></span> </span></a>
						<ul class="submenu">
							<?php
							if(	Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_CONFIGURACION') )
							{
								?>
								<li><a href="<?=_DOMINIO_._ADMIN_;?>configuracion/" class="waves-effect"><?=l('admin-menu-configuracion');?></a></li>
								<?php
							}

							if(	Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_SLUGS') )
							{
								?>
								<li><a href="<?=_DOMINIO_._ADMIN_;?>slugs/" class="waves-effect"><?=l('admin-menu-paginas');?></a></li>
								<?php
							}

							if(	Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_TEXTOS_LEGALES') )
							{
								?>
								<li><a href="<?=_DOMINIO_._ADMIN_;?>textos-legales/" class="waves-effect"><?=l('admin-menu-textos-legales');?></a></li>
								<?php
							}

							if(	Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_TEXTOS_EMAILS') )
							{
								?>
								<li><a href="<?=_DOMINIO_._ADMIN_;?>textos-emails/" class="waves-effect"><?=l('admin-menu-textos-emails');?></a></li>
								<?php
							}
							?>
						</ul>
					</li>
					<?php
				}

				if(
					Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_USUARIOS_ADMIN') ||
					Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_PERMISOS')
				)
				{
					?>
					<li>
						<a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-account-key"></i><span> <?=l('admin-menu-usuarios-admin');?> <span class="float-right menu-arrow"><i class="mdi mdi-plus"></i></span> </span></a>
						<ul class="submenu">
							<?php
							if( Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_USUARIOS_ADMIN') )
							{
								?>
								<li><a href="<?=_DOMINIO_._ADMIN_;?>usuarios-admin/" class="waves-effect"><?=l('admin-menu-listado');?></a></li>
								<?php
							}

							if( Admin::checkAccess($_SESSION['admin_panel']->id_usuario_admin, 'ACCESS_PERMISOS') )
							{
								?>
								<li><a href="<?=_DOMINIO_._ADMIN_;?>permisos/" class="waves-effect"><?=l('admin-menu-permisos');?></a></li>
								<?php
							}
							?>
						</ul>
					</li>
					<?php
				}
				?>

				<li class="menu-title">Apartados demo</li>

				<li>
					<a href="<?=_DOMINIO_._ADMIN_;?>" class="waves-effect">
						<i class="mdi mdi-home"></i><span class="badge badge-primary float-right">3</span> <span> Dashboard </span>
					</a>
				</li>

				<li>
					<a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-email"></i><span> Email <span class="float-right menu-arrow"><i class="mdi mdi-plus"></i></span> </span></a>
					<ul class="submenu">
						<li><a href="<?=_DOMINIO_._ADMIN_;?>email-inbox/">Inbox</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>email-read/">Email Read</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>email-compose/">Email Compose</a></li>
					</ul>
				</li>

				<li>
					<a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-buffer"></i> <span> UI Elements <span class="float-right menu-arrow"><i class="mdi mdi-plus"></i></span> </span> </a>
					<ul class="submenu">
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-alerts/">Alerts</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-buttons/">Buttons</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-badge/">Badge</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-cards/">Cards</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-carousel/">Carousel</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-dropdowns/">Dropdowns</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-grid/">Grid</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-images/">Images</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-modals/">Modals</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-pagination/">Pagination</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-popover-tooltips/">Popover & Tooltips</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-progressbars/">Progress Bars</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-tabs-accordions/">Tabs & Accordions</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-typography/">Typography</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>ui-video/">Video</a></li>
					</ul>
				</li>

				<li>
					<a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-black-mesa"></i> <span> Components <span class="float-right menu-arrow"><i class="mdi mdi-plus"></i></span> </span> </a>
					<ul class="submenu">
						<li><a href="<?=_DOMINIO_._ADMIN_;?>components-lightbox/">Lightbox</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>components-rangeslider/">Range Slider</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>components-session-timeout/">Session Timeout</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>components-sweet-alert/">Sweet-Alert</a></li>
					</ul>
				</li>

				<li>
					<a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-clipboard"></i><span> Forms <span class="badge badge-success float-right">6</span> </span></a>
					<ul class="submenu">
						<li><a href="<?=_DOMINIO_._ADMIN_;?>form-elements/">Form Elements</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>form-validation/">Form Validation</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>form-advanced/">Form Advanced</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>form-editors/">Form Editors</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>form-uploads/">Form File Upload</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>form-xeditable/">Form Xeditable</a></li>

					</ul>
				</li>

				<li>
					<a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-finance"></i><span> Charts <span class="float-right menu-arrow"><i class="mdi mdi-plus"></i></span> </span></a>
					<ul class="submenu">
						<li><a href="<?=_DOMINIO_._ADMIN_;?>charts-chartist/">Chartist Chart</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>charts-chartjs/">Chartjs Chart</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>charts-flot/">Flot Chart</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>charts-c3/">C3 Chart</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>charts-morris/">Morris Chart</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>charts-other/">Jquery Knob Chart</a></li>
					</ul>
				</li>

				<li>
					<a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-table-settings"></i><span> Tables <span class="float-right menu-arrow"><i class="mdi mdi-plus"></i></span> </span></a>
					<ul class="submenu">
						<li><a href="<?=_DOMINIO_._ADMIN_;?>tables-basic/">Basic Tables</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>tables-datatable/">Data Table</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>tables-responsive/">Responsive Table</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>tables-editable/">Editable Table</a></li>
					</ul>
				</li>

				<li>
					<a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-album"></i> <span> Icons  <span class="float-right menu-arrow"><i class="mdi mdi-plus"></i></span></span> </a>
					<ul class="submenu">
						<li><a href="<?=_DOMINIO_._ADMIN_;?>icons-material/">Material Design</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>icons-ion/">Ion Icons</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>icons-fontawesome/">Font Awesome</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>icons-themify/">Themify Icons</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>icons-dripicons/">Dripicons</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>icons-typicons/">Typicons Icons</a></li>
					</ul>
				</li>

				<li>
					<a href="<?=_DOMINIO_._ADMIN_;?>calendar/" class="waves-effect"><i class="mdi mdi-calendar-check"></i><span> Calendar </span></a>
				</li>

				<li>
					<a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-google-maps"></i><span> Maps  <span class="float-right menu-arrow"><i class="mdi mdi-plus"></i></span></span></a>
					<ul class="submenu">
						<li><a href="<?=_DOMINIO_._ADMIN_;?>maps-google/"> Google Map</a></li>
						<li><a href="<?=_DOMINIO_._ADMIN_;?>maps-vector/"> Vector Map</a></li>
					</ul>
				</li>

				<li>
					<a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-google-pages"></i><span> Pages <span class="float-right menu-arrow"><i class="mdi mdi-plus"></i></span> </span></a>
					<ul class="submenu">
						<li><a href="<?=_DOMINIO_._ADMIN_;?>pages-login/">Login</a></li>
						<!--li><a href="<?=_DOMINIO_._ADMIN_;?>pages-recoverpw/">Recover Password</a></li-->
					</ul>
				</li>
			</ul>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
