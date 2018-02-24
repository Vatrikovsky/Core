<?php


namespace Vatrikovsky\Core;

class DBResult {
	
	// Result
	protected $r;
	
	// Query
	protected $q;
	
	
	
	
	/* Construct */
	public function __construct( $r, $q ) {
		$this->r = $r;
		$this->q = $q;
	}
	
	
	
	
	/* Get Assoc */
	public function fetchAssoc() {
		return mysqli_fetch_assoc( $this->r );
	}
	
	
	
	
	/* Get Row */
	public function fetchRow() {
		return mysqli_fetch_row( $this->r );
	}
	
	
	
	
	/* Get Array */
	public function getArray() {
		$array = [];
		while ( $assoc = mysqli_fetch_assoc( $this->r ) ) {
			$array[] = $assoc;
		}
		return $array;
	}
	
	
	
	
	/* Get List */
	public function getList() {
		$list = [];
		while ( $row = mysqli_fetch_row( $this->r ) ) {
			$list[] = $row;
		}
		return $list;
	}
	
	
	
	
	/* Get Value */
	public function getValue() {
		return mysqli_fetch_row( $this->r )[0];
	}
	
	
	
	
	/* Num Rows */
	public function numRows() {
		return mysqli_num_rows( $this->r );
	}	
	
	
	
	
	/* Get */
	public function __get( $varname ) {
		if ( isset( $this->$varname ) ) {
			return $this->$varname;
		}
		else return NULL;
	}
	
	
	
	
	/* To String */
	public function __toString() {
		return 'DB Object';
	}	
}