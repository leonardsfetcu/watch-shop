<?php
    session_start();
    require_once("db.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ceasuri de mana</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="script/jquery-3.3.1.min.js"></script>
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
             <a href="reset-cart.php?location=index.php" id="empty-cart">Empty Cart</a>
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
                 <a href="reset-wishlist.php?location=index.php" id="empty-wishlist">Empty Wishlist</a>
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



<!-- The flexible grid (content) -->
<div class="row">
    
    <!-- Left Side -->
    <div class="side">

        <h2>Why us?</h2>
        <p><q>Watch Shop is an official stockist for all watch brands listed on this website.
            Established in 1991 on the high street, Watch Shop is a leading retailer of brand name designer watches and is also the UK's most popular watch website.
            Watch Shop was the first independent watch retailer to advertise on national television, and we pride ourselves on having one of the most efficient
            shopping systems available with communication at every stage to inform you of your order status, as well as excellent 7-days sales, customer service
            and support team who are glad to assist you with any enquiry.</q>
        </p>

    </div>
    
    <!-- Main side -->
    <div class="main">
        <h2>Most popular brands</h2>
        <!-- Photo Grid -->
        <div class="row">
            
            <?php
                
                $sql = "SELECT imagepath, brandname from brands";
                $result = $link->query($sql);

                if($result->num_rows > 0)
                {
                    $imagesOnColumn = floor($result->num_rows / 4);
                    $extraImgOnLastCol = $result->num_rows - $imagesOnColumn * 3;
                  
                    if($extraImgOnLastCol == 0)
                    {
                        $extraImgOnLastCol = $imagesOnColumn;
                    }

                  //  echo "rows: ". $result->num_rows . "<br>" . $imagesOnColumn . "on column<br>" . $extraImgOnLastCol . "extra";

                    for($i=0;$i<3;$i++)
                    {
                        echo "<div class='column'>";

                        for($j=0;$j<$imagesOnColumn;$j++)
                        {
                            $row = $result->fetch_assoc();
                            echo "<img src='" . $row['imagepath'] . "' alt='" . $row['brandname'] . "' style='width: 100%' />";
                        }

                        echo "</div>";
                    }


                    echo "<div class='column'>";

                    for($i=0;$i<$extraImgOnLastCol;$i++)
                    {
                        $row = $result->fetch_assoc();
                        echo "<img src='" . $row['imagepath'] . "' alt='" . $row['brandname'] . "' style='width: 100%' />";
                    }
                    echo "</div>";
                }

                $link->close();
            ?>

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
