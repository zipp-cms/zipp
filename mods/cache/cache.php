<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Cache;

use Core\{Module, KERNEL, FS};

// if you make a GET REQUEST with another item
// this will make a new request

class Cache extends Module {

	protected $cPath = '';

	protected $pUrl = '';

	protected $pUri = '';

	public function onInit() {

		$this->parseUrl();

		if ( isNil( $this->pUrl ) )
			return;

		$this->cPath = TMP_PATH. 'cache'. DS;

		$filename = md5( $this->pUrl ). '.html';

		if ( !is_file( $this->cPath. $filename ) || DEBUG )
			return;

		session_start();
		$skip = (bool) ( $_SESSION['CACHE_SKIP'] ?? false );
		session_write_close();

		if ( $skip )
			return;

		readfile( $this->cPath. $filename );

		if ( $this->mods->has( 'Logs' ) )
			$this->mods->Logs->log( 'req', $this->pUri, 'cache' );

		// to stop the execution of the next step
		KERNEL::stop();

	}

	// starting to cache a site
	public function start() {
		ob_start();
	}

	public function stop() {

		if ( defined( 'CACHE_DONT_SAVE' ) )
			return ob_end_flush();

		$ctn = ob_get_flush();

		$filename = md5( $this->pUrl ). '.html';

		if ( !is_dir( $this->cPath ) )
			mkdir( $this->cPath );
		
		file_put_contents( $this->cPath. $filename, $ctn );

	}

	public function clear() {

		if ( !is_dir( $this->cPath ) )
			return;

		foreach ( FS::ls( $this->cPath, true ) as $entry )
			unlink( $this->cPath. $entry );

	}

	// if you change this, change it at the router to
	protected function parseUrl() {

		$https = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on';
		$host = $_SERVER['HTTP_HOST'] ?? null;
		$uri = $_SERVER['REQUEST_URI'] ?? null;

		if ( isNil( $host ) || isNil( $uri ) )
			return null;

		$uri = parseUri( $uri );
		$this->pUri = $uri;

		$this->pUrl = sprintf( 'http%s://%s/%s', $https ? 's' : '', $host, $uri );

	}

}