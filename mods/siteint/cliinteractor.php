<?php
/*
@package: Zipp
@version: 1.0 <2019-05-31>
*/

namespace SiteInt;

use CLI\Interactor;

class CLIInteractor extends Interactor {

	public function on( array $args ) {

		if ( !has( $args ) )
			return $this->info();

		$fn = array_shift( $args );

		switch ( $fn ) {

			case 'install':
				$this->install();
				break;

			case 'get':
				$this->get( $args );
				break;

			case 'set':
				$this->set( $args );
				break;

			default:
				return $this->info();
				break;

		}

		return false;

	}

	protected function info() {

		echo EOL. 'welcome to site'. EOL;
		echo '- [install] for creating the table'. EOL;
		echo '- [get] <key> <lang/-> for getting an entry'. EOL;
		echo '- [set] <key> <lang/-> <value> for editing table'. EOL;

	}

	protected function install() {

		echo 'installing...'. EOL;

		$this->mods->Site->install();

		echo 'create site table'. EOL;

	}

	protected function get( array $args ) {

		$s = $this->mods->Site;

		if ( !has( $args, 1 ) )
			return;

		$k = $args[0];
		$l = $args[1] ?? null;

		if ( isNil( $l ) )
			return var_dump( $s->get( $k ) );

		var_dump( $s->getMl( $k, $l ) );

	}

	protected function set( array $args ) {

		$s = $this->mods->Site;

		if ( !has( $args, 3 ) )
			return;

		$k = array_shift( $args );
		$l = array_shift( $args );
		$v = json_decode( implode( ' ', $args ) );

		if ( $l === '-' )
			$s->set( $k, $v );
		else
			$s->setMl( $k, $l, $v );

		echo 'set'. EOL;

	}

}