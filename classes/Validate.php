<?php

	/*
	 *	By Nick Swardh - nswardh.com
	 *
	 *	Free to use as long as this comment stay intact.
	*/
	
class Validate extends Member {


	// Class member holding form-errors.
	private $error = array();



	// Constructor.
	public function __construct() {

		// Members-class's constructor.
		parent::__construct();

	}



	// Check the registrationform to make sure all data has been filled out correctly.
	public function CheckForm() {

		$form = $_POST;

		// For each form-value...
		foreach ($form as $key => $input) {

			switch($key) {

				// Validate email.
				case 'email' :
					//$this->Email($form['email']);
					$email = trim($form['email']);

					if ($this->Email($email)) {
						// Email passed validation, Does it exist in the database?
						if ($this->Exist("members", "email", $email))
							$this->error['email'] = "e-mail already exist!";
					}

					break;

				// Validate email.
				case 'pass1' :
					$this->VerifyPass($form[$key], $form['pass2']);
					break;

				case 'oldpass' :
					$this->ValidatePass($form[$key], $key);
					break;

				case 'firstname' :
					$this->FirstName(trim($form[$key]));
					break;

				case 'lastname' :
					$this->LastName(trim($form[$key]));
					break;
			}

		}

		return (empty($this->error)) ? false : $this->error;
	}



	// Method for validating email.
	public function Email($email) {

		// Valid email.
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

			$this->error['email'] = "Invalid e-mail address";
			return false;
		}

		return true;

	}



	// Verify password.
	public function VerifyPass($pass1, $pass2) {

		// Does the passwords match?
		if ($pass1 != $pass2) {

			$this->error['pass1'] = "Password doesn't match!";
			return false;

		// Call ValidatePass().
		} else if (!$this->ValidatePass($pass1)) {

			return false;

		}

		return true; // Password verified and validated!
	}



	// Validate password.
	public function ValidatePass($pass, $input = 'pass1') {

		// How many chars?
		if (strlen(utf8_decode($pass)) < 8) {
			$this->error[$input] = "8 chars minimum.";
			return false;
		}

		return true;

	}



	// Method for validating first name.
	public function FirstName($firstName) {

		// Valid letters are letters from any language. Min length is 2 chars, for names like 'Al', 'Ed', 'Ty' etc
		// Also make sure the maxlength doesn't exceed 20 chars.
		if (strlen($firstName) > 20 || !preg_match('/^[\p{L}]{2,}(( |-)?[\p{L}]{2,})?$/u', $firstName)) {

			$this->error['name'] = "Invalid name!";
			return false;
		}

		return true;

	}



	// Method for validating last name.
	public function LastName($lastName) {

		// Valid letters are letters from any language. Min length is 2 chars.
		if (strlen($lastName) > 20 || !preg_match('/^[\p{L}]{2,}(( |-)?[\p{L}]{2,}(( |-)?[\p{L}]{2,})?)?$/u', $lastName)) {

			$this->error['name'] = "Invalid name!";
			return false;
		}

		return true;

	}



	// Return all form errors.
	public function Error() {

		return $this->error;

	}



	// Check if an entry in the database already exist.
	private function Exist($table, $column, $param) {

		// If-case for checking if the user is logged in...
		if (isset($_SESSION[Config::Get('session/cookie')])) {

			// If the user is trying to change information to something that already exist, do nothing and return false.
			// For example: If the user is trying to change e-mail to the same e-mail that is already registered, return false and continue.
			if (strtolower($this->Select($table, $column, $_SESSION[Config::Get('session/cookie')])->Result()->email) == strtolower($param)) {
				echo "thtdh";
			
			return false;
			}

		}

		return ($this->Select($table, $column, $param)->Count()) ? true : false;

	}

}