$(document).ready(function(){
	$(".product-details-container *").prop("disabled",true);

	$("button[type='reset']").click(function(){
		$(".product-details-container *").prop("disabled",true);
	});

	$("button[name='insert-product']").click(function() {
		$(".product-details-container *").prop("disabled",false);
		$("input,textarea,select").val("");
		$("input[name='productid']").val("NULL");
	});

});

/*
	function insertProduct()
	{
		$.ajax({
			type:"POST",
			data: {
				name:$("input[name='name']").val(),
				productcode:$("input[name='code']").val(),
				description:$("input[name='description']").val(),
				price:$("input[name='price']").val(),
				brandid:$("select[name='brand']").val(),
				mechanismType:$("select[name='mechanismType']").val(),
				braceletType:$("select[name='braceletType']").val(),
				waterResistant:$("select[name='waterResistant']").val()
			},
			url: "admin.php",
			success: function(result)
			{
				console.log(result);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
	        alert("Status: " + textStatus);
	        alert("Error: " + errorThrown); 
	    }
		});
	}
*/

function deleteProduct(id)
{
	if(confirm("Are you sure???"))
	{
		$.ajax({
		type:"POST",
		data:{delete:true,
			productid:id},
		url: "admin.php",
		success: function(result)
		{
			$(".product-card#"+id).remove();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus);
        alert("Error: " + errorThrown); 
    }
	});
	}
	
}

function modifyProduct(id)
{
	$(".product-details-container *").prop("disabled",false);

	$.ajax({
		type:"POST",
		data:{modify:true,
			productid:id},
		url: "admin.php",
		success: function(result)
		{
			try{
				result = JSON.parse(result);
				console.log(result);
				if(result.hasOwnProperty('error'))
				{
					alert(result.error);
				}
				fillForm(result);

			}
			catch(e)
			{
				console.log(e);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus);
        alert("Error: " + errorThrown); 
    }
	});
}

function fillForm(data)
{
	$("input[name='name']").val(data.name);
	$("textarea[name='description']").val(data.description);
	$("input[name='price']").val(data.price);
	$("select[name='brand']").val(data.brandid);
	$("select[name='mechanismType']").val(data.mechanismType);
	$("select[name='braceletType']").val(data.braceletType);
	$("select[name='waterResistant']").val(data.waterResistant);
	$("input[name='code']").val(data.productcode);
	$("input[name='productid']").val(data.productid);

}