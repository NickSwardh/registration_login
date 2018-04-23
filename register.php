<?php


require_once("init/init.php");


// Get Cross-Site-Forgery-Request cookie-name from config.
$xsfr_cookie = Config::Get('xsfr/cookie');


// Did the user submit the form and does the xsfr cookie contain a valid token?
if (isset($_POST['submit']) && Cookie::Auth($xsfr_cookie)) {

	$validate 	= new Validate();			// Instantiate a new Validate object.
	$error 		= $validate->CheckForm();	// Validate the submited form-data and $_POST variables.
	
	// No errors!? The form have passed validation and contains valid data! Awesome!
	if (!$error) {

		// Prepare an array with all user data.
		$data = array( 	
						'email' 	=> Sanitize('email'),
						'password' 	=> password_hash($_POST['pass1'], PASSWORD_DEFAULT),
						'firstname' => Sanitize('firstname'),
						'lastname' 	=> Sanitize('lastname')
					);
		
		// Register user in the database.
		if ($validate->Insert("members", $data)) {

			// Bake a session-cookie with the name from the INI-file with the value of the user id.
			$_SESSION[Config::Get('session/cookie')] = $validate->LastId();

			// Crush the xsfr cookies.
			Cookie::Crush($xsfr_cookie);

			// All set and done, redirect and sign in the new member.
			header("Location: index.php");
			exit;

		}

	}

}

// Bake a new cookie for this current page-load.
Cookie::XSFR_Cookies($xsfr_cookie);

?>
<!doctype html>

<html>
<head>
	<meta charset="UTF-8">
	<title>Register</title>
</head>
<body>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" cellspacing="2" cellpadding="2">
		<table border="0" id="register">
			<tr>
				<td>
					<label for="email">E-mail (ID):</label> <?php echo $error['email']; ?><br />
					<input type="email" name="email" id="email" placeholder="Enter your e-mail" maxlength="50" tabindex="1" value="<?php echo Escape($_POST['email']); ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="pass1">Password:</label> <?php echo $error['pass1']; ?><br />
					<input type="password" name="pass1" id="pass1" autocomplete="off" placeholder="Enter your Password" tabindex="2" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="pass2">Confirm:</label> <?php echo $error['pass1']; ?><br />
					<input type="password" name="pass2" id="pass2" autocomplete="off" placeholder="Confirm Password" tabindex="3" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="first">Full Name:</label> <?php echo $error['name']; ?><br />
					<input type="text" name="firstname" id="first" placeholder="First Name" maxlength="20" tabindex="4" value="<?php echo Escape($_POST['firstname']); ?>" />
					<input type="text" name="lastname" id="last" placeholder="Last Name" maxlength="20" tabindex="5" value="<?php echo Escape($_POST['lastname']); ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="submit" id="submit" tabindex="6" value="Continue" />
				</td>
			</tr>
		</table>
	</form>

</body>
</html>