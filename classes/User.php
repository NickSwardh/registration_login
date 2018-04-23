<?php

	/*
	 *	By Nick Swardh - nswardh.com
	 *
	 *	Free to use as long as this comment stay intact.
	*/
	
class User extends Member {



	// Class members
	private $loggedIn;	// Flag for setting/checking logged-in status.
	private $id;		// User id.



	// Constructor.
	public function __construct($userId = null) {

		// Members-class's constructor.
		parent::__construct();

		// Check if there is a valid session or valid `remember me` cookie.
		if (!$userId) {
			$this->SessionLogIn();
			$this->id = $_SESSION[Config::Get('session/cookie')];
		} else {
			$this->RememberLogIn($userId);
			$this->id = $userId;
		}

	}



	// Method for checking if a valid session exist. Log in if true.
	private function SessionLogIn() {

		// Get the name for the session cookie.
		$sess = Config::Get('session/cookie');

		// A session exist...
		if (isset($_SESSION[$sess])) {

			// Request the userinformation and set flag to true.
			$this->Select('members', 'id', $_SESSION[$sess]);

			// User exist?
			if($this->Count())
				$this->loggedIn = true;		// Yes, Logged in.
			else
				$this->LogOut();			// No, Logg out.

		} else {
			$this->loggedIn 	= false;	// No session exist, keep logged out!
		}

	}



	private function RememberLogIn($user_id) {

		// No session exist, grab a user based on the passed in parameter $userId
		$this->Select('members', 'id', $user_id);

		if($this->Count()) {
			$this->loggedIn = true;
			$_SESSION[Config::Get('session/cookie')] = $user_id;
		} else {
			$this->LogOut();
		}

	}



	// Return loggin status.
	public function LoggedIn() {
		return $this->loggedIn;
	}



	// Return current user-id.
	public function ID() {
		return $this->id;
	}



	// Logg out! Crush all cookies!
	public function LogOut() {
		Cookie::Crush(Config::Get('session/cookie'));	// Crush session cookies.
		Cookie::Crush(Config::Get('autologin/cookie'));	// Crush remember cookie.
	}



}