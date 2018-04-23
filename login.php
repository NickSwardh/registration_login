<?php


require_once('init/init.php');


// If the user is logged in, redirect!
if ($user->LoggedIn()) {

	header("Location: index.php");
	exit;

}


$xsfr_cookie 	= Config::Get('xsfr/cookie'); 	// Get Cross-Site-Forgery-Request cookie-name from config.
$error 			= null; 						// Variable for holding error messsages.


// Did the user submit the form?
// Does the xsfr cookie contain a valid token?
if (isset($_POST['submit']) && Cookie::Auth($xsfr_cookie)) {

	// Instantiate a new object of the userdefine datatype 'Validate'.
	$validate = new Validate();

	// Validate username and password.
	$validate->Email($_POST['email']);
	$validate->ValidatePass($_POST['pass1']);

	// Gather errors, if any.
	$error = $validate->Error();

	// If no errors, the submited form and $_POST have passed the validation check. All form-data ok!
	if (!$error) {

		// Check for the user-email in the database.
		$validate->Select('members', 'email', Sanitize('email'));

		// Does the user exist?
		if ($validate->Count()) {

			// Yes, user exist. Get the user object.
			$user = $validate->Result();

			// Does the password match with the hashed password in the database?
			if (password_verify($_POST['pass1'], $user->password)) {

				// Yes, passwords match!

				// Did the user tick the `remember me` option?
				// Bake a cookie for automatic authentication.
				if (isset($_POST['remember']) == 'on') {

					$hash 				= Cookie::Token(16);			// Generate a new hash identifier (32-chars).
					$validator 			= Cookie::Token();				// Generate a new hash validator (64-chars by default).
					$validator_hash 	= hash('sha256', $validator);	// Hash the validator for storing in database.

					// Check if the user already has a remember-hash stored in the database.
					$validate->Select('autologin', 'user_id', $user->id);

					// Does a hash already exist? Update, else Insert new.
					if ($validate->Count()) {

						// Prepare an array with all user data.
						$token = array( 	
										'hash' 		=> $hash,
										'validator' => $validator_hash
									);

						// Update database.
						$validate->Update('autologin', $token, 'user_id', $user->id);

					} else {

						// Prepare an array with all user data.
						$token = array( 	
										'user_id' 	=> $user->id,
										'hash' 		=> $hash,
										'validator' => $validator_hash
									);

						// Insert the hash token into the database.
						$validate->Insert('autologin', $token);

					}

					// Finally, bake an autologin cookie.
					Cookie::RememberMe(Config::Get('autologin/cookie'), $hash . ':' . $validator);

				}

				// Set a session cookie for login validation.
				$_SESSION[Config::Get('session/cookie')] = $user->id;

				// Remove the xsfr-cookies, they're not needed anymore.
				Cookie::Crush($xsfr_cookie);

				// All set, user has logged in successfully!
				// Exit and go to main page.
				header('Location: index.php');
				exit;

			} else {
				$error['pass1'] = "Wrong password!";
			}

		} else {
			$error['email'] = "e-mail doesn't exist";
		}

	}

}

// Bake a new cookie for this session.
Cookie::XSFR_Cookies($xsfr_cookie);

?><!doctype html>

<html>
<head>
	<meta charset="UTF-8">
	<title>Login</title>
</head>
<body>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table border="0" id="register">
			<tr>
				<td>
					<label for="email">E-mail:</label> <?php echo $error['email']; ?><br />
					<input type="email" name="email" id="email" placeholder="Enter your e-mail" value="<?php echo Escape($_POST['email']); ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="password">Password:</label> <?php echo $error['pass1']; ?><br />
					<input type="password" name="pass1" id="pass1" placeholder="Enter your password" />
					<a href="resetpass.php" id="remember_link">Forgot password?</a>
				</td>
			</tr>
			<tr>
				<td>
					<label for="remember">Remember me:</label>
					<input type="checkbox" name="remember" id="remember" />
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="submit" id="submit" value="Sign In &raquo;" />
				</td>
			</tr>
			<tr>
				<td style="padding-top: 20px">
					Not a demo-member yet? <a href="register.php">Register now!</a>
				</td>
			</tr>
		</table>
	</form>

</body>
</html>