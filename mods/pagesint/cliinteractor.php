<?php
/*
@package: Zipp
@version: 0.1 <2019-05-31>
*/

namespace PagesInt;

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

		echo EOL. 'welcome to pages'. EOL;
		echo '- [install] for creating the table'. EOL;

	}

	protected function install() {

		echo 'installing...'. EOL;

		$this->mods->Pages->install();

		echo 'created pages table'. EOL;

	}

}