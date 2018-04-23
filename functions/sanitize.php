<?php



// Escape string.
function Escape($input) {

	return htmlspecialchars($input, ENT_QUOTES, "UTF-8");

}



// Decode htmlspecialchars.
function Decode($input) {

	return htmlspecialchars_decode($input);

}



// Sanitize $_POST variables.
function Sanitize($input) {

	return filter_var($_POST[$input], FILTER_SANITIZE_STRING);

}