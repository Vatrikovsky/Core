<?php


namespace Vatrikovsky\Core;

class Singleton {
	
	protected static $instance;
	
	public static function instance() {
		$class = get_called_class();
		if ( !self::$instance or !( self::$instance instanceof $class ) )
			self::$instance = new $class();
		return self::$instance;
	}
	
	protected function __construct() {}
	protected function __clone() 	 {}
	protected function __copy() 	 {}
}


?>