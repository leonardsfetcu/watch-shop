<?php

    session_start();
    require_once("db.php");
    if(!isset($_SESSION['cart']))
    {
        $_SESSION['cart'] = array();
    }

    if(!isset($_SESSION['wishlist']))
    {
        $_SESSION['wishlist'] = array();
    }
    
    
    if(isset($_POST['productid']))
    {
        $result = $link->query("select * from products where productid='".$_POST['productid']."'");

        if($result->num_rows==1)
        {
            $product = $result->fetch_assoc();

            if(isset($_POST['insertCart']))
            {
                $temp = array('quantity'=>1,'product'=>$product);
                $found=0;
                
                for($i=0;$i<count($_SESSION['cart']);$i++)
                {
                    if($_SESSION['cart'][$i]['product']['productid'] === $product['productid'])
                    {
                        $_SESSION['cart'][$i]['quantity']++;
                        $found=1;
                        break;
                    }
                }

                if($found===0)
                {
                    array_push($_SESSION['cart'],$temp);  
                }


                echo json_encode($_SESSION['cart']);
            }

            $found=0;
            if(isset($_POST['insertWishlist']))
            {
                for($i=0;$i<count($_SESSION['wishlist']);$i++)
                {
                    if($_SESSION['wishlist'][$i]['productid'] == $product['productid'])
                    {
                        $found =1;
                        break;
                    }
                }

                if($found==0)
                {
                    array_push($_SESSION['wishlist'],$product);
                }

                echo json_encode($_SESSION['wishlist']);
            }
            
        }
        else
        {
            echo "Error!";
        }
        exit();     
    }

    if(isset($_POST['resetWishlist']))
    {
        $_SESSION['wishlist']=array();
        echo "true";

        exit();
    }

    $filteredProducts = array();

    $result = $link->query("select products.*, brands.* from products inner join brands on products.brandid = brands.brandid");
    if($result->num_rows>0)
    {
        while($row = $result->fetch_assoc())
        {
            $filteredProducts[] = $row;
        }
    }

    if(isset($_GET['brand']))
    {
        $temp = array();
        
        for($i=0;$i<count($filteredProducts);$i++)
        {
            for($j=0;$j<count($_GET['brand']);$j++)
            {
                if($filteredProducts[$i]['brandname'] == $_GET['brand'][$j])
                {
                    $temp[] = $filteredProducts[$i];
                }
            }
        }
        $filteredProducts = $temp;
    }

    if(isset($_GET['gender']))
    {
        $temp = array();

        for($i=0;$i<count($filteredProducts);$i++)
        {
            for($j=0;$j<count($_GET['gender']);$j++)
            {
                if($filteredProducts[$i]['gender'] == $_GET['gender'][$j])
                {
                    $temp[] = $filteredProducts[$i];
                }
            }
            
        }

        $filteredProducts = $temp;
    }

    if(isset($_GET['price-start']) && isset($_GET['price-end']))
    {
        $temp = array();

        for($i=0;$i<count($filteredProducts);$i++)
        {
            if($filteredProducts[$i]['price']>$_GET['price-start'] and $filteredProducts[$i]['price'] < $_GET['price-end'])
            {
                $temp[] = $filteredProducts[$i];
            }
        }

        $filteredProducts = $temp;
    }

    if(isset($_GET['strap']))
    {
        $temp = array();

        for($i=0;$i<count($filteredProducts);$i++)
        {
            for($j=0;$j<count($_GET['strap']);$j++)
            {
                if($filteredProducts[$i]['braceletType'] == $_GET['strap'][$j])
                {
                    $temp[] = $filteredProducts[$i];
                }
            }
        }

        $filteredProducts = $temp;
        
    }

    if(isset($_GET['mechanism']))
    {
        $temp = array();

        for($i=0;$i<count($filteredProducts);$i++)
        {
            for($j=0;$j<count($_GET['mechanism']);$j++)
            {
                if($filteredProducts[$i]['mechanismType'] == $_GET['mechanism'][$j])
                {
                    $temp[] = $filteredProducts[$i];
                }
            }
        }

        $filteredProducts = $temp;
        
    }


