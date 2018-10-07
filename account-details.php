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


    if(isset($_POST['firstname']))
    {
        $errorArray = array();

        $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
        $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
        $birthdate = filter_var($_POST['birthdate'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
        $phone = filter_var($_POST['phone'],FILTER_SANITIZE_STRING);
        $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
        $currentPassword = filter_var($_POST['currentPassword'], FILTER_SANITIZE_STRING);
        $newPassword = filter_var($_POST['newPassword'], FILTER_SANITIZE_STRING);
        $newPassword2 = filter_var($_POST['newPassword2'], FILTER_SANITIZE_STRING);
        $username = filter_var($_POST['username'],FILTER_SANITIZE_STRING);


        $sqlCommand = "select * from users where username like '".$username."'";
        $user = '';
        $result = $link->query($sqlCommand);

        if($result->num_rows!=1)
        {
            $errorArray['username'] = "You are trying to hack us! I caught you!";
        }
        else
        {
            $user = $result->fetch_assoc();
        
            if(filter_var($email, FILTER_VALIDATE_EMAIL)==false)
            {
                $errorArray['email']="Invalid format for email!";
            }

            $checkPassword='';

            if(strlen($currentPassword) == 0)
            {
                $checkPassword = 'no-edit';
            }

            if($newPassword === $newPassword2 and $checkPassword !== 'no-edit')
            {
                if(strlen($newPassword)>7)
                {   
                    if(password_verify($currentPassword,$user['password']))
                    {
                        $checkPassword='true';
                    }
                    else
                    {
                        $errorArray['currentPassword'] = "Invalid password";
                    }
                }
                else
                {
                    $errorArray['newPassword'] = "Password has less than 8 characters";
                }
            }
            else
            {
                if($checkPassword!=='no-edit')
                {
                    $errorArray['newPassword'] = "Passwords don't match";
                }
            }

            if($checkPassword == 'true')
            {
                $sqlCommand = "UPDATE users SET `firstname`='".$firstname."' , `lastname`='".$lastname."' , `email`='".$email."' , `address`='".$address."' , `phone`='".$phone."' , `birthdate`='".$birthdate."' , `password`='".password_hash($newPassword,PASSWORD_BCRYPT)."' WHERE `username` like '".$username."'";
                
                if($link->query($sqlCommand) != TRUE)
                {
                    $errorArray['query'] = "Error: Unable to make changes in database1";
                }
                else
                {
                    $_SESSION['firstname']=$firstname;
                    $_SESSION['lastname']=$lastname;
                }
            }




            if($checkPassword=='no-edit')
            {
                 $sqlCommand = "UPDATE users SET `firstname`='".$firstname."' , `lastname`='".$lastname."' , `email`='".$email."' , `address`='".$address."' , `phone`='".$phone."' , `birthdate`='".$birthdate."' WHERE `username` like '".$username."'";
                 
                 if($link->query($sqlCommand)!=TRUE)
                 {
                    $errorArray['query'] = "Error: Unable to make changes in database2";
                 }
                 else
                 {
                    $_SESSION['firstname']=$firstname;
                    $_SESSION['lastname']=$lastname;
                 }
            }

            echo json_encode($errorArray);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="script/jquery-3.3.1.min.js"></script>
    <script src="script/index.js"></script>
    <script src="script/account-details.js"></script>
    <link rel="stylesheet" type="text/css" href="style/account-details.css">
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
             <a href="reset-cart.php?location=account-details.php" id="empty-cart">Empty Cart</a>
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
                 <a href="reset-wishlist.php?location=account-details.php" id="empty-wishlist">Empty Wishlist</a>
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
                    <li><a href="order-details.php">My Orders</a></li>
                    <li class="active"><a href="account-details.php">Account Settings</a></li>
                    <li><a href="vouchers.php">Vouchers</a></li>
                    <li><a href="wishlist.php">Wishlist</a></li>
                </ul>
            </div>
        </div>


    <!-- Right Side -->
        <div class="right-container">
            <div class="container">    
                <div class="image-container">
                    <img src="resources/login-avatar.png" alt="avatar">
                    <button name="edit-account-data">Edit account data</button>
                    <button name="save-account-data">Save</button>
                    <span style="display: none;"" id="username"><?php echo $_SESSION['username']; ?></span>
                </div>
                <div class="data-container">
                    <div class="row">
                        <p>Firstname</p><input type="text" name="firstname" value="<?php echo $currentUser['firstname']; ?>" >
                    </div>
                    <div class="row">
                        <p>Last name</p><input type="text" name="lastname" value="<?php echo $currentUser['lastname'];?>" >
                    </div>
                    <div class="row">
                        <p>Birthdate</p><input type="text" name="birthdate" value="<?php echo $currentUser['birthdate'];?>" >
                    </div>
                    <div class="row">
                        <p>Email</p><input type="text" name="email" value="<?php echo $currentUser['email'];?>" >
                    </div>
                     <div class="row">
                        <p>Phone</p><input type="text" name="phone" value="<?php echo $currentUser['phone'];?>" >
                    </div>
                    <div class="row">
                        <p>Address</p><textarea name="address" ><?php echo $currentUser['address'];?></textarea>
                    </div>
                    <div class="row">
                        <p>Current Password</p><input type="password" name="current-password" >
                    </div>
                    <div class="row">
                        <p>New Password</p><input type="password" name="new-password" >
                    </div>
                    <div class="row">
                        <p>Retype Password</p><input type="password" name="new-password2" >
                    </div>
                </div>
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
