<?php

	/*
	 *	By Nick Swardh - nswardh.com
	 *
	 *	Free to use as long as this comment stay intact.
	*/

class Member {



	// Class members.
	private $conn 		= null,
			$result 	= null,
			$rowCount 	= 0,
			$lastId 	= null;



	// Constructor.
	public function __construct($sql = null) {

		// Get the singleton database object.
		$this->conn = DB::Connect()->Get();

	}



	// Method for preparing the SQL-query.
	private function PrepareQuery($query, $param) {

		// Prepare the $query.
		$assemble = $this->conn->prepare($query);

		// If $param is an array
		if (is_array($param)) {

			// How many parameters?
			$count = count($param);

			// Iterate through $param.
			for($i = 1; $i <= $count; $i++) 
				$assemble->bindValue($i, $param[$i - 1]); 	// Bind each $parame to its ?-placeholder.

		// ...it's a string...
		} else {
			$assemble->bindValue(1, $param);				// Bind it.
		}

		// All good, return prepared and bound query-object.
		return $assemble;

	}



	// Method for executing the database query.
	private function Query($query, $param) {

		// Prepare $query.
		$result = $this->PrepareQuery($query, $param);

		// Execute the database query.
		if ($this->Execute($result)) {

			$this->result 		= $result->fetchAll(PDO::FETCH_OBJ);	// Fetch the result as an object.
			$this->rowCount 	= $result->rowCount();					// Get number of rows.

		} else {
			$this->Error($result->errorInfo()[2]);						// Oops, something went wrong...
		}

		// Glory to the Gods! Raise your horns, Skål!
		return $this;

	} 



	// Method for selecting data from database.
	public function Select($table, $column, $value) {

		$query = "SELECT * FROM {$table} WHERE {$column}=?";
		return $this->Query($query, $value);

	}




	// Method for selecting data WHERE data AND something AND... etc from database.
	public function SelectAnd($table, $where = array()) {

		$and 	= array();
		$param 	= array();

		foreach($where as $key => $value) {
			
			// $value[0] = column
			// $value[1] = operator
			// $value[2] = value

			if ($value[2] === 'NOW()') {
				$and[] 		= $value[0] . $value[1] . 'NOW()';
			} else {
				$and[] 		= $value[0] . $value[1] . '?';
				$param[] 	= $value[2];
			}

		}

		// Construct query.
		$query = "SELECT * FROM {$table} WHERE " . implode(' AND ', $and);

		return $this->Query($query, $param);

	}



	// Method for deleting a user with user-id.
	public function Delete($table, $where, $operator, $value) {
		
		$query = "DELETE FROM {$table} WHERE {$where}{$operator}?";
		return $this->Query($query, $value);

	}



	// Method for inserting data into the database.
	public function Insert($table, $param) {

		$column	= '';
		$holder	= '';
		$data 	= array();

		foreach ($param as $key => $value) {
			$column	.= $key . ', ';
			$holder	.= '?, ';
			$data[]  = $value;
		}

		// Construct the query.
		$query = "INSERT INTO {$table} (" . trim($column, ', ') . ') VALUES (' . trim($holder, ', ') . ')';

		// Call PrepareQuery() and pass in the $query along with $data for binding.
		$result = $this->PrepareQuery($query, $data);

		$this->Execute($result);						// Execute query!
		$this->lastId = $this->conn->lastInsertId();	// Get the last inserted ID from the connection-object.

		return true;

	}



	// Method for updating data into the database.
	public function Update($table, $param, $where = 'id', $id) {

		$column = '';
		$data 	= array();

		foreach ($param as $key => $value) {
			$data[]  = $value;
			$column	.= $key . '=?, ';
		}

		$data[] = $id;												// Add $id as the last index.
		$column = trim($column, ', ');								// Remove the ', ' from the end.
		$query 	= "UPDATE {$table} SET {$column} WHERE {$where}=?";	// Construct the query.

		$result = $this->PrepareQuery($query, $data);				// Call PrepareQuery().
		$this->Execute($result); 									// Execute query!

		return true;
	}



	// Private method for executing the PDO query.
	private function Execute($query) {

		if (!$query->execute()) {
			$this->Error($query->errorInfo()[2]);
			return false;
		}

		// Holy crap Batman! Nice!
		return true;

	}



	// Return number of effected rows.
	public function Count() {
		return $this->rowCount;
	}



	// Return all results from the database query.
	public function AllResults() {
		return $this->result;
	}



	// Return the first result from the database query.
	public function Result() {
		return $this->AllResults()[0];
	}



	// Return the last ID.
	public function LastId() {
		return $this->lastId;
	}



	// Kill everything if there's an error!
	private function Error($err_message) {
		die("Failed to connect to database:\r\n\r\n" . $err_message);
	}



}