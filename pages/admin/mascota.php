<?php
/**
 * @var $mascota
 * @var $caracteristicas
 */
$image = file_exists(_RESOURCES_PATH_.'private/mascotas/'.$mascota->id.'/profile.jpg') ? _RESOURCES_.'private/mascotas/'.$mascota->id.'/profile.jpg' : _RESOURCES_ . _COMMON_ .'img/petType_'.$mascota->tipo.'_default.png';
?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-5 col-xl-4">

                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center position-relative">
                            <img id="profileImage"
                                 class="profile-user-img img-fluid img-circle w-100 clickable"
                                 src="<?=$image?>"
                                 alt="<?=$mascota->nombre?>"
                                 data-mascota-id="<?=$mascota->id?>"
                                 onclick="changeProfileImage()"
                                 style="cursor: pointer; transition: opacity 0.3s ease;"
                                 onmouseover="this.style.opacity='0.8'"
                                 onmouseout="this.style.opacity='1'"
                                 data-toggle="tooltip"
                                 title="Click para cambiar la imagen de perfil">

                            <!-- Overlay de cámara -->
                            <div class="position-absolute"
                                 style="top: 50%; left: 50%; transform: translate(-50%, -50%);
                                        background: rgba(0,0,0,0.7); border-radius: 50%;
                                        width: 40px; height: 40px; display: flex;
                                        align-items: center; justify-content: center;
                                        opacity: 0; transition: opacity 0.3s ease; pointer-events: none;"
                                 id="cameraOverlay">
                                <i class="fas fa-camera text-white"></i>
                            </div>

                            <!-- Loading spinner -->
                            <div id="profileImageLoading"
                                 class="position-absolute"
                                 style="top: 50%; left: 50%; transform: translate(-50%, -50%); display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                            </div>
                        </div>

                        <h3 class="profile-username clickable text-center editable" data-type="mascota" data-content="nombre" data-id="<?=$mascota->id?>" onclick="modalGeneral(this)">
                            <?=$mascota->nombre?><?=$mascota->alias == '' ? '' : '<span class="small"> ('.$mascota->alias.')</span>'?>
                            <i class="fa fa-pencil text-muted"></i>
                        </h3>

                        <p class="text-muted text-center clickable editable" data-type="mascota" data-content="generoraza" data-id="<?=$mascota->id?>" onclick="modalGeneral(this)">
                            <?=$mascota->GENERO?><?=$mascota->raza == '' ?: ' '.$mascota->raza?>
                            <i class="fa fa-pencil text-muted"></i>
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item clickable" data-type="mascota" data-content="peso" data-id="<?=$mascota->id?>" onclick="modalGeneral(this)">
                                <i class="fa fa-weight-hanging"></i>
                                <b>Peso</b>
                                <a class="float-right editable">
                                    <i class="fa fa-pencil text-muted"></i>
                                    <?=$mascota->peso == 0 ? '---' : $mascota->peso.'Kg'?>
                                </a>
                            </li>
                            <li class="list-group-item clickable" data-type="mascota" data-content="esterilizado" data-id="<?=$mascota->id?>" onclick="modalGeneral(this)">
                                <i class="fa fa-syringe"></i>
                                <b>Esterilizado/a</b>
                                <a class="float-right editable <?=$mascota->esterilizado == 1 ? ' text-success' : ''?>">
                                    <i class="fa fa-pencil text-muted"></i>
                                    <?=$mascota->esterilizado == 1 ? 'Sí' : 'No'?>
                                </a>
                            </li>
                            <li class="list-group-item clickable" data-type="mascota" data-content="edad" data-id="<?=$mascota->id?>" onclick="modalGeneral(this)">
                                <i class="fa fa-clock"></i>
                                <b>Edad (años)</b>
                                <a class="float-right editable">
                                    <i class="fa fa-pencil text-muted"></i>
                                    <?=$mascota->nacimiento_fecha != '' && $mascota->nacimiento_fecha != '0000-00-00' ? Tools::calcularAniosTranscurridos($mascota->nacimiento_fecha).' <span class="small">(nació el ' . Tools::fecha($mascota->nacimiento_fecha).')</span>' : ($mascota->edad == 0 ? '---' : Tools::calcularAniosTranscurridos($mascota->edad_fecha)+$mascota->edad .' <span class="small">('.$mascota->edad.' a día ' . Tools::fecha($mascota->edad_fecha).')').'</span>'?>
                                </a>
                            </li>
                        </ul>

                        <!--<a href="<?php /*=_DOMINIO_.$_SESSION['admin_vars']['entorno']*/?>editar-mascota/<?php /*=$mascota->slug.'-'.$mascota->id*/?>/" class="btn btn-primary btn-block"><b>Editar</b></a>-->
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <!-- About Me Box -->

                <div class="accordion" id="aboutme">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <div class="accordion-button clickable collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                Sobre mi
                            </div>
                        </h3>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#aboutme">
                            <div class="accordion-body">
                                <?php $cnom = '';
                                foreach ($caracteristicas as $cr){
                                    if($cnom != $cr->nombre){//Cambiamos de característica?>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span>
                                                <i class="fa <?=$cr->ico?> mr-1"></i>
                                                <strong><?=$cr->nombre?>: </strong>
                                                <span class="caracteristicaTag_<?=$cr->id?>"><?=$cr->tipo == 'escala' && isset($mascotaCaracteristicas[$cr->id]) && $mascotaCaracteristicas[$cr->id]->valor > 0 ? $mascotaCaracteristicas[$cr->id]->valor.'/'.max(explode(',', $cr->valores)) : ''?></span>
                                            </span>
                                            <i class="fa fa-save fs-4 text-secondary clickable d-none save_<?=Tools::urlAmigable($cr->nombre)?>" onclick="saveEvaluation('<?=$mascota->id?>','evaluate_<?=Tools::urlAmigable($cr->nombre)?>')"></i>
                                        </div>
                                        <?php $cnom = $cr->nombre;}
                                    if($cr->tipo == 'escala'){
                                        $values = explode(',', $cr->valores);?>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="col-11">
                                                <div class="slider-yellow">
                                                    <input type="text" value="<?=isset($mascotaCaracteristicas[$cr->id]) ? $mascotaCaracteristicas[$cr->id]->valor : 0 ?>" class="detchng evaluate_<?=Tools::urlAmigable($cr->nombre)?> slider form-control" data-slider-min="<?=min($values)?>" data-slider-max="<?=max($values)?>" data-slider-step="1" data-slider-value="<?=isset($mascotaCaracteristicas[$cr->id]) ? $mascotaCaracteristicas[$cr->id]->valor : 0 ?>" data-slider-orientation="horizontal" data-slider-selection="before" data-slider-tooltip="show" data-crslug="<?=$cr->slug?>" data-crtype="<?=$cr->tipo?>" data-crid="<?=$cr->id?>" data-orig="<?=isset($mascotaCaracteristicas[$cr->id]) ? $mascotaCaracteristicas[$cr->id]->valor : 0 ?>" data-savebtn="save_<?=Tools::urlAmigable($cr->nombre)?>" onchange="compruebaCambios(this)">
                                                </div>
                                            </div>
                                            <i class="fa fa-question-circle text-info fs-4 pl-2" data-toggle="tooltip" title="<?=$cr->texto_ayuda?>"></i>
                                        </div>

                                    <?php }
                                    if($cr->tipo == 'texto'){
                                        $values = explode(',', $cr->valores);?>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="col-12">
                                                <textarea rows="5" style="height: 70px" class="detchng evaluate_<?=Tools::urlAmigable($cr->nombre)?> form-control form-text" data-crslug="<?=$cr->slug?>" data-crtype="<?=$cr->tipo?>" data-crid="<?=$cr->id?>" data-orig="<?=isset($mascotaCaracteristicas[$cr->id]) ? $mascotaCaracteristicas[$cr->id]->valor : '' ?>" data-savebtn="save_<?=Tools::urlAmigable($cr->nombre)?>" onkeyup="compruebaCambios(this)"><?=isset($mascotaCaracteristicas[$cr->id]) ? $mascotaCaracteristicas[$cr->id]->valor : '' ?></textarea>
                                            </div>
                                        </div>

                                    <?php }?>
                                    <hr class="border-1 bg-secondary">
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-sm-7 col-xl-8">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#ficha" data-toggle="tab">Ficha</a></li>
                            <li class="nav-item"><a class="nav-link" href="#historial" data-toggle="tab">Historial</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="ficha">

                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="historial">
                                <!-- The timeline -->
                                <div class="timeline timeline-inverse">
                                    <!-- timeline time label -->
                                    <div class="time-label">
                        <span class="bg-danger">
                          10 Feb. 2014
                        </span>
                                    </div>
                                    <!-- /.timeline-label -->
                                    <!-- timeline item -->
                                    <div>
                                        <i class="fas fa-envelope bg-primary"></i>

                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> 12:05</span>

                                            <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                                            <div class="timeline-body">
                                                Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                                weebly ning heekya handango imeem plugg dopplr jibjab, movity
                                                jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                                                quora plaxo ideeli hulu weebly balihoo...
                                            </div>
                                            <div class="timeline-footer">
                                                <a href="#" class="btn btn-primary btn-sm">Read more</a>
                                                <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END timeline item -->
                                    <!-- timeline item -->
                                    <div>
                                        <i class="fas fa-user bg-info"></i>

                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> 5 mins ago</span>

                                            <h3 class="timeline-header border-0"><a href="#">Sarah Young</a> accepted your friend request
                                            </h3>
                                        </div>
                                    </div>
                                    <!-- END timeline item -->
                                    <!-- timeline item -->
                                    <div>
                                        <i class="fas fa-comments bg-warning"></i>

                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> 27 mins ago</span>

                                            <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                                            <div class="timeline-body">
                                                Take me to your leader!
                                                Switzerland is small and neutral!
                                                We are more like Germany, ambitious and misunderstood!
                                            </div>
                                            <div class="timeline-footer">
                                                <a href="#" class="btn btn-warning btn-flat btn-sm">View comment</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END timeline item -->
                                    <!-- timeline time label -->
                                    <div class="time-label">
                        <span class="bg-success">
                          3 Jan. 2014
                        </span>
                                    </div>
                                    <!-- /.timeline-label -->
                                    <!-- timeline item -->
                                    <div>
                                        <i class="fas fa-camera bg-purple"></i>

                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> 2 days ago</span>

                                            <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                                            <div class="timeline-body">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END timeline item -->
                                    <div>
                                        <i class="far fa-clock bg-gray"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- Input file oculto para seleccionar imagen -->
<input type="file" id="profileImageInput" accept="image/jpeg,image/jpg,image/png" style="display: none;">

<!-- Modal para recortar imagen de perfil -->
<div class="modal fade" id="profileCropModal" tabindex="-1" role="dialog" aria-labelledby="profileCropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileCropModalLabel">
                    <i class="fas fa-crop-alt mr-2"></i>Recortar imagen de perfil
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="img-container">
                            <img id="profileCropImage" src="" alt="Imagen a recortar" style="max-width: 100%;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="preview-container">
                            <h6 class="text-center mb-3">Vista previa</h6>
                            <div id="profileCropPreview"
                                 style="width: 150px; height: 150px; border-radius: 50%;
                                        overflow: hidden; margin: 0 auto; border: 2px solid #ddd;"></div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    La imagen se guardará como 400x400px en formato JPG
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="profileConfirmCrop">
                    <i class="fas fa-check mr-1"></i>Aplicar recorte
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Efecto hover para mostrar overlay de cámara
    $(document).ready(function() {
        $('#profileImage').hover(
            function() {
                $('#cameraOverlay').css('opacity', '1');
            },
            function() {
                $('#cameraOverlay').css('opacity', '0');
            }
        );
    });
</script>
