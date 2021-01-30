<?php
/*
@package: Magma PHP Template Engine
@author: Sören Meier <info@s-me.ch>
@version: 0.1 <2019-06-26>
*/

namespace MagmaTemplate;

class CharDivider {

	protected $char = '';

	public function __construct( string $char ) {
		$this->char = $char;
	}

	public function divide( string $ctn, $retEsc = false ) {

		$parts = [];
		$buff = '';
		$inBlock = false;
		$esc = false;

		$len = strlen( $ctn );
		for ( $i = 0; $i < $len; $i++ ) {
			$c = $ctn[$i];

			if ( $esc ) {

				if ( $c !== $this->char || ( $inBlock && $retEsc ) )
					$buff .= '\\';

				$buff .= $c;

				$esc = false;
				continue;

			}

			if ( $c === '\\' ) {
				$esc = true;
				continue;
			}

			if ( $c === $this->char ) {

				// block finished or started
				$parts[] = [ $inBlock, $buff ];
				$inBlock = !$inBlock;
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