<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Ajax;

use Router\Request as RouterRequest;
use Core\MagicGet;

class Request extends MagicGet {

	protected $ajax = null;

	protected $rawReq = null;

	public $event = '';

	// GETTERS
	public function _getData() {
		return $this->getData();
	}

	public function _getFiles() {
		return $this->rawReq->files;
	}

	// maybe later will remove the ajax parameter
	public function __construct( Ajax $ajax, RouterRequest $req ) {

		$this->ajax = $ajax;
		$this->rawReq = $req;

	}

	public function ok( $data ) {
		$this->rawRes( true, 0, $data );
	}

	public function formOk( $data, string $nonce = null ) {
		$this->rawRes( true, 1, [$data, $nonce] );
	}

	public function error( string $msg = '' ) {
		$this->rawRes( false, 0, $msg );
	}

	public function formError( string $msg, string $nonce = null ) {
		$this->rawRes( false, 1, [$msg, $nonce] );
	}

	public function getData() {

		$t = $this->rawReq->ctnType;

		if ( $t === 'application/json' )
			return json_decode( $this->rawReq->rawData );

		return $this->rawReq->post;

	}

	// PROTECTED
	protected function rawRes( bool $ok, int $type, $d ) {
		$this->rawReq->json( [$ok, $type, $d, calcExTime(). ' ms'] );
		define( 'DONT_OUTPUT_TIME', true );
	}

	// MAGIC
	public function __debugInfo() {
		return [
			'data' => $this->data,
			'files' => $this->files,
			'ok' => '()',
			'formOk' => '()',
			'error' => '()',
			'formError' => '()'
		];
	}

}