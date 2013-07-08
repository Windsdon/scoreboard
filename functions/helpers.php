<?php
abstract class RandomHelper {
	const UPPERCASE = 1;
	const LOWERCASE = 2;
	const NUMBERS = 4;
	const SYMBOLS = 8;
	const MASK = 15;
	static $uppercase = "ABCDEFGHIJKLMNOPQRTSUVWXYZ";
	static $lowercase = "abcdefghijklmnopqrtsuvwxyz";
	static $numbers = "1234567890";
	static $symbols = "!@#$%&*()_-+=[]{}~;:<>.,|/\\'\"";
	/**
	 *
	 * @param number $length
	 *        	the size of the string
	 * @param number $alphabets        	
	 * @return string
	 */
	public static function getString($length, $alphabets = 7) {
		$str = "";
		$alphabets &= self::MASK;
		if ($alphabets <= 0 || $length <= 0) {
			// empty string, as there is nothing to generate
			return $str;
		}
		$s = "";
		// echo decbin ( $alphabets ) . "\n";
		if(!!($alphabets & self::UPPERCASE)){
			$s .= self::$uppercase;
		}
		if(!!($alphabets & self::LOWERCASE)){
			$s .= self::$lowercase;
		}
		if(!!($alphabets & self::NUMBERS)){
			$s .= self::$numbers;
		}
		if(!!($alphabets & self::SYMBOLS)){
			$s .= self::$symbols;
		}
		for($i = 0; strlen ( $str ) != $length; $i ++) {
			$str .= substr($s, rand(0, strlen($s) - 1), 1);
		}
		
		return $str;
	}
	/**
	 * Returns a flag from the set
	 *
	 * @param int|long $whole
	 *        	The set of flags
	 * @return number A random flag contained in the set
	 */
	public static function getFlag($whole) {
		// this function works, but possibly is very inefficient
		if ($whole == 0) {
			return 0;
		}
		
		// echo "whole is $whole\n";
		
		$flag = 0;
		$maxBit = 0;
		
		for(; ($whole >> $maxBit) != 0; $maxBit ++) {
		}
		
		// echo "max bit is $maxBit\n";
		
		do {
			$flag = 1 << rand ( 0, $maxBit );
			$r = $flag & $whole;
			// echo "testing $flag: {$r}\n";
		} while ( ($flag & $whole) == 0 );
		
		return $flag;
	}
}

?>