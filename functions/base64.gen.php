<?php
include_once "generator.php";
include_once 'system.php';
include_once 'formGenerator.php';

class Base64Page implements PageGenerator{
	private $system;

	public function __construct(ScoreboardSystem $scoreboardSystem){
		$this->system = $scoreboardSystem;
	}


	/**
	 * Base64Page page has no arguments
	 * @return boolean True if has to echo
	 */
	public function generate(array $args){
		$form = FormGenerator::createForm(array(new TextInput("ASCII", 32, "ascii"), new SubmitButton("Enviar", "submitascii")));
		$form2 = FormGenerator::createForm(array(new TextInput("BASE 64", 32, "base64"), new SubmitButton("Enviar", "submit64")));
		$result = isset($_POST["submitascii"]) ? base64_encode($_POST["ascii"]) : (isset($_POST["submit64"]) ? base64_decode($_POST["base64"]) : "");
		
		$this->system->responseAppendLine($this->system->makeHeader("Base 64 encoder and decoder"));
		$this->system->responseAppendLine("<body>$form $form2 <br/>$result</body>{$this->system->makeEndfile()}");
		return true;
	}
}


?>