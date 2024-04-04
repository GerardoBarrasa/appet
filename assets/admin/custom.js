function ajax_get_usuarios_admin( comienzo, limite, pagina )
{
    let formData = new FormData($("#formFiltrosAdmin")[0]);

    formData.append("comienzo", comienzo);
    formData.append("limite", limite);
    formData.append("pagina", pagina);

    $.ajax({
        type: "POST",
        url: dominio+"ajax/ajax-get-usuarios-admin/",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data)
        {
            data = JSON.parse(data);
            if( data.type == 'success' )
                $('#page-content').html(data.html);
        }
    });
}

function ajax_get_idiomas_admin( comienzo, limite, pagina )
{
    let formData = new FormData($("#formFiltrosAdmin")[0]);

    formData.append("comienzo", comienzo);
    formData.append("limite", limite);
    formData.append("pagina", pagina);

    $.ajax({
        type: "POST",
        url: dominio+"ajax/ajax-get-idiomas-admin/",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data)
        {
            data = JSON.parse(data);
            if( data.type == 'success' )
                $('#page-content').html(data.html);
        }
    });
}

//Funcion que gestiona la actualizacion o no de una traduccion
function ajax_update_traduction(formNameId, idioma="", updateAjaxTable=false, reload=true){

    var formData = new FormData($("#"+formNameId)[0]);
    var action = formData.get('action');
    var comienzo = formData.get('comienzo');
    var limite = formData.get('limite');
    var pagina = formData.get('pagina');

    $.ajax({
        type: "POST",
        url: dominio+"ajax/ajax-update-traduction/",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function(){},
        success: function(data){
            
            if(data == 'ok'){

                if(action == 'create'){

                    //Comprobamos el metodo de recarga.
                    if(!updateAjaxTable && reload){
                        setTimeout("location.reload()", 4500);
                        showSuccessToast('Traducción creada', 'Se ha creado la traducción correctamente. La página se recargará en 5 segundos.');
                    }
                    else{
                        //Cerramos modal (se habra creado desde modal)
                        $('#closeModal').click();

                        //Actualizamos tabla
                        ajax_get_traductions_filtered(comienzo, limite, pagina);

                        //Reseteamos inputs
                        $('#input_traduction_for').val('');
                        $('#input_shortcode').val('');
                        $('#input_contenido').val('');

                        showSuccessToast('Traducción creada', 'Se ha creado la traducción correctamente. Recargando tabla de traducciones.');
                    }
                }
                else{
                    showSuccessToast('Traducción actualizada', 'Se ha actualizado la traducción correctamente.');
                }
            }
            else{
                showDangerToast('Ha ocurrido un error', data);
            }
        }
    });

    return false;
}

//Funcion ajax que va a pintar toda la tabla de traducciones en funcion de los filtros.
function ajax_get_traductions_filtered(comienzo, limite, pagina){

    var formData = new FormData($("#formFiltrosAdmin")[0]);
    formData.append('comienzo', comienzo);
    formData.append('pagina', pagina);
    formData.append('limite', limite);

    $.ajax({
        type: "POST",
        url: dominio+"ajax/ajax-get-traductions-filtered/",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function(){},
        success: function(data)
        {
            data = JSON.parse(data);
            if( data.type == 'success' )
                $('#page-content').html(data.html);
        }
    });

    return false;
}

function confirmarEliminacion( id, modelo, callback = null ) 
{
    swal({
        title: '¿Seguro que deseas continuar?',
        text: "Esta acción es irreversible",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger m-l-10',
        confirmButtonText: 'Si, continuar!'
    })
    .then( eliminado => {
        if( eliminado ){
            eliminarRegistroAjax( id, modelo, callback );
        }
    })
    .catch( () => '' );
}

function eliminarRegistroAjax( id, modelo, callback = null )
{
    let formData = new FormData();
    formData.append("id", id);
    formData.append("modelo", modelo);

    $.ajax({
        type: "POST",
        url: dominio+"ajax/ajax-eliminar-registro/",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(eliminado){
            if( +eliminado === 1 ){
                swal(
                    'Hecho!',
                    'Es historia',
                    'success'
                );
                
                if( callback ) 
                    callback();

            } else {
                swal(
                    'Error',
                    'No se pudo eliminar el registro',
                    'error'
                );
            }
            
        }
    });
}

(function($) {
  showSuccessToast = function(title, content) {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: title,
      text: content,
      showHideTransition: 'slide',
      icon: 'success',
      loaderBg: '#f96868',
      position: 'top-right'
    })
  };
  showInfoToast = function(title, content) {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: title,
      text: content,
      showHideTransition: 'slide',
      icon: 'info',
      loaderBg: '#46c35f',
      position: 'top-right'
    })
  };
  showWarningToast = function(title, content) {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: title,
      text: content,
      showHideTransition: 'slide',
      icon: 'warning',
      loaderBg: '#57c7d4',
      position: 'top-right'
    })
  };
  showDangerToast = function(title, content) {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: title,
      text: content,
      showHideTransition: 'slide',
      icon: 'error',
      loaderBg: '#f2a654',
      position: 'top-right'
    })
  };
  showToastPosition = function(position) {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: 'Positioning',
      text: 'Specify the custom position object or use one of the predefined ones',
      position: String(position),
      icon: 'info',
      stack: false,
      loaderBg: '#f96868'
    })
  }
  showToastInCustomPosition = function() {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: 'Custom positioning',
      text: 'Specify the custom position object or use one of the predefined ones',
      icon: 'info',
      position: {
        left: 120,
        top: 120
      },
      stack: false,
      loaderBg: '#f96868'
    })
  }
  resetToastPosition = function() {
    $('.jq-toast-wrap').removeClass('bottom-left bottom-right top-left top-right mid-center'); // to remove previous position class
    $(".jq-toast-wrap").css({
      "top": "",
      "left": "",
      "bottom": "",
      "right": ""
    }); //to remove previous position style
  }
})(jQuery);
