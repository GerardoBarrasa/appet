// Función para realizar llamadas AJAX
function ajax_call(url, data, callback) {
    // Aseguramos que data sea un objeto
    data = data || {}

    // Añadimos el token CSRF a todas las peticiones
    data.token = static_token

    $.ajax({
        url: url,
        type: "POST",
        data: data,
        dataType: "json", // Especificamos que esperamos JSON
        success: (response) => {
            if (callback && typeof callback === "function") {
                callback(response)
            }
        },
        error: (xhr, status, error) => {
            // Intentar parsear la respuesta como JSON si es posible
            try {
                var jsonResponse = JSON.parse(xhr.responseText)
                if (callback && typeof callback === "function") {
                    callback(jsonResponse)
                }
            } catch (e) {
                // Mostrar mensaje de error con toastr si está disponible
                if (typeof toastr !== "undefined") {
                    toastr.error("Ha ocurrido un error en la comunicación con el servidor")
                }
            }
        },
    })
}

// Función para cerrar modal manualmente
function closeModal(modalId) {
    modalId = modalId || "modalGeneral"
    $("#" + modalId).modal("hide")
}

// Función para cargar mascotas con valores por defecto y paginación
function ajax_get_mascotas_admin(comienzo = 0, limite = 12, pagina = 1) {
    // Validar y convertir a números si es necesario
    comienzo = parseInt(comienzo) || 0;
    limite = parseInt(limite) || 12;
    pagina = parseInt(pagina) || 1;

    let campo = $("#busqueda");
    let listado = 'admin_mascotas_list';
    let idtutor = '';
    let ifempty = '';
    if (campo.data('listado') !== undefined) {
        listado = campo.data('listado');
    }
    if (campo.data('idtutor') !== undefined) {
        idtutor = campo.data('idtutor');
    }
    if (campo.data('ifempty') !== undefined) {
        ifempty = campo.data('ifempty');
    }

    $(".loadingscr").removeClass("d-none")

    ajax_call(
        dominio + "adminajax/ajax-get-mascotas-admin/",
        {
            comienzo: comienzo,
            limite: limite,
            pagina: pagina,
            busqueda: campo.val() || "",
            listado: listado,
            idtutor: idtutor,
            ifempty: ifempty,
        },
        (response) => {
            // Si la respuesta es un string, intentar parsearlo
            if (typeof response === "string") {
                try {
                    response = JSON.parse(response)
                } catch (e) {
                    if (typeof toastr !== "undefined") {
                        toastr.error("Error en el formato de respuesta del servidor")
                    }
                    $(".loadingscr").addClass("d-none")
                    return
                }
            }

            if (response && response.type === "success") {
                $("#page-content").html(response.html);
                let total = response.total || 0;
                $(".totalfound").empty().html(total+' resultado'+(total !== 1 ? 's' : ''))
                // Generar paginador si hay información de paginación
                if (response.pagination) {
                  generarPaginador(response.pagination, limite, 'ajax_get_mascotas_admin')
                }
                else{
                    $(".paginador").html("") // Limpiar paginador si no hay paginación
                }
            } else {
                if (typeof toastr !== "undefined") {
                    toastr.error(response.error || response.html || "Error al cargar las mascotas")
                }
            }
            $(".loadingscr").addClass("d-none")
        },
    )
}

// Función para cargar usuarios con valores por defecto y paginación
function ajax_get_usuarios_admin(comienzo = 0, limite = 20, pagina = 1) {
    // Validar y convertir a números si es necesario
    comienzo = parseInt(comienzo) || 0;
    limite = parseInt(limite) || 20;
    pagina = parseInt(pagina) || 1;

    $(".loadingscr").removeClass("d-none")

    ajax_call(
        dominio + "adminajax/ajax-get-usuarios-admin/",
        {
            comienzo: comienzo,
            limite: limite,
            pagina: pagina,
            busqueda: $("#busqueda").val() || "",
        },
        (response) => {
            // Si la respuesta es un string, intentar parsearlo
            if (typeof response === "string") {
                try {
                    response = JSON.parse(response)
                } catch (e) {
                    if (typeof toastr !== "undefined") {
                        toastr.error("Error en el formato de respuesta del servidor")
                    }
                    $(".loadingscr").addClass("d-none")
                    return
                }
            }

            if (response && response.type === "success") {
                $("#page-content").html(response.html);
                let total = response.total || 0;
                $(".totalfound").empty().html(total+' resultado'+(total !== 1 ? 's' : ''))
                // Generar paginador si hay información de paginación
                if (response.pagination) {
                    generarPaginador(response.pagination, limite, 'ajax_get_usuarios_admin')
                }
                else{
                    $(".paginador").html("") // Limpiar paginador si no hay paginación
                }
            } else {
                if (typeof toastr !== "undefined") {
                    toastr.error(response.error || response.html || "Error al cargar los usuarios")
                }
            }
            $(".loadingscr").addClass("d-none")
        },
    )
}

