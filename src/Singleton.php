<?php


namespace Vatrikovsky\Core;

class Singleton {
	
	// Instance
	protected static $instance;
	
	
	
	
	/* Instance */
	public static function instance() {
		if ( !self::$instance or !( self::$instance instanceof $class ) ) {
			$class_name = get_called_class();
			self::$instance = new $class_name();
		}
		return self::$instance;
	}
	
	
	
	
	/* Protection */
	protected function __construct() {}
	protected function __clone() 	 {}
	protected function __copy() 	 {}
	
	
	
	/* To String */
	public function __toString() {
		return 'Singleton Object';
	}
}


?>