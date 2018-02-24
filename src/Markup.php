<?php

namespace Vatrikovsky\Core;

abstract class Markup {
	
	// On / Off Methods
	protected static $methods = [
		'cleanup' => TRUE,
		'strong' => TRUE,
		'em' => TRUE,
		'a' => TRUE,
		'img' => TRUE,
		'youtube' => TRUE,
		'caption' => TRUE,
		'p' => TRUE		
	];
	
	// Config
	protected static $options = [
		'img_ext' => ['jpg', 'jpeg', 'png', 'gif']
	];
	
	// Protected
	protected static $delimiter = FALSE;
	
	
	
	
	/* All Methods in One */
	public static function all( $text ) {
		foreach ( self::$methods as $method => $on ) {
			if ( $on ) {
				$text = self::$method( $text );
			}
		}
		return $text;
	}
	
	
	
	
	/* Enable Method */
	public static function enableMethod( $method ) {
		if ( array_key_exists( $method, self::$methods ) ) {
			self::$methods[ $method ] = TRUE;
		}
	}
	
	
	
	
	/* Enable All Methods */
	public static function enableAll() {
		foreach ( self::$methods as $method => $flag ) {
			self::$methods[ $method ] = TRUE;
		}
	}
	
	
	
	
	/* Disable Method */
	public static function disableMethod( $method ) {
		if ( array_key_exists( $method, self::$methods ) ) {
			self::$methods[ $method ] = FALSE;
		}
	}
	
	
	
	
	/* Disable All Methods */
	public static function disableAll() {
		foreach ( self::$methods as $method => $flag ) {
			self::$methods[ $method ] = FALSE;
		}
	}
	
	
	
	
	/* Set Option */
	public static function setOption( $option, $value ) {
		if ( array_key_exists( $option, self::$options ) ) {
			self::$options[ $option ] = $value;
		}
	}
	
	
	
	
	/* Trims strings spaces and text empty strings */
	public static function cleanup( $text ) {
		if ( !self::$methods['cleanup'] ) return $text;
		$delimiter = "\r\n";
		if ( FALSE === mb_strpos( $text, $delimiter ) ) $delimiter = "\n\r";
		elseif ( FALSE === mb_strpos( $text, $delimiter ) ) $delimiter = "\n";
		elseif ( FALSE === mb_strpos( $text, $delimiter ) ) $delimiter = "\r";
		elseif ( FALSE === mb_strpos( $text, $delimiter ) ) $delimiter = FALSE;
		
		if ( $delimiter ) {
			self::$delimiter = $delimiter;
			$text = explode( $delimiter, $text );
			foreach ( $text as &$line ) {
				$line = trim( $line );
			}
			$text = join( $delimiter, $text );
			$text = trim( $text, $delimiter );
		}
		else {
			$text = trim( $text );
		}	
		
		return $text;
	}
	
	
	
	
	/* **some text** -> <strong>some text</strong> */
	public static function strong( $text ) {
		if ( !self::$methods['strong'] ) return $text;
		return preg_replace( 
			'~(^|\s)\*\*(.*)\*\*(\W|$)~im', 
			'$1<strong>$2</strong>$3',
			$text 
		);
	}
	
	
	
	
	/* //some text// -> <em>some text</em> */
	public static function em( $text ) {
		if ( !self::$methods['em'] ) return $text;
		return preg_replace( 
			'~(^|\s)//(.*)//(\W|$)~im',  
			'$1<em>$2</em>$3',
			$text 
		);
	}
	
	
	
	
	/* ((some-text)) -> <a href="...">...<a> */
	/* ((some-text some-text-2)) -> <a href="...1">...2<a> */
	public static function a( $text ) {
		if ( !self::$methods['a'] ) return $text;
		$text = preg_replace( 
			'~\(\((https?://\S+)\)\)~im',  
			'<a href="$1">$1</a>',
			$text 
		);
		$text = preg_replace( 
			'~\(\((https?://\S+)\s(.+)\)\)~im',  
			'<a href="$1">$2</a>',
			$text 
		);
		return $text;		
	}
	
	
	
	
	/* image.jpg -> <div class="v-image"><img src="..." /></div> */
	public static function img( $text ) {
		if ( !self::$methods['img'] or empty( self::$options['img_ext'] ) ) return $text;
		return preg_replace( 
			'~^\s*(\S*)\.((' . join( ')|(', self::$options['img_ext'] ) . '))\s*$~im',  
			'<div class="v-image"><img src="$1.$2" /></div>',
			$text 
		);
	}
	
	
	
	
	/* youtube -> <div class="v-video"><iframe /></div> */
	public static function youtube( $text ) {
		if ( !self::$methods['youtube'] ) return $text;
		$text = preg_replace( 
			'~^\s*https?://(www\.)?youtube\.com/watch\?v=(\S+)\s*$~im',  
			'<div class="v-video"><iframe width="640" height="360" src="https://www.youtube.com/embed/$2" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>',
			$text
		);
		$text = preg_replace( 
			'~^\s*https?://(www\.)?youtu\.be/(\S+)(&|\s|$)~im',  
			'<div class="v-video">$2</div>',
			$text 
		);
		return $text;
	}
	
	
	
	
	/* string after </div> -> <p class="v-caption">...</p></div> */
	public static function caption( $text ) {
		if ( !self::$methods['caption'] ) return $text;
		return preg_replace( 
			'~</div>(\r|\n|(\r\n))?^(.+)$~im',  
			'<p class="v-caption">$3</p></div>',
			$text 
		);
	}
	
	
	
	
	/* non-tag-starts string -> <p>...</p> */
	public static function p( $text ) {
		if ( !self::$methods['p'] ) return $text;
		return preg_replace( 
			'~^(?!<div)(.+)$(?!</div>)~im',  
			'<p class="v-text">$1</p>',
			$text 
		);
	}	
}

?>