<?php
    session_start();

    if(count($_SESSION['wishlist']))
    {
    	$_SESSION['wishlist'] = array();
    }
    header("Location: " . $_GET['location']);
?>