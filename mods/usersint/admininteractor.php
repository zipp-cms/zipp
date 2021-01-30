<?php
/*
@package: Zipp
@version: 0.1 <2019-05-31>
*/

namespace UsersInt;

use Admin\Interactor;

class AdminInteractor extends Interactor {

	protected function onInit() {

		$l = $this->lang;
		$u = $this->users;

		$this->addSection( 'user', sprintf( $l->usersHi, $u->surname ), 10 );

		// ah users should be initialized now
		$this->addStyle( 'style' );
		$this->addScript( 'main' );


		$list = [
			[ 'Pages\User', 'user', $l->userTitle ]
		];

		if ( $this->isAdmin() ) {
			$list[] = [ 'Pages\AllUsers', 'user', $l->allUsersTitle ];
		}

		$list[] = [ 'Pages\Logout', 'user', $l->logoutTitle ];

		// activate the cli interactor
		$this->addMultiple( $list );

	}

}