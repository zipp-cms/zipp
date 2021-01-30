<?php
/*
@package: Zipp
@version: 1.0 <2019-06-01>
*/

namespace Fields;

class Validator {

	public static function main( string $in, object $sett ) {

		$t = $sett->type ?? 'string';

		switch ( $t ) {

			case 'int':
				return Validator::int( $in, $sett->min ?? null, $sett->max ?? null );
				break;

			case 'string':
				return Validator::str( $in, $sett->req ?? false, $sett->min ?? -1, $sett->max ??-1 );
				break;

		}

		return false;

	}

	public static function int( string $in, int $min = null, int $max = null ) {

		$i = (int) $in;

		if ( $i != $in )
			return false;

		$ma = isNil( $max ) || $in <= $max;
		$mi = isNil( $min ) || $in >= $min;

		return $ma && $mi;

	}

	public static function str( string $in, bool $req, int $min = -1, int $max = -1 ) {

		if ( $req && $min < 0 )
			$min = 1;

		$ma = $max < 0 || len( $in ) <= $max;
		$mi = $min < 0 || len( $in ) >= $min;

		return $ma && $mi;

	}

	public static function email( string $in, bool $req, int $min = -1, int $max = -1 ) {

		if ( !self::str( $in, $req, $min, $max ) )
			return false;

		if ( $req || cLen( $in ) )
			return filter_var( $in, FILTER_VALIDATE_EMAIL );

		return true;

	}

}