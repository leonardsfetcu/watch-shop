<?php 
session_start();

if(isset($_SESSION['username']))
{
    header("Location: index.php");
}

require_once('db.php');

$login_error = '';

function loadCart()
{
    global $link;


    $sql_command = "SELECT orderid FROM orders WHERE userid = ".$_SESSION['userid']." and finished = 0";
            $result = $link->query($sql_command);

            if($result->num_rows == 1)
            {   
                $row = $result->fetch_assoc();
                $orderid = $row['orderid'];
                
                $sql_command = "SELECT products.*, order_details.quantity FROM order_details INNER JOIN products on order_details.productid = products.productid
                                    WHERE order_details.orderid = ".$orderid;
                $result = $link->query($sql_command);
                if($result->num_rows > 0)
                {
                    $count = count($_SESSION['cart']);
                    while($row = $result->fetch_assoc())
                    {   
                        $quantity = $row['quantity'];
                        unset($row['quantity']);
                        $_SESSION['cart'][$count]['product'] =$row;
                        $_SESSION['cart'][$count]['quantity'] = $quantity;
                        $count++;

                    }
                }
                
            }
}

function loadWishlist()
{
    global $link;

    $sql_command = "SELECT orderid FROM orders WHERE userid = ".$_SESSION['userid']." and wishlisted = 1";
            $result = $link->query($sql_command);

            if($result->num_rows == 1)
            {   
                $row = $result->fetch_assoc();
                $orderid = $row['orderid'];
                
                $sql_command = "SELECT products.*, order_details.quantity FROM order_details INNER JOIN products on order_details.productid = products.productid
                                    WHERE order_details.orderid = ".$orderid;

                $result = $link->query($sql_command);
                if($result->num_rows > 0)
                {
                    $count = count($_SESSION['wishlist']);
                    while($row = $result->fetch_assoc())
                    {   
                        $_SESSION['wishlist'][$count++] = $row;
                    }
                }
            
            }
}


if(isset($_POST['login-button']))
{

    $username = $_POST['username'];
    $password = $_POST['pass'];

    $sql_command = "SELECT users.*, roles.* from users INNER JOIN roles ON users.roleid = roles.roleid WHERE username LIKE '$username'";

    $result = $link->query($sql_command);

    if($result->num_rows == 0)
    {
        $login_error = "Wrong username or password!";
    }
    else
    {
        $row = $result->fetch_assoc();
        if(password_verify($password,$row['password']))
        {
            $_SESSION['userid'] = $row['userid'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['lastname'] = $row['lastname'];
            $_SESSION['roleid'] = $row['roleid'];
            $_SESSION['role'] = $row['name'];
            $_SESSION['cart'] = array();
            $_SESSION['wishlist'] = array();
            $_SESSION['wallet'] = $row['wallet'];

            loadCart();
            loadWishlist();
            if($_SESSION['role']=='admin')
            {
                $count=0;
               $temp="<h4>".$_SESSION['firstname']." ".$_SESSION['lastname']."</h4><h4>Wallet: $".$_SESSION['wallet']."</h4><hr>";
                $_SESSION['upper-menu'][$count++]=$temp;
                $temp = "<a href='account-details.php'>Account Details</a>";
                $_SESSION['upper-menu'][$count++]=$temp;
                $temp = "<a href='products-admin.php'>Product Administration</a>";
                $_SESSION['upper-menu'][$count++]=$temp;
                $temp = "<a href='users-admin.php'>Users Administration</a>";
                $_SESSION['upper-menu'][$count++]=$temp;
                $temp = "<a href='order-details.php'>Order Details</a>";
                $_SESSION['upper-menu'][$count++]=$temp;
                $temp = "<a href='wishlist.php'>Wishlist</a>";
                $_SESSION['upper-menu'][$count++]=$temp;
                $temp = "<a href='session.php' id='special-row'>Logout</a>";
                $_SESSION['upper-menu'][$count++]=$temp;
            }
            else
            {
                $count = 0;
                $temp="<h4>".$_SESSION['firstname']." ".$_SESSION['lastname']."</h4><h4>Wallet: $".$_SESSION['wallet']."</h4><hr>";
                $_SESSION['upper-menu'][$count++]=$temp;
                $temp = "<a href='account-details.php'>Account Details</a>";
                $_SESSION['upper-menu'][$count++]=$temp;
                $temp = "<a href='order-details.php'>Order Details</a>";
                $_SESSION['upper-menu'][$count++]=$temp;
                $temp = "<a href='wishlist.php'>Wishlist</a>";
                $_SESSION['upper-menu'][$count++]=$temp;
                $temp = "<a href='session.php' id='special-row'>Logout</a>";
                $_SESSION['upper-menu'][$count++]=$temp;
            }
            header("Location: index.php");
        }
        else
        {
            $login_error = 'Wrong username or password!';
        }
    }

    $link->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style/login.css">
    <link rel="stylesheet" type="text/css" href="style/index.css">
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
             <a href="reset-cart.php?location=login.php" id="empty-cart">Empty Cart</a>
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
                 <a href="reset-wishlist.php?location=login.php" id="empty-wishlist">Empty Wishlist</a>
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
            
         <div class="login-header">
                <div class="img-container">
                    <img src="resources/login-avatar.png" alt="login avatar" />
                </div>


                <div class="login-form">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="error">
                            <?php echo $login_error; ?>
                        </div>
                        <input type="text" name="username" placeholder="Enter username" required />
                        <input type="password" name="pass" placeholder="Enter password" required />
                        <label>
                            <input type="checkbox" checked="checked" name="remember"> Remember me
                         </label>
                        <input type="submit" name="login-button" value="Login" />
                    </form>
                    
                    <div style="display:flex; justify-content: space-between; align-items: center;">
                        <p>No account? <a href="signup.php"><input name="register" type="button" value="Go to register"></a></p>
                        <a href="reset-password.php">Forgot password?</a>
                    </div>
                </div>
             
         </div>
         <div class="login-footer">
             
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
