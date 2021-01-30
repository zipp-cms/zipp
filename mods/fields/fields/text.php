<?php
/*
@package: Zipp
@version: 1.0 <2019-06-01>
*/

namespace Fields\Fields;

use Fields\Field;
use Fields\Validator;
use \Error;

class Text extends Field {

	public $type = 'text';

	public $default = '';

	public function validate( object $input ) {

		$d = $this->getValue( $input );

		if ( isNil( $d ) || !is_string( $d ) )
			return false;

		return Validator::str( $d, $this->sett->req ?? false, $this->sett->min ?? -1, $this->sett->max ?? -1 );

	}

}