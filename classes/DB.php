<?php

	/*
	 *	Singleton-class that restricts instantiation of the class to 1 single object.
	 *
	 *	By Nick Swardh - nswardh.com | 
	 *
	 *	Free to use as long as this comment stay intact.
	*/

class DB {
	


	// Class members
	private static $conn 	= null;
	private $pdo 		= null;



	// Constructor
	private function __construct() {

		try {

			$host = Config::Get('config/host');
			$name = Config::Get('config/db');
			$user = Config::Get('config/user');
			$pass = Config::Get('config/pass');

			// Create PDO-database connection and store in $conn.
			$this->pdo = new PDO("mysql:host={$host}; dbname={$name}", $user, $pass);
			
		} catch (PDOException $e) {

			$this->Error($e->getMessage());

		}

	}



	// Instatiate object.
	// This function will check if an instance of the connection-object already exist.
	// If not, create it! This way, we make sure there´s only 1 database connection.
	public static function Connect() {

		// If no object exist, instatiate!
		if(!isset(self::$conn))
			self::$conn = new DB();

		return self::$conn;

	}



	// Return the singleton object.
	public function Get() {

		if(!isset($this->pdo))
			die("No connection established!");

		return $this->pdo;

	}



}
