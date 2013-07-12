<?php
include_once "db_functions.php";
include_once "permission.class.php";

class User {
	public $perm = NULL;
	public $id = "";
	public $username = "";
	public $class = -1;
	public $options = NULL;
	
	/**
	 * Includes check to see if user exists
	 * @param string $userID
	 *        	The 16-char unique user ID
	 * @return User The User object or NULL if user doesn't exist
	 */
	public static function getFromID($userID) {
		$qr = Database::$default->query ( "SELECT * FROM users WHERE users.user_id = '$userID' LIMIT 0,1" );
		if (!is_null($qr) && $qr->numRows() == 1) {
			return new User ( $userID );
		}else{
			return null;
		}
	}
	public static function getFromUsername($username) {
		$qr = Database::$default->query("SELECT * FROM users WHERE users.username = '$username' LIMIT 0,1");
		//var_dump($qr);
		if(!is_null($qr) && $qr->numRows() == 1){
			$userobj = $qr->getObject();
			//var_dump($userobj);
			return new User($userobj->user_id);
		}else{
			return null;
		}
	}
	
	private function __construct($userID) {
		$qr = Database::$default->query ( "SELECT users.username, users.user_id, classes.class_attributes, classes.class_id FROM users, classes WHERE users.user_id = '$userID' AND classes.class_id = users.user_class LIMIT 0,1" );
		if(!is_null($qr) && $qr->numRows() == 1){
			$userobj = $qr->getObject();
			$this->id = $userobj->user_id;
			$this->username = $userobj->username;
			$this->class = $userobj->class_id;
		}
		$this->perm = new Permission($userID);
	}
	
	public function getID(){
		return $this->id;
	}
	
	public function  getUsername(){
		return $this->username;
	}
}
?>