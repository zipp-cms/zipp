<?php
/*
@package: Zipp
@version: 0.1 <2019-06-13>
*/

namespace Admin;

use Router\Interactor;
use Router\Request;

class RouterInteractor extends Interactor {

	public function on( Request $req ) {

		$users = $this->mods->Users;
		$loggedIn = $users->isLoggedIn();

		$this->mod->initMainInteractor();

		if ( !$loggedIn )
			return $this->loadPage( $req, 'login' );

		// log that it is a user
		// TODO: check if we should do that
		/*if ( $this->mods->has( 'Logs' ) )
			$this->mods->Logs->log( 'user:'. $users->username );*/

		$this->mod->initInteractors();
		return $this->loadPage( $req, $this->getPage( $req ) );

	}

	protected function loadPage( Request $req, string $page ) {

		$p = $this->mod->getPage( $page );

		if ( $req->type === 'data' )
			return $this->handleData( $req, $p );

		return $p->onRequest( $req );

	}

	protected function getPage( Request $req ) {

		$urls = $this->mod->urls;
		krsort( $urls, SORT_NUMERIC );

		$uri = $req->uri;
		$ul = len( $uri );

		foreach ( $urls as $l => $us ) {

			if ( $l > $ul )
				continue;

			foreach ( $us as $u => $page ) {

				if ( substr( $uri, 0, $l ) === $u )
					return $page;

			}

		}

		// base page
		return 'home';

	}

	protected function handleData( Request $req, Page $p ) {

		$dReq = new DataRequest( $req );

		$p->baseOnData( $dReq );

	}

}