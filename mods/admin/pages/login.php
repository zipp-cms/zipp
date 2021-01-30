<?php
/*
@package: Zipp
@version: 1.0 <2019-05-28>
*/

namespace Admin\Pages;

use Admin\{Page, DataRequest};
use Router\Interactor;
use Router\Request;
use Ajax\Request as AjaxRequest;

class Login extends Page {

	protected $slug = 'login';

	protected $nonceKey = 'login';

	protected $template = 'login';

	public function onData( DataRequest $req ) {

		// if i return a string its an error
		// if i return an array its ok

		//return 'Login Data Error';

		return [
			'title' => $this->lang->loginTitle,
			'nonce' => $this->nonce()
		];

	}

	public function onAjax( AjaxRequest $req ) {

		if ( !$this->checkNonce( $req ) )
			return;

		$d = $req->data;

		$username = (string) $d->username ?? '';
		$password = (string) $d->password ?? '';

		$res = $this->mods->Users->login( $username, $password );

		if ( $res )
			$req->ok( true );
		else
			$req->formError( $this->lang->credsError, $this->newNonce() );

	}

	// onCLI

}