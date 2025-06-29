<?php
/**
 * @var $tutor
 */
?>
<section class="content">
    <div class="container-fluid">
        <form method="post">
            <div class="row">
                <div class="col-12">
                    <div class="row">

                        <!-- Form principal -->
                        <div class="col-12">
                            <div class="row">
                                <input type="hidden" name="id" id="id" value="<?= !empty($tutor) ? $tutor->id : '' ?>">
                                <input type="hidden" name="idmascota" id="idmascota" value="<?= !empty($idmascota) ? $idmascota : '0' ?>">

                                <!-- Alerta de compatibilidad para Contact Picker API -->
                                <div class="col-12 mb-3">
                                    <div id="contactPickerAlert" class="alert alert-info d-none">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Función de contactos disponible:</strong>
                                        Puedes seleccionar un contacto de tu agenda tocando el botón
                                        <i class="fas fa-address-book"></i> junto al campo de nombre.
                                    </div>

                                    <div id="contactPickerUnsupported" class="alert alert-warning d-none">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Función no disponible:</strong>
                                        La selección de contactos no está disponible en este navegador o dispositivo.
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="nombre">Nombre<span class="text-danger ml-2">*</span></label>
                                        <div class="input-group">
                                        <input type="text" name="nombre" id="nombre" class="form-control"
                                               placeholder="Nombre" aria-describedby="nombreHelpId"
                                               value="<?= !empty($tutor) ? $tutor->nombre : '' ?>" required>
                                            <div class="input-group-append">
                                                <button type="button"
                                                        class="btn btn-outline-secondary contact-picker-btn"
                                                        id="selectContactBtn"
                                                        title="Seleccionar desde contactos"
                                                        style="display: none;">
                                                    <i class="fas fa-address-book"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small id="nombreHelpId" class="text-muted">Nombre del tutor</small>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="telefono_1">Teléfono 1<span class="text-danger ml-2">*</span></label>
                                        <input type="tel" name="telefono_1" id="telefono_1" class="form-control"
                                               value="<?= !empty($tutor) ? $tutor->telefono_1 : '' ?>" required>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="telefono_2">Teléfono 2</label>
                                        <input type="tel" name="telefono_2" id="telefono_2" class="form-control"
                                               value="<?= !empty($tutor) ? $tutor->telefono_2 : '' ?>">
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                               placeholder="Email" aria-describedby="emailHelpId"
                                               value="<?= !empty($tutor) ? $tutor->email : '' ?>">
                                        <small id="emailHelpId" class="text-muted">Email del tutor</small>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="notas">Notas</label>
                                        <textarea name="notas" id="notas" class="form-control"><?= !empty($tutor) ? $tutor->notas : '' ?></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end align-items-center">
                            <div class="justify-self-end">
                                <?php if (!empty($tutor) && isset($tutor->id)) : ?>
                                    <button type="submit" name="submitUpdateTutor"
                                            class="btn btn-primary  waves-effect waves-light">Actualizar
                                    </button>
                                    <button type="button" class="btn btn-danger  waves-effect waves-light"
                                            onClick="confirmarEliminacion( <?= $tutor->id ?>, 'Admin', () => window.history.back() )">
                                        Eliminar
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="submitCreateTutor"
                                            class="btn btn-primary  waves-effect waves-light">Crear
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
        <?php if($tutor){?>
            <div class="row">
                <div class="h4">Mascotas asignadas</div>
            </div>
            <div class="row">
                <div class="col-12 col-md 6" id="mascotasAsignadas">

                </div>
                <div class="col-12 col-md 6">
                    <input type="text" class="form-control debouncefunc mb-1" name="busqueda" id="busqueda" placeholder="Busca una mascota para asignarla al tutor" value="" data-function="ajax_get_mascotas_admin" data-idtutor="<?=$tutor ? $tutor->id : ''?>" data-listado="admin_mascotas_list_tutor" data-ifempty="empty">
                    <div id="page-content">

                    </div>
                    <div class="form-group row mt-3">
                        <div class="col-12 text-center">
                            <a href="<?=_DOMINIO_ . $_SESSION['admin_vars']['entorno'] . 'nueva-mascota/for-' . $tutor->id . '/'?>" class="btn btn-outline-secondary">O haz click aquí para crear una nueva</a>
                        </div>
                    </div>
                </div>

            </div>
            <script> ajax_get_mascotas_asignadas('<?=$tutor ? $tutor->id : ''?>'); </script>
        <?php } ?>
    </div>
</section>

