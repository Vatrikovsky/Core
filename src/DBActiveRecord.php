<?php


namespace Vatrikovsky\Core;

abstract class DBActiveRecord {
	
	// Main Table
	const TABLE = 'database_table_name';
	
	// Fields
	protected $fields = [];
	
	// Loaded Flag
	protected $loaded = FALSE;
	
	
	
	
	/* Construct and Load by ID */
	public function __construct( $id = NULL ) {
		
		// Define Fields
		$r = DB::instance()->q('
			SELECT 
				`COLUMN_NAME` 
			FROM 
				`INFORMATION_SCHEMA`.`COLUMNS` 
			WHERE 
				`TABLE_SCHEMA` = ? AND 
				`TABLE_NAME` = ? 
			ORDER BY 
				`ORDINAL_POSITION`',
			DB::$name,
			static::TABLE
		);
		
		// Success
		if ( $r->numRows() ) {
			while ( $column_name = $r->getValue() ) {
				$this->fields[ $column_name ] = NULL;
			}
		}
		
		// Fail
		else {
			throw new Exception( 'Can\'t read field names of `' . static::TABLE . '` from Database.' );
			return FALSE;
		}
		
		// Load if ID
		if ( ! is_null( $id ) ) {
			$this->load( $id );
		}
	}
	
	
	
	
	/* Load */
	public function load( $id ) {
		$this->fields['id'] = $id;

		// Query
		$r = DB::instance()->q( 'SELECT * FROM `' . static::TABLE . '` WHERE id = ? LIMIT 1', $this->fields['id'] );
		
		// Success
		if ( $r->numRows() ) {
			$fields = $r->fetchAssoc();
			foreach ( $fields as $name => $value ) {
				if ( array_key_exists( $name, $this->fields ) ) {
					$this->fields[ $name ] = $value;
				}
			}			
			$this->loaded = TRUE;
			return TRUE;
		}
		
		// Fail
		else {
			#throw new Exception( 'Can\'t load object ' . get_called_class() . ' with id ' . $this->fields['id'] );
			return FALSE;		
		}
	}
	
	
	
	
	/* Object By Field */
	public static function getByField( $field, $value ) {
		
		// Query
		$field = DB::instance()->escape( $field );
		$r = DB::instance()->q( 'SELECT id FROM `' . static::TABLE . '` WHERE `' . $field . '` = ? LIMIT 1', $value );
		
		// Success
		if ( $r->numRows() ) {
			$class_name = get_called_class();
			return new $class_name( $r->get_value() );
		}
		
		// Fail
		else {
			#throw new Exception( 'Can\'t load object ' . get_called_class() . ' by field ' . $field . ' = ' . $value );
			return FALSE;		
		}
	}
	
	
	
	
	/* Save */
	public function save() {
		
		// Method
		$method = 'insert';				
		if ( isset( $this->fields['id'] ) ) {
			$r = DB::instance()->q('SELECT * FROM `' . static::TABLE . '` WHERE id = ? LIMIT 1', $this->fields['id'] );
			if ( $r->numRows() ) {
				$method = 'update';
			}
		}
		
		// Query
		switch ( $method ) {
			
			// Insert
			case 'insert':
				$args = [];
				$placeholders = [];
				$columns = [];
				
				foreach ( $this->fields as $name => $value ) {
					if ( $name === 'id' ) continue;
					if ( $name === 'created_at' ) continue;
					$args[] = $value;
					$placeholders[] = '?';
					$columns[] = '`' . $name . '`';
				}
				$query = 'INSERT INTO `' . static::TABLE . '` ( ' . join( $columns, ', ' ) . ' ) VALUES ( ' . join( $placeholders, ', ' ) . ' )';
				break;
				
			// Update
			case 'update':
				$args = array();
				$placeholders = array();
				$columns = array();
				
				foreach ( $this->fields as $name => $value ) {
					if ( $name === 'id' ) continue;
					if ( $name === 'created_at' ) continue;
					$args[] = $value;
					$sets[] = '`' . $name . '` = ?';
				}
				$query = 'UPDATE `' . static::TABLE . '` SET ' . join( $sets, ', ' ) . ' WHERE id = ? LIMIT 1';
				$args[] = $this->fields['id'];
				break;
		}
		
		// Execute Query
		$r = DB::instance()->q( $query, $args );
		return TRUE;
	}
	
	
	
	
	/* Get Property */
	public function __get( $name ) {
		if ( array_key_exists( $name, $this->fields ) )
			return $this->fields[ $name ];
		else if ( isset( $this->$name ) )
			return $this->$name;
		else return NULL;
	}
	
	
	
	
	/* Set Property */
	public function __set( $name, $value ) {
		if ( array_key_exists( $name, $this->fields ) ) {
			$this->fields[ $name ] = $value;
			return TRUE;
		}
		else return FALSE;
	}
	
	
	
	
	/* To String */
	public function __toString() {
		ob_start();
		echo '<pre>';
		var_dump( $this );
		echo '</pre>';
		$var_dump = ob_get_contents();
		ob_end_clean();
		return $var_dump;
	}
}

?>