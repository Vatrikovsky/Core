<?php


namespace Vatrikovsky\Core;

abstract class DBObject {
	
	// Таблица
	const TABLE = 'database_table_name';
	
	// Поля
	protected $fields;
	
	// Лог ошибок
	protected $errors = array();
	
	// Маркер загрузки
	protected $loaded = FALSE;
	
	
	
	
	/* Конструктор класса */
	public function __construct( $id = NULL ) {
		
		// Определяем названия колонок
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
			DB_NAME,
			static::TABLE
		);
		
		# Удалось
		if ( $r->num_rows() ) {
			while ( $column_name = $r->get_value() ) {
				$this->fields[ $column_name ] = NULL;
			}
		}
		
		# Не удалось, пишем ошибку в лог
		else {
			$this->add_error( 'Не удалось считать поля таблицы `' . static::TABLE . '` из базы данных.' );
			return FALSE;
		}
		
		// Если передан id - загружаем
		if ( ! is_null( $id ) ) {
			$this->load( $id );
		}
	}
	
	
	
	
	/* Загрузка по id */
	public function load( $id ) {
		$this->fields['id'] = $id;

		// Запрашиваем значения из базы данных
		$r = DB::instance()->q( 'SELECT * FROM `' . static::TABLE . '` WHERE id = ? LIMIT 1', $this->fields['id'] );
		
		# Удалось
		if ( $r->num_rows() ) {
			$fields = $r->fetchAssoc();
			foreach ( $fields as $name => $value ) {
				if ( array_key_exists( $name, $this->fields ) ) {
					$this->fields[ $name ] = $value;
				}
			}			
			$this->loaded = TRUE;
			return TRUE;
		}
		
		# Не удалось
		else {
			$this->loaded = FALSE;
			return FALSE;		
		}
	}
	
	
	
	/* Объект по значению поля */
	public static function getByField( $field, $value ) {
		
		// Запрашиваем значения из базы данных
		$field = DB::instance()->escape( $field );
		$r = DB::instance()->q( 'SELECT id FROM `' . static::TABLE . '` WHERE `' . $field . '` = ? LIMIT 1', $value );
		if ( $r->num_rows() ) {
			$class_name = get_called_class();
			return new $class_name( $r->get_value() );
		}
		else return FALSE;
	}
	
	
	
	
	/* Сохранение */
	public function save() {
		
		// Определяем метод
		$method = 'insert';
				
		if ( isset( $this->fields['id'] ) ) {
			$r = DB::instance()->q('SELECT * FROM `' . static::TABLE . '` WHERE id = ? LIMIT 1', $this->fields['id'] );
			if ( $r->numRows() ) {
				$method = 'update';
			}
		}
		
		// Формируем запрос в базу данных
		switch ( $method ) {
			
			// Добавляем запись
			case 'insert':
				$args = array();
				$placeholders = array();
				$columns = array();
				
				foreach ( $this->fields as $name => $value ) {
					if ( $name === 'id' ) continue;
					if ( $name === 'created_at' ) continue;
					$args[] = $value;
					$placeholders[] = '?';
					$columns[] = '`' . $name . '`';
				}
				$query = 'INSERT INTO `' . static::TABLE . '` ( ' . join( $columns, ', ' ) . ' ) VALUES ( ' . join( $placeholders, ', ' ) . ' )';
				break;
				
			// Обновляем запись
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
		
		// Отправляем запрос в базу данных
		$r = DB::instance()->q( $query, $args );
		#echo $r->q;
		return TRUE;
	}
	
	
	
	
	/* Получение свойства */
	public function __get( $name ) {
		if ( array_key_exists( $name, $this->fields ) )
			return $this->fields[ $name ];
		else if ( isset( $this->$name ) )
			return $this->$name;
		else return NULL;
	}
	
	
	
	
	/* Установка свойства */
	public function __set( $name, $value ) {
		if ( array_key_exists( $name, $this->fields ) ) {
			$this->fields[ $name ] = $value;
			return TRUE;
		}
		else return FALSE;
	}
	
	
	
	
	/* Добавление ошибки в лог */
	protected function addError( $message ) {
		$this->errors[] = $message;
		return TRUE;
	}
	
	
	
	
	/* Получение лога */
	public function getErrors() {
		return join( $this->errors, '<br />' );
	}
	
	
	
	/* В строку */
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