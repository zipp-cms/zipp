<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace CLI;

use Core\Module;
use \Error;

// other should register a route
// others should 
class CLI extends Module {

	protected $enabled = false;

	protected $args = [];

	protected $active = [];

	// GETTERS

	// METHODS
	public function isEnabled() {
		return $this->enabled;
	}

	public function register( string $prefix, string $desc, string $intCls, string $mod ) {
		$this->active[$prefix] = [$desc, $intCls, $mod];
	}

	// INIT
	public function onInit() {

		if ( php_sapi_name() !== 'cli' || !defined( 'STDIN' ) )
			return;

		$args = $_SERVER['argv'];
		array_shift( $args );

		$this->args = $args;
		$this->enabled = true;

	}

	// START
	public function onStart() {

		if ( !$this->enabled )
			return;

		if ( has( $this->args ) && $this->run( $this->args ) === false )
			return false;

		// duplicated the has check but its cleaner
		if ( !has( $this->args ) )
			$this->displayInfo();

		while ( true ) {

			$line = trim( fgets( STDIN ) );

			if ( !cLen( $line ) )
				continue;

			if ( $this->run( preg_split( '/\s+/', $line ) ) === false )
				break;

		}

	}

	protected function run( array $args ) {

		$pre = array_shift( $args );

		if ( $pre === 'help' )
			return $this->displayInfo();

		if ( $pre === 'q' || $pre === 'quit' )
			return false;

		if ( isset( $this->active[$pre] ) ) {
			$int = new $this->active[$pre][1]( $this->mods->get( $this->active[$pre][2] ) );
			return $int->on( $args );
		}

		$this->displayInfo();

	}

	protected function displayInfo() {

		echo EOL. 'Welcome to Zipp'. EOL;

		echo '- [help] to show this page again'. EOL;

		foreach ( $this->active as $p => $o ) {
			printf( '- [%s] %s%s', $p, $o[0], EOL );
		}

		echo '- [quit/q] to quit the programm'. EOL;

	}

}