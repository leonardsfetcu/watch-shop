<?php
    session_start();

    if(count($_SESSION['cart']))
    {
    	$_SESSION['cart'] = array();
    }
    header("Location: " . $_GET['location']);
?>