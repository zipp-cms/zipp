<?php
/*
@package: Zipp
@version: 1.0 <2019-06-01>
*/

namespace Fields\Fields;

use Fields\{Field, Validator};
use \Error;

class Keywords extends Text {

	public $type = 'keywords';

	public function out( object $in ) {
		$d = explode( ',', (string) $this->getValue( $in ) );
		return array_map( 'trim', $d );
	}

}