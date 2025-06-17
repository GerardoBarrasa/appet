<?php
// Verificar autenticación
if (!isset($_SESSION['admin_panel'])) {
    header("Location: " . _DOMINIO_ . _ADMIN_);
    exit;
}

// Establecer título de la página
if (class_exists('Metas')) {
    Metas::$title = "Nueva Mascota";
}
?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Formulario de nueva mascota -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Datos de la nueva mascota
                </h3>
            </div>

            <form method="post" action="" enctype="multipart/form-data" id="formNuevaMascota">
                <div class="card-body">

                    <!-- Información básica -->
                    <div class="row">
                        <!-- Imagen de Perfil -->
                        <div class="col-md-4">
                            <h5 class="mb-3"><i class="fas fa-camera mr-2"></i>Imagen de Perfil</h5>

                            <div class="form-group">
                                <div class="image-upload-container">
                                    <!-- Preview de la imagen -->
                                    <div class="image-preview-wrapper">
                                        <div class="image-preview" id="imagePreview">
                                            <i class="fas fa-camera fa-3x text-muted"></i>
                                            <p class="text-muted mt-2">Selecciona una imagen</p>
                                        </div>
                                    </div>

                                    <!-- Input file oculto -->
                                    <input type="file" id="imageInput" name="imagen_perfil" accept="image/jpeg,image/jpg,image/png" style="display: none;">

                                    <!-- Botones de control -->
                                    <div class="image-controls mt-3">
                                        <button type="button" class="btn btn-primary btn-sm" id="selectImageBtn">
                                            <i class="fas fa-upload mr-1"></i>Seleccionar Imagen
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm" id="cropImageBtn" style="display: none;">
                                            <i class="fas fa-crop mr-1"></i>Recortar
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" id="removeImageBtn" style="display: none;">
                                            <i class="fas fa-trash mr-1"></i>Eliminar
                                        </button>
                                    </div>

                                    <!-- Campo oculto para la imagen procesada -->
                                    <input type="hidden" id="croppedImageData" name="cropped_image_data">

                                    <small class="form-text text-muted mt-2">
                                        Formatos permitidos: JPG, PNG<br>
                                        Tamaño máximo: 5MB<br>
                                        La imagen se recortará en formato cuadrado
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nombre">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="nombre"
                                       name="nombre"
                                       placeholder="Nombre de la mascota"
                                       value="<?=Tools::getValue('nombre')?>"
                                       required>
                                <small class="form-text text-muted">
                                    Nombre oficial de la mascota
                                </small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="alias">
                                    Alias
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="alias"
                                       name="alias"
                                       placeholder="Alias o apodo"
                                       value="<?=Tools::getValue('alias')?>">
                                <small class="form-text text-muted">
                                    Nombre por el que se le conoce comúnmente
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo y género -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo">
                                    Tipo <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="tipo" name="tipo" required>
                                    <option value="">Selecciona el tipo</option>
                                    <?php if (!empty($tipos)): ?>
                                        <?php foreach ($tipos as $tipo): ?>
                                            <option value="<?=$tipo->id?>"
                                                <?=Tools::getValue('tipo') == $tipo->id ? 'selected' : ''?>>
                                                <?=htmlspecialchars($tipo->nombre)?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="genero">
                                    Género <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="genero" name="genero" required>
                                    <option value="">Selecciona el género</option>
                                    <?php if (!empty($generos)): ?>
                                        <?php foreach ($generos as $genero): ?>
                                            <option value="<?=$genero->id?>"
                                                <?=Tools::getValue('genero') == $genero->id ? 'selected' : ''?>>
                                                <?=htmlspecialchars($genero->nombre)?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Raza y cuidador -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="raza">Raza</label>
                                <input type="text"
                                       class="form-control"
                                       id="raza"
                                       name="raza"
                                       placeholder="Indica la raza o razas"
                                       value="<?=Tools::getValue('raza')?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="peso">Peso (kg)</label>
                                <input type="number"
                                       class="form-control"
                                       id="peso"
                                       name="peso"
                                       placeholder="0.0"
                                       step="0.1"
                                       min="0"
                                       value="<?=Tools::getValue('peso')?>">
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nacimiento_fecha">Fecha de nacimiento</label>
                                <input type="date"
                                       class="form-control"
                                       id="nacimiento_fecha"
                                       name="nacimiento_fecha"
                                       value="<?=Tools::getValue('nacimiento_fecha')?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-1">
                                <div class="col-3 col-lg-2">
                                    <div class="form-group mb-0">
                                        <label for="edad">años</label>
                                        <input type="number"
                                               step="1"
                                               min="0"
                                               class="form-control"
                                               id="edad"
                                               name="edad"
                                               value="<?=Tools::getValue('edad')?>">
                                    </div>
                                </div>
                                <div class="col-9 col-lg-10">
                                    <div class="form-group mb-0">
                                        <label for="edad_fecha">el día</label>
                                        <input type="date"
                                               class="form-control"
                                               id="edad_fecha"
                                               name="edad_fecha"
                                               value="<?=Tools::getValue('edad_fecha')?>">
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    Si no conoces la fecha de nacimiento, indica su edad en una fecha concreta
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Esterilización -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="esterilizado">
                                    Esterilizado/Castrado
                                </label>
                                <div class="form-check">
                                    <input type="checkbox"
                                           id="esterilizado"
                                           name="esterilizado"
                                           value="1"
                                           data-bootstrap-switch
                                           data-off-color="danger"
                                           data-on-color="success"
                                           data-off-text="No"
                                           data-on-text="Sí"
                                        <?=Tools::getValue('esterilizado') ? 'checked' : ''?>>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ultimo_celo">Último celo</label>
                                <input type="date"
                                       class="form-control"
                                       id="ultimo_celo"
                                       name="ultimo_celo"
                                       value="<?=Tools::getValue('ultimo_celo')?>">
                                <small class="form-text text-muted">
                                    Solo para hembras no esterilizadas
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Notas -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="notas_internas">Notas internas</label>
                                <textarea class="form-control"
                                          id="notas_internas"
                                          name="notas_internas"
                                          rows="3"
                                          placeholder="Notas para uso interno del equipo"><?=Tools::getValue('notas_internas')?></textarea>
                                <small class="form-text text-muted">
                                    Información privada para el equipo
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea class="form-control"
                                          id="observaciones"
                                          name="observaciones"
                                          rows="3"
                                          placeholder="Observaciones generales sobre la mascota"><?=Tools::getValue('observaciones')?></textarea>
                                <small class="form-text text-muted">
                                    Información general sobre la mascota
                                </small>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Botones de acción -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" name="submitCrearMascota" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Crear Mascota
                            </button>
                            <button type="button" class="btn btn-secondary ml-2" onclick="limpiarFormularioNuevaMascota()">
                                <i class="fas fa-eraser mr-2"></i>
                                Limpiar
                            </button>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="<?=_DOMINIO_.$_SESSION['admin_vars']['entorno']?>mascotas/" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Volver a Mascotas
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Información de ayuda -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Información de ayuda
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-star text-warning mr-2"></i>Campos obligatorios</h5>
                        <ul class="list-unstyled">
                            <li><strong>Nombre:</strong> Nombre oficial de la mascota</li>
                            <li><strong>Tipo:</strong> Perro, gato, etc.</li>
                            <li><strong>Género:</strong> Macho o hembra</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-lightbulb text-info mr-2"></i>Consejos útiles</h5>
                        <ul class="list-unstyled">
                            <li><strong>Slug automático:</strong> Se genera automáticamente basado en el nombre</li>
                            <li><strong>Peso:</strong> Puedes dejarlo vacío y completarlo más tarde</li>
                            <li><strong>Edad:</strong> Ayuda a calcular la edad automáticamente, puedes indicar la fecha de nacimiento o una fecha cualquiera y la edad que tenía en ese día, el sistema calculará su edad según pase el tiempo.</li>
                            <li><strong>Último celo:</strong> Solo relevante para hembras no esterilizadas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>



<!-- Modal para recortar imagen -->
<div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-labelledby="cropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropModalLabel">
                    <i class="fas fa-crop mr-2"></i>Recortar Imagen de Perfil
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="crop-container">
                            <img id="cropImage" style="max-width: 100%;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="preview-container">
                            <h6>Vista Previa:</h6>
                            <div class="preview-wrapper">
                                <div id="cropPreview" class="crop-preview"></div>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                La imagen se recortará en formato cuadrado (1:1)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="confirmCrop">
                    <i class="fas fa-check mr-1"></i>Aplicar Recorte
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(() => {
        // Inicializar el sistema de recorte de imágenes
        const imageCropper = initStandardImageCropper({
            onSuccess: function (dataURL, file) {
                if (dataURL) {
                    console.log('Imagen de perfil procesada correctamente');
                } else {
                    console.log('Imagen de perfil eliminada');
                }
            },
            onError: function (error) {
                console.error('Error procesando imagen de perfil:', error);
                alert('Error al procesar la imagen. Por favor, inténtalo de nuevo.');
            }
        });
    })
</script>