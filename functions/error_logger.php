<?php
include_once "db_functions.php";

class ErrorLogger{
	public static $Enabled = false;
	public static $Default = array();
	
	public static $events = array(
		"UNKOWN",
		"DB_FAIL"
	);
	
	private $output;
	
	private $db;
	
	function __construct($output = "database"){
		if($output == "database"){
			try{
				$db = new Database();
				$db->connect();
			}catch(ScoreboardException $e){
				die("Fatal error on error logger");
			}
		}
		
		self::$output = $output;
	}
	
	public function event($event_id, $event_message = "", $information = ""){
		echo "[ErrorLogger] $event_id - $event_message\n $information";
	}
}

?>