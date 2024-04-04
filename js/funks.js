$(document).ready(function(){
	$("#trigger").click(function(){
		let var1 = $("#ajax__testvar").val();
		$.ajax({
			type: "POST",
			url: dominio+"ajax/ajax-test/",
			data: "var1="+var1,
	 		success: function(data){
	 			data = JSON.parse(data);
	 			if( data.type == 'success' )
					$("#ajax__result").html(data.html);
				else
				{
					console.log(data.error);
					$("#ajax__result").html("Revisa la consola");
				}
			}
		});
	});
});
