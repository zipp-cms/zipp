<?php
/*
@package: Magma PHP Minifier for JS and CSS
@author: Sören Meier <info@s-me.ch>
@version: 0.1 <2019-07-05>
@docs: minifier.magma-lang.com/php/docs/
*/

namespace MagmaMinifier;

class CssMinifier {

	public function go( string $str ) {

		$str = str_replace( "\r", '', $str );
		$str = preg_replace( '/\/\*(?!\!).*?\*\//m', '', $str );

		$parts = $this->divideStr( $str );

		$str = '';
		foreach ( $parts as $p ) {

			if ( $p[0] ) {
				$str .= $p[1];
				continue;
			}

			$s = preg_replace( '/(?<=;|{|}|,|:)\s+(?=\S|$)|(?<=\S)(?<!or|and)\s+(?={|\()/m', '', $p[1] );
			$s = preg_replace( '/;}/m', '}', $s );

			$str .= $s;

		}

		return $str;

	}

	protected function divideStr( $str ) {

		$parts = [];
		$buff = '';
		$char = '';
		$inBlock = false;
		$esc = false;

		$len = strlen( $str );
		for ( $i = 0; $i < $len; $i++ ) {
			$c = $str[$i];

			if ( $esc ) {
				$buff .= '\\'. $c;
				$esc = false;
				continue;
			}

			if ( $inBlock && $c === '\\' ) {
				$esc = true;
				continue;
			}

			if ( ( $inBlock && $c === $char ) || ( !$inBlock && ( $c === '"' || $c === "'" ) ) ) {

				// block finished or started
				$char = $inBlock ? $char : '';
				$parts[] = [ $inBlock, $char. $buff. $char ];
				$inBlock = !$inBlock;
				$char = $c;
				$buff = '';
				continue;

			}

			$buff .= $c;

		}

		if ( strlen( $buff ) > 0 )
			$parts[] = [ $inBlock, $buff ];

		return $parts;

	}

}