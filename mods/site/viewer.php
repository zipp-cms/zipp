<?php
/*
@package: Zipp
@version: 0.1 <2019-05-31>
*/

namespace Site;

class Viewer {

	protected $site = null;

	protected $lang = '';

	public function __construct( Site $site, string $lang ) {
		$this->site = $site;
		$this->lang = $lang;
	}

	public function __get( string $k ) {

		return $this->site->getMl( $k, $this->lang ) ?? $this->site->get( $k );

	}

	public function __debugInfo() {
		return [
			'__get' => 'getAnything'
		];
	}

}