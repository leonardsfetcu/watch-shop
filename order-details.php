<?php
    session_start();
    require_once("db.php");

    $currentUser= array();
    if(!isset($_SESSION['userid']))
    {
        echo "<h1>Access denied</h1>";
        exit();
    }

    $result=$link->query("SELECT * FROM users WHERE userid=".$_SESSION['userid']);

    if($result->num_rows!=1)
    {
        echo "<h1>Error: Unable to get information about you from database!</h1>";
        exit();
    }
    else
    {
        $currentUser = $result->fetch_assoc();
    }

    if(isset($_POST['getUserId']))
    {
        echo json_encode($_SESSION['userid']);
        exit();
    }

    if(isset($_POST['getOrders']))
    {
        if($_SESSION['role'] == 'admin')
        {
            $sqlCommand = "SELECT orders.orderid,orders.datetime,CONCAT(users.firstname,' ', users.lastname) as user_name, users.address,users.phone from orders inner join users on orders.userid=users.userid where orders.finished = 1";
        }
        else
        {
            $sqlCommand = "SELECT orders.orderid,orders.datetime,CONCAT(users.firstname,' ', users.lastname) as user_name, users.address,users.phone from orders inner join users on orders.userid=users.userid where orders.userid =".$_SESSION['userid']." and orders.finished = 1";
        }
       
        $orders = array();
        $counter=0;
        $result=$link->query($sqlCommand);
        if($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    $orders[$counter++] = $row;
                }
            }
        
        for($i=0;$i<count($orders);$i++)
        {
            $counter = 0;
            $sqlCommand = "SELECT products.productid, order_details.quantity, products.name,products.description,products.price, products.productcode, products.photopath from orders INNER JOIN order_details on orders.orderid = order_details.orderid INNER join products on order_details.productid=products.productid where order_details.orderid = ".$orders[$i]['orderid'];

            $result=$link->query($sqlCommand);
            if($result->num_rows>0)
            {
                while($row = $result->fetch_assoc())
                {
                    $orders[$i]['details'][$counter++]=$row;
                }
            }
        }
        echo json_encode($orders);
        exit();
    }

?>

<!DOCTYPE html>
<html>
<head>
    <title>Ceasuri de mana</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/index.css">
    <link rel="stylesheet" type="text/css" href="style/account-details.css">
    <link rel="stylesheet" type="text/css" href="style/order-details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="script/jquery-3.3.1.min.js"></script>
    <script src="script/order-details.js"></script>
</head>
<body>
    
<!-- Upper Header -->
     <div class="upper">
       <div class="dropdown" id="my-account">
        <i class="fa fa-user-circle"></i>
          <button class="dropbtn">My Account</button>
          <div class="dropdown-content">
            <?php 
                if(isset($_SESSION['username']))
                {
                    foreach ($_SESSION['upper-menu'] as $key => $value) {
                        echo $value;
                    }
                }
                else
                {
                    echo "
                        <a href='login.php' id='special-row'>Login</a>
                    ";
                }
            ?>
          </div>
        </div>

        <div class="dropdown" id="my-cart">
            <i class="fa fa-cart-plus"></i>
          <button class="dropbtn">My Cart</button>
          <div class="dropdown-content" id="cart">
             <a href="cart.php" id="go-cart">Go to Cart!</a>
             <a href="reset-cart.php?location=order-details.php" id="empty-cart">Empty Cart</a>
            <?php

                if(count($_SESSION['cart'])>0)
                {
                    $products = $_SESSION['cart'];
                    
                    for($i=0;$i<count($products);$i++)
                    {
                            echo "<a href=#>"."<b>".$products[$i]['quantity']."</b> x " . $products[$i]['product']['name'] . "</a>"; 
                    }
                }
            ?>

          </div>
        </div>

        <div class="dropdown" id="my-wishlist">
            <i class="fa fa-heart"></i>
            <button class="dropbtn">My Wishlist</button>
            <div class="dropdown-content" id="wishlist">
                 <a href="wishlist.php" id="go-cart">Go to Wishlist!</a>
                 <a href="reset-wishlist.php?location=order-details.php" id="empty-wishlist">Empty Wishlist</a>
                  <?php
                    if(count($_SESSION['wishlist'])>0)
                    {
                        $products = $_SESSION['wishlist'];
                        
                        for($i=0;$i<count($products);$i++)
                        {
                                echo "<a href=#>". $products[$i]['name'] . "</a>"; 
                        }
                    }
                ?>
            </div>
        </div>
    </div>
    


<!-- Header -->
<div class="header">
      <h1>Watch Shop</h1>
  <p>In <b>time</b> for everything</p>
</div>

<!-- Navigation Bar -->
<div class="navbar">
    <a href="index.php" id="home-link">Home</a>
    <a href="products.php?gender[]=female" id="women-link">Women</a>
    <a href="products.php?gender[]=male" id="men-link">Men</a>
    <a href="brands.php" id="brands-link">Brands</a>
    <a href="products.php?promotion=true" id="promotion-link">Promotion</a>
    <a href="contact.php" id="contact-link">Contact</a>

</div>


<!-- Content -->
    <div class="content">

    <!-- Left Side -->
        <div class="left-container">
            <div class="menu-container">
                <ul>
                    <li class="active"><a href="order-details.php">My Orders</a></li>
                    <li><a href="account-details.php">Account Settings</a></li>
                    <li><a href="vouchers.php">Vouchers</a></li>
                    <li><a href="wishlist.php">Wishlish</a></li>
                </ul>
            </div>
        </div>


    <!-- Right Side -->
        <div class="right-container">
            <div class="order-container">    
                
            </div>
        </div>

</div>

<!-- Footer -->
<div class="footer">
    
    <div class="contact">
        
        <h4>Contact us</h4>
        <ul>
            <li>By Telephone</li>
            <li>By Email</li>
            <li>Login / register</li>
            <li>Press</li>
            <li>Report a bug</li>    
        </ul>
  
    </div>
    
    <div class="returns">
        
        <h4>Returns & Policies</h4>
        <ul>
            <li>Returns Policy</li>
            <li>Delivery Policy</li>
            <li>Shipping Locations</li>
            <li>Click & Collect Service</li>
            <li>Vouchers / Discounts</li> 
            <li>Bulk Purchases</li> 
        </ul>
    </div>
    
    <div class="other">
        
        <h4>Other Information</h4>
        <ul>
            <li>Watch Repairs & Service</li>
            <li>Bracelet Adjustment</li>
            <li>Watch Buying Guide</li>
            <li>Watch News</li>
            <li>Watch Reviews</li> 
            <li>Finance Information</li> 
            <li>The Watch Hut</li> 
        </ul>
        
    </div>
      
</div>
</body>
</html>
