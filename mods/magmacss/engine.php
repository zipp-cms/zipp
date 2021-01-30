<?php
/*
@package: A CSS Preprocessor for PHP
@author: Sören Meier <info@s-me.ch>
@version: 0.1.3 <2019-08-05>
@docs: css.magma-lang.com/php/docs/
*/

namespace MagmaCSS;

use \Error;

class Engine {

	protected $debug = false;

	protected $tmpPath = '';

	protected $globalMixins = [];

	// METHODS
	public function go( string $file, string $niceName = null ) {

		$filename = ( is_null( $niceName ) ? md5( $file ) : $niceName ). '.css';

		$out = $this->tmpPath. $filename;

		if ( is_file( $out ) && !$this->debug )
			return $filename;

		$str = file_get_contents( $file );
		$str = $this->parse( $str );

		file_put_contents( $out, $str );
		return $filename;

	}

	public function parseRaw( string $ctn ) {
		return $this->parse( $ctn );
	}

	public function cleanTmp() {
		self::deleteDir( $this->tmpPath );
	}

	// the properties need to be css valid expect (simicolon)
	public function addMixin( string $name, $props ) {
		if ( !is_array( $props ) )
			$props = [$props];
		$this->globalMixins[$name] = $props;
	}

	// INIT
	public function __construct( string $tmpPath, bool $debug = false ) {

		$this->tmpPath = $tmpPath;
		$this->debug = $debug;
		if ( !is_dir( $this->tmpPath ) )
			mkdir( $this->tmpPath );

		$this->defaultMixins();

	}

	// PROTECTED
	protected function defaultMixins() {

		// core
		$this->addMixin( 'core', [
			'margin: 0',
			'padding: 0',
			'box-sizing: border-box'
		] );

		// Position
		$this->addMixin( 'fixed', 'position: fixed' );
		$this->addMixin( 'absolute', 'position: absolute' );
		$this->addMixin( 'relative', 'position: relative' );

		// Display
		$this->addMixin( 'block', 'display: block' );
		$this->addMixin( 'none', 'display: none' );
		$this->addMixin( 'flex', 'display: flex' );
		$this->addMixin( 'grid', 'display: grid' );

		// box-sizing
		$this->addMixin( 'border-box', 'box-sizing: border-box' );

		// content
		$this->addMixin( 'ctn', 'content: \'\'' );

		// abs center
		$this->addMixin( 'abs-center', [
			'position: absolute',
			'top: 50%',
			'left: 50%',
			'transform: translate(-50%, -50%)'
		] );

		// flex-center
		$this->addMixin( 'flex-center', [
			'display: flex',
			'align-items: center',
			'justify-content: center'
		] );

		// clearfix
		$this->addMixin( 'clearfix', [
			'content: \'\'',
			'display: table',
			'clear: both'
		] );

		// text transform
		$this->addMixin( 'uppercase', 'text-transform: uppercase' );
		$this->addMixin( 'lowercase', 'text-transform: lowercase' );
		$this->addMixin( 'normalcase', 'text-transform: none' );

	}

