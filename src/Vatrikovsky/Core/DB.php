<?php


namespace Vatrikovsky\Core;

class DB extends Singleton {
	
	// Connection Id
	protected $connection_id = NULL;
	
	
	protected function __construct() {
		$this->connection_id = mysqli_connect( DB_HOST, DB_USER, DB_PASS, DB_NAME ) or die( 'Database server not found' );
		mysqli_query( $this->connection_id, 'SET NAMES utf8' );
	}

	
	// Query
	public function q() {
		$args = func_get_args();
		$q_template = array_shift( $args );
		$q_template = str_replace( '%', '%%', $q_template );
		$q_template = str_replace( '?', '%s', $q_template );
		
		if ( count( $args ) == 1 and is_array( $args[0] ) )
			$args = $args[0];
		
		if ( !empty( $args ) ) {
			foreach ( $args as &$arg ) {
				if ( is_int( $arg ) ) continue;
				if ( is_numeric( $arg ) ) continue;
				$arg = '\'' . mysqli_real_escape_string( $this->connection_id, $arg ) . '\'';
			}
		}		
		array_unshift( $args, $q_template );
		$r = mysqli_query( $this->connection_id, $q = call_user_func_array( 'sprintf', $args ) );

		return new DatabaseResult( $r, $q );		
	}
	
	// Last Insert Id
	public function insertId() {
		return mysqli_insert_id( $this->connection_id );
	}
	
	// Escape
	public function escape( $var ) {
		return mysqli_real_escape_string( $this->connection_id, $var );
	}
}


?>