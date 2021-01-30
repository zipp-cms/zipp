<?php
/*
@package: Zipp
@version: 0.1 <2019-06-01>
*/

namespace Fields\Fields;

use Fields\Field;
use \Error;

class CheckBox extends Field {

	public $type = 'checkbox';

	public $default = false;

	public function validate( object $input ) { return true; }

	public function out( object $input ) {
		return (bool) $this->getValue( $input );
	}

}