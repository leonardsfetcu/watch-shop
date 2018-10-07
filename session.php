<?php 
	session_start();
	require_once("db.php");

function updateDatabase($result)
{
	global $link,$cart;

	if($result->num_rows == 1)
	{
		$orderid = $result->fetch_assoc()['orderid'];
		$sqlCommand = "DELETE FROM order_details WHERE orderid = ".$orderid;

		$result = $link->query($sqlCommand);
		if(!$result)
		{
			echo "Error: Unable to delete order from database!";
			die();
		}
	}
	else
	{
		if($cart == 1)
		{
			$sqlCommand = "INSERT INTO orders(userid,finished,wishlisted,datetime) VALUES(".$_SESSION['userid'].", 0, NULL, CURRENT_TIMESTAMP)";
		}
		else
		{
			$sqlCommand = "INSERT INTO orders(userid,finished,wishlisted,datetime) VALUES(".$_SESSION['userid']. ",NULL,1,CURRENT_TIMESTAMP)";
		}

		$result = $link->query($sqlCommand);
		if($result == TRUE)
		{
			$orderid = $link->insert_id;
		}
		else
		{
			echo "Error: Unable to insert order to database!";
			die();
		}
	}

	if($cart == 1)
	{
		for($i=0;$i<count($_SESSION['cart']);$i++)
		{
			$sqlCommand = "INSERT INTO order_details(orderid,productid,quantity) VALUES
										(".$orderid.",".$_SESSION['cart'][$i]['product']['productid'].",".$_SESSION['cart'][$i]['quantity'].")";

			$result = $link->query($sqlCommand);

		}
	}
	else
	{
		for($i=0;$i<count($_SESSION['wishlist']);$i++)
		{
			$sqlCommand = "INSERT INTO order_details(orderid,productid,quantity) VALUES
										(".$orderid.",".$_SESSION['wishlist'][$i]['productid'].",0)";

			$result = $link->query($sqlCommand);

		}
	}
	
}

	$sqlCommand = "select orderid from orders where userid = ".$_SESSION['userid']. " and finished = 0";
	$result = $link->query($sqlCommand);
	$orderid ='';
	$cart = 1;
	updateDatabase($result);

	$sqlCommand = "select orderid from orders where userid = ".$_SESSION['userid']. " and wishlisted = 1";
	$result = $link->query($sqlCommand);
	$orderid ='';
	$cart = 0;

	updateDatabase($result);



	$_SESSION = array();
	session_destroy();
	header("Location: index.php");
?>