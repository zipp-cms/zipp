<?php
/*
@package: Zipp
@version: 1.0 <2019-06-01>
*/

namespace Fields\Fields;

use Fields\{Field, Validator};
use \Error;

class Number extends Text {

	public $type = 'number';

	public $default = 0;

	public function validate( object $data ) {

		$d = $this->getValue( $data );

		if ( !is_string( $d ) && !is_int( $d ) )
			return false;

		return Validator::int( $d, $this->sett->min ?? null, $this->sett->max ?? null );

	}

	public function out( object $data ) {
		return (int) ( $this->getValue( $data ) );
	}

}