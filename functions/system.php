<?php
include_once 'index.gen.php';
include_once 'base64.gen.php';
include_once 'errorpage.gen.php';
include_once 'user.class.php';
include_once 'user.gen.php';
include_once 'helpers.php';
include_once 'permission.class.php';
include_once 'db_functions.php';

/**
 * This file contains the brain of the site.
 * This class is responsable for all parsing and response making.
 * Be careful!
 */
class ScoreboardSystem {
	private $config;
	const PAGE_REDIRECT = - 1;
	const PAGE_404 = 0;
	const PAGE_INDEX = 1;
	const PAGE_403 = 2;
	const PAGE_LOGIN = 3;
	const PAGE_BASE64 = 4;
	const PAGE_RANDSTR = 5;
	const PAGE_USER = 6;
	private $argAccepted = array (
			// format is ["identifier", min args, max args, accept redirect]
			- 1 => array (
					"redirect",
					0,
					0,
					true 
			),
			array (
					"404",
					0,
					- 1,
					false 
			),
			array (
					"index",
					0,
					0,
					true 
			),
			array (
					"403",
					0,
					- 1,
					false 
			),
			array (
					"login",
					0,
					0,
					true 
			),
			array (
					"base64",
					0,
					0,
					true 
			),
			array (
					"randstr",
					0,
					2,
					true 
			),
			array (
					"user",
					0,
					2,
					true 
			) 
	);
	
	/**
	 * Represents the full response text that will be sent to the client
	 */
	private $response = "";
	
	/**
	 * The page generator
	 */
	private $generator = NULL;
	
	/**
	 *
	 * @var User The user object of the current user, or false if not logged in.
	 */
	public $user = false;
	
	/**
	 * Create the system and prepare response
	 */
	function __construct() {
		$config = $this->loadConfiguration ();
		$this->start ();
		srand ( time () );
	}
	private function start() {
		$argSeq = null;
		$functionArgs = array ();
		$redirect = false;
		$redirectPage = "";
		$page = false;
		$database = new Database ();
		$database->connect ();
		
		
		if (isset ( $_GET ['arg'] )) {
			// remove all dangerous stuff and separate the args
			$argSeq = $this->argExplode ( $this->safetify ( $_GET ['arg'] ) );
			// var_dump($argSeq);
			
			if ($argSeq) {
				/*
				 * the first arg is the most important, and decides wich page will be loaded the function checks to see if the input is valid, and for optional redirects
				 */
				
				$addingToArguments = true;
				for($i = 0; $i < count ( $argSeq ); $i ++) {
					if ($i == 0) { // the first argument
						$argSeq [$i] = strtolower ( $argSeq [$i] );
						if ($argSeq [$i] != $this->argAccepted [self::PAGE_REDIRECT] [0]) {
							foreach ( $this->argAccepted as $k => $j ) {
								if ($j [0] == $argSeq [$i]) {
									$page = $k;
								}
							}
							
							if (! $page) { // not found
								$page = self::PAGE_404;
								break;
							}
							continue;
						} else {
							// this is a special case. I should beautify this code later.
							$page = self::PAGE_REDIRECT;
							$addingToArguments = false;
						}
					}
					
					if ($addingToArguments) {
						if ((count ( $functionArgs ) < $this->argAccepted [$page] [2] || $this->argAccepted [$page] [2] < 0) && ($argSeq [$i] != $this->argAccepted [self::PAGE_REDIRECT] [0])) {
							$functionArgs [] = $argSeq [$i];
						} else {
							$addingToArguments = false;
							$i --; // inefficient, but works
						}
					} else {
						if ($this->argAccepted [$page] [3] && ($argSeq [$i] == $this->argAccepted [self::PAGE_REDIRECT] [0])) {
							// redirect must be the last part.
							if (isset ( $argSeq [$i + 1] )) {
								$redirect = true;
								$redirectPage = base64_decode ( $argSeq [$i + 1] ); // the redirection string is in base 64
							}
						}
						break;
					}
				}
			} else {
				// we are at index
				$page = self::PAGE_INDEX;
			}
		} else {
			$page = self::PAGE_INDEX;
		}
		
		// now, let's try to login the user
		$this->user = User::getFromSession();
		
		switch ($page) {
			case self::PAGE_INDEX :
				$this->generator = new IndexPage ( $this );
				break;
			case self::PAGE_BASE64 :
				$this->generator = new Base64Page ( $this );
				break;
			case self::PAGE_404 :
				$this->generator = new NotFoundGenerator ( $this );
				break;
			case self::PAGE_403 :
				$this->generator = new ForbbidenGenerator ( $this );
				break;
			case self::PAGE_RANDSTR :
				// just a useful thing to have
				echo "<pre>";
				if (count ( $functionArgs ) > 0) {
					echo RandomHelper::getString ( ( int ) $functionArgs [0], ( int ) $functionArgs [1] );
				} else {
					for($i = 0; $i <= 256; $i += 16) {
						echo "$i: " . RandomHelper::getString ( $i, RandomHelper::LOWERCASE | RandomHelper::UPPERCASE ) . "\n";
					}
				}
				echo "</pre>";
				die ();
			case self::PAGE_REDIRECT :
				break;
			case self::PAGE_USER :
				$this->generator = new UserPage ( $this );
				break;
			default :
				var_dump ( $_GET );
				var_dump ( $argSeq );
				var_dump ( $page );
				die ();
		}
		
		if ($redirect) {
			// we don't echo anything
			$newplace = "/$redirectPage";
			header ( "Location: $newplace" );
			die ( "Redirecting" );
		}
		
		if (isset ( $this->generator )) {
			if ($this->generator->generate ( $functionArgs )) {
				echo $this->response;
				$cq = Database::$default->queryCount;
				echo "Completed with $cq MySQL queries";
			}
		} else {
			echo "No generator";
			exit ();
		}
	}
	
	/**
	 * Gets the current system config
	 */
	public function loadConfiguration() {
		require_once 'config.inc.php';
		$this->config = ( object ) $config;
	}
	private function safetify($str) {
		$str = str_replace ( '\\', '', $str );
		$str = str_replace ( "'", '', $str );
		$str = str_replace ( '"', '', $str );
		
		return $str;
	}
	private function argExplode($str) {
		// explode wth '/' as delimiter
		return explode ( '/', $str );
	}
	public function responseAppendLine($line) {
		$this->response .= $line . "\n";
	}
	public function makeEndfile() {
		return "</html>";
	}
	public function makeHeader($title = "Scoreboard 0.0.1") {
		// here we make the basic header
		$headerOutput = "";
		$headerOutput .= "<!doctype html>\n<html>\n<head>";
		$headerOutput .= '<meta charset="iso-8859-1">';
		$headerOutput .= "<title>$title</title>" . "\n";
		$headerOutput .= '<link href="s/ytle/default.css" rel="stylesheet" type="text/css">' . "\n";
		$headerOutput .= '<script type="text/javascript" src="/scripts/jQuery-min-*.js"></script>' . "\n";
		$headerOutput .= '<script type="text/javascript" src="/scripts/commons.js"></script>' . "\n";
		$headerOutput .= "<head>\n";
		
		return $headerOutput;
	}
}
?>