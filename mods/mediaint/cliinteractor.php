<?php
/*
@package: Zipp
@version: 1.0 <2019-06-11>
*/

namespace MediaInt;

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

			default:
				return $this->info();
				break;

		}

		return false;

	}

	protected function info() {

		echo EOL. 'welcome to media'. EOL;
		echo '- [install] for creating the table'. EOL;

	}

	protected function install() {

		echo 'installing...'. EOL;

		$this->mods->Media->install();

		echo 'created media table'. EOL;

	}

}