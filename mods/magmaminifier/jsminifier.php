<?php
/*
@package: Magma PHP Minifier for JS and CSS
@author: Sören Meier <info@s-me.ch>
@version: 0.1.2 <2019-08-23>
@docs: minifier.magma-lang.com/php/docs/
*/

namespace MagmaMinifier;

class JsMinifier {

	protected $tree = [];

	public function go( string $str ) {

		$reg = $this->prepareRegex();

		// die( $reg );

		$str = str_replace( "\r", '', $str );

		// 1e5 + +x
		// fix for
		$str = preg_replace( '/(?<=[+\-\/%*])\s+([+-][a-zA-Z0-9.]+)/', '($0)', $str );

		$parts = $this->parseJs( $str );

		$str = '';
		foreach ( $parts as $p ) {
			$s = $p[1];

			if ( $p[0] ) {
				$str .= $s;
				continue;
			}

			// ' ' for lookbehind
			$s = preg_replace( $reg, '', ' '. $s );
			$s = preg_replace( '/(?<=[\s;\({]else)\n/sm', ' ', $s );
			$str .= $s;

		}

		// foreach ( $parts as $p ) {
		// 	echo $p[0] ? 'block ' : 'noblock ';
		// 	echo '"'. $p[1]. "\"\n";
		// }
		// echo "\n\nEOF\n\n";


		return $str;
		

	}

	protected function prepareRegex() {

		// fix (new >
		$left = [ '[\s;\({]new' ];
		$right = [ 'new\s' ];

		$keepingSpaces = explode( ',', 'in,of,instanceof,extends' );
		foreach ( $keepingSpaces as $ks ) {
			$left[] = '\s'. $ks;
			$right[] = $ks. '\s';
		}

		$keepingSpace = explode( ',', 'let,const,var,await,async,class,typeof,function,export,import,case,throw,import,void,delete,return,yield,static,get,set,implements,package,private,public,protected,break,continue,else' );
		foreach ( $keepingSpace as $ks )
			$left[] = '[\s;\({]'. $ks;

		return sprintf( '/(?<!%s)\s+(?!%s)/sm', implode( '|', $left ), implode( '|', $right ) );

	}

	protected function parseJs( string $str ) {

		// ' < ar outside of the block
		// ${ < ar outlise of the nBlock and inside of the block
		// 'block' "block" `block${  nBlock  }block`

		$parts = [];

		$buff = '';

		$esc = false;

		// comments
		$cBlockStart = false;
		$inCBlock = false;
		$cShort = false; // single our double comment

		// regex
		$inRegBlock = false;
		$regEsc = false;

		// strBlock
		$inBlock = false;
		$blockChar = '';

		// nested block
		$nBlockStart = false;
		$inNBlock = false;
		$nBlockCount = 0;

		$len = strlen( $str );
		for ( $i = 0; $i < $len; $i++ ) {
			$c = $str[$i];

			if ( $cBlockStart ) {
				$cBlockStart = false;

				// check for comment
				if ( $c === '*' || $c === '/' ) {
					$parts[] = [false, $buff];
					$buff = '';
					$inCBlock = true;
					$cShort = $c === '/';
					continue;
				}

				// check if this block corresponds to a regex
				if ( preg_match( '/([^a-zA-Z0-9\s\])]|=>|[^a-zA-Z0-9]return\s)\s*$/s', $buff ) ) {
					// we have a regex
					$parts[] = [false, $buff. '/'];
					$buff = '';
					$inBlock = true;
					$blockChar = '/';

				} else {
					$buff .= '/'. $c;
					continue;
				}

			}

			// if in comments /* */
			if ( $inCBlock ) {

				// skip comment block
				$ending = $cShort ? "\n" : '*/';

				$piece = substr( $str, $i );
				$endI = strpos( $piece, $ending );
				if ( $endI !== false )
					$piece = substr( $piece, 0, $endI + strlen( $ending ) );

				if ( $piece[0] === '!' )
					$parts[] = [true, ( $cShort ? '//' : '/*' ). $piece];

				$i += strlen( $piece ) - 1;
				$inCBlock = false;

				continue;

			}

			if ( $inNBlock ) { // the buffer contains the inblock without ${

				if ( $c === '{' ) {
					$nBlockCount++;
					$buff .= $c;
					continue;
				}

				// maybe should use strpos, would be faster :/
				if ( $c === '}' ) {
					$nBlockCount--;
					
					if ( $nBlockCount > 0 ) {
						$buff .= $c;
						continue;
					}

					// nBlock is finished
					$parts = array_merge( $parts, $this->parseJs( $buff ) );

					// the buffer start with the end of the nBlock
					$buff = $c;
					$inNBlock = false;
					$nBlockCount = 0;

					continue;

				}

				$buff .= $c;
				continue;

			}

			// if in strBlock
			if ( $inBlock ) {

				if ( $esc ) {
					$esc = false;
					$buff .= $c;
					continue;
				}

				if ( $regEsc && $c === $blockChar ) {
					$buff .= $c;
					continue;
				}

				if ( $c === '\\' ) {
					$buff .= $c;
					$esc = true;
					continue;
				}

				// if could start a nested block
				if ( $blockChar === '`' ) {

					if ( $nBlockStart ) {

						// start nBlock
						if ( $c === '{' ) {

							$parts[] = [true, $buff. $c];
							$buff = '';
							$nBlockStart = false;
							$nBlockCount = 1;
							$inNBlock = true;

							continue;

						} else
							$nBlockStart = false;

					}

					if ( $c === '$' ) {
						$buff .= $c;
						$nBlockStart = true;
						continue;
					}

				}

				// if regex
				if ( $blockChar === '/' ) {

					if ( $c === '[' )
						$regEsc = true;

					if ( $c === ']' )
						$regEsc = false;

				}


				// if at the end of the block
				// add block to the parts
				if ( $c === $blockChar ) {
					$parts[] = [true, $buff];
					$buff = $c;
					$inBlock = false;
					continue;
				}

				$buff .= $c;

				continue;

			}


			// CHECK TO START A BLOCK
			if ( $c === '/' ) {
				$cBlockStart = true;
				continue;
			}

			// if str block is starting
			if ( $c === '\'' || $c === '"' || $c === '`' ) {

				$buff .= $c;
				$parts[] = [false, $buff];
				$buff = '';
				$inBlock = true;
				$blockChar = $c;
				continue;

			}

			$buff .= $c;

		}

		if ( $inNBlock ) {
			$buff .= $c;
			$parts = array_merge( $parts, $this->parseJs( $buff ) );
			$buff = '';
		}

		if ( $buff !== '' )
			$parts[] = [$inBlock, $buff];

		return $parts;

	}

}