// Función para cargar tutores con valores por defecto y paginación
function ajax_get_tutores(comienzo = 0, limite = 20, pagina = 1) {
    // Validar y convertir a números si es necesario
    comienzo = parseInt(comienzo) || 0;
    limite = parseInt(limite) || 20;
    pagina = parseInt(pagina) || 1;

    $(".loadingscr").removeClass("d-none")

    let campo = $("#busqueda");
    let listado = 'admin_tutores_list';
    let idmascota = '';
    let ifempty = '';

    if (campo.data('listado') !== undefined) {
        listado = campo.data('listado');
    }
    if (campo.data('idmascota') !== undefined) {
        idmascota = campo.data('idmascota');
    }
    if (campo.data('ifempty') !== undefined) {
        ifempty = campo.data('ifempty');
    }

    ajax_call(
        dominio + "adminajax/ajax-get-tutores/",
        {
            comienzo: comienzo,
            limite: limite,
            pagina: pagina,
            listado: listado,
            idmascota: idmascota,
            ifempty: ifempty,
            busqueda: $("#busqueda").val() || "",
        },
        (response) => {
            // Si la respuesta es un string, intentar parsearlo
            if (typeof response === "string") {
                try {
                    response = JSON.parse(response)
                } catch (e) {
                    if (typeof toastr !== "undefined") {
                        toastr.error("Error en el formato de respuesta del servidor")
                    }
                    $(".loadingscr").addClass("d-none")
                    return
                }
            }

            if (response && response.type === "success") {
                $("#page-content").html(response.html);
                let total = response.total || 0;
                $(".totalfound").empty().html(total+' resultado'+(total !== 1 ? 's' : ''))
                // Generar paginador si hay información de paginación
                if (response.pagination) {
                    generarPaginador(response.pagination, limite, 'ajax_get_tutores')
                }
                else{
                    $(".paginador").html("") // Limpiar paginador si no hay paginación
                }
            } else {
                if (typeof toastr !== "undefined") {
                    toastr.error(response.error || response.html || "Error al cargar los tutores")
                }
            }
            $(".loadingscr").addClass("d-none")
        },
    )
}

/**
 * Genera el HTML del paginador
 */
function generarPaginador(paginacion, limite, funcion) {
    // No mostrar paginador si no hay paginación o solo hay una página
    if (!paginacion || paginacion.total_paginas <= 1) {
        $(".paginador").html("")
        return
    }

    let html = '<nav class="paginador"><ul class="pagination justify-content-center m-0">'

    // Botón anterior - solo mostrar si no estamos en la primera página
    if (paginacion.tiene_anterior) {
        html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="cambiarPagina(${paginacion.pagina_anterior}, ${limite}, ${funcion}); return false;" title="Página anterior">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                 </li>`
    }

    // Páginas
    paginacion.paginas.forEach((pagina) => {
        if (pagina.separador) {
            html += `<li class="page-item disabled">
                        <span class="page-link">...</span>
                     </li>`
        }

        if (pagina.activa) {
            html += `<li class="page-item active">
                        <span class="page-link">${pagina.numero}</span>
                     </li>`
        } else {
            html += `<li class="page-item">
                        <a class="page-link" href="#" onclick="cambiarPagina(${pagina.numero}, ${limite}, ${funcion}); return false;" title="Ir a página ${pagina.numero}">
                            ${pagina.numero}
                        </a>
                     </li>`
        }
    })

    // Botón siguiente - solo mostrar si no estamos en la última página
    if (paginacion.tiene_siguiente) {
        html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="cambiarPagina(${paginacion.pagina_siguiente}, ${limite}, ${funcion}); return false;" title="Página siguiente">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                 </li>`
    }

    html += "</ul></nav>"

    // Información de registros
    const inicio = (paginacion.pagina_actual - 1) * paginacion.registros_por_pagina + 1
    const fin = Math.min(paginacion.pagina_actual * paginacion.registros_por_pagina, paginacion.total_registros)

    html += `<div class="pagination-info text-center mt-2">
                <small class="text-muted">
                    Mostrando ${inicio}-${fin} de ${paginacion.total_registros} registros
                </small>
             </div>`

    // Insertar en el DOM
    $(".paginador").html(html)
}

/**
 * Cambia a una página específica
 */
function cambiarPagina(pagina, limite, funcion) {
    const comienzo = (pagina - 1) * limite
    funcion(comienzo, limite, pagina)
}

// Función para abrir modal general
function modalGeneral(element) {
    $(".loadingscr").removeClass("d-none")

    var data = {
        type: $(element).data("type"),
        content: $(element).data("content"),
        id: $(element).data("id"),
    }

    ajax_call(dominio + "adminajax/ajax-contenido-modal/", data, (response) => {
        // Si la respuesta es un string, intentar parsearlo
        if (typeof response === "string") {
            try {
                response = JSON.parse(response)
            } catch (e) {
                if (typeof toastr !== "undefined") {
                    toastr.error("Error en el formato de respuesta del servidor")
                }
                $(".loadingscr").addClass("d-none")
                return
            }
        }

        if (response && response.type === "success") {
            $(".modal-content.corporate").html(response.html)
            $("#modalGeneral").modal("show")
        } else {
            if (typeof toastr !== "undefined") {
                toastr.error(response.error || response.html || "Error al cargar el contenido del modal")
            }
        }
        $(".loadingscr").addClass("d-none")
    })
}

