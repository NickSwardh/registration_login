<?php

require_once('init/init.php');


// Is the user logged in?
if (!$user->LoggedIn()) {
	header("Location: login.php");
	exit;
}


// Get the csfr cookie name.
$xsfr_cookie = Config::Get('xsfr/cookie');
$error		 = null;

// If the form was submited and the csfr cookie is valid, process form!
if (isset($_POST['submit']) && Cookie::Auth($xsfr_cookie)) {

$email = trim(Sanitize('email'));

$validate = new Validate();	// Instantiate a new Validate-object.
$validate->Email($email);	// Validate the submited form and gather error info.
$error = $validate->Error();	// Collect errors, if any.

	// If there are no errors and the email is not the current email...
	if (!$error) {

		if ($user->Result()->email != $email) {
			$validate->Select('members', 'email', $email);

			// Nothing found? Good, the new e-mail can be registered.
			if(!$validate->Count()) {

				$data = array('email' => $email);			// Prepare an array with all user data.
				$user->Update('members', $data, 'id', $user->ID());	// Update the database with the new password.

				// Destroy the xsfr-cookies.
				Cookie::Crush($xsfr_cookie);

				// All set! Reload the page with a success-parameter.
				header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
				exit;

			} else {
				$error['email'] = "E-mail already exist!";
			}

		} else {
			$error['email'] = "Can't update the same e-mail!";
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
	<title>Update E-mail</title>
</head>
<body>

	<h1 style="margin-top: 0px">Update E-mail</h1>

	<?php if($_GET['status'] === 'success') { ?>
	<p>Your e-mail was updated!</p>

	<?php } else { ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table border="0" id="register">
			<tr>
				<td>
					<label for="email">E-mail:</label> <?php echo $error['email']; ?><br />
					<input type="email" name="email" id="email" placeholder="Enter your e-mail" value="<?php echo $user->Result()->email; ?>">
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="submit" id="submit" value="Save &raquo;">
				</td>
			</tr>
		</table>
	</form>
	<?php } ?>

	<p>Back to the <a href="index.php">main page</a> &raquo;</p>

</body>
</html>
