<?php
include_once 'generator.php';
include_once 'system.php';
include_once 'formGenerator.php';

/**
 * @author Windsdon
 * Generates 404 page
 */
class NotFoundGenerator implements PageGenerator{
	private $system;
	private $file404 = "../models/404.php";

	public function __construct(ScoreboardSystem $scoreboardSystem){
		$this->system = $scoreboardSystem;
	}


	/**
	 * 404 has no arguments
	 * @return boolean true
	 */
	public function generate(array $args){
		if(file_exists($this->file404)){
			$this->system->responseAppendLine(file_get_contents($this->file404));
		}else{
			$this->system->responseAppendLine("<h1>404 - Not Found</h1>");
		}
		return true;
	}
}


/**
 * @author Windsdon
 * Generates 403 page
 */
class ForbbidenGenerator implements PageGenerator{
	private $system;
	private $file403 = "../models/403.php";

	public function __construct(ScoreboardSystem $scoreboardSystem){
		$this->system = $scoreboardSystem;
	}


	/**
	 * 403 has no arguments
	 * @return boolean true
	 */
	public function generate(array $args){
		if(file_exists($this->file403)){
			$this->system->responseAppendLine(file_get_contents($this->file403));
		}else{
			$this->system->responseAppendLine("<h1>403 - Forbbiden</h1>");
		}
		return true;
	}
}
?>