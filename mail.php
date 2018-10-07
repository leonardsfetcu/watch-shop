<?php

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\OAuth;
	use League\OAuth2\Client\Provider\Google;
	date_default_timezone_set('Etc/UTC');
	require 'vendor/autoload.php';
	

	$mail = new PHPMailer;
	$mail->isSMTP();

	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 587;	
	$mail->SMTPSecure = 'tls';	
	$mail->SMTPAuth = true;	
	$mail->AuthType = 'XOAUTH2';
	$mail->SMTPOptions = array(
	    'ssl' => array(
	        'verify_peer' => false,
	        'verify_peer_name' => false,
	        'allow_self_signed' => true
	    )
	);
	

	$rootEmail = 'swiftmail.tester@gmail.com';
	$clientId = '913287690575-i15e040kc2hupdl0jgr1mtuqod1evhnq.apps.googleusercontent.com';
	$clientSecret = '3q5k5XG1fCb_4LtbOjwQ4Pzo';
	$refreshToken = '1/3XsfnbZdMJpRisjI7jfw6qMVXl-RccAEJqFwMso76mg';
	
	//Create a new OAuth2 provider instance
	$provider = new Google(
	    [
	        'clientId' => $clientId,
	        'clientSecret' => $clientSecret,
	    ]
	);
	//Pass the OAuth provider instance to PHPMailer
	$mail->setOAuth(
	    new OAuth(
	        [
	            'provider' => $provider,
	            'clientId' => $clientId,
	            'clientSecret' => $clientSecret,
	            'refreshToken' => $refreshToken,
	            'userName' => $rootEmail,
	        ]
	    )
	);
	//Set who the message is to be sent from
	//For gmail, this generally needs to be the same as the user you logged in as
	

?>