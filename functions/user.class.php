<?php
include_once "db_functions.php";

class User {
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
		if(!is_null($qr) && $qr->numRows() == 1){
			$userobj = $qr->getObject();
			var_dump($userobj);
			return new User($userobj.user_id);
		}else{
			return null;
		}
	}
	public function __construct($userID) {
	}
}
?>