<?php
/*
@package: Zipp
@version: 0.2 <2019-07-02>
*/

namespace UsersInt\Pages;

use Router\Request;
use Admin\Page;
use Admin\DataRequest;

class Logout extends Page {

	protected $section = 'user';

	protected $slug = 'logout';

	public function onRequest( Request $req ) {

		$this->loadHeader();

		echo 'Logout';

		$this->loadFooter();

	}

	public function onData( DataRequest $req ) {
		$this->users->logout();
		return [
			'link' => $this->mods->Admin->bUrl()
		];
	}

}