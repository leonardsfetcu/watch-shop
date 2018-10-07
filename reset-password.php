<?php

	require_once("db.php");
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\OAuth;
	use League\OAuth2\Client\Provider\Google;
	date_default_timezone_set('Etc/UTC');
	require 'vendor/autoload.php';
	require 'mail.php';
	
	
	$error = '';
	$success= '';
	$validationSucces = true;

	if(isset($_POST['submit']))
	{

		$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);

		if(strlen($email) == 0 || filter_var($email, FILTER_VALIDATE_EMAIL) == false)
        {
            $validationSucces = false;
            $error = 'Field is either empty or invalid. Please insert a valid email adress! '; 
        }

        if($validationSucces == true)
        {
        	$sql = "select * from users where email like '".$email."'";
        	$result = $link->query($sql);

			if($result->num_rows==1)
			{
				$row = $result->fetch_assoc();

				$randomPassword = substr(md5(microtime()),rand(0,26),10);
				$randomPasswordHash = password_hash($randomPassword,PASSWORD_DEFAULT);

				$sql = "UPDATE users SET password = '".$randomPasswordHash."' WHERE userid = ".$row['userid'];
				$result = $link->query($sql);

				if($result != TRUE)
				{
					$error = "Error: Unable to reset your password!";
				}

				$mail->setFrom($rootEmail, 'Watch-Shop authentication&reset mechanism');
				//Set who the message is to be sent to
				$mail->addAddress($email, $row['firstname']." ".$row['lastname']);
				//Set the subject line
				$mail->Subject = 'Reset password';
				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				$mail->CharSet = 'utf-8';
				$mail->msgHTML('<h2> Hello!</h2><p>Your new password is: '.$randomPassword."</p>");
				//Replace the plain text body with one created manually
				$mail->AltBody = 'Hello! Your password is:'.$randomPassword;
				//Attach an image file
				//$mail->addAttachment('images/phpmailer_mini.png');
				//send the message, check for errors

				if (!$mail->send()) 
				{
				    $error = "Mailer Error: " . $mail->ErrorInfo;
				}
				else
				{
				    $success = "An email was send to ".$email."!";
				}
			}
			else
			{
				$error = "Your email is not in database!";
			}
	    }
		
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Reset password</title>
	<style>

	.container {
		display: flex;
		padding: 100px;
		}

	form {
		min-width: 500px;
		min-height: 300px;
		margin:auto;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-content: center;
		border: 4px solid #ddd;
		padding: 20px;
	}
	input,button {
		padding: 5px;
		margin: 10px;
	}
	</style>
</head>
<body>

<div class="container">
	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
		<label for="email"><h3>Enter your email</h3></label>
		<?php
		if($success !='')
		{
			echo "<p style='color: green;'>".$success."</p>";
		}
		else
			if($error !='')
			{
				echo "<p style='color: red;'>".$error."</p>";
			}
		?>
		<input type="text" name="email">
		<button name="submit">Reset!</button>
		<a href="login.php">Or...go to login</a>
	</form>
</div>

</body>
</html>