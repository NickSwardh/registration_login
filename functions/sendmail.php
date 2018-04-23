<?php

// Function for sending mail in HTML-format.
function SendMail($to, $name, $subject, $message) {

	// Set HTML and charset headers.
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';

	// Additional headers.
	$headers[] = "To: {$name} <{$to}>";
	$headers[] = 'From: nswardh.com <' . Config::Get('email/noreply') . '>';

	// Attempt to send mail. Return true if successful, else false.
	return mail($to, $subject, $message, implode("\r\n", $headers));

}