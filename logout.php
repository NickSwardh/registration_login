<?php

require_once('init/init.php');


// Destroy session cookies.
$user->LogOut();


// Send member back to the login page.
header("Location: login.php");
exit;