<?php
    
    session_start();
    require_once("db.php");

    // Update changes in database
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['price']))
    {
        if($_POST['productid']=='NULL')
        {
            $sql = "INSERT INTO products(productid,name,description,price,brandid,
            mechanismType,braceletType,waterResistant,productcode,gender,photopath) VALUES(
            NULL,'".$_POST['name']."','".$_POST['description']."','".$_POST['price']."','".
            $_POST['brand']."','".$_POST['mechanismType']."','".$_POST['braceletType']."','".$_POST['waterResistant']."','"
            .$_POST['code']."','".$_POST['gender']."','')";
            
            $result=$link->query($sql);
            if($result!=TRUE)
            {
                echo "Error: Unable to insert product in database!";
                exit();
            }
            
        }
        else
        {
            $sql = "UPDATE products SET price = '".$_POST['price']."' , name = '".$_POST['name']."' ,
                    description = '".$_POST['description']."', brandid = '".$_POST['brand']."',
                    mechanismType = '".$_POST['mechanismType']."', braceletType = '".$_POST['braceletType']."',
                    waterResistant = '".$_POST['waterResistant']."', productcode = '".$_POST['code']."' 
                    where productid = '".$_POST['productid']."'";

        $result = $link->query($sql);
        if($result != TRUE)
        {
            echo "Error: Unable to modify product in database!<br>";
            echo "<a href='index.php'>Please go home:</a>";
        }
        }
        
    }

    // --------------------------------------------------- //

    // Get Mechanism Type enum values
    $sql = "SHOW COLUMNS FROM `products` LIKE 'mechanismType'";
    $result = $link->query($sql);

    $row = $result->fetch_assoc();
    $type = $row['Type'];

    preg_match('/enum\((.*)\)$/', $type, $matches);
    $vals = explode(',', $matches[1]);

    $mechanismType = array();
    foreach($vals as $key => $value)
    {
        $value=trim($value, "'");
        $mechanismType[] = $value;
    }
    // ------------------------------------------------ //

    // Get Bracelet Type enum values
    $sql = "SHOW COLUMNS FROM `products` LIKE 'braceletType'";
    $result = $link->query($sql);

    $row = $result->fetch_assoc();
    $type = $row['Type'];

    preg_match('/enum\((.*)\)$/', $type, $matches);
    $vals = explode(',', $matches[1]);

    $braceletType = array();
    foreach($vals as $key => $value)
    {
        $value=trim($value, "'");
        $braceletType[] = $value;
    }
    // ------------------------------------------------ //

    // Get Water Resistant enum values
    $sql = "SHOW COLUMNS FROM `products` LIKE 'waterResistant'";
    $result = $link->query($sql);

    $row = $result->fetch_assoc();
    $type = $row['Type'];

    preg_match('/enum\((.*)\)$/', $type, $matches);
    $vals = explode(',', $matches[1]);

    $waterResistant = array();
    foreach($vals as $key => $value)
    {
        $value=trim($value, "'");
        $waterResistant[] = $value;
    }
    // ------------------------------------------------ //

    // Get brand information
    $sql = "SELECT * FROM brands";
    $result = $link->query($sql);
    $brands = array();

    if($result->num_rows>0)
    {
        while($row=$result->fetch_assoc())
        {
            $brands[] = $row;
        }
    }
    // ------------------------------------------------ //

    // Get list of products
    $allProducts = array();
    $sql = "SELECT products.*, brands.brandname FROM products INNER JOIN brands ON products.brandid = brands.brandid";
    $result = $link->query($sql);

    if($result->num_rows>0)
    { 
        while($row=$result->fetch_assoc())
        {
            $allProducts[] = $row;
        }
    }

    // ------------------------------------------------- //

    // Delete product from DB
    if(isset($_POST['delete']))
    {
        $sql = "select * from products where productid = ".$_POST['productid'];
        $result = $link->query($sql);

        if($result->num_rows == 1)
        {
            $sql = "DELETE FROM products WHERE productid = ".$_POST['productid'];
            $result = $link->query($sql);
            if($result == TRUE)
            {
                echo "success";
            }
            else
            {
                echo "fail";
            }
        }
        else
        {

            echo "fail";
        }

        exit();
    }

    // ------------------------------------------------ //

    // Get information about a specific product 
    if(isset($_POST['modify']))
    {
        $sql = "SELECT products.*, brands.* from products inner join brands on products.brandid = brands.brandid
         where products.productid = ".$_POST['productid'];

         $result = $link->query($sql);
         if($result->num_rows == 1)
         {
            echo json_encode($result->fetch_assoc());
         }
         else
         {
            $temp['error'] = "Unable to find product with id = ".$_POST['productid'];
            echo json_encode($temp);
         }
         exit();
    }

    // ----------------------------------------------- //

    // Search products in database
    if(isset($_GET['search']))
    {
        $sql = "select products.*,brands.* from products inner join brands on products.brandid = brands.brandid
         where products.productid = '".$_GET['search']."' or products.price like '%".$_GET['search']."%' or products.productcode like '%".$_GET['search']."%' or
        products.name like '%".$_GET['search']."%' or brands.brandname like '%".$_GET['search']."%' or products.description like '%".$_GET['search']."%' or products.gender like '".$_GET['search']."'";


        $result = $link->query($sql);
        $allProducts = array();
        while($row = $result->fetch_assoc())
        {
            $allProducts[] = $row;
        }
    }
    

