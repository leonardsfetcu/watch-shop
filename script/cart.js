$(document).ready(function(){

	$("#place-order").click(function(){
	   
	    sendOrder();
	});

	$(".close").click(function(){
	    $("#myModal").css("display","none");
	});

	$(window).click(function(event){

	    if($(event.target).is("#myModal"))
	    {
	        $("#myModal").css("display","none");
	    }

	});



});

function sendOrder() {

	isLoggedIn(
		function(result){

			if(result == "false")
			{
				$("p#info").text("Please login before deliver a order!");
				$("#myModal").css("display","flex");
			}
			else
			{
				result = JSON.parse(result);
				if($("td#total").html()!=="$0")
				{
					sendToDatabase(function(result){
						console.log(result);
						if(result == 'has-money')
						{
							$(".modal-content").html("<span class='close'>&times;</span><p id='info'></p><button name='yes' onclick='emptyCart()'>Yes</button>"+
							"<button name='no' onclick='hideModal()'>No</button>");
							$("p#info").text("Your order was processed! Do you want to empty your cart?");
							$("#myModal").css("display","flex");
						}
						else if(result == 'no-money')
						{
							$(".modal-content").html("<span class='close'>&times;</span><p id='info'></p><button name='yes' onclick='emptyCart()'>Yes</button>"+
							"<button name='no' onclick='hideModal()'>No</button>");
							$("p#info").text("You don't have enough money! Contact an administrator! Do you want to empty your cart?");
							$("#myModal").css("display","flex");
						}
					});
					

				}
				else
				{
					$("p#info").text("Hello "+result.firstname+"! Your cart is empty!");
					$("#myModal").css("display","flex");
				}
			}
		});
	
}

function hideModal()
{
	$("#myModal").css("display","none");
}

function sendToDatabase(callback)
{
	$.ajax({
		type: "POST",
		data: {sendOrder:"true"},
		url: "cart.php",
		success: callback,
		error:function(XMLHttpRequest, textStatus, errorThrown) {
	        alert("Status: " + textStatus);
	        alert("Error: " + errorThrown); 
	    }
	});
}

function isLoggedIn(callback)
{
	$.ajax({
		type:"POST",
		data:{checkLogin:"true"},
		url: "cart.php",
		success: callback,
		error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus);
        alert("Error: " + errorThrown); 
    	}
	});
}

function emptyCart()
{
	getCartProducts(function(result){
		productArray = JSON.parse(result);

		if(productArray)
		{
			for(i=0;i<productArray.length;i++)
			{
				deleteProduct(productArray[i].product.productid);
			}
		}
	});

	$("#myModal").css("display","none");
}

function getCartProducts(callback)
{
	$.ajax({
		type: "POST",
		data:{getProducts:"true"},
		url: "cart.php",
		success: callback,
		error: function(XMLHttpRequest,textStatus,errorThrown)
		{
			alert("Status: "+textStatus);
			alert("Error: "+errorThrown);
		}
	});
}

function deleteProduct(id)
{
	$.ajax({
		type: 'POST',
		data: { removedProduct:id },
		url: 'cart.php',
		success: function(result){
			
			$("td#total").text("$"+result);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {

        alert("Status: " + textStatus);
        alert("Error: " + errorThrown); 
    	}
	});

	$(".left-container div#"+id+", tr#"+id).remove();


	$(".dropdown-content a#"+id).remove();	

	if($(".product-container").length==0)
	{
		$(".left-container").html(
			'<div class="product-container"><p>No products in cart.</p></div>');
	}
	
}