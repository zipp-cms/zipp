<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Fields\Viewer;

use \Error;

class Boolean {

	protected $data = false;

	public function __construct( bool $data ) {
		$this->data = $data;
	}

	public function __toString() {
		return $this->data ? 'true' : 'false';
	}

}