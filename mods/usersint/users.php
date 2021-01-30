<?php
/*
@package: Zipp
@version: 0.2 <2019-07-02>
*/

namespace UsersInt;

use Core\Module;
use Langs\Module as LangsModule;

class Users extends Module {

	use LangsModule;

	protected $cli = null;

	protected $admin = null;

	// INIT
	public function onInit() {

		if ( $this->mods->has( 'CLI' ) )
			$this->cli = new SetupCLI( $this );

		if ( $this->mods->has( 'Admin' ) )
			$this->admin = new SetupAdmin( $this );

	}

}