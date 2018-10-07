<?php
    
    session_start();
    require_once("db.php");

    if(isset($_POST['checkLogin']))
    {
        if(isset($_SESSION['username']))
        {
            echo json_encode(array(
                "username"=>$_SESSION['username'],
                "firstname"=>$_SESSION['firstname'],
                "lastname"=>$_SESSION['lastname']));
            exit();
        }
        else
        {
            echo "false";
            exit();
        }
    }

    function getTotal()
    {
        $cartSize = count($_SESSION['cart']);
        $totalPrice = 0;

        if($cartSize>0)
        {
            for($i=0;$i<$cartSize;$i++)
            {
                $totalPrice += $_SESSION['cart'][$i]['quantity']*$_SESSION['cart'][$i]['product']['price'];
            }

            return $totalPrice;
        }

    }

    if(isset($_POST['removedProduct']))
    {
        $cartSize = count($_SESSION['cart']);
        $totalPrice = 0;

        if($cartSize>0)
        {
            for($i=0;$i<$cartSize;$i++)
            {
                if($_SESSION['cart'][$i]['product']['productid'] === $_POST['removedProduct'])
                {
                    array_splice($_SESSION['cart'],$i,1);
                    break;
                }
            }

            for($i=0;$i<count($_SESSION['cart']);$i++)
            {
                $totalPrice += $_SESSION['cart'][$i]['quantity']*$_SESSION['cart'][$i]['product']['price'];
            }

            echo $totalPrice;
        }
        exit();
    }

    if(isset($_POST['getProducts']))
    {
        echo json_encode($_SESSION['cart']);
        exit();
    }

    if(isset($_POST['sendOrder']))
    {
        $success=true;
        $sqlCommand = "Select * from users where userid = ".$_SESSION['userid'];
        $result = $link->query($sqlCommand);

        if($result->num_rows == 1)
        {
            $row = $result->fetch_assoc();
            $totalPrice = getTotal();

            if($totalPrice > $row['wallet'] )
            {
                echo "no-money";
                $success=false;
            }
            else
            {
                $money = $row['wallet']-$totalPrice;
                $_SESSION['upper-menu'][0]="<h4>".$_SESSION['firstname']." ".$_SESSION['lastname']."</h4><h4>Wallet: $".$money."</h4><hr>";
                echo "has-money";
                $sqlCommand = "UPDATE users SET wallet = '".$money."' WHERE userid = ".$_SESSION['userid'];
                $result = $link->query($sqlCommand);

                if($result != TRUE)
                {
                    echo "Unable to update database!";
                    $success=false;
                }
            }
        }

        if($success == true)
        {
             $sqlCommand = "INSERT INTO orders(orderid,userid,finished,datetime)
            VALUES(NULL,".$_SESSION['userid'].",1,CURRENT_TIMESTAMP)";

            if($result = $link->query($sqlCommand))
            {
                $last_id = $link->insert_id;
                $success = true;
                for($i=0;$i<count($_SESSION['cart']);$i++)
                {
                    $sqlCommand = "INSERT INTO order_details(id,orderid,productid,quantity)
                    VALUES(NULL,$last_id,".$_SESSION['cart'][$i]['product']['productid'].",".$_SESSION['cart'][$i]['quantity'].")";

                    if($result = $link->query($sqlCommand) == FALSE)
                    {
                        $success = false;
                    }

                }
                if($success == false)
                {
                    echo "Error!";
                }
                else
                {
                   
                }
            }
            else
            {
                echo "Error!";
            }

        }
       exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ceasuri de mana</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/index.css">
    <link rel="stylesheet" type="text/css" href="style/cart.css">
    <script src="script/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="script/index.js"></script>  
    <script src="script/cart.js"></script> 
<body>
    <div id="myModal" class="modal">

      <!-- Modal content -->
      <div class="modal-content">
        <span class="close">&times;</span>
        <p id="info"></p>
      </div>

    </div>
    
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
             <a href="reset-cart.php?location=cart.php" id="empty-cart">Empty Cart</a>
            <?php

                 if(isset($_SESSION['cart']))
                    {
                        if(count($_SESSION['cart'])>0)
                        {
                            $products = $_SESSION['cart'];
                            
                            for($i=0;$i<count($products);$i++)
                            {
                                    echo "<a id='".$products[$i]['product']['productid']."' href=#>"."<b>".$products[$i]['quantity']."</b> x " . $products[$i]['product']['name'] . "</a>"; 
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
                 <a href="reset-wishlist.php?location=cart.php" id="empty-wishlist">Empty Wishlist</a>
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

<div class="content">

    <!-- The Modal -->
    


    <div class="left-container">

        <?php

            if(isset($_SESSION['cart']))
            {

                $cartSize = count($_SESSION['cart']);

                if($cartSize > 0)
                {

                    for($i=0;$i<$cartSize;$i++)
                    {
                        echo '<div class="product-container" id="'.$_SESSION['cart'][$i]['product']['productid'].'">
                            <div class="image-container">
                                <img src="'.$_SESSION['cart'][$i]['product']['photopath'].'">
                            </div>
                            <div class="details-container">
                                <div class="description-container">
                                    <p id="name">'.$_SESSION['cart'][$i]['product']['name'].'</p>
                                    <p id="product-code">Product code: '.$_SESSION['cart'][$i]['product']['productid'].'</p>
                                    <p id="description">'.$_SESSION['cart'][$i]['product']['description'].'</p>
                                </div>
                                <div class="quantity-container">
                                    <p>Quantity</p>
                                    <select name="quantity">';

                                    for($j=1;$j<=50;$j++)
                                    {
                                        if($_SESSION['cart'][$i]['quantity'] == $j)
                                        {
                                            echo '<option selected="selected">'.$j.'</option>';
                                        }
                                        else
                                        {
                                            echo '<option>'.$j.'</option>';
                                        }
                                    }
                        echo '      </select>
                                </div>
                                <div class="price-container">
                                    <p id="price-paragraph">Price</p>
                                    <p id="price-value">$'.$_SESSION['cart'][$i]['product']['price'].'</p>';
                                    echo '<button name="delete-product" ';
                                    echo 'onclick="deleteProduct('.$_SESSION['cart'][$i]['product']['productid'].',';
                                    echo "$(this))";
                                    echo '">Delete me!</button>
                                </div>
                            </div>
                        </div>';

                    }
                }
                else
                {
                    echo '<div class="product-container">
                            <p>No products in cart.</p>   
                        </div>';
                } 

            }
            else
            {
                echo '<div class="product-container">
                            <p>No products in cart.</p>   
                        </div>';
            }

        ?>

    </div>
  
    <div class="right-container">
        <div class="order-details-container">
            <h3>Order Summary</h3>
            <hr>
            <table>
                <tr>
                    <th>Quantity</th>
                    <th>Product name</th>
                    <th>Price</th>
                </tr>
            <?php 

                $totalPrice = 0;

                if(isset($_SESSION['cart']))
                {
                    if($cartSize > 0)
                    {
                        
                       for($i=0;$i<$cartSize;$i++)
                       {   
                            
                            $totalPrice += $_SESSION['cart'][$i]['quantity'] * $_SESSION['cart'][$i]['product']['price'];

                            echo '
                                <tr id="'.$_SESSION['cart'][$i]['product']['productid'].'">
                                    <td>'.$_SESSION['cart'][$i]['quantity'].'</td>
                                    <td>'.$_SESSION['cart'][$i]['product']['name'].'</td>
                                    <td>$'.$_SESSION['cart'][$i]['product']['price'].'</td>
                                </tr>
                            ';
                        }
                    }
                

                }
            ?>
                
              
                <tr>
                    <td colspan="2">Total</td>
                    <td id="total">$<?php echo $totalPrice; ?></td>
                </tr>
            </table>
            <button id="place-order">Place Order!</button>
        </div>
    </div>

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
