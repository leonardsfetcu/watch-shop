<?php 
    session_start();
    
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ceasuri de mana</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/index.css">
    <link rel="stylesheet" type="text/css" href="style/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="script/jquery-3.3.1.min.js"></script>
    <script src="script/index.js"></script>
    
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
             <a href="reset-cart.php?location=contact.php" id="empty-cart">Empty Cart</a>
            <?php

                if(isset($_SESSION['cart']))
                    {
                        if(count($_SESSION['cart'])>0)
                        {
                            $products = $_SESSION['cart'];
                            
                            for($i=0;$i<count($products);$i++)
                            {
                                    echo "<a href=#>"."<b>".$products[$i]['quantity']."</b> x " . $products[$i]['product']['name'] . "</a>"; 
                            }
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
                 <a href="reset-wishlist.php?location=contact.php" id="empty-wishlist">Empty Wishlist</a>
                  <?php
                    
                    if(isset($_SESSION['wishlist']))
                    {
                        if(count($_SESSION['wishlist'])>0)
                        {
                            $products = $_SESSION['wishlist'];
                            
                            for($i=0;$i<count($products);$i++)
                            {
                                    echo "<a href=#>". $products[$i]['name'] . "</a>"; 
                            }
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


    <div class="container">
            
        <div class="left-side">
            <h2>Contact us</h2>
            <p>
                    At WatchShop we do our best to answer all your questions quickly and to help you choose the watch that suits you.
                    <br><br>
                    <br>Don't let your questions without answer!
                    <br>
                    Contact us right now!
            </p>
             <form action="/action_page.php">
                  <label for="name"><b>Your Name</b></label>
                  <input id="name" type="text" placeholder="Enter your name" name="name" required>

                  <label for="email"><b>Your Email</b></label>
                  <input id="email" type="text" placeholder="Enter your email" name="email" required>
                  
                  <label for="message"><b>Your Message</b></label>
                  <textarea placeholder="How can we help you?" name="message" required rows="15" cols="85"></textarea>  

                  <button type="submit">SEND</button>
            </form>
        </div>
        <div class="right-side">
            <h2>Email</h2>
            <p>helpdesk@watchshop.net</p>
            <h2>Telephone</h2>
            <p>+30 6847 559 125</p>
            <h2>Adress</h2>
            <p>Bulevardul Pierre de Coubertin 3-5, Bucuresti</p>
            <div id="location">
                
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
    <script async defer src="https://maps.googleapis.com/maps/api/js?key= AIzaSyABgUUjrWfMOO5FAbeY9LFEp0ID4dgOleg&callback=myMap" type="text/javascript"></script>
    <script>
        function myMap() {
        var mapOptions = {
            center: new google.maps.LatLng(44.441547, 26.151519),
            zoom: 16,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        var map = new google.maps.Map(document.getElementById("location"), mapOptions);
        }
    </script>
   
</body>
</html>
