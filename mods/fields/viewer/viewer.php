<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Fields\Viewer;

use \Error;

class Viewer {

	protected $viewers = [];

	public function __construct( array $viewers ) {
		$this->viewers = $viewers;
	}

	public function __get( string $k ) {
		return $this->viewers[$k] ?? $k;
	}

	public function __debugInfo() {
		return $this->viewers;
	}

}