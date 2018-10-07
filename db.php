<?php 

	$host = 'localhost';
	$username = 'leonard';
	$password = 'Parola!';
	$db = 'wsdb';

	$link = new mysqli($host,$username,$password,$db);

	if($link->connect_error)
	{
		die("Connection error: " . $link->connect_error);
	}

?>