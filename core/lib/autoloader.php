<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Core;

use \Error;

class Autoloader {

	protected static $paths = [];

	protected static $directives = [];

	// METHODS
	public static function addPath( string $path ) {
		self::$paths[] = $path;
	}

	public static function addDirective( string $prefix, string $path ) {
		self::$directives[$prefix] = $path;
	}

	// cls with namespace
	public static function load( string $cls ) {

		$parts = explode( '\\', $cls );
		if ( !has( $parts ) )
			throw new Error( sprintf( 'Autoloader could not load %s because it had only one part', $cls ) );

		if ( isset( self::$directives[ $parts[0] ] ) ) {
			$pre = array_shift( $parts );
			return self::loadInPath( $parts, self::$directives[$pre] );
		}

		foreach ( self::$paths as $path ) {

			try {
				return self::loadInPath( $parts, $path );
			} catch ( Error $e ) {
				if ( ( $e->output ?? false ) )
					throw $e;
			}

		}

		throw new Error( 'autoloader could not load class '. $cls );

	}

	protected static function loadInPath( array $parts, string $path ) {

		$nPath = $path. lower( implode( DS, $parts ) ). '.php';

		if ( !is_file( $nPath ) )
			throw new Error( sprintf( 'could not find file %s', $nPath ) );

		try {
			require_once( $nPath );
		} catch ( Error $e ) {
			$e->output = true;
			throw $e;
		}

	}

}

spl_autoload_register( 'Core\Autoloader::load' );