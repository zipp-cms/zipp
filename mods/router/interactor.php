<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Router;

use Core\Module;

class Interactor {

	protected $mods = null;

	protected $mod = null;

	public function __construct( Module $mod ) {

		$this->mod = $mod;
		$this->mods = $mod->mods;

	}

	public function on( Request $req ) {
		$req->error( 404, 'no callback defined' );
	}

}