?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/index.css">
    <link rel="stylesheet" type="text/css" href="style/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="script/jquery-3.3.1.min.js"></script>
    <script src="script/index.js"></script>
    <script src = "script/products.js"></script>
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
             <a href="reset-cart.php?location=products.php" id="empty-cart">Empty Cart</a>
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
                 <a href="reset-wishlist.php?location=products.php" id="empty-wishlist">Empty Wishlist</a>
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

    <div class="filters">
        <form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <h4 id="brand-heading" onclick="brandContainer()">Brand</h4>
            <hr>
            <div class="brand-container">
                <?php

                    $sqlCommand = "select brandid,brandname from brands order by brandname asc";
                    $brandCheckBox = array();
                    $result = $link->query($sqlCommand);

                    if($result->num_rows)
                    {

                        while($row = $result->fetch_assoc())
                        {
                            $brandCheckBox['brandname'] = false;

                            echo "<div class='filter-container'>";
                            echo "<input type='checkbox' name='brand[]' value ='".$row['brandname']."'
                            id='".$row['brandname']."'";
                            if(isset($_GET['brand']))
                            {
                                $found=0;
                                for($i=0;$i<count($_GET['brand']);$i++)
                                {
                                    if($_GET['brand'][$i]==$row['brandname'])
                                    {
                                        echo ' checked>';
                                        $found=1;
                                        break;
                                    }
                                }
                                if($found==0)
                                {
                                    echo ">";
                                }
                                
                            } 
                            else
                            {
                                echo ">";
                            }
                            echo "<label for='".$row['brandname']."'>"." ".$row['brandname']."</label>";
                            echo "</div>";
                        }
                    } 

                ?>
            </div>


            <h4 id="gender-heading" onclick="genderContainer()">Gender</h4>
            <hr>
            <div class="gender-container">
                <div class="filter-container">
                    <input type="checkbox" id="male" name="gender[]" value="male"
                    <?php 
                    if(isset($_GET['gender']))
                    {
                        for($i=0;$i<count($_GET['gender']);$i++)
                        {
                            if($_GET['gender'][$i]=='male')
                            {
                                echo 'checked';
                            }
                        }
                        
                    } 
                    ?>
                    >
                    <label for="male">Male</label>
                </div>

                <div class="filter-container">
                    <input type="checkbox" name="gender[]" id="female" value="female"
                    <?php 
                    if(isset($_GET['gender']))
                    {
                        for($i=0;$i<count($_GET['gender']);$i++)
                        {
                            if($_GET['gender'][$i]=='female')
                            {
                                echo 'checked';
                            }
                        }
                    } 
                    ?>
                    >
                    <label for="female">Female</label>
                </div>

                <div class="filter-container">
                    <input type="checkbox" name="gender[]" id="unisex" value="unisex"
                    <?php 
                    if(isset($_GET['gender']))
                    {
                        for($i=0;$i<count($_GET['gender']);$i++)
                        {
                            if($_GET['gender'][$i]=='unisex')
                            {
                                echo 'checked';
                            }
                        }
                    } 
                    ?>
                    >
                    <label for="unisex">Unisex</label>
                </div>
            </div>
            <h4 id="price-heading" onclick="priceContainer()">Price range</h4>
            <hr>
            <div class="price-container">
                <div style="margin:0 0 5px 10px;">
                    <label>
                        From:
                    </label>
                    <input type="text" name="price-start" size=10 value="<?php

                    if(isset($_GET['price-start']))
                    {
                        echo $_GET['price-start'];
                    }
                    else
                    {
                        echo '0';
                    }

                    ?>" />
                </div>

                <div style="margin: 0 0 10px 10px;">
                    <label>
                        To:
                    </label>
                    <input type="text" name="price-end" size=10 value="<?php

                    if(isset($_GET['price-end']))
                    {
                        echo $_GET['price-end'];
                    }
                    else
                    {
                        echo "5000";
                    }

                    ?>" />
                </div>
            </div>

            <h4 id="strap-heading" onclick="strapContainer()">Strap type</h4>
            <hr>
            <div class="strap-container">
                <div class="filter-container">
                    <input type="checkbox" id="stainless-steel" name="strap[]" value="Stainless Steel"
                    <?php 
                        if(isset($_GET['strap']))
                        {
                            for($i=0;$i<count($_GET['strap']);$i++)
                            {
                                if($_GET['strap'][$i] == 'Stainless Steel')
                                {
                                    echo "checked";
                                }
                            }
                        }

                     ?>
                    >
                    <label for="stainless-steel">Stainless Steel</label>
                </div>
                <div class="filter-container">
                    <input type="checkbox" id="textile" name="strap[]" value="Textile"

                    <?php 
                        if(isset($_GET['strap']))
                        {
                            for($i=0;$i<count($_GET['strap']);$i++)
                            {
                                if($_GET['strap'][$i] == 'Textile')
                                {
                                    echo "checked";
                                }
                            }
                        }

                     ?>

                    >
                    <label for="textile">Textile</label>
                </div>
                <div class="filter-container">
                    <input type="checkbox" id="leather" name="strap[]" value="Leather"

                    <?php 
                        if(isset($_GET['strap']))
                        {
                            for($i=0;$i<count($_GET['strap']);$i++)
                            {
                                if($_GET['strap'][$i] == 'Leather')
                                {
                                    echo "checked";
                                }
                            }
                        }

                     ?>

                    >
                    <label for="leather">Leather</label>
                </div>
                <div class="filter-container">
                    <input type="checkbox" id="rubber" name="strap[]" value="Rubber"

                     <?php 
                        if(isset($_GET['strap']))
                        {
                            for($i=0;$i<count($_GET['strap']);$i++)
                            {
                                if($_GET['strap'][$i] == 'Rubber')
                                {
                                    echo "checked";
                                }
                            }
                        }

                     ?>   

                    >
                    <label for="rubber">Rubber</label>
                </div>
                 <div class="filter-container">
                    <input type="checkbox" id="silicone" name="strap[]" value="Silicone"

                    <?php 
                        if(isset($_GET['strap']))
                        {
                            for($i=0;$i<count($_GET['strap']);$i++)
                            {
                                if($_GET['strap'][$i] == 'Silicone')
                                {
                                    echo "checked";
                                }
                            }
                        }

                     ?>   

                    >
                    <label for="silicone">Silicone</label>
                </div>
            </div>
            <h4 id="movement-heading" onclick="movementContainer()">Movement</h4>
            <hr>
            <div class="movement-container">
                <div class="filter-container">
                    <input type="checkbox" id="automatic" name="mechanism[]" value="Automatic" 
                    <?php 
                        if(isset($_GET['mechanism']))
                        {
                            for($i=0;$i<count($_GET['mechanism']);$i++)
                            {
                                if($_GET['mechanism'][$i] == 'Automatic')
                                {
                                    echo "checked";
                                }
                            }
                        }

                     ?>
                 >
                    <label for="automatic">Automatic</label>
                </div>
                <div class="filter-container">
                    <input type="checkbox" id="mechanical" name="mechanism[]" value="Mechanical"
                    <?php 
                        if(isset($_GET['mechanism']))
                        {
                            for($i=0;$i<count($_GET['mechanism']);$i++)
                            {
                                if($_GET['mechanism'][$i] == 'Mechanical')
                                {
                                    echo "checked";
                                }
                            }
                        }

                     ?>
                     >
                    <label for="mechanical">Mechanical</label>
                </div>
                <div class="filter-container">
                    <input type="checkbox" id="quartz" name="mechanism[]" value="Quartz" 

                    <?php 
                        if(isset($_GET['mechanism']))
                        {
                            for($i=0;$i<count($_GET['mechanism']);$i++)
                            {
                                if($_GET['mechanism'][$i] == 'Quartz')
                                {
                                    echo "checked";
                                }
                            }
                        }

                     ?>
                    >
                    <label for="quartz">Quartz</label>
                </div>
                <div class="filter-container">
                    <input type="checkbox" id="kinetic" name="mechanism[]" value="Kinetic"

                    <?php 
                        if(isset($_GET['mechanism']))
                        {
                            for($i=0;$i<count($_GET['mechanism']);$i++)
                            {
                                if($_GET['mechanism'][$i] == 'Kinetic')
                                {
                                    echo "checked";
                                }
                            }
                        }

                     ?>

                    >
                    <label for="kinetic">Kinetic</label>
                </div>
            </div>
            <br>

            <h4 id="apply">Apply Filters!</h4>
            <hr>
            <br>
            <button style="margin-left: 10px; width: 137px; margin-bottom: 2px;">Go</button>
            
            <button id="resetButton" style="margin-left: 10px; width: 137px; margin-bottom: 20px;" onclick="return resetFilters()">Reset Filters</button>
        </form>
    </div>

    <div class="products">

    <?php 

        if(count($filteredProducts)>0)
        {
            for($i=0;$i<count($filteredProducts);$i++)
            {
                echo '<div class="product-card">';
                echo '<div class="product-image">';
                echo '<a href="product-details.php?productid='.$filteredProducts[$i]['productid'].'" style="display:block;">';
                echo '<img src="'.$filteredProducts[$i]["photopath"].'"/>';
                echo '</a></div>';

                echo '<div class="product-info">';
                echo '<div class="row"><p id="description">' . $filteredProducts[$i]["name"] . '</p></div>';
                echo '<p id="price">Price: $' . $filteredProducts[$i]['price'].'</p><div class="button-row">
                <button name="add-cart" onclick="addToCart('. $filteredProducts[$i]['productid'] . ')">Add to Cart</button>
                <button name="add-wishlist" onclick="addToWishlist('.$filteredProducts[$i]['productid'].')">Add to Wishlist</button>
                </div>
                </div></div>';
            }
        }
        else
        {
            echo "<h3>Sorry...we didn't find any product based on your filters!";
        }
        
            


    ?>
  
    </div>


