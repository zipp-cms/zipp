<?php
/*
@package: Zipp
@version: 1.0 <2019-08-15>
*/

namespace Fields\Fields;

use Fields\Field;
use Fields\Validator;
use \Error;

class Password extends Text {

	public $type = 'password';

	public function validate( object $input ) {

		$d = $this->getValue( $input );

		if ( !is_string( $d ) )
			return false;

		return Validator::str( $d, $this->sett->req ?? false, $this->sett->min ?? -1, $this->sett->max ?? -1 );

	}

}