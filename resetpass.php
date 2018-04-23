<?php


require_once('init/init.php');


$xsfr_cookie 	= Config::Get('xsfr/cookie'); 	// Get Cross-Site-Forgery-Request cookie-name from config.
$error 			= null;							// Variable for holding error messsages.

if (isset($_POST['submit']) && Cookie::Auth($xsfr_cookie)) {

	// Create a new Validate-object.
	$validate = new Validate();

	// First things first, delete all expired tokens from the database to keep things neat and clean ;)
	$validate->Delete('resetpass', 'expire', '<', date("Y-m-d H:i:s"));

	// Validate the e-mail address.
	$validate->Email($_POST['email']);
	
	// Any errors?
	$error = $validate->Error();

	// Form ok? No errors?
	if (!$error) {

		// E-mail ok, fetch user data from the database.
		$validate->Select('members', 'email', $_POST['email']);

		// If the email exist...
		if ($validate->Count()) {

			// Members email.
			$email 		= $validate->Result()->email;
			$memberName = $validate->Result()->firstname . ' ' . $validate->Result()->lastname;

			// Get userinfo from the database...
			$user_id 	= $validate->Result()->id;									// Get user ID.
			$token		= Cookie::Token();											// Generate token.
			$token_hash	= hash("sha256", $token);									// Hash the token
			$minutes	= 20;														// Expiration time in minutes.
			$expire		= date("Y-m-d H:i:s", strtotime("+{$minutes} minutes"));	// Set expiration time from now.
 
			// Does a token for the password reset exist?
			$validate->Select('resetpass', 'user_id', $user_id);

			// Yes, token exist.
			if ($validate->Count()) {

				// Prepare an array with all user data.
				$data = array( 	
								'token' 	=> $token_hash,
								'expire' 	=> $expire
							);

				// Update with new a new has and a new expiraration time.
				$validate->Update('resetpass', $data, 'user_id', $user_id);

			} else {

				// Prepare an array with all user data.
				$data = array( 	
								'user_id' 	=> $user_id,
								'token' 	=> $token_hash,
								'expire' 	=> $expire
							);

				// Insert a new token.
				$validate->Insert('resetpass', $data);
			}

			// Create a link with the e-mail Id and the token.
			$link 		= 'https://www.nswardh.com/demo/login/newpass.php?id=' . $email . '&token=' . $token;

			// Subject and message.
			$subject	= 'Reset Your Demo Password - nswardh.com';
			$message	= '<html><head><title>nswardh.com - Reset Password</title>
							<head>
							<body>
							<h1>Reset Password</h1>
							<p>To reset your password, please click the link below and follow the instructions. The link will expire in ' . $minutes . ' minutes.</p>
							<a href="' . $link . '">' . $link . '</a>
							</body>
							</html>';

			// Send mail with the instructions to the users e-mail account!
			if (SendMail($email, $memberName, $subject, $message)) {

				// All done! Reload the page.
				header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
				exit;

			}

			// All done! Reload the page.
			$error['email'] = "Error sending e-mail!";

		} else {

			$error['email'] = "E-mail doesn't exist!";

		}

	}

}

// Bake a new cookie for this session.
Cookie::XSFR_Cookies($xsfr_cookie);

?><!doctype html>

<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title></title>

	<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet" />
	<link rel="stylesheet" href="layout.css">

</head>
<body>


<div id="dimblock"></div>


<section id="boxes">
	<div id="container">

		<div id="box1">
			<h1>Reset Password</h1>
			<p>Enter your e-mail &amp; check your inbox for instructions.</p>
		</div>

		<img id="arrow" src="arrow.png" />

		<div id="box2">

			<h1 style="margin-top: 0px">Reset Password</h1>
			<?php if($_GET['status'] === 'success') { ?>
			<p>Success! Check your inbox.</p>

			<?php } else { ?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<table border="0" id="register">
					<tr>
						<td>
							<label for="email">E-mail:</label> <?php echo $error['email']; ?><br />
							<input type="email" name="email" id="email" placeholder="Your e-mail" value="<?php echo Escape($_POST['email']); ?>" />
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" name="submit" id="submit" value="Send new password &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			<?php } ?>
			
			<p>Back to the <a href="index.php">main page</a> &raquo;</p>

		</div>

	</div>
</section>

</body>

</html>