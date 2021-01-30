<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Fields\Viewer;

use \Error;

class ArrayViewer {

	protected $ar = [];

	public function __construct( array $ar ) {
		$this->ar = $ar;
	}

	public function __toString() {
		return implode( ', ', $this->ar );
	}

}