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

$validate 	= new Validate();
$error 		= $validate->CheckForm();

	// If the form pass validation check...
	if (!$error) {

		// Prepare an array with all user data.
		$data = array(
						'firstname' => Sanitize('firstname'),
						'lastname' 	=> Sanitize('lastname')
					);

		// Update database.
		$user->Update('members', $data, 'id', $user->ID());

		// Destroy the xsfr-cookies, they're not need enymore.
		Cookie::Crush($xsfr_cookie);

		// Reload the page with a success flag.
		header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
		exit;

	}

}

// Bake a new cookie for this session.
Cookie::XSFR_Cookies($xsfr_cookie);

?>
<!doctype html>

<html>
<head>
	<meta charset="UTF-8">
	<title>Update Profile</title>
</head>
<body>

	<h1>Update Profile</h1>

	<?php if($_GET['status'] === 'success') { ?>

	<p><img style="max-width: 32px; max-height: 32px; vertical-align: middle;" src="check.png" /> Your profile was updated!</p>

	<?php } else { ?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table border="0" id="register">
			<tr>
				<td>
					<label for="last">Name</label> <?php echo $error['name']; ?><br />
					<input type="text" name="firstname" id="first" placeholder="First name" value="<?php echo Escape($user->Result()->firstname); ?>">
					<input type="text" name="lastname" id="last" placeholder="Last name" value="<?php echo Escape($user->Result()->lastname); ?>">
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