?>

<!DOCTYPE html>
<html>
<head>
    <title>Ceasuri de mana</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/index.css">
    <link rel="stylesheet" type="text/css" href="style/admin.css">
    <script src="script/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="script/index.js"></script>  
    <script src="script/admin.js"></script> 
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
             <a href="reset-cart.php?location=cart.php" id="empty-cart">Empty Cart</a>
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

<!-- The flexible grid (content) -->
    
    <div class="content">
        <div class="product-list-container">
            <div class="search-bar">
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <label>Search product</label>
                    <input type="text" name="search">
                    <button>Search!</button>
                </form>
                <button name="insert-product">Insert New Product</button>
            </div>
            <div class="product-list">

            <?php 
                for($i=0;$i<count($allProducts);$i++)
                {
                    echo '<div class="product-card" id="'.$allProducts[$i]['productid'].'">
                    <div class="image-container">
                        <img src="'.$allProducts[$i]['photopath'].'">
                    </div>
                    <div class="details">
                        <table>

                            <tr>
                                <th colspan="2">Specification</th>
                            </tr>
                            <tr>
                                    <td>Product ID</td>
                                    <td>'.$allProducts[$i]['productid'].'</td>
                            </tr>
                            <tr>
                                    <td>Product name</td>
                                    <td>'.$allProducts[$i]['name'].'</td>
                            </tr>
                            <tr>
                                    <td>Description</td>
                                    <td>'.$allProducts[$i]['description'].'</td>
                            </tr>
                            <tr>
                                    <td>Brand</td>
                                    <td>'.$allProducts[$i]['brandname'].'</td>
                            </tr>
                            <tr>
                                    <td>Price</td>
                                    <td>'.$allProducts[$i]['price'].'</td>
                            </tr>
                        </table>
                        <div class="actions">
                        <button onclick="modifyProduct('.$allProducts[$i]['productid'].')">Modify</button>
                        <button onclick="deleteProduct('.$allProducts[$i]['productid'].')">Delete</button>
                    </div>
                    </div>
                </div>';
                }
            ?>   
            </div>
        </div>
        <div class="product-details-container">
            <form enctype="multipart/form-data" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="row">
                    <label>Product Name</label>
                    <input type="text" name="name">
                </div>

                <div class="row">
                    <label>Description</label>
                    <textarea name="description"></textarea>
                </div>

                <div class="row">
                    <label>Price</label>
                    <input type="text" name="price">
                </div>

                <div class="row">
                    <label>Select image to upload</label>
                    <input type="file" name="fileToUpload" id="fileToUpload">
                </div>
                <div class="row">
                    <label>Brand</label>
                    <select name="brand">
                        <?php
                            for($i=0;$i<count($brands);$i++)
                            {
                                echo "<option value='".$brands[$i]['brandid']."'>".$brands[$i]['brandname']."</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="unisex">Unisex</option>
                    </select>
                </div>

                <div class="row">
                    <label>Mechanism Type</label>
                    <select name="mechanismType">
                        <?php
                            for($i=0;$i<count($mechanismType);$i++)
                            {
                                echo "<option value='".$mechanismType[$i]."'>".$mechanismType[$i]."</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <label>Bracelet Type</label>
                    <select name="braceletType">
                        <?php
                            for($i=0;$i<count($braceletType);$i++)
                            {
                                echo "<option value='".$braceletType[$i]."'>".$braceletType[$i]."</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <label>Water Resistant</label>
                    <select name="waterResistant">
                        <?php
                            for($i=0;$i<count($waterResistant);$i++)
                            {
                                echo "<option value='".$waterResistant[$i]."'>".$waterResistant[$i]."</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <label>Product Code</label>
                    <input type="text" name="code">
                </div>

                <div class="row" id="id" style="display: none;">
                    <label>Product Id</label>
                    <input type="text" name="productid">
                </div>

                <div class="row">
                    <button type="submit-changes">Save</button>
                    <button type="reset">Cancel</button>
                </div>
            </form>
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
