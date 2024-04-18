var abortController = new AbortController();
var signal = abortController.signal;

function ajax_get_usuarios( comienzo, limite, pagina )
{
    let formData = new FormData($("#formFiltrosAdmin")[0]);

    formData.append("comienzo", comienzo);
    formData.append("limite", limite);
    formData.append("pagina", pagina);

    afetch(
        dominio+"adminajax/ajax-get-usuarios/",
        {
            method: 'POST',
            body: formData
        }
    )
    .then((response) => response.json())
    .then(data => {
        if( data.type === 'success' )
            $('#page-content').html(data.html);
    });
}
function ajax_get_accounts( comienzo, limite, pagina )
{
    let formData = new FormData($("#formFiltrosAdmin")[0]);

    formData.append("comienzo", comienzo);
    formData.append("limite", limite);
    formData.append("pagina", pagina);

    afetch(
        dominio+"adminajax/ajax-get-accounts/",
        {
            method: 'POST',
            body: formData
        }
    )
    .then((response) => response.json())
    .then(data => {
        if( data.type === 'success' )
            $('#page-content').html(data.html);
    });
}

function ajax_get_idiomas_admin( comienzo, limite, pagina )
{
    let formData = new FormData($("#formFiltrosAdmin")[0]);

    formData.append("comienzo", comienzo);
    formData.append("limite", limite);
    formData.append("pagina", pagina);

    afetch(
        dominio+"adminajax/ajax-get-idiomas-admin/",
        {
            method: 'POST',
            body: formData
        }
    )
    .then((response) => response.json())
    .then(data => {
        if( data.type == 'success' )
            $('#page-content').html(data.html);
    });
}

function ajax_get_metas_admin(comienzo, limite, pagina)
{
    var formData = new FormData($("#formFiltrosAdmin")[0]);
    formData.append("comienzo", comienzo);
    formData.append("limite", limite);
    formData.append("pagina", pagina);

    afetch(
        dominio+"adminajax/ajax-get-slugs-filtered/",
        {
            method: 'POST',
            body: formData
        }
    )
    .then((response) => response.json())
    .then(data => {
        if( data.type == 'success' )
            $('#page-content').html(data.html);
    });
}

function ajax_get_traductions_filtered(comienzo, limite, pagina){

    var formData = new FormData($("#formFiltrosAdmin")[0]);
    formData.append('comienzo', comienzo);
    formData.append('pagina', pagina);
    formData.append('limite', limite);

    afetch(
        dominio+"adminajax/ajax-get-traductions-filtered/",
        {
            method: 'POST',
            body: formData
        }
    )
    .then((response) => response.json())
    .then(data => {
        if( data.type == 'success' )
            $('#page-content').html(data.html);
    });
}

function confirmarEliminacion( id, modelo, callback = null ) 
{
    Swal.fire({
        title: '¿Seguro que deseas continuar?',
        text: "Esta acción es irreversible",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger m-l-10',
        confirmButtonText: 'Si, continuar!',
        cancelButtonText: 'Cancelar'
    })
    .then( eliminado => {
        if( eliminado.isConfirmed ){
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

    afetch(
        dominio+"adminajax/ajax-eliminar-registro/",
        {
            method: 'POST',
            body: formData
        }
    )
    .then((response) => response.json())
    .then(responseJson => {
        if( responseJson.type == 'success' )
        {
            swal.fire(
                'Hecho!',
                'Es historia',
                'success'
            );
            
            if( callback ) 
                callback();
        }
        else
        {
            swal.fire(
                'Error',
                responseJson.error,
                'error'
            );
        }
    });
}

function afetch(url, options = {}, abortBeforeFetch = true)
{
    if( abortBeforeFetch )
    {
        abortController.abort();
        abortController = new AbortController();
        signal = abortController.signal;
        options.signal = signal;
    }

    if( typeof options.method !== 'undefined' && options.method == 'POST' && options.body instanceof FormData )
    {
        options.body.append('token', static_token);
        return fetch(url, options);
    }
    else if( typeof options.method !== 'undefined' && options.method == 'POST' && typeof options.body === 'undefined' )
    {
        options.body = new FormData();
        options.body.append('token', static_token);
        return fetch(url, options);
    }
    else if( typeof options.method !== 'undefined' && options.method == 'POST' )
    {
        const defaultOptions = { token: static_token };
        const mergedOptions = { ...defaultOptions, ...options };
        return fetch(url, mergedOptions);
    }
    return fetch(url, options);
}