// Función para guardar evaluación
function saveEvaluation(idmascota, evaluationClass) {
    $(".loadingscr").removeClass("d-none")

    let valorSlider = 0;
    let maxSlider = 0;
    let tag = '';
    let tagspan = '';

    var data = {
        idmascota: idmascota,
        evaluations: [],
    }

    // Recopilar todos los valores de evaluación
    $("." + evaluationClass).each(function () {
        let tipo = $(this).data("crtype");
        let crid = $(this).data("crid");
        if(tipo === 'escala'){
            valorSlider = $(this).val();
            maxSlider   = $(this).data("slider-max");
            tagspan = $(".caracteristicaTag_" + crid);
        }
        data.evaluations.push({
            id: crid,
            type: tipo,
            value: $(this).val(),
        })
    })

    ajax_call(dominio + "adminajax/ajax-save-mascota-evaluation/", data, (response) => {
        // Si la respuesta es un string, intentar parsearlo
        if (typeof response === "string") {
            try {
                response = JSON.parse(response)
            } catch (e) {
                if (typeof toastr !== "undefined") {
                    toastr.error("Error en el formato de respuesta del servidor")
                }
                $(".loadingscr").addClass("d-none")
                return
            }
        }

        if (response && response.type === "success") {
            if (typeof toastr !== "undefined") {
                toastr.success("Evaluación guardada correctamente")
            }
            // Ocultar botones de guardar
            $(".save_" + evaluationClass.split("_")[1]).addClass("d-none")

            // Actualizar valores originales
            $("." + evaluationClass).each(function () {
                $(this).data("orig", $(this).val())
            })
            // Actualizar valor de etiqueta
            if(valorSlider > 0){
                tag = valorSlider+"/"+maxSlider
            }
            if (tag !== '') {
                tagspan.empty().html(tag)
            }
        } else {
            if (typeof toastr !== "undefined") {
                toastr.error(response.error || response.html || "Error al guardar la evaluación")
            }
        }
        $(".loadingscr").addClass("d-none")
    })
}

// Función para comprobar cambios en evaluaciones
function compruebaCambios(element) {
    var orig = $(element).data("orig")
    var current = $(element).val()
    var saveBtn = $(element).data("savebtn")

    if (orig != current) {
        $("." + saveBtn).removeClass("d-none")
    } else {
        // Verificar si hay otros elementos que necesitan guardar
        var needsSave = false
        $('.detchng[data-savebtn="' + saveBtn + '"]').each(function () {
            if ($(this).data("orig") != $(this).val()) {
                needsSave = true
                return false // Salir del bucle
            }
        })

        if (!needsSave) {
            $("." + saveBtn).addClass("d-none")
        }
    }
}

// Función para guardar datos de formulario (adaptada para usar ajax_call original)
function saveData(formName) {
    const loadingscr = $(".loadingscr")
    loadingscr.removeClass("d-none")

    var form = $("#" + formName)[0]

    // Convertir FormData a objeto JavaScript compatible con ajax_call
    var data = serializeFormToObject(form)

    // Debug: mostrar los datos que se van a enviar
    console.log("Datos a enviar:", data)

    ajax_call(dominio + "adminajax/ajax-save-data/", data, (response) => {
        // Si la respuesta es un string, intentar parsearlo
        if (typeof response === "string") {
            try {
                response = JSON.parse(response)
            } catch (e) {
                if (typeof toastr !== "undefined") {
                    toastr.error("Error en el formato de respuesta del servidor")
                }
                loadingscr.addClass("d-none")
                return
            }
        }

        if (response && response.type === "success") {
            if (typeof toastr !== "undefined") {
                toastr.success("Datos guardados correctamente")
            }
            // Cerrar modal si existe
            closeModal("modalGeneral")
            // Si viene en la respuesta un parámetro url no vacío, redirigimos
            if (response.url) {
                window.location.href = response.url
            }
            if(response.reload) {
                // Recargar la página tras medio segundo
                setTimeout(() => {
                    location.reload()
                }, 500)
            }
        } else {
            if (typeof toastr !== "undefined") {
                toastr.error(response.error || response.html || "Error al guardar los datos")
            }
        }
        loadingscr.addClass("d-none")
    })
}


// Función para convertir FormData a objeto JavaScript
function formDataToObject(formData) {
    var object = {}

    formData.forEach((value, key) => {
        // Verificar si la clave ya existe
        if (object.hasOwnProperty(key)) {
            // Si ya existe, convertir a array o añadir al array existente
            if (!Array.isArray(object[key])) {
                object[key] = [object[key]]
            }
            object[key].push(value)
        } else {
            // Si es la primera vez que vemos esta clave
            object[key] = value
        }
    })

    return object
}

