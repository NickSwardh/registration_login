<?php

require_once('init/init.php');


// Is the user logged in?
if (!$user->LoggedIn()) {
	header("Location: login.php");
	exit;
}


// Get the xsfr cookie name.
$xsfr_cookie = Config::Get('xsfr/cookie');
$error		 = null;

// If the form was submited and the xsfr cookie is valid, process form!
if (isset($_POST['submit']) && Cookie::Auth($xsfr_cookie)) {

$validate 	= new Validate();			// Instantiate a new Validate-object.
$error 		= $validate->CheckForm();	// Validate the submited form and gather error info.

	// No errors? Form and data is valid!
	if (!$error) {

		// Check if the old password is the same as the one currently stored in the database.
		if (password_verify($_POST['oldpass'], $user->Result()->password)) {

			// Hash the new password.
			$new_pass = password_hash($_POST['pass1'], PASSWORD_DEFAULT);

			// Prepare an array with all data.
			$date = array('password' => $new_pass);

			// Update the database with the new password.
			$user->Update('members', $date, 'id', $user->ID());

			// All set! Reload the page with a success-parameter.
			header("Location: editpass.php?status=success");
			exit;

		}

	}

}

// Get a new token.
Cookie::XSFR_Cookies($xsfr_cookie);

?>
<!doctype html>

<html>
<head>
	<meta charset="UTF-8">
	<title>Change Password</title>
</head>
<body>

	<h1 style="margin-top: 0px">Change Password</h1>
	<?php if($_GET['status'] === 'success') { ?>

	<p>Your password has changed!</p>

	<?php } else { ?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table border="0" id="register">
			<tr>
				<td>
					<label for="oldpass">Old Password:</label> <?php echo $error['oldpass']; ?><br />
					<input type="password" name="oldpass" id="oldpass" placeholder="Enter old password" value="">
				</td>
			</tr>
			<tr>
				<td>
					<label for="pass1">New Password</label> <?php echo $error['pass1']; ?><br />
					<input type="password" name="pass1" id="pass1" placeholder="Enter new password" value="">
				</td>
			</tr>
			<tr>
				<td>
					<label for="pass2">Confirm:</label> <?php echo $error['pass1']; ?><br />
					<input type="password" name="pass2" id="pass2" placeholder="Confirm new password" value="">
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="submit" id="submit" value="Save Changes &raquo;">
				</td>
			</tr>
		</table>
	</form>
	<?php } ?>
	
	<p>Back to the <a href="index.php">main page</a> &raquo;</p>
			
</body>
</html>