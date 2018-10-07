<?php 
    session_start();
    require_once("db.php");

    $product = array();

    if(!isset($_GET['productid']))
    {
        echo "<h1>Access denied!</h1>";
        exit();
    }
    else
    {
        $sqlCommand = "SELECT products.name, products.photopath,products.productcode, products.description, products.price, products.mechanismType,
        products.braceletType, products.waterResistant, brands.brandname FROM products inner join brands on products.brandid = brands.brandid WHERE productid =".$_GET['productid'];
        $result = $link->query($sqlCommand);

        if($result->num_rows != 1)
        {
            echo "<H1>Error: Product not found!</H1>";
            exit();
        }
        else
        {
            $product = $result->fetch_assoc();
            $link->close();
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ceasuri de mana</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/index.css">
    <link rel="stylesheet" type="text/css" href="style/product-details.css">
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
             <a href="reset-cart.php?location=product-details.php" id="empty-cart">Empty Cart</a>
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
                 <a href="reset-wishlist.php?location=product-details.php" id="empty-wishlist">Empty Wishlist</a>
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


<div class="product-details-container">
    <div class="product-image-container">
       <div class="imgcontainer">
            <img src="<?php echo $product['photopath']; ?>">
        </div>
    </div>

    <div class="product-text">
        <table border=2>
            <th colspan="2">
               Specification
            </th>

            <tr>
                <td>
                    Brand
                </td>
                <td>
                    <?php echo $product['brandname']; ?>
                </td>
            </tr>
                <td>
                    Product Code
                </td>
                <td>               
                    <?php echo $product['productcode']; ?>
                </td>
            <tr>
                <td>
                    Movement
                </td>
                <td>
                    <?php echo $product['mechanismType']; ?>
                </td>
            </tr>
                <td>
                    Bracelet
                </td>
                <td>
                    <?php echo $product['braceletType']; ?>
                </td>
            <tr>
                <td>
                    Water resistant
                </td>
                <td>
                    <?php echo $product['waterResistant']; ?>
                </td>
                
            </tr>
        </table>
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