</div>


<!-- Footer -->
<div class="footer">
    
    <div class="contact">
        
        <h5>Contact us</h5>
        <ul>
            <li>By Telephone</li>
            <li>By Email</li>
            <li>Login / register</li>
            <li>Press</li>
            <li>Report a bug</li>    
        </ul>
  
    </div>
    
    <div class="returns">
        
        <h5>Returns & Policies</h5>
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
        
        <h5>Other Information</h5>
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

<script type="text/javascript">
    var brand=0;
    var gender=0;
    var strap=0;
    var movement=0;
    var price=0;

    function resetFilters()
    {
        $(".filters input").prop('checked',false);
        $("input[name='price-start']").val('0');
        $("input[name='price-end']").val('5000');
        return false;
    }
    function brandContainer()
    {
        brand = (brand + 1) % 2;

        if(brand)
        {
            document.getElementsByClassName("brand-container")[0].style.display = 'block';
        }
        else
        {
            document.getElementsByClassName("brand-container")[0].style.display = 'none';
        }

    }

    function genderContainer()
    {
        gender = (gender + 1) % 2;

        if(gender)
        {
            document.getElementsByClassName("gender-container")[0].style.display = 'block';
        }
        else
        {
            document.getElementsByClassName("gender-container")[0].style.display = 'none';
        }

    }

    function strapContainer()
    {
        strap = (strap + 1) % 2;

        if(strap)
        {
            document.getElementsByClassName("strap-container")[0].style.display = 'block';
        }
        else
        {
            document.getElementsByClassName("strap-container")[0].style.display = 'none';
        }

    }

    function movementContainer()
    {
        movement = (movement + 1) % 2;

        if(movement)
        {
            document.getElementsByClassName("movement-container")[0].style.display = 'block';
        }
        else
        {
            document.getElementsByClassName("movement-container")[0].style.display = 'none';
        }

    }

    function priceContainer()
    {
        price = (price + 1) % 2;

        if(price)
        {
            document.getElementsByClassName("price-container")[0].style.display = 'block';
        }
        else
        {
            document.getElementsByClassName("price-container")[0].style.display = 'none';
        }

    }
</script>

</body>
</html>
