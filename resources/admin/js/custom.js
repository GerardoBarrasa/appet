var abortController = new AbortController();
var signal = abortController.signal;

$(document).ready(function(){
    $('#formFiltrosAdmin input[type=text]').keyup(_.debounce(function () {
        let funcion = $(this).data('function');
        window[funcion]('0', '10', '1');
    }, 500));
    /* BOOTSTRAP SLIDER */
    $('.slider').bootstrapSlider();
    $('[data-toggle="tooltip"]').tooltip()
});

function show_loader()
{
    $(".loadingscr").removeClass('d-none').addClass('d-flex');
}
function hide_loader()
{
    $(".loadingscr").removeClass('d-flex').addClass('d-none');
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
function ajax_get_mascotas_admin( comienzo, limite, pagina )
{
    show_loader();
    let formData = new FormData($("#formFiltrosAdmin")[0]);

    formData.append("comienzo", comienzo);
    formData.append("limite", limite);
    formData.append("pagina", pagina);

    afetch(
        dominio+"adminajax/ajax-get-mascotas-admin/",
        {
            method: 'POST',
            body: formData
        }
    )
        .then((response) => response.json())
        .then(data => {
            if( data.type === 'success' )
                $('#page-content').html(data.html);
            hide_loader();
        });
}