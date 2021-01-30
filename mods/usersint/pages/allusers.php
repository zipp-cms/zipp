<?php
/*
@package: Zipp
@version: 0.1 <2019-05-31>
*/

namespace UsersInt\Pages;

use Router\Interactor;
use Router\Request;
use Ajax\Request as AjaxRequest;
use Admin\Page;

// TODO: this page
class AllUsers extends Page {

	public function onRequest( Request $req ) {

		if ( !$this->isAdmin() )
			return $req->errorAuth();

		echo 'all Users';

		/*$this->section = 'user';
		$this->slug = 'allusers';

		$this->loadHeader( $this->lang->allUsersTitle );

		echo 'welcome to the all users page';

		


		$this->loadFooter();*/

	}

	/*public function onAjax( AjaxRequest $req ) {

		$this->nonceKey = 'login';

		if ( !$this->checkNonce( $req ) )
			return;

		$d = $req->data;

		$username = $d['username'] ?? '';
		$password = $d['password'] ?? '';

		$res = $this->mods->Users->login( $username, $password );

		if ( $res )
			$req->ok( true );
		else
			$req->formError( $this->lang->credsError, $this->newNonce() );

	}*/

	// onCLI

}