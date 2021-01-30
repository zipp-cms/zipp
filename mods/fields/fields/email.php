<?php
/*
@package: Zipp
@version: 1.0 <2019-06-01>
*/

namespace Fields\Fields;

use Fields\Field;
use Fields\Validator;
use \Error;

class Email extends Text {

	public $type = 'email';

	public function validate( object $input ) {

		$d = $this->getValue( $input );

		if ( !is_string( $d ) )
			return false;

		return Validator::email( $d, $this->sett->req ?? false, $this->sett->min ?? -1, $this->sett->max ?? -1 );

	}

}