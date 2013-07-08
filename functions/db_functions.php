<?php
include_once "error_logger.php";
class Database {
	private static $db_host = "localhost";
	private static $db_user = "score";
	private static $db_pass = "V6axPRnJnT8Ht34q";
	private static $db_name = "scoreboard";
	private $link = false;
	private $success = false;
	public static $default;
	public function __construct() {
		$this->default = $this;
	}
	public function connect() {
		$newlink = mysql_connect ( Database::$db_host, Database::$db_user, Database::$db_pass );
		
		if ($newlink) {
			$this->link = $newlink;
			if (mysql_select_db ( self::$db_name, $this->link )) {
				$this->success = true;
				return true;
			} else {
				if (ErrorLogger::$Enabled) {
					ErrorLogger::$Default->event ( ErrorLogger::$events [0], "Failed to select database: {self::$db_name}" );
				} else {
					die ( "Failed to select database: {self::$db_name}" );
				}
				return false;
			}
		} else {
			if (ErrorLogger::$Enabled) {
				ErrorLogger::$Default->event ( ErrorLogger::$events [0], "Failed to connect!" );
			} else {
				die ( "Could not connect to database." );
			}
			return false;
		}
	}
	public function disconnect() {
		if ($this->success) {
			mysql_close ( $this->link );
		} else {
			if (ErrorLogger::$Enabled) {
				ErrorLogger::$Default->event ( ErrorLogger::$events [0], "Failed to connect!" );
			} else {
				die ( "Could not closed connection tha was not open." );
			}
		}
	}
	
	/**
	 *
	 * @param string $q
	 *        	The query string
	 * @return QueryResult query object
	 */
	public function query($q) {
		if ($this->success) {
			return mysql_query ( $q, $this->link );
		} else {
			return null;
		}
	}
	public function queryResource($q) {
		if ($this->success) {
			return mysql_query ( $q, $this->link );
		} else {
			return false;
		}
	}
	public static function makeObject(resource $resource) {
		return mysql_fetch_object ( $resource );
	}
	public static function count(resource $resource) {
		return mysql_num_rows ( $resource );
	}
}
class QueryResult {
	public $success = false;
	public $res;
	private $db;
	
	/**
	 * Runs the query $q and store the results as this object
	 *
	 * @param string $q
	 *        	The query string
	 */
	public function __construct($q, Database $db) {
		$this->db = $db;
		$this->res = $db->queryResource ( $q );
		
		if ($link) {
			$this->success = true;
		}
	}
	public function numRows() {
		return mysql_num_rows ( $this->res );
	}
	public function getObject() {
		return Database::makeObject ( $this->res );
	}
}

?>