<?php


namespace Vatrikovsky\Core;

class Singleton {
	
	// Instance
	protected static $instance;
	
	
	
	
	/* Instance */
	public static function instance() {
		if ( !self::$instance or !( self::$instance instanceof $class ) )
			self::$instance = new self();
		return self::$instance;
	}
	
	
	
	
	/* Protection */
	protected function __construct() {}
	protected function __clone() 	 {}
	protected function __copy() 	 {}
}


?>