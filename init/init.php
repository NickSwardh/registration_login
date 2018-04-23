<?php



// Start session.
session_start();



// Set the charset to UTF-8.
header('Content-Type: text/html; charset=utf-8');



// Autoloader for loading classes.
spl_autoload_register(function($class) {
	require_once("classes/" . $class .  ".php");
});



// Load functions
require_once('functions/sanitize.php');
require_once('functions/sendmail.php');



// Check if the user has a `remember` cookie but not a session-cookie...
if (Cookie::Exist(Config::Get('autologin/cookie')) && !isset($_SESSION[Config::Get('session/cookie')])) {

	// Get the hash-string from the cookie.
	list($hash_cookie, $cookie_validator) = explode(':', $_COOKIE[Config::Get('autologin/cookie')]);

	// Compare the hash with the database.
	$db_remember = new Member();	// Create a new object instance of the user-defined datatype Member.
	$db_remember->Select('autologin', 'hash', $hash_cookie);

	// Does the hash exist?
	if ($db_remember->Count() > 0) {

		// Compare the validator from the database with the validator in the cookie.

		// Use the hash_equals() function which is time-attack safe string comparison method.
		if (hash_equals($db_remember->Result()->validator, hash('sha256', $cookie_validator))) {

			// User is logged in. Create a new user.
			$user = new User($db_remember->Result()->user_id);

		}

	}

	// Do some cleaning.
	unset($db_remember);
	unset($hash_cookie);
	unset($cookie_validator);

} else {

	// Create a new user object. If the user has a valid session-cookie, the user will be logged in.
	$user = new User();
	
}