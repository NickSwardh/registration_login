<?php

require_once("init/init.php");


if (!$user->LoggedIn()) {

	header("Location: login.php");
	exit;

}

?>
<!doctype html>

<html>
<head>
	<meta charset="UTF-8">
	<title>Welcome</title>
</head>
<body>

	<h1>Welcome, <?php echo $user->Result()->firstname; ?>!</h1>

	<ul>
		<li><a href="emailupdate.php">Update e-mail</a></li>
		<li><a href="profile.php">Update profile</a></li>
		<li><a href="editpass.php">Change password</a></li>
		<li><a href="logout.php">Sign out</a></li>
	</ul>

</body>
</html>