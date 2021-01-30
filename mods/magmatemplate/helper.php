<?php
/*
@package: Magma PHP Template Engine
@author: Sören Meier <info@s-me.ch>
@version: 0.1.1 <2019-08-23>
*/

namespace MagmaTemplate;

class Helper {

	public static function between( int $a, int $b ) {
		$ar = [];

		$up = $a < $b;
		$add = $up ? 1 : -1;

		for ( $i = $a; ( $up ? $i < $b : $i > $b ); $i += $add )
			$ar[] = $i;

		return $ar;
	}

	public static function repeat( int $num, $str ) {
		return str_repeat( $str, $num );
	}

	public static function esc( string $str ) {
		return htmlspecialchars( $str, ENT_HTML5 | ENT_QUOTES, 'UTF-8' );
	}

	public static function has( $d = null ) {

		if ( is_string( $d ) )
			return str_len( $d ) > 0;
		else if ( is_array( $d ) )
			return count( $d ) > 0;

		return !is_null( $d );

	}

	public static function join( array $ar, $char = '' ) {
		return implode( $char, $ar );
	}

}