<script>
// Contact Picker API functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectContactBtn = document.getElementById('selectContactBtn');
    const nombreInput = document.getElementById('nombre');
    const telefono1Input = document.getElementById('telefono_1');
    const telefono2Input = document.getElementById('telefono_2');
    const emailInput = document.getElementById('email');
    const contactPickerAlert = document.getElementById('contactPickerAlert');
    const contactPickerUnsupported = document.getElementById('contactPickerUnsupported');

    // Verificar si Contact Picker API está disponible
    if ('contacts' in navigator && 'ContactsManager' in window) {
        // API disponible
        contactPickerAlert.classList.remove('d-none');
        selectContactBtn.style.display = 'block';

        console.log('Contact Picker API disponible');

        selectContactBtn.addEventListener('click', async function() {
            try {
                // Verificar qué propiedades están disponibles
                const supportedProperties = await navigator.contacts.getProperties();
                console.log('Propiedades soportadas:', supportedProperties);

                // Definir qué propiedades queremos obtener
                const props = ['name', 'tel'];
                if (supportedProperties.includes('email')) {
                    props.push('email');
                }

                // Opciones para la selección
                const opts = {
                    multiple: false, // Solo un contacto
                    includeNames: true,
                    includeTel: true,
                    includeEmail: supportedProperties.includes('email')
                };

                // Mostrar loading en el botón
                const originalHtml = selectContactBtn.innerHTML;
                selectContactBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                selectContactBtn.disabled = true;

                console.log('Abriendo selector de contactos...');

                // Abrir selector de contactos
                const contacts = await navigator.contacts.select(props, opts);

                console.log('Contactos seleccionados:', contacts);

                if (contacts && contacts.length > 0) {
                    const contact = contacts[0];
                    console.log('Procesando contacto:', contact);

                    // Rellenar nombre
                    if (contact.name && contact.name.length > 0) {
                        nombreInput.value = contact.name[0];
                        console.log('Nombre importado:', contact.name[0]);
                    }

                    // Rellenar teléfono principal
                    if (contact.tel && contact.tel.length > 0) {
                        telefono1Input.value = contact.tel[0];
                        console.log('Teléfono 1 importado:', contact.tel[0]);

                        // Si hay más de un teléfono, usar el segundo para telefono_2
                        if (contact.tel.length > 1) {
                            telefono2Input.value = contact.tel[1];
                            console.log('Teléfono 2 importado:', contact.tel[1]);
                        }
                    }

                    // Rellenar email si está disponible
                    if (contact.email && contact.email.length > 0) {
                        emailInput.value = contact.email[0];
                        console.log('Email importado:', contact.email[0]);
                    }

                    // Mostrar notificación de éxito
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Contacto importado correctamente');
                    } else {
                        alert('Contacto importado correctamente');
                    }
                } else {
                    console.log('No se seleccionó ningún contacto');
                }

            } catch (error) {
                console.error('Error al acceder a los contactos:', error);

                let errorMessage = 'Error al acceder a los contactos';
                if (error.name === 'NotAllowedError') {
                    errorMessage = 'Permiso denegado para acceder a los contactos';
                } else if (error.name === 'NotSupportedError') {
                    errorMessage = 'Esta función no está soportada en tu dispositivo';
                } else if (error.name === 'InvalidStateError') {
                    errorMessage = 'No se puede acceder a los contactos en este momento';
                } else if (error.name === 'SecurityError') {
                    errorMessage = 'Error de seguridad. Asegúrate de estar usando HTTPS';
                }

                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
            } finally {
                // Restaurar botón
                selectContactBtn.innerHTML = originalHtml;
                selectContactBtn.disabled = false;
            }
        });

    } else {
        // API no disponible
        console.log('Contact Picker API no está disponible en este navegador');

        // Verificar si estamos en un dispositivo móvil para mostrar mensaje más específico
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        if (isMobile) {
            contactPickerUnsupported.classList.remove('d-none');
            // Cambiar el mensaje para ser más específico en móviles
            contactPickerUnsupported.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Función no disponible:</strong>
                La selección de contactos requiere Chrome o Edge en Android con HTTPS.
            `;
        }

        selectContactBtn.style.display = 'none';
    }

    // Formateo automático de teléfonos al perder el foco
    telefono1Input.addEventListener('blur', function() {
        formatPhoneNumber(this);
    });

    telefono2Input.addEventListener('blur', function() {
        formatPhoneNumber(this);
    });

    function formatPhoneNumber(input) {
        const value = input.value.trim();
        if (value) {
            // Limpiar espacios extra y caracteres no deseados
            let cleanValue = value.replace(/[^\d\+\-\s$$$$]/g, '');
            // Normalizar espacios
            cleanValue = cleanValue.replace(/\s+/g, ' ');
            input.value = cleanValue;
        }
    }

    // Validación mejorada del formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const nombre = nombreInput.value.trim();
            const telefono1 = telefono1Input.value.trim();

            if (!nombre || !telefono1) {
                e.preventDefault();
                if (typeof toastr !== 'undefined') {
                    toastr.error('Por favor, completa al menos el nombre y teléfono principal');
                } else {
                    alert('Por favor, completa al menos el nombre y teléfono principal');
                }

                // Enfocar el primer campo vacío
                if (!nombre) {
                    nombreInput.focus();
                } else if (!telefono1) {
                    telefono1Input.focus();
                }

                return false;
            }

            return true;
        });
    }
});

</script>
