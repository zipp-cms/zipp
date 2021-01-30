<?php
/*
@package: Zipp
@version: 0.1 <2019-06-04>
*/

namespace PagesInt\Fields;

use \Error;
use Fields\Fields\Text;

class PageUrl extends Text {

	public $type = 'pageurl';

	public $default = '';

	public function validate( object $in ) {

		$d = $this->getValue( $in );

		if ( !is_string( $d ) )
			return false;

		// 49 maybe there isnt a slash at the end /
		return cLen( $d, 1, 49 ) && !preg_match( '/[;?:@=&"<>#%{}|\\^~\[\]`\s]/', $d );

	}

	public function out( object $input ) {
		return trim( $this->getValue( $input ), '/' ). '/';
	}

}