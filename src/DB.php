<?php

namespace Vatrikovsky\Core;

class DB extends Singleton {
	
	// Connection
	protected $connection = NULL;
	
	// Config
	protected static $host;
	protected static $user;
	protected static $pass;
	protected static $name;
	
	
	
	
	/* Singleton Constructor */
	protected function __construct() {
		if ( !$this->connection = mysqli_connect( self::$host, self::$user, self::$pass, self::$name ) ) {
			throw new Exception('Cannot connect to Database');
		}
		mysqli_query( $this->connection, 'SET NAMES utf8' );
	}
	
	
	
	
	/* Config */
	protected static function config( $host, $user, $pass, $name ) {
		self:$host = $host;
		self:$user = $user;
		self:$pass = $pass;
		self:$name = $name;
	}


	
	/* Query */
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
				$arg = '\'' . mysqli_real_escape_string( $this->connection, $arg ) . '\'';
			}
		}		
		array_unshift( $args, $q_template );
		$r = mysqli_query( $this->connection, $q = call_user_func_array( 'sprintf', $args ) );

		return new DatabaseResult( $r, $q );		
	}
	
	
	
	
	/* Last Insert Id */
	public function insertId() {
		return mysqli_insert_id( $this->connection );
	}
	
	
	
	
	/* Escape */
	public function escape( $var ) {
		return mysqli_real_escape_string( $this->connection, $var );
	}
	
	
	
	
	/* Get Property */
	public function __get( $name ) {
		if( property_exists( get_called_class(), $name ) ) {
			return self::$name;
		}
		else return NULL;
	}
	
	
	
	
	/* To String */
	public function __toString() {
		return 'DB Object';
	}
}