<?php
/*
@package: Zipp
@version: 0.1 <2019-06-13>
*/

namespace Admin;

use Router\Request;
use Core\MagicGet;

class DataRequest extends MagicGet {

	protected $rawReq = null;

	// GETTERS
	public function _getUri() {
		return $this->rawReq->uri;
	}

	// will always output min 1 item which can be ''
	public function _getParts() {
		return $this->rawReq->parts;
	}

	// METHODS
	public function ok( $data = '' ) {
		$this->rawRes( true, $data );
	}

	public function error( string $msg = '' ) {
		$this->rawRes( false, $msg );
	}

	// INIT
	public function __construct( Request $req ) {

		$this->rawReq = $req;
		define( 'DONT_OUTPUT_TIME', true );

	}

	// PROTECTED STUFF
	protected function rawRes( bool $ok, $d ) {
		$this->rawReq->json( [$ok, $d, calcExTime(). ' ms'] );
	}

	// MAGIC
	public function __debugInfo() {
		return [
			'uri' => $this->uri,
			'parts' => $this->parts
		];
	}

}