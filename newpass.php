<?php


require_once('init/init.php');

$xsfr_cookie = Config::Get('xsfr/cookie');

// Flag for controlling the reset_pass form on the page.
$resetPass 	= false;
$error		= null;
$name		= '';

$email 		= Decode($_GET['id']);	// Decode 'htmlspecialchars'.
$token 		= $_GET['token'];


// Validate $email and $token and make sure they are what we expect them to be.
if(filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^[a-f0-9]{64}$/', $token)) {

	// Variables are valid! Check if email-id exist in the database.
	$user->Select('members', 'email', $email);

	// Does the user exist?
	if ($user->Count()) {

		$id 	= $user->Result()->id;			// Get user ID from database;
		$name 	= $user->Result()->firstname;	// Get user ID from database;

		$query 	= array(
			['user_id', '=', $id],				// Where User Id = $id
			['expire', 	'>', 'NOW()']			// And where expire > Now()
		);
		
		// Conditional query.
		$user->SelectAND('resetpass', $query);

		// Does a valid token exit?
		if ($user->Count()) {

			// Does the token match with the one stored in the database?
			// Use the hash_equals() function which is a time-attack safe comparison method.
			if (hash_equals($user->Result()->token, hash("sha256", $token)) ) {

				// Yes, tokens match! Set $resetPass to true.
				// (to determine if the form should be displayed or not)
				$resetPass = true;

				// Did the user submit a new password?
				if (isset($_POST['submit']) && Cookie::Auth($xsfr_cookie)) {

					$validate 	= new Validate();			// Instantiate a new Validate-object.
					$error 		= $validate->CheckForm();	// Validate passwords.

					// No errors? Awesome, form contains valid data!
					if (!$error)
					{
						
						$new_pass	= password_hash($_POST['pass1'], PASSWORD_DEFAULT);	// Hash password.
						$data 		= array('password' => $new_pass);					// Prepare an array with all user data.

						// Update database.
						$user->Update('members', $data, 'id', $id);
						$user->Delete('resetpass', 'user_id', '=', $id);				// Remove reset

						// Destroy the cross-site-request-forgery-cookies. Not needed anymore!
						Cookie::Crush($xsfr_cookie);

						// Reload page with a success-flag.
						header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
						exit;

					}

				}

			}

		}

	}

}

// Bake a new cookie for this session.
Cookie::XSFR_Cookies($xsfr_cookie);

?>
<!doctype html>

<html>
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>

<div id="dimblock"></div>

	<?php if ($resetPass) { ?>

		<h1>Hi, <?php echo $name; ?>!</h1>
		<p>Enter your new password below.</p>

		<form action="" method="post">
			<table border="0" id="register">
				<tr>
					<td>
						<label for="pass1">Password:</label> <?php echo $error['pass1']; ?><br />
						<input type="password" name="pass1" id="pass1" autocomplete="off" placeholder="New password" />
					</td>
				</tr>
				<tr>
					<td>
						<label for="pass2">Confirm:</label> <?php echo $error['pass1']; ?><br />
						<input type="password" name="pass2" id="pass2" autocomplete="off" placeholder="Confirm new password" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="submit" id="submit" value="Save &raquo;" />
					</td>
				</tr>
			</table>
		</form>

	<?php } else if ($_GET['status'] == 'success') { ?>

		<h1>Success!</h1>
		<p>Your password has successfully been changed.</p>
		<p>Click here to <a href="login.php">sign in</a> &raquo;</p>

	<?php } else { ?>

		<h1>Broken / Expired link</h1>
		<p>It looks like you're link is broken or has expired.</p>
		<p>Click here to <a href="resetpass.php">request a new password</a>.</p>

	<?php } ?>

</body>
</html>