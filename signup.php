<?php 
    require_once("db.php");
    session_start();

    $username_error = '';
    $password_error = '';
    $email_error = '';
    $firstname_error = '';
    $lastname_error = '';
    $password2_error = '';
    $form_error ='*All fields are mandatory';

    $validationSucces = true;

    function sanitizeFields($firstname,$lastname,$username,$email,$password,$password2)
    {
        $firstname = filter_var($firstname, FILTER_SANITIZE_STRING);
        $lastname = filter_var($lastname, FILTER_SANITIZE_STRING);
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_STRING);
        $password = filter_var($password, FILTER_SANITIZE_STRING);
        $password2 = filter_var($password2, FILTER_SANITIZE_STRING);
    }

    function validateFields($firstname,$lastname,$username,$email,$password,$password2)
    {
        global $validationSucces,$link,$username_error,$password_error,$email_error,$firstname_error,$lastname_error,$password2_error,$form_error;

        if(strlen($firstname) == 0)
        {
            $validationSucces = false;
            $firstname_error .= 'Field is either empty or invalid. Please insert a valid firstname! ';
        }

        if(strlen($lastname) == 0)
        {
            $validationSucces = false;
            $lastname_error .= 'Field is either empty or invalid. Please insert a valid lastname! '; 
        }

        if(strlen($email) == 0 || filter_var($email, FILTER_VALIDATE_EMAIL) == false)
        {
            $validationSucces = false;
            $email_error .= 'Field is either empty or invalid. Please insert a valid email adress! '; 
        }
        else
        {
            $result = $link->query("select userid from users where email like '" . $email . "'");

            if($result->num_rows)
            {
                $email_error = 'Email already exists in database. Please insert another email!';
                $validationSucces = false;
            }
        }

        if(strlen($password) >= 8 && strlen($password2) >= 8)
        {
            if($password != $password2)
            {
                $validationSucces = false;
                $password2_error .= "Password doesn't match! ";
            }
        }
        else
        {
            $validationSucces = false;
            $password_error .= 'Field is either empty or invalid. Please insert a valid password adress! (min 8 characters length) '; 
        }

        if(strlen($username) > 0)
        {
            $result = $link->query("select userid from users where username like '" . $username . "'");

            if($result->num_rows)
            {
                $username_error = 'Username already exists in database. Please insert another username!';
                $validationSucces = false;
            }

        }
        else
        {
            $username_error = 'Field must not be empty!';
            $validationSucces = false;
        }
    }

    if(isset($_POST['signup-button']))
    {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['pass'];
        $password2 = $_POST['repass'];
        // sanitize external data from user
        sanitizeFields($firstname,$lastname,$username,$email,$password,$password2);

        // validate user data
        validateFields($firstname,$lastname,$username,$email,$password,$password2);

        if($validationSucces == true)
        {
            $password = password_hash($password,PASSWORD_BCRYPT);

            $sqlCommand = "INSERT INTO users(firstname,lastname,username,email,password) VALUES('$firstname','$lastname','$username','$email','$password')";
            $result = $link->query($sqlCommand);

            if(!$result)
            {
                $form_error = 'Server error: unable to send form data to database';
            }
            else
            {
                header("Location: login.php");
            }

        }
    }

    $link->close();


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
             <a href="reset-cart.php?location=signup.php" id="empty-cart">Empty Cart</a>
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
                 <a href="reset-wishlist.php?location=signup.php" id="empty-wishlist">Empty Wishlist</a>
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
    <a href="products.php?gender=female" id="women-link">Women</a>
    <a href="products.php?gender=male" id="men-link">Men</a>
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
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <label>First name</label>
                        <input type="text" name="firstname" placeholder="Enter first name"
                        <?php
                        if(isset($_POST['signup-button']))
                        {
                            echo ' value="'.$firstname.'"';
                        }
                        ?>
                        />
                        <div class="error">
                            <?php echo $firstname_error; ?>
                        </div>
                        <label>Last name</label>
                        <input type="text" name="lastname" placeholder="Enter last name"
                        <?php
                        if(isset($_POST['signup-button']))
                        {
                            echo ' value="'.$lastname.'"';
                        }
                        ?>
                        />
                        <div class="error">
                            <?php echo $lastname_error; ?>
                        </div>
                        <label>Your email</label>
                        <input type="text" name="email" placeholder="Enter email"
                        <?php 
                        if(isset($_POST['signup-button']))
                        {
                            echo ' value="'.$email.'"';
                        }
                        ?>
                        />
                        <div class="error">
                            <?php echo $email_error; ?>
                        </div>
                        <label>Choose a username</label>
                        <input type="text" name="username" placeholder="Enter a username"
                        <?php
                        if(isset($_POST['signup-button']))
                        {
                            echo ' value="'.$username.'"';
                        }
                        ?>
                        />
                        <div class="error">
                            <?php echo $username_error; ?>
                        </div>
                        <label>Type a password</label>
                        <input type="password" name="pass" placeholder="Enter password"  />
                        <div class="error">
                            <?php echo $password_error; ?>
                        </div>
                        <label>Retype password</label>
                        <input type="password" name="repass" placeholder="Enter password"  />
                        <div class="error">
                            <?php echo $password2_error; ?>
                        </div>
                        <div class="error">
                            <?php echo $form_error; ?>
                        </div>
                        <input name="signup-button" type="submit" value="Register">
                    </form>
                    
                    <div style="display:flex; justify-content: space-between; align-items: center;">
                        <p>Have an account? <a href="login.php"><input name="register" type="button" value="Go to login"></a></p>
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
