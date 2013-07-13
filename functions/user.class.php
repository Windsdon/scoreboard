<?php
include_once "db_functions.php";
include_once "permission.class.php";
include_once "helpers.php";
class User {
	const LOGIN_HASH = "6hfWy7eXCshza5PcV6tenvgmm2wEKNEjmTfUxDoEE8mBQAA4sn44Sy3uVCDrqPBaq63KKCModEFFc8ILdMbKWulvbEtLkJVJHbm445HGQC6BDxoWlnXUwito50sj15Cv";
	public $perm = NULL;
	public $id = "";
	public $username = "";
	public $class = - 1;
	/**
	 * If it's false, the user hasn't logged in yet.
	 */
	public $loginSession = false;
	public $options = NULL;
	
	/**
	 * Includes check to see if user exists
	 *
	 * @param string $userID
	 *        	The 16-char unique user ID
	 * @return User The User object or NULL if user doesn't exist
	 */
	public static function getFromID($userID) {
		$qr = Database::$default->query ( "SELECT * FROM users WHERE users.user_id = '$userID' LIMIT 0,1" );
		if (! is_null ( $qr ) && $qr->numRows () == 1) {
			return new User ( $userID );
		} else {
			return null;
		}
	}
	/**
	 * Includes check to see if user exists.
	 *
	 * @param string $username
	 *        	The username
	 * @return User NULL object on success
	 */
	public static function getFromUsername($username) {
		$qr = Database::$default->query ( "SELECT * FROM users WHERE users.username = '$username' LIMIT 0,1" );
		// var_dump($qr);
		if (! is_null ( $qr ) && $qr->numRows () == 1) {
			$userobj = $qr->getObject ();
			// var_dump($userobj);
			return new User ( $userobj->user_id );
		} else {
			return null;
		}
	}
	/**
	 * This function will never fail, even it receives a bad userID.
	 * Be sure to check if the user exists before calling it!
	 *
	 * @param string $userID
	 *        	The ID of the user
	 * @param boolean $send
	 *        	If true, the user cookie is updated
	 * @param number $expire
	 *        	The duration of the login. The default is 10 years.
	 * @return string The new 256 char login session string
	 */
	public static function generateLoginSession($userID, $send = true, $expire = 315360000) {
		$newHash = RandomHelper::getString ( 256 ); // generate the new login session
		                                            // let's delete the old session, if it exists. No need to store the result.
		Database::$default->query ( "DELETE FROM user_login WHERE user_login.login_user_id = '$userID'" );
		// now, we add the new login
		$qr = Database::$default->query ( "INSERT INTO user_login(login_token,login_user_id) VALUES ('$newHash','$userID')" );
		if ($send) {
			// let's update the user cookie!
			setcookie ( "scoreboard_login", "$newHash", time () + $expire );
		}
		return $newHash;
	}
	/**
	 * Loads the user object from the session string.
	 *
	 * @param string $session
	 *        	The user login string. If null, it will try to load from the cookie
	 * @return boolean User user object, or false if there is no such session
	 */
	public static function getFromSession($session = null) {
		// I still don't know how to properly check if the variable is null...
		if (! $session) {
			if (isset ( $_COOKIE ['scoreboard_login'] )) {
				$session = $_COOKIE ['scoreboard_login'];
			} else {
				return false;
			}
		}
		$qr = Database::$default->query ( "SELECT user_login.login_user_id FROM user_login WHERE user_login.login_token = '$session'" );
		if (! ! $qr && $qr->numRows () == 1) {
			// yay, the user exists!
			return new User ( $qr->getObject ()->login_user_id );
		} else {
			// oops, no such session
			return false;
		}
	}
	
	/**
	 * Checks if there is a user with this username/password combination, and returns the object on success.
	 * Warning! This also updates the user login session string on success, and sends it to the user.
	 *
	 * @param string $username        	
	 * @param string $password        	
	 * @return User boolean user object if login is successful, of false if failed to login (as in bad user/pass combination)
	 */
	public static function getFromLogin($username, $password) {
		$passHash = md5 ( self::LOGIN_HASH . $password );
		$qr = Database::$default->query ( "SELECT * FROM users WHERE users.username = '$username' AND users.user_password = '$passHash'" );
		
		if (! is_null ( $qr ) && $qr->numRows () == 1) {
			$userobj = $qr->getObject ();
			// let's update the user session, as this is a login
			self::generateLoginSession ( $userobj->user_id, true );
			return new User ( $userobj->user_id );
		} else {
			return false;
		}
	}
	private function __construct($userID) {
		$q = "SELECT users.username, users.user_id, classes.class_attributes, classes.class_id 
				FROM users, classes WHERE users.user_id = '$userID' 
				AND classes.class_id = users.user_class LIMIT 0,1";
		// echo $q;
		$qr = Database::$default->query ( $q );
		if (! is_null ( $qr ) && $qr->numRows () == 1) {
			$userobj = $qr->getObject ();
			$this->id = $userobj->user_id;
			$this->username = $userobj->username;
			$this->class = $userobj->class_id;
			$sessionqr = Database::$default->query ( "SELECT user_login.login_token FROM user_login WHERE user_login.login_user_id = '$userID'" );
			if (! ! $sessionqr && $sessionqr->numRows () > 0) {
				$this->loginSession = $sessionqr->getObject ()->login_token;
			}
		} else {
			throw new Exception ( "No such user: $userID" );
		}
		$this->perm = new Permission ( $userID );
	}
	public function getID() {
		return $this->id;
	}
	public function getUsername() {
		return $this->username;
	}
}
?>