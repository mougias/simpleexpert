<?php 

/**
 * Singleton connection to the database.
 * 
 * @author Stefanos Demetriou
 *
 */
class DB {
	
	const DB_HOST = '127.0.0.1';
	const DB_NAME = 'debugger';
	const DB_USER = 'debugger';
	const DB_PASS = 'C6ud3DYq$R83>Rn';
	
	
	private static $instance;
	
	/**
	 * returns the instance
	 */
	public static function getInstance() {
		if (empty(self::$instance))
			new DB();
		
		return self::$instance;
	}
	
	
	/**
	 * private construction, creates the connection to the database.
	 */
	private function __construct() {
		self::$instance = new mysqli(self::DB_HOST, self::DB_USER, self::DB_PASS, self::DB_NAME);
		if (mysqli_connect_errno())
			throw new Exception("Connect failed: ". mysqli_connect_error());
	}
	
	
	
	
}