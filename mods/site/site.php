<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Site;

use Core\Module;
use Data\Module as DataModule;
use \Error;

// other should register a route
// others should 
class Site extends Module {

	use DataModule;

	protected $handlers = [ 'database' => 'Data\DbSite' ];

	protected $data = null;

	protected $mlData = [];

	protected $changed = false;

	// on end should clear cache

	// should accelalerate the json decode
	// maybe save it in the data
	public function getMl( string $k, string $lang, $def = null ) {

		if ( !isset( $this->mlData[$lang] ) )
			$this->mlData[$lang] = $this->handler->getAllByLang( $lang );

		if ( isset( $this->mlData[$lang][$k] ) )
			return json_decode( $this->mlData[$lang][$k] );

		return $def;

	}

	public function getAllMl( array $ks, string $lang ) {

		$d = [];

		foreach ( $ks as $k ) {

			$v = $this->getMl( $k, $lang );

			if ( !isNil( $v ) )
				$d[$k] = $v;

		}

		return (object) $d;

	}

	public function get( string $k, $def = null ) {

		if ( isNil( $this->data ) )
			$this->data = $this->handler->getAllByLangNull();

		if ( isset( $this->data[$k] ) )
			return json_decode( $this->data[$k] );

		return $def;

	}

	public function set( string $k, $value ) {

		$this->changed = true;

		$value = json_encode( $value );

		if ( !isNil( $this->data ) )
			$this->data[$k] = $value;

		$this->handler->set( $k, $value );

	}

	public function setMl( string $k, string $lang, $value ) {

		$this->changed = true;

		$value = json_encode( $value );

		if ( isset( $this->mlData[$lang] ) )
			$this->mlData[$lang][$k] = $value;

		$this->handler->setMl( $k, $lang, $value );

	}

	public function setMultMl( array $data, string $lang ) {

		$d = [];

		foreach ( $data as $k => $v ) {

			$d[$k] = json_encode( $v );

			if ( isset( $this->mlData[$lang] ) )
				$this->mlData[$lang][$k] = $d[$k];

		}

		$this->handler->setMultMl( $d, $lang );
	}

	public function getView( string $lang ) {
		return new Viewer( $this, $lang );
	}

	public function install() {
		$this->handler->create();
		// insert
		// languages nulll ["en"]
		// multilingual nulll false
		// name en "My Site"
		// theme null "Example\\Example"
	}

	// ININT
	public function onInstalling() {
		$this->install();
	}

}