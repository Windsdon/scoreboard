<?php
include_once 'generator.php';
include_once 'system.php';
include_once 'user.class.php';

/**
 *
 * @author Windsdon
 *         Generates the user page
 */
class UserPage implements PageGenerator {
	private $system;
	public function __construct(ScoreboardSystem $scoreboardSystem) {
		$this->system = $scoreboardSystem;
	}
	
	/**
	 * User page takes 2 arguments (for now): listing type ("username" or "id") and value
	 *
	 * @return boolean True if has to echo
	 */
	public function generate(array $args) {
		if(count($args) > 1 && $args[0] == "id"){
			//echo "Trying to list by ID";
			//var_dump($args);
			
			$user = User::getFromID($args[1]);
			if(!$user){
				$this->system->responseAppendLine("User not found!");
			}else{
				$this->system->responseAppendLine("Listing user by ID: {$user->getID()}");
			}
		}else{
			//echo "Trying to list by USERNAME";
			//var_dump($args);
			
			$user = User::getFromUsername($args[0]);
			if(!$user){
				$this->system->responseAppendLine("User not found!");
			}else{
				$this->system->responseAppendLine("Listing user by Username: {$user->getUsername()}");
			}
		}
		return true;
	}
}
?>