<?php
/*
@package: Zipp
@version: 0.2 <2019-07-11>
*/

namespace Logs;

use Core\{Module, FS};

class Logs extends Module {

	protected $logPath = '';

	protected $logFile = '';

	protected $queue = [];

	// METHODS
	// custom should be a string or an array (only one layer)
	public function log( string $cat, string $uri, $custom = '' ) {

		if ( !is_array( $custom ) )
			$custom = [$custom];

		$this->queue[] = [ $cat, $uri, $custom ];

	}

	public function get( string $date ) {

		$path = $this->logPath. $date. '.txt';
		if ( !is_file( $path ) )
			return [];

		return $this->parseFile( $path, $date );

	}

	public function getAll() {

		$entries = [];
		foreach ( FS::ls( $this->logPath, true ) as $entry )
			$entries[] = $entry;

		sort( $entries );

		// yyyy-mm-dd
		$list = [];
		foreach ( $entries as $entry )
			$list = array_merge( $list, $this->parseFile( $this->logPath. $entry, substr( $entry, 0, 10 ) ) );
		
		return $list;

	}

	public function clearAll() {
		FS::removeRecursive( $this->logPath );
		$this->onConstruct();
	}

	// INIT
	public function onConstruct() {

		$this->logPath = USER_PATH. 'logs'. DS;
		$this->logFile = $this->logPath. date('Y-m-d'). '.txt';

		if ( !is_dir( $this->logPath ) )
			mkdir( $this->logPath );

		if ( !is_file( $this->logFile ) )
			touch( $this->logFile );

	}

	public function onTermination() {

		if ( !has( $this->queue ) )
			return;

		$ip = $_SERVER['REMOTE_ADDR'] ?? '::1';
		$host = substr( $_SERVER['HTTP_HOST'] ?? '', 0, 50 );
		// $uri = urldecode( substr( $_SERVER['REQUEST_URI'] ?? '', 0, 200 ) );
		$referer = substr( $_SERVER['HTTP_REFERER'] ?? '', 0, 100 );
		$userAgent = substr( $_SERVER['HTTP_USER_AGENT'], 0, 200 );

		foreach ( $this->queue as $el )
			$this->write( $el[0], substr( $el[1], 0, 200 ), $ip, $host, $referer, $userAgent, $el[2] );

		// TODO: shoud we do this??
		/*if ( php_sapi_name() === 'cli' ) {
			$this->write( 'cli', '::1', 'local', $this->subArray( $_SERVER['argv'] ?? [] ), 'cli' );
			return;
		}*/

	}

	// PROTECTED
	protected function write( string $cat, string $uri, string $ip, string $host, string $referer, string $userAgent, array $custom ) {

		$ctn = [ $cat, date('H:i:s'), $uri, calcExTime(), DEBUG ? 'debug' : 'prod', $host, $ip, $referer, $userAgent, $this->subArray( $custom ) ];
		foreach ( $ctn as &$c )
			$c = str_replace( [ "\r", "\n", "#" ], [ '', '\n', '-' ], $c );

		FS::append( $this->logFile, implode( '#', $ctn ). "\n" );

	}

	protected function subArray( array $ctn ) {

		$nCtn = [];
		foreach ( $ctn as $c )
			$nCtn[] = str_replace( '|', '_', $c );

		return implode( '|', $nCtn );

	}

	protected function parseFile( string $path, string $date ) {
		$str = file_get_contents( $path );
		$list = explode( "\n", $str );
		$nList = [];
		foreach ( $list as $l ) {
			if ( $l === '' )
				continue;
			$items = explode( '#', $l );
			$items[1] = $date. ' '. $items[1];
			$nList[] = $items;
		}

		return $nList;
	}

}