<?php
/*
@package: Zipp
@version: 0.1 <2019-05-31>
*/

namespace UsersInt\Pages;

use Ajax\Request as AjaxRequest;
use Admin\Page;
use Admin\DataRequest;

use Fields\Fields\{Text, Email, Password};

class User extends Page {

	protected $section = 'user';

	protected $slug = 'user';

	protected $nonceKey = 'user-edit';

	protected $template = 'user';

	public function onData( DataRequest $req ) {

		$l = $this->lang;

		$d = $this->users->export();
		$d->niceRole = $l->roleTexts[$d->role] ?? $d->role;

		$fs = [];

		foreach ( $this->fields() as $f )
			$fs[] = $f->export( $d );

		return [
			'title' => $l->userTitle,
			'user' => $d,
			'fields' => $fs,
			'nonce' => $this->nonce()
		];

	}

	protected function fields() {

		$l = $this->lang;
		// $lOpts = $this->mods->Langs->getAllPossible();

		return [
			new Text( 'surname', [
				'name' => $l->surnameField, 
				'sett' => [ 'req' => true ]
			] ),
			new Text( 'lastname', [
				'name' => $l->lastnameField,
				'sett' => [ 'req' => true ]
			] ),
			new Email( 'email', [
				'name' => $l->emailField, 
				'sett' => [ 'req' => true ]
			] ),
			new Password( 'pw1', [ 'name' => $l->newPassword ] ),
			new Password( 'pw2', [ 'name' => $l->repeatNewPassword ] )
		];

	}

	public function onAjax( AjaxRequest $req ) {

		if ( !$this->checkNonce( $req ) )
			return;

		$data = $req->data;

		$d = [];
		foreach ( $this->fields() as $f ) {

			if ( !$f->validate( $data ) )
				return $req->formError( sprintf( $this->lang->fieldError, $f->name ), $this->newNonce() );

			$d[$f->slug] = $f->out( $data );

		}

		$nPw = null;
		if ( len( $d['pw1'] ) > 0 ) {
			if ( $d['pw1'] !== $d['pw2'] )
				return $req->formError( $this->lang->passwordError, $this->newNonce() );

			$nPw = $d['pw1'];
		}

		$res = $this->mods->Users->edit( $d['surname'], $d['lastname'], $d['email'], $nPw );

		if ( $res )
			$req->ok( true );
		else
			$req->formError( $this->lang->credsError, $this->newNonce() );

	}

	// onCLI

}