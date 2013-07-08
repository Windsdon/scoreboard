<?php
include_once 'generator.php';
include_once 'system.php';
include_once 'formGenerator.php';

/**
 *
 * @author Windsdon
 *         Generates the index page
 */
class IndexPage implements PageGenerator {
	private $system;
	public function __construct(ScoreboardSystem $scoreboardSystem) {
		$this->system = $scoreboardSystem;
	}
	
	/**
	 * Index page has no arguments
	 *
	 * @return boolean True if has to echo
	 */
	public function generate(array $args) {
		$v = ( object ) array (
				"permissions" => ( object ) array (
						"post" => true,
						"ban" => true 
				),
				"spec" => ( object ) array (
						"pefix" => "[ADM]",
						"prefix_color" => 0xff0000,
						"name_color" => 0xcccccc,
						"class_name" => "Admin" 
				) 
		);
		$this->system->responseAppendLine ( "<pre>" . json_encode ( $v ) . "</pre>" );
		
		$path = realpath ( dirname ( __FILE__ ) );
		$files = scandir ( $path );
		$lines = 0;
		if ($files) {
			foreach ( $files as $file ) {
				if ($file == "." || $file == "..") {
					continue;
				}
				$handle = fopen ( $path . "\\" . $file, "r" );
				while ( ! feof ( $handle ) && $handle ) {
					$line = fgets ( $handle );
					$lines ++;
				}
				
				fclose ( $handle );
			}
		}
		$this->system->responseAppendLine ( "This project contains $lines lines of code." );
		
		return true;
	}
}
?>