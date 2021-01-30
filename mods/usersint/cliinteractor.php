<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace UsersInt;

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

			case 'new':
				$this->new( $args );
				break;

			default:
				return $this->info();
				break;

		}

		return false;

		

		// var_dump( $args );

		// return false;

	}

	protected function info() {

		echo EOL. 'welcome to users'. EOL;
		echo '- [install] for creating the database'. EOL;
		echo '- [new] <username> <password> for inserting a new user'. EOL;

	}

	protected function install() {

		echo 'installing...'. EOL;

		$this->mod->install();

		echo 'create users table'. EOL;

	}

	protected function new( array $args ) {

		if ( !has( $args, 2 ) ) {
			echo 'specifiy <username> and <password>'. EOL;
			return;
		}

		$res = $this->mod->new( $args[0], $args[1], 'surname', 'lastname', 'email@example.com', 10 );

		if ( !$res )
			echo 'could not create new user input is incorrect';
		else
			echo 'user was created';

		echo EOL;

	}

}