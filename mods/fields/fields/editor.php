<?php
/*
@package: Zipp
@version: 1.0 <2019-06-01>
*/

namespace Fields\Fields;

use Fields\Field;
use \Error;

class Editor extends Field {

	public $type = 'editor';

	public $default = '';

	public function validate( object $input ) {
		return is_string( $this->getValue( $input ) );
	}

}