<?php
/*
@package: Zipp
@version: 0.1 <2019-05-31>
*/

namespace Langs;

use MagmaCfg\Engine;
use \Error;

class Viewer {

	// all config entries ar saved here
	protected $langs = [];

	// save if we already tried to open the file
	protected $opened = false;

	// the path to the config file
	protected $pLang = '';

	protected $defaultLang = '';

	protected $activeLang = '';

	public function __construct( string $p, string $lang, string $default ) {
		$this->activeLang = $lang;
		$this->defaultLang = $default;
		$this->langPath = $p. $lang. '.mgcfg';
		$this->defaultPath = $p. $default. '.mgcfg';
	}

	// get a config item
	public function get( string $k ) {

		if ( !$this->opened )
			$this->load();

		return $this->langs->$k ?? $this->activeLang. '.'. $k;

	}

	// should implement a write

	public function __get( string $k ) {
		return $this->get( $k );
	}

	public function getActiveLang() {
		return $this->activeLang;
	}

	public function getAll() {
		
		if ( !$this->opened )
			$this->load();

		return $this->langs;

	}

	// opens the file
	protected function load() {

		$eng = new Engine( TMP_PATH. 'langs'. DS, DEBUG );
		
		$this->langs = $eng->go( $this->getPath() );
		$this->opened = true;

	}

	protected function getPath() {

		if ( is_file( $this->langPath ) )
			return $this->langPath;

		$this->activeLang = $this->defaultLang;

		if ( !is_file( $this->defaultPath ) )
			throw new Error( sprintf( 'could not find lang file "%s" and "%s"', $this->defaultPath, $this->langPath ) );

		return $this->defaultPath;

	}

}