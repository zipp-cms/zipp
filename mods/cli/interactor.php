<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace CLI;

use Core\Module;

class Interactor {

	protected $mod = null;

	protected $mods = null;

	public function __construct( Module $mod ) {
		$this->mod = $mod;
		$this->mods = $mod->mods;
	}

	public function on( array $args ) {
		return false;
	}

}