// Función alternativa para serializar formulario (más completa)
function serializeFormToObject(form) {
    var formData = new FormData(form)
    var object = {}

    // Procesar todos los campos del formulario
    $(form)
        .find("input, select, textarea")
        .each(function () {
            var $field = $(this)
            var name = $field.attr("name")
            var type = $field.attr("type")

            if (!name) return // Saltar campos sin nombre

            switch (type) {
                case "checkbox":
                    if ($field.is(":checked")) {
                        if (object[name]) {
                            // Si ya existe, convertir a array
                            if (!Array.isArray(object[name])) {
                                object[name] = [object[name]]
                            }
                            object[name].push($field.val())
                        } else {
                            object[name] = $field.val()
                        }
                    }
                    break

                case "radio":
                    if ($field.is(":checked")) {
                        object[name] = $field.val()
                    }
                    break

                case "file":
                    // Para archivos, podríamos necesitar un manejo especial
                    // Por ahora, solo incluimos el nombre del archivo
                    if ($field[0].files && $field[0].files.length > 0) {
                        object[name] = $field[0].files[0].name
                    }
                    break

                default:
                    // text, email, password, hidden, textarea, select, etc.
                    object[name] = $field.val()
                    break
            }
        })

    return object
}

// Inicialización cuando el documento está listo
$(document).ready(() => {
    // Inicializar sliders si existen
    if ($(".slider").length > 0) {
        $(".slider").slider()
    }

    $(".debouncefunc").on('keyup', $.debounce(750, function(e) {
        var functionName = $(this).data('function');

        if (typeof window[functionName] === 'function') {
            window[functionName](); // Ejecuta la función
        }
    }));

    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip()

    // Configuración de toastr si está disponible
    if (typeof toastr !== "undefined") {
        toastr.options = {
            closeButton: true,
            debug: false,
            newestOnTop: false,
            progressBar: true,
            positionClass: "toast-top-right",
            preventDuplicates: false,
            onclick: null,
            showDuration: "300",
            hideDuration: "1000",
            timeOut: "5000",
            extendedTimeOut: "1000",
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut",
        }
    }

    // Event listener para cerrar modales manualmente
    $(document).on("click", '[data-dismiss="modal"], [data-bs-dismiss="modal"]', function (e) {
        e.preventDefault()
        var modalId = $(this).closest(".modal").attr("id") || "modalGeneral"
        closeModal(modalId)
    })

    // Event listener para cerrar modal al hacer clic fuera
    $(document).on("click", ".modal", function (e) {
        if (e.target === this) {
            var modalId = $(this).attr("id") || "modalGeneral"
            closeModal(modalId)
        }
    })

    // Event listener para cerrar modal con tecla Escape
    $(document).on("keydown", (e) => {
        if (e.key === "Escape" || e.keyCode === 27) {
            if ($(".modal.show").length > 0) {
                var modalId = $(".modal.show").attr("id") || "modalGeneral"
                closeModal(modalId)
            }
        }
    })

    // Solo inicializar si estamos en la página de nueva mascota
    if (document.getElementById("formNuevaMascota")) {
        initNuevaMascotaEvents()
    }
    $("input[data-bootstrap-switch]").each(function(){
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    })

    mostrarAlertasPHP()
})


// ========================================
// FUNCIONALIDAD DE RECORTE DE IMÁGENES
// ========================================

/**
 * Inicializa el sistema de recorte de imágenes
 * @param {Object} config - Configuración personalizada
 * @param {string} config.imageInputId - ID del input file
 * @param {string} config.selectBtnId - ID del botón seleccionar
 * @param {string} config.cropBtnId - ID del botón recortar
 * @param {string} config.removeBtnId - ID del botón eliminar
 * @param {string} config.previewId - ID del contenedor preview
 * @param {string} config.modalId - ID del modal de recorte
 * @param {string} config.cropImageId - ID de la imagen a recortar
 * @param {string} config.cropPreviewId - ID del preview del recorte
 * @param {string} config.confirmBtnId - ID del botón confirmar
 * @param {string} config.hiddenInputId - ID del input hidden para datos
 * @param {Object} config.validation - Configuración de validación
 * @param {Function} config.onSuccess - Callback cuando se recorta exitosamente
 * @param {Function} config.onError - Callback cuando hay error
 */
