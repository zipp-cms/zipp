<?php
/*
@package: Zipp
@version: 0.2 <2019-07-02>
*/

namespace UsersInt;

use Core\ShadowModule;
use CLI\Module as CLIModule;

class SetupCLI extends ShadowModule {

	use CLIModule;

	// INIT
	public function onConstruct() {

		if ( $this->cliIsEnabled() )
			$this->cliRegister( 'users', 'manage the users', 'CLIInteractor' );

	}

}