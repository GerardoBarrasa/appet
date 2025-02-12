$(document).ready(function(){
	$('body').on('click', '#trigger_get', function(){
		afetch(
			dominio+'ajax/ajax-test-get/',
			{method: 'GET'}
		)
		.then((response) => response.json())
		.then(data => {
			if( data.type == 'success' )
				$("#ajax__result_get").html(data.html);
			else
			{
				console.log(data.error);
				$("#ajax__result_get").html("Revisa la consola");
			}
		});
	});

	$('body').on('click', '#trigger_post', function(){
		let var1 = $("#ajax__testvar_post").val();
		let formData = new FormData();
		formData.append('var1', var1);

		afetch(
			dominio+"ajax/ajax-test-post/",
			{
				method: 'POST',
				body: formData
			}
		)
		.then((response) => response.json())
		.then(data => {
			if( data.type == 'success' )
				$("#ajax__result_post").html(data.html);
			else
			{
				console.log(data.error);
				$("#ajax__result_post").html("Revisa la consola");
			}
		});
	});
});

function show_loader()
{
	$(".loadingscr").removeClass('d-none').addClass('d-flex');
}
function hide_loader()
{
	$(".loadingscr").removeClass('d-flex').addClass('d-none');
}

function afetch(url, options = {})
{
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