function initImageCropper(config = {}) {
    // Verificar que Cropper.js esté disponible
    if (typeof Cropper === "undefined") {
        console.error("Cropper.js no está cargado. Asegúrate de incluir la librería.")
        return false
    }

    // Configuración por defecto
    const defaultConfig = {
        imageInputId: "imageInput",
        selectBtnId: "selectImageBtn",
        cropBtnId: "cropImageBtn",
        removeBtnId: "removeImageBtn",
        previewId: "imagePreview",
        modalId: "cropModal",
        cropImageId: "cropImage",
        cropPreviewId: "cropPreview",
        confirmBtnId: "confirmCrop",
        hiddenInputId: "croppedImageData",
        validation: {
            maxFileSize: 5 * 1024 * 1024, // 5MB
            allowedTypes: ["image/jpeg", "image/jpg", "image/png"],
            messages: {
                invalidType: "Por favor selecciona una imagen en formato JPG o PNG.",
                fileTooBig: "El archivo es demasiado grande. El tamaño máximo permitido es 5MB.",
                confirmDelete: "¿Estás seguro de que quieres eliminar la imagen?",
            },
        },
        cropOptions: {
            aspectRatio: 1,
            viewMode: 2,
            dragMode: "move",
            autoCropArea: 0.8,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            minContainerWidth: 300,
            minContainerHeight: 300,
        },
        outputOptions: {
            width: 400,
            height: 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: "high",
            format: "image/jpeg",
            quality: 0.9,
        },
        onSuccess: null,
        onError: null,
    }

    // Combinar configuración
    const settings = Object.assign({}, defaultConfig, config)
    settings.validation = Object.assign({}, defaultConfig.validation, config.validation || {})
    settings.cropOptions = Object.assign({}, defaultConfig.cropOptions, config.cropOptions || {})
    settings.outputOptions = Object.assign({}, defaultConfig.outputOptions, config.outputOptions || {})

    // Obtener elementos del DOM
    const elements = {
        imageInput: document.getElementById(settings.imageInputId),
        selectBtn: document.getElementById(settings.selectBtnId),
        cropBtn: document.getElementById(settings.cropBtnId),
        removeBtn: document.getElementById(settings.removeBtnId),
        preview: document.getElementById(settings.previewId),
        modal: $("#" + settings.modalId),
        cropImage: document.getElementById(settings.cropImageId),
        cropPreview: document.getElementById(settings.cropPreviewId),
        confirmBtn: document.getElementById(settings.confirmBtnId),
        hiddenInput: document.getElementById(settings.hiddenInputId),
    }

    // Verificar que todos los elementos existan
    const missingElements = []
    Object.keys(elements).forEach((key) => {
        if (!elements[key] && key !== "cropBtn" && key !== "removeBtn") {
            missingElements.push(settings[key + "Id"] || key)
        }
    })

    if (missingElements.length > 0) {
        console.error("Elementos no encontrados:", missingElements)
        if (settings.onError) {
            settings.onError("Elementos del DOM no encontrados: " + missingElements.join(", "))
        }
        return false
    }

    // Variables del cropper
    let cropper = null
    let currentFile = null

    // Configurar preview del cropper
    if (elements.cropPreview) {
        settings.cropOptions.preview = elements.cropPreview
    }

    // Event Listeners

    // Abrir selector de archivos
    if (elements.selectBtn) {
        elements.selectBtn.addEventListener("click", () => {
            elements.imageInput.click()
        })
    }

    // Manejar selección de archivo
    elements.imageInput.addEventListener("change", (e) => {
        const file = e.target.files[0]
        if (!file) return

        // Validar tipo de archivo
        if (!settings.validation.allowedTypes.includes(file.type)) {
            alert(settings.validation.messages.invalidType)
            if (settings.onError) {
                settings.onError("Tipo de archivo no válido: " + file.type)
            }
            return
        }

        // Validar tamaño de archivo
        if (file.size > settings.validation.maxFileSize) {
            alert(settings.validation.messages.fileTooBig)
            if (settings.onError) {
                settings.onError("Archivo demasiado grande: " + file.size + " bytes")
            }
            return
        }

        currentFile = file
        loadImageForCrop(file)
    })

    // Cargar imagen para recortar
    function loadImageForCrop(file) {
        const reader = new FileReader()
        reader.onload = (e) => {
            elements.cropImage.src = e.target.result
            elements.modal.modal("show")
        }
        reader.readAsDataURL(file)
    }

    // Inicializar cropper cuando se abre el modal
    elements.modal.on("shown.bs.modal", () => {
        if (cropper) {
            cropper.destroy()
        }

        cropper = new Cropper(elements.cropImage, settings.cropOptions)
    })

    // Limpiar cropper cuando se cierra el modal
    elements.modal.on("hidden.bs.modal", () => {
        if (cropper) {
            cropper.destroy()
            cropper = null
        }
    })

    // Confirmar recorte
    elements.confirmBtn.addEventListener("click", () => {
        if (!cropper) return

        try {
            // Obtener datos del recorte
            const canvas = cropper.getCroppedCanvas(settings.outputOptions)

            // Convertir a base64
            const croppedDataURL = canvas.toDataURL(settings.outputOptions.format, settings.outputOptions.quality)

            // Guardar datos de la imagen recortada
            // Guardar datos de la imagen recortada
            if (elements.hiddenInput) {
                elements.hiddenInput.value = croppedDataURL
            }

            // Mostrar preview
            if (elements.preview) {
                showImagePreview(croppedDataURL)
            }

            // Cerrar modal
            elements.modal.modal("hide")

            // Mostrar controles
            if (elements.cropBtn) elements.cropBtn.style.display = "inline-block"
            if (elements.removeBtn) elements.removeBtn.style.display = "inline-block"

            // Callback de éxito
            if (settings.onSuccess) {
                settings.onSuccess(croppedDataURL, currentFile)
            }
        } catch (error) {
            console.error("Error al procesar la imagen:", error)
            if (settings.onError) {
                settings.onError("Error al procesar la imagen: " + error.message)
            }
        }
    })

    // Mostrar preview de la imagen
    function showImagePreview(dataURL) {
        if (elements.preview) {
            elements.preview.innerHTML = `<img src="${dataURL}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">`
        }
    }

    // Botón para recortar de nuevo
    if (elements.cropBtn) {
        elements.cropBtn.addEventListener("click", () => {
            if (currentFile) {
                loadImageForCrop(currentFile)
            }
        })
    }

    // Botón para eliminar imagen
    if (elements.removeBtn) {
        elements.removeBtn.addEventListener("click", () => {
            if (confirm(settings.validation.messages.confirmDelete)) {
                resetImageUpload()
            }
        })
    }

    // Resetear upload de imagen
    function resetImageUpload() {
        elements.imageInput.value = ""
        if (elements.hiddenInput) {
            elements.hiddenInput.value = ""
        }
        currentFile = null

        if (elements.preview) {
            elements.preview.innerHTML = `
            <i class="fas fa-camera fa-3x text-muted"></i>
            <p class="text-muted mt-2">Selecciona una imagen</p>
        `
        }

        if (elements.cropBtn) elements.cropBtn.style.display = "none"
        if (elements.removeBtn) elements.removeBtn.style.display = "none"

        // Callback de eliminación
        if (settings.onSuccess) {
            settings.onSuccess(null, null)
        }
    }

    // Métodos públicos
    return {
        reset: resetImageUpload,
        getCurrentFile: () => currentFile,
        getCroppedData: () => (elements.hiddenInput ? elements.hiddenInput.value : null),
        showPreview: showImagePreview,
        destroy: () => {
            if (cropper) {
                cropper.destroy()
                cropper = null
            }
        },
    }
}

