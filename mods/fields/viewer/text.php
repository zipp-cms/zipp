<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Fields\Viewer;

use \Error;

class Text {

	protected $str = '';

	public function __construct( string $str ) {
		$this->str = $str;
	}

	public function esc() {
		return e( $this->str );
	}

	public function __toString() {
		return $this->str;
	}

}