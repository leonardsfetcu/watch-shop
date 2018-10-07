function addToCart(id)
{

	$.ajax({
		type: 'POST',
		data: { productid: id,
				insertCart: 'true'},
		url: "products.php",
		success: function(result){
			try{
				console.log(result);
				result=JSON.parse(result);
				var htmlCode = '<a href="cart.php" id="go-cart">Go to Cart!</a><a href="reset-cart.php?location=products.php" id="empty-cart">Empty Cart</a>';
				for(i=0;i<result.length;i++)
				{
					htmlCode +="<a href=# id='"+result[i].product.productid+"'><b>"+result[i].quantity+"</b> x "+result[i].product.name+"</a>";
				}
				$("#cart").html(htmlCode);
				alert("Your product was successfully added to cart!");
			}
			catch(e)
			{
				alert(e);
			}
		
		}
	});

}


function addToWishlist(id)
{

	$.ajax({
		type: 'POST',
		data: { productid: id,
				insertWishlist: 'true'},
		url: "products.php",
		success: function(result){

		try{
			result=JSON.parse(result);
			var htmlCode = '<a href="wishlist.php" id="go-wishlist">Go to Wishlist!</a>'+
			'<a href="reset-wishlist.php?location=products.php" id="empty-wishlist">Empty Wishlist</a>';
			for(i=0;i<result.length;i++)
			{
				htmlCode +="<a href=# id='"+result[i].productid+"'>"+result[i].name+"</a>";
			}
			$("#wishlist").html(htmlCode);
			alert("Your product was successfully added to cart!");
		}
		catch(e)
		{
			alert(e);
		}
		
	}
	});

}

$("#empty-wishlist").click(function(){
/*	$.ajax({
		type:'POST',
		data: {resetWishlist:"true"},
		url: "products.php",
		success: function(result){
			alert(result);
			var htmlCode = '<a href="wishlist.php" id="go-wishlist">Go to Wishlist!</a>'+
			'<a href="reset-wishlist.php?location=products.php" id="empty-wishlist">Empty Wishlist</a>';
			$("#wishlist").html(htmlCode);
		}
	});*/
});