// Función de conveniencia para inicializar con configuración estándar
function initStandardImageCropper(callbacks = {}) {
    return initImageCropper({
        onSuccess:
            callbacks.onSuccess ||
            ((dataURL, file) => {
                console.log("Imagen procesada correctamente")
            }),
        onError:
            callbacks.onError ||
            ((error) => {
                console.error("Error en el procesamiento de imagen:", error)
            }),
    })
}



// ========================================
// FUNCIONALIDAD DE IMAGEN DE PERFIL DE MASCOTA
// ========================================

/**
 * Inicializa el sistema de cambio de imagen de perfil para mascotas
 * @param {Object} config - Configuración personalizada
 */
function initProfileImageCropper(config = {}) {
    // Verificar que estamos en la página correcta
    if (!document.getElementById("profileImage")) {
        return false
    }

    // Configuración por defecto
    const defaultConfig = {
        mascotaId: null,
        imageInputId: "profileImageInput",
        profileImageId: "profileImage",
        cropModalId: "profileCropModal",
        cropImageId: "profileCropImage",
        cropPreviewId: "profileCropPreview",
        confirmBtnId: "profileConfirmCrop",
        loadingId: "profileImageLoading",
        ajaxUrl: null,
        validation: {
            maxFileSize: 5 * 1024 * 1024, // 5MB
            allowedTypes: ["image/jpeg", "image/jpg", "image/png"],
        },
    }

    // Combinar configuración
    const settings = Object.assign({}, defaultConfig, config)
    settings.validation = Object.assign({}, defaultConfig.validation, config.validation || {})

    // Verificar configuración requerida
    if (!settings.mascotaId) {
        console.error("Se requiere el ID de la mascota")
        return false
    }

    if (!settings.ajaxUrl) {
        console.error("Se requiere la URL del endpoint AJAX")
        return false
    }

    // Variables globales para el cropper de perfil
    let profileCropper = null
    let currentProfileFile = null

    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip()

    // Función para cambiar imagen de perfil
    window.changeProfileImage = () => {
        $("#" + settings.imageInputId).click()
    }

    // Manejar selección de archivo
    $("#" + settings.imageInputId).on("change", (e) => {
        const file = e.target.files[0]
        if (!file) return

        // Validar tipo de archivo
        if (!settings.validation.allowedTypes.includes(file.type)) {
            toastr.error("Por favor selecciona una imagen en formato JPG o PNG.")
            return
        }

        // Validar tamaño de archivo
        if (file.size > settings.validation.maxFileSize) {
            toastr.error("El archivo es demasiado grande. El tamaño máximo permitido es 5MB.")
            return
        }

        currentProfileFile = file
        loadProfileImageForCrop(file)
    })

    // Cargar imagen para recortar
    function loadProfileImageForCrop(file) {
        const reader = new FileReader()
        reader.onload = (e) => {
            $("#" + settings.cropImageId).attr("src", e.target.result)
            $("#" + settings.cropModalId).modal("show")
        }
        reader.readAsDataURL(file)
    }

    // Inicializar cropper cuando se abre el modal
    $("#" + settings.cropModalId).on("shown.bs.modal", () => {
        if (profileCropper) {
            profileCropper.destroy()
        }

        profileCropper = new Cropper(document.getElementById(settings.cropImageId), {
            aspectRatio: 1, // Relación de aspecto cuadrada
            viewMode: 2,
            dragMode: "move",
            autoCropArea: 0.8,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            minContainerWidth: 300,
            minContainerHeight: 300,
            preview: "#" + settings.cropPreviewId,
        })
    })

    // Limpiar cropper cuando se cierra el modal
    $("#" + settings.cropModalId).on("hidden.bs.modal", () => {
        if (profileCropper) {
            profileCropper.destroy()
            profileCropper = null
        }
    })

    // Confirmar recorte y enviar por AJAX
    $("#" + settings.confirmBtnId).on("click", () => {
        if (!profileCropper) return

        try {
            // Mostrar loading
            $("#" + settings.loadingId).show()
            $("#" + settings.cropModalId).modal("hide")

            // Obtener datos del recorte
            const canvas = profileCropper.getCroppedCanvas({
                width: 400,
                height: 400,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: "high",
            })

            // Convertir a base64
            const croppedDataURL = canvas.toDataURL("image/jpeg", 0.9)

            // Enviar por AJAX
            $.ajax({
                url: settings.ajaxUrl,
                type: "POST",
                data: {
                    mascota_id: settings.mascotaId,
                    image_data: croppedDataURL,
                },
                success: (response) => {
                    $("#" + settings.loadingId).hide()

                    if (response && response.type === "success") {
                        toastr.success("Imagen de perfil actualizada correctamente")
                        setTimeout(() => {
                            location.reload()
                        }, 500)
                    } else {
                        toastr.error(response.message || "Error al actualizar la imagen")
                    }
                },
                error: (xhr, status, error) => {
                    $("#" + settings.loadingId).hide()
                    console.error("Error AJAX:", error)
                    toastr.error("Error al comunicarse con el servidor")
                },
            })
        } catch (error) {
            $("#" + settings.loadingId).hide()
            console.error("Error al procesar la imagen:", error)
            toastr.error("Error al procesar la imagen")
        }
    })

    return {
        destroy: () => {
            if (profileCropper) {
                profileCropper.destroy()
                profileCropper = null
            }
        },
    }
}