	protected function parse( string $str ) {

		$str = rtrim( str_replace( "\r", '', $str ) );
		$str = preg_replace( '/(\/\*.*?\*\/)|(^\s*\/\/.*?$)/ms', '', $str );
		// $str = preg_replace( '/^\s*\/\/.*?$/m', '', $str );
		$lines = explode( "\n", $str );


		$inMixins = false;
		$actMix = '';
		$mixins = $this->globalMixins;

		$inSelect = false;
		$selTree = [];
		$selLevel = 0;

		$selectors = [];

		$inSpecial = false;
		$actSpec = '';
		$levelSpec = 0;
		$specials = [];

		foreach ( $lines as $num => $line ) {

			// if empty line skip
			if ( preg_match( '/^\s*$/', $line ) )
				continue;

			// short version for media queries
			// <150px converto @media (max-width: 150px)
			// >150pxconverto @media (min-width: 150px)

			$line = preg_replace( '/^(\s*)<(\d+[a-z%]+)\s*$/', '$1@media (max-width: $2)', $line );
			$line = preg_replace( '/^(\s*)>(\d+[a-z%]+)\s*$/', '$1@media (min-width: $2)', $line );

			$level = $this->countLevel( $line );

			// special management
			if ( $inSpecial && $level <= $levelSpec )
				$inSpecial = false;

			if ( $inSpecial )
				$level--;

			// Properties
			$prop = preg_match( '/^\s*[^@]*:\s.*$/', $line ) > 0;
			if ( $prop ) {
				$ctn = trim( $line );
				$ctn = preg_replace( '/(?<=:\s)(--[a-zA-Z0-9\-]*)/m', 'var($0)', $ctn );

				if ( $inMixins )
					$mixins[$actMix][] = trim( $line );
				else if ( $inSelect ) {

					// prop in select
					$sel = $this->buildSelector( $selTree, $level );

					if ( $inSpecial ) {

						if ( !isset( $specials[$actSpec] ) )
							$specials[$actSpec] = [];

						if ( !isset( $specials[$actSpec][$sel] ) )
							$specials[$actSpec][$sel] = [];

						$specials[$actSpec][$sel][] = $ctn;
							

					} else {

						if ( !isset( $selectors[$sel] ) )
							$selectors[$sel] = [];

						$selectors[$sel][] = $ctn;

					}

				} else
					throw new Error( sprintf( 'No selector or mixin before line %d: %s', $num, $line ) );

				continue;
			}

			// Mixins
			$defMixin = preg_match( '/^\s*@([a-zA-Z][\w\-]*)\s*$/', $line, $mixinName ) > 0;
			if ( $defMixin ) {
				$mixinName = $mixinName[1];

				if ( $inMixins && $level > 0 ) {

					if ( !isset( $mixins[$mixinName] ) )
						throw new Error( sprintf( 'Could not find mixin %s, on line %d', $mixinName, $num ) );

					$mixins[$actMix] = array_merge( $mixins[$actMix], $mixins[$mixinName] );

				} else if ( $inSelect ) {

					if ( !isset( $mixins[$mixinName] ) )
						throw new Error( sprintf( 'Could not find mixin %s, on line %d', $mixinName, $num ) );

					// prop in select
					$sel = $this->buildSelector( $selTree, $level, $inSpecial );

					if ( $inSpecial ) {

						if ( !isset( $specials[$actSpec] ) )
							$specials[$actSpec] = [];

						if ( !isset( $specials[$actSpec][$sel] ) )
							$specials[$actSpec][$sel] = [];

						$specials[$actSpec][$sel] = array_merge( $specials[$actSpec][$sel], $mixins[$mixinName] );

					} else {

						if ( !isset( $selectors[$sel] ) )
							$selectors[$sel] = [];

						$selectors[$sel] = array_merge( $selectors[$sel], $mixins[$mixinName] );

					}

				} else {
					$inMixins = true;
					$inSelect = false;
					$actMix = $mixinName;
					$mixins[$actMix] = [];
				}

				continue;

			}

			// if special
			if ( preg_match( '/^\s*@/', $line ) ) {

				$inSpecial = true;
				$inSelect = $level > 0;
				$inMixins = false;
				$spec = trim( $line );
				$actSpec = $spec;
				$levelSpec = $level;

				continue;

			}

			// else we have a selector
			$inSelect = true;
			$inMixins = false;
			$sel = trim( $line );

			$selTree[$level] = trim( $line );


		}

		return $this->buildFromSelectors( $selectors ). $this->buildFromSpecials( $specials );

	}

	protected function buildSelector( array $inTree, int $level, bool $withTab = false ) {

		$tree = [];
		foreach ( array_slice( $inTree, 0, $level ) as $tr )
			$tree[] = array_map( 'trim', explode( ',', $tr ) );

		$sels = array_shift( $tree );
		foreach ( $tree as $tr ) {

			$nSels = [];
			foreach ( $tr as $t ) {

				$noSpace = $t[0] === ':' || $t[0] === '+' && ( $t[1] ?? ' ' ) !== ' ';
				if ( $noSpace && $t[0] === '+' )
					$t = substr( $t, 1 );

				foreach ( $sels as $sel )
					$nSels[] = sprintf( '%s%s%s', $sel, $noSpace ? '' : ' ', $t );

			}

			$sels = $nSels;

		}

		return implode( ",\n". ( $withTab ? "\t" : '' ), $sels );

	}

	protected function countLevel( string $line ) {
		$count = 0;
		$len = strlen( $line );
		for ( $i = 0; $i < $len; $i++ ) {
			$c = $line[$i];
			if ( $c !== "\t" )
				break;
			$count++;
		}
		return $count;
	}

	protected function buildFromSelectors( array $selectors ) {

		$str = '';
		foreach ( $selectors as $sel => $props ) {
			$str .= $sel. " {\n";
			foreach ( $props as $prop )
				$str .= "\t". $prop. ";\n";
			$str .= "}\n\n";
		}

		return $str;

	}

	protected function buildFromSpecials( array $specials ) {

		$str = '';
		foreach ( $specials as $spec => $selectors ) {
			$str .= $spec. " {\n\n";
			foreach ( $selectors as $sel => $props ) {
				$str .= "\t". $sel. " {\n";
				foreach ( $props as $prop )
					$str .= "\t\t". $prop. ";\n";
				$str .= "\t}\n\n";
			}
			$str .= "}\n\n";
		}

		return $str;

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