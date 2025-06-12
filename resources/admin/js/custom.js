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

// Función para cargar mascotas
function ajax_get_mascotas_admin(comienzo, limite, pagina) {
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
                $("#page-content").html(response.html)
            } else {
                if (typeof toastr !== "undefined") {
                    toastr.error(response.error || response.html || "Error al cargar las mascotas")
                }
            }
            $(".loadingscr").addClass("d-none")
        },
    )
}

// Función para abrir modal general
function modalGeneral(element) {
    $(".loadingscr").removeClass("d-none")

    var data = {
        type: $(element).data("type"),
        content: $(element).data("content"),
        idmascota: $(element).data("idmascota"),
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

    var data = {
        idmascota: idmascota,
        evaluations: [],
    }

    // Recopilar todos los valores de evaluación
    $("." + evaluationClass).each(function () {
        data.evaluations.push({
            id: $(this).data("crid"),
            type: $(this).data("crtype"),
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

// Inicialización cuando el documento está listo
$(document).ready(() => {
    // Inicializar sliders si existen
    if ($(".slider").length > 0) {
        $(".slider").slider()
    }

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
})
