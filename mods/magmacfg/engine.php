<?php
/*
@package: A Magma Config Reader for PHP
@author: Sören Meier <info@s-me.ch>
@version: 0.2 <2021-07-01>
@docs: cfg.magma-lang.com/php/docs/
*/

namespace MagmaCfg;

use \Error;

class Engine {

	protected $debug = false;

	protected $tmpPath = '';

	public $tabWidth = 4;

	// METHODS
	public function go( string $file ) {

		$filename = md5( $file ). '.php';
		$out = $this->tmpPath. $filename;

		if ( is_file( $out ) && !$this->debug )
			return ( include $out );

		$str = file_get_contents( $file );
		$tree = $this->parse( $str );

		file_put_contents( $out, $this->exportTree( $tree ) );
		return $tree;

	}

	public function parseRaw( string $ctn ) {
		return $this->parse( $ctn );
	}

	public function cleanTmp() {
		self::deleteDir( $this->tmpPath );
	}

	// INIT
	public function __construct( string $tmpPath, bool $debug = false ) {

		$this->tmpPath = $tmpPath;
		$this->debug = $debug;
		if ( !is_dir( $this->tmpPath ) )
			mkdir( $this->tmpPath );

	}

	// PROTECTED
	protected function parse( string $str ) {

		$str = rtrim( str_replace( "\r", '', $str ) );
		$lines = explode( "\n", $str );



		$data = (object) [];


		$prevLevel = 0;
		$levels = [
			(object) [ 'type' => 'obj', 'name' => 'root' ]
		];

		foreach ( $lines as $num => $line ) {

			// if empty line or comment line
			if ( preg_match( '/^\s*(\/\/[^\n]*)?$/', $line ) )
				continue;

			$level = $this->countLevel( $line, $num );
			$line = trim( $line );

			if ( $level < $prevLevel )
				$prevLevel = $level;
			else if ( $level > $prevLevel )
				throw new \Error( 'should not switch level on line '. $num );

			// set ctx
			$ctx = $levels[$level];

			if ( $ctx->type === 'obj' ) {

				$col = strpos( $line, ':' );

				if ( !$col ) { // is multiline array or obj

					$prevLevel++;
					$nType = 'obj';
					$nName = $line;

					// is array
					if ( preg_match( '/\s-a$/', $line ) ) {
						$nType = 'arr';
						$nName = rtrim( substr( $line, 0, -2 ) );
					}

					// is object following
					$levels[$prevLevel] = (object) [ 'type' => $nType, 'name' => $nName ];
					continue;

				}

				// is property

				$key = rtrim( substr( $line, 0, $col ) );
				$value = ltrim( substr( $line, $col + 1 ) );

				// is array
				if ( preg_match( '/\s-a$/', $key ) ) {

					$key = rtrim( substr( $key, 0, -2 ) );
					$value = $this->parseSingleLineArray( $value );

				} else
					$value = $this->parseValue( $value );

				$this->write( $level, $levels, $data, $key, $value );
				continue;

			}

			// if is type array
			if ( $ctx->type === 'arr' ) {

				if ( preg_match( '/^-[ao]$/', $line ) ) {

					$prevLevel++;
					$nType = $line === '-a' ? 'arr' : 'obj';

					$levels[$prevLevel] = (object) [ 'type' => $nType, 'name' => null ];
					continue;
					// new level line

				}

				// check if inline array
				$value = $line;
				if ( preg_match( '/^-a:\s/', $value ) ) // if is single array
					$value = $this->parseSingleLineArray( ltrim( substr( $value, 3 ) ) );
				else
					$value = $this->parseValue( $value );

				$this->write( $level, $levels, $data, null, $value );

			}

		}


		return $data;

	}

	protected function countLevel( string $line, int $lineNum ) {
		$count = 0;
		$spaceCount = 0;
		$len = strlen( $line );
		for ( $i = 0; $i < $len; $i++ ) {
			$c = $line[$i];
			// count a tab as one level
			if ( $c === "\t" ) {
				$count++;
				$spaceCount = 0;
				continue;
			}

			if ( $c !== " " )
				break;

			// we have a space
			$spaceCount++;

			if ( $spaceCount === $this->tabWidth ) {
				$count++;
				$spaceCount = 0;
			}
		}
		if ( $spaceCount > 0 )
			throw new Error( sprintf( 'Only %d spaces instead of %d where found on line %d', $spaceCount, $this->tabWidth, $lineNum ) );
		return $count;
	}

