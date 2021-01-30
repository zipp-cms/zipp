<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Core;

use \Error;

class FS {

	// items to ignore
	protected static $ignore = [ '.', '..', 'lost+found' ];

	// input is not sanitized
	// only === true (only files) only === false (only folders)
	public static function ls( string $path, bool $only = null ) {

		$h = opendir( $path );

		if ( !$h )
			throw new Error( 'could not list files in '. $path );

		while ( ( $entry = readdir( $h ) ) ) {

			if ( in_array( $entry, self::$ignore ) )
				continue;

			// only logic
			if ( !isNil( $only ) && !( $only ? is_file( $path. $entry ) : is_dir( $path. $entry ) ) )
				continue;

			yield $entry;

		}

		closedir( $h );

	}

	// read json
	// doenst check if json or file is valid
	public static function readJson( string $path ) {
		return json_decode( file_get_contents( $path ) );
	}

	// checks if file exists and reads
	public static function checkAndRead( string $path ) {

		if ( !is_file( $path ) )
			throw new Error( sprintf( 'could not read file %s', $path ) );

		return self::read( $path );

	}

	// doenst check if file is valid
	public static function read( string $path ) {
		return file_get_contents( $path );
	}

	// doenst check if data is valid for json or file
	public static function writeJson( string $path, $ctn ) {
		file_put_contents( $path, json_encode( $ctn ) );
	}

	// doenst check if file is valid
	public static function write( string $path, string $ctn ) {
		file_put_contents( $path, $ctn );
	}

	// the file must already exist
	public static function append( string $path, string $ctn ) {
		$hnd = fopen( $path, 'a' );
		fwrite( $hnd, $ctn );
		fclose( $hnd );
	}

	public static function removeRecursive( string $path ) {
		
		foreach ( self::ls( $path ) as $p ) {

			if ( is_file( $path. $p ) )
				unlink( $path. $p );
			else
				self::removeRecursive( $path. $p. DS );

		}

		rmdir( $path );

	}

	// the extension zip must be loaded
	// the folder from needs to exist
	public static function zip( string $from, string $to, array $ignore ) {

		$zip = new \ZipArchive;

		$zip->open( $to, \ZipArchive::CREATE );

		self::zipRecu( $zip, $from, '', $ignore );

		$zip->close();

	}

	protected static function zipRecu( \ZipArchive $zip, string $folder, string $niceFolder, array $ignore ) {

		foreach ( self::ls( $folder ) as $entry ) {

			$p = $folder. $entry;
			$nP = $niceFolder. $entry;

			if ( in_array( $p, $ignore ) || in_array( $p. DS, $ignore ) )
				continue;

			if ( is_dir( $p. DS ) )
				self::zipRecu( $zip, $p. DS, $nP. '/', $ignore );
			else
				$zip->addFile( $p, $nP );

		}

	}

}