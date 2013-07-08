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
		return true;
	}
}
?>