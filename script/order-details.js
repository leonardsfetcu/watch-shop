$(document).ready(function(){

	userid = '';
	
	$.ajax({
		type:"POST",
		data:{getOrders:"true"},
		url:"order-details.php",
		success: function(orders){
			try{
				orders=JSON.parse(orders);
				for(i=0;i<orders.length;i++)
				{
					var total=0;
					orderid=orders[i].orderid;
					$(".order-container").append("<div class='order-item' id='"+orderid+"'>"+
						"<div class='order-header'><div class='order-id'><h4>OrderId:</h4><p>"+orderid+
						"</p></div><div class='order-user_contact'><h4>Customer: "+
						"</h4><p>"+orders[i].user_name+"</p></div>"+
						"<div class='order-user_location'><h4>Shipping Address: </h4><p>"+orders[i].address+
						"</p></div><div class='order-date'><h4>Order date: </h4><p>"+orders[i].datetime+"</p></div><div class='order-phone'><h4>Phone: </h4><p>"+orders[i].phone+"</p></div><div class='order-total'></div></div><div class='order-content'></div></div>");
					for(j=0;j<orders[i].details.length;j++)
					{
						total +=orders[i].details[j].quantity * orders[i].details[j].price;
						$("#"+orderid+" .order-content").append("<div class='product-item'><div class='product-quantity'><h4>Quantity</h4><p>"+
							orders[i].details[j].quantity+"</p></div><div class='product-image'><img src='"+
							orders[i].details[j].photopath+"'></img></div>"+
							"<div class='product-description'><h4>Description:</h4><p>"+orders[i].details[j].description+"</p></div><div class='product-price'>"+
							"<h4>Price:</h4><p>$"+orders[i].details[j].price+"</p></div></div>");
						
					}

					$("#"+orderid+" .order-total").html("<h4>Total:</h4><p>$"+total.toFixed(2)+"</p>");
				}
			}
			catch(e)
			{
				alert(e);
			}
		}
	});
});