// Inicializar el cropper de perfil si estamos en la página de mascota
$(document).ready(() => {
    // Verificar si estamos en la página de mascota
    if (document.getElementById("profileImage")) {
        // Obtener el ID de la mascota del elemento
        const mascotaId = document.getElementById("profileImage").getAttribute("data-mascota-id")

        if (mascotaId) {
            initProfileImageCropper({
                mascotaId: mascotaId,
                ajaxUrl: dominio + "adminajax/ajax-update-profile-image/",
            })
        }
    }
})

/**
 * Funciones para la página de nueva mascota
 */

// Función para limpiar el formulario de nueva mascota
function limpiarFormularioNuevaMascota() {
    if (confirm("¿Estás seguro de que quieres limpiar todos los campos?")) {
        document.getElementById("formNuevaMascota").reset()
        // Ejecutar toggle después de limpiar para resetear visibilidad de campos
        toggleUltimoCelo()
    }
}

// Validación del formulario de nueva mascota
function validarFormularioNuevaMascota(e) {
    const nombre = document.getElementById("nombre").value.trim()
    const tipo = document.getElementById("tipo").value
    const genero = document.getElementById("genero").value

    if (!nombre || !tipo || !genero) {
        e.preventDefault()
        if (typeof toastr !== "undefined") {
            toastr.error("Por favor, completa todos los campos obligatorios marcados con *")
        } else {
            alert("Por favor, completa todos los campos obligatorios marcados con *")
        }
        return false
    }
    return true
}



// Inicializar eventos para nueva mascota
function initNuevaMascotaEvents() {
    // Validación del formulario
    const form = document.getElementById("formNuevaMascota")
    if (form) {
        form.addEventListener("submit", validarFormularioNuevaMascota)
    }
}

/**
 * Función mejorada para mostrar múltiples alertas desde PHP
 * Ahora maneja un array de alertas en lugar de una sola
 */
