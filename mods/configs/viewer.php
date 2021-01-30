<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Configs;

use MagmaCfg\Engine;

class Viewer {

	// all config entries ar saved here
	protected $cfgs = [];

	// save if we already tried to open the file
	protected $opened = false;

	// the path to the config file
	protected $p = '';

	public function __construct( string $p ) {
		$this->p = $p;
	}

	// opens the file
	protected function load() {

		if ( is_file( $this->p ) ) {
			$eng = new Engine( TMP_PATH. 'configs'. DS, DEBUG );
			$this->cfgs = $eng->go( $this->p );
		}

		$this->opened = true;

	}

	// get a config item
	public function get( string $k, $def = null ) {

		if ( !$this->opened )
			$this->load();

		return $this->cfgs->$k ?? $def;

	}

	// should implement a write

	public function __get( string $k ) {
		return $this->get( $k );
	}

}