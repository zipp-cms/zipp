<?php
/*
@package: Zipp
@version: 1.0 <2019-06-01>
*/

namespace Fields\Fields;

use Fields\Field;
use Fields\Validator;
use \Error;

class Hidden extends Field {

	public $type = 'hidden';

	public $default = '';

	public function validate( object $input ) {

		$d = $this->getValue( $input );

		if ( !is_string( $d ) )
			return false;

		return Validator::main( $d, (object) $this->sett );

	}

	public function out( object $input ) {
		$d = $this->getValue( $input );

		if ( !isNil( $d ) && ( $this->sett->type ?? '' ) === 'int' )
			return (int) $d;

		return $d;
	}

}