function mostrarAlertasPHP() {
    // Verificar si existen alertas en la variable global
    if (typeof window.alertas_php !== "undefined" && window.alertas_php && Array.isArray(window.alertas_php)) {
        // Configurar toastr si está disponible
        if (typeof toastr !== "undefined") {
            toastr.options = {
                closeButton: true,
                timeOut: 5000,
                extendedTimeOut: 2000,
                positionClass: "toast-top-right",
                preventDuplicates: false,
                newestOnTop: true,
                progressBar: true,
                showDuration: 300,
                hideDuration: 1000,
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut",
            }

            // Mostrar cada alerta con un pequeño delay para que se vean todas
            window.alertas_php.forEach((alerta, index) => {
                setTimeout(() => {
                    // Mostrar según el tipo
                    switch (alerta.type) {
                        case "success":
                            toastr.success(alerta.message)
                            break
                        case "warning":
                            toastr.warning(alerta.message)
                            break
                        case "info":
                            toastr.info(alerta.message)
                            break
                        case "error":
                        default:
                            toastr.error(alerta.message)
                            break
                    }
                }, index * 200) // Delay de 200ms entre cada alerta
            })
        } else {
            // Fallback si no hay toastr - mostrar todas las alertas concatenadas
            const mensajes = window.alertas_php
                .map((alerta) => `${alerta.type.toUpperCase()}: ${alerta.message}`)
                .join("\n\n")
            alert(mensajes)
        }

        // Limpiar las alertas después de mostrarlas para evitar que se muestren de nuevo
        window.alertas_php = null
        delete window.alertas_php
    }

    // Mantener compatibilidad con el sistema anterior (una sola alerta)
    else if (typeof window.alerta_php !== "undefined" && window.alerta_php) {
        const alerta = window.alerta_php

        if (typeof toastr !== "undefined") {
            // Configurar toastr básico
            toastr.options = {
                closeButton: true,
                timeOut: 5000,
                positionClass: "toast-top-right",
            }

            // Mostrar según el tipo
            if (alerta.type === "success") {
                toastr.success(alerta.message)
            } else if (alerta.type === "warning") {
                toastr.warning(alerta.message)
            } else if (alerta.type === "info") {
                toastr.info(alerta.message)
            } else {
                toastr.error(alerta.message)
            }
        } else {
            // Fallback si no hay toastr
            alert(alerta.message)
        }

        // Limpiar la alerta
        window.alerta_php = null
        delete window.alerta_php
    }
}

function asignarMascota(este){
    let idmascota = $(este).data("idmascota");
    let idtutor = $(este).data("idtutor");
    let action = $(este).data("add");
    if ($(este).data('action') !== undefined) {
        action = $(este).data('action');
    }
    $(".loadingscr").removeClass("d-none");
    ajax_call(dominio + "adminajax/ajax-asignar-mascota/", { idmascota: idmascota, idtutor: idtutor, action: action },
        (response) => {
            // Si la respuesta es un string, intentar parsearlo
            if (typeof response === "string") {
                try {
                    response = JSON.parse(response)
                } catch (e) {
                    if (typeof toastr !== "undefined") {
                        toastr.error("Error en el formato de respuesta del servidor")
                    }
                    $(".loadingscr").addClass("d-none")
                    return
                }
            }

            if (response && response.type === "success") {
                if (typeof toastr !== "undefined") {
                    toastr.success(response.message || "Mascota asignada correctamente")
                }
                // Recargar la lista de mascotas asignadas
                ajax_get_mascotas_asignadas(idtutor);
                // Recargar la lista de tutores asignados
                ajax_get_tutores_asignados(idmascota);
            } else {
                if (typeof toastr !== "undefined") {
                    toastr.error(response.error || response.html || "Error al asignar la mascota")
                }
            }
            $(".loadingscr").addClass("d-none")
        },
    )
}

function ajax_get_mascotas_asignadas(idtutor){
    $(".loadingscr").removeClass("d-none");

    ajax_call(dominio + "adminajax/ajax-get-mascotas-asignadas/", { idtutor: idtutor }, (response) => {
        // Si la respuesta es un string, intentar parsearlo
        if (typeof response === "string") {
            try {
                response = JSON.parse(response)
            } catch (e) {
                if (typeof toastr !== "undefined") {
                    toastr.error("Error en el formato de respuesta del servidor")
                }
                $(".loadingscr").addClass("d-none")
                return
            }
        }

        if (response && response.type === "success") {
            $("#mascotasAsignadas").html(response.html);
        } else {
            if (typeof toastr !== "undefined") {
                toastr.error(response.error || response.html || "Error al cargar las mascotas asignadas")
            }
        }
        $(".loadingscr").addClass("d-none");
    });
}

function ajax_get_tutores_asignados(idmascota){
    $(".loadingscr").removeClass("d-none");

    ajax_call(dominio + "adminajax/ajax-get-tutores-asignados/", { idmascota: idmascota }, (response) => {
        // Si la respuesta es un string, intentar parsearlo
        if (typeof response === "string") {
            try {
                response = JSON.parse(response)
            } catch (e) {
                if (typeof toastr !== "undefined") {
                    toastr.error("Error en el formato de respuesta del servidor")
                }
                $(".loadingscr").addClass("d-none")
                return
            }
        }

        if (response && response.type === "success") {
            $("#tutoresAsignados").html(response.html);
        } else {
            if (typeof toastr !== "undefined") {
                toastr.error(response.error || response.html || "Error al cargar los tutores asignados")
            }
        }
        $(".loadingscr").addClass("d-none");
    });
}