<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Router;

use Core\MagicGet;
use Core\KERNEL;

class Request extends MagicGet {

	protected $router = null;

	protected $uri = '';

	protected $rawData = null;

	// GETTERS
	public function _getRouter() {
		return $this->router;
	}

	public function _getUri() {
		return $this->uri;
	}

	public function _getFullUri() {
		return cleanUrl( $this->router->basePath. $this->uri ); // is this good?
	}

	public function _getHost() {
		return $this->router->host;
	}

	public function _getRawData() {

		if ( isNil( $this->rawData ) )
			$this->rawData = file_get_contents( 'php://input' );

		return $this->rawData;

	}

	public function _getPost() {
		return (object) $_POST;
	}

	public function _getGet() {
		return (object) $_GET;
	}

	public function _getFiles() {
		return (object) $_FILES;
	}

	// content type (json/form)
	public function _getCtnType() {
		return $_SERVER['CONTENT_TYPE'] ?? '';
	}

	// request type (http/ajax)
	public function _getType() { // this should
		return (string) ( $_SERVER['HTTP_REQ_TYPE'] ?? 'http' );
	}

	public function _getParts() {
		return explode( '/', trim( $this->uri, '/' ) );
	}

	// METHODS
	public function error( int $code, string $msg = '' ) {
		printf( 'error %d url: "%s" > %s', $code, $this->uri, $msg ); // here i dont throw an error because it isnt a fatal error
		KERNEL::stop();
	}

	public function error404( string $msg = '' ) {
		$this->error( 404, $msg );
	}

	public function errorAuth( string $msg = '' ) {
		$this->error( 401, $msg );
	}

	public function redirect( string $url = '', bool $perm = false ) {
		$this->router->redirect( $url, $perm );
	}

	public function json( $any ) {
		echo json_encode( $any );
	}

	// INIT
	public function __construct( Router $router, string $uri ) {

		$this->router = $router;
		$this->uri = $uri;

	}

	// MAGIC
	public function __debugInfo() {
		return [
			'uri' => $this->uri,
			'parts' => $this->parts,
			'get' => $this->get,
			'files' => $this->files,
			'post' => $this->post,
			'rawData' => null,
			'type' => $this->type,
			'ctnType' => $this->ctnType
		];
	}

}