	protected function parseSingleLineArray( string $line ) {

		$ar = [];

		$buff = '';
		$esc = false;
		$act = 0;
		$len = strlen( $line );
		for ( $i = 0; $i < $len; $i++ ) {
			$c = $line[$i];

			if ( $esc ) {

				$esc = false;
				if ( $c !== ',' )
					$buff .= '\\';
				$buff .= $c;
				continue;

			}

			if ( $c === '\\' ) {
				$esc = true;
				continue;
			}

			if ( $c === ',' ) {
				$ar[] = $this->parseValue( trim( $buff ) );
				$buff = '';
				continue;
			}

			$buff .= $c;

		}

		if ( $esc )
			$buff .= '\\';

		if ( $buff !== '' )
			$ar[] = $this->parseValue( trim( $buff ) );

		return $ar;

	}

	protected function parseValue( string $line ) {

		// boolean
		if ( $line === 'true' || $line === 'false' )
			return $line === 'true';

		// null
		if ( $line === 'null' )
			return null;

		// int
		if ( preg_match( '/^-?\d+$/', $line ) )
			return (int) $line;

		// float
		if ( preg_match( '/^-?\d*\.\d*$/', $line ) )
			return (float) $line;

		// string
		return $line;

	}

	// $this->writeToObj( $level, $levels, $data, $value );
	protected function write( int $level, array &$levels, object &$data, string $key = null, $value ) {

		$actData = &$data;
		for ( $i = 1; $i <= $level; $i++ ) {
			$name = $levels[$i]->name;
			$type = $levels[$i]->type;
			if ( is_null( $name ) )
				$actData = &$actData[count( $actData ) - 1];
			else {
				if ( is_null($actData) )
					$actData = (object) [ $name => null ];
				$actData = &$actData->$name;
			}
		}

		if ( is_null( $key ) ) {

			// array
			if ( !is_array( $actData ) )
				$actData = [];
			$actData[] = $value;

		} else {
			if ( !is_object( $actData ) )
				$actData = (object) [];
			$actData->$key = $value;
		}

	}

	// protected function recu

	//	$this->write( $level, $levels, $data, $key, $value );

	protected function exportTree( object $tree ) {
		$h = "<?php \n\n return ";
		$h .= $this->recuObjExport( $tree, 0 );
		$h .= ';';
		return $h;
	}

	protected function recuObjExport( object $data, int $level ) {

		// var_export( true )
		$h = '(object) ['. "\n";
		$t = str_repeat( "\t", $level + 1 );
		$itms = [];

		foreach ( $data as $key => $value ) {

			if ( is_array( $value ) )
				$value = $this->recuArrExport( $value, $level + 1 );
			else if ( is_object( $value ) )
				$value = $this->recuObjExport( $value, $level + 1 );
			else
				$value = var_export( $value, true );

			$itms[] = $t. var_export( $key, true ). ' => '. $value;

		}

		$h .= implode( ",\n", $itms ). "\n";

		$h .= str_repeat( "\t", $level ). ']';

		return $h;

	}

	protected function recuArrExport( array $data, int $level ) {

		// var_export( true )
		$h = "[\n";
		$t = str_repeat( "\t", $level + 1 );
		$itms = [];

		foreach ( $data as $value ) {

			if ( is_array( $value ) )
				$value = $this->recuArrExport( $value, $level + 1 );
			else if ( is_object( $value ) )
				$value = $this->recuObjExport( $value, $level + 1 );
			else
				$value = var_export( $value, true );

			$itms[] = $t. $value;

		}

		$h .= implode( ",\n", $itms ). "\n";

		$h .= str_repeat( "\t", $level ). ']';

		return $h;

	}

	protected static function deleteDir( string $dir ) {

		foreach ( glob( $dir. '*', GLOB_MARK ) as $path )
			if ( is_file( $path ) )
				unlink( $path );
			else
				self::deleteDir( $path );

		rmdir( $dir );

	}

}