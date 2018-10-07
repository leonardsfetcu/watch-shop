<?php
    session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ceasuri de mana</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/index.css">
    <script src="script/index.js" type="text/javascript"></script> 
    
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
                    echo "<h4>".$_SESSION['firstname']." ".$_SESSION['lastname']."</h4><hr>";
                    echo "
                        <a href='account-details.php'>Account Details</a>
                        <a href='order-details.php'>Order Details</a>
                        <a href='wishlist.php'>Wishlist</a>
                        <a href='session.php' id='special-row'>Logout</a>
                    ";
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
             <a href="reset-cart.php?location=account.php" id="empty-cart">Empty Cart</a>
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
                 <a href="reset-wishlist.php?location=account.php" id="empty-wishlist">Empty Wishlist</a>
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
    <a href="index.php">Home</a>
    <a href="products.php?gender=female">Women</a>
    <a href="products.php?gender=male">Men</a>
    <a href="products.php?brands=true">Brands</a>
    <a href="products.php?promotion=true">Promotion</a>
    <a href="contact.php">Contact</a>

</div>



<!-- The flexible grid (content) -->
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
