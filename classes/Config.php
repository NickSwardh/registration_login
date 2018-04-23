<?php

	/*
	 *	By Nick Swardh - nswardh.com
	 *
	 *	Free to use as long as this comment stay intact.
	*/
	
class Config {



	// Initialize a static variable storing the credentials in `config.ini`.
	private static $credentials = null;



	// Get and return the requested credentials.
	////////////////////////////////////////////////////////////////////////
	public static function Get($config) {

		// Get the cridentials from the static helper-method LoadIni().
		$ini = self::LoadIni();

		// Explode the incoming `$config` string into an array, index 0=criteria, 1=value
		$set = explode("/", $config);

		// Return the requested value.
		return $ini[$set[0]][$set[1]];

	}




	// Helper-method to load and parse `config.ini`.
	// This method makes sure the ini file only loads once.
	////////////////////////////////////////////////////////////////////////
	private static function LoadIni() {

		// If `self::$ini` is not set...
		if (!isset(self::$credentials)) {

			// Parse `config.ini` and store the array in `self::$ini`.
			self::$credentials = parse_ini_file("init/config.ini", true);

		}

			// return the parsed `config.ini` file.
			return self::$credentials;

	}
	


}