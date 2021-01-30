<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Admin;

use Ajax\Interactor;
use Ajax\Request;

class AjaxInteractor extends Interactor {

	// public function
	// add to section ( Home, Content, Settings, Developer, User )
	// add to bereich ( Home, Inhalt, Einstellungen, Entwicklung, Benutzer )

	public function on( Request $req ) {

		$page = $req->event;
		$loggedIn = $this->mods->Users->isLoggedIn();

		$this->mod->initMainInteractor();

		if ( !$loggedIn ) {

			if ( $page !== 'login' )
				return $req->error( $this->mod->lang->notLoggedIn );

			return $this->loadPage( $page, $req );

		}

		$this->mod->initInteractors();

		if ( !$this->mod->hasPage( $page ) )
			return $req->error( sprintf( 'could not find page "%s" in module admin', $page ) );

		$this->loadPage( $page, $req );

	}

	protected function loadPage( string $page, Request $req ) {

		$p = $this->mod->getPage( $page );

		$p->onAjax( $req );

	}

}