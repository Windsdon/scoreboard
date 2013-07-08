<?php

abstract class FormGenerator {
	public static function createForm(array $elements, $target = "?", $method = "POST"){
		$text = "<form method=\"$method\" action=\"$target\">\n";
		
		foreach ($elements as $element){
			$text .= $element->render()."\n";
		}
		
		$text .= "</form>";
		
		return $text;
	}
}

/**
 * @author Windsdon
 * Form item prototype
 */
abstract class FormItem {
	public $label;
	public $id;
	
	public function __construct($label, $id){
		$this->label = $label;
		$this->id = $id;
	}
	
	/**
	 * @return string The element's tags
	 */
	public abstract function render();
}

/**
 * @author Windsdon
 * Basic text field
 */
class TextInput extends FormItem {
	private $size;
	public function __construct($label, $size, $id){
		parent::__construct($label, $id);
		$this->size = $size;
		
	}
	/* (non-PHPdoc)
	 * @see FormItem::render()
	 */
	public function render() {
		return "<label>{$this->label}<input type=\"text\" size=\"{$this->size}\" id=\"{$this->id}\" name=\"{$this->id}\" /></label>";
	}
}

/**
 * @author Windsdon
 * Basic submit button
 */
class SubmitButton extends FormItem {
	public function __construct($label, $id){
		parent::__construct($label, $id);
	}
	
	public function render(){
		return "<input type=\"submit\" value=\"{$this->label}\" id=\"{$this->id}\" name=\"{$this->id}\" />";
	}
}
?>