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

    $(".loadingscr").removeClass("d-none")

    ajax_call(
        dominio + "adminajax/ajax-get-mascotas-admin/",
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
                  generarPaginador(response.pagination)
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

// Función para generar el paginador
function generarPaginador(paginacion) {
  var paginadorHtml = '<ul class="pagination justify-content-center m-0">'

  // Botón anterior
  if (paginacion.tiene_anterior) {
    paginadorHtml += `<li class="page-item">
      <a class="page-link" href="#" onclick="cambiarPagina(${paginacion.pagina_anterior}); return false;" aria-label="Anterior">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>`
  } else {
    paginadorHtml += `<li class="page-item disabled">
      <span class="page-link" aria-label="Anterior">
        <span aria-hidden="true">&laquo;</span>
      </span>
    </li>`
  }

  // Páginas
  paginacion.paginas.forEach((pagina) => {
    if (pagina.separador && pagina.numero > 1) {
      paginadorHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>'
    }

    if (pagina.activa) {
      paginadorHtml += `<li class="page-item active">
        <span class="page-link">${pagina.numero}</span>
      </li>`
    } else {
      paginadorHtml += `<li class="page-item">
        <a class="page-link" href="#" onclick="cambiarPagina(${pagina.numero}); return false;">${pagina.numero}</a>
      </li>`
    }
  })

  // Botón siguiente
  if (paginacion.tiene_siguiente) {
    paginadorHtml += `<li class="page-item">
      <a class="page-link" href="#" onclick="cambiarPagina(${paginacion.pagina_siguiente}); return false;" aria-label="Siguiente">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>`
  } else {
    paginadorHtml += `<li class="page-item disabled">
      <span class="page-link" aria-label="Siguiente">
        <span aria-hidden="true">&raquo;</span>
      </span>
    </li>`
  }

  paginadorHtml += "</ul>"

  // Información adicional
  var infoHtml = `<div class="pagination-info text-center mt-2 small text-muted">
    Mostrando ${Math.min((paginacion.pagina_actual - 1) * paginacion.registros_por_pagina + 1, paginacion.total_registros)} - 
    ${Math.min(paginacion.pagina_actual * paginacion.registros_por_pagina, paginacion.total_registros)} 
    de ${paginacion.total_registros} registros
  </div>`

  // Insertar en el DOM
  $(".paginador").html(paginadorHtml + infoHtml)
}

// Función para cambiar de página
function cambiarPagina(nuevaPagina) {
  var limite = 12 // Valor por defecto
  var comienzo = (nuevaPagina - 1) * limite

  ajax_get_mascotas_admin(comienzo, limite, nuevaPagina)
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

    $(".debouncefunc").on('keyup', $.debounce(250, function